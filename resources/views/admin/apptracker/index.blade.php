@extends('layouts.demo_admin_app')
@push('head-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style> 


</style>
@endpush
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
     
	 
<?php 
 	
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
	
?>
	 
	 
	  <!--begin::Requisitions=-->
      <td class="d-flex align-items-center border-0">
      <!--begin:: Avatar -->
      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
         <a href="javascript:void(0);">
            <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
         </a>
      </div>
      <!--end::Avatar-->
      <!--begin::User details-->
      <div class="d-flex flex-column">
         <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo strtoupper(substr(@$jobtitle, 0, 2)); ?></a>
         <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary"><?php echo ucwords(substr(@$jobclient->name, 0, 10)); ?></a>
      </div>
      <!--begin::User details-->
      </td>
	  
	  
	  
      <!--end::Requisitions-->      
      <!--begin::Requisition Status--> 
      <td>                                        
         <div class="d-flex flex-column ml-2">
            <span class="badge fw-bolder fs-6 joined-bg px-2 py-1"><?php echo ucwords(substr(@$job->status, 0, 10)); ?></span>
            <span class="text-muted fw-bold text-muted d-block fs-7">R-<?php echo @$job->id; ?></span>
         </div>
      </td>
      <!--end::Requisition Status-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Similar Candidates-->
      <div class="d-flex flex-column">    
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
          <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1">Similar Candidates</a>
        </h1>
      <span class="text-gray-600">0</span>
    </div>
    <!--end::Similar Candidates-->
    </div>
    <!--end::Page title-->
      <form action="" id="filter-form" style="">
                     @csrf
			<input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
			<input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />			 
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

              <!--begin::Export-->
              <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
              <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
              <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                  <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                  <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                </svg>
              </span>
              <!--end::Svg Icon-->Export</button>
              <!--end::Export-->


@if(in_array("add_job_applications", $userPermissions))	
              <!--begin::Add Candidate--> 
			  <a href="<?php echo url('admin/job-applications/create/'.@$job->id.'/0'); ?>">
			  <button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate">
              <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
              <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                  </g>
                </svg>
              </span>
              Add Candidate</button>
              </a>
			  <!--end::Add Candidate-->
@endif	


            </div>
            <!--end::Toolbar-->
            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
              <div class="fw-bolder me-5">
              <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
              <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
            </div>            
            <!--begin::Modal - Adjust Balance-->
            <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
              <!--begin::Modal dialog-->
              <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                  <!--begin::Modal header-->
                  <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bolder">Export Users</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
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
                  <!--begin::Modal body-->
                  <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Form-->
                   
                      <!--begin::Input group-->
                      <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="fs-6 fw-bold form-label mb-2">Select Roles:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="role" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                          <option></option>
                          <option value="Administrator">Administrator</option>
                          <option value="Analyst">Analyst</option>
                          <option value="Developer">Developer</option>
                          <option value="Support">Support</option>
                          <option value="Trial">Trial</option>
                        </select>
                        <!--end::Input-->
                      </div>
                      <!--end::Input group-->
                      <!--begin::Input group-->
                      <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-bold form-label mb-2">Select Export Format:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                          <option></option>
                          <option value="excel">Excel</option>
                          <option value="pdf">PDF</option>
                          <option value="cvs">CVS</option>
                          <option value="zip">ZIP</option>
                        </select>
                        <!--end::Input-->
                      </div>
                      <!--end::Input group-->
                      <!--begin::Actions-->
                      <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                          <span class="indicator-label">Submit</span>
                          <span class="indicator-progress">Please wait...
                          <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                      </div>
                      <!--end::Actions-->
                   
                    <!--end::Form-->
                  </div>
                  <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
              </div>
              <!--end::Modal dialog-->
            </div>
            <!--end::Modal - New Card-->
          </div>
      </div>
      <!--end::Actions-->
	  </form>
	  
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->

<?php 
               //echo '<pre>'; print_r($candStatusClass); 
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
                }
                else
                {
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

?>
			   
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!-- Circles boxes -->
      <div class="d-flex justify-content-space-around align-items-center custm-bg">
        <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav" href="Javascript:void(0);" rel="all">
          <div class="total_jobs mr-2">
              <div class=" <?php echo @$tr_class; ?> text-center squre_box" role="alert" id="bg_all">
                <span class="total-content-view">
                    <!-- <i class="fa fa-users text-white fa-sm" aria-hidden="true"></i> -->
                    <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <?php echo @$applicationCounter->allCandidate ?>  
                </span>
              </div>
              <h6 class="text-center color-all font-weight-600">&nbsp;Total</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav" href="Javascript:void(0);" rel="1">
          <div class="sourcing mr-2">
              <div class="bg text-center circle_boxes SourcingCounter <?php echo $sourcing_bg; ?>" role="alert" id="bg_1"> <?php echo @$applicationCounter->Sourcing ?> </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $sourcing_text; ?>" id="tx_1">Sourcing</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="2">
          <div class="screening mr-2">
              <div class="bg text-center circle_boxes <?php echo $screening_bg; ?>" role="alert" id="bg_2"> <?php echo @$applicationCounter->Screening ?>  </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $screening_text; ?>" id="tx_2">Screening</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="3">
          <div class="negitation mr-2">
              <div class="bg text-center circle_boxes <?php echo $interview_bg; ?>" role="alert" id="bg_3"> <?php echo @$applicationCounter->Interview ?> </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $interview_text; ?>" id="tx_3">Evaluation</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="4">
          <div class="negitation mr-2">
              <div class="bg text-center circle_boxes <?php echo $negotation_bg; ?>" role="alert" id="bg_4"> <?php echo @$applicationCounter->Negotiation ?> </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $negotation_text; ?>" id="tx_4">Negotation</h6>
          </div>
        </a>
       
	    <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="5">
          <div class="offer mr-2">
              <div class="bg text-center circle_boxes <?php echo $offer_bg; ?>" role="alert" id="bg_5"> <?php echo @$applicationCounter->Offered ?> </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $offer_text; ?>" id="tx_5">Offer</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="6">
          <div class="joining mr-2">
              <div class="bg text-center circle_boxes <?php echo $joining_bg; ?>" role="alert" id="bg_6"> <?php echo @$applicationCounter->Joined ?> </div>
              <h6 class="tx text-center color-all font-weight-600 <?php echo $joining_text; ?>" id="tx_6">Joined</h6>
          </div>
        </a>
       
	    <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="7">
          <div class="rejected mr-2">
              <div class="bg alert text-center circle_boxes   <?php echo $rejected_bg; ?>" role="alert" id="bg_7"> <?php echo @$applicationCounter->Rejected ?> </div>
              <h6 class="tx text-center   font-weight-600 <?php echo $rejected_text; ?>" id="tx_7">Rejected</h6>
          </div>
        </a>
    </div>
      <!-- Circles boxes end -->
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
         
@if(in_array("view_job_applications", $userPermissions))		  
		 
		  <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0">
                  <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div>
                </th>
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
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
         
          </table>
          <!--end::Table-->
@endif	
		  
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

<!--begin::Stage sub stage popup-->
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">	
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Stage and sub stage</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="stage_sub_stage_close">  
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
	 <div class="stage_body_content" id="stage_body_content"></div> 	  
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Stage sub stage popup-->

@endsection
@push('footer-script')

<script>

		
		$('body').on('click', '#AddCandidate', function(){
			var url					=	'{{ route('admin.job-applications.create') }}';
			window.location.href 	=	url;			
		});
		
</script>
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
   
   $( document ).ready(function() {
      
         var current_nav_active  = $('#mstatus_id').val(); 
      	//alert(current_nav_active);
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
        
        
        $('#mstatus_id').val(page);
        
        var url   = '<?php echo url('/admin/apptracker/') ?>';
        url     = url+'/'+jobid+'/'+page;
        window.history.pushState('page2', 'Title', url);
        tableLoad('load');
     })
});
</script>

