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
            margin-left: 5px;
            cursor: pointer;
            font-size: 21px;
            position: relative;
            top: -5px;
        }

        .tagify__tag .star.checked {
            color: #ef1632;
        }

    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
         @php
          use App\Helper\GeneralMethod;
            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );

            // This route is shared by both flows (an agency's own jobs AND jobs a
            // direct client shared with this company), but the two flows have
            // completely different sibling tabs — a direct-client job only has
            // Total/Similar/In-house Bench/Website Candidates (the "d"-prefixed
            // routes + clientappHeader), it has no Agency Bench or Bench Talent
            // Request tabs at all. Reaching this page from a client job used to
            // always render the full 6-tab apptrackerHeader with own-job URLs,
            // which 404s the moment "Agency Bench Talents"/"Bench Talent Requests"
            // is clicked for a job this company doesn't own. Same ownership check
            // toolbar.blade.php already uses for the Internal/Client tag.
            $isClientJob = @$job->company_id != auth()->user()->company_id;

            if ($isClientJob) {
                $req_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt(@$job->id) . '/all');
                $internal_bench_path = url(
                    '/service_company/apptracker/dinternal_bench/' . GeneralMethod::value_encrypt(@$job->id),
                );
                $existing_path = url(
                    '/service_company/apptracker/dsimilar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
                );

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
                    'career_active'=>'tabactive',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    ];
            } else {
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
                    'benchTalentReq_active'=>'',
                    'benchTalentReq_url'=>$candidates_request_path,
                    'benchTalentReq_id'=>'benchTalentReq_counter',
                    'career_active'=>'tabactive',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    'public_bench_counter' =>@$public_bench_counter,
                    'recomnded_counter' => @$recomnded_counter,
                    ];
            }

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
                    @if ($isClientJob)
                        @include('sections.service_company.clientappHeader',['tabactive'=>$tabactive])
                    @else
                        @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])
                    @endif
                </div>


                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 custm-bg mb-0">
                            <div class="flex-grow-1" style="position: relative; min-width: 0;">
                                <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                                    value="{{ @$default_skill }}" placeholder="Search by candidate name, skills, education or location..." id="kt_tagify_2" />
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
                                                            tagData.checked = e.target.classList.contains('checked');
                                                            tagify.updateValueByDOMTags();
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
                                                <label class="form-label">Current location of candidate</label>
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

                                        <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" id="kt_tagify_company"
                                                class="form-control form-control-solid"
                                                placeholder="Enter your Company name..." name="company_name"
                                                value="">
                                            <script>
                                                // The DOM elements you wish to replace with Tagify
                                                var inputdcompany = document.querySelector("#kt_tagify_company");
                                                new Tagify(inputdcompany, {
                                                    whitelist: [""],
                                                    maxTags: 10,
                                                    dropdown: {
                                                        maxItems: 20, // <- mixumum allowed rendered suggestions
                                                        classname: "", // <- custom classname for this dropdown, so it could be targeted
                                                        enabled: 0, // <- show suggestions on focus
                                                        closeOnSelect: false // <- do not hide the suggestions dropdown once an item has been selected
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
                                                                    value="0">
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
                                            <label class="form-label required">Hiring type</label>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="0" id="app_int_outsource1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="1" id="app_int_outsource2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="2" id="app_int_outsource3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Contract</span>
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
                                        <button type="button" id="changetextupdate"
                                            class="btn btn-primary changetextupdate" onclick="filtersdataget()">
                                            <span class="indicator-label">Submit</span></button>
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
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Availability</th>
                                    <th class="">Location</th>
                                    {{-- <th class="">Type</th> --}}
                                    <!--<th class="">Offer by</th>-->
                                    <th class="">Last Active</th>
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
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
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
        new Tagify(input2);
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
            var jobid = '{{ $job->id }}';
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
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();

            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.career_apply_data_ajax') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&kt_tagify_company=' + kt_tagify_company +
                    '&app_int_notice=' + app_int_notice + '&sal_mode=' + sal_mode + '&app_int_outsource=' +
                    app_int_outsource,
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
                        width: '17%'
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
                        width: '5%'
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false,
                        width: '5%'
                    },
                    // {data: 'Type', name: 'Type', orderable : false},   
                    // 		{data: 'OfferBy', name: 'OfferBy', orderable: false},
                    {
                        data: 'LastActive',
                        name: 'LastActive',
                        orderable: false,
                        width: '10%'
                    },

                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false,
                        width: '15%'
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
                $('.internal_bench_data').text(existingDataCount);


            }, 2500);
        }
    </script>
    <script>
        // Small top-right toast, auto-dismissing, pause-on-hover — same
        // SweetAlert2 toast mixin used on Similar Candidates/In-house Bench/
        // Agency Bench. This page had no toast mixin at all before; every
        // add_to_inreview notice used a blocking Swal.fire() modal instead.
        const careerToast = Swal.mixin({
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

        // The candidate stays visible in THIS list after a successful add —
        // swapping the button to the same "Added" label the row partial
        // already renders server-side for candidates already in the job's
        // pipeline (see career_apply_ajax.blade.php's $existingCandidatesQuery
        // branch) — rather than re-fetching the list (which used to make it
        // vanish immediately via fetchCandidates(page)). The row only
        // actually disappears once the list is genuinely re-fetched
        // (pagination, filter, sort, or a real page reload). This page's
        // button isn't wrapped in its own div (unlike the other bench
        // pages), so the anchor itself is replaced in place.
        function careerMarkCandidateRowAdded($btn) {
            $btn.replaceWith('<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a>');
        }

        $(document).on('click', '.add_to_inreview', function(e) {
            e.preventDefault();

            var $btn = $(this);

            // 🔒 Prevent double click
            if ($btn.hasClass('processing')) {
                return false;
            }

            var id = $btn.data('row-id');
            var source_from_type = $btn.data('row-source_from_type');
            var source_jobid = $btn.data('row-jobid');

            var url = '{{ route('service_company.apptracker.rec_candidate_assign') }}';
            var token = "{{ csrf_token() }}";

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

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'id': id,
                    'source_from_type': source_from_type,
                    'source_jobid': source_jobid
                },
                success: function(response) {
                    if (response.status == 'coinCheck') {
                        restoreButton();
                        careerToast.fire({ icon: 'warning', title: response.message });
                        return false;
                    }
                    if (response.status == 'success') {
                        careerMarkCandidateRowAdded($btn);
                        careerToast.fire({ icon: 'success', title: 'Selected request has been In-Review' });
                    } else {
                        restoreButton();
                    }
                },
                error: function() {
                    restoreButton();
                    careerToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
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
            var jobid = '{{ $job->id }}';
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
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();


            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.apptracker.career_apply_data_ajax') !!}?page=${page}`,
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
                kt_tagify_company:kt_tagify_company,
                app_int_notice:app_int_notice, 
                sal_mode:sal_mode,
                app_int_outsource:app_int_outsource
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('#candidatetList').html(response.html);
                    $('#inhouseBench_counter').html(response.total);
                    $('#paginationcandidate').html(response.pagination);
                }
            });
        }

    </script>
@endpush
