@extends('layouts.service_company_layout')
@push('head-script')
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
            #client_stage_sub_stage {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #client_stage_sub_stage {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
            }
        }
    </style>
@endpush
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid py-4" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <div class="d-flex flex-column">
                        <h5 class="d-flex align-items-center text-dark fw-bolder">Talent Interviews</h5>
                        <span class="text-gray-600" id="total_record"></span> 
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                        <!--end::Svg Icon-->
                        <input type="hidden" name="inputSearch" id="inputSearch"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->


                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

                        </div>
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="fw-bolder me-5"> <span class="me-2"
                                    data-kt-user-table-select="selected_count"></span>Selected</div>
                            <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete
                                Selected</button>
                        </div>
                    </div>


                </div>
                <!--end::Actions-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        @if (in_array('view_bench', $userPermissions))
            <!--begin::Post-->
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <!--begin::Container-->
                <div id="kt_content_container" class="container-fluid px-3">
                    <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                        <div class="col-lg-4 d-flex align-items-center px-2" style="width:70%">
                            <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                                value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 30%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="sortingfilter" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                                onchange="filtersdataget()">
                                <option value="Show All" selected="">Sort</option>
                                <option value="1">Today Interview ({{ date('d M y') }})</option>
                                <option value="Feedback Pending">Feedback Pending's</option>
                                <option value="4">New to Old (Interview Date)</option>
                                <option value="3">Old to New (Interview Date)</option>
                            </select>
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="country" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                                style="border: 1px solid #ffffff;" onchange="filtersdata()">
                                <option value="110" selected="selected">Location</option>
                                <option value="111">India</option>
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
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card body-->
                        <div class="card-body pt-3 px-5">

                            @if (in_array('view_schedule', $userPermissions))
                                <div class="loadingMessage loadingMessagebenchCandidateInterviewScheduleAjax"
                                    style="display: none; position: absolute;">Processing...</div>
                                <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Talent Name</th>
                                            <th class="">Jobs</th>
                                            <th class="">Status</th>
                                            <th class="">Interview Mode</th>
                                            <th class="">Interviewers</th>
                                            <th class="">Source</th>
                                            <th class="">Interview Date</th>
                                            <th class="">Feedback</th>
                                            <th class="">Rating</th>
                                            <th class="">Stage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="benchCandidateInterviewScheduleAjax"> </tbody>
                                </table>
                                <div id="paginationbenchCandidateInterviewSchedule"></div>
                            @else
                                <table id="no-access" class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="">
                                                <div align="center">ACCESS DENIED CONTACT ADMINISTRATOR</div>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            @endif


                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Post-->
        @endif
    </div>
    <!--end::Content-->
    <!--begin::Modal - Interview Feedback-->
    <div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6"
        data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
    <div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Interview Feedback</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary data-bs-dismiss" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div id="load_interview_feedback">&nbsp;</div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}{{ TM_VERSION }}"
        type="text/javascript"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        $(document).ready(function() {
            $('#kt_tagify_2').keyup(function() {
                // table._fnDraw();
                fetchBenchInterviewScheduleCandidateList();
            });
        });

        function filtersdataget() {
            // table._fnDraw();
            fetchBenchInterviewScheduleCandidateList();
        }

        // loadTable();
        var table;

        // function loadTable() {

        //     var startDate = $('#start-date').val();
        //     if (startDate == '') {
        //         startDate = null;
        //     }

        //     var endDate = $('#end-date').val();
        //     if (endDate == '') {
        //         endDate = null;
        //     }

        //     startDate = null;
        //     endDate = null;

        //     var status = $('#status').val();
        //     var applicationID = $('#applicationID').val();
        //     var inputSearch = $('#inputSearch').val();
        //     var sortingfilter = $('#sortingfilter').val();
        //     var country = $('#country').val();
        //     var cus_search = $('#kt_tagify_2').val();

        //     table = $('#myTable').dataTable({

        //         responsive: true,
        //         processing: true,
        //         serverSide: true,
        //         destroy: true,
        //         ordering: false, //disable ordering
        //         "bLengthChange": false, //thought this line could hide the LengthMenu
        //         sDom: 'lrtip',
        //         oLanguage: {
        //             sLengthMenu: "_MENU_",
        //         },
        //         ajax: {
        //             'url': '{!! route('service_company.bench-candidates.bench-data') !!}',
        //             "data": function(d) {
        //                 d.search = $('#inputSearch').val(), d.sortingfilter = $('#sortingfilter').val(), d
        //                     .country = $('#country').val(), d.cus_search = $('#kt_tagify_2').val()
        //             }
        //         },
        //         "fnDrawCallback": function(settings, start, end, max, total, pre) {

        //             var total_record = this.fnSettings().fnRecordsTotal();
        //             $('#total_record').html(total_record);
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
        //                 width: '15%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Requisitions',
        //                 name: 'Requisitions',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Status',
        //                 name: 'Status',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },

        //             {
        //                 data: 'InterviewMode',
        //                 name: 'InterviewMode',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Interviewers',
        //                 name: 'Interviewers',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Source',
        //                 name: 'Source',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'InterviewDate',
        //                 name: 'InterviewDate',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Feedback',
        //                 name: 'Feedback',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Rating',
        //                 name: 'Rating',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             },
        //             {
        //                 data: 'Stage',
        //                 name: 'Stage',
        //                 width: '10%',
        //                 searchable: false,
        //                 orderable: false
        //             }
        //         ],

        //         //timeout: 500000,
        //         async: false

        //     });
        //     new $.fn.dataTable.FixedHeader(table);

        // }
        $(document).on('click', '.show-feedback', function() {
            $('#intervied_feedback_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.interview-schedule.showfeedback', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#load_interview_feedback').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#load_interview_feedback').html(response.view);
                    }
                }
            });
        });
        $(document).on('click', '#app_interview_schedule_reschedule', function() {

            $("#interview_feedback .close").click()
            var id = $(this).attr('rel');
            var url = "{{ route('service_company.job-applications.create-schedule', ':id') }}?reschedule=1";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#app_interview_schedule_content').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#app_interview_schedule_content').html(response.view);
                }
            });
        });




        $(document).ready(function() {
            fetchBenchInterviewScheduleCandidateList();
            $(document).on('click', '#paginationbenchCandidateInterviewSchedule .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchBenchInterviewScheduleCandidateList(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationbenchCandidateInterviewSchedule .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationbenchCandidateInterviewSchedule .page-link').removeClass('disabled');
                });

            });
        });

        function fetchBenchInterviewScheduleCandidateList(page = 1, search = '') {
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
            $('.loadingMessagebenchCandidateInterviewScheduleAjax').css({
                display: 'block'
            });
            $.ajax({
                url: `{!! route('service_company.bench-candidates.benchinterviewdata_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: $('#inputSearch').val(),
                    cus_search: $('#kt_tagify_2').val(),
                    country: $('#country').val(),
                    sortingfilter: $('#sortingfilter').val()
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidateInterviewScheduleAjax').css({
                        display: 'none'
                    });
                    $('#total_record').html(response.total);
                    $('#benchCandidateInterviewScheduleAjax').html(response.html);
                    $('#paginationbenchCandidateInterviewSchedule').html(response.pagination);
                }
            });
        }
    </script>
@endpush
