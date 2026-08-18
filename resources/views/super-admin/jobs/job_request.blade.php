@extends('layouts.superadmin_layout')
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
            #kt_activitiesRecruiter {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesRecruiter {
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
    @php
        use App\Helper\GeneralMethod;

        $productAllowCompany = GeneralMethod::companiesType();

    @endphp
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center border-0 flex-wrap me-3 mb-5 mb-lg-0">





                <!--begin::Jobs-->
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);"
                            class="text-primary">Job Requests Pending</a> </h5>
                    <span class="text-gray-600" id="span_jobRequest"><?php echo @$dataCnt['jobRequest']; ?></span>
                </div>
                <!--end::Jobs-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Custom Req.-->
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a
                            href="{{ url('super-admin/jobs/job_request_accept/0') }}"
                            class="text-gray-800 text-hover-primary">Job Accepted</a> </h5>
                    <span class="text-gray-600" id="span_customRequisition"><?php echo @$dataCnt['customRequisition']; ?></span>
                </div>
                <!--end::Custom Req.-->

            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                    <!--end::Svg Icon-->
                    <input type="hidden" id="inputSearch" name="inputSearch" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                    <!--<input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">-->
                </div>
                <!--end::Search-->
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <!--begin::Filter-->

                        <!--end::Filter-->
                    </div>
                    <!--end::Toolbar-->
                </div>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">
                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">


                </div>
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">
                        <!--begin::Table-->
                        <div class="loadingMessage loadingMessagerecomendedrequestpendingList" style="display: none; position: absolute;">Processing...</div>
                        <table id="job_request" class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fw-bolder text-muted" role="row">
                                    <th class="w-10px pe-0">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_table_users .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="customedivclass">Jobs</th>
                                    <th class="customedivclass">Exp.</th>
                                    <th class="customedivclass">Salary</th>
                                    <th class="">Location</th>
                                    <th class="">Status</th>
                                    <th class="">Date</th>
                                    <th class="">To Company</th>
                                    <th class="">Conversation</th>
                                    {{-- <th class="">Terms</th>
                                    <th class="">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold" id="recomendedrequestpendingList">
                            </tbody>
                            <!--end::Table head-->
                        </table>
                        <div id="paginationrecomendedrequestpending"></div>
                        <!--end::Table-->
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



    <div id="kt_activitiesClientjob_toggle"></div>
    <div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
        <div class="card shadow-none rounded-0 w-100">

            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Direct Client Requisition</h3>

                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>

                </div>

                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesClientjob_close">

                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span>

                    </button>
                </div>
            </div>

            <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>

        </div>
    </div>




    <div id="kt_activitiesHM_toggle"></div>
    <div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark" id="company_or_client">Direct Client Contact Person</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesHM_close">
                        <span class="svg-icon svg-icon-1">
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
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
        </div>
    </div>
    <!--end::Content-->


    <!--start terms::Content-->

    <div id="kt_activitiesTerms_toggle"></div>
    <div id="kt_activitiesTerms" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesTerms_toggle" data-kt-drawer-close="#kt_activitiesTerms_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark" id="company_or_client">Terms & Condition</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesTerms_close">
                        <span class="svg-icon svg-icon-1">
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
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0">

                <div class="card-body position-relative px-0 py-0" id="kt_terms_body">
                    <div id="kt_terms_scroll" class="position-relative scroll-y bg-light pt-2 h-100"
                        data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_terms_body"
                        data-kt-scroll-dependencies="#kt_terms_header, #kt_terms_footer" data-kt-scroll-offset="5px"
                        style="">
                        <div class="card card-custom mx-2">
                            <div class="card-body py-7" id="kt_activitiesTerms_body">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!--end terms ::Content-->

    <div id="kt_activitiesRecruiter_toggle"></div>
    <div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle"
        data-kt-drawer-close="#kt_activitiesRecruiter_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                            style="float:left"> <a href="javascript:void(0);" class="l">
                                <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
                            </a> </div>
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_recruiter_name">&nbsp;</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
        </div>
    </div>

    <div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6"
        data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
    <div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Conversation Feedback</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary data-bs-dismiss" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span> </div>
                </div>
                <div id="load_interview_feedback">
                    

                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        $(document).ready(function() {
            $('#inputSearch').keyup(function() {
                // tableLoad('load');
                fetchjobrequestpending();
            });
        });

        var table;
        // tableLoad('load');

        function tableLoad(type) {
            table = $('#job_request').dataTable({

                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                "bLengthChange": true,
                "lengthMenu"   : [[10, 25, 50, 100], [10, 25, 50, 100]],
                sDom: 'lrtip',

                ajax: {
                    'url': '{!! route('superadmin.company.recommandedrequest') !!}',
                    "data": function(d) {
                        d.search = $('#inputSearch').val()
                    }
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                "initComplete": function(settings, json) {},
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Requisition',
                        name: 'Requisition',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                    {
                        data: 'Exp',
                        name: 'Exp',
                        searchable: false,
                        orderable: false,
                        width: '15%'
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        searchable: false,
                        orderable: false,
                        width: '10%'
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Date',
                        name: 'Date',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'company_name_to',
                        name: 'company_name_to',
                        orderable: false
                    },
                    {
                        data: 'HM',
                        name: 'HM',
                        orderable: false
                    }
                    // {data: 'Terms', name: 'Terms', orderable: false},
                    // {data: 'Action', name: 'Action', orderable: false}

                ]
            });
            new $.fn.dataTable.FixedHeader(table);

        }

        $(document).on('click', '.client-terms-listing', function() {

            $('#kt_activitiesRecruiter_toggle').trigger('click');
            var id = $(this).data('row-id');
            var token = "{{ csrf_token() }}";


            var url = "{{ route('superadmin.company.hmlisting', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    '_token': token,
                    'id': id
                },
                beforeSend: function() {
                    $('#kt_activitiesRecruiter_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#kt_activitiesRecruiter_body').html(response.view);
                }
            });

        });

        $(document).on('click', '.show-feedback', function() {
            $('#intervied_feedback_toggle').trigger('click');
            var id = $(this).data('row-job_id');
            var conversations_id = $(this).data('row-conversations_id');
            var recommendation_relation_id = $(this).data('row-recommendation_relation_id');
            var url = "{{ route('superadmin.company.showfeedback', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#load_interview_feedback').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                data: {
                    'id': id,
                    'conversations_id': conversations_id,
                    'recommendation_relation_id': recommendation_relation_id
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#load_interview_feedback').html(response.view);
                    }
                }
            });
        });

        $(document).on('click', '.interview_mention_link', function() {

            Swal.fire({
                text: "The interview link either does not exist or has expired..",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });

        });


        $(document).ready(function() {
            // $.noConflict();

            fetchjobrequestpending();
            
            $(document).on('click', '#paginationrecomendedrequestpending .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchjobrequestpending(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationrecomendedrequestpending .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationrecomendedrequestpending .page-link').removeClass('disabled');
                });
            });


        });

 

        function fetchjobrequestpending(page = 1, search = '') {
            var clientid = '1';
            $('.loadingMessagerecomendedrequestpendingList').css({ display: 'block' });
            $.ajax({
                url: `{!! route('superadmin.company.all_recommandedrequest_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputSearch').val()
                },
                success: function(response) {
                    $('.loadingMessagerecomendedrequestpendingList').css({ display: 'none' });
                    $('#recomendedrequestpendingList').html(response.html);
                    // $('#span_jobRequest').html(response.total);
                    $('#paginationrecomendedrequestpending').html(response.pagination);
                }
            });
        }

    </script>
@endpush
