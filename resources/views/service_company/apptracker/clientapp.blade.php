@extends('layouts.service_company_layout')
 
@push('head-script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

<style> 

@media only screen and (min-width: 1024px) and (max-width: 1440px){
  	#client_stage_sub_stage{width: 1025px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  	#kt_client_activitiesApplication{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  	#client_stage_sub_stage{width: 1200px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  	#kt_client_activitiesApplication{width: 1200px;}
}


</style>
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
  	#kt_activitiesApplication{
      width: 1025px;
      z-index: 999999 !important;
    }
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  	#kt_activitiesApplication{
      width: 1200px;
      z-index: 999999 !important;
      
    }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  	#kt_activitiesCopy{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  	#kt_activitiesCopy{width: 1200px;}
}

.table-custom-class tbody tr {
    height: 82px;
    position: relative;
}

.btn.custombtn {
    padding: 6px 6px 6px 6px !important;
}

/* Fwd Resume / Copy — same pill style as CV Tracker's row-selection action buttons. */
.aptk-action-btn.btn {
    font-size: 12px;
    font-weight: 500;
    background: #fff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    box-shadow: none !important;
}
.aptk-action-btn.btn:hover {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
}
/* The original toolbar buttons stay in the DOM (so the fwd_resume/application_copy
   modal-trigger classes keep working unchanged) but are permanently hidden now —
   their restyled equivalent lives in #aptk_selection_bar below. */
.aptk-legacy-hide {
    display: none !important;
}

/* Row selection action bar — hidden until candidates are checked, then sits
   directly above the results table. */
.aptk-selection-bar {
    display: none;
    align-items: center;
    gap: 14px;
    background: #fff;
    border: 1px solid #e5e9f2;
    border-radius: 12px;
    padding: 10px 16px;
    margin-bottom: 14px;
    font-family: 'Poppins', sans-serif;
    flex-wrap: wrap;
}
.aptk-selection-bar.show {
    display: flex;
}
.aptk-selection-count {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
}
.aptk-selection-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-wrap: wrap;
}
.aptk-selection-actions .btn {
    font-size: 12px;
    font-weight: 500;
    background: #fff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    box-shadow: none !important;
}
.aptk-selection-actions .btn:hover {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
}
.aptk-selection-close {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.aptk-selection-close:hover {
    background: #f1f5f9;
    color: #334155;
}

/* Hiring Pipeline card */
.hp-toolbar-card .card-body {
    padding: 14px 16px;
}
.hp-card .card-body {
    padding: 16px 20px;
}
.hp-flow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    width: 100%;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding-bottom: 2px;
    font-family: 'Poppins', sans-serif;
}
.hp-total-link {
    text-decoration: none;
    flex-shrink: 0;
}
.hp-total {
    width: 82px;
    height: 82px;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    transition: background-color .15s;
}
.hp-total.bg-total-active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}
.hp-total.bg-gray {
    background: #e4e6ef;
}
.hp-total-icon {
    color: #fff;
    display: flex;
}
.hp-total.bg-gray .hp-total-icon {
    color: #7e8299;
}
.hp-total-icon svg {
    width: 16px;
    height: 16px;
}
.hp-total-count {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}
.hp-total.bg-gray .hp-total-count {
    color: #3f4254;
}
.hp-total-label {
    font-size: 9px;
    font-weight: 500;
    color: #dbeafe;
    text-align: center;
    line-height: 1.2;
    padding: 0 4px;
}
.hp-total.bg-gray .hp-total-label {
    color: #7e8299;
}
.hp-eq {
    font-size: 18px;
    font-weight: 600;
    color: #b5b5c3;
    flex-shrink: 0;
}
.hp-chevron {
    display: flex;
    color: #c2c5cc;
    flex-shrink: 0;
}
.hp-chevron svg {
    width: 16px;
    height: 16px;
}
.hp-divider {
    width: 1px;
    height: 44px;
    background: #e4e6ef;
    flex-shrink: 0;
    margin: 0 4px;
}
.hp-stage {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.hp-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    background: #f2f3f7;
    border: 2px solid #e4e6ef;
    transition: border-color .15s, background-color .15s;
}
.hp-circle .hp-icon {
    display: flex;
    color: #99a1b7;
    transition: color .15s;
}
.hp-circle .hp-icon svg {
    width: 14px;
    height: 14px;
}
.hp-circle .hp-count {
    font-size: 13px;
    font-weight: 700;
    color: #5e6278;
    line-height: 1.2;
    transition: color .15s;
}
.hp-circle.active {
    border-color: var(--hp-color);
    background: var(--hp-bg);
}
.hp-circle.active .hp-icon {
    color: var(--hp-color);
}
.hp-circle.active .hp-count {
    color: var(--hp-color);
}
.hp-label {
    font-size: 11px;
    font-weight: 500;
    color: #7e8299;
    text-align: center;
    white-space: nowrap;
    transition: color .15s, font-weight .15s;
}
.hp-label.active {
    font-weight: 700;
    color: #181c32;
}
@media (max-width: 1440px) {
    .hp-total { width: 72px; height: 72px; }
    .hp-circle { width: 52px; height: 52px; }
}
@media (max-width: 576px) {
    .hp-flow { justify-content: flex-start; }
}