<script>

   $( document ).ready(function() {
       
       $( ".addColumn" ).click(function() {
         
           var id = $(this).attr('rel');
           if (typeof scheduleDate === "undefined")
           {
             scheduleDate = '';
           }
           var url = '{{ route('admin.jobtracker.addclass')}}?id=' + id;
           $('#modelHeading').html('Schedule');
           $.ajaxModal('#scheduleDetailModal', url);       
       });
});
</script>
<script>
   $(document).ready(function() { 
      $('#inputSearch').keyup(function() {
        
          tableLoad('filter');   
          //alert("call");   
      });
   });
    
      
       $('#column_visibility_control_btn').click(function(){
          var id    = $('#jobid').val();
        //var id        = 12;
        if(typeof scheduleDate === "undefined") {
          scheduleDate  = '';
        }
        var url       = "{{ route('admin.jobtracker.load_visibility_control',':id') }}";
        url         = url.replace(':id', id);
        $('#modelHeading_cvc').html('Schedule');
        $.ajaxModal('#scheduleDetailModal_cvc', url);
      });
      
   
           $('#start-date').datepicker({
               format: 'yyyy-mm-dd'
           })
           $('#end-date').datepicker({
               format: 'yyyy-mm-dd'
           })
   
           var table;
           tableLoad('load');
           // For select 2
           $(".select2").select2();
           $('#reset-filters').click(function () {
      
        //$('#myTable').DataTable().column('').search('').draw();
        var table = $('#myTable').DataTable();
        
        table.search( '' ).columns().search( '' ).draw();
    
        
               $('#filter-form')[0].reset();
              // $('#filter-form').find('select').val('all').trigger('change');
               tableLoad('load');
           })
           $('#apply-filters').click(function () {
               tableLoad('filter');
           });
      
      
          
      
   
   
       function tableLoad(type) {
           
		var jobid          		= $('#jobid').val();
		var mstatus_id        	= $('#mstatus_id').val();
		var inputSearch       	= $('#inputSearch').val();
        
     	table 					= $('#myTable').dataTable({
      
	   			responsive     	: true, 
      			processing      : true,
				ordering		: false, //disable ordering      			
      			destroy       	: true,   
      			"bLengthChange"   : false,   
      			"lengthMenu"      : [[10, 25, 50, 100], [10, 25, 50, 100]],
				sDom			: 'lrtip',       
				          
          		ajax			: '{!! route('admin.apptracker.data') !!}?jobid='+jobid+'&mstatus_id='+mstatus_id+'&search='+inputSearch,
                          
                language		: {
                       					"url": "<?php echo __("app.datatable") ?>"
                   				  },           
               "fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
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
   
   
   
   
   
   
       $('body').on('click', '.sa-params', function(){
               var id = $(this).data('row-id');
               swal({
						title				: "@lang('errors.areYouSure')",
						text				: "@lang('errors.deleteWarning')",
						type				: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "@lang('app.delete')",
						cancelButtonText: "@lang('app.cancel')",
						closeOnConfirm: true,
						closeOnCancel: true
					}, function(isConfirm){
                   if (isConfirm) {
   
                       var url = "{{ route('admin.cvtracker.destroy',':id') }}";
                       url = url.replace(':id', id);
   
                       var token = "{{ csrf_token() }}";
   
                       $.easyAjax({
                           type: 'POST',
                           url: url,
                           data: {'_token': token, '_method': 'DELETE'},
                           success: function (response) {
                               if (response.status == "success") {
                                   $.unblockUI();
   									//swal("Deleted!", response.message, "success");
                                   table._fnDraw();
                               }
                           }
                       });
                   }
               });
           });        
   
      table.on('click', '.show-detail', function () 
      {
        //alert("call");
        
               $(".right-sidebar").slideDown(50).addClass("shw-rside");
               var id       = $(this).data('row-id');
               var url    = "{{ route('admin.job-applications.show',':id') }}";
               url      = url.replace(':id', id);
               $.easyAjax({
                        type  : 'GET',
                        url   : url,
                        success : function (response) {
                                            if (response.status == "success") 
                                  {
                                                  $('#right-sidebar-content').html(response.view);
                                            }
                                        }
               });
        
        
        
           }); 
		   
		   
	 table.on('click', '.stage_sub_stage', function ()   
      {
       
               var id       = $(this).data('row-id');			   
               var url    	= "{{ route('admin.job-applications.stage_body_content',':id') }}";
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
		   	   
		   
                 
      $('body').on('click', '.ti-close', function () {
        $("body").removeClass("control-sidebar-slide-open");
      });     
      
       $('.toggle-filter').click(function () {
               $('#ticket-filters').toggle('slide');
           });
   
      function exportJobApplication()
      {
        
			var jobs      		= '';
			jobs          		= $('#jobid').val();
			
			var mstatus_id      = '';
			mstatus_id          = $('#mstatus_id').val();
			
			var table       	= $('#myTable').dataTable();
			var length      	= table.fnGetData().length;
			
			var oSettings     	= table.fnSettings();
			var start         	= oSettings._iDisplayStart;
			
			var url       		= '{{ route('admin.apptracker.export', [':jobs', ':mstatus_id', ':start', ':length' ]) }}';
			url         		= url.replace(':jobs', jobs);
			url         		= url.replace(':mstatus_id', mstatus_id);
			url         		= url.replace(':start', start);
			url         		= url.replace(':length', length);
			
			window.location.href = url; 
       }       
      
      $( document ).ready(function() 
      {
        var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
        
        
        $('#jobs').on('change', function() {
          //alert("call"); 
          $('.jobLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
          tableLoad('filter');
        });
        
        $('#source').on('change', function() {
          $('.sourceLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
          tableLoad('filter');
        });
        
        
        
        
        
        $('#search_passport').on('change', function() {
          $('.search_passportLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
          tableLoad('filter');
        });
        
        $('#search_np_id').on('change', function() {
          $('.search_np_idLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
          tableLoad('filter');
        });
        
        $('#status').on('change', function() {
        
          
          $('.statusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
          tableLoad('filter');
        });
        
        
        
        $('#submit').click(function () {
   
                $.easyAjax({
                  url: '{{route('admin.apptracker.store')}}',
                  container: '#filter-form',
                  type: "POST",
                  redirect: true,
                  file: true,
                  data: $('#filter-form').serialize(),
                  beforeSend: function() {
                    $('.skill_posting_loader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                  },
                              success: function (response) {
                          $('.skill_posting_loader').html('');
                            location.reload();
                  },
                      
                  error: function (response) {
                    handleFails(response);
                  }
                })
              });
          
      
      });   
      
      $('body').on('keyup, change', '.userinfo', function()
      {
         var jobid     = $(this).data('jobid');
         var candidate   = $(this).data('candidate');
         var schematype  = $(this).data('schematype');
         var schemaname  = $(this).data('schemaname');
         var schemavalue = $(this).val();
        
        //alert(schemavalue);
      //  url: '{{route('admin.jobtracker.store')}}',
              var url = '{{ route('admin.jobtracker.updateinformation') }}';
             // url = url.replace(':candidate', candidate);
   
                       var token = "{{ csrf_token() }}";
   
                       $.easyAjax({
                           type: 'POST',
                           url: url,
                           data: {'_token': token, 'jobid': jobid, 'candidate': candidate, 'schematype': schematype, 'schemaname': schemaname, 'schemavalue': schemavalue},
              // data: { 'jobid': jobid},
                           success: function (response) {
              
                //alert(response); return false;
                               if (response.status == "success") 
                {
                                   $.unblockUI();
                  
                                   table._fnDraw();
                               }
                           }
                       });
                  
               
           });
      
      function updateCandidatePassport(currentvalue, candidate, jobid)
      {
        //updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'passport');
        customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'passport');
      }
      
      function updateCandidateNoticePeriod(currentvalue, candidate, jobid)
      {
        //updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'np_id');
        customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'np_id');
      }
      
      function updateApplicationStaus(currentvalue, candidate, jobid)
      {
        updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'status_id');
      }
      
      function updateApplicationEducation(currentvalue, candidate, jobid)
      {
        //updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'education');
        customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'education');
      }
          
      function customApplicationUpdate(currentvalue,  candidate, jobid, schematype, schemaname )
      {
         var jobid     = jobid;
         var candidate   = candidate;
         var schematype  = schematype;
         var schemaname  = schemaname;
         var schemavalue = currentvalue;
        
        //alert(schemavalue);
      //  url: '{{route('admin.jobtracker.store')}}',
              var url = '{{ route('admin.jobtracker.updateinformation') }}';
             // url = url.replace(':candidate', candidate);
   
                       var token = "{{ csrf_token() }}";
   
                       $.easyAjax({
                           type: 'POST',
                           url: url,
                           data: {'_token': token, 'jobid': jobid, 'candidate': candidate, 'schematype': schematype, 'schemaname': schemaname, 'schemavalue': schemavalue},
              // data: { 'jobid': jobid},
                           success: function (response) {
              
                //alert(response); return false;
                               if (response.status == "success") 
                {
                                   $.unblockUI();
                  
                                   table._fnDraw();
                               }
                           }
                       });
        
      }   
      
      function updateApplicationStatus121(statusId, applicationId)
      {
        var statusId    = statusId;
        var applicationId   = applicationId;
                  
   
                  var url = "{{ route('admin.job-applications.statusupdate',':statusId') }}";
                       url = url.replace(':statusId', statusId);
   
            var baseurl = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
          
                       var token = "{{ csrf_token() }}";
            
                       $.easyAjax({
                           type: 'POST',
                           url: url,
                           data: {'_token': token, '_method': 'POST', 'statusId' : statusId, 'applicationId': applicationId },
              beforeSend: function() {
                  $('.statusLoader_'+applicationId).html('<img src="'+baseurl+'" height="50px" width="50px">');
              },
                           success: function (response) {
                            
                /*  if (response.status == "success") {
                                   $.unblockUI();
   //                                    swal("Deleted!", response.message, "success");
                                   table._fnDraw();
                               }*/
                
                           },
              complete: function (data) {
                    $('.statusLoader_'+applicationId).html('');
               }
                       });
          
      }   
      
      function updateCandidateStatus(scheduleDate) 
      {
               if (typeof scheduleDate === "undefined") {
                   scheduleDate = '';
               }
               var url = '{{ route('admin.cvtracker.create_status')}}?date=' + scheduleDate;
               $('#modelHeading_status').html('Schedule');
               $.ajaxModal('#scheduleDetailModal_status', url);
           }    
      
      function getMasterChild(current_status, current_application_id, job_id)
      {
          var id        = id;
          var url       = "{{ route('admin.apptracker.getchild',':id') }}";
          url         = url.replace(':id', current_application_id);
          var baseurl     = "{{ asset('assets/images/Loading2.gif') }}"; 
          var token       = "{{ csrf_token() }}";
          
           $.ajax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'POST', 'current_application_id' : current_application_id, 'current_status': current_status, 'job_id': job_id },
                    beforeSend: function() {
                                  $('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                    success: function (response) 
                    {
                      $('#childStatus_'+current_application_id).html('');
                      if(response.flag == 1)
                      {
                        //$('#child').show();
                        $('#childStatus_'+current_application_id).html(response.data);
                      }
                      else if(response.flag == 0)
                      {
                        //alert(response.activeAppCounter.Sourcing); 
                        
                        $('#bg_1').html(response.activeAppCounter.Sourcing);     // Sourcing
                        $('#bg_2').html(response.activeAppCounter.Screening);    // Screening
                        $('#bg_3').html(response.activeAppCounter.Interview);    // Interview
                        $('#bg_4').html(response.activeAppCounter.Negotiation);   // Negotiation
                        $('#bg_5').html(response.activeAppCounter.Offered);      // Offered
                        $('#bg_6').html(response.activeAppCounter.Joined);     // Joined
                        $('#bg_7').html(response.activeAppCounter.Rejected);      // Rejected
                        
                         table._fnDraw();
                      }
                    },
                    complete: function (data) {
                        //$('#childStatus_'+current_application_id).html('');
                     }
          });
       }
    
      function updateSubStage(current_status, current_application_id, job_id, parent_status)
      {
          var id        = id;
          var url       = "{{ route('admin.apptracker.sub_stage',':id') }}";
          url         = url.replace(':id', current_application_id);
          var baseurl     = "{{ asset('assets/images/Loading2.gif') }}"; 
          //var token     = "{{ csrf_token() }}";
          
          $.ajax({
                    type: 'GET',
                    url: url,
                    data: { 'application_id' : current_application_id, 'status': current_status, 'job_id': job_id, 'parent_status': parent_status },
                    beforeSend: function() {
                                  //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                    success: function (response) 
                    {
                      
                       if(response.flag == 1)
                      {
                        //alert(response.activeAppCounter.Sourcing); 
                        
                        $('#bg_1').html(response.activeAppCounter.Sourcing);     // Sourcing
                        $('#bg_2').html(response.activeAppCounter.Screening);    // Screening
                        $('#bg_3').html(response.activeAppCounter.Interview);    // Interview
                        $('#bg_4').html(response.activeAppCounter.Negotiation);   // Negotiation
                        $('#bg_5').html(response.activeAppCounter.Offered);      // Offered
                        $('#bg_6').html(response.activeAppCounter.Joined);     // Joined
                        $('#bg_7').html(response.activeAppCounter.Rejected);      // Rejected
                        
                         table._fnDraw();
                      }
                      
                      
                    },
                    complete: function (data) {
                        //$('#childStatus_'+current_application_id).html(''); 
                     }
          });
       }
           
       function getOpenRejectionResionFrm(current_child_value, current_application_id, current_job_id, current_parent_status)
       {
        //alert(current_child_value);
        //var csname = $('#childStatus_'+current_application_id).val();
         
         var csname = $('#childStatus_'+current_application_id+' :selected').text();
        // alert(csname);
         
        if(current_parent_status == 7 && current_child_value > 0 && csname == 'Make a Note')
        {     
           
          if (typeof scheduleDate === "undefined") {
            scheduleDate = '';
          }
          var url = '{{ route('admin.apptracker.openRejFrm')}}?application_id=' + current_application_id+'&job_id='+current_job_id+'&parent_status_id='+current_parent_status+'&current_child_id='+current_child_value ;
          $('#modelHeading_reschedule').html('Schedule');
          $.ajaxModal('#scheduleDetailModal_reschedule', url);
        
        }
        else if ( csname != 'Select Sub Stage' )
        {
            updateSubStage(current_child_value, current_application_id, current_job_id, current_parent_status)
          
        }
        
        
          
       }  
	   	  		
		function showInterview(id) 
		{
				
             
						var url = "{{ route('admin.job-applications.interview-show',':id') }}";
						url = url.replace(':id', id);
						$('#moveApplicationmodelHeading').html('Schedule');
						$.ajaxModal('#moveApplicationModal', url);
			   
						/*var url = "{{ route('admin.apptracker.show-interviews',':id') }}";
						url = url.replace(':id', id);
						$('#moveApplicationmodelHeading').html('Schedule');
						$.ajaxModal('#moveApplicationModal', url);*/
		}
		   				
	
    </script>
@endpush 
