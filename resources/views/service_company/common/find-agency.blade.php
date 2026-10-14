@extends('layouts.service_company_layout')
@push('head-script')
    <style>
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
            #kt_activitiesDirectClient {
                width: 1025px;
            }

        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDirectClient {
                width: 1200px;
            }
        }

        .selectednotice {
            background-color: #007bff !important;
            /* Example color */
            color: white !important;
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        /* â”€â”€ Right-side filter drawer â”€â”€ */
        #fd-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1045;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s ease;
        }
        #fd-overlay.fd-open { opacity: 1; pointer-events: auto; }

        #apptrackerfilter {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            width: 80% !important;
            height: 100vh !important;
            z-index: 1050 !important;
            background: #fff !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: -4px 0 24px rgba(0,0,0,.13) !important;
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }
        #apptrackerfilter.fd-open { transform: translateX(0); }
        #apptrackerfilter .modal-header.flex-stack { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f3f8; flex-shrink:0; }
        #apptrackerfilter .apptrackerfilter_body_content { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        #apptrackerfilter .modal-body { flex:1; overflow-y:auto; padding:20px !important; }
        #apptrackerfilter .modal-footer { flex-shrink:0; padding:14px 20px; border-top:1px solid #f1f3f8; }
        @media (max-width: 768px) { #apptrackerfilter { width: 380px !important; } }
        @media (max-width: 480px) { #apptrackerfilter { width: 100% !important; } }

        /* ── Hero section redesign ── */
        .shield-icon {
            width: 22px;
            height: 22px;
            vertical-align: middle;
            margin-left: 6px;
            flex-shrink: 0;
        }
        .hero-section-wrap {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .hero-head-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .hero-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.3;
        }
        .hero-sub {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
        }
        .hero-stats-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .h-stat-card {
            flex: 1 1 160px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            min-width: 140px;
        }
        .h-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .h-stat-icon svg {
            width: 20px !important;
            height: 20px !important;
        }
        .h-icon-blue   { background: #eff6ff; color: #2563eb; }
        .h-icon-green  { background: #f0fdf4; color: #16a34a; }
        .h-icon-yellow { background: #fefce8; color: #ca8a04; }
        .h-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .h-stat-num {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .h-stat-label {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
        }
    </style>
    <link href="{{ asset('demo/css/find-agency.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />


@endpush
@section('content')
   
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
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -70px;">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">
                
                <!--Start Filter Drawer-->
                <div id="fd-overlay" onclick="fdClose()"></div>
                <div id="apptrackerfilter" tabindex="-1">
                            <div class="modal-header flex-stack">
                                <div>
                                    <h1>Find Trusted Hiring Agencies</h1>
                                    <p>Discover verified recruitment partners based on your hiring requirements.</p>
                                </div>
                                <div class="btn btn-sm btn-icon btn-active-light-danger close" onclick="fdClose()" id="apptrackerfilter_close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"></rect>
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"></rect>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="apptrackerfilter_body_content" id="">
                                <div class="modal-body" style="padding: 2em;">

                                    <!-- Trust bar -->
                                    <div class="trust-bar">
                                    <div class="trust-item">
                                        <i class="ti ti-shield-check" aria-hidden="true"></i>
                                        <span>All agencies are verified</span>
                                    </div>
                                    <div class="trust-item">
                                        <i class="ti ti-medal" aria-hidden="true"></i>
                                        <span>Quality &amp; performance checked</span>
                                    </div>
                                    <div class="trust-item">
                                        <i class="ti ti-users" aria-hidden="true"></i>
                                        <span>Secure &amp; confidential</span>
                                    </div>
                                    </div>

                                    <!-- Search with tag input -->
                                    <div class="section">
                                      <p class="section-label">1. Search Skills / Keywords <span class="required">*</span></p>

                                        <div class="suggestions-wrap search-input-wrap">
                                        <input class="search-input" name="kt_tagify_users" value="" placeholder="Search by skills..."
                                                    id="kt_tagify_users" />
                                        </div>

                                        <p class="input-hint">Press <span>Enter</span> or <span>,</span> to add a keyword &nbsp;Â·&nbsp; Press <span>Backspace</span> to remove last</p>

                                        <div class="tags-row" id="popularTagsRow">
                                            <span class="label">Popular searches:</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="Java Developer">Java Developer</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value=".NET Developers">.NET Developers</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="SAP Consultants">SAP Consultants</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="AWS">AWS</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="React Developers">React Developers</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="Node.js">Node.js</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="Python Developer">Python Developer</span>
                                            <span class="pop-tag" onclick="addFromPopular(this)" data-value="DevOps Engineer">DevOps Engineer</span>
                                            <span class="pop-tag more">+ More</span>
                                        </div>
                                    </div>

                                    <!-- Hiring type -->
                                    <div class="section">
                                        <p class="section-label">2. Hiring Type <span class="required">*</span></p>
                                        <div class="hiring-grid">
                                            <input type="hidden" id="hiring_type" name="hiring_type" value="2">
                                            <div class="hiring-card active-blue" id="card-full" onclick="selectHiringType('full')" role="button" tabindex="0" aria-pressed="true">
                                            <div class="hiring-icon blue"><i class="ti ti-briefcase" aria-hidden="true"></i></div>
                                            <div class="hiring-text">
                                                <h3>Full-Time Hiring</h3>
                                                <p>Permanent hiring solutions for long-term requirements.</p>
                                            </div>
                                            <div class="radio-dot checked-blue" id="radio-full"><div class="dot-inner"></div></div>
                                            </div>
                                            <div class="hiring-card" id="card-contract" onclick="selectHiringType('contract')" role="button" tabindex="0" aria-pressed="false">
                                            <div class="hiring-icon teal"><i class="ti ti-clock" aria-hidden="true"></i></div>
                                            <div class="hiring-text">
                                                <h3>Contract Hiring</h3>
                                                <p>On-demand talent, immediate joiners &amp; project-based hiring.</p>
                                            </div>
                                            <div class="radio-dot" id="radio-contract"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional filters -->
                                    <div class="section">
                                    <p class="section-label">3. Additional Filters <span class="optional">(Optional)</span></p>
                                    <div class="filters-grid">
                                        <select aria-label="Industry" class="" id="industry">
                                        <option value="" >Industry</option>
                                        @if ($td_industries)
                                            @foreach ($td_industries as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                        <select aria-label="Experience" id="experience">
                                        <option value="">Experience</option>
                                        <option>0â€“2 years</option>
                                        <option>3â€“5 years</option>
                                        <option>5â€“10 years</option>
                                        <option>10+ years</option>
                                        </select>
                                        <select aria-label="Location" id="country">
                                        <option value="">Location</option>
                                        @if ($cities)
                                            @foreach ($cities as $country)
                                                <option value="{{ $country }}">{{ $country }}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                    </div>

                                    <!-- Bottom bar -->
                                    <div class="bottom-bar">
                                    <div class="match-note">
                                        <i class="ti ti-stars" aria-hidden="true"></i>
                                        <span>We'll show agencies best matched to your requirements.</span>
                                    </div>
                                    <div class="agencies-info">
                                        <div class="avatars">
                                        <img src="https://i.pravatar.cc/28?img=3" alt="" />
                                        <img src="https://i.pravatar.cc/28?img=5" alt="" />
                                        <img src="https://i.pravatar.cc/28?img=8" alt="" />
                                        </div>
                                        <div class="agencies-count">
                                        <h4>{{ $activeCompanies }} verified agencies available</h4>
                                        <p><span class="green-dot"></span> Last updated just now</p>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Actions -->
                                    </div>
                                    <div class="modal-footer flex-center">
                                    <div class="div1">
                                        <button type="button" id="" class="btn btn-secondary me-3" onclick="fdClose()">Close</button>
                                         <button class="btn btn-primary changetextupdate" id="changetextupdate" onclick="filtersdataget()"><i class="ti ti-search" aria-hidden="true"></i>Find Agencies</button>
                                    </div>
                                </div>
                            </div>
                </div>
                <!--End Filter Drawer-->

                <!-- PAGE CONTENT -->
                <main class="page-content">

                    <!-- HERO -->
                    <div class="hero-section-wrap">
                        <div class="hero-head-row">
                            <div>
                                <div class="hero-title">
                                    Find Trusted Hiring Agencies
                                    <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
                                    </svg>
                                </div>
                                <div class="hero-sub">Discover verified recruitment partners specialized in your hiring needs</div>
                            </div>
                        </div>
                        <div class="hero-stats-row">
                            <div class="h-stat-card">
                                <div class="h-stat-icon h-icon-blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="h-stat-num">{{ $allCompanies }}</div>
                                    <div class="h-stat-label">Total Agencies</div>
                                </div>
                            </div>
                            <div class="h-stat-card">
                                <div class="h-stat-icon h-icon-green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="h-stat-num">{{ $activeCompanies }}</div>
                                    <div class="h-stat-label">Verified Agencies</div>
                                </div>
                            </div>
                            <div class="h-stat-card">
                                <div class="h-stat-icon h-icon-yellow">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="h-stat-num">92</div>
                                    <div class="h-stat-label">Highly Rated (4★+)</div>
                                </div>
                            </div>
                            <div class="h-stat-card">
                                <div class="h-stat-icon h-icon-purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="h-stat-num">24</div>
                                    <div class="h-stat-label">AI Matched for You</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FILTER BAR -->
                    <div class="filter-bar">
                        <div class="search-input-wrap">
                            <input class="search-input" name="inputJobSearch" id="kt_tagifyskill" value="" type="text" placeholder="Search by skills..."/>
                        </div>
                        <button class="ai-match-btn" onclick="filtersdataget()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                            Search
                        </button>
                        
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                            <select class="form-select form-select-lg sortingfilterselect" data-hide-search="true"
                                id="sortingfilter" data-dropdown-css-class="w-150px"
                                data-placeholder="Select an option" style="">
                                <option value="2">Hiring Type : Full-Time Hiring</option>
                                <option value="1">Hiring Type : Contract Hiring</option>
                            </select>
                        </div>
                        <div class="more-filters-btn" onclick="fdOpen()" style="cursor:pointer;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
                            </svg>
                            More Filters
                        </div>
                    </div>

                    <!-- ACTIVE FILTER TAGS -->
                    <div class="active-filters" id="selectedFilters" style="display:none;"></div>

                    <!-- TABLE -->
                    <div class="table-wrap">
                        
                    <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                    <table>
                        <thead>
                        <tr>
                            <th>Agency &amp; Specialization</th>
                            <th>Rates</th>
                            <th>Overview</th>
                            <th>Location &amp; Team</th>
                            <th>Last Active</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="findAgencyList">

                        </tbody>
                    </table>

                    <!-- PAGINATION -->
                    <div class="m-5">
                        <div id="paginationfindagency"></div>
                    </div>
                    </div>

                </main>

            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->


    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close" style="z-index: 9999 !important;">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Copy Talents </h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-primary me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
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
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>


    
    <div id="kt_activitiesDirectClient_toggle"></div>
    <div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">{{__('messages.Agency')}} Detail</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesDirectClient_close"> <span class="svg-icon svg-icon-1">
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
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
        </div>
    </div>




@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

    <script>
        const inputElm = document.querySelector('#kt_tagify_users');
        const inputJobSearch = document.querySelector('#kt_tagifyskill');

        const tagify = new Tagify(inputElm, {
            templates: {
                tag: function(tagData) {
                    tagData = tagData || {};
                    const value = tagData.value || '';
                    const isChecked = tagData.checked ? 'checked' : '';
                    return `
                <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                    <div>
                        <span class="star ${isChecked}">ðŸŸŒ</span>
                        <span class="tagify__tag-text">${value}</span>
                        <span style="width: 16px; height: 16px; border-radius: 50%; background: #A5B4FC !important; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #3730A3; line-height: 1; flex-shrink: 0; transition: background 0.12s;"><x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><span>
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

        // Add event listener for star click - instant state update with zero timer cascade
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('star')) {
                const tagElm = e.target.closest('.tagify__tag');
                if (!tagElm) return;
                const valueElm = tagElm.querySelector('.tagify__tag-text');
                const value = valueElm ? valueElm.textContent : '';

                e.target.classList.toggle('checked');

                // Update the tag data directly without artificial timer delays
                if (typeof tagify !== 'undefined' && Array.isArray(tagify.value)) {
                    const tagData = tagify.value.find(tag => tag.value === value);
                    if (tagData) {
                        tagData.checked = e.target.classList.contains('checked');
                    }
                }
            }
        });

        
        const tagify2 = new Tagify(inputJobSearch, {
            maxTags: 300,
            templates: {
                tag: function(tagData) {
                    tagData = tagData || {};
                    const value = tagData.value || '';
                    const isChecked = tagData.checked ? 'checked' : '';
                    return `
                <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                    <div style="padding: 2px 6px;">
                        <span class="tagify__tag-text">${value}</span>
                        <span style="width: 16px; height: 16px; border-radius: 50%; background: #A5B4FC !important; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #3730A3; line-height: 1; flex-shrink: 0; transition: background 0.12s;"><x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><span>
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

        // Prevent infinite loop
        let isSyncing = false;

        // Sync tagify -> tagify2
        function syncToTagify2() {
            if (isSyncing) return;
            isSyncing = true;
            const tags = (tagify.value || []).map(tag => ({
                value: tag.value
            }));
            tagify2.removeAllTags();
            tagify2.addTags(tags);
            isSyncing = false;
        }

        function syncToTagify1() {
            if (isSyncing) return;
            isSyncing = true;
            const tags = (tagify2.value || []).map(tag => ({
                value: tag.value
            }));
            tagify.removeAllTags();
            tagify.addTags(tags);
            isSyncing = false;
        }

        // Function to update inputJobSearch based on tagify's value
        function updateInputJobSearch() {
            const tags = (tagify.value || []).map(tag => tag.value).join(',');
            inputJobSearch.value = tags;
            syncToTagify2();
        }

        function updateTagifyUsers() {
            syncToTagify1();
        }

        // Listen for tag removal
        tagify.on('add', updateInputJobSearch);
        tagify.on('remove', updateInputJobSearch);

        // Initial sync
        updateInputJobSearch();

        // Sync tagify_users with inputJobSearch on change
        inputJobSearch.addEventListener('change', function() {
            const newTags = inputJobSearch.value.split(',').map(tag => ({
                value: tag.trim()
            })).filter(t => t.value);
            tagify.loadOriginalValues(newTags);
            syncToTagify1();
        });

        function debounceskill(func, delay) {
            let timeoutskill;
            return function(...args) {
                clearTimeout(timeoutskill);
                timeoutskill = setTimeout(() => func.apply(this, args), delay);
            };
        }

        var activeSkillXHR = null;
        const fetchSuggestionsSkills = debounceskill(function(query) {
            if (activeSkillXHR && activeSkillXHR.readyState !== 4) {
                activeSkillXHR.abort();
            }
            activeSkillXHR = $.ajax({
                url: "{{ url('/') }}" + "/skill-suggestions?q=" + encodeURIComponent(query),
                method: 'GET',
                success: function(data) {
                    if (Array.isArray(data)) {
                        tagify.settings.whitelist = data;
                        tagify.dropdown.show.call(tagify);
                    }
                },
                error: function(err) {
                    if (err.statusText !== 'abort') {
                        console.error("Error fetching Skill:", err);
                    }
                }
            });
        }, 200); 

        // Listen for user input to trigger API call
        tagify.on('input', function(e) {
            var query = (e.detail.value || '').trim();
            if (query.length < 1) return;
            fetchSuggestionsSkills(query);
        });

        /* â”€â”€ Add from popular row â”€â”€ */
    function addFromPopular(el) {
            let value = el.getAttribute('data-value');
            /*
            |--------------------------------------------------------------------------
            | CHECK EXISTING TAG
            |--------------------------------------------------------------------------
            */
            let existing = tagify.value.find(
                tag => tag.value.toLowerCase() === value.toLowerCase()
            );
            /*
            |--------------------------------------------------------------------------
            | REMOVE TAG
            |--------------------------------------------------------------------------
            */
            if (existing) {

                tagify.removeTags(value);

                el.classList.remove('pop-added');
            }
            /*
            |--------------------------------------------------------------------------
            | ADD TAG
            |--------------------------------------------------------------------------
            */
            else {

                tagify.addTags([value]);

                el.classList.add('pop-added');
            }
            inputElm.focus();
    }


    /*
|--------------------------------------------------------------------------
| SHOW SELECTED FILTERS
|--------------------------------------------------------------------------
*/

function updateSelectedFilters() {

    let html = '';
    let htmlTop = '';

    

    /*
    |--------------------------------------------------------------------------
    | TAGIFY SKILLS
    |--------------------------------------------------------------------------
    */

    if (typeof tagify !== 'undefined') {

        tagify.value.forEach(function(tag){

             html += `<div class="filter-tag">
                        Skills : ${tag.value}
                        <svg class="remove-chip" onclick="removeFilter(this,'skill','${tag.value}')" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>`;

        });
    }
    /*
    |--------------------------------------------------------------------------
    | INDUSTRY
    |--------------------------------------------------------------------------
    */

    let industry = document.getElementById('industry');

    if (industry && industry.value !== '') {

        let text = industry.options[industry.selectedIndex].text;

        html += `<div class="filter-tag">
                        Industry : ${text}
                        <svg class="remove-chip" onclick="removeFilter(this,'industry')" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>`;
    }

    /*
    |--------------------------------------------------------------------------
    | EXPERIENCE
    |--------------------------------------------------------------------------
    */

    let experience = document.getElementById('experience');

    if (experience && experience.value !== '') {

         html += `<div class="filter-tag">
                        Experience : ${experience.value}
                        <svg class="remove-chip" onclick="removeFilter(this,'experience')" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>`;
    }

    /*
    |--------------------------------------------------------------------------
    | LOCATION
    |--------------------------------------------------------------------------
    */

    let country = document.getElementById('country');

    if (country && country.value !== '') {

        html += `<div class="filter-tag">
                        Location : ${country.value}
                        <svg class="remove-chip" onclick="removeFilter(this,'country')" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>`;
    }


     let hiring_type = document.getElementById('hiring_type').value;
     
        $('#sortingfilter').val(hiring_type);

    // if(hiring_type==2){
    //     htmlTop += `Hiring Type  : Full-Time Hiring
    //                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    //                     <path d="M6 9l6 6 6-6"></path>
    //                     </svg>`;

    // }else if(hiring_type==1){
    //     htmlTop += `Hiring Type  : Contract Hiring
    //                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    //                     <path d="M6 9l6 6 6-6"></path>
    //                 </svg>`;

    // }
    const filterBox = document.getElementById('selectedFilters');
    if((typeof tagify !== 'undefined' && tagify.value.length > 0) || industry && industry.value !== '' || experience && experience.value !== '' || country && country.value !== ''){
        filterBox.style.display = 'flex';
        html += ` <button class="clear-all" id="clearAllFilters">Clear all</button>
                    <div class="sort-row">
                        Sort by:
                        <select>
                        <option>Best Match</option>
                        <option>Rating</option>
                        <option>Recent</option>
                        </select>
                    </div>`;
    }else{
        filterBox.style.display = 'none';
    }


    document.getElementById('selectedFilters').innerHTML = html;
    // document.getElementById('hiringTypehtmlTop').innerHTML = htmlTop;
}

 $('body').off('change', '#sortingfilter').on('change', '#sortingfilter', function(e) {
        e.preventDefault();
        var sortingfilter = $(this).val();
        if(sortingfilter == 2){
            selectHiringType('full');
        }else{
            selectHiringType('contract');
        }   
        fetchClientConpanyAgency(1, $('#search').val() || '', false);         
    });

function removeFilter(el, type, value = '') {
    if (el && el.closest('.filter-tag')) {
        el.closest('.filter-tag').remove();
    }

    if (type === 'skill') {
        if (typeof tagify !== 'undefined' && Array.isArray(tagify.value)) {
            let tagData = tagify.value.find(function(tag){
                return tag.value.toLowerCase() === value.toLowerCase();
            });
            if (tagData) {
                tagify.removeTags(tagData.value);
            }
        }

        document.querySelectorAll('.pop-tag').forEach(function(tag){
            if (tag.getAttribute('data-value').toLowerCase() === value.toLowerCase()) {
                tag.classList.remove('pop-added');
            }
        });
    } else if (type === 'industry') {
        var indEl = document.getElementById('industry');
        if (indEl) indEl.selectedIndex = 0;
    } else if (type === 'experience') {
        var expEl = document.getElementById('experience');
        if (expEl) expEl.selectedIndex = 0;
    } else if (type === 'country') {
        var cntEl = document.getElementById('country');
        if (cntEl) cntEl.selectedIndex = 0;
    }

    fetchClientConpanyAgency(1, $('#search').val() || '', false);
}


    /*
    |--------------------------------------------------------------------------
    | AUTO ACTIVE CLASS AFTER REMOVE
    |--------------------------------------------------------------------------
    */

    tagify.on('remove', function(e) {

        let removedValue = e.detail.data.value;

        document.querySelectorAll('.pop-tag').forEach(function(tag) {

            if (
                tag.getAttribute('data-value').toLowerCase() === removedValue.toLowerCase()
            ) {
                tag.classList.remove('pop-added');
            }
        });
    });

    $(document).off('click', '#clearAllFilters').on('click', '#clearAllFilters', function() {
        tagify.removeAllTags();
        if (typeof tagify2 !== 'undefined') {
            tagify2.removeAllTags();
        }
        /*
        |--------------------------------------------------------------------------
        | RESET SELECT DROPDOWNS
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('select').forEach(function(select){

            select.selectedIndex = 0;
        });

        /*
        |--------------------------------------------------------------------------
        | RESET CHECKBOXES
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){

            checkbox.checked = false;
        });

        /*
        |--------------------------------------------------------------------------
        | RESET RADIO BUTTONS
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('input[type="radio"]').forEach(function(radio){

            radio.checked = false;
        });
        /*
        |--------------------------------------------------------------------------
        | OPTIONAL AJAX RELOAD
        |--------------------------------------------------------------------------
        */

        filtersdataget();



    });

    /* â”€â”€ Hiring type toggle â”€â”€ */
    function selectHiringType(type) {
        var cardFull     = document.getElementById('card-full');
        var cardContract = document.getElementById('card-contract');
        var radioFull    = document.getElementById('radio-full');
        var radioContract = document.getElementById('radio-contract');

        if (type === 'full') {
        document.getElementById('hiring_type').value = 2;
        cardFull.className     = 'hiring-card active-blue';
        cardFull.setAttribute('aria-pressed', 'true');
        cardContract.className = 'hiring-card';
        cardContract.setAttribute('aria-pressed', 'false');
        radioFull.className    = 'radio-dot checked-blue';
        radioFull.innerHTML    = '<div class="dot-inner"></div>';
        radioContract.className = 'radio-dot';
        radioContract.innerHTML = '';
        } else {
        document.getElementById('hiring_type').value = 1;
        cardContract.className  = 'hiring-card active-teal';
        cardContract.setAttribute('aria-pressed', 'true');
        cardFull.className      = 'hiring-card';
        cardFull.setAttribute('aria-pressed', 'false');
        radioContract.className = 'radio-dot checked-teal';
        radioContract.innerHTML = '<div class="dot-inner"></div>';
        radioFull.className     = 'radio-dot';
        radioFull.innerHTML     = '';
        }
    }

    document.querySelectorAll('.hiring-card').forEach(function(card) {
        card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
        });
    });

        var activeDetailXHR = null;
        window.JeroAgencyDetailCache = window.JeroAgencyDetailCache || new Map();

        $(document).off('click', '.show-detail-direct').on('click', '.show-detail-direct', function() {
            $('#kt_activitiesDirectClient_toggle').trigger('click');
            var id = $(this).data('row-id');
            if (!id) return;

            // Check L1 In-Memory Cache (Instant 0ms render without network hit)
            if (window.JeroAgencyDetailCache.has(id)) {
                $('#kt_activitiesDirectClient_body').html(window.JeroAgencyDetailCache.get(id));
                return;
            }

            // Abort prior in-flight detail fetch to prevent race conditions
            if (activeDetailXHR && activeDetailXHR.readyState !== 4) {
                activeDetailXHR.abort();
            }

            var url = "{{ route('service_company.consultancy.dviewmore', ':id') }}";
            url = url.replace(':id', id);

            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $('#kt_activitiesDirectClient_body').html('<div class="d-flex justify-content-center p-5"><img src="' + baseurl + '" height="35px"></div>');

            activeDetailXHR = $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response && response.view) {
                        window.JeroAgencyDetailCache.set(id, response.view);
                        $('#kt_activitiesDirectClient_body').html(response.view);
                    }
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('#kt_activitiesDirectClient_body').html('<div class="alert alert-danger m-4">Failed to load agency details. Please try again.</div>');
                    }
                }
            });
        });

        var isFilterSubmitting = false;
        function filtersdataget() {
            if (isFilterSubmitting) return;
            isFilterSubmitting = true;

            var $btn = $('.changetextupdate');
            var originalText = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Searching...');

            fdClose();

            fetchClientConpanyAgency(1, $('#search').val() || '', false).finally(function() {
                $btn.html(originalText);
                isFilterSubmitting = false;
            });
        }


    /**
     * =========================================================================
     * JERO FIND AGENCY CLIENT-FIRST ENGINE (L1 RAM + L2 INDEXEDDB + ECHO SWR)
     * =========================================================================
     * Engineered for 10,000 Concurrent Recruiter Sessions with 0% avoidable server strain.
     * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
     */
    window.JeroFindAgencyRAM = {
        cache: new Map(),
        get: function(key) { return this.cache.get(key) || null; },
        set: function(key, val) {
            if (this.cache.size >= 50) {
                var firstKey = this.cache.keys().next().value;
                this.cache.delete(firstKey);
            }
            this.cache.set(key, val);
        },
        clear: function() { this.cache.clear(); }
    };

    window.JeroFindAgencyDB = (function() {
        var DB_NAME = 'jero_find_agency_tenant_{{ auth()->check() ? auth()->user()->company_id : 0 }}';
        var DB_VERSION = 1;
        var STORE_PAGES = 'pages';
        var PAGE_TTL_MS = 300000; // 5 minutes TTL
        var dbInstance = null;

        function getDB() {
            if (dbInstance) return Promise.resolve(dbInstance);
            return new Promise(function(resolve) {
                if (!window.indexedDB) return resolve(null);
                try {
                    var req = window.indexedDB.open(DB_NAME, DB_VERSION);
                    req.onupgradeneeded = function(e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains(STORE_PAGES)) {
                            db.createObjectStore(STORE_PAGES, { keyPath: 'key' });
                        }
                    };
                    req.onsuccess = function(e) {
                        dbInstance = e.target.result;
                        resolve(dbInstance);
                    };
                    req.onerror = function() { resolve(null); };
                } catch(e) { resolve(null); }
            });
        }

        return {
            getPage: function(key) {
                return getDB().then(function(db) {
                    if (!db) return null;
                    return new Promise(function(resolve) {
                        try {
                            var tx = db.transaction(STORE_PAGES, 'readonly');
                            var store = tx.objectStore(STORE_PAGES);
                            var req = store.get(String(key));
                            req.onsuccess = function() {
                                var res = req.result;
                                if (!res) return resolve(null);
                                if (Date.now() - res.timestamp > PAGE_TTL_MS) return resolve(null);
                                resolve(res.data);
                            };
                            req.onerror = function() { resolve(null); };
                        } catch(e) { resolve(null); }
                    });
                });
            },
            setPage: function(key, data) {
                return getDB().then(function(db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(STORE_PAGES, 'readwrite');
                        var store = tx.objectStore(STORE_PAGES);
                        store.put({ key: String(key), data: data, timestamp: Date.now() });
                    } catch(e) {}
                });
            },
            clear: function() {
                return getDB().then(function(db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(STORE_PAGES, 'readwrite');
                        tx.objectStore(STORE_PAGES).clear();
                    } catch(e) {}
                });
            }
        };
    })();

    var activeAgencyXHR = null;

    var CURRENT_TENANT_ID = {{ auth()->check() ? auth()->user()->company_id : 0 }};
    var CURRENT_USER_ID = {{ auth()->check() ? auth()->user()->id : 0 }};

    function canonicalJsonStringify(obj) {
        if (obj === null || typeof obj !== 'object') {
            return JSON.stringify(obj);
        }
        if (Array.isArray(obj)) {
            return '[' + obj.map(canonicalJsonStringify).join(',') + ']';
        }
        var keys = Object.keys(obj).sort();
        return '{' + keys.map(function(k) {
            return JSON.stringify(k) + ':' + canonicalJsonStringify(obj[k]);
        }).join(',') + '}';
    }

    function sha256Sync(ascii) {
        function rightRotate(v, a) { return (v >>> a) | (v << (32 - a)); }
        var lengthProperty = 'length';
        var i, j;
        var result = '';
        var words = [];
        var asciiBitLength = ascii[lengthProperty] * 8;
        var hash = [
            0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
            0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
        ];
        var k = [
            0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
            0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
            0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
            0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
            0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
            0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
            0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
            0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
        ];
        ascii += '\x80';
        while (ascii[lengthProperty] % 64 - 56) ascii += '\x00';
        for (i = 0; i < ascii[lengthProperty]; i++) {
            words[i >> 2] |= ascii.charCodeAt(i) << ((3 - i % 4) * 8);
        }
        words[words[lengthProperty]] = ((asciiBitLength / Math.pow(2, 32)) | 0);
        words[words[lengthProperty]] = (asciiBitLength);
        for (j = 0; j < words[lengthProperty];) {
            var w = words.slice(j, j += 16);
            var oldHash = hash.slice(0);
            for (i = 0; i < 64; i++) {
                var w15 = w[i - 15], w2 = w[i - 2];
                var a = hash[0], e = hash[4];
                var temp1 = (hash[7]
                    + (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25))
                    + ((e & hash[5]) ^ ((~e) & hash[6]))
                    + k[i]
                    + (w[i] = (i < 16) ? w[i] : (
                            w[i - 16]
                            + (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3))
                            + w[i - 7]
                            + (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10))
                        ) | 0
                    )) | 0;
                var temp2 = ((rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22))
                    + ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2]))) | 0;
                hash = [(temp1 + temp2) | 0].concat(hash);
                hash[4] = (hash[4] + temp1) | 0;
                hash.pop();
            }
            for (i = 0; i < 8; i++) {
                hash[i] = (hash[i] + oldHash[i]) | 0;
            }
        }
        for (i = 0; i < 8; i++) {
            for (var i2 = 3; i2 >= 0; i2--) {
                var b = (hash[i] >> (i2 * 8)) & 255;
                result += ((b < 16) ? '0' : '') + b.toString(16);
            }
        }
        return result;
    }

    function buildAgencyFilterHash(params) {
        if (!params || typeof params !== 'object') return 'fa_default';
        var scoped = Object.assign({}, params, {
            _tenant: CURRENT_TENANT_ID,
            _user: CURRENT_USER_ID
        });
        var utf8String = unescape(encodeURIComponent(canonicalJsonStringify(scoped)));
        return 'fa_' + sha256Sync(utf8String);
    }


    function renderAgencyResponse(response) {
        updateSelectedFilters();
        $('.loadingMessage').css({ display: 'none' });
        $('#findAgencyList').html(response.html);
        $('#paginationfindagency').html(response.pagination);
    }

    $('body').off('click', '.send_job_recommanded_request').on('click', '.send_job_recommanded_request', function(event) {
        event.preventDefault();

        var $this = $(this);
        if ($this.prop('disabled') || $this.hasClass('is-submitting')) return;
        $this.prop('disabled', true).addClass('is-submitting');
        var originalBtnText = $this.text();
        $this.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + originalBtnText);

        var from_company_id = $this.data('row-id');
        var current_jobid = $this.data('row-jobid');
        var event_id = $this.data('row-event');
        var matchcount = $this.data('matchcount');
        var id = from_company_id;
        var kt_tagify_location = $('#kt_tagify_location').val();
        var boostercom = $(this).data('boostercom');
        var boostids = boostercom == 1 ? $(this).data('boost-ids') : '';

        // Generate atomic client idempotency key
        var idempotencyKey = 'agency_req_' + from_company_id + '_' + id + '_' + event_id + '_' + Date.now();

        var url = "{{ route('service_company.agency.recommanded_request', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                'from_company_id': from_company_id,
                'event_id': event_id,
                'current_jobid': current_jobid,
                'boostercom': boostercom,
                'boostids': boostids,
                'matchcount': matchcount,
                'kt_tagify_location': kt_tagify_location,
                'idempotency_key': idempotencyKey
            },
            success: function(response) {
                if (response.status === "success") {
                    // Surgical DOM Update without reloading table
                    if (event_id == 1) {
                        $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-danger')
                                      .attr('data-row-event', '-1')
                                      .text('Cancel Request');
                    } else {
                        $('#sp_list_' + id).removeClass('btn-light-danger')
                                      .addClass('btn-light-primary send_job_recommanded_request')
                                      .attr('data-row-event', '1')
                                      .text('Send Request');
                    }

                    // Invalidate browser caches so subsequent navigations are fresh
                    window.JeroFindAgencyRAM.clear();
                    window.JeroFindAgencyDB.clear();

                    Swal.fire("Success", response.message || "Request updated successfully.", "success");
                } else if (response.status === "warning" || response.status === "info" || response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                    $this.text(originalBtnText);
                }
                $this.prop('disabled', false).removeClass('is-submitting');
            },
            error: function() {
                Swal.fire("Error", "Could not complete the request. Please try again.", "error");
                $this.prop('disabled', false).removeClass('is-submitting');
                $this.text(originalBtnText);
            }
        });
    });

    $(document).ready(function() {
        // Initial load with 0ms delay
        fetchClientConpanyAgency(1, '', false);

        // Echo Real-Time WebSocket Channel Listener for Partner Agency Updates
        if (typeof window.Echo !== 'undefined') {
            var tenantCompanyId = {{ auth()->check() ? auth()->user()->company_id : 0 }};
            if (tenantCompanyId > 0) {
                window.Echo.private('company.' + tenantCompanyId)
                    .listen('.agency.request.delta', function(e) {
                        if (e && e.agency_id) {
                            var btn = $('#sp_list_' + e.agency_id);
                            if (btn.length) {
                                if (e.event_type === 'sent') {
                                    btn.removeClass('btn-light-primary send_job_recommanded_request').addClass('btn-light-danger').attr('data-row-event', '-1').text('Cancel Request');
                                } else if (e.event_type === 'cancelled') {
                                    btn.removeClass('btn-light-danger').addClass('btn-light-primary send_job_recommanded_request').attr('data-row-event', '1').text('Send Request');
                                }
                            }
                            window.JeroFindAgencyRAM.clear();
                            window.JeroFindAgencyDB.clear();
                        }
                    });
            }
        }

        $(document).off('click', '#paginationfindagency .pagi-page, #paginationfindagency .pagi-arrow')
            .on('click', '#paginationfindagency .pagi-page, #paginationfindagency .pagi-arrow', function(e) {
                e.preventDefault();

                var page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled') || $(this).hasClass('disabled')) return;

                var $btn = $(this);
                $btn.addClass('disabled');
                fetchClientConpanyAgency(page, $('#search').val() || '', false).finally(function() {
                    $btn.removeClass('disabled');
                });
            });

        $(document).off('change', '#paginationfindagency .pagi-perpage')
            .on('change', '#paginationfindagency .pagi-perpage', function(e) {
                var selectedPerPage = $(this).val();
                if (selectedPerPage) {
                    localStorage.setItem('candidatePerPage', selectedPerPage);
                    fetchClientConpanyAgency(1, $('#search').val() || '', false);
                }
            });
    });

    function fetchClientConpanyAgency(page, search, forceRefresh) {
        if (typeof page === 'undefined') page = 1;
        if (typeof search === 'undefined') search = '';
        if (typeof forceRefresh === 'undefined') forceRefresh = false;

        var perPage = localStorage.getItem('candidatePerPage') || 10;
        var jobid = '0';
        var cus_search = $('#kt_tagify_users').val() || '';
        var kt_tagify_location = $('#country').val() || '';  
        var app_int_outsource = $('#hiring_type').val() || '';
        var experience = $('#experience').val() || '';
        var industry = $('#industry').val() || '';

        var queryParams = {
            jobid: jobid,
            search: search,
            cus_search: cus_search,
            kt_tagify_location: kt_tagify_location,
            experience: experience,
            industry: industry,
            app_int_outsource: app_int_outsource,
            page: page,
            per_page: perPage,
            size: perPage
        };

        var cacheKey = buildAgencyFilterHash(queryParams);

        // 1. Try L1 Browser RAM Cache (Instant 0ms render)
        if (!forceRefresh) {
            var ramCached = window.JeroFindAgencyRAM.get(cacheKey);
            if (ramCached) {
                renderAgencyResponse(ramCached);
                return Promise.resolve(ramCached);
            }

            // 2. Try L2 Tenant-Scoped IndexedDB Cache (<10ms render)
            return window.JeroFindAgencyDB.getPage(cacheKey).then(function(dbCached) {
                if (dbCached) {
                    window.JeroFindAgencyRAM.set(cacheKey, dbCached);
                    renderAgencyResponse(dbCached);
                    return dbCached;
                }
                return executeAgencyFetch();
            });
        }

        return executeAgencyFetch();

        function executeAgencyFetch() {
            // Abort any prior in-flight request to prevent race conditions
            if (activeAgencyXHR && activeAgencyXHR.readyState !== 4) {
                activeAgencyXHR.abort();
            }

            $('.loadingMessage').css({ display: 'block' });

            var headers = {};
            var cachedInRam = window.JeroFindAgencyRAM.get(cacheKey);
            if (cachedInRam && cachedInRam._etag) {
                headers['If-None-Match'] = cachedInRam._etag;
                headers['X-Has-Representation'] = '1';
            }

            return new Promise(function(resolve, reject) {
                activeAgencyXHR = $.ajax({
                    url: `{!! route('service_company.find.findAgencyData') !!}?page=${page}`,
                    type: 'GET',
                    cache: false,
                    headers: headers,
                    data: queryParams,
                    statusCode: {
                        304: function() {
                            $('.loadingMessage').css({ display: 'none' });
                            var preserved = window.JeroFindAgencyRAM.get(cacheKey);
                            if (preserved) {
                                renderAgencyResponse(preserved);
                                resolve(preserved);
                            } else {
                                fetchClientConpanyAgency(page, search, true).then(resolve).catch(reject);
                            }
                        }
                    },
                    success: function(response, statusText, xhr) {
                        if (response) {
                            var etag = xhr.getResponseHeader('ETag');
                            if (etag) {
                                response._etag = etag;
                            }
                            // Store into L1 RAM and L2 IndexedDB
                            window.JeroFindAgencyRAM.set(cacheKey, response);
                            window.JeroFindAgencyDB.setPage(cacheKey, response);

                            renderAgencyResponse(response);
                            resolve(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.loadingMessage').css({ display: 'none' });
                        if (xhr.status === 304) {
                            return;
                        }
                        if (status !== 'abort') {
                            reject(error);
                        }
                    }
                });
            });
        }
    }
        

    </script>
    <script>
        function fdOpen() {
            document.getElementById('apptrackerfilter').classList.add('fd-open');
            document.getElementById('fd-overlay').classList.add('fd-open');
            document.body.style.overflowY = 'hidden';
        }
        function fdClose() {
            document.getElementById('apptrackerfilter').classList.remove('fd-open');
            document.getElementById('fd-overlay').classList.remove('fd-open');
            document.body.style.overflowY = '';
        }
        document.addEventListener('keydown', function(e){ if(e.key==='Escape') fdClose(); });
    </script>
@endpush