</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <?php  
     use App\Helper\GeneralMethod;
     $logos = GeneralMethod::companyDetails($job->id);
    
       $lock_unlock = GeneralMethod::checksubscription();
       $jobIdEncrypt = GeneralMethod::value_encrypt($job->id);
        $rc_path	=	url('/service_company/apptracker/dsimilar_candidates/'.@$job->id);
        $req_path				=	url('/service_company/apptracker/clientapp/'. GeneralMethod::value_encrypt(@$job->id).'/all');
        $public_bench_path		=	url('/service_company/apptracker/public_bench/'. GeneralMethod::value_encrypt(@$job->id));
        $internal_bench_path	=	url('/service_company/apptracker/dinternal_bench/'. GeneralMethod::value_encrypt(@$job->id));

            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );
        $existing_path			=	url('/service_company/apptracker/dsimilar_candidates/'. GeneralMethod::value_encrypt(@$job->id));

         $tabactive = [
                    'pool_active'=>'tabactive',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter
                    ];

                    // dd($tabactive);



   ?>
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    @include('sections.service_company.toolbar',['id' => $job->id])
    <!--end::Page title-->
    <form action="" id="filter-form" style="">
      @csrf
      <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
      <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        @if (in_array('view_jobs', $userPermissions))
            {{-- Opens the same right-side job-details drawer (#kt_activitiesJob_show,
                 defined once in the shared layout) that the job title/logo already
                 uses elsewhere — .show-detail-open's click handler in
                 service_company_layout.blade.php ajax-loads jobs.viewmore into it. --}}
            <a href="javascript:void(0);" data-id="{{ $job->id }}"
                class="btn btn-sm btn-flex btn-outline btn-outline-primary fw-bolder me-2 show-detail-open">
                View Job
            </a>
        @endif
        <?php
        if ($final_status_job == 1) {
        ?>
        <a href="javascript:void(0);">
            {{-- Always visible regardless of job status — hold/inactive jobs used to
                 hide this button outright, which just confused agencies. Now the
                 click handler itself (in footer-script below) intercepts hold/closed
                 jobs and explains why via a popup instead of silently disappearing. --}}
            <button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="addServiceCandidate" data-job-status="{{ $job->status }}">
                <span class="svg-icon svg-icon-primary svg-icon-1x">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                        </g>
                    </svg>
                </span> Add Candidate
            </button>
        </a>
        <?php
        }
        ?>
        <!--begin::Forward CV-->

		<a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 custombtn aptk-action-btn aptk-legacy-hide  fwd_resume d-none" data-toggle="modal" data-target="#fwd_resume"> <span class="svg-icon svg-icon-primary svg-icon-1x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <rect x="0" y="0" width="24" height="24"/>
          <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"/>
          <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"/>
        </g>
      </svg>
      </span> Fwd Resume </a>
	  
        <!--end::Forward CV-->
        <!--begin::Send Email-->
        
		<!--<a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 d-none send_email" data-toggle="modal" data-target="#send_email"> <span class="svg-icon svg-icon-primary svg-icon-1x">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
            <path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
          </g>
        </svg>
        </span> Send Email </a>-->
		
        <!--end::Send Email-->
        <!--begin::Copy-->
        <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 custombtn aptk-action-btn aptk-legacy-hide d-none application_copy" data-toggle="modal" data-target="#application_copy">
        <!--begin::Svg icon-->
        <span class="svg-icon svg-icon-primary svg-icon-1x">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z" fill="#000000" fill-rule="nonzero"/>
            <path d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z" fill="#000000" opacity="0.3"/>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        Copy </a>
        <!--end::Copy-->
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 me-3 custom_hide">
          <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
          
          <!--end::Svg Icon-->
          <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <!--end::Search-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          
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
            <!--end::Svg Icon-->
            Export</button>
            <!--end::Export-->
            @if(in_array("add_job_applications", $userPermissions))
            <!--begin::Add Candidate-->

