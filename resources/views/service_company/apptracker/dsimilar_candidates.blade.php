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
            $req_path = url(
                '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt(@$job->id) . '/all',
            );
            //$public_bench_path	=	url('/service_company/apptracker/public_bench/'. GeneralMethod::value_encrypt(@$job->id));
            $internal_bench_path = url(
                '/service_company/apptracker/dinternal_bench/' . GeneralMethod::value_encrypt(@$job->id),
            );

            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );
            $existing_path = url(
                '/service_company/apptracker/dsimilar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
            );
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
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter
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
                    <div class="d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                        <!--end::Svg Icon-->
                        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

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
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">

                <div id="" class="container-fluid d-flex flex-stack second_header">
                    @include('sections.service_company.clientappHeader',['tabactive'=>$tabactive])
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
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <script>
        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
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
            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.dsimilar_existing_data') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
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
                        width: '17%'
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
                $('.existing_counter').text(existingDataCount);


            }, 3500);
        }

        $(document).on('click', '.add_to_inreview', function() {

            var id = $(this).data('row-id');
            var source_from_type = $(this).data('row-source_from_type');
            var source_jobid = $(this).data('row-jobid');
            var source_tab = $(this).data('row-from');
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

                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            'id': id,
                            'source_from_type': source_from_type,
                            'source_jobid': source_jobid,
                            'source_tab': source_tab
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                if (response.status == 'coinCheck') {
                                    Swal.fire("Success", ` ${response.message}`, "warning");
                                    return false;
                                }

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



                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Selected request is safe',
                        'error'
                    )
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
            
            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.apptracker.dsimilar_existing_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  jobid:jobid,
                  search:inputSearch,
                  sortingfilter:sortingfilter ,
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
                  sal_mode: sal_mode
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
