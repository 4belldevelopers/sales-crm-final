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
    </style>
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="javascript:void(0);" class="text-primary">Talent Placed</a>
                    </h5>
                    <span class="text-gray-600" id="totalPlace"></span>
                    {{-- {{ @$allBenchApplication }} --}}
                </div>
            </div>
            <div class="d-flex align-items-center py-1">
                <div class="d-flex align-items-center position-relative my-1 me-3">

                    <input type="hidden" id="inputSearch" name="inputSearch"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (in_array('view_bench', $userPermissions))
        <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
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
                                <option value="10">Newest to Oldest (Offer Date)</option>
                                <option value="11">Oldest to Newest (Offer Date)</option>
                                <option value="12">Newest to Oldest (Join Date)</option>
                                <option value="13">Oldest to Newest (Join Date)</option>
                                <option value="14">Low to High (Offer Amount)</option>
                                <option value="15">High to Low (Offer Amount)</option>
                            </select>
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                id="country" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                                style="border: 1px solid #ffffff;" onchange="filtersdata()">
                                <option value="110" selected="selected">Location</option>
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

                    <div class="card">
                        <div class="card-body pt-3 px-5">
                            <div class="loadingMessage loadingMessagebenchCandidatePlaceAjax" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted" role="row">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" value="1"></div>
                                        </th>
                                        <th class="">Talent Name</th>
                                        <th class="">Jobs</th>
                                        <th class="">Job Status</th>
                                        <th class="">Source</th>
                                        <th class="">Offer Amount</th>
                                        <th class="">Joining Date</th>
                                        <th class="">Stage</th>
                                        <th class="">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="benchCandidatePlaceAjax"> </tbody>
                            </table>
                            <div id="paginationbenchCandidatePlace"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Content-->

    <div id="kt_activitiesHM_toggle"></div>
    <div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">HM | CP</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
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
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
        </div>
    </div>

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
@endsection



@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>
    <script>
        function showAlert(bench_app_id, placeid) {
            Swal.fire({
                title: "Placed End Date",
                html: '<input type="date" id="swal-input-date" value="" class="swal2-input" placeholder="Placed End Date...">',
                icon: "info",
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: () => {
                    const dateValue = document.getElementById('swal-input-date').value;
                    if (!dateValue) {
                        Swal.showValidationMessage('Please select a date');
                    }
                    return dateValue;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Selected date:', result.value);
                    var bench_placed_end_date = result.value;
                    var url = '{!! route('service_company.bench-candidates.update_placed_end_date') !!}';
                    //url 							= 	url.replace(':id', id);
                    var token = '{{ csrf_token() }}';
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'POST',
                            'bench_app_id': bench_app_id,
                            'bench_placed_end_date': bench_placed_end_date,
                            'placeid': placeid
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                Swal.fire("success", 'End date has been updated successfully', "success");
                            }
                            fetchBenchBenchCandidatesCandidateList();
                        }
                    });
                }
            });
        }


        $(document).ready(function() {


            $('#kt_tagify_2').keyup(function() {
                // tableLoad('load');
                fetchBenchBenchCandidatesCandidateList();
            });
        });

        var table;
        // tableLoad('load');

        function filtersdataget() {
            // tableLoad('load');
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
        //     table = $('#myTable').dataTable({
        //         serverSide: true, // Enable server-side processing
        //         responsive: true,
        //         processing: true,
        //         ordering: false,
        //         destroy: true,
        //         stateSave: true,
        //         "bLengthChange": false,
        //         sDom: 'lrtip',
        //         ajax: '{!! route('service_company.bench-candidates.placedListingData') !!}?search=' + inputSearch + '&sortingfilter=' + sortingfilter +
        //             '&country=' + country + '&cus_search=' + cus_search,

        //         "fnDrawCallback": function(oSettings) {
        //             $("body").tooltip({
        //                 selector: '[data-toggle="tooltip"]'
        //             });
        //         },  		  		
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
        //             // { data: 'Contacts', name: 'Contacts', width: '7%', orderable: true },
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

        //             // { data: 'Qualifaction', name: 'Qualifaction', width: '2%', orderable: true },

        //             {
        //                 data: 'Location',
        //                 name: 'Location',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Assigned',
        //                 name: 'Assigned',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'JoiningDate',
        //                 name: 'JoiningDate',
        //                 width: '7%',
        //                 orderable: true
        //             },

        //             // { data: 'Placed_End_Date', name: 'Placed_End_Date', width: '8%', orderable: true },

        //             {
        //                 data: 'OfferAmount',
        //                 name: 'OfferAmount',
        //                 width: '7%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Requisitions',
        //                 name: 'Requisitions',
        //                 width: '2%',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'Status',
        //                 name: 'Status',
        //                 width: '2%',
        //                 orderable: true
        //             }
        //         ]


        //     });
        //     new $.fn.dataTable.FixedHeader(table);


        // }
    </script>

    <script>
        $(document).on('click', '.hm-listing', function() {

            $('#kt_activitiesHM_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesHM_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesHM_body').html(response.view);
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

        $(document).on('change', '.placed_end_date', function() {

            var bench_app_id = $(this).data('id');
            var placeid = $(this).data('placeid');
            var bench_placed_end_date = $(this).val();
            var url = '{!! route('service_company.bench-candidates.update_placed_end_date') !!}';
            //url 							= 	url.replace(':id', id);
            var token = '{{ csrf_token() }}';
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'bench_app_id': bench_app_id,
                    'bench_placed_end_date': bench_placed_end_date,
                    'placeid': placeid
                },
                success: function(response) {
                    if (response.status == "success") {
                        //$('#right-sidebar-content').html(response.view);
                    }
                }
            });
        });

        $(document).ready(function() {
				fetchBenchBenchCandidatesCandidateList();
				$(document).on('click', '#paginationbenchCandidatePlace .page-link', function(e) {
						e.preventDefault();

						const page = $(this).data('page');
						if (!page || $(this).hasClass('disabled')) return;

						// Disable the clicked pagination link
						$(this).addClass('disabled');

						// Fetch data
						fetchBenchBenchCandidatesCandidateList(page, $('#search').val()).then(() => {
							// Re-enable the links after successful data fetch
							$('#paginationbenchCandidatePlace .page-link').removeClass('disabled');
						}).catch(() => {
							// Re-enable the links even if there's an error
							$('#paginationbenchCandidatePlace .page-link').removeClass('disabled');
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
				$('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'block' });
				$.ajax({
					url: `{!! route('service_company.bench-candidates.placedListingData_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
						country: $('#country').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'none' });
                        $('#totalPlace').html(response.total);
						$('#benchCandidatePlaceAjax').html(response.html);
						$('#paginationbenchCandidatePlace').html(response.pagination);
					}
				});
		}


    </script>
@endpush