<?php 
if( ($final_status_job == 1) && ($job->status == 'active') )
{
?>

<!--<a href="<?php echo url('service_company/job-applications/clientcreate/'.@$job->id.'/0'); ?>">
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
-->

{{-- <a href="javascript:void(0);">
<button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3 custom_hide" id="addServiceCandidate">
<span class="svg-icon svg-icon-primary svg-icon-1x">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
	<polygon points="0 0 24 0 24 24 0 24"/>
	<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
	<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
  </g>
</svg>
</span> Add Candidate </button>
</a> --}}

<?php 
}
?>
            <!--end::Add Candidate-->
            @endif </div>
          <!--end::Toolbar-->
          <!--begin::Group actions-->
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

				$allCandidateCounter	=	@$applicationCounter->allCandidate > 0 ? @$applicationCounter->allCandidate : 0; 	
				$SourcingCounter		=	@$applicationCounter->Sourcing > 0 ? @$applicationCounter->Sourcing : 0;
				$ScreeningCounter		=	@$applicationCounter->Screening > 0 ? @$applicationCounter->Screening : 0 ;		
				$InterviewCounter		=	@$applicationCounter->Interview > 0 ? @$applicationCounter->Interview : 0;	
				$NegotiationCounter		=	@$applicationCounter->Negotiation > 0 ? @$applicationCounter->Negotiation : 0;	
				$OfferedCounter			=	@$applicationCounter->Offered > 0 ? @$applicationCounter->Offered : 0;	
				$JoinedCounter			=	@$applicationCounter->Joined > 0 ? @$applicationCounter->Joined : 0; 	
				$RejectedCounter		=	@$applicationCounter->Rejected > 0 ? @$applicationCounter->Rejected : 0;	
