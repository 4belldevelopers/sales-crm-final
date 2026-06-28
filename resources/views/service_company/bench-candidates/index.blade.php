@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
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
            #kt_activitiesBenchApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesBenchApplication {
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

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }
    </style>
@endpush


@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="javascript:void(0);" class="text-primary">Inhouse Talents</a>
                    </h5>
                    {{-- <span class="text-gray-600" id="">000</span> --}}
                </div>
            </div>
            <div class="d-flex align-items-center py-1">
                <div class="d-flex align-items-center position-relative my-1 me-3">

                    <input type="hidden" id="inputSearch" name="inputSearch"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    @if (in_array('view_bench', $userPermissions))
        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">

            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-fluid px-3">

                    <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                        <div class="col-lg-4 d-flex align-items-center px-2" style="width:58%">
                            <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                                value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 25%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="sortingfilter" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                                onchange="filtersdataget()">
                                <option value="Show All" selected="">Sort</option>
                                <option value="1">Newest to Oldest (Upload Date)</option>
                                <option value="2">Oldest to Newest (Upload Date)</option>
                                <!--<option value="3">Low to High (Salary)</option>-->
                                <!--<option value="4">High to Low (Salary)</option>-->
                                <option value="9">Notice Period (Immediate)</option>
                                <option value="5">Low to High (Notice Period)</option>
                                <option value="6">High to Low (Notice Period)</option>
                                <option value="7">High to Low (Experience) </option>
                                <option value="8">Low to High (Experience)</option>
                            </select>
                        </div>
                        {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="filter_status" data-control="select2" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                                style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                                <option value="" selected="selected">All Status</option>
                                <option value="1">Pool</option>
                                <option value="2">Bench</option>
                                <option value="4">Pause</option>
                                <option value="3">Placed</option>
                            </select>
                        </div> --}}
                        <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 10%;">
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

                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 17%;">

                            <a href="javascript:void(0);" type="button"
                                class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3  py-4"
                                id="AddInternalPool"> <span class="svg-icon svg-icon-primary svg-icon-1x">
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
                                </span>Add Inhouse Talent
                            </a>
                        </div>
                    </div>
                    @php

                        $sourcing_bg = $screening_bg = $interview_bg = $benchleft_bg = $negotation_bg = $offer_bg = $joining_bg = $rejected_bg = $All_candidate_bg =
                            'alert bg-gray text-center circle_boxes';
                        $sourcing_text = $screening_text = $interview_text = $benchleft_text= $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text =
                            'text-center color-all';

                        $tr_class = 'bg-gray';
                        if (!empty($mstatus_id)) {
                            if ($mstatus_id == 'all') {
                                $tr_class = 'bg-total-active';
                            } else {
                                $tr_class = 'bg-gray';
                            }
                        }
                    @endphp


                    <div class="d-flex justify-content-space-around align-items-center custm-bg">
                        <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav all_talents"
                            href="Javascript:void(0);" rel="all" for="all_talents">
                            <div class="total_jobs mr-2">
                                <div class="text-center bg-total-active squre_box" role="alert" id="bg_all"> <span
                                        class="total-content-view">
                                        <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span
                                            id="totalcnt">{{ ($counts->all_records) ? $counts->all_records : 0 }}</span> </span> </div>
                                <h6 class="text-center color-all font-weight-600">&nbsp;Total Inhouse Talents</h6>
                            </div>
                            <input class="rating-input" name="status_manage" checked="checked" value="all" type="radio" id="all_talents">
                        </a> 
                        <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                            href="Javascript:void(0);" rel="1" for="talent_Pool">
                            <div class="sourcing mr-2">
                                <div class="bg text-center circle_boxes SourcingCounter {{ @$sourcing_bg }}" role="alert"
                                    id="bg_1"> {{ ($counts->pool) ? $counts->pool : 0 }} </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$sourcing_text }}" id="tx_1">
                                    Talent Pool</h6>
                            </div>
                            <input class="rating-input" name="status_manage" value="1" type="radio" id="talent_Pool">
                        </a> 
                        <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                            href="Javascript:void(0);" rel="2" for="bench_live">
                            <div class="screening mr-2">
                                <div class="bg text-center circle_boxes {{ @$screening_bg }}" role="alert" id="bg_2">
                                    {{( $counts->bench) ?  $counts->bench : 0 }} </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$screening_text }}" id="tx_2">
                                    Talent On Bench</h6>
                            </div>
                            <input class="rating-input" name="status_manage" value="2" type="radio" id="bench_live">
                        </a>
                         <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav"
                            href="Javascript:void(0);" rel="4" for="bench_pause">
                            <div class="negitation mr-2">
                                <div class="bg text-center circle_boxes {{ @$interview_bg }}" role="alert" id="bg_4">
                                    {{ ($counts->paused) ? $counts->paused : 0 }} </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$interview_text }}" id="tx_4">
                                    Talent On Hold </h6>
                            </div>
                            <input class="rating-input" name="status_manage" value="4" type="radio" id="bench_pause">
                        </a>
                         <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                            href="Javascript:void(0);" rel="3" for="bench_placed">
                            <div class="negitation mr-2">
                                <div class="bg text-center circle_boxes {{ @$negotation_bg }}" role="alert"
                                    id="bg_3"> {{ ($counts->placed) ? $counts->placed : 0 }} </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$negotation_text }}" id="tx_3">
                                    Talent Placed</h6>
                            </div>
                            <input class="rating-input" name="status_manage" value="3" type="radio" id="bench_placed">
                        </a> 
                        
                         <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav"
                            href="Javascript:void(0);" rel="5" for="bench_left">
                            <div class="negitation mr-2">
                                <div class="bg text-center circle_boxes {{ @$benchleft_bg }}" role="alert"
                                    id="bg_5"> {{ (@$counts->exited) ? $counts->exited : 0 }} </div>
                                <h6 class="tx text-center color-all font-weight-600 {{ @$benchleft_text }}" id="tx_5">
                                    Talent Exited</h6>
                            </div>
                            <input class="rating-input" name="status_manage" value="5" type="radio" id="bench_left">
                        </a>

                        
                    </div>



                    <div class="card">
                        <div class="card-body pt-3 px-5">
                            <div class="loadingMessage loadingMessagebenchCandidateBenchCandidatesAjax"
                                style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                                id="benchListingTable">
                                <thead>
                                    <tr class="fw-bolder text-muted" role="row">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </div>
                                        </th>
                                        <th class="">Talent Name</th>
                                        <th class="">Exp</th>
                                        <th class="">Rates</th>
                                        <th class="">Education</th>
                                        <th class="">Availability</th>
                                        <th class="">Location</th>
                                        <th class="">Status</th>
                                        <th class="">Upload</th>
                                        <th class="">Manager</th>
                                        <th class="">TOT</th>
                                        <th class="">SOU</th>
                                        <th class="">SCR</th>
                                        <th class="">EVA</th>
                                        <th class="">NEO</th>
                                        <th class="">OFF</th>
                                        <th class="">JOI</th>
                                        <th class="">REJ</th>
                                    </tr>
                                </thead>
                                <tbody id="benchCandidateBenchCandidatesAjax"> </tbody>
                            </table>
                            <div id="paginationbenchCandidateBenchCandidates"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    @endif
    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Inhouse Talent Status</h2>
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

    <div id="kt_activitiesHMB_toggle"></div>
    <div id="kt_activitiesHMB" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHMB_toggle" data-kt-drawer-close="#kt_activitiesHMB_close" style="z-index:999999;">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activitiesB_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Relationship Manager</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesHMB_close"> <span class="svg-icon svg-icon-1">
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
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHMB_body"> </div>
        </div>
    </div>
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

        $(document).on('click', '.application_sttus_nav', function() {

        $(".bg").removeClass('bg-Asourcing').addClass('bg-gray');
        $(".bg").removeClass('bg-Ascreening').addClass('bg-gray');
        $(".bg").removeClass('bg-Ainterview').addClass('bg-gray');
        $(".bg").removeClass('bg-Anegotation').addClass('bg-gray');
        $(".bg").removeClass('bg-Aoffer').addClass('bg-gray');
        $(".bg").removeClass('bg-Ajoiner').addClass('bg-gray');
        $(".bg").removeClass('bg-Arejected').addClass('bg-gray');
        $(".bg").removeClass('bg-benchleft').addClass('bg-gray');

        $("#tx_1").removeClass('text-Asourcing').addClass('color-all');
        $("#tx_2").removeClass('text-Ascreening').addClass('color-all');
        $("#tx_3").removeClass('text-Ainterview').addClass('color-all');
        $("#tx_4").removeClass('text-Anegotation').addClass('color-all');
        $("#tx_5").removeClass('text-Aoffer').addClass('color-all');
        $("#tx_6").removeClass('text-Ajoiner').addClass('color-all');
        $("#tx_7").removeClass('text-Arejected').addClass('color-all');
        $("#tx_5").removeClass('text-bench-left').addClass('color-all');

        $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');

        var page = $(this).attr('rel');
        var jobid = $('#jobid').val();

        var jobIdEncrypt = '';

        if (page == 1) {
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-Asourcing');
            $('#tx_' + page).removeClass('color-all').addClass('text-Asourcing');
            $('.nav_status').html('Sourcing');
            $('.nav_status').css('background', '#efefef');
            $('.nav_status').css('color', '#000000');
            $('#talent_Pool').prop('checked', true).trigger('change');
        } else if (page == 2) {
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-Ainterview');
            $('#tx_' + page).removeClass('color-all').addClass('text-Ainterview');
            $('.nav_status').html('Interview');
            $('.nav_status').css('background', '#f0f8ff');
            $('.nav_status').css('color', '#2896ff');
            $('#bench_live').prop('checked', true).trigger('change');
        } else if (page == 3) {
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-Ajoiner');
            $('#tx_' + page).removeClass('color-all').addClass('text-Ajoiner');
            $('.nav_status').html('Joined');
            $('.nav_status').css('background', '#f7deff');
            $('.nav_status').css('color', '#c000ff');
            $('#bench_placed').prop('checked', true).trigger('change');
        } else if (page == 4) {
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-Aoffer');
            $('#tx_' + page).removeClass('color-all').addClass('text-Aoffer');
            $('.nav_status').html('Offered');
            $('.nav_status').css('background', '#d4ffde');
            $('.nav_status').css('color', '#06c732');
            $('#bench_pause').prop('checked', true).trigger('change');
        } else if (page == 5) {
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-benchleft');
            $('#tx_' + page).removeClass('color-all').addClass('text-bench-left');
            $('.nav_status').html('Offered');
            $('.nav_status').css('background', '#ff4098');
            $('.nav_status').css('color', '#ff4098');
            $('#bench_left').prop('checked', true).trigger('change');
        } else if (page == 'all') {
            $('.nav_status').html('All Candidate');
            $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
            $('.nav_status').css('color', '#FFFFFF');
            $('#bg_' + page).removeClass('bg-gray').addClass('bg-total-active');
            $('#all_talents').prop('checked', true).trigger('change');
        }
        // var selectedValue = $("input[name='status_manage']:checked").val();
        // console.log("Selected value:", selectedValue);
        // alert(selectedValue);

        $('#mstatus_id').val(page);
        var page = $('#paginationbenchCandidateBenchCandidates .active_disabled').data('page');
          fetchBenchBenchCandidatesCandidateList(page);

        })


        $(document).ready(function() {
            $(document).on('click', '#AddInternalPool', function() {
                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var eventId = 5;
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
                            var url = '{{ url('service_company/bench-candidates/create') }}';
                            window.location = url;
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });

            });
        });


        $(document).ready(function() {
            $('#kt_tagify_2').keyup(function() {
                // $('#benchListingTable').DataTable().draw();
                fetchBenchBenchCandidatesCandidateList();
            });
        });

        var table;
        // tableLoad('load');

        function filtersdataget() {
            // $('#benchListingTable').DataTable().draw();
            fetchBenchBenchCandidatesCandidateList();
        }

        // function tableLoad(type) {

        //     var status = 'all';
        //     var jobs = 'all';
        //     var clients = 'all';
        //     var source = 'all';
        //     var search_passport = 'all';
        //     var search_np_id = 'all';
        //     var inputSearch = $('#inputSearch').val();
        //     var sortingfilter = $('#sortingfilter').val();
        //     var country = $('#country').val();
        //     var cus_search = $('#kt_tagify_2').val();

        //     table = $('#benchListingTable').dataTable({
        //         serverSide: true, // Enable server-side processing
        //         responsive: true,
        //         processing: true,
        //         ordering: false, //disable ordering      			
        //         destroy: true,
        //         stateSave: true,
        //         "bLengthChange": false,
        //         sDom: 'lrtip',
        //         ajax: {
        //             'url': '{!! route('service_company.bench-candidates.data') !!}',
        //             'data': function(d) {
        //                 d.search = $('#inputSearch').val(), d.sortingfilter = $('#sortingfilter').val(), d
        //                     .filter_status = $('#filter_status').val(), d.cus_search = $('#kt_tagify_2').val()
        //             }
        //         },
        //         "fnDrawCallback": function(oSettings) {
        //             $("body").tooltip({
        //                 selector: '[data-toggle="tooltip"]'
        //             });
        //         },

        //         "columnDefs": [{
        //                 "targets": 0
        //             },
        //             {
        //                 "targets": 1,
        //                 "class": "align-items-center"
        //             },
        //             {
        //                 "targets": 2
        //             },
        //             {
        //                 "targets": 3
        //             },
        //             {
        //                 "targets": 4
        //             },
        //             {
        //                 "targets": 5
        //             },
        //             {
        //                 "targets": 6
        //             },
        //             {
        //                 "targets": 7
        //             },
        //             {
        //                 "targets": 8
        //             },
        //             {
        //                 "targets": 9
        //             },
        //             {
        //                 "targets": 10
        //             },
        //             {
        //                 "targets": 11
        //             },

        //             {
        //                 "targets": 12
        //             },
        //             {
        //                 "targets": 13
        //             },
        //             {
        //                 "targets": 14
        //             },
        //             {
        //                 "targets": 15
        //             },

        //             {
        //                 "targets": 16
        //             }
        //         ],
        //         columns: [{
        //                 data: 'chkbox',
        //                 name: 'chkbox',
        //                 width: '1%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Name',
        //                 name: 'Name',
        //                 width: '10%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Exp',
        //                 name: 'Exp',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Salary',
        //                 name: 'Salary',
        //                 width: '5%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Qualifaction',
        //                 name: 'Qualifaction',
        //                 width: '2%',
        //                 orderable: true
        //             },

        //             {
        //                 data: 'Availability',
        //                 name: 'Availability',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Location',
        //                 name: 'Location',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Status',
        //                 name: 'Status',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Date',
        //                 name: 'Date',
        //                 width: '7%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Assigned',
        //                 name: 'Assigned',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'TOT',
        //                 name: 'TOT',
        //                 width: '2%',
        //                 orderable: true
        //             },

        //             {
        //                 data: 'SOU',
        //                 name: 'SOU',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'SCR',
        //                 name: 'SCR',
        //                 width: '2%',
        //                 orderable: true
        //             },

        //             {
        //                 data: 'EVA',
        //                 name: 'EVA',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'NEO',
        //                 name: 'NEO',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'OFF',
        //                 name: 'OFF',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'JOI',
        //                 name: 'JOI',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'REJ',
        //                 name: 'REJ',
        //                 width: '2%',
        //                 orderable: true
        //             }

        //         ]


        //     });
        //     new $.fn.dataTable.FixedHeader(table);


        // }
    </script>

    <script>
        

        $(document).on('click','.checkStatusSales',function(){
            // e.preventDefault();
              var id = $(this).val();
              var btn = $(this);
              var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
              var url       	=	"{{ route('service_company.bench-candidates.benchSwitch') }}";
              var token       =	"{{ csrf_token() }}";
              var isChecked = btn.prop('checked');
                   $.ajax({
                        type		: 'POST',
                        url			: url,
                        data		: {'_token': token, '_method': 'POST', 'id' : id},
                        beforeSend	: function() {
                                      $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                        },
                        success 	: function (response) {
                                if (response.status == "info") {

                                      // ❗ Revert switch state
                                      btn.prop('checked', !isChecked).trigger('change.bootstrapSwitch');

                                        Swal.fire({
                                            title: "Warning",
                                            text: response.message,
                                            icon: "warning",
                                            confirmButtonText: "OK",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            },
                                            buttonsStyling: false
                                        }).then((result) => {

                                            if (result.isConfirmed) {
                                                // Close modal AFTER confirmation
                                                $('#kt_activitiesBenchApplication_close').click();
                                            }

                                        });

                                        return false;
                                                                                                        
                                    }
                                if (response.status == "success") {
                                    sweetAlert("Success", response.message, "success");
                                    $('.checkStatusSales_text').text(response.pack_status);
                                    var page = $('#paginationbenchCandidateBenchCandidates .active_disabled').data('page');
                                    fetchBenchBenchCandidatesCandidateList(page);
                                                                                                    
                                }
                          }
                  });	

        });

        $(document).on('click', '.hmb-listing', function() {

            $('#kt_activitiesHMB_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesHMB_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesHMB_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });


        $(document).on('click', '.stage_sub_stage', function() {
            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.bench_application_status', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                    }
                }
            });

        });


        $(document).ready(function() {
            fetchBenchBenchCandidatesCandidateList();
            $(document).on('click', '#paginationbenchCandidateBenchCandidates .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchBenchBenchCandidatesCandidateList(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationbenchCandidateBenchCandidates .page-link').removeClass(
                        'disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationbenchCandidateBenchCandidates .page-link').removeClass(
                        'disabled');
                });

            });
        });

        function fetchBenchBenchCandidatesCandidateList(page = 1, search = '') {
            var status = 'all';
            var jobs = 'all';
            var clients = 'all';
            var source = 'all';
            var search_passport = 'all';
            var search_np_id = 'all';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            var selectedValue = $("input[name='status_manage']:checked").val();
            $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({
                display: 'block'
            });
            $.ajax({
                url: `{!! route('service_company.bench-candidates.data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: $('#inputSearch').val(),
                    cus_search: $('#kt_tagify_2').val(),
					filter_status: $('#filter_status').val(),
                    country: $('#country').val(),
                    sortingfilter: $('#sortingfilter').val(),
                    selectedValue: $("input[name='status_manage']:checked").val()
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({
                        display: 'none'
                    });
                    $('#benchCandidateBenchCandidatesAjax').html(response.html);
                    $('#paginationbenchCandidateBenchCandidates').html(response.pagination);
                    countDataGetBenchList();

                }
            });
        }

       function countDataGetBenchList(){
            $.ajax({
                    url: `{!! route('service_company.bench-candidates.countDataGetBenchList') !!}`,
                    type: 'GET',
                    data: {
                        countData: true,
                    },
                    success: function(response) {
                        $('#totalcnt').text(response.all_records);
                        $('#bg_1').text(response.pool);
                        $('#bg_2').text(response.bench);
                        $('#bg_4').text(response.paused);
                        $('#bg_3').text(response.placed);
                        $('#bg_5').text(response.exited);

                        
                    }
                });
       }

    </script>
@endpush
