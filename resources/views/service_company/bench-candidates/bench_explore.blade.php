@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
    <style>
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
            #kt_activitiesJob {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesJob {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesClientjob_toggle {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClientjob_toggle {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            /* Adjust the height as per your requirement */
            position: relative;
        }
    </style>
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            
                @php
                    
                    $bench_explore_url = url('service_company/bench-candidates/bench_explore/') . @$bench_detail->id . '/all';
                    $recommend_req_url = url('service_company/bench-candidates/recommend_req/') .'/'. @$bench_detail->id;
                    $recommend_internal_req = url('service_company/bench-candidates/recommend_internal_req/') .'/'. @$bench_detail->id;

                    $tabactive = [
                    'bench_explore_pool_active'=>'tabactive',
                    'bench_explore_pool_url'=> $bench_explore_url,
                    'bench_explore_pool_id'=>'bench_explore_pool_id',
                    'bench_explore_counter' =>$recomCount['bench_explore_counter'],
                    'recommend_req_active'=>'',
                    'recommend_req_url'=>$recommend_req_url,
                    'recommend_req_id'=>'recommend_req_id',
                    'recommend_req_counter' =>$recomCount['recommend_req_counter'],
                    'recommend_req_internal_active'=>'',
                    'recommend_req_internal_url'=>$recommend_internal_req,
                    'recommend_req_internal_id'=>'recommend_req_internal_id',
                    'recommend_req_internal_counter' =>$recomCount['recommend_internal_req_counter'],
                    ];


                @endphp

                 @include('sections.service_company.bench-toolbar',['job' => @$job,'bench_detail'=>@$bench_detail])

          
            <form class="d-none" action="" id="filter-form" style="">
                @csrf
                <input type="hidden" name="bench_id" id="bench_id" value="{{ @$bench_id }}" />
                <input type="hidden" name="stage" id="stage" value="{{ @$stage }}" />
                <div class="d-flex align-items-center py-1">
                    <div class="d-flex align-items-center position-relative my-1 me-3">
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
                        <input type="text" id="inputSearch" name="inputSearch"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                data-kt-menu-flip="top-end">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                            fill="black" />
                                    </svg>
                                </span>
                                Filter
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1"
                                            transform="rotate(90 12.75 4.25)" fill="black" />
                                        <path
                                            d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                            fill="black" />
                                        <path
                                            d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                            fill="#C4C4C4" />
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
    @if (in_array('view_bench', $userPermissions))
        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-fluid px-3">

                    <div id="" class="container-fluid d-flex second_header">
                        @include('sections.service_company.benchExploreHeader',['tabactive'=>$tabactive])
                    </div>


                    <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                        <div class="col-lg-4 d-flex align-items-center px-2" style="width:65%">
                            <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                                value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="status" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                                style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                                <option value="">Status</option>
                                <option value="all">All</option>
                                <option value="active" selected="selected">Active</option>
                                <option value="hold">Hold</option>
                                <option value="inactive">Closed</option>
                                <option value="removed">Removed</option>
                            </select>
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="hiringtype" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                                onchange="filtersdataget()">
                                <option value="All">Sort</option>
                                <option value="1" selected="selected">Newest to Oldest (Date)</option>
                                <option value="2">Oldest to Newest (Date)</option>
                                <option value="3">Low to High (Salary)</option>
                                <option value="4">High to Low (Salary)</option>
                            </select>
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                            <button type="button"
                                class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                data-kt-menu-flip="top-end">
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


                    <div class="d-flex justify-content-space-around align-items-center custm-bg">
                        <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav"
                            href="Javascript:void(0);" rel="all">
                            <div class="total_jobs mr-2">
                                <div class="bg-total-active text-center squre_box" role="alert" id="bg_all">
                                    <span class="total-content-view">
                                        <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span
                                            id="totalcnt"> 0 </span>
                                    </span>
                                </div>
                                <h6 class="text-center color-all font-weight-600">&nbsp;Total</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                            href="Javascript:void(0);" rel="1">
                            <div class="sourcing mr-2">
                                <div class="bg text-center circle_boxes SourcingCounter {{ @$sourcing_bg }}"
                                    role="alert" id="bg_1"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$sourcing_text }}"
                                    id="tx_1">Sourcing</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="2">
                            <div class="screening mr-2">
                                <div class="bg text-center circle_boxes {{ @$screening_bg }}" role="alert"
                                    id="bg_2"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$screening_text }}"
                                    id="tx_2">Screening</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="3">
                            <div class="negitation mr-2">
                                <div class="bg text-center circle_boxes {{ @$interview_bg }}" role="alert"
                                    id="bg_3"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$interview_text }}"
                                    id="tx_3">Evaluation</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="4">
                            <div class="negitation mr-2">
                                <div class="bg text-center circle_boxes {{ @$negotation_bg }}" role="alert"
                                    id="bg_4"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$negotation_text }}"
                                    id="tx_4">Negotation</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="5">
                            <div class="offer mr-2">
                                <div class="bg text-center circle_boxes {{ @$offer_bg }}" role="alert"
                                    id="bg_5"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$offer_text }}" id="tx_5">
                                    Offer</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="6">
                            <div class="joining mr-2">
                                <div class="bg text-center circle_boxes {{ @$joining_bg }}" role="alert"
                                    id="bg_6"> 0 </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$joining_text }}" id="tx_6">
                                    Joined</h6>
                            </div>
                        </a>
                        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="7">
                            <div class="rejected mr-2">
                                <div class="bg alert text-center circle_boxes   {{ @$rejected_bg }}" role="alert"
                                    id="bg_7"> 0 </div>
                                <h6 class="tx text-center   font-weight-600 {{ @$rejected_text }}" id="tx_7">
                                    Rejected</h6>
                            </div>
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body pt-3 px-5">
                            <div class="loadingMessage loadingMessagebenchCandidateExploreJobAjax"
                                style="display: none; position: absolute;">Processing...</div>
                            <table id="bench_explore_listing"
                                class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </div>
                                        </th>
                                        <th class="d-flex align-items-center border-0">Jobs</th>
                                        <th class="" data-bs-toggle="tooltip" title="Experience">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="">Location</th>
                                        <th class="">Status</th>
                                        <th class="">Added Date</th>
                                        <th class="">Interview</th>
                                        <th class="">Stage</th>
                                    </tr>
                                </thead>
                                <tbody id="benchCandidateExploreJobAjax"> </tbody>
                            </table>
                            <div id="paginationbenchCandidateExploreJob"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="kt_activitiesJob_toggle"></div>
    <div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle"
        data-kt-drawer-close="#kt_activitiesJob_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
        </div>
    </div>
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>
    <script>
        $(document).ready(function() {
            var current_nav_active = $('#stage').val();
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
                var bench_id = $('#bench_id').val();

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


                $('#stage').val(page);

                var url = '<?php echo url('/service_company/bench-candidates/bench_explore/'); ?>';
                url = url + '/' + bench_id + '/' + page;
                window.history.pushState('page2', 'Title', url);
                // tableLoad('load');
                fetchBenchExploreJobCandidateList();



            })
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#kt_tagify_2').keyup(function() {
                // tableLoad('load');
                fetchBenchExploreJobCandidateList();
            });
        });

        var table;
        // tableLoad('load');

        function filtersdataget() {
            // tableLoad('load');
            fetchBenchExploreJobCandidateList();
        }

        function tableLoad(type) {
            var bench_id = $('#bench_id').val();
            var stage = $('#stage').val();
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#hiringtype').val();
            var statusfilter = $('#status').val();
            var cus_search = $('#kt_tagify_2').val();
                    var url =
                        '{{ route('service_company.bench-candidates.bench_explore_listing_counter', [':bench_id', ':stage', ':inputSearch']) }}';
                    url = url.replace(':bench_id', bench_id);
                    url = url.replace(':stage', stage);
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
                                Evaluation = response.data.Evaluation;
                            }
                            setTimeout(() => {
                                $('#bg_1').html(Sourcing);
                                $('#bg_2').html(Screening);
                                $('#bg_3').html(Evaluation);
                                $('#bg_4').html(Negotiation);
                                $('#bg_5').html(Offered);

                                $('#bg_6').html(Joined);
                                $('#bg_7').html(Rejected);
                                $('#totalcnt').html(allCandidate);

                            }, 100);
                        }
                    });
        }

        //  table.on('click', '.show-detail', function ()
        // 		{
        // 			$('#kt_activitiesJob_toggle').trigger('click');

        // 			var id    					=	$(this).data('row-id');
        // 			var job_applications_id		=	$(this).data('row-job_applications_id');

        // 			var url   					=	"{{ route('service_company.apptracker.benchReqViewMore', ['id' => ':id', 'job_applications_id' => ':job_applications_id']) }}";
        // 			url     					=	url.replace(':id', id);
        // 			url     					=	url.replace(':job_applications_id', job_applications_id);
        // 			var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 

        // 			$.ajax({
        // 							type  		:	'GET',
        // 							url   		:	url,
        // 							 beforeSend	:	function() {
        // 										 		$('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
        // 											},
        // 							success 	:	function (response) {
        // 																	setTimeout(
        // 																				()=>{
        // 																						if (response.status == "success"){
        // 																							$('#kt_activitiesJob_body').html(response.view);
        // 																						}
        // 																				}, 500);	
        // 																}
        // 				});	
        // 	 });


        $(document).on('click', '.show-detail', function() {
            // alert("{{ $bench_detail->id }}");
            $('#kt_activitiesJob_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url =
                "{{ route('service_company.jobs.viewmore', ':id') }}?viewmore=viewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesJob_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#kt_activitiesJob_body').html(response.view);
                }
            });
        });

        $(document).on('click', '.client-show-detail', function() {

            $('#kt_activitiesJob_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url =
                "{{ route('service_company.jobs.viewmore', ':id') }}?viewmore=clientviewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesJob_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#kt_activitiesJob_body').html(response.view);
                }
            });

            // $('#kt_activitiesClientjob_toggle').trigger('click');	
            // var id    		=	$(this).data('row-id');
            // var url   		=	"{{ route('service_company.jobs.clientviewmore', ':id') }}"; //clientviewmore
            // url     		=	url.replace(':id', id);
            // var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
            // $.ajax({
            // 				type  		: 'GET',
            // 				url   		: url,
            // 				 beforeSend	: function() {
            // 							 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');			
            // 				},
            // 				success 	: function (response) {
            // 						$('#kt_activitiesclientjob_body').html(response.view);		
            // 				}
            // 		});
        });



        $(document).ready(function() {
            fetchBenchExploreJobCandidateList();
            $(document).on('click', '#paginationbenchCandidateExploreJob .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchBenchExploreJobCandidateList(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationbenchCandidateExploreJob .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationbenchCandidateExploreJob .page-link').removeClass('disabled');
                });

            });
        });

        function fetchBenchExploreJobCandidateList(page = 1, search = '') {
            var bench_id = $('#bench_id').val();
            var stage = $('#stage').val();
            var search = $('#inputSearch').val();
            var sortingfilter = $('#hiringtype').val();
            var statusfilter = $('#status').val();
            var cus_search = $('#kt_tagify_2').val();
            $('.loadingMessagebenchCandidateExploreJobAjax').css({
                display: 'block'
            });
            $.ajax({
                url: `{!! route('service_company.bench-candidates.bench_explore_listing_job_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    bench_id: bench_id,
                    stage:stage,
                    search:search,
                    cus_search:cus_search,
                    statusfilter:statusfilter,
                    sortingfilter:sortingfilter
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidateExploreJobAjax').css({
                        display: 'none'
                    });
                    $('#benchCandidateExploreJobAjax').html(response.html);
                      $('#bench_explore_pool_id').html(response.total);
                    $('#paginationbenchCandidateExploreJob').html(response.pagination);
                    tableLoad('load');
                }
            });
        }
    </script>
@endpush