?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <div id="" class="container-fluid d-flex flex-stack second_header">
         @include('sections.service_company.clientappHeader',['tabactive'=>$tabactive])
      </div>
        
        <div class="card hp-toolbar-card mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3 custm-bg mb-0">
                    <div class="flex-grow-1" style="position: relative; min-width: 0;">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search by candidate name, skills, recruiter, education or location..." id="kt_tagify_2" onchange="filtersdataget()" />
                    </div>
                    <div style="width: 220px; flex-shrink: 0;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Upload Date)</option>
                            <option value="2">Oldest to Newest (Upload Date)</option>
                            <option value="3">Low to High (Salary)</option>
                            <option value="4">High to Low (Salary)</option>
                            <option value="21">Notice Period (Immediate)</option>
                            <option value="22">Notice Period (Serving)</option>
                            <option value="7">High to Low (Experience) </option>
                            <option value="8">Low to High (Experience)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $hpKnownStages = ['Sourcing', 'Screening', 'Interview', 'Negotiation', 'Offered', 'Joined', 'Rejected'];
            $hpActiveStage = (!empty(@$candStatusClass['Index']) && in_array(@$candStatusClass['Index'], $hpKnownStages))
                ? $candStatusClass['Index']
                : null;
            $hpTotalActive = !empty($mstatus_id) && $mstatus_id == 'all';

            $hpStages = [
                ['key' => 'Sourcing', 'rel' => '1', 'label' => 'Sourcing', 'color' => '#7e8299', 'bg' => '#eef0f2',
                    'icon' => 'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
                ['key' => 'Screening', 'rel' => '2', 'label' => 'Screening', 'color' => '#c000ff', 'bg' => '#f9e6ff',
                    'icon' => 'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
                ['key' => 'Interview', 'rel' => '3', 'label' => 'Evaluation', 'color' => '#2896ff', 'bg' => '#e8f4ff',
                    'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
                ['key' => 'Negotiation', 'rel' => '4', 'label' => 'Negotiation', 'color' => '#ff4098', 'bg' => '#ffe6f2',
                    'icon' => 'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
                ['key' => 'Offered', 'rel' => '5', 'label' => 'Offer', 'color' => '#ffab00', 'bg' => '#fff4e0',
                    'icon' => 'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
                ['key' => 'Joined', 'rel' => '6', 'label' => 'Joined', 'color' => '#06c732', 'bg' => '#e3fbe9',
                    'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
            ];
            $hpRejected = ['key' => 'Rejected', 'rel' => '7', 'label' => 'Rejected', 'color' => '#ff0202', 'bg' => '#ffe3e3',
                'icon' => 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'];
            $hpCounters = [
                '1' => @$SourcingCounter,
                '2' => @$ScreeningCounter,
                '3' => @$InterviewCounter,
                '4' => @$NegotiationCounter,
                '5' => @$OfferedCounter,
                '6' => @$JoinedCounter,
                '7' => @$RejectedCounter,
            ];
        ?>

        <div class="card hp-card mb-4">
            <div class="card-body">
                <div class="hp-flow">
                    <a class="application_sttus_nav hp-total-link" href="javascript:void(0);" rel="all">
                        <div class="<?php echo $hpTotalActive ? 'bg-total-active' : 'bg-gray'; ?> text-center hp-total" id="bg_all">
                            <span class="hp-total-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                </svg>
                            </span>
                            <span class="hp-total-count" id="bg_alldata"><?php echo @$allCandidateCounter; ?></span>
                            <span class="hp-total-label">Total Candidates</span>
                        </div>
                    </a>
                    <span class="hp-eq">=</span>
                    <?php foreach ($hpStages as $i => $stage): ?>
                        <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="<?php echo $stage['rel']; ?>">
                            <div class="hp-circle <?php echo $hpActiveStage === $stage['key'] ? 'active' : ''; ?>"
                                data-hp="<?php echo $stage['rel']; ?>"
                                style="--hp-color: <?php echo $stage['color']; ?>; --hp-bg: <?php echo $stage['bg']; ?>;">
                                <span class="hp-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="<?php echo $stage['icon']; ?>" />
                                    </svg>
                                </span>
                                <span class="hp-count" id="bg_<?php echo $stage['rel']; ?>"><?php echo @$hpCounters[$stage['rel']]; ?></span>
                            </div>
                            <div class="hp-label <?php echo $hpActiveStage === $stage['key'] ? 'active' : ''; ?>" id="tx_<?php echo $stage['rel']; ?>">
                                <?php echo $stage['label']; ?>
                            </div>
                        </a>
                        <?php if ($i < count($hpStages) - 1): ?>
                            <span class="hp-chevron">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 6 6 6-6 6" />
                                </svg>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="hp-divider"></span>
                    <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="<?php echo $hpRejected['rel']; ?>">
                        <div class="hp-circle <?php echo $hpActiveStage === $hpRejected['key'] ? 'active' : ''; ?>"
                            data-hp="<?php echo $hpRejected['rel']; ?>"
                            style="--hp-color: <?php echo $hpRejected['color']; ?>; --hp-bg: <?php echo $hpRejected['bg']; ?>;">
                            <span class="hp-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="<?php echo $hpRejected['icon']; ?>" />
                                </svg>
                            </span>
                            <span class="hp-count" id="bg_<?php echo $hpRejected['rel']; ?>"><?php echo @$hpCounters[$hpRejected['rel']]; ?></span>
                        </div>
                        <div class="hp-label <?php echo $hpActiveStage === $hpRejected['key'] ? 'active' : ''; ?>" id="tx_<?php echo $hpRejected['rel']; ?>">
                            <?php echo $hpRejected['label']; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Row selection action bar — hidden until candidates are checked. Reuses the
             exact same trigger classes as the legacy toolbar buttons (fwd_resume /
             application_copy) — this page has no working mass_mail/export handlers, so
             those buttons are intentionally omitted here. --}}
        <div class="aptk-selection-bar" id="aptk_selection_bar">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" id="aptk_selection_master_chk" checked title="Clear selection" />
            </div>
            <span class="aptk-selection-count"><span id="aptk_selected_count">0</span> candidates selected</span>
            <div class="aptk-selection-actions">
                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary fwd_resume" data-toggle="modal" data-target="#fwd_resume">
                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/><path d="m9 14 3 3 3-3"/><path d="M12 17v-6"/></svg>
                    </span> Forward Resume
                </a>
                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary application_copy" data-toggle="modal" data-target="#application_copy">
                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                    </span> Copy
                </a>
            </div>
            <button type="button" class="aptk-selection-close" id="aptk_selection_close" title="Clear selection">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5"> @if(in_array("view_job_applications", $userPermissions))
          <!--begin::Table-->
          <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
          <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="myTable">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input chk_list" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Candidate Name</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Notice</th>
                <th class="">Interview</th>
                <th class="">Location</th>
                <th class="">Ownership</th>
                <th class="">Stage</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <tbody class="text-gray-600 fw-bold" id="candidatetList">
            </tbody>
          </table>
          <!--end::Table-->
          <div id="paginationcandidate"></div>
          @endif </div>
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
<!--<div class="modal fade" id="client_stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="client_stage_sub_stage_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="client_stage_body_content" id="client_stage_body_content"></div>
    </div>
  </div>
</div>-->

<div class="modal fade" id="client_stage_sub_stage_new" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-bs-dismiss="modal" data-dismiss="modal"  id="client_stage_sub_stage_new_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="client_stage_body_content" id="client_stage_body_content"></div>
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
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Forward Resume</h2>
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
      <div class="card-body position-relative px-0 py-0" id="application_fwd_resume_body_content"></div>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
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



@endsection
@push('footer-script')
<script>
		
		$('body').on('click', '#AddCandidate', function(){
			var url					=	'{{ route('service_company.job-applications.create') }}';
			window.location.href 	=	url;			
		});
		
</script>


<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>
<script>

    @if(session('packageStatus'))
    Swal.fire("Failed", "{{ session('packageStatus') }}" , "warning");	
    @endif

   
   $( document ).ready(function() {
        var current_nav_active  = $('#mstatus_id').val(); 
      	//alert(current_nav_active);
      	if(current_nav_active == 'all'){
            $('.nav_status').html('All Candidate'); 
            $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
            $('.nav_status').css('color','#fff'); 
        } 
      
      	$(document).on('click', '.application_sttus_nav', function(){

            $('.hp-circle').removeClass('active');
            $('.hp-label').removeClass('active');
            $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');

            var page  = $(this).attr('rel');
            var jobid = $('#jobid').val();
            var jobIdEncrypt = '{{ $jobIdEncrypt }}';

            if(page == 1)
            {
                $('.hp-circle[data-hp="1"]').addClass('active');
                $('#tx_1').addClass('active');
                $('.nav_status').html('Sourcing');
                $('.nav_status').css('background','#efefef');
                $('.nav_status').css('color','#000000');

            }
            else if(page==2)
            {
                $('.hp-circle[data-hp="2"]').addClass('active');
                $('#tx_2').addClass('active');
                $('.nav_status').html('Screening');
                $('.nav_status').css('background','#fff9ed');
                $('.nav_status').css('color','#ffab00');
            }
            else if(page==3)
            {
                $('.hp-circle[data-hp="3"]').addClass('active');
                $('#tx_3').addClass('active');
                $('.nav_status').html('Interview');
                $('.nav_status').css('background','#f0f8ff');
                $('.nav_status').css('color','#2896ff');
            }
            else if(page==4)
            {
                $('.hp-circle[data-hp="4"]').addClass('active');
                $('#tx_4').addClass('active');
                $('.nav_status').html('Negotiation');
                $('.nav_status').css('background','#ffe6f2');
                $('.nav_status').css('color','#ff4098');
            }
            else if(page==5)
            {
                $('.hp-circle[data-hp="5"]').addClass('active');
                $('#tx_5').addClass('active');
                $('.nav_status').html('Offered');
                $('.nav_status').css('background','#d4ffde');
                $('.nav_status').css('color','#06c732');
            }
            else if(page==6)
            {
                $('.hp-circle[data-hp="6"]').addClass('active');
                $('#tx_6').addClass('active');
                $('.nav_status').html('Joined');
                $('.nav_status').css('background','#f7deff');
                $('.nav_status').css('color','#c000ff');
            }
            else if(page==7)
            {
                $('.hp-circle[data-hp="7"]').addClass('active');
                $('#tx_7').addClass('active');
                $('.nav_status').html('Rejected');
                $('.nav_status').css('background','#ffe2e5');
                $('.nav_status').css('color','#ff0202');
            }
            else if(page=='all')
            {
                $('.nav_status').html('All Candidate');
                $('.nav_status').css('background','linear-gradient(to left, #01e074, #0581d9)');
                $('.nav_status').css('color','#FFFFFF');
                $('#bg_all').removeClass('bg-gray').addClass('bg-total-active');
            }


            $('#mstatus_id').val(page);

            var url   	= '<?php echo url('/service_company/apptracker/clientapp') ?>';
            url     	= url+'/'+jobIdEncrypt+'/'+page;
            window.history.pushState('page2', 'Title', url);
            // tableLoad('load');
            fetchCandidates();
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
           var url = '{{ route('service_company.jobtracker.addclass')}}?id=' + id;
           $('#modelHeading').html('Schedule');
           $.ajaxModal('#scheduleDetailModal', url);       
       });
      
    });
	   
</script>
<script>

	   $(document).ready(function() { 
        $('#kt_tagify_2').keyup(function() {
        
          // tableLoad('filter');   
          fetchCandidates();
          //alert("call");   
        });
	   });
          
   
           $('#start-date').datepicker({
               format: 'yyyy-mm-dd'
           })
           $('#end-date').datepicker({
               format: 'yyyy-mm-dd'
           })
   
           var table;
          //  tableLoad('load');
           // For select 2
           $(".select2").select2();
           $('#reset-filters').click(function () {
      
        //$('#myTable').DataTable().column('').search('').draw();
        var table = $('#myTable').DataTable();
        
        table.search( '' ).columns().search( '' ).draw();
    
        
               $('#filter-form')[0].reset();
              // $('#filter-form').find('select').val('all').trigger('change');
              //  tableLoad('load');
              fetchCandidates();
           })
           $('#apply-filters').click(function () {
              //  tableLoad('filter');
              fetchCandidates();
           });
      

   function filtersdataget(){
        		// tableLoad('load');	
            fetchCandidates();
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
   
                       var url = "{{ route('service_company.cvtracker.destroy',':id') }}";
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
                                   fetchCandidates();
                               }
                           }
                       });
                   }
               });
           });        
   
        $(document).on("click",".show-detail",function(){
        //alert("call");
        
               $(".right-sidebar").slideDown(50).addClass("shw-rside");
               var id       = $(this).data('row-id');
               var url    = "{{ route('service_company.job-applications.show',':id') }}";
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
			
			var url       		= '{{ route('service_company.apptracker.export', [':jobs', ':mstatus_id', ':start', ':length' ]) }}';
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
                  url: '{{route('service_company.apptracker.store')}}',
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
      //  url: '{{route('service_company.jobtracker.store')}}',
              var url = '{{ route('service_company.jobtracker.updateinformation') }}';
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
                  
                                   fetchCandidates();
                               }
                           }
                       });
                  
               
           });
      
      function updateCandidatePassport(currentvalue, candidate, jobid)
      {
        customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'passport');
      }
      
      function updateCandidateNoticePeriod(currentvalue, candidate, jobid)
      {
        customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'np_id');
      }
      
      function updateApplicationStaus(currentvalue, candidate, jobid)
      {
        updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'status_id');
      }
      
      function updateApplicationEducation(currentvalue, candidate, jobid)
      {
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
      //  url: '{{route('service_company.jobtracker.store')}}',
              var url = '{{ route('service_company.jobtracker.updateinformation') }}';
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
                  
                                   fetchCandidates();
                               }
                           }
                       });
        
      }   
      
      function updateApplicationStatus121(statusId, applicationId)
      {
        var statusId    = statusId;
        var applicationId   = applicationId;
                  
   
                  var url = "{{ route('service_company.job-applications.statusupdate',':statusId') }}";
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
               var url = '{{ route('service_company.cvtracker.create_status')}}?date=' + scheduleDate;
               $('#modelHeading_status').html('Schedule');
               $.ajaxModal('#scheduleDetailModal_status', url);
           }    
      
      function getMasterChild(current_status, current_application_id, job_id)
      {
          var id        = id;
          var url       = "{{ route('service_company.apptracker.getchild',':id') }}";
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
                        
                         fetchCandidates();
                      }
                    },
                    complete: function (data) {
                        //$('#childStatus_'+current_application_id).html('');
                     }
          });
       }
    
      function updateSubStage(current_status, current_application_id, job_id, parent_status)
      {
          var id        	= id;
          var url       	= "{{ route('service_company.apptracker.sub_stage',':id') }}";
          url         		= url.replace(':id', current_application_id);
          var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 
          //var token     	= "{{ csrf_token() }}";
          
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
                        
                         fetchCandidates();
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
          var url = '{{ route('service_company.apptracker.openRejFrm')}}?application_id=' + current_application_id+'&job_id='+current_job_id+'&parent_status_id='+current_parent_status+'&current_child_id='+current_child_value ;
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
             
						var url = "{{ route('service_company.job-applications.interview-show',':id') }}";
						url = url.replace(':id', id);
						$('#moveApplicationmodelHeading').html('Schedule');
						$.ajaxModal('#moveApplicationModal', url);
			   
		}
		   				
	
    </script>
