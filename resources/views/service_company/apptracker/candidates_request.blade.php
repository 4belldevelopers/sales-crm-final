@extends('layouts.service_company_layout')
@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
    <style>
        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        /* Search + Sort toolbar — same visual language as
           similar_candidates.blade.php's simf-toolbar-card (no Filter
           button/drawer here, this page has no skill/location/salary
           search). brf- prefix (Bench Request Filter) to keep this page's
           styles/JS distinct from the ibf-/abf-/simf-/plf- prefixed ones on
           other AppTracker tabs. */
        .brf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .brf-search-wrap,
        .brf-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .brf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .brf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .brf-search-icon,
        .brf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brf-search-icon svg,
        .brf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .brf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        .brf-sort-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3f4254;
            text-align: left;
        }
        .brf-sort-btn:focus { outline: none; }
        .brf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .brf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .brf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .brf-sort-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 100%;
            width: max-content;
            max-width: 280px;
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(16,24,40,.12), 0 4px 6px -2px rgba(16,24,40,.06);
            padding: 6px;
            z-index: 60;
            max-height: 320px;
            overflow-y: auto;
        }
        .brf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .brf-sort-item:hover {
            background: #f8f9fb;
        }
        .brf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        @media (max-width: 992px) {
            .brf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .brf-search-wrap { flex-basis: 100%; }
            .brf-sort-wrap { width: 140px; }
        }
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
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
                    'similar_active'=>'',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'agencyBench_active'=>'',
                    'agencyBench_url'=>$public_bench_path,
                    'agencyBench_id'=>'agencyBench_counter',
                    'benchTalentReq_active'=>'tabactive',
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
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1 me-3 d-none">
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

                            <!--end::Svg Icon-->
                            <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                                class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                                placeholder="Search..." />
                        </div>
                        <!--end::Search-->

                        <!--begin::Export-->
                        <button type="button"
                            class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none"
                            data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
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
                            <!--end::Svg Icon-->
                            Export</button>
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

                <div class="card mb-4 brf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="brf-search-wrap" style="position: relative;">
                                <span class="brf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control brf-search-input" name="inputJobSearch"
                                    value="" placeholder="Search by candidate name, skills, education or location..." id="kt_tagify_2" onchange="filtersdataget()" autocomplete="off" />
                            </div>
                            <div class="brf-sort-wrap" id="brf_sort_wrap">
                                <span class="brf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="brf-sort-btn" id="brf_sort_btn">
                                    <span id="brf_sort_label">Best Match</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="Show All">Sort</option>
                                    <option value="10" selected="">Best Match</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice (Serving)</option>
                                    <option value="5">Low to High (Notice Period)</option>
                                    <option value="6">High to Low (Notice Period)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="brf-sort-panel d-none" id="brf_sort_panel"></div>
                            </div>
                            <div style="width: 160px; flex-shrink: 0;">
                                <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                    id="country" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                                    style="border: 1px solid #ffffff;" onchange="filtersdata()">
                                    <option value="110" selected="selected">Location</option>
                                    <option value="111">India</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">
                        <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                            <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                        </div>
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                            id="candidates_request">
                            <thead>
                                <tr class="fw-bolder text-muted" role="row">
                                    <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1"
                                        style="width: 30px;">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#candidates_request .form-check-input"
                                                value="1">
                                        </div>
                                    </th>
                                    <th class="">Candidate Name</th>
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Notice</th>
                                    <th class="">Location</th>
                                    {{-- <th class="">Type</th> --}}
                                    <th class="">Last Active</th>
                                    <th class="">Action</th>
                                </tr>
                            </thead>
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
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        // Was Tagify-wrapped, but candidates_request_data_ajax() never reads
        // cus_search as an actual filter (this page has no skill/location
        // search — see the plan for why), so the tag-chip UI was cosmetic
        // and conflicted with the plain-input toolbar styling below. Left
        // as a plain text field, same as similar_candidates.blade.php's own
        // #kt_tagify_2 search box.
    </script>

    <script>
        // The candidate stays visible in THIS list after a successful add —
        // swapping the button to the same "Added" (btn-success) label the
        // row partial already renders server-side for ac_status==1 (see
        // candidates_request_ajax.blade.php) — rather than the previous
        // location.reload(), which made the row (and every other row's
        // scroll position/filter state) vanish instantly. The row only
        // actually disappears once the list is genuinely re-fetched
        // (pagination, filter, sort, or a real page reload).
        function benchReqMarkCandidateRowAdded($btn) {
            $btn.replaceWith('<a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a>');
        }

        $(document).ready(function() {
            $(document).on("click", ".add_to_list", function(e) {
                e.preventDefault();

                var $btn = $(this);

                // 🔒 Prevent double click
                if ($btn.hasClass('processing')) {
                    return false;
                }

                var jobId = $btn.attr('data-row-id');
                var recomndedId = $btn.attr('data-recomnded-id');
                var condidateId = $btn.attr('data-condidate-id');

                // No confirmation dialog — a single click starts the add
                // immediately; the spinner below is the only "processing"
                // feedback needed, and the result comes back as a toast.
                var originalHtml = $btn.html();
                $btn.addClass('processing')
                    .prop('disabled', true)
                    .css('pointer-events', 'none')
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');

                function restoreButton() {
                    $btn.removeClass('processing')
                        .prop('disabled', false)
                        .css('pointer-events', '')
                        .html(originalHtml);
                }

                var url = "{{ route('service_company.jobs.add_to_list', ':id') }}";
                url = url.replace(':id', jobId) + "?recomndedId=" + recomndedId +
                    "&condidateId=" + condidateId;

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        benchReqMarkCandidateRowAdded($btn);
                        benchReqToast.fire({ icon: 'success', title: 'Added successfully to public bench' });
                    },
                    error: function() {
                        restoreButton();
                        benchReqToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
                    }
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
            // tableLoad('load');
            fetchCandidates();
        }

        function tableLoad(type) {
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            table = $('#candidates_request').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',
                ajax: '{!! route('service_company.apptracker.candidates_request_data') !!}?jobid=' + jobid + '&mstatus_id=' + mstatus_id + '&search=' +
                    inputSearch + '&sortingfilter=' + sortingfilter + '&country=' + country + '&cus_search=' +
                    cus_search,
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });


                    var url =
                        '{{ route('service_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
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





                },

                "drawCallback": function(settings) {
                    alert("call" + settings.json);
                },


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
                        width: '15%'
                    },
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
                        width: '10%'
                    },
                    {
                        data: 'NoticePeriod',
                        name: 'NoticePeriod',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Qualifaction',
                        name: 'Qualifaction',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false
                    },
                    // {
                    //     data: 'Contacts',
                    //     name: 'Contacts',
                    //     searchable: false,
                    //     orderable: false,
                    //     width: '15%'
                    // },
                    {
                        data: 'Source',
                        name: 'Source',
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
        }

        $(document).on("click", ".stage_sub_stage", function() {

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

        $(document).on("click", ".master_status_div", function(e) {

            e.preventDefault();



            var id = $(this).data('master-status-id');
            $("#master_status_" + id).attr('checked', true);
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
                $('#main_substage').hide();
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
                            $('#substage').html('');
                            $('#substage').html(response.html);
                        }
                    }
                });

            }

            e.run();

        });

        // Non-blocking toast for simple pass/fail notifications, same pattern
        // as similar_candidates.blade.php's simfToast. The stage-update
        // success below stays a blocking Swal.fire() since it gates a
        // chained .then() (closing the stage modal).
        const benchReqToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2600,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Custom Sort dropdown, same pattern as similar_candidates.blade.php's
        // simfRenderSortPanel — reads options straight from the real
        // #sortingfilter <select>, keeps that select's value in sync so
        // filtersdataget()/fetchCandidates() are unaffected, only replaces
        // how the option list is PRESENTED.
        function brfRenderSortPanel() {
            var $panel = $('#brf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="brf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#brf_sort_label').text(label);
                        brfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function brfOpenSortPanel() {
            brfRenderSortPanel();
            $('#brf_sort_panel').removeClass('d-none');
            $('#brf_sort_btn').addClass('open');
        }
        function brfCloseSortPanel() {
            $('#brf_sort_panel').addClass('d-none');
            $('#brf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#brf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#brf_sort_panel').hasClass('d-none')) brfOpenSortPanel();
            else brfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#brf_sort_wrap').length) brfCloseSortPanel();
        });

        $(document).ready(function() {
            $(document).on("click", "#custom_stage_submit", function(e) {
                e.preventDefault();

                var master_status = $('input[name="master_status"]:checked').val();
                if (master_status == 8) {
                    benchReqToast.fire({ icon: 'info', title: 'Stage can not be updated, please select other option!' });
                } else {
                    $.ajax({
                        url: '{{ route('service_company.job-applications.stage_submit') }}',
                        container: '#status_frm',
                        type: "POST",
                        data: $('#status_frm').serialize(),
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire("Success", 'Application stage update successfully !',
                                    "success").then(function() {
                                    // $('#stage_sub_stage').modal('hide');
                                    $("#stage_sub_stage_close").click();
                                });
                                // var table;
                                // tableLoad('load');
                                var page = $('#paginationcandidate .active_disabled').data('page');
                                fetchCandidates(page);
                                // table._fnDraw();
                                load_interviewSchedule();
                            }
                        }
                    })
                }


            });
            e.run();
        })

        $(document).ready(function() {
            $(document).on("click", "#addServiceCandidate", function() {

                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'event': 'CVACCESS'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            var url =
                                "{{ url('service_company/job-applications/create/' . @$job->id . '/0') }}";
                            window.location = url;
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });

            });

        });



        $(document).on("click", ".chk_list", function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').attr('checked', 'checked');
            } else {
                $('.chk_list_item').removeAttr('checked');
            }
        });
        $(document).on("click", ".chk_list", function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });
        $(document).on("click", ".chk_list_item", function() {
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });


        $(document).on("click", ".fwd_resume", function() {

            var id = {{ $job->id }};
            var url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
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
    </script>

    <script>
        $(document).on("click", ".application_copy", function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                }
            });
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
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();

            $('.loadingMessage').css({ display: 'flex' });
            $.ajax({
                url: `{!! route('service_company.apptracker.candidates_request_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                jobid:jobid, 
                mstatus_id:mstatus_id,
                search:inputSearch,
                sortingfilter:sortingfilter, 
                country:country,
                cus_search:(cus_search)
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('#candidatetList').html(response.html);
                    $('.benchTalentReq_counter').html(response.total);
                    $('#paginationcandidate').html(response.pagination);
                }
            });
        }

    </script>
@endpush
