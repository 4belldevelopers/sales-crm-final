@extends('layouts.service_company_layout')
@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
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
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
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

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        .selectednotice {
            background-color: #007bff !important;
            /* Example color */
            color: white !important;
        }

        .tagify__tag {
            display: flex;
            align-items: center;
        }

        .tagify__tag .star {
            margin-left: 0px;
            cursor: pointer;
            font-size: 21px;
            position: relative !important;
            top: 0px;
        }

        .tagify__tag .star.checked {
            color: #ef1632;
        }

        /* The original mass_mail toolbar button stays in the DOM (so its modal-trigger
           class keeps working unchanged) but is permanently hidden now — its restyled
           equivalent lives in #aptk_selection_bar below. */
        .aptk-legacy-hide {
            display: none !important;
        }

        /* Row selection action bar — same design/position as CV Tracker's
           #cvh_selection_bar: hidden until candidates are checked, then sits directly
           above the results table. Only Send Email is offered here (this page has no
           Forward Resume / Copy / Export functionality). */
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
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
               @php
                    use App\Helper\GeneralMethod;

                    $req_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt(@$job->id) . '/all');
                    $public_bench_path = url(
                        '/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $internal_bench_path = url(
                        '/service_company/apptracker/internal_bench/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $existing_path = url(
                        '/service_company/apptracker/similar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
                    );

                    $career_url = url(
                        '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
                    );
                    $candidates_request_path = route('service_company.apptracker.candidates_request');
                 
                    $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'tabactive',
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
                    <!--begin::Forward CV-->
                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-1 custombtn aptk-legacy-hide d-none" data-toggle="modal"
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

                    <!--begin::Search-->
                    <div class="d-none d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                    transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1 me-3">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                                <!--end::Svg Icon-->
                                <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                                    placeholder="Search..." />
                            </div>
                            <!--end::Search-->

                            <!--begin::Filter-->
                            <!--<button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">-->
                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                            <!--<span class="svg-icon svg-icon-2">-->
                            <!--<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">-->
                            <!--  <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />-->
                            <!--</svg>-->
                            <!--</span>-->
                            <!--end::Svg Icon-->
                            <!--Filter </button>-->



                            <!--end::Filter-->
                            <!--begin::Export-->
                            <button type="button"
                                class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2"
                                            rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                        <path
                                            d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                            fill="black" />
                                        <path
                                            d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                            fill="#C4C4C4" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                Export
                            </button>
                            <!--end::Export-->

                        </div>
                        <!--end::Toolbar-->
                    </div>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    @php
        //echo '<pre>'; print_r($candStatusClass);
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

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">

            <div id="" class="container-fluid d-flex flex-stack second_header">
               @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])

            </div>

                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 custm-bg mb-0">
                            <div class="flex-grow-1" style="position: relative; min-width: 0;">
                                <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                                    value="{{ @$default_skill }}" placeholder="Search by candidate name, skills, recruiter, education or location..." id="kt_tagify_2" />
                                <div data-toggle="modal" data-target="#apptrackerfilter"
                                    style="position: absolute; z-index: 99; width: 100%; height: 100%; top: 0;"> </div>
                            </div>
                            <div style="width: 220px; flex-shrink: 0;">
                                <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                    id="sortingfilter" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                                    onchange="filtersdataget()">
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
                            <div style="width: 140px; flex-shrink: 0;">
                                <button type="button"
                                    class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end"
                                    data-toggle="modal" data-target="#apptrackerfilter">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                fill="black"></path>
                                        </svg>
                                    </span>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!--Start Popup-->
                <div class="modal fade show" id="apptrackerfilter" tabindex="-1" style="">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header flex-stack">
                                <h2>Filter</h2>
                                <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"
                                    id="apptrackerfilter_close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                transform="rotate(45 7.41422 6)" fill="black"></rect>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="apptrackerfilter_body_content" id="">
                                <div class="modal-body" style="padding: 2em;">
                                    <form id="createSchedule" class="ajax-form" method="post">
                                        <div class="d-flex flex-column mb-8">
                                            <label class="form-label required">Enter skills to filter candidate</label>
                                            <input class="form-control form-control-solid d-flex align-items-center"
                                                name="kt_tagify_users" value="{{ @$default_skill }}" placeholder=""
                                                id="kt_tagify_users" />
                                            <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
                                            <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
                                            <script>
                                                const inputElm = document.querySelector('#kt_tagify_users');

                                                const tagify = new Tagify(inputElm, {
                                                    templates: {
                                                        tag: function(tagData) {
                                                            tagData = tagData || {};
                                                            const value = tagData.value || '';
                                                            const isChecked = tagData.checked ? 'checked' : '';
                                                            return `
                                                        <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                                                            <x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x>
                                                            <div>
                                                                <span class="star ${isChecked}">🟌</span>
                                                                <span class="tagify__tag-text">${value}</span>
                                                            </div>
                                                        </tag>
                                                    `;
                                                        }
                                                    },
                                                    transformTag: function(tagData) {
                                                        tagData.checked = tagData.checked || false;
                                                        return tagData;
                                                    }
                                                });

                                                // Add event listener for star click
                                                document.addEventListener('click', function(e) {
                                                    if (e.target.classList.contains('star')) {
                                                        const tagElm = e.target.closest('.tagify__tag');
                                                        const value = tagElm.querySelector('.tagify__tag-text').textContent;

                                                        e.target.classList.toggle('checked');

                                                        // Update the tag data
                                                        const tagData = tagify.value.find(tag => tag.value === value);
                                                        if (tagData) {
                                                            setTimeout(() => {
                                                                tagData.checked = e.target.classList.contains('checked');
                                                                tagify.updateValueByDOMTags();
                                                            }, 500);

                                                        }
                                                    }
                                                });



                                                // Initialize Tagify for kt_tagify_2
                                                const inputJobSearch = document.querySelector('#kt_tagify_2');
                                                // const tagify2 = new Tagify(inputJobSearch);
                                                const tagify2 = new Tagify(inputJobSearch, {
                                                    maxTags: 3 // Limit the number of tags to 5
                                                });

                                                // Function to update inputJobSearch based on tagify's value
                                                function updateInputJobSearch() {
                                                    const tags = tagify.value.map(tag => tag.value).join(',');
                                                    inputJobSearch.value = tags;
                                                    autoclickstar();
                                                }

                                                function updateTagifyUsers() {
                                                    const newTags = inputJobSearch.value.split(',').map(tag => ({
                                                        value: tag.trim()
                                                    }));
                                                    tagify.removeAllTags();
                                                    tagify.addTags(newTags);

                                                }


                                                // Listen for tag removal
                                                tagify.on('add', updateInputJobSearch);
                                                tagify.on('remove', updateInputJobSearch);
                                                inputJobSearch.addEventListener('change', updateTagifyUsers);

                                                // Initial sync
                                                updateInputJobSearch();

                                                // Sync tagify_users with inputJobSearch
                                                inputJobSearch.addEventListener('change', function() {
                                                    const newTags = inputJobSearch.value.split(',').map(tag => ({
                                                        value: tag.trim()
                                                    }));
                                                    tagify.loadOriginalValues(newTags);
                                                });


                                                function autoclickstar() {
                                                    setTimeout(() => {
                                                        var starElement = document.querySelector('.star');
                                                        starElement.click();
                                                        setTimeout(() => {
                                                            starElement.click();
                                                        }, 200);
                                                    }, 500);
                                                }

                                                function debounceskill(func, delay) {
                                                    let timeoutskill;
                                                    return function(...args) {
                                                        clearTimeout(timeoutskill);
                                                        timeoutskill = setTimeout(() => func.apply(this, args), delay);
                                                    };
                                                }

                                                 const fetchSuggestionsSkills = debounceskill(function(query) {
                                                    $.ajax({
                                                        url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
                                                        method: 'GET',
                                                        success: function(data) {
                                                            console.warn("Fetched Skill: ", data);

                                                            if (Array.isArray(data)) {
                                                                tagify.settings.whitelist = data; // Update whitelist
                                                                // tagify.dropdown.show(query); // Show suggestions
                                                                tagify.dropdown.show.call(tagify);
                                                            } else {
                                                                console.error("Invalid API response format: Expected array.");
                                                            }
                                                        },
                                                        error: function(err) {
                                                            console.error("Error fetching Skill:", err);
                                                        }
                                                    });
                                                }, 200); 

                                                // Listen for user input to trigger API call
                                                tagify.on('input', function(e) {
                                                    var query = e.detail.value.trim();
                                                    if (query.length < 1) return;
                                                    fetchSuggestionsSkills(query);
                                                });

                                                // new Tagify(inputElm);   
                                            </script>
                                            <!--<textarea class="form-control form-control-solid" rows="4" name=""
                                                placeholder="Enter your Skills/Keywords..."></textarea>-->
                                        </div>

                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <div class="d-flex flex-column mb-3 fv-row">
                                                <label class="form-label">Select location for candidate</label>
                                                <input class="form-control form-control-solid"
                                                    value="{{ $default_loc }}" id="kt_tagify_location"
                                                    placeholder="Enter your Location..." />
                                                <!--<input class="form-control form-control-solid" value="" name="tags" placeholder="Enter your Location...">-->
                                                <script>
                                                    // The DOM elements you wish to replace with Tagify
                                                    var inputloc = document.querySelector("#kt_tagify_location");
                                                    var cities = @json($cities);
                                                    // Initialize Tagify script on the above inputs
                                                    var tagifylocation = new Tagify(inputloc, {
                                                        whitelist: [],
                                                        // enforceWhitelist: true, // Prevents users from adding custom values  
                                                        maxTags: 10,
                                                        dropdown: {
                                                            maxItems: 20, // <- mixumum allowed rendered suggestions
                                                            classname: "", // <- custom classname for this dropdown, so it could be targeted
                                                            enabled: 0, // <- show suggestions on focus
                                                            closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
                                                        }
                                                    });
                                                    // Prevent creating tags on blur or enter key
                                                    tagifylocation.on('input', function(e) {
                                                        tagifylocation.dropdown.show(); // show the dropdown immediately
                                                    });

                                                    // Create tags only on select
                                                    tagifylocation.on('dropdown:select', function(e) {
                                                        tagifylocation.addTags([e.detail.data.value]);
                                                    });
                                                    tagifylocation.on('add', function(e) {
                                                        // alert(cities);
                                                        // Remove the tag if it wasn't selected from the dropdown
                                                        // if (!cities.includes(e.detail.data.value)) {
                                                        //     setTimeout(() => {
                                                        //         tagifylocation.removeTag(e.detail.data.value);
                                                        //     }, 500);

                                                        // }
                                                    });


                                                    function debounce(func, delay) {
                                                        let timer;
                                                        return function (...args) {
                                                            clearTimeout(timer);
                                                            timer = setTimeout(() => func.apply(this, args), delay);
                                                        };
                                                    }

                                                        // Debounced function to fetch suggestions
                                                    const fetchSuggestions = debounce(function(query) {
                                                        $.ajax({
                                                            url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                                                            method: 'GET',
                                                            success: function(data) {
                                                                console.warn("Fetched Cities: ", data);

                                                                if (Array.isArray(data)) {
                                                                    tagifylocation.settings.whitelist = data; // Update whitelist
                                                                    // tagifylocation.dropdown.show(query); // Show suggestions
                                                                    tagifylocation.dropdown.show.call(tagifylocation);
                                                                } else {
                                                                    console.error("Invalid API response format: Expected array.");
                                                                }
                                                            },
                                                            error: function(err) {
                                                                console.error("Error fetching cities:", err);
                                                            }
                                                        });
                                                    }, 200); // 300ms delay

                                                    // Listen for user input to trigger API call
                                                    tagifylocation.on('input', function(e) {
                                                        var query = e.detail.value.trim();
                                                        if (query.length < 2) return;
                                                        fetchSuggestions(query);
                                                    });

                                                    setTimeout(() => {
                                                    var city = "{{@$default_loc}}";
                                                    if(city !=''){
                                                        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
                                                        fetchSuggestions(cityName);
                                                    }
                                                    }, 100);
                                                    setTimeout(() => {
                                                        var defaultLocation = {!! json_encode(@$default_loc) !!}; // Ensure proper Blade syntax for JavaScript
                                        
                                                        if (defaultLocation) {
                                                            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
                                                            tagifylocation.dropdown.hide();
                                                        }
                                                    }, 5000); // 3 seconds delay
    
                                                </script>
                                            </div>
                                            <div class="d-flex">
                                                <div class="fw-semibold me-5 d-flex">
                                                    <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                        name="communication[]" value="email" checked="checked">
                                                    <div class="fs-7 text-muted">Include Candidate who prefer to relocate
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column mb-8">
                                            <label class="form-label">Education</label>


                                            <input class="form-control form-control-solid" value="{{ $qualification }}"
                                                id="kt_tagify_education" placeholder="About Education..." />
                                            <!--<input class="form-control form-control-solid" value="" name="tags" placeholder="Enter your Location...">-->
                                            <script>
                                                // The DOM elements you wish to replace with Tagify
                                                var inputdeducation = document.querySelector("#kt_tagify_education");
                                                var qualification = @json($qualificationTag);
                                                var tagifyqualification = new Tagify(inputdeducation, {
                                                    whitelist: qualification,
                                                    maxTags: 10,
                                                    dropdown: {
                                                        maxItems: 20, // <- mixumum allowed rendered suggestions
                                                        classname: "", // <- custom classname for this dropdown, so it could be targeted
                                                        enabled: 0, // <- show suggestions on focus
                                                        closeOnSelect: false // <- do not hide the suggestions dropdown once an item has been selected
                                                    }
                                                });

                                                // Prevent creating tags on blur or enter key
                                                tagifyqualification.on('input', function(e) {
                                                    tagifyqualification.dropdown.show(); // show the dropdown immediately
                                                });

                                                // Create tags only on select
                                                tagifyqualification.on('dropdown:select', function(e) {
                                                    tagifyqualification.addTags([e.detail.data.value]);
                                                });
                                                tagifyqualification.on('add', function(e) {
                                                    // alert(qualification);
                                                    // Remove the tag if it wasn't selected from the dropdown
                                                    if (!qualification.includes(e.detail.data.value)) {
                                                        setTimeout(() => {
                                                            tagifyqualification.removeTag(e.detail.data.value);
                                                        }, 200);

                                                    }
                                                });
                                            </script>
                                        </div>



                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <div class="col-lg-6">
                                                <label class="form-label required">Minimum Experience in years:</label>
                                                <select class="form-select form-select-solid" name="min_exp"
                                                    id="min_exp">
                                                    <option value="" selected="">Select
                                                        Experience</option>
                                                    <option value="0" {{ 0 == $min_exp ? 'selected' : '' }}>Freshers
                                                    </option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == $min_exp ? 'selected' : '' }}>{{ $i }}
                                                            &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label required">Maximum Experience in years:</label>
                                                <select class="form-select form-select-solid" name="max_exp"
                                                    id="max_exp">
                                                    <option value="" selected="">Select
                                                        Experience</option>
                                                    <option value="0" {{ 0 == $max_exp ? 'selected' : '' }}>Freshers
                                                    </option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == $max_exp ? 'selected' : '' }}>{{ $i }}
                                                            &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Currency</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="min_salary_currency"
                                                                    id="min_salary_currency">
                                                                    <option value="">Select currency</option>
                                                                    <option value="0"
                                                                        {{ 0 == $min_salary_currency ? 'selected' : '' }}>INR
                                                                    </option>
                                                                    <option value="1"
                                                                        {{ 1 == $min_salary_currency ? 'selected' : '' }}>EUR
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ 2 == $min_salary_currency ? 'selected' : '' }}>JPY
                                                                    </option>
                                                                    <option value="3"
                                                                        {{ 3 == $min_salary_currency ? 'selected' : '' }}>GBP
                                                                    </option>
                                                                    <option value="4"
                                                                        {{ 4 == $min_salary_currency ? 'selected' : '' }}>USD
                                                                    </option>
                                                                    <option value="5"
                                                                        {{ 5 == $min_salary_currency ? 'selected' : '' }}>AED
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Mode</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                                    <option value="">Select Mode</option>
                                                                    <option value="0"
                                                                        {{ 0 == $min_sal_mode ? 'selected' : '' }}>Annual</option>
                                                                    <option value="1"
                                                                        {{ 1 == $min_sal_mode ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="2"
                                                                        {{ 2 == $min_sal_mode ? 'selected' : '' }}>Hourly</option>
                                                                </select>
                                                            </div>
                                                        </div>   
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Minimum Salary:</label>
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="min_sal" id="min_sal" placeholder="Enter min salary"
                                                                    value="{{ $min_salary }}">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Maximum Salary:</label>
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="max_sal" id="max_sal" placeholder="Enter max salary"
                                                                    value="{{ $max_salary }}">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div>  
                                                            <span class="form-text text-muted" id="salary_error" style="    color: red !important;text-align: center;"></span>  
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                            <label class="form-label required">Notice Period/Availability to join</label>
                                            <div class="d-flex flex-stack gap-5 mb-3">

                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="1" id="app_int_notice1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Any</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="8" id="app_int_notice8">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice8">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-6">Immediate</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-3">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="9" id="app_int_notice9">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice9">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">Served
                                                                Notice</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-4">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="7" id="app_int_notice7">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice7">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">Currently
                                                                Serving Notice</span>
                                                        </span>
                                                    </label>
                                                </div>







                                            </div>
                                            <div class="d-flex flex-stack gap-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="2" id="app_int_notice2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">0-15
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="3" id="app_int_notice3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">1
                                                                Month</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="4" id="app_int_notice4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">2
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="5" id="app_int_notice5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">3
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-3">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="6" id="app_int_notice6">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice6">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">More than 3
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>



                                            </div>
                                        </div>

                                        <div class="d-flex flex-column mb-8">
                                            <label class="form-label required">Stages</label>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="0" id="app_int_status0"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status0">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="8" id="app_int_status8">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status8">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Inreview</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="1" id="app_int_status1">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Sourcing</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="2" id="app_int_status2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Screening</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="3" id="app_int_status3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Evaluation</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="5" id="app_int_status5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Offer</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 mt-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="4" id="app_int_status4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Negotiation</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 mt-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="7" id="app_int_status7">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status7">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Rejected</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="d-flex flex-column mb-8">
                                            <label class="form-label required">Activity</label>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="1" id="app_int_activity1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="2" id="app_int_activity2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">30
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="3" id="app_int_activity3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">60
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="4" id="app_int_activity4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">90
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="5" id="app_int_activity5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">180
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="6" id="app_int_activity6">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity6">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">1
                                                                Year</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>







                                    </form>
                                </div>
                                <!--<div class="modal-footer" style="display: table;">-->
                                <!--  <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal">Close <i class="fa fa-circle fa-sm"></i> </button>-->
                                <!--  <button type="button" class="btn btn-light-success btn-sm p-1" id="" onclick="newUpdateJobstatus()">Update <i class="fa fa-spinner fa-sm"></i> </button>-->
                                <!--</div>-->
                                <div class="modal-footer flex-center">
                                    <div class="div1">
                                        <button type="button" id="" class="btn btn-secondary me-3"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" id="changetextupdate" class="btn btn-primary"
                                            onclick="filtersdataget()">
                                            <span class="indicator-label changetextupdate">Submit</span></button>
                                    </div>
                                </div>
                                <script src="https://servicetesting.4bell.com/assets/node_modules/moment/moment.js?ver=1553620716"
                                    type="text/javascript"></script>
                                <script src="https://servicetesting.4bell.com/assets/node_modules/multiselect/js/jquery.multi-select.js?ver=1553620716">
                                </script>
                                <script
                                    src="https://servicetesting.4bell.com/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js?ver=1553620716"
                                    type="text/javascript"></script>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Popup-->

                {{-- Row selection action bar — hidden until candidates are checked.
                     Reuses the same mass_mail trigger class as the legacy toolbar button,
                     so the existing modal/ajax handler works unchanged; only the
                     presentation and position (directly above the results table) are
                     new. Only Send Email is offered here (no Forward Resume / Copy /
                     Export on this page). --}}
                <div class="aptk-selection-bar" id="aptk_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="aptk_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="aptk-selection-count"><span id="aptk_selected_count">0</span> candidates selected</span>
                    <div class="aptk-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" data-toggle="modal" data-target="#mass_mail">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                    </div>
                    <button type="button" class="aptk-selection-close" id="aptk_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">
                      <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="myTable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                    <th class="w-10px pe-0">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input chk_list" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th class="">Candidate Name</th>
                                    {{-- <th class="">Contacts</th> --}}
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Notice</th>
                                    <th class="">Location</th>
                                    <th class="">Jobs</th>
                                    <th class="">Status</th>
                                    <th class="">Ownership</th>
                                    {{-- <th class="">Date</th> --}}
                                    <th class="">Stage</th>
                                    <th class="">Action</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <tbody class="text-gray-600 fw-bold" id="candidatetList">
                            </tbody>
                        </table>
                        <div id="paginationcandidate"></div>
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
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect fill="#000000" opacity="0.5"
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
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        $(document).ready(function() {

            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_exp", function() {
                experience_check()
            });
            $(document).on("change", "#max_exp", function() {
                experience_check()
            });

            $(document).on('keyup', '#min_sal', function() {
                // alert("dkd");
                var number = parseInt($('#min_sal').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));

            });
            $(document).on('keyup', '#max_sal', function() {
                var number = parseInt($('#max_sal').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));

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
            updateAptkSelectionBar();
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
            updateAptkSelectionBar();
        });

        /* Selection bar: live count + clear-selection controls (checkbox + close X) —
           same behavior as the Pool tab / CV Tracker's #cvh_selection_bar. */
        function updateAptkSelectionBar() {
            var n = $('.chk_list_item:checked').length;
            $('#aptk_selected_count').text(n);
            $('#aptk_selection_bar').toggleClass('show', n > 0);
            $('#aptk_selection_master_chk').prop('checked', n > 0);
        }
        function clearAptkSelection() {
            $('.chk_list, .chk_list_item').prop('checked', false);
            $('.fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
            $('.custom_hide').removeClass('d-none');
            updateAptkSelectionBar();
        }
        $(document).on('click', '#aptk_selection_close', clearAptkSelection);
        $(document).on('change', '#aptk_selection_master_chk', function() {
            if (!$(this).is(':checked')) clearAptkSelection();
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

        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function convertToWords(num) {
            if (num < 10) {
                return ones[num];
            } else if (num < 20) {
                return teens[num - 10];
            } else if (num < 100) {
                return tens[Math.floor(num / 10)] + ' ' + ones[num % 10];
            } else if (num < 1000) {
                return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100);
            } else if (num < 100000) {
                return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000);
            } else if (num < 10000000) {
                return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000);
            } else if (num < 1000000000) {
                return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        function salary_check() {
            var minimum_salary = parseInt($("#min_sal").val());
            var maximum_salary = parseInt($("#max_sal").val());
            if (minimum_salary != '') {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Enter the maximum salary greater than the minimum salary',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_sal").val('');
                }
            }
        }

        function experience_check() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (min_exp != '') {
                if (min_exp > max_exp) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Select the maximum experience greater than the minimum experience',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_exp").val('');
                }
            }
        }





        var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
        new Tagify(input2, {
            whitelist: [""],
            maxTags: 15,
            dropdown: {
                maxItems: 20, // <- mixumum allowed rendered suggestions
                classname: "", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: false // <- do not hide the suggestions dropdown once an item has been selected
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('[data-kt-modal-bidding="option"]');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove the "selected" class from all buttons
                    buttons.forEach(btn => btn.classList.remove('selectednotice'));

                    // Add the "selected" class to the clicked button
                    this.classList.add('selectednotice');
                });
            });
        });
    </script>
    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');	
        }, 500);

        function filtersdataget() {
            $('.changetextupdate').text('Searching....!');
            setTimeout(() => {
                // tableLoad('load');
                fetchCandidates();
            }, 1000);
            var $this = $('#apptrackerfilter');
            // $this.fadeOut(2000, function() {
            $this.modal('hide');
            // });
            setTimeout(() => {
                $('#apptrackerfilter_close').click();
                $('.changetextupdate').text('Submit')
            }, 2000);


        }

        function tableLoad(type) {

            var jobid = '{{ @$job->id }}';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();

            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.similar_existing_data') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&app_int_notice=' + app_int_notice + '&sal_mode=' +
                    sal_mode,
                "fnDrawCallback": function(oSettings) {},
                "initComplete": function(settings, json) {
                    $('.clientStatusLoader').html('');
                    $('.jobLoader').html('');
                    $('.sourceLoader').html('');
                    $('.search_np_idLoader').html('');
                    $('.search_passportLoader').html('');
                    $('.statusLoader').html('');
                },

                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        orderable: false,
                        searchable: false,
                        width: '12%'
                    },
                    // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '10%'},						
                    {
                        data: 'Exp',
                        name: 'Exp',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'NP',
                        name: 'NP',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Qualification',
                        name: 'Qualification',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        orderable: false
                    },
                    {
                        data: 'Date',
                        name: 'Date',
                        orderable: false
                    },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        orderable: false
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        orderable: false
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        orderable: false
                    },
                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false
                    }
                ]
            });
            new $.fn.dataTable.FixedHeader(table);

            countexist();

        }


        function countexist() {
            setTimeout(() => {
                // var rowCount = $('#myTable tbody tr:not(.dataTables_empty)').length;
                var allRowCount = $('#myTable tbody tr').length;

                // Count rows that have a cell with the class dataTables_empty
                var emptyRowCount = $('#myTable tbody tr:has(td.dataTables_empty)').length;
                var existingDataCount = allRowCount - emptyRowCount;
                $('.existing_data').text(existingDataCount);


            }, 2500);
        }
    </script>
    <script>
        
        $(document).on('click', '.add_to_inreview', function(e) {
            e.preventDefault(); // 🚫 stop anchor default behavior

            var $btn = $(this);

            // 🔒 Prevent double click
            if ($btn.hasClass('processing')) {
                return false;
            }
            $btn.addClass('processing')
                .addClass('disabled')
                .css('pointer-events', 'none');


            var button = $(this); // Store the button element for later use

            var id = $(this).data('row-id');
            var source_from_type = $(this).data('row-source_from_type');
            var source_jobid = $(this).data('row-jobid');
            var source_tab = $(this).data('row-from');
            var wiseId = $(this).data('row-wiseid');
            // alert(wiseId);

            var url = '{{ route('service_company.apptracker.rec_candidate_assign') }}';
            var token = "{{ csrf_token() }}";

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Add to In-Review!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                     $btn.removeClass('processing disabled')
                        .css('pointer-events', '');

                        
                    var inner_url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                    var inner_token = "{{ csrf_token() }}";
                    var inner_baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    var eventId = 9;

                    // Disable the button
                    button.prop('disabled', true);

                    $.ajax({
                        type: 'POST',
                        url: inner_url,
                        beforeSend: function() {},
                        data: {
                            '_token': inner_token,
                            'event': 'internaldatabaseaccess',
                            'app_id': id,
                            'source_jobid': source_jobid,
                            'eventId': eventId
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $.ajax({
                                    type: 'GET',
                                    url: url,
                                    data: {
                                        'id': id,
                                        'source_from_type': source_from_type,
                                        'source_jobid': source_jobid,
                                        'source_tab': source_tab,
                                        'wiseId': wiseId
                                    },
                                    success: function(response) {


                                        if (response.status == 'coinCheck') {
                                            Swal.fire("Success",
                                                ` ${response.message}`,
                                                "warning");
                                            return false;
                                        }

                                        if (response.status == 'success') {
                                            // Enable the button again if needed
                                            button.prop('disabled', false);

                                            // tableLoad('load');
                                            var page = $('#paginationcandidate .active_disabled').data('page');
                                            fetchCandidates(page);
                                            swalWithBootstrapButtons.fire(
                                                'In-Review!',
                                                'Selected request has been In-Review.',
                                                'success'
                                            )
                                        }
                                    }
                                });
                            } else if (response.status == "info") {
                                swalWithBootstrapButtons.fire('Fail!', response.message,
                                    'error')
                            }
                        }
                    });

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Selected request is safe',
                        'error'
                    );
                    
                     $btn.removeClass('processing disabled')
                        .css('pointer-events', '');
                }
            })
        });


        $(document).ready(function() {
            // $.noConflict();
            
            setTimeout(() => {
                fetchCandidates();
            }, 200);
            
            
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
            var jobid = '{{ @$job->id }}';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();

            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.apptracker.similar_existing_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                jobid:jobid, 
                search:inputSearch,
                sortingfilter:sortingfilter, 
                country:country,
                cus_search:(cus_search),
                min_exp:min_exp,
                max_exp:max_exp, 
                kt_tagify_location:kt_tagify_location,
                min_sal:min_sal,
                max_sal:max_sal, 
                min_salary_currency:min_salary_currency,
                kt_tagify_education:kt_tagify_education,
                app_int_status:app_int_status,
                app_int_activity:app_int_activity,
                app_int_notice:app_int_notice, 
                sal_mode:sal_mode
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('#candidatetList').html(response.html);
                    $('#similar_counter').html(response.total);
                    $('#paginationcandidate').html(response.pagination);
                }
            });
        }
    </script>
@endpush
