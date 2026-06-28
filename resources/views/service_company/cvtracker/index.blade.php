@extends('layouts.service_company_layout')
@push('head-script')
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
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
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
            #kt_activitiesClient {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClient {
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
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);"
                            class="text-primary-800">All Candidates</a> </h5>
                    <span class="text-gray-600" id="total_record"></span>
                    {{-- {{ @$applicationCounter }} --}}
                </div>
            </div>
            <div class="d-flex align-items-center py-1">
                <!--  <a href="javascript:void(0);" class="me-3 daterange"><span class="svg-icon svg-icon-primary svg-icon-1x">-->
                <input type="hidden" class="form-control form-control-solid" placeholder="Pick date rage"
                    id="kt_daterangepicker" />
                <!--</span> </a>-->

                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-3 d-none" data-toggle="modal"
                    data-target="#mass_mail"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
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
                    </span> Mass Email </a>


                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fwd_resume fw-bolder me-3 d-none" data-toggle="modal"
                    data-target="#fwd_resume"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
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
                    </span> Fwd Resume </a>
                {{-- <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 send_email d-none" data-toggle="modal" data-target="#send_email"> <span class="svg-icon svg-icon-primary svg-icon-1x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <rect x="0" y="0" width="24" height="24"/>
          <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
          <path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
        </g>
      </svg>
      </span> Send Email </a> --}}
                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 application_copy d-none" data-toggle="modal"
                    data-target="#application_copy">
                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
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
                    Copy </a>

                <div class="before-event downloadexcell d-flex align-items-center position-relative my-1 me-3 d-none">
                   
                    <button type="button"
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 exportRecord"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" style="width: 100px;">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                    fill="black" />
                            </svg>
                        </span>
                        Export </button>
                </div>

                <div class="before-event d-flex align-items-center position-relative my-1 me-3 custom_hide">

                    <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>




                <div class="card-toolbar">
                    <div class="before-event d-flex justify-content-end" data-kt-user-table-toolbar="base">

                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-user-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Role:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="role" data-hide-search="true">
                                        <option></option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Analyst">Analyst</option>
                                        <option value="Developer">Developer</option>
                                        <option value="Support">Support</option>
                                        <option value="Trial">Trial</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="two-step" data-hide-search="true">
                                        <option></option>
                                        <option value="Enabled">Enabled</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-bold px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="apptrackerfilter_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                    rx="1" transform="rotate(-45 6 17.3137)"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)"></rect>
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
                                           if (!tagData.hasOwnProperty('checked')) {
                                                tagData.checked = true; // ⭐ auto select
                                            }
                                            return tagData;
                                        }
                                    });

                                    tagify.on('add', function(e) {

                                        const tagElm = e.detail.tag;

                                        setTimeout(() => {

                                            // get correct data reference from Tagify
                                            const tagData = tagify.getSetTagData(tagElm);

                                            if (tagData) {
                                                tagData.checked = false; // ✅ update real data
                                            }

                                            // update UI
                                            const star = tagElm.querySelector('.star');
                                            if (star) {
                                                star.classList.remove('checked');
                                            }

                                            // sync everything
                                            tagify.updateValueByDOMTags();

                                        }, 200);

                                    });

                                    // ⭐ CLICK STAR TO TOGGLE
                                    document.addEventListener('click', function(e) {

                                        if (e.target.classList.contains('star')) {

                                            const tagElm = e.target.closest('.tagify__tag');

                                            // toggle UI
                                            e.target.classList.toggle('checked');

                                            // get correct tag data
                                            const tagData = tagify.getSetTagData(tagElm);

                                            if (tagData) {
                                                tagData.checked = e.target.classList.contains('checked');
                                                tagify.updateValueByDOMTags();
                                            }
                                        }
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
                                        value="" id="kt_tagify_location"
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


                                <input class="form-control form-control-solid" value=""
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
                                        <option value="0">Freshers
                                        </option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}
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
                                        <option value="0">Freshers
                                        </option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}
                                                &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                </div>
                            </div>

                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Currency</label>
                                                    <select class="form-select form-select-solid"
                                                        arial-label="Select Currency" name="min_salary_currency"
                                                        id="min_salary_currency">
                                                        <option value="">Select currency</option>
                                                        <option value="0">INR</option>
                                                        <option value="1">EUR</option>
                                                        <option value="2">JPY</option>
                                                        <option value="3">GBP</option>
                                                        <option value="4">USD</option>
                                                        <option value="5">AED</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Mode</label>
                                                    <select class="form-select form-select-solid"
                                                        arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                        <option value="">Select Mode</option>
                                                        <option value="0">Annual</option>
                                                        <option value="1">Monthly</option>
                                                        <option value="2">Hourly</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Minimum Salary:</label>
                                                    <input type="number"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="min_sal" id="min_sal" placeholder="Enter min salary"
                                                        value="">
                                                    <div class="fv-plugins-message-container"></div>
                                                    <span class="form-text text-muted minimum_salary_word"></span>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Maximum Salary:</label>
                                                    <input type="number"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="max_sal" id="max_sal" placeholder="Enter max salary"
                                                        value="">
                                                    <div class="fv-plugins-message-container"></div>
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
                                            name="app_int_status" value="6" id="app_int_status6">
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                            for="app_int_status6">
                                            <span class="d-block fw-bold text-start">
                                                <span class="text-dark-800 fw-bolder d-block fs-5">Joined</span>
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


    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                    <div class="col-lg-4 d-flex align-items-center px-2" style="width:65%;position: relative;">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                            value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                            <div style="position: absolute; top: 0px; right: 36px;">
                               <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                            </div>

                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="sortingfilter" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdataget()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Upload Date)</option>
                            <option value="2">Oldest to Newest (Upload Date)</option>
                            <!--<option value="3">Low to High (Salary)</option>-->
                            <!--<option value="4">High to Low (Salary)</option>-->
                            <!--<option value="9">Notice Period (Immediate)</option>-->
                            <option value="21">Notice Period (Immediate)</option>
                            <option value="22">Notice Period (Serving)</option>
                            <option value="7">High to Low (Experience) </option>
                            <option value="8">Low to High (Experience)</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
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

                    <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="country" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                            style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                            <option value="" selected="selected">Location</option>
                            @if (@$countries)
                                @foreach ($countries as $val_co)
                                    <option value="{{ $val_co->id }}">{{ $val_co->country_name }}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                        <button type="button"
                            class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
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
                    <div class="card-body pt-3 px-5" style="position: relative">
                        @if (in_array('view_job_applications', $userPermissions))
                           <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                                id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input datatable_chk chk_list" type="checkbox"
                                                    data-kt-check="true" data-kt-check-target="#myTable .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Name</th>
                                        {{-- <th class="">Contacts</th> --}}
                                        <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="" data-toggle="tooltip" title="Notice Period">Notice</th>
                                        <th class="">Qualification</th>
                                        <th class="">Location</th>
                                        <th class="">Source</th>
                                        {{-- <th class="" data-toggle="tooltip" title="Upload Date">Date</th> --}}
                                        <th class="">Jobs</th>
                                        <th class="">Status</th>
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
                                    transform="rotate(-45 6 17.3137)"/>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="client_stage_sub_stage_new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Stage and sub stage</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close   client_stage_sub_stage_new_close "
                        data-dismiss="modal" id="client_stage_sub_stage_new_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span> </div>
                </div>
                <div class="client_stage_body_content" id="client_stage_body_content"></div>
            </div>
        </div>
    </div>


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


    <!--begin::Fwd Resume - Modal-->
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
    <!--end::Fwd Resume - Modal-->

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


