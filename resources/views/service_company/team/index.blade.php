@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
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


        #load_jobs tbody tr {
            height: 82px;
            /* Adjust the height as per your requirement */
            position: relative;
        }
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span
                            class="text-gray-800 mb-1">Team</span> </h5>
                    <!--<span class="text-gray-600" id="">{{ $usersCount }}</span> -->
                </div>
                <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
            </div>
            <div class="d-flex align-items-center py-1">

                <div class="d-flex align-items-center position-relative my-1 me-3">

                    <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>

                <button type="button" class="btn btn-danger teamdelete d-none" data-kt-user-table-select="delete_selected">
                    Inactive
                </button>

            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                    <div class="col-lg-4 d-flex align-items-center px-2" style="width:63%">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                            value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 16%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="sortingfilter" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdataget()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Added Date)</option>
                            <option value="2">Oldest to Newest (Added Date)</option>
                        </select>
                    </div>
                    {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-control="select2" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
                        <option value="110" selected="selected">----------</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
                     <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-2">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                           </svg>
                        </span>
                        Filter
                     </button>
                  </div> --}}
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                        @if (in_array('add_team', $userPermissions))
                            <a href="javascript:void(0);" class="btn btn-primary align-self-center add_team_member me-3 d-flex"
                                data-toggle="modal" data-target="#add_team_member"> <span
                                    class="svg-icon svg-icon-primary svg-icon-1x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path
                                                d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                            <path
                                                d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                fill="#000000" fill-rule="nonzero"></path>
                                        </g>
                                    </svg>
                                </span>
                                 <span>Add Member </span> 
                            </a>
                        @endif

                    </div>




                </div>

                <div class="card">
                    <div class="card-body pt-3 px-5">
                        @if (in_array('view_team', $userPermissions))
						<div class="loadingMessage loadingMessageteamsListajax" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="min-w-250px">Name</th>
                                        <th class="min-w-100px">Rank</th>
                                        <th class="min-w-100px">Jobs</th>
                                        <th class="min-w-100px">Role</th>
                                        <th class="min-w-90px">Status</th>
                                        <th class="min-w-150px">Whatsapp Status</th>
                                        <th class="min-w-150px">Added Date</th>
                                    </tr>
                                </thead>
								<tbody id="teamsListajax"> </tbody>
                            </table>
							<div id="paginationTeams"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->

        <!--begin::Add Team Member Modal-->
        <div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <div class="modal-header" id="add_team_member_header">
                        <h2 class="fw-bolder">Add Team Member</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-danger" data-dismiss="modal"> <span
                                class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" />
                                </svg>
                            </span> </div>


                    </div>
                    <div class="modal-body scroll-y mx-5">
                        <form id="add_team_member_form" name="add_team_member_form" class="form" action="#"
                            method="post">
                            <meta name="csrf-token" content="{{ csrf_token() }}" />
                            @csrf
                            @method('POST')
                            <div id="load_add_team_invitation_content"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Add team member popup-->

        <!--begin::Update role-->
        <div class="modal fade show" id="update_role" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <div class="modal-header" id="update_role_header" data-dismiss="modal">
                        <h2 class="fw-bolder">Update role</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-danger update_role_close"> <span
                                class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)"></rect>
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)"></rect>
                                </svg>
                            </span> </div>
                    </div>
                    <div class="modal-body scroll-y mx-5" id="update_role_body"> </div>
                </div>
            </div>
        </div>
        <!--end::Update role-->


        <div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header flex-stack">
                        <h2>Requisition Status</h2>
                        <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                            id="status_sub_stage_close"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" />
                                </svg>
                            </span> </div>
                    </div>
                    <div class="status_body_content" id="status_body_content"></div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="team_rank_user" tabindex="-1" aria-hidden="true" >
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content custmbg">
                <div class="modal-header flex-stack" style="position: relative;">
                    <h2>Score Board <span id="rankMonth"> </span></h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger data-bs-dismiss" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                    </svg>
                    </span> </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="border-0">
                                                <th class="p-0 min-w-150px">Events</th>
                                                <th class="p-0 min-w-150px">Points</th>
                                                <th class="p-0 min-w-150px">Activity</th>
                                                <th class="p-0 min-w-140px">Earned</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->

                                        <!--begin::Table body-->
                                        <tbody>
                                                <tr style="background: #f8f3f396;">
                                                    <td colspan="4">
                                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">As a individual performance</a>
                                                    </td>
                                                    <td class=
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">InReview</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +1                                    
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="profileUploadAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="profileUpload">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Sourcing</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +1                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="SourcingAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="Sourcing">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Screening</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +1                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="ScreeningAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="Screening">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Evaluation</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +1                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="EvaluationAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="Evaluation">+0</span>
                                                    </td>
                                                </tr>  
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Negotiation</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +1                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="NegotiationAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="Negotiation">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Offers Released</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +300                                  
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="offerReleasedAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="offerReleased">+0</span>
                                                    </td>
                                                </tr>  
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & OnBoarded</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +500                                   
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="candidateJoinedAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="candidateJoined">+0</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & Backed Out</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        -500                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="backoutAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-danger" id="backout">+0</span>
                                                    </td>
                                                </tr>   
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interviews Conducted</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +10                                   
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="interviewScheduledAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="interviewScheduled">+0</span>
                                                    </td>
                                                </tr>  
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interview NoShow</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        -5                                    
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="interviewNoShowAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-danger" id="interviewNoShow">-0</span>
                                                    </td>
                                                </tr> 


                                                <tr style="background: #f8f3f396;">
                                                    <td colspan="4">
                                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">As a Team Captain performance</a>
                                                    </td>
                                                    <td class=
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Offer Released</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +20                               
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="teamCaptainOfferReleasedAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="teamCaptainOfferReleased">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & OnBoarded</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +30                               
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="teamCaptainCandidateJoinedAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="teamCaptainCandidateJoined">+0</span>
                                                    </td>
                                                </tr> 
                                                
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & Backed Out</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        -30                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="backoutTeamAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-danger" id="backoutteam">+0</span>
                                                    </td>
                                                </tr> 
                                            
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Interviews Conducted</a>
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        +2                             
                                                    </td>
                                                    <td class="text-muted fw-bold">
                                                        <span class="" id="teamCaptainInterviewScheduledAc">0</span>
                                                    </td>
                                                    <td class="">
                                                        <span class="text-success" id="teamCaptainInterviewScheduled">+0</span>
                                                    </td>
                                                </tr> 
                                                <tr style="background: #d1ccd3;">
                                                <td colspan="3">
                                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-3">Final Score</a>
                                                    </td>
                                                    <td class="">
                                                        <span class="fs-3" id="score">00</span>
                                                    </td>
                                                </tr> 
                                                                            
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                </div>
                </div>
                </div>
            </div>
        </div>


    @endsection

    @push('footer-script')
        <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
        <div class="closeclass"></div>

        <!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->
        <script>
            var input2 = document.querySelector("#kt_tagify_2");
            // new Tagify(input2);
        </script>
        <script>
            $('#kt_tagify_2').keyup(function() {
                // table._fnDraw();
				fetchTeams();
            });

            function filtersdataget() {
                // table._fnDraw();
				fetchTeams();
            }

             $(document).on('click', '.teamViewScore', function() {
                var profileUpload = '+'+$(this).data('profileupload');
                var interviewScheduled = '+'+$(this).data('interviewscheduled');
                var interviewNoShow = $(this).data('interviewnoshow');
                var offerReleased = '+'+$(this).data('offerreleased');
                var candidateJoined = '+'+$(this).data('candidatejoined');
                var teamCaptainInterviewScheduled = '+'+$(this).data('teamcaptaininterviewscheduled');
                var teamCaptainOfferReleased = '+'+$(this).data('teamcaptainofferreleased');
                var teamCaptainCandidateJoined = '+'+$(this).data('teamcaptaincandidatejoined');
                var sourcing = '+'+$(this).data('sourcing');
                var screening = '+'+$(this).data('screening');
                var evaluation = '+'+$(this).data('evaluation');
                var negotiation = '+'+$(this).data('negotiation');
                var backout = $(this).data('backout');
                var backoutTeam = $(this).data('backoutteam');
                var rankMonth = $(this).data('rankmonth');
                var score = $(this).data('score');
                $('#profileUpload').text(profileUpload);
                $('#profileUploadAc').text(profileUpload/1);
                $('#interviewScheduled').text(interviewScheduled);
                $('#interviewScheduledAc').text(interviewScheduled/10);
                $('#interviewNoShow').text(interviewNoShow);
                $('#interviewNoShowAc').text(interviewNoShow/5);
                $('#offerReleased').text(offerReleased);
                $('#offerReleasedAc').text(offerReleased/300);
                $('#candidateJoined').text(candidateJoined);
                $('#candidateJoinedAc').text(candidateJoined/500);
                $('#teamCaptainInterviewScheduled').text(teamCaptainInterviewScheduled);
                $('#teamCaptainInterviewScheduledAc').text(teamCaptainInterviewScheduled/2);
                $('#teamCaptainOfferReleased').text(teamCaptainOfferReleased);
                $('#teamCaptainOfferReleasedAc').text(teamCaptainOfferReleased/20);
                $('#teamCaptainCandidateJoined').text(teamCaptainCandidateJoined);
                $('#teamCaptainCandidateJoinedAc').text(teamCaptainCandidateJoined/30);                
                $('#Sourcing').text(sourcing);
                $('#SourcingAc').text(sourcing/1);
                $('#Screening').text(screening);
                $('#ScreeningAc').text(screening/1);
                $('#Evaluation').text(evaluation);
                $('#EvaluationAc').text(evaluation/1);
                $('#Negotiation').text(negotiation);
                $('#NegotiationAc').text(negotiation/1);
                $('#backout').text(backout);
                $('#backoutAc').text(backout/500);
                $('#backoutTeam').text(backoutTeam);
                $('#backoutTeamAc').text(backoutTeam/30);
                $('#rankMonth').text(rankMonth);
                $('#score').text(score);
                


                // alert("ji");
                $("#team_rank_user").modal("show");
            });

            $(document).on('click', '.update_role_close', function() {


                // alert("ji");
                $("#update_role").removeAttr("style");
            });
            $(document).on('click', '#kt_activitiesClient_close', function() {
                $(".closeclass").removeAttr("style");
                $(".closeclass").removeClass("drawer-overlay");
                $("#kt_activitiesClient").removeAttr("style");
            });

            $(document).on('click', '.closeclass', function() {

                $("#kt_activitiesClient_close").click();
            });
            $(document).on('click', '.drawer-overlay', function() {
              $("#kt_activitiesClient_close").click();
            });


            $(document).on('click', '.chk_list_item', () => {
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.teamdelete').removeClass("d-none");
                    } else {
                        $('.teamdelete').addClass("d-none");
                    }
                }, 10);

            });
            $(document).on('click', '.chk_list', () => {
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').attr('checked', 'checked');
                    $('.teamdelete').addClass("d-none");
                } else {
                    $('.chk_list_item').removeAttr('checked');
                    $('.teamdelete').removeClass("d-none");
                }
            });

            $(document).on('click', '.teamdelete', () => {
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                var id = 0;
                var url = "{{ route('service_company.team.teamDelete', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
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
                    text: "You want to delete team!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'arr': arr
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    // table._fnDraw();
									fetchTeams();
                                    $('.teamdelete').addClass("d-none");
                                    Swal.fire({
                                        text: "Team inactive successfully.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            }
                        });

                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'Team not delete:)', 'error')
                    }

                })

            });

            function handleClick(currentValue, currentId) {
  
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var statusText = $(currentValue).parents('td').find('.status_text');
                if ($(currentValue).is(':checked')) {
                    statusText.text('Active').removeClass('text-danger').addClass('text-success');
                } else {
                    statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                }
                var url = "{{ route('service_company.team.statusUpdate') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'chkStatus': chkStatus,
                        'userId': userId
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            fetchTeams(); 
                            $('#' + block).remove();  
                        }
                    }
                });
            }

            function reSendhandleClick(currentValue, currentId) {

                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.reSendmail') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";


                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to resend mail!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {

                                    Swal.fire({
                                        text: "Team member Re-send successfully. set password link has been sent to team member email.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });


                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'mail not send:)', 'error')
                    }

                })





            }


            $(document).on('click', '.update-user-role', function() {

                $('.update_role').trigger('click');

                var id = $(this).data('row-id');
                var url = "{{ route('service_company.team.editrole', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                // alert(url);
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#update_role_body').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $("#update_role").attr("style", "display: block;");
                        $('#update_role_body').html(response.view);

                        /*setTimeout(
                        ()=>
                        {
                        		if (response.status == "success")
                        		{
                        			$('#kt_activitiesClient_body').html(response.view);
                        		}
                        }, 500);*/
                    }
                });

            });

            $(document).on('click', '.add_team_member', function() {
                var id = 123;
                var url = "{{ route('service_company.team.add_team_member_form', ':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#load_add_team_invitation_content').html('<img src="' + baseurl +
                            '" height="60%" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == 'coinCheck') {
                            Swal.fire("Failed", ` ${response.message}`, "warning").then(function() {
                                $(".btn-active-icon-primary").click();
                            });
                            return false;
                        }
                        if (response.status == "success") {
                            $('#load_add_team_invitation_content').html(response.view);
                        }

                    }
                });
            });
        </script>

        <script>
            function WorkingStatus(currentValue, currentId) {
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.WorkingStatus') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to update Working Status.!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    swalWithBootstrapButtons.fire('Success!',
                                        'Working status update successfully.', 'success')
                                } else if (response.status == "info") {
                                    if (chkStatus == true) {
                                        $('#working_status_' + userId).prop('checked', false);
                                    } else {
                                        $('#working_status_' + userId).prop('checked', true);
                                    }
                                    swalWithBootstrapButtons.fire('Fail!', response.message, 'error')
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        if (chkStatus == true) {
                            $('#working_status_' + userId).prop('checked', false);
                        } else {
                            $('#working_status_' + userId).prop('checked', true);
                        }
                        swalWithBootstrapButtons.fire('Cancelled', 'Working status can not update:)', 'error')
                    }

                })


            }

            function jointableLoad(userId) {
                return false;
                //    var userId		=	$('#userId').val();
                table = $('#myTable_joining').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.joiningdata') !!}?userId=' + userId,
                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            width: '5%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            width: '20%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Contact',
                            name: 'Contact',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Offer',
                            name: 'Offer',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Joining',
                            name: 'Joining',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                new $.fn.dataTable.FixedHeader(jointable);
            }

            function load_interviewSchedule(userId) {
                return false;

                var userId = $('#userId').val();

                table = $('#load_interviewSchedule').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.intdata') !!}?userId=' + userId,

                    "fnDrawCallback": function(settings, start, end, max, total, pre) {
                        var total_record = this.fnSettings().fnRecordsTotal();
                        $('#total_record').html(total_record);
                        $("body").tooltip({
                            selector: '[data-toggle="tooltip"]'
                        });
                    },


                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0
                        },
                        {
                            "width": "150px",
                            "targets": 1,
                            "class": "align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "50px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "50px",
                            "targets": 6
                        },
                        {
                            "width": "50px",
                            "targets": 7
                        },
                        {
                            "width": "50px",
                            "targets": 8
                        },
                        {
                            "width": "50px",
                            "targets": 9
                        }
                    ],

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
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewMode',
                            name: 'InterviewMode',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Interviewers',
                            name: 'Interviewers',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewDate',
                            name: 'InterviewDate',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Feedback',
                            name: 'Feedback',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Rating',
                            name: 'Rating',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        }
                    ],

                });
                new $.fn.dataTable.FixedHeader(table);

            }

            function load_application(userId) {
                return false;
                // var userId		=	$('#userId').val();
                application_table = $('#load_application').dataTable({

                    responsive: true,
                    processing: true,
                    ordering: false, //disable ordering
                    destroy: true,
                    stateSave: true,
                    "bLengthChange": false,
                    sDom: 'lrtip',
                    ajax: '{!! route('service_company.team.appdata') !!}?userId=' + userId,
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
                        // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
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
                        {
                            data: 'Source',
                            name: 'Source',
                            orderable: false
                        },
                        {
                            data: 'UploadDate',
                            name: 'UploadDate',
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            orderable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(application_table);

            }

            $(document).on('keyup', function() {
                var userId = $('#kt_tagify_hidden').val();
                load_jobs(userId);
            });
            function load_jobs(userId) {
                return false;
                var load_jobs = $('#load_jobs').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering   			
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: {
                        'url': '{!! route('service_company.team.jobdata') !!}?userId=' + userId,
                        "data": function(d) {
                            d.search = $('#inputSearch').val(), d.custom_search = $('#kt_tagify_search').val(), d
                                .custom_status = $('#statusjb').val(), d.custom_hiringtype = $('#hiringtypejob')
                                .val(), d.profile = true
                        }
                    },
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip1"]').tooltip();
                        $('[data-toggle="tooltip2"]').tooltip();
                        $('[data-toggle="tooltip3"]').tooltip();
                        $('[data-toggle="tooltip4"]').tooltip();
                    },
                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0,
                            "width": "4%",
                            "class": ""
                        },
                        {
                            "width": "200px",
                            "targets": 1,
                            "class": "d-flex align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "200px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "200px",
                            "targets": 6
                        },
                        {
                            "width": "150px",
                            "targets": 7
                        },
                        {
                            "width": "150px",
                            "targets": 8
                        },

                        {
                            "width": "20px",
                            "targets": 9
                        },
                        {
                            "width": "20px",
                            "targets": 10
                        },
                        {
                            "width": "20px",
                            "targets": 11
                        },
                        {
                            "width": "20px",
                            "targets": 12
                        },
                        {
                            "width": "20px",
                            "targets": 13
                        },
                        {
                            "width": "20px",
                            "targets": 14
                        },
                        {
                            "width": "20px",
                            "targets": 15
                        },
                        {
                            "width": "20px",
                            "targets": 16
                        }
                    ],
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
                            searchable: false
                        },
                        {
                            data: 'Exp',
                            name: 'Exp',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Salary',
                            name: 'Salary',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Location',
                            name: 'Location',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'Status',
                            name: 'Status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Date',
                            name: 'Date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'HM',
                            name: 'HM',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Partners',
                            name: 'Partners',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'TOT',
                            name: 'TOT',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SOU',
                            name: 'SOU',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SCR',
                            name: 'SCR',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'EVA',
                            name: 'EVA',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'NEO',
                            name: 'NEO',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'OFF',
                            name: 'OFF',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'JOI',
                            name: 'JOI',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'REJ',
                            name: 'REJ',
                            orderable: false,
                            searchable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(load_jobs);
            }

			$(document).ready(function() {
				fetchTeams();
				$(document).on('click', '#paginationTeams .page-link', function(e) {
						e.preventDefault();

						const page = $(this).data('page');
						if (!page || $(this).hasClass('disabled')) return;

						// Disable the clicked pagination link
						$(this).addClass('disabled');

						// Fetch data
						fetchTeams(page, $('#search').val()).then(() => {
							// Re-enable the links after successful data fetch
							$('#paginationTeams .page-link').removeClass('disabled');
						}).catch(() => {
							// Re-enable the links even if there's an error
							$('#paginationTeams .page-link').removeClass('disabled');
						});
						
				});
			});
            
               var page_no = localStorage.getItem('currentPageTeam');
                if (!page_no) {
                    page_no = 1;
                }
			

			function fetchTeams(page = page_no, search = '') {
				$('.loadingMessageteamsListajax').css({ display: 'block' });
				$.ajax({
					url: `{!! route('service_company.team.data_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
						country: $('#country').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessageteamsListajax').css({ display: 'none' });
                        localStorage.setItem('currentPageTeam', 1);
						$('#teamsListajax').html(response.html);
						$('#paginationTeams').html(response.pagination);
					}
				});
			}



        </script>
    @endpush
