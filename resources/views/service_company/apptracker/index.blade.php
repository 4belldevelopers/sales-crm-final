@extends('layouts.service_company_layout')
@push('head-script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"> --}}
    </script>
    <style>

       


        #send_email .modal-dialog {
            margin: .3rem auto !important;
        }

        #fwd_resume .modal-dialog {
            margin: .3rem auto !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {

            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0rem;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesApplication {
                width: 1025px;
                z-index: 999999 !important;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
                z-index: 999999 !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy {
                width: 1200px;
            }
        }

        .error {
            color: #FF0000;
            font-weight: bold;
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        .btn.custombtn {
            padding: 6px 6px 6px 6px !important;
        }
       
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
             @php
                    use App\Helper\GeneralMethod;
                    $lock_unlock = GeneralMethod::checksubscription();
                    $jobIdEncrypt = GeneralMethod::value_encrypt($job->id);


                     $pool_path = "javascript:void(0);";
                     $public_bench_path = url('/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id));
                     $internal_bench_path = url('/service_company/apptracker/internal_bench/' . GeneralMethod::value_encrypt(@$job->id));
                     $existing_path = url('/service_company/apptracker/similar_candidates/' . GeneralMethod::value_encrypt(@$job->id));

                        $career_url = url(
                            '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
                        );
                     $candidates_request_path = route('service_company.apptracker.candidates_request');

                     $tabactive = [
                    'pool_active'=>'tabactive',
                    'pool_url'=> $pool_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'agencyBench_active'=>'',
                    'agencyBench_url'=>$public_bench_path,
                    'agencyBench_id'=>'agencyBench_counter',
                    'benchTalentReq_active'=>'',
                    'benchTalentReq_url'=>$candidates_request_path,
                    'benchTalentReq_id'=>'benchTalentReq_counter',
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    'public_bench_counter' =>@$public_bench_counter,
                    'recomnded_counter' => @$recomnded_counter,
                    ];

                    // dd($tabactive);


            @endphp

            <!--begin::Page title-->
             @include('sections.service_company.toolbar',['id' => $job->id])

           
            <!--end::Page title-->
            <form action="" id="filter-form" style="">
                @csrf
                <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
                <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                    <!--begin::Forward CV-->
                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-1 custombtn d-none" data-toggle="modal"
                        data-target="#mass_mail"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                        fill="#000000" opacity="0.3" />
                                    <path
                                        d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span> Mass Email 
                    </a>

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder me-1 custombtn d-none fwd_resume" data-toggle="modal"
                        data-target="#fwd_resume"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                        fill="#000000" opacity="0.3" />
                                    <path
                                        d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span> Fwd Resume 
                    </a>

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder me-1 custombtn d-none application_copy"
                        data-toggle="modal" data-target="#application_copy">
                        <!--begin::Svg icon-->
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                    <path
                                        d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        Copy </a>
                    <!--end::Copy-->

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder before-event downloadexcell exportRecord d-flex align-items-center position-relative my-1 me-1 custombtn d-none"
                        data-toggle="modal" data-target="#application_copy" style="width: 100px;">
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                    <path
                                        d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        Export 
                    </a>

                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1 me-3 custom_hide">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <!--end::Svg Icon-->
                        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end custom_hide" data-kt-user-table-toolbar="base">








                        </div>
                        <!--end::Toolbar-->

                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="fw-bolder me-5"> <span class="me-2"
                                    data-kt-user-table-select="selected_count"></span>Selected</div>
                            <button type="button" class="btn btn-danger"
                                data-kt-user-table-select="delete_selected">Delete Selected</button>
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
                                        <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                            data-kt-users-modal-action="close">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                        rx="1" transform="rotate(-45 6 17.3137)"
                                                        fill="black" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                        transform="rotate(45 7.41422 6)" fill="black" />
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
                                            <select name="role" data-control="select2"
                                                data-placeholder="Select a role" data-hide-search="true"
                                                class="form-select form-select-solid fw-bolder">
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
                                            <label class="required fs-6 fw-bold form-label mb-2">Select Export
                                                Format:</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <select name="format"
                                                data-placeholder="Select a format" data-hide-search="true"
                                                class="form-select form-select-solid fw-bolder">
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
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-users-modal-action="cancel">Discard</button>
                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit"> <span
                                                    class="indicator-label">Submit</span> <span
                                                    class="indicator-progress">Please wait... <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
    @php

        $sourcing_bg = $screening_bg = $interview_bg = $negotation_bg = $offer_bg = $joining_bg = $rejected_bg = $All_candidate_bg =
            'alert bg-gray text-center circle_boxes';
        $sourcing_text = $screening_text = $interview_text = $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text =
            'text-center color-all';

        if (!empty(@$candStatusClass['Index'])) {
            if (@$candStatusClass['Index'] == 'Sourcing') {
                $sourcing_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $sourcing_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Screening') {
                $screening_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $screening_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Interview') {
                $interview_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $interview_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Negotiation') {
                $negotation_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $negotation_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Offered') {
                $offer_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $offer_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Joined') {
                $joining_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $joining_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Rejected') {
                $rejected_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $rejected_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } else {
                $All_candidate_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $All_candidate_text = 'text-center  ' . @$candStatusClass['nav_text'];
            }
        }

        $tr_class = 'bg-gray';
        if (!empty($mstatus_id)) {
            if ($mstatus_id == 'all') {
                $tr_class = 'bg-total-active';
            } else {
                $tr_class = 'bg-gray';
            }
        }

    @endphp

    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

        <div id="" class="container-fluid d-flex flex-stack second_header">

            @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])

        </div>
                
                


                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                    <div class="col-lg-4 d-flex align-items-center px-2" style="width:61%;position: relative;">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                            value="" placeholder="Search..." id="kt_tagify_2" />
                            <div style="position: absolute; top: 0px; right: 36px;">
                                <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                             </div>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 21%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="sortingfilter" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdataget()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Upload Date)</option>
                            <option value="2">Oldest to Newest (Upload Date)</option>
                            <option value="3">Low to High (Salary)</option>
                            <option value="4">High to Low (Salary)</option>
                            <!--<option value="9">Notice Period (Immediate)</option>-->
                            <option value="21">Notice Period (Immediate)</option>
                            <option value="22">Notice Period (Serving)</option>
                            <option value="7">High to Low (Experience) </option>
                            <option value="8">Low to High (Experience)</option>
                        </select>
                    </div>
                    {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-control="select2" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
                        <option value="110" selected="selected">Location</option>
                        <option value="111">India</option>
                     </select>
                  </div> --}}
                    {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 18%;">
                     <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-2">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                           </svg>
                        </span>
                        Filter
                     </button>
                  </div> --}}
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 18%;">

                        @if (in_array('add_job_applications', $userPermissions))
                            <a href="javascript:void(0);">
                                <button type="button"
                                    class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3 py-4"
                                    id="addServiceCandidate">
                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                    fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                <path
                                                    d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                    fill="#000000" fill-rule="nonzero" />
                                            </g>
                                        </svg>
                                    </span>
                                    Add Candidate
                                </button>
                            </a>
                        @endif
                        {{-- <a href="javascript:void(0);" style="background: #95f1a4; padding: 13px; border-radius: 5px;">
                      <select class="form-select form-select-transparent text-gray-900 fs-7 lh-1 fw-bold py-0 ps-3 w-auto" data-hide-search="true" id="action_type" data-control="select2" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;width: 0px !important;" onchange="filtersdata()">
                      <option value=""></option>
                      <option value="2">Import</option>
                      <option value="1">Sample download</option>
                    </select>
                  </a> --}}
                    </div>
                </div>


                <div class="d-flex justify-content-space-around align-items-center custm-bg">
                    <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav"
                        href="Javascript:void(0);" rel="all">
                        <div class="total_jobs mr-2">
                            <div class=" {{ @$tr_class }} text-center squre_box" role="alert" id="bg_all"> <span
                                    class="total-content-view">
                                    <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span
                                        id="totalcnt">0</span> </span> </div>
                            <h6 class="text-center color-all font-weight-600">&nbsp;Total</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                        href="Javascript:void(0);" rel="1">
                        <div class="sourcing mr-2">
                            <div class="bg text-center circle_boxes SourcingCounter {{ @$sourcing_bg }}" role="alert"
                                id="bg_1"> 0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$sourcing_text }}" id="tx_1">
                                Sourcing</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="2">
                        <div class="screening mr-2">
                            <div class="bg text-center circle_boxes {{ @$screening_bg }}" role="alert" id="bg_2">
                                0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$screening_text }}" id="tx_2">
                                Screening</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="3">
                        <div class="negitation mr-2">
                            <div class="bg text-center circle_boxes {{ @$interview_bg }}" role="alert" id="bg_3">
                                0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$interview_text }}" id="tx_3">
                                Evaluation</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="4">
                        <div class="negitation mr-2">
                            <div class="bg text-center circle_boxes {{ @$negotation_bg }}" role="alert"
                                id="bg_4"> 0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$negotation_text }}" id="tx_4">
                                Negotiation</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="5">
                        <div class="offer mr-2">
                            <div class="bg text-center circle_boxes {{ @$offer_bg }}" role="alert" id="bg_5">
                                0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$offer_text }}" id="tx_5">Offer
                            </h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="6">
                        <div class="joining mr-2">
                            <div class="bg text-center circle_boxes {{ @$joining_bg }}" role="alert" id="bg_6">
                                0 </div>
                            <h6 class="tx text-center color-all font-weight-600 {{ @$joining_text }}" id="tx_6">
                                Joined</h6>
                        </div>
                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                        href="Javascript:void(0);" rel="7">
                        <div class="rejected mr-2">
                            <div class="bg alert text-center circle_boxes   {{ @$rejected_bg }}" role="alert"
                                id="bg_7"> 0 </div>
                            <h6 class="tx text-center   font-weight-600 {{ $rejected_text }}" id="tx_7">Rejected
                            </h6>
                        </div>
                    </a>
                </div>
                <div class="card">
                    <div class="card-body pt-3 px-5">
                        @if (in_array('view_job_applications', $userPermissions))
                           <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input chk_list" type="checkbox"
                                                    data-kt-check="true"
                                                    data-kt-check-target="#kt_table_users .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Name</th>
                                        {{-- <th class="">Contacts</th> --}}
                                        <th class="">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="">Notice</th>
                                        <th class="">Qualification</th>
                                        <th class="">Interview</th>
                                        <th class="">Location</th>
                                        <th class="">Source</th>
                                        <th class="">Stage</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="candidatetList">
                                </tbody>
                            </table>
                            <div id="paginationcandidate"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Stage sub stage popup-->
    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Stage and sub stage</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="stage_sub_stage_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
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
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Select the job checkbox, then click "Save" to add the candidate</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    
                    <div class="">
                        <button type="button" id="submit-frm-btn" class="btn btn-primary copy_current_selected_applicant d-none"> 
                            <span class="indicator-label">Save to Jobs</span> 
                            <span class="indicator-progress">
                                Please wait...
                            </span> 
                        </button>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-danger me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>


    <div class="modal fade" id="fwd_resume" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Forward Resume</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_fwd_resume_body_content"></div>
            </div>
        </div>
    </div>


    <div id="kt_activitiesBA_toggle"></div>
    <div id="kt_activitiesBA" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBA_toggle" data-kt-drawer-close="#kt_activitiesBA_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Relationship Manager Listing</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesBA_body"> </div>
        </div>
    </div>



    <div class="modal fade" id="bulk_upload_condidate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Bulk Upload condidate</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger closebtn" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_bulk_upload_condidate_body_content">


                    <div class="modal-body">
                        <!-- Upload form -->
                        <form action="{{ route('service_company.import.sample-job-applications') }}" method="POST"
                            enctype="multipart/form-data" id="application_bulk_upload_condidate">
                            @csrf
                            <div class="form-group">
                                <label for="file">Choose File(s):</label>
                                <input type="hidden" name="job_id" value="{{ @$job->id }}">
                                <input type="file" class="form-control-file" id="file" name="file" multiple>
                            </div>
                            <button type="submit" id="uploadBtn" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary closebtn" data-dismiss="modal">Close</button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!--begin::Mass Mail - Modal-->
    <div class="modal fade" id="mass_mail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Send Email</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_mass_mail_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Mass Mail - Modal-->
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        @if (session('packageStatus'))
            Swal.fire("Failed", "{{ session('packageStatus') }}", "warning");
        @endif

        $(document).ready(function() {




            var current_nav_active = $('#mstatus_id').val();
            if (current_nav_active == 'all') {
                $('.nav_status').html('All Candidate');
                $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                $('.nav_status').css('color', '#fff');
            }
            
            $(document).on('click', '.application_sttus_nav', function() {

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

                var page = $(this).attr('rel');
                var jobid = $('#jobid').val();

                var jobIdEncrypt = '{{ $jobIdEncrypt }}';

                if (page == 1) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Asourcing');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Asourcing');
                    $('.nav_status').html('Sourcing');
                    $('.nav_status').css('background', '#efefef');
                    $('.nav_status').css('color', '#000000');
                } else if (page == 2) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Ascreening');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Ascreening');
                    $('.nav_status').html('Screening');
                    $('.nav_status').css('background', '#fff9ed');
                    $('.nav_status').css('color', '#ffab00');
                } else if (page == 3) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Ainterview');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Ainterview');
                    $('.nav_status').html('Interview');
                    $('.nav_status').css('background', '#f0f8ff');
                    $('.nav_status').css('color', '#2896ff');
                } else if (page == 4) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Anegotation');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Anegotation');
                    $('.nav_status').html('Negotiation');
                    $('.nav_status').css('background', '#ffe6f2');
                    $('.nav_status').css('color', '#ff4098');
                } else if (page == 5) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Aoffer');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Aoffer');
                    $('.nav_status').html('Offered');
                    $('.nav_status').css('background', '#d4ffde');
                    $('.nav_status').css('color', '#06c732');
                } else if (page == 6) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Ajoiner');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Ajoiner');
                    $('.nav_status').html('Joined');
                    $('.nav_status').css('background', '#f7deff');
                    $('.nav_status').css('color', '#c000ff');
                } else if (page == 7) {
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-Arejected');
                    $('#tx_' + page).removeClass('color-all').addClass('text-Arejected');
                    $('.nav_status').html('Rejected');
                    $('.nav_status').css('background', '#ffe2e5');
                    $('.nav_status').css('color', '#ff0202');
                } else if (page == 'all') {
                    $('.nav_status').html('All Candidate');
                    $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                    $('.nav_status').css('color', '#FFFFFF');
                    $('#bg_' + page).removeClass('bg-gray').addClass('bg-total-active');
                }

                $('#mstatus_id').val(page);
                 var  mstatus_id = page;
                if (mstatus_id) {
                    localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
                }
                localStorage.setItem('currentPage', 1);
                //  var mstatus_id = localStorage.getItem('mstatusValueSet');
                //  alert(mstatus_id+page);
                var url = "{{ url('/service_company/apptracker/') }}";
                url = url + '/' + jobIdEncrypt + '/' + page;
                window.history.pushState('page2', 'Title', url);
                fetchCandidates();

            })

        });
    </script>

    <script>
        //  var table;   
        //    tableLoad('load');

        document.addEventListener("DOMContentLoaded", function() {
            var inputJobSearch = document.querySelector("#kt_tagify_2");

            inputJobSearch.addEventListener("keyup", function() {
                // filtersdataget();
                fetchCandidates();
            });
        });


        function filtersdataget() {
            // tableLoad('load');	
            fetchCandidates();
        }
        // function tableLoad(type) 
        // 	{

        var jobid = $('#jobid').val();
        var mstatus_id = $('#mstatus_id').val();
        var inputSearch = $('#inputSearch').val();
        var sortingfilter = $('#sortingfilter').val();
        var country = $('#country').val();
        var cus_search = $('#kt_tagify_2').val();

        // 
        //  } 


        // Toggle the dropdown on button click



        function countder() {
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var url = '{{ route('service_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
            url = url.replace(':jobid', jobid);
            url = url.replace(':mstatus_id', mstatus_id);
            url = url.replace(':inputSearch', inputSearch);

            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {

                    var allCandidate = 0;
                    var Sourcing = 0;
                    var Screening = 0;
                    var Rejected = 0;
                    var Offered = 0;

                    var Negotiation = 0;
                    var Joined = 0;
                    var Interview = 0;

                    if (response.allCounter > 0) {
                        allCandidate = response.data.allCandidate;
                        Sourcing = response.data.Sourcing;
                        Screening = response.data.Screening;
                        Rejected = response.data.Rejected;
                        Offered = response.data.Offered;

                        Negotiation = response.data.Negotiation;
                        Joined = response.data.Joined;
                        Interview = response.data.Interview;
                    }

                    setTimeout(() => {
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
        }
        countder();
        $(document).on('click', '.stage_sub_stage', async function(e) {

            e.preventDefault();
            const connectedsub = await isInternetAvailable();
                if (!connectedsub) {
                    Swal.fire({
                        title: "No Internet",
                        text: "You are currently offline or the server is unreachable.",
                        icon: "warning",
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    return; // ⛔ STOP EXECUTION
                }

            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html('');
                        $('#stage_body_content').html(response.view);
                    }
                }
            });

        });

        $(document).on('click', '.master_status_div', function(e) {

            e.preventDefault();

            var id = $(this).data('master-status-id');
            $("#master_status_" + id).attr('checked', true);
            $('#main_substage').show();
            $('#comment_div').show();

            $("#inreview").attr('checked', false);
            $("#sourcing").attr('checked', false);
            $("#screening").attr('checked', false);
            $("#evaluation").attr('checked', false);
            $("#negotation").attr('checked', false);
            $("#offer").attr('checked', false);
            $("#joined").attr('checked', false);
            $("#rejected").attr('checked', false);

            if (id == 1) {
                $("#sourcing").attr('checked', true);
                $('lbl_master_status_' + id).addClass('sourcing-bg');
            } else if (id == 2) {
                $("#screening").attr('checked', true);
                $('lbl_master_status_' + id).addClass('screening-bg');
            } else if (id == 3) {
                $("#evaluation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('interview');
            } else if (id == 4) {
                $("#negotation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-negotation');
            } else if (id == 5) {
                $("#offer").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-offer');
            } else if (id == 6) {
                $("#joined").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-joined');
            } else if (id == 7) {
                $("#rejected").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-rejected');
                // $('#main_substage').hide();	
                $('#comment_div').show();
            } else if (id == 8) {
                $("#inreview").attr('checked', true);
                $('lbl_master_status_' + id).addClass('bg-light-warning text-warning');
                $('#main_substage').hide();
            }

            if (id > 0 && (id != 8)) {

                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('service_company.job-applications.child_stage', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#substage').html('<img src="' + baseurl + '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            if (response.html == '') {
                                $('#main_substage').hide();
                            }
                            $('#substage').html('');
                            $('#substage').html(response.html);
                        }
                    }
                });

            }

            e.run();

        });

        $(document).ready(function() {

          $(document).on('click', '.exportRecord', function() {

                var action_type = $(this).val();
                var id = 0;
                var url =
                "{{ route('service_company.candidateexport.candidate-job-applications', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";
                var jobId = "{{ @$job->id }}";
                var arr = [];
                var jobIdArr = [jobId];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    var wiid = $(this).attr('data-wiid');
                    arr.push(wiid);
                });

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'GET',
                        'arr': arr,
                        'jobIdArr': jobIdArr
                    },
                    beforeSend: function() {
                        $('.exportRecord').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {

                        if (response.status == 'info') {
                            Swal.fire("Success", ` ${response.message}`, "warning");

                            $('.exportRecord').html(
                                '<span class="svg-icon svg-icon-primary svg-icon-1x"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <rect x="0" y="0" width="24" height="24"></rect><path d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z" fill="#000000" fill-rule="nonzero"></path><path d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z" fill="#000000" opacity="0.3"></path></g></svg></span> Export '
                                );

                            return false;
                        }


                        window.location.href = response.file_url;

                        setTimeout(() => {
                            $('.exportRecord').text('Export');
                            $.ajax({
                                type: 'GET',
                                url: "{{ route('service_company.unlink.sample-job-applications', ':id') }}",
                                data: {
                                    '_token': token,
                                    '_method': 'GET',
                                    'file_name': response.file_name
                                },
                                beforeSend: function() {},
                                success: function(response) {


                                }
                            });

                        }, 2000);
                    }
                });


            });

            $(document).on('click', '#custom_stage_submit', async function(e) {

                e.preventDefault();
                 const connected = await isInternetAvailable();
                    if (!connected) {
                        Swal.fire({
                            title: "No Internet",
                            text: "You are currently offline or the server is unreachable.",
                            icon: "warning",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-warning"
                            }
                        });
                        return; // ⛔ STOP EXECUTION
                    }

                var master_status = $('input[name="master_status"]:checked').val();
                var comment = $('#comment').val();
                if (master_status == 8 && comment=='') {
                    Swal.fire("Warning", 'InReview Comment Required !', "info");
                } else {
                    $("#custom_stage_submit").prop("disabled", true);
                    $("#custom_stage_submit").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                        );

                    $.ajax({
                        url: '{{ route('service_company.job-applications.stage_submit') }}',
                        container: '#status_frm',
                        type: "POST",
                        data: $('#status_frm').serialize(),
                        success: function(response) {
                            $("#custom_stage_submit").prop("disabled", false);
                            $("#custom_stage_submit").html(
                                `<span class="indicator-label">Submit</span>`);
                            if (response.status == 'success') {
                                Swal.fire("Success", 'Application stage update successfully !',
                                    "success").then(function() {
                                    // $('#stage_sub_stage').modal('hide');
                                    $("#stage_sub_stage_close").click();
                                });
                                var page = $('#paginationcandidate .active_disabled').data('page');
                                fetchCandidates(page);
                                //  	var table;
                                // 				tableLoad('load');
                                // table._fnDraw();
                                load_interviewSchedule();
                            } else if (response.status == 'cvpending') {
                                Swal.fire("Warning", 'Resume pending this job application.',
                                    "warning").then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            } else if (response.status == 'candidatevalidation') {
                                Swal.fire("Warning", response.message, "warning").then(
                                function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            }
                        }
                    })
                }


            });
            e.run();
        })

        $(document).ready(function() {
          $(document).on('click', '#addServiceCandidate', function() {

                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var eventId = 4;
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'event': 'CVACCESS',
                        'eventId': eventId
                    },
                    success: function(response) {

                        if (response.status == "success") {
                            var url =
                                "{{ url('service_company/job-applications/create/' . GeneralMethod::value_encrypt(@$job->id) . '/0') }}";
                            window.location = url;
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });

            });

            $(document).on('change', '#action_type', function() {
                var action_type = $(this).val();
                var id = "{{ @$job->id }}";
                var url = "{{ route('service_company.export.sample-job-applications', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";
                if (action_type == 1) {
                    // alert(action_type);
                    // return false;
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'GET',
                            'job_id': id
                        },
                        beforeSend: function() {
                            //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                        },
                        success: function(response) {

                            window.location.href = response.file_url;
                            setTimeout(() => {

                                $.ajax({
                                    type: 'GET',
                                    url: "{{ route('service_company.unlink.sample-job-applications', ':id') }}",
                                    data: {
                                        '_token': token,
                                        '_method': 'GET',
                                        'file_name': response.file_name
                                    },
                                    beforeSend: function() {},
                                    success: function(response) {
                                        $('#action_type').val($(
                                            '#action_type option:first'
                                            ).val());
                                    }
                                });

                            }, 2000);
                        }
                    });
                } else {
                    if (action_type == 2) {
                        // alert("djd");
                        $('#bulk_upload_condidate').modal('show');
                    }

                }

            })
            $(document).on('change', '.closebtn', function() {
                $('#action_type').val($('#action_type option:first').val());
                $('#bulk_upload_condidate').modal('hide');
            });
            $(document).on('submit', '#application_bulk_upload_condidate', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                document.getElementById('uploadBtn').disabled = true;
                $('#uploadBtn').text('Uploading....');
                $.ajax({
                    url: "{{ route('service_company.import.sample-job-applications') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = (evt.loaded / evt.total) * 100;
                                console.log(
                                percentComplete); // Update your UI here with the progress percentage
                            }
                        }, false);

                        return xhr;
                    },
                    success: function(response) {
                        document.getElementById('uploadBtn').disabled = false;
                        if (response.status) {
                            alert(response.message);
                            $('#uploadBtn').text('Uploading....');
                            $('#bulk_upload_condidate').modal('hide');
                        }
                        // Handle success response here
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });


        });


        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
            } else {
                $('.chk_list_item').prop('checked', false);	
            }
        });
        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });
        $(document).on('click', '.chk_list_item', function() {
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });

        $(document).on('click', '.fwd_resume', function() {
            localStorage.setItem('toggleOpen', 'true');
            // alert("kf");
            $('#application_mass_mail_body_content').html('');
            $('#application_fwd_resume_body_content').html('');
            var id = {{ $job->id }};
            var url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            var jobIdArr = [id];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'jobIdArr': jobIdArr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#application_fwd_resume_body_content').html(response.view);
                    }
                }
            });

        });

        $(document).on('click', '.mass_mail', function() {

            $('#application_mass_mail_body_content').html('');
            $('#application_fwd_resume_body_content').html('');
            var id = {{ $job->id }};
            var arr = [];
            var jobIdArr = [id];
            $('.chk_list_item:checked').each(function() {

                var jobId = $(this).attr('data-jobId');
                arr.push($(this).val());
                jobIdArr.push(jobId);
            });

            console.log(jobIdArr);

            var id = jobIdArr[0];
            var url = "{{ route('service_company.cvtracker.mass_mail', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'jobIdArr': jobIdArr
                },
                cache: false,
                success: function(response) {
                    if (response.status == "success") {
                        $('#application_mass_mail_body_content').html(response.view);
                    }
                }
            });

        });
    </script>

    <script>
       $(document).on('click', '.application_copy', function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            var wiseId = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $('.chk_list_item:checked').each(function() {
                wiseId.push($(this).data('wiseid'));
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'wiseId': wiseId
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
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

 
       var page_no = localStorage.getItem('currentPage');
        if (!page_no) {
            page_no = 1;
            localStorage.setItem('currentPage', page_no);
        }

           

        function fetchCandidates(page = page_no, search = '') {

            var mstatus_id = localStorage.getItem('mstatusValueSet');
          
            if (mstatus_id) {
                try {
                    mstatus_id = JSON.parse(mstatus_id);
                } catch (e) {
                      var mstatus_id = $('#mstatus_id').val();
                   localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
                }
            } else {
                var mstatus_id = $('#mstatus_id').val();
                localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
            }
           

            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.apptracker.allDataAjax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  jobid : $('#jobid').val(),
                  mstatus_id : mstatus_id,
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  country : $('#country').val(),
                  cus_search : $('#kt_tagify_2').val()
                },
                success: function(response) {
                     countder();
                     $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#candidatetList').html(response.html);
                    $('#pool_counter').html(response.total);
                     localStorage.setItem('currentPage', 1);
                    var mstatusvalue = $('#mstatus_id').val();
                     localStorage.setItem('mstatusValueSet', mstatusvalue);
                    
                    $('#paginationcandidate').html(response.pagination);


                    if (mstatus_id == 1) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Asourcing');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Asourcing');
                    $('.nav_status').html('Sourcing');
                    $('.nav_status').css('background', '#efefef');
                    $('.nav_status').css('color', '#000000');
                } else if (mstatus_id == 2) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Ascreening');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Ascreening');
                    $('.nav_status').html('Screening');
                    $('.nav_status').css('background', '#fff9ed');
                    $('.nav_status').css('color', '#ffab00');
                } else if (mstatus_id == 3) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Ainterview');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Ainterview');
                    $('.nav_status').html('Interview');
                    $('.nav_status').css('background', '#f0f8ff');
                    $('.nav_status').css('color', '#2896ff');
                } else if (mstatus_id == 4) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Anegotation');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Anegotation');
                    $('.nav_status').html('Negotiation');
                    $('.nav_status').css('background', '#ffe6f2');
                    $('.nav_status').css('color', '#ff4098');
                } else if (mstatus_id == 5) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Aoffer');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Aoffer');
                    $('.nav_status').html('Offered');
                    $('.nav_status').css('background', '#d4ffde');
                    $('.nav_status').css('color', '#06c732');
                } else if (mstatus_id == 6) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Ajoiner');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Ajoiner');
                    $('.nav_status').html('Joined');
                    $('.nav_status').css('background', '#f7deff');
                    $('.nav_status').css('color', '#c000ff');
                } else if (mstatus_id == 7) {
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-Arejected');
                    $('#tx_' + mstatus_id).removeClass('color-all').addClass('text-Arejected');
                    $('.nav_status').html('Rejected');
                    $('.nav_status').css('background', '#ffe2e5');
                    $('.nav_status').css('color', '#ff0202');
                } else if (mstatus_id == 'all') {
                    $('.nav_status').html('All Candidate');
                    $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                    $('.nav_status').css('color', '#FFFFFF');
                    $('#bg_' + mstatus_id).removeClass('bg-gray').addClass('bg-total-active');
                }



                }
            });
        }

        $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job Selected!', 'Selected');
            } else {
                showToastr('error', 'Job Removed!', 'Removed');
            }

            // Show/hide the bulk action button based on overall selection
            if (checkedCount > 0) {
                $('.copy_current_selected_applicant').removeClass("d-none");
            } else {
                $('.copy_current_selected_applicant').addClass("d-none");
            }
        });


    </script>
@endpush