@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

   

    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
        }

        $("#kt_daterangepicker").daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);

        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
                $('.custom_hide').addClass("d-none");
            } else {
                $('.chk_list_item').prop('checked', false);	
                $('.custom_hide').removeClass("d-none");
            }
        });
    </script>

    <script>
        $(document).ready(function() {




          $(document).on('click', '.exportRecord', function() {

                var action_type = $(this).val();
                var id = 0;
                var url = "{{ route('service_company.candidateexport.candidate-job-applications', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    var wiseid = $(this).attr('data-wiseid');
                    arr.push(wiseid);
                    jobIdArr.push(jobId);
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
                        //  return false;

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

            $('#inputSearch').keyup(function() {
                // tableLoad('filter');
            });


            $(document).on('click', '.fwd_resume', function(e) {

                e.preventDefault();
                // localStorage.setItem('toggleOpen', 'true');
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id = jobIdArr[0];
                var url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}";
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
                            $('#application_fwd_resume_body_content').html(response.view);
                        }
                    }
                });

            });


            $(document).on('click', '.mass_mail', function(e) {

                e.preventDefault();
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
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

        var table;
        
        document.getElementById('kt_tagify_2').addEventListener('keyup', function() {
               fetchCandidates();
        });

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

            var status = 'all';
            var jobs = 'all';
            var clients = 'all';
            var source = 'all';
            var search_passport = 'all';
            var search_np_id = 'all';
            var inputSearch = $('#inputSearch').val();
            var kt_daterangepicker = $('#kt_daterangepicker').text();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            // alert(kt_daterangepicker);
            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',
                //  ajax			: '{!! route('service_company.cvtracker.alldata') !!}?search='+inputSearch,
                ajax: {
                    'url': '{!! route('service_company.cvtracker.alldata') !!}',
                    "data": function(d) {
                        d.search = $('#inputSearch').val(),
                            d.sortingfilter = $('#sortingfilter').val(),
                            d.country = $('#country').val(),
                            d.cus_search = $('#kt_tagify_2').val()
                    }
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        width: '1%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        width: '15%',
                        orderable: true
                    },
                    {
                        data: 'Exp',
                        name: 'Exp',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'NoticePeriod',
                        name: 'NoticePeriod',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Qualifaction',
                        name: 'Qualifaction',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        width: '2%',
                        orderable: true
                    },
                    // { data: 'UploadDate', name: 'UploadDate', width: '10%', orderable: true },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        width: '2%',
                        orderable: true
                    }
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $(document).on('click', '.application_copy', function() {
            $('#application_copy_body_content').html('');
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
    </script>

    <script>
        $(document).on('click', '.client_stage_sub_stage', async function() {

            e.preventDefault();
            const connectedsubclient = await isInternetAvailable();
                if (!connectedsubclient) {
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

            var app_id = $(this).data('row-id');
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
                    var inner_url =
                        "{{ route('service_company.auth-custom-package.validateClientPackage') }}";
                    var inner_token = "{{ csrf_token() }}";
                    var inner_baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    var eventId = 4;
                    $.ajax({
                        type: 'POST',
                        url: inner_url,
                        beforeSend: function() {},
                        data: {
                            '_token': inner_token,
                            'event': 'CVACCESS',
                            'app_id': app_id,
                            'eventId': eventId
                        },
                        success: function(response) {

                            if (response.status == "success") {

                                $('#client_stage_sub_stage_new').modal('show');
                                var url =
                                    "{{ route('service_company.job-applications.client_stage_body_content', ':id') }}";
                                url = url.replace(':id', app_id);
                                $.ajax({
                                    type: 'GET',
                                    url: url,
                                    success: function(response) {
                                        if (response.status == "success") {
                                            $('#client_stage_body_content').html(
                                                response.view);
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
                        'Selected request is safe :)',
                        'error'
                    )
                }

            })
        });

        $(document).on('click', '.client_stage_sub_stage_new_close', function() {
            $('#client_stage_sub_stage_new').modal('hide');
        });

        $(document).on('click', '#int_stage_submit', function(e) {

            e.preventDefault();
            var master_status = $('input[name="master_status"]:checked').val();
            if ((master_status == 1) || (master_status == 9)) {
                $.ajax({
                    url: '{{ route('service_company.job-applications.client_stage_submit') }}',
                    container: '#status_frm',
                    type: "POST",
                    data: $('#status_frm').serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success");
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // $.unblockUI();
                            // table._fnDraw();

                        } else if (response.status == "info") {
                            swalWithBootstrapButtons.fire('Fail!', response.message, 'error')
                        }

                    }
                })
            } else {
                Swal.fire("Warning", 'Only Sourcing stage update!', "info");
            }

            e.run();

        });

        
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
            $('#client_stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
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
                        $("#custom_stage_submit").html(`<span class="indicator-label">Submit</span>`);
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success")
                                .then(function() {
                                    // $('#stage_sub_stage').modal('hide'); 
                                    $("#stage_sub_stage_close").click();
                                });
                            // $('#myTable').DataTable().draw();
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // table._fnDraw();
                            load_interviewSchedule();
                        } else if (response.status == 'cvpending') {
                            Swal.fire("Warning", 'Resume pending this job application.', "warning")
                                .then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                        } else if (response.status == 'candidatevalidation') {
                            Swal.fire("Warning", response.message, "warning").then(function() {
                                $("#stage_sub_stage_close").click();
                            });
                        }
                    }
                })
            }

        });
    </script>

    <script>
        $(document).ready(function() {

          $(document).on('click', '.chk_list', function() {
           
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').prop('checked', true);
                } else {
                    $('.chk_list_item').prop('checked', false);	
                }
            });

            $(document).on('click', '.chk_list', function(e) {
                $('#application_fwd_resume_body_content').html('');

                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.fwd_resume').removeClass("d-none");
                    $('.custom_hide').addClass("d-none");
                    $('.mass_mail').removeClass("d-none");
                    $('.send_email').removeClass("d-none");
                    $('.downloadexcell').removeClass("d-none");
                    $('.app_copy').removeClass("d-none");
                    $('.application_copy').removeClass("d-none");

                } else {
                    $('.fwd_resume').addClass("d-none");
                    $('.send_email').addClass("d-none");
                    $('.mass_mail').addClass("d-none");
                    $('.app_copy').addClass("d-none");
                    $('.downloadexcell').addClass("d-none");
                    $('.application_copy').addClass("d-none");
                    $('.custom_hide').removeClass("d-none");
                }
            });

            $(document).on('click', '.chk_list_item', function() {
                  
                $('#application_fwd_resume_body_content').html('');
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.fwd_resume').removeClass("d-none");
                        $('.mass_mail').removeClass("d-none");
                        $('.send_email').removeClass("d-none");
                        $('.custom_hide').addClass("d-none");
                        $('.downloadexcell').removeClass("d-none");
                        $('.app_copy').removeClass("d-none");
                        $('.application_copy').removeClass("d-none");
                    } else {
                        $('.fwd_resume').addClass("d-none");
                        $('.mass_mail').addClass("d-none");
                        $('.downloadexcell').addClass("d-none");
                        $('.send_email').addClass("d-none");
                        $('.app_copy').addClass("d-none");
                        $('.application_copy').addClass("d-none");
                        $('.custom_hide').removeClass("d-none");
                    }
                }, 500);

            });

            //  $.noConflict();
           
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
            var clientid = '1';
           
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            
            // document.getElementById("loader_search").classList.remove("hidden");
            $.ajax({
                url: `{!! route('service_company.cvtracker.allcandidates') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: search,
                    search : $('#inputSearch').val(),
                    sortingfilter : $('#sortingfilter').val(),
                    country : $('#country').val(),
                    cus_search : $('#kt_tagify_2').val(),
                    cus_search_skill : $('#kt_tagify_users').val(),
                    min_exp : $('#min_exp').val(),
                    max_exp : $('#max_exp').val(),
                    kt_tagify_location : $('#kt_tagify_location').val(),
                    min_sal : $('#min_sal').val(),
                    max_sal : $('#max_sal').val(),
                    min_salary_currency : $('#min_salary_currency').val(),
                    sal_mode : $('#sal_mode').val(),
                    kt_tagify_education : $('#kt_tagify_education').val(),
                    app_int_status : $('[name="app_int_status"]:checked').val(),
                    app_int_activity : $('[name="app_int_activity"]:checked').val(),
                    app_int_notice : $('[name="app_int_notice"]:checked').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#total_record').html(response.total);
                    $('#candidatetList').html(response.html);
                    localStorage.setItem('currentPage', 1);
                    $('#paginationcandidate').html(response.pagination);
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