<script>

	$('.chk_list').click(function(){ 
		
		if($(".chk_list").prop('checked') == true){
			var chk_list 	=	$('.chk_list').val();
      $('.custom_hide').addClass("d-none");
			$('.chk_list_item').attr('checked','checked');
		}else{
			$('.chk_list_item').removeAttr('checked');		
      $('.custom_hide').removeClass("d-none");
		}
	});	

</script>
<script>

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
			$('.send_email').addClass("d-none");
      $('.custom_hide').removeClass("d-none");
			$('.app_copy').addClass("d-none");
			$('.application_copy').addClass("d-none");
		}
		updateAptkSelectionBar();
	 });

	$(document).on('click', '.chk_list_item', ()=>{
	   var i=0;
	   var arr = [];
       $('.chk_list_item:checked').each(function () {
		   arr.push($(this).val());
       });

	   if(arr.length > 0){
	   		$('.fwd_resume').removeClass("d-none");
			$('.send_email').removeClass("d-none");
			$('.app_copy').removeClass("d-none");
			$('.application_copy').removeClass("d-none");
      $('.custom_hide').addClass("d-none");


	   }else{
	   		$('.fwd_resume').addClass("d-none");
			$('.send_email').addClass("d-none");
			$('.app_copy').addClass("d-none");
			$('.application_copy').addClass("d-none");
      $('.custom_hide').removeClass("d-none");
	   }
		updateAptkSelectionBar();
	 });

	/* Selection bar: live count + clear-selection controls (checkbox + close X). */
	function updateAptkSelectionBar() {
		var n = $('.chk_list_item:checked').length;
		$('#aptk_selected_count').text(n);
		$('#aptk_selection_bar').toggleClass('show', n > 0);
		$('#aptk_selection_master_chk').prop('checked', n > 0);
	}
	function clearAptkSelection() {
		$('.chk_list, .chk_list_item').prop('checked', false);
		$('.fwd_resume, .send_email, .app_copy, .application_copy').addClass('d-none');
		$('.custom_hide').removeClass('d-none');
		updateAptkSelectionBar();
	}
	$(document).on('click', '#aptk_selection_close', clearAptkSelection);
	$(document).on('change', '#aptk_selection_master_chk', function() {
		if (!$(this).is(':checked')) clearAptkSelection();
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

                            if (response.status == "block") {
                                 Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(function(){
                                  $("#kt_activitiesCopy_close").click();
                                 });
                                return false;
														}

														if (response.status == "success") {
															  $('#application_copy_body_content').html(response.view);
														}
											}
               				});
									
	});
	
</script>

<script> 

	$('.fwd_resume').click(function(){
	
						var id        	=	'{{ $job->id }}'
						var url       	=	"{{ route('service_company.cvtracker.fwd_resume',':id') }}";
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

	$(document).on('click', '.bench_assign_listing', function (){
   		$('#kt_activitiesBA_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.bench-candidates.bench_assign_listing',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   							type  		: 'GET',
   							url   		: url,
   							beforeSend	: function() {
                                     					$('#kt_activitiesBA_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       		},
   							success 	: function (response) 
   							{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activitiesBA_body').html(response.view);
   										}
   								}, 500);
     		  					
   							}
   	  			});
   	});
		 		   
</script>

<script> 
	
	 $(document).ready(function(){
   
   		$('body').on('click', '#int_stage_submit', function(e){
   			
   				e.preventDefault();

				
   				var master_status	=	$('input[name="master_status"]:checked').val();
   				if((master_status == 1) || (master_status == 7) || (master_status == 9) ){
            $('#int_stage_submit').prop('disabled',true);
            $('.indicator-label').hide();
            $('.indicator-progress').show();
						$.ajax({
										url				: '{{route('service_company.job-applications.client_stage_submit')}}',
										container		: '#status_frm',
										type			: "POST",
										data			: $('#status_frm').serialize(),
										success			: function (response) {
                      $('#int_stage_submit').prop('disabled',false);
                      $('.indicator-label').show();
                      $('.indicator-progress').hide();
                                  if(response.status == 'coinCheck'){
   																	 Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
                                      $('#client_stage_sub_stage_new').modal('hide');
                                     });	
                                    return false;
   																}

																				if(response.status == 'success'){
																					   Swal.fire("Success", 'Application stage update successfully !', "success").then(function(){ 
																						// $('#stage_sub_stage').modal('hide');
                                               $('#client_stage_sub_stage_new').modal('hide');
																					 });
																					    
																						// var table;
																						// tableLoad('load');
                                            var page = $('#paginationcandidate .active_disabled').data('page');
                                            fetchCandidates(page);
																						
																						$.unblockUI();
																					
																						
																						 
								   
																				}else if (response.status == 'cvpending'){ 																					 
																					 Swal.fire("Warning", 'Resume pending this job application.', "warning").then(function(){ 
                                                                                    $("#stage_sub_stage_close").click();
																					});	
																				}else if (response.status == 'candidatevalidation'){ 																					 
																					 Swal.fire("Warning", response.message, "warning").then(function(){ 
                                                                                    $("#stage_sub_stage_close").click();
																					});	
																				}
															}
   									})  
				}else{
							Swal.fire("Warning", 'Only Sourcing stage update!', "info");	
              
                     $('#client_stage_sub_stage_new').modal('hide');
				}
				 
   				e.run();	
   						
   		});
   
   })
   
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	

<script>

	$(document).ready(function(){
			$('body').on('click', '#addServiceCandidate', function(){

							// Hold/Closed jobs stay visible now (previously the button was
							// hidden entirely), but uploading a new profile isn't allowed —
							// explain why instead of silently disappearing.
							var jobStatus = $(this).data('job-status');
							if (jobStatus === 'hold' || jobStatus === 'inactive') {
								var statusLabel = jobStatus === 'hold' ? 'Hold' : 'Closed';
								Swal.fire(
									'Position ' + statusLabel,
									'The position has been ' + statusLabel + '. You cannot upload new profiles for this position — kindly connect with the Hiring Manager regarding the same.',
									'warning'
								);
								return false;
							}

							var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
							var token 				=	"{{ csrf_token() }}";							
							var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
              var eventId 			=	4; 
							
							$.ajax({
										type		: 'POST',
										url			: url,
										beforeSend	: function(){ },
										data		: {'_token': token, 'event':'CVACCESS','eventId':eventId},
										success		: function (response){
																																
																 
																			
																					if (response.status == "success")
																						{  
																																												  																																																																																		                                                                                                var url   			=	'<?php echo url('service_company/job-applications/clientcreate/'.GeneralMethod::value_encrypt(@$job->id).'/0'); ?>';
																								window.location 	=	url;
																							
																																																																																																	
																							/*swalWithBootstrapButtons.fire
																							(
																									'Success!',
																									'Working status update successfully.',
																									'success'
																							)*/
																						 }
																					 else if (response.status == "info")
																						{   
																							
																							sweetAlert("Fail!", response.message, "error");
																							
																								/*alert("call");  
																							swalWithBootstrapButtons.fire
																							(
																									'Fail!',
																									response.message,
																									'error'
																							);*/
																						 }
																												 
																			}
									});
					
			});
				
	});

</script>




<script>

			
				 
				   
				   		   
  $('body').on('click', '.client_stage_sub_stage', function(){              
              var app_id       = $(this).data('row-id');		
              var eventId = 4;										
								
              // const swalWithBootstrapButtons = Swal.mixin({
              // 												   customClass		: {
              // 																			confirmButton: 'btn btn-success',
              // 																			cancelButton: 'btn btn-danger'
              // 																  		},
              //   													buttonsStyling	: false
              // })
		
		          $('#client_stage_sub_stage_new').modal('show');  
		  
		  
		 					// var inner_url 				=	"{{ route('service_company.auth-custom-package.validateClientPackage') }}";
							// var inner_token 			=	"{{ csrf_token() }}";							
							// var inner_baseurl    		=	"{{ asset('assets/images/Loading2.gif') }}"; 
							
              // 							$.ajax({
              // 										type		: 'POST',
              // 										url			: inner_url,
              // 										beforeSend	: function(){ },
              // 										data		: {'_token': inner_token, 'event':'CVACCESS', 'app_id': app_id,'eventId':eventId},
              // 										success		: function (response){
																					
                                                    
              // if (response.status == "success")
              // {   
			
              var id       		=	$(this).data('row-id');			   
              var url    			=	"{{ route('service_company.job-applications.client_stage_body_content',':id') }}";
              url      			=	url.replace(':id', id);
              $.ajax({

                  type  		:	'GET',
                  url   		:	url,
                  success 	:	function (response) {
                                      if (response.status == "success") {
                                          $('#client_stage_body_content').html(response.view);
                                      }
                          }
                          
              });		
										
              // }
              // else if (response.status == "info"){ 																					 
              // 		// swalWithBootstrapButtons.fire('Fail!', response.message, 'error') 
              // }
                                                                      
              // }

	});
									
		  


  $(document).ready(function() {
            // $.noConflict();

            fetchCandidates();

            $(document).on('click', '#paginationcandidate .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcandidate .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcandidate .page-link').removeClass('disabled');
                });
            });


        });

        function fetchCandidates(page = 1, search = '') {
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = '';
            var cus_search = $('#kt_tagify_2').val();
            $('.loadingMessage').css({
                display: 'block'
            });
            $.ajax({
                url: `{!! route('service_company.apptracker.clientappdata_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    jobid: jobid,
                    mstatus_id: mstatus_id,
                    search: inputSearch,
                    sortingfilter: sortingfilter,
                    country: country,
                    cus_search: cus_search
                },
                success: function(response) {
                    $('.loadingMessage').css({
                        display: 'none'
                    });
                    $('#candidatetList').html(response.html);
                    $('#pool_counter').html(response.total);
                    $('#paginationcandidate').html(response.pagination);
                    $('#bg_alldata').html(response.allCandidateCounter);
                    $('#bg_1').html(response.SourcingCounter);
                    $('#bg_2').html(response.ScreeningCounter);
                    $('#bg_3').html(response.InterviewCounter);
                    $('#bg_4').html(response.NegotiationCounter);
                    $('#bg_5').html(response.OfferedCounter);
                    $('#bg_6').html(response.JoinedCounter);
                    $('#bg_7').html(response.RejectedCounter);

                }
            });
        }
        

</script>

 
@endpush 