@php
    use App\Helper\GeneralMethod;
    $userPermissions = [];
@endphp
<style>
    @media only screen and (min-width: 900px) and (max-width: 2560px) {

        .table td,
        .table th,
        .table tr {
            width: max-content !important;
        }

        .table> :not(caption)>*>* {
            padding: 0.75em 0rem !important;
        }

        td:nth-child(n+2),
        th:nth-child(n+2) {
            width: revert !important;
            border-bottom: 1px dashed #f5f8fa !important;
        }
    }

    .text-justify {
        text-align: justify !important;
    }

    @media only screen and (min-width: 900px) and (max-width: 2560px) {

        .table td,
        .table th,
        .table tr {
            width: max-content !important;
        }

        .table> :not(caption)>*>* {
            padding: 0.75em 0rem !important;
        }

        td:nth-child(n+2),
        th:nth-child(n+2) {
            width: revert !important;
            border-bottom: 1px dashed #f5f8fa !important;
        }
    }

    $("timeline-item:last-child").addClass("timeline-item:last-child");

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

    .tagify__input {
        height: 18px !important;
    }

    body {
        overflow-x: hidden;
    }

    .company-logo {
        max-height: 50px;
        max-width: 120px;
    }

    .company-logo-div {
        border-radius: 2px;
        padding: 2px 2px 2px 2px;
        margin-bottom: 2px;
    }

    .symbol.symbol-circle,
    .symbol.symbol-circle .symbol-label,
    .symbol.symbol-circle>img {
        border-radius: 10%;
    }

    @media only screen and (min-width: 1024px) and (max-width: 1440px) {
        #kt_activitiesWallet {
            width: 1025px;
        }
    }

    @media only screen and (min-width: 1440px) and (max-width: 2560px) {
        #kt_activitiesWallet {
            width: 1200px;
        }
    }

    .payment-method {
        display: none;
        /* Hide all sections initially */
        margin-bottom: 20px;
    }

    .active {
        display: block;
        /* Show active section */
    }
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $client->id }}" />
    <input type="hidden" name="hid_compant_id" id="hid_compant_id" value="{{ $client->company_id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        <div class="card mb-5 mb-xl-5 mx-2">
            <div class="card-body pt-5 pb-0 px-5">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <!--begin: Pic-->
                    <div class="me-7 mb-4">

                        @php
                            $bgTextDecore = $strVal = '';
                            $str = trim(@$client->company_name);
                            $str = $strVal = strtolower($str[0]);
                            $str = ord($str);
                            if ($str >= 97 && $str <= 101) {
                                $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                            } elseif ($str >= 102 && $str <= 106) {
                                $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                            } elseif ($str >= 107 && $str <= 111) {
                                $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                            } elseif ($str >= 112 && $str <= 116) {
                                $bgTextDecore = 'fs-3 bg-light-success text-success';
                            } elseif ($str >= 117 && $str <= 121) {
                                $bgTextDecore = 'fs-3 bg-dark text-secondary';
                            } else {
                                $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                            }

                            $background_image = '';
                            if (!empty(@$client->logo) && strlen(@$client->logo) > 10) {
                                $background_image = @$client->logo;
                            }

                            $background_image_row = '';
                            if (!empty($background_image)) {
                                $background_image_row =
                                    '<img src="' .
                                    str_replace('index.php', '', @$background_image) .
                                    '" height="100px" width="100px"  />';
                            } else {
                                $background_image_row =
                                    '<div class="symbol-label fs-3 ' .
                                    @$bgTextDecore .
                                    ' border-radius-5"><span>' .
                                    ucwords(@$strVal) .
                                    '</span></div>';
                            }

                        @endphp
                        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <a
                                href="javascript:void(0);">{!! @$background_image_row !!}</a>
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                            </div>
                        </div>
                    </div>
                    <!--end::Pic-->
                    <!--begin::Info-->
                    <div class="flex-grow-1">
                        <!--begin::Row-1-->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <!--begin::Candidate Details-->
                            <div class="d-flex flex-column">
                                <!--begin::Name & Candidate ID-->
                                <div class="d-flex align-items-center mb-2"> <span
                                        class="text-gray-900 fs-2 fw-bolder me-2">
                                        {{ ucwords(@$client->company_name) }}
                                    </span> <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Verified">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                        <span class="svg-icon svg-icon-1 svg-icon-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                    fill="#00A3FF"></path>
                                                <path class="permanent"
                                                    d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z"
                                                    fill="white"></path>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a> </div>
                                <!--end::Name & Candidate ID-->
                                <!--begin::Designaiton-->
                                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <!--begin::Svg Icon-->
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z"
                                                        fill="#000000" />
                                                    <rect fill="#FFFFFF" x="13" y="8" width="3" height="3"
                                                        rx="1" />
                                                    <path
                                                        d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z"
                                                        fill="#000000" opacity="0.3" />
                                                </g>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        @php
                                            $industries_name = '';
                                            if (trim($client->industries) != '') {
                                                // dd($client->industries);
                                                $industries_name = $client->industries_data[0]->ind_name;
                                            }
                                            echo $industries_name;
                                        @endphp
                                    </a> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <!--begin::Svg Icon-->
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z"
                                                    fill="black"></path>
                                                <path
                                                    d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z"
                                                    fill="black"></path>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        @php
                                            echo ucwords($city_name);
                                            echo ' , ';
                                            echo ucwords($country_name);

                                        @endphp
                                    </a> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                        <!--begin::Svg Icon-->
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <path
                                                        d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                    <path
                                                        d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                        fill="#000000" fill-rule="nonzero" />
                                                </g>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        Badge</a> </div>
                                <!--end::Designaiton-->
                            </div>
                            <!--end::Candidate Details-->
                            <!--begin::Actions-->
                            <div class="d-flex mt-4">
                                <!--end::Active-->
                                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-{{ @$account->accountVerify ? 'success' : 'warning' }} fw-bolder me-3 fs-6">
                                    @if(@$account->accountVerify)
                                      <span class="badge badge-light-success">Verified</span>
                                    @else
                                      <span class="badge badge-light-warning">Verification Pending</span>
                                    @endif
                                </a>
                                <!--begin::Inactive-->
                                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-info btn-active-info fw-bolder me-3 fs-6">
                                    <span class="checkActivetext text-{{ @$users->status === 'active' ? 'success' : 'danger' }}"
                                        id="">{{ @$users->status === 'active' ? 'Active' : 'Suspend' }}
                                    </span>
                                </a>

                                
                                <a href="javascript:void(0);" id="app_meeting_schedule"
                                    class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"
                                    data-toggle="modal" data-target="#interview_schedule" rel="{{ @$client->id }}">
                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path
                                                    d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                    fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path
                                                    d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                    fill="#000000" fill-rule="nonzero"></path>
                                            </g>
                                        </svg>
                                    </span> Meeting </a>


                                <!--end::Inactive-->
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Row-1-->
                        <!--begin::Row-2-->
                        <div class="d-flex flex-wrap flex-stack">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="row w-75"> <span class="">
                                        @php
                                            $tags = [];
                                            $tags_type = @$client->clients()->where('self_company', 1)->get();

                                            if (@$tags_type) {
                                                if (count($tags_type) > 0) {
                                                    $tags = @$tags_type[0]->tags;
                                                    $tags = explode(',', $tags);
                                                }
                                            }

                                            if ($tags) {
                                                foreach ($tags as $key => $val) {
                                                    echo '<span class="badge badge-light">' .
                                                        ucwords($val) .
                                                        '</span>  ';
                                                }
                                            }

                                        @endphp
                                    </span> </div>
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Row-2-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--begin::separator-->
                <div class="separator separator-solid my-7"></div>
                <!--end::separator-->
                <!--begin::section-->
                <div class="d-flex align-items-center flex-wrap pb-7">
                    <!--begin: Item1-->
                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                        <!--begin::Svg Icon-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z"
                                        fill="#000000" />
                                    <rect fill="#FFFFFF" x="13" y="8" width="3" height="3"
                                        rx="1" />
                                    <path
                                        d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Company Type</span>
                            <span class="fw-bolder fs-3">
                                @if ($client->company_type > 0 || $client->company_type == 0)
                                    {{ $companyType[@$client->company_type] }}
                                @endif
                            </span> </div>
                    </div>
                    <!--end: Item1-->
                    <!--begin: Item2-->
                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                        <!--begin::Svg-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Employees</span>
                            <span class="fw-bolder fs-3">{{ number_format(@$client->company_size) }}</span> </div>
                    </div>
                    <!--end: Item2-->
                    <!--begin: Item3-->
                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                        <!--begin::Svg-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Active
                                Requisition</span> <span
                                class="fw-bolder fs-3">{{ number_format(@$active_requisition) }}</span> </div>
                    </div>
                    <!--end: Item3-->
                    <!--begin: Item4-->
                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                        <!--begin::Svg-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M7,3 L17,3 C19.209139,3 21,4.790861 21,7 C21,9.209139 19.209139,11 17,11 L7,11 C4.790861,11 3,9.209139 3,7 C3,4.790861 4.790861,3 7,3 Z M7,9 C8.1045695,9 9,8.1045695 9,7 C9,5.8954305 8.1045695,5 7,5 C5.8954305,5 5,5.8954305 5,7 C5,8.1045695 5.8954305,9 7,9 Z"
                                        fill="#000000" />
                                    <path
                                        d="M7,13 L17,13 C19.209139,13 21,14.790861 21,17 C21,19.209139 19.209139,21 17,21 L7,21 C4.790861,21 3,19.209139 3,17 C3,14.790861 4.790861,13 7,13 Z M17,19 C18.1045695,19 19,18.1045695 19,17 C19,15.8954305 18.1045695,15 17,15 C15.8954305,15 15,15.8954305 15,17 C15,18.1045695 15.8954305,19 17,19 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Founded</span>
                            <span class="fw-bolder fs-3">
                                @php
                                    $currentDate = $foundedDate = $finalDate = 0;
                                    $currentDate = date('Y');
                                    $foundedDate = @$client->founded;
                                    $finalDate = $currentDate - $foundedDate;
                                    echo number_format(@$finalDate);
                                @endphp
                                Years ago</span> </div>
                    </div>
                    <!--end: Item4-->
                    <!--begin: Item5-->
                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                        <!--begin::Svg-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="d-flex flex-column text-dark-75"> 
                            <span class="fw-bolder fs-6">Joined</span> <span
                                class="fw-bolder fs-3">
                                @php
                                    $currentDate = $createDate = $finalDate = 0;
                                    $currentDate = date('Y');
                                    $createDate = date('Y', strtotime(@$client->created_at));
                                    $finalDate = $currentDate - $createDate;
                                    echo number_format(@$finalDate);
                                @endphp
                                Years ago</span> </div>
                    </div>
                    <!--end: Item5-->
                    <!--begin: Item-->
                    <div class="d-flex align-items-center flex-lg-fill my-1">
                        <!--begin::Svg Icon-->
                        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z"
                                        fill="#000000" />
                                    <path
                                        d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <div class="symbol-group symbol-hover">
                            @if ($client_contact_info)
                                @foreach ($client_contact_info as $cci)
                                    @php
                                        $image_path = str_replace('index.php','',url('public/avatar.png'));
                                    @endphp
                                    <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ @$cci->contact_person_name }}"> <img
                                            alt="Pic" src="{{ $image_path }}"></div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <!--end: Item-->
                </div>
                <!--end::sections-->
                <!--end::Details-->
            </div>
        </div>
        <!--end::candidate details-->
        <!--begin::Tabs-->
        <div class="card card-custom mx-2">
            <!--Begin::Tabs Header-->
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x"
                        role="tablist">
                        <li class="nav-item me-1"> <a class="nav-link active" data-toggle="tab" href="#overview">
                                <span class="nav-text font-weight-bold">Overview</span> </a> </li>
                        <li class="nav-item me-1 TeamListOpen"> <a class="nav-link" data-toggle="tab" href="#contacts"> <span
                                    class="nav-text font-weight-bold">Teams</span> </a> </li>
                        <li class="nav-item me-1 JobListOpen"> <a class="nav-link" data-toggle="tab" href="#requisitions"> <span
                                    class="nav-text font-weight-bold">Jobs</span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#timeline"> <span
                                    class="nav-text font-weight-bold">Timeline</span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#packages"> <span
                                    class="nav-text font-weight-bold">Op. Package</span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#invoices"> <span
                                    class="nav-text font-weight-bold">Sales Package</span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#topupcoins"> <span
                                    class="nav-text font-weight-bold">Top Up Coins </span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#transactionhistory">
                                <span class="nav-text font-weight-bold"> History </span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#migration"> <span
                                    class="nav-text font-weight-bold">Migration</span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#codeSetting"> <span
                                    class="nav-text font-weight-bold"> Setting </span> </a> </li>
                        <li class="nav-item me-1"> <a class="nav-link" data-toggle="tab" href="#boostkeyword"> <span
                                        class="nav-text font-weight-bold"> Boost Keywords </span> </a> </li>            

                    </ul>
                </div>
            </div>
            <!--end::Tabs Header-->
            <!--Begin::Body-->
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                    <!--begin::Overview Content-->
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <!--begin::Overview header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Overview</h3>
                            </div>
                            <a href="{{ route('superadmin.company.edit_company', \Crypt::encryptString($client->id)) }}"
                                class="btn btn-primary align-self-center">Edit Profile</a>
                            <!--end::Card title-->
                            @if (in_array('edit_clients', $userPermissions))
                                <!--begin::Action-->
                                <a href="{{ route('service_company.clients.edit', $client->id) }}"
                                    class="btn btn-primary align-self-center">Edit Client</a>
                                <!--end::Action-->
                            @endif
                        </div>
                        <!--end::Overview header-->
                        <div class="card-body px-0 py-7">
                            <!--begin::Overview Content-->
                            <div class="row">
                                <!--begin::Col-Desc-->
                                <div class="col-lg-12 mb-7">
                                    @php
                                        $description = '';
                                        $description = @$client->description;
                                        echo $description;
                                    @endphp
                                </div>
                                <div class="col-lg-12">
                                    <!--end::Col-Desc-->
                                    <!--begin::Row-1-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                         <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9">
                                                <a href="javascript:void(0);"class="fw-bold fs-6 text-primary">{{@$client->website }}</a> 
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-1-->
                                    <!--begin::Row-2-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> 
                                            <span class="fw-bold fs-6 text-muted">{{ @$industries_name }} </span> 
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-2-->
                                    <!--begin::Row-3-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                                {{ @$client->company_sizes }} + employees, </span> 
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-3-->
                                    <!--begin::Row-4-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> 
                                            <span class="fw-bold fs-6 text-muted">
                                                {{ ucwords($city_name) }} {{ ',' }}
                                                {{ ucwords($country_name) }}
                                            </span> 
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-4-->
                                    <!--begin::Row-5-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Type</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> 
                                            <span class="fw-bold fs-6 text-muted">
                                                    @if ($client->company_type > 0 || $client->company_type == 0)
                                                        {{ $companyType[@$client->company_type] }}
                                                    @endif
                                                </span> 
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-5-->
                                    <!--begin::Row-6-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> 
                                            <span class="fw-bold fs-6 text-muted">{{ @$client->founded }}</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-6-->
                                    <!--begin::Row-5-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-9"> 
                                            <span class="fw-bold fs-6 text-muted">
                                                @php
                                                    $tags = @$client->tags;
                                                    $tags = explode(',', $tags);
                                                    $cnt = 0;
                                                    if ($tags) {
                                                        foreach ($tags as $key => $val) {
                                                            if ($cnt > 0) {
                                                                echo ', ';
                                                            }
                                                            echo '<span class="">' . ucwords($val) . '</span> ';
                                                            $cnt++;
                                                        }
                                                    }
                                                @endphp
                                            </span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-5-->
                                </div>
                            </div>
                            <!--end::Overview Content-->
                        </div>
                    </div>
                    <!--end::Overview Content-->
                    <!--begin::Contacts Content-->
                    <div class="tab-pane" id="contacts" role="tabpanel">
                        <!--begin::Contacts header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Contact List</h3>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Action-->


                            {{-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Contact</a> --}}
                            <!--end::Action-->
                        </div>
                        <!--end::Contacts header-->
                        <!--begin::Contacts Content-->
                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <div class="loadingMessage loadingMessageteamsListajax" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="contactListss">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_table_users .form-check-input2"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-150px">Role</th>
                                        <th class="min-w-150px">Contacts</th>
                                        <th class="min-w-15px">Status</th>
                                        <th class="min-w-15px">Action</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                               <tbody id="teamsListajax"> </tbody>
                            </table>
                            <!--end::Table-->
                            <div id="paginationTeams"></div>
                        </div>
                        <!--end::Contacts Content-->
                    </div>
                    <!--end::Contacts Content-->
                    <!--begin::invoices Content-->
                    <div class="tab-pane" id="invoices" role="tabpanel">
                        <div class="card-header cursor-pointer px-0" style="display: block;">

                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                id="">


                                <thead id="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="min-w-200px">Package</th>
                                        <th class="min-w-150px">Duration</th>
                                        <th class="min-w-150px">Total Coins</th>
                                        <th class="min-w-150px">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @php

                                        $coin_packages_free = DB::table('coin_packages')
                                            ->join(
                                                'coin_package_types',
                                                'coin_package_types.id',
                                                '=',
                                                'coin_packages.coin_package_type_id',
                                            )
                                            ->join(
                                                'coin_orders',
                                                'coin_orders.coin_package_id',
                                                '=',
                                                'coin_packages.id',
                                            )
                                            ->join('companies', 'companies.id', '=', 'coin_orders.company_id')
                                            ->join('company_coins', 'company_coins.company_id', '=', 'companies.id')
                                            ->select(
                                                'coin_packages.*',
                                                'coin_package_types.title',
                                                'company_coins.free_pack_status',
                                            )
                                            ->where('coin_packages.custome_pck', 2)
                                            ->where('coin_packages.event_type', '=', 'sales')
                                            ->orderByDesc('coin_orders.id')
                                            ->take(1)
                                            ->get();
                                    @endphp
                                    @if ($coin_packages_free)
                                        @foreach ($coin_packages_free as $packages_free)
                                            <tr class="fw-bolder text-muted">
                                                <td class="min-w-200px">{{ $packages_free->title }}</td>
                                                <td class="min-w-150px">
                                                    {{ $packages_free->days ? $packages_free->days : 0 }} Days</td>
                                                <td class="min-w-150px">{{ $packages_free->coins }}</td>
                                                <td class="min-w-150px">
                                                    <div class="col-sm-6 col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-8"></label>

                                                            <div class="col-sm-4">
                                                                <div
                                                                    class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                                    <input type="checkbox"
                                                                        class="js-switch form-check-input checkStatusSales"
                                                                        name="checkStatusSales"
                                                                        @if (@$packages_free->free_pack_status == 'active') checked @endif
                                                                        data-color="#99d683" data-size="small" />
                                                                </div>

                                                                <a href="javascript:void(0);"
                                                                    class="text-muted fw-bold d-block fs-7 checkStatusSales_text">{{ @$packages_free->free_pack_status == 'active' ? 'Active' : 'Inactive' }}</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>

                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">
                                    Current Package
                                </h3>

                                <a href="<?php echo url('super-admin/custompackages/create/' . @$client->id); ?>?packageType={{ encrypt('sales') }}"
                                    class="btn btn-primary align-self-center"
                                    style="position: absolute; right: 18px;">New Package</a>
                            </div>

                            <div class="card-body px-0 py-7">
                                <div class="table-responsive">

                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                        id="">


                                        <tbody id="">
                                            @if (@$company_coins_sales)

                                                @foreach ($company_coins_sales as $package_sales)
                                                    <tr class="fw-bolder text-muted">
                                                        <th class="min-w-200px">Package</th>
                                                        <th class="min-w-200px">Status</th>
                                                        <th class="min-w-150px">Duration</th>
                                                        <th class="min-w-150px">Total Coins</th>
                                                        <th class="min-w-150px">Remaining Coins</th>
                                                    </tr>


                                                    <tr class="fw-bolder text-muted">
                                                        <td class="text-dark fs-6"><?php echo @$package_sales['title'] ? @$package_sales['title'] : ''; ?></td>
                                                        <td class="text-dark fs-6">
                                                            @if (strtotime(@$package_sales['end_date']) >= strtotime(date('Y-m-d')) && @$package_sales['status'] == 'valid')
                                                                Active
                                                            @else
                                                                Expired
                                                            @endif
                                                        </td>
                                                        <td class="text-dark fs-6"><?php echo @$package_sales['start_date'] ? date('d-M-Y', strtotime(@$package_sales['start_date'])) : ''; ?> to
                                                            <?php echo @$package_sales['end_date'] ? date('d-M-Y', strtotime(@$package_sales['end_date'])) : ''; ?></td>
                                                        <td class="text-dark fs-6"><?php echo @$package_sales['order_coins']; ?></td>
                                                        <td class="text-dark fs-6"><?php echo @$package_sales['coins']; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="5">
                                                            <h3 class="fw-bolder m-0">
                                                                Credit Usage Overview per Activity
                                                            </h3>
                                                        </td>
                                                    </tr>

                                                    {{-- <tr>
                                                    <td colspan="4">
                                                        <span class="badge bg-primary"><?php echo @$package_sales['title'] ? @$package_sales['title'] : ''; ?></span>
                                                        <span class="badge bg-info"><?php echo @$package_sales['start_date'] ? date('d-M-Y', strtotime(@$package_sales['start_date'])) : ''; ?> to <?php echo @$package_sales['end_date'] ? date('d-M-Y', strtotime(@$package_sales['end_date'])) : ''; ?></span>
                                                        <span class="badge bg-success">Total Coins - <?php echo @$package_sales['order_coins']; ?></span>
                                                        <span class="badge bg-warning">Remain Coins - <?php echo @$package_sales['coins']; ?></span>  
                                                    </td>
                                                    </tr> --}}



                                                    <tr class="fw-bolder text-muted">
                                                        <th colspan="2" class="min-w-200px">Event</th>
                                                        <th class="min-w-150px">Deductions</th>
                                                        <th class="min-w-150px">Deduction Type</th>
                                                        <th class="min-w-150px">Minimum Coins Required</th>
                                                    </tr>

                                                    @foreach ($package_sales['coin_order_events'] as $package_value)
                                                        <tr>
                                                            <td colspan="2">
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_value->title }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_value->deductions }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_value->deduction_type }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_value->required_coin }}
                                                                </a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::invoices Content-->
                    <!--begin::transactionhistory Content-->
                    <div class="tab-pane" id="transactionhistory" role="tabpanel">
                        <div class="card-header cursor-pointer px-0" style="display: block;">
                            <div class="row">
                                <table
                                    class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer"
                                    id="myTable" role="grid" aria-describedby="myTable_info"
                                    style="width: 1270px;">
                                    <thead>
                                        <tr class="fw-bolder text-muted" role="row">
                                            <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 40px;">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input datatable_chk chk_list"
                                                        type="checkbox" data-kt-check="true"
                                                        data-kt-check-target="#myTable .form-check-input"
                                                        value="1">
                                                </div>
                                            </th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Order ID</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Package</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Credits</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Amount</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Transection</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Status</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Download</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @if ($cart_transaction_history)
                                            @foreach ($cart_transaction_history as $val)
                                                <tr role="row" class="odd">

                                                    <td>
                                                        <div
                                                            class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input chk_list_item "
                                                                name="chk_list_item[]" type="checkbox"
                                                                data-jobid="11" value="1015">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            @php
                                                                $number = $val->id;
                                                                $ordno = sprintf('%05d', $number);
                                                            @endphp
                                                            <a href="javascript:void(0);"
                                                                class="text-dark fw-bolder text-hover-primary fs-6">#{{ $ordno }}</a>
                                                            <span
                                                                class="fw-bold text-muted d-block fs-7">{{ $val->updated_at }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="javascript:void(0);"
                                                                class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->product_name }}</a>
                                                            <span class="fw-bold text-muted d-block fs-7">&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="javascript:void(0);"
                                                                class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->credit }}</a>
                                                            <span class="fw-bold text-muted d-block fs-7">Credit</span>
                                                        </div>
                                                    </td>
                                                    
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="javascript:void(0);"
                                                                class="text-dark fw-bolder text-hover-primary fs-6"><i
                                                                    class="fa fa-{{ $val->currency == 'INR' ? 'inr' : 'usd' }}"
                                                                    aria-hidden="true"></i>
                                                                {{ $val->total_price }}</a>
                                                            <span
                                                                class="fw-bold text-muted d-block fs-7">{{ $val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online' ? 'Online' : ucfirst($val->payment_gateway) }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($val->payment_mode === 'Cash')
                                                                {{ 'Cash' }}
                                                        @elseif($val->payment_mode === 'Cheque')
                                                        {{ ($val->chequenumber) ? $val->chequenumber : '-----------' }}
                                                        @elseif($val->payment_mode === 'Bank Transfer')
                                                        {{ ($val->transactionid) ? $val->transactionid :'-----------' }}
                                                        @elseif($val->payment_mode === 'Online')
                                                        {{ ($val->payment_Id) ? $val->payment_Id : '------------'}}
                                                        @elseif($val->payment_mode === 'Payment Link')
                                                        {{ ($val->payment_Id) ? $val->payment_Id : '------------'}}
                                                        @endif
                                                    </td>  
                                                    <td>
                                                        <div class="">
                                                            @php
                                                                if (
                                                                    $val->payment_mode != 'Payment Link' &&
                                                                    $val->status == 'created'
                                                                ) {
                                                                    $status_color = 'danger';
                                                                    $status_text = 'Failed';
                                                                } else {
                                                                    $status_get = GeneralMethod::paymentstatus($val->status,'razorpay');
                                                                    $status_color = @$status_get->status_color;
                                                                    $status_text = @$status_get->status_text;
                                                                }

                                                                $link_payments =	DB::table('link_payments')->select('*')->where('payment_link_id', @$val->payment_link_id)->orderByDesc('id')->first();


                                                            @endphp

                                                            <span
                                                                class="fw-bolder fs-6 text-{{ @$status_color }}">{{ @$status_text }}
                                                            </span>
                                                            <span
                                                                class="fw-bold text-muted d-block fs-7">{{ ($val->payment_mode === 'Online') ? 'Website' : $val->payment_mode }}</span>
                                                                @if($status_text==='Initiated')
                                                                <p><span class="paymentLinktextToCopy">{{ $link_payments->payment_link }}</span> <button class="copy-button" onclick="paymentLinkcopyText('{{$link_payments->payment_link}}')"><i class="fa fa-clipboard"></i></button></p>
                                                                @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ route('superadmin.company.receiptdownload', [encrypt($val->cart_id)]) }}?company_id={{ $val->company_id }}"
                                                                class="text-danger fw-bolder text-hover-primary fs-6">Download&nbsp;
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="18px" height="18px" viewBox="0 0 24 24"
                                                                    version="1.1" class="kt-svg-icon">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect id="bound" x="0" y="0"
                                                                            width="24" height="24" />
                                                                        <path
                                                                            d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z"
                                                                            id="Path-57" fill="red"
                                                                            fill-rule="nonzero" opacity="0.3" />
                                                                        <rect id="Rectangle" fill="red"
                                                                            opacity="0.3"
                                                                            transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) "
                                                                            x="11" y="1" width="2" height="14"
                                                                            rx="1" />
                                                                        <path
                                                                            d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z"
                                                                            id="Path-102" fill="#000000"
                                                                            fill-rule="nonzero"
                                                                            transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) " />
                                                                    </g>
                                                                </svg>
                                                            </a>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <!--end::transactionhistory Content-->
                    <!--begin::codeSetting Content-->
                    <div class="tab-pane" id="codeSetting" role="tabpanel">
                        <div class="card-header cursor-pointer px-0" style="display: block;">
                            <div class="row">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer"
                                    id="myTable" role="grid" aria-describedby="myTable_info"
                                    style="width: 1270px;">
                                    <thead>
                                        <tr class="fw-bolder text-muted" role="row">
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Purpose</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Login Url</th>
                                            <th class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        <tr role="row" class="odd">
                                            <td class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">Login
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;
                                            <td class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">
                                                <span id="copyText"
                                                    style="cursor: pointer; color: black; background: #f1e8e8; padding: 6px; border-radius: 8px;">Url
                                                    Copy</span>&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;
                                            </td>
                                            <td class="sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 103px;">

                                                <div class="d-flex flex-column">
                                                    {{ @$users->email }}
                                                    <span class="fw-bolder code_generator text-success fs-6"
                                                        id="row_{{ $users->id }}" data-id="{{ $users->id }}"
                                                        style="cursor: pointer;">Code Generate <i
                                                            class="fa fa-refresh"></i></span>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr role="row" class="odd">
                                            <td class="sorting_disabled" rowspan="1" colspan="1" style="width: 103px;">
                                                Company Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
                                            <td class="sorting_disabled" rowspan="1" colspan="1" style="width: 103px;">
                                                <span
                                                    class="checkActivetext text-{{ @$users->status === 'active' ? 'success' : 'danger' }}"
                                                    id="">{{ @$users->status === 'active' ? 'Active' : 'Suspend' }}</span>
                                            </td>
                                            <td class="sorting_disabled" rowspan="1" colspan="1" style="width: 103px;">

                                                <div
                                                    class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                    <input type="checkbox" class="js-switch form-check-input checkActive"
                                                        name="checkActive" @if (@$users->status === 'active') checked @endif
                                                        data-color="#99d683" data-size="small" style="width: 43px; height: 24px;" />
                                                </div>
                                                <div>
                                                    <span
                                                        class="checkActivetext text-{{ @$users->status === 'active' ? 'success' : 'danger' }}"
                                                        id="">{{ @$users->status === 'active' ? 'Active' : 'Suspend' }}</span>
                                                </div>

                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::codeSetting Content-->
                    <!--begin::migration Content-->
                    <div class="tab-pane" id="migration" role="tabpanel">
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->

                            <div class="card-body px-0 py-7">
                                <p><strong>Step 1.</strong> Kindly share your Google Sheet to this below ID<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        id="textToCopy">get-google-api-data@get-goolge-sheet.iam.gserviceaccount.com</span>
                                    <button class="copy-button" onclick="copyText()">Click to Copy Text</button></p>

                                <p><strong>Step 2.</strong> Find out your Google Sheet ID from URL and in between d and Edit
                                    </span>
                                    <img alt="Pic"
                                        src="{{ str_replace('index.php', '', url('public/tracker-teams-image.png')) }}">
                                </p>

                                <p><strong>Step 3.</strong> Arrange the google sheet in below format for Client and Candidate
                                    Data Migration </span><br>
                                    <a href="{{ str_replace('index.php', '', url('public/Book1-client.xlsx')) }}"
                                        download><button class="copy-button">Client Format <i
                                                class="fa fa-download"></i></button></a>
                                    <a href="{{ str_replace('index.php', '', url('public/Book1-candidate.xlsx')) }}"
                                        download><button class="copy-button">Candidate Format <i
                                                class="fa fa-download"></i></button></a>
                                    <a href="{{ str_replace('index.php', '', url('public/Book1-jobs.xlsx')) }}"
                                    download><button class="copy-button">Job Format <i
                                    class="fa fa-download"></i></button></a>
                                </p>

                                <div class="row">
                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                        <!--begin::Chart widget 8-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Header-->
                                            <div class="card-header"  style="padding: 6px;">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-gray-900">Client Migrations</span> 
                                                    <span class="card-label fw-bold text-gray-900"><a href="https://docs.google.com/spreadsheets/d/17oawy1WWLysg4M3IRLuSBArn2Rl_Igj18Jp2G9TztOg/edit?gid=0#gid=0" target="_blank"> Click To Open</a></span>
                                                </h3>
                                                <!--end::Title-->

                                            </div>

                                            <!--end::Header-->

                                            <!--begin::Body-->
                                            <div class="card-body" style="padding: 6px;">
                                                <!--begin::Tab content-->
                                                <div class="tab-content">
                                                    <!--begin::Tab pane-->
                                                    <div class="modal-body scroll-y" style="padding: 0px;">
                                                        <!--begin::Form-->
                                                        <form id="client_new_form" class="form" action="#">
                                                            <!--begin::Input group-->
                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <!--begin::Label-->
                                                                <label
                                                                    class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                                    <span class="required">Client Google Sheet Id</span>


                                                                    <span class="ms-1" data-bs-toggle="tooltip"
                                                                        title="Specify a card holder's name">
                                                                        <i
                                                                            class="ki-outline ki-information-5 text-gray-500 fs-6"></i></span>
                                                                </label>
                                                                <!--end::Label-->

                                                                <input type="text" class="form-control form-control-solid"
                                                                    placeholder="" name="googlesheetid" value="" />
                                                                <input type="hidden" value="{{ @$client->id }}"
                                                                    name="company_id">
                                                            </div>
                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <label
                                                                    class="required fs-6 fw-semibold form-label mb-2">Teams</label>
                                                                <select name="teams" class="form-select form-select-solid"
                                                                    data-control="select2" data-hide-search="true" required>
                                                                    <option value="">Select Team</option>
                                                                    @if (@$teams)
                                                                        @foreach (@$teams as $valuser)
                                                                            <option value="{{ $valuser->id }}">
                                                                                {{ $valuser->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>


                                                            <button id="successButtonclient" class="btn btn-success"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <button id="errorButtonclient" class="btn btn-danger"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <!--begin::Actions-->
                                                            <div class="text-center pt-15">


                                                                <button type="submit" id="client_new_submit"
                                                                    class="btn btn-primary">
                                                                    <span class="indicator-label indicator-label-client">
                                                                        Submit
                                                                    </span>
                                                                    <span class="indicator-progress indicator-progress-client">
                                                                        Please wait... <span
                                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                            <!--end::Actions-->
                                                        </form>
                                                        <!--end::Form-->
                                                    </div>
                                                </div>
                                                <!--end::Tab content-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Chart widget 8-->
                                    </div>

                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                    <!--begin::Chart widget 8-->
                                    <div class="card card-flush h-xl-100">
                                        <!--begin::Header-->
                                        <div class="card-header" style="padding: 6px;">
                                            <!--begin::Title-->
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-gray-900">Jobs Migrations</span>
                                                <span class="card-label fw-bold text-gray-900"><a href="https://docs.google.com/spreadsheets/d/1rQBpcFxDprxxr1wDG3fn7vhnUGgqZCqpe_gxK6fwgeo/edit?gid=0#gid=0" target="_blank"> Click To Open</a></span> 
                                            </h3>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div class="card-body" style="padding: 6px;">
                                            <!--begin::Tab content-->
                                            <div class="tab-content">

                                                <div class="modal-body scroll-y" style="padding: 0px;">
                                                    <!--begin::Form-->
                                                    <form id="candidate_new_form_job" class="form" action=""
                                                        method="POST">
                                                        <!--begin::Input group-->
                                                        <div class="d-flex flex-column mb-7 fv-row">
                                                            <!--begin::Label-->
                                                            <label
                                                                class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                                <span class="required">Jobs Google Sheet Id</span>


                                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                                    title="Specify a card holder's name">
                                                                    <i
                                                                        class="ki-outline ki-information-5 text-gray-500 fs-6"></i></span>
                                                            </label>
                                                            <!--end::Label-->

                                                            <input type="text" class="form-control form-control-solid"
                                                                placeholder="" name="googlesheetid" value=""
                                                                required />
                                                            <input type="hidden" value="{{ @$client->id }}"
                                                                name="company_id">
                                                        </div>
                                                        <div class="d-flex flex-column mb-7 fv-row">
                                                            <label
                                                                class="required fs-6 fw-semibold form-label mb-2">Clients</label>
                                                            <select name="client_id" id="clientID" class="form-select form-select-solid"
                                                                data-control="select2" data-hide-search="true" required>
                                                                <option value="">Select Client</option>
                                                                @if(@$clientlist)
                                                                    @foreach($clientlist as $val)  
                                                                        <option value="{{ $val->id }}"> {{ $val->name }}</option>
                                                                    @endforeach

                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="d-flex flex-column mb-7 fv-row">
                                                            <label
                                                                class="required fs-6 fw-semibold form-label mb-2">Hiring Manager/HR </label>
                                                            <select name="hiring_namager" id="hiring_namager" class="form-select form-select-solid"
                                                                data-control="select2" data-hide-search="true" required>
                                                                <option value="">Select Hiring Manager</option>
                                                                
                                                            </select>
                                                        </div>
                                                        <div class="d-flex flex-column mb-7 fv-row">
                                                            <label
                                                                class="required fs-6 fw-semibold form-label mb-2">Teams</label>
                                                            <select class="form-select form-select-solid"
                                                                arial-label="Select Currency" name="recruiter"
                                                                id="teamsId">
                                                                <option value="">Select Team</option>
                                                                @if (@$teams)
                                                                    @foreach (@$teams as $valuser)
                                                                        <option value="{{ $valuser->name }}">
                                                                            {{ $valuser->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>




                                                        <button id="successButtoncanJob" class="btn btn-success"
                                                            style="margin: 1px; width: 100%;"></button>
                                                        <button id="errorButtoncanJob" class="btn btn-danger"
                                                            style="margin: 1px; width: 100%;"></button>
                                                        <!--begin::Actions-->
                                                        <div class="text-center pt-15">

                                                            <button type="submit" id="candidate_new_submit_job"
                                                                class="btn btn-primary">
                                                                <span class="indicator-label-can-job indicator-label">
                                                                    Submit
                                                                </span>
                                                                <span class="indicator-progress-can-job indicator-progress">
                                                                    Please wait... <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                </span>
                                                            </button>

                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>


                                            </div>
                                            <!--end::Tab content-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Chart widget 8-->
                                    </div>

                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                        <!--begin::Chart widget 8-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Header-->
                                            <div class="card-header"  style="padding: 6px;">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-gray-900">Candidate Migrations</span>
                                                    <span class="card-label fw-bold text-gray-900"><a href="https://docs.google.com/spreadsheets/d/1oMkFyIZKX56IWGi0-4kB3om0RPib3NnwLyQcjaIUabs/edit?gid=0#gid=0" target="_blank"> Click To Open</a></span> 
                                                </h3>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Body-->
                                            <div class="card-body" style="padding: 6px;">
                                                <!--begin::Tab content-->
                                                <div class="tab-content">

                                                    <div class="modal-body scroll-y" style="padding: 0px;">
                                                        <!--begin::Form-->
                                                        <form id="candidate_new_form" class="form" action=""
                                                            method="POST">
                                                            <!--begin::Input group-->
                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <!--begin::Label-->
                                                                <label
                                                                    class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                                    <span class="required">Candidate Google Sheet Id</span>


                                                                    <span class="ms-1" data-bs-toggle="tooltip"
                                                                        title="Specify a card holder's name">
                                                                        <i
                                                                            class="ki-outline ki-information-5 text-gray-500 fs-6"></i></span>
                                                                </label>
                                                                <!--end::Label-->

                                                                <input type="text" class="form-control form-control-solid"
                                                                    placeholder="" name="googlesheetid" value=""
                                                                    required />
                                                                <input type="hidden" value="{{ @$client->id }}"
                                                                    name="company_id">
                                                            </div>
                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <label
                                                                    class="required fs-6 fw-semibold form-label mb-2">Jobs</label>
                                                                <select name="job_id" class="form-select form-select-solid"
                                                                    data-control="select2" data-hide-search="true" required>
                                                                    <option value="">Select Job</option>
                                                                    @if (@$jobsforcandidate)
                                                                        @foreach (@$jobsforcandidate as $val_job)
                                                                            <option value="{{ $val_job->id }}">
                                                                                J{{ $val_job->id }}, {{ $val_job->title }}
                                                                                ({{ $val_job->clients_name }})</option>
                                                                        @endforeach

                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <label
                                                                    class="required fs-6 fw-semibold form-label mb-2">Teams</label>
                                                                <select name="teams" class="form-select form-select-solid"
                                                                    data-control="select2" data-hide-search="true" required>
                                                                    <option value="">Select Team</option>
                                                                    @if (@$teams)
                                                                        @foreach (@$teams as $valuser)
                                                                            <option value="{{ $valuser->id }}">
                                                                                {{ $valuser->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <label
                                                                    class="required fs-6 fw-semibold form-label mb-2">Currency</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" id="current_ctc_cur"
                                                                    name="current_ctc_cur">
                                                                    <option value="0" selected="selected">INR</option>
                                                                    <option value="1">EUR</option>
                                                                    <option value="2">JPY</option>
                                                                    <option value="3">GBP</option>
                                                                    <option value="4">USD</option>
                                                                </select>
                                                            </div>

                                                            <div class="d-flex flex-column mb-7 fv-row">
                                                                <label
                                                                    class="required fs-6 fw-semibold form-label mb-2">Currency
                                                                    Mode</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="current_ctc_mode"
                                                                    id="current_ctc_mode">
                                                                    <option value="0" selected="selected">Annual</option>
                                                                    <option value="1">Monthly</option>
                                                                    <option value="2">Hourly</option>
                                                                </select>
                                                            </div>




                                                            <button id="successButtoncan" class="btn btn-success"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <button id="errorButtoncan" class="btn btn-danger"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <!--begin::Actions-->
                                                            <div class="text-center pt-15">

                                                                <button type="submit" id="candidate_new_submit"
                                                                    class="btn btn-primary">
                                                                    <span class="indicator-label-can indicator-label">
                                                                        Submit
                                                                    </span>
                                                                    <span class="indicator-progress-can indicator-progress">
                                                                        Please wait... <span
                                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                    </span>
                                                                </button>

                                                            </div>
                                                            <!--end::Actions-->
                                                        </form>
                                                        <!--end::Form-->
                                                    </div>


                                                </div>
                                                <!--end::Tab content-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Chart widget 8-->
                                    </div>

                                </div>


                            </div>

                        </div>
                    </div>
                    <!--end::migration Content-->
                    <!--begin::migration Content-->
                    <div class="tab-pane" id="topupcoins" role="tabpanel">
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->

                            <div class="card-body px-0 py-7">

                                <div class="row">
                                    <div class="col-xxl-12 mb-5 mb-xl-10">
                                        <!--begin::Chart widget 8-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-gray-900">Top-Up Operational
                                                        Coins</span>
                                                </h3>
                                                <!--end::Title-->

                                            </div>

                                            <!--end::Header-->

                                            <!--begin::Body-->
                                            <div class="card-body pt-6">
                                                <!--begin::Tab content-->
                                                <div class="tab-content">
                                                    <!--begin::Tab pane-->
                                                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                        <!--begin::Form-->
                                                        <form id="operational_top_up_new_form" class="form" action="#">
                                                            <!--begin::Input group-->

                                                            <input type="hidden" name="paymentLinkPaymentstatus" id="paymentLinkPaymentstatus" value="" />
                                                            <input type="hidden" name="paymentLinkpayment_orderId" id="paymentLinkpayment_orderId" value="" />
                                                            <input type="hidden" name="paymentLinkshort_url" id="paymentLinkshort_url" value="" />


                                                            <div class="row">
                                                                <div class="col-xxl-4 mb-5 mb-xl-10">
                                                                    <label
                                                                        class="required fs-6 fw-semibold form-label mb-2">Currency</label>
                                                                    <select name="currency" id="currency_get"
                                                                        class="form-select form-select-solid"
                                                                        data-control="select2" data-hide-search="true"
                                                                        required>
                                                                        <option value="INR"><i class="fa fa-inr"
                                                                                aria-hidden="true"></i> INR</option>
                                                                        <option value="USD"><i class="fa fa-usd"
                                                                                aria-hidden="true"></i> USD</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xxl-2 mb-5 mb-xl-10">
                                                                    <label
                                                                        class="required fs-6 fw-semibold form-label mb-2">GST</label>
                                                                    <select name="gst_on" id="gst_on_get"
                                                                        class="form-select form-select-solid"
                                                                        data-control="select2" data-hide-search="true"
                                                                        required>
                                                                        <option value="Yes">Yes</option>
                                                                        <option value="No">No</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                    <label
                                                                        class="required fs-6 fw-semibold form-label mb-2">Top-up
                                                                        Type</label>
                                                                    <select name="topUptype" id="topUptype"
                                                                        class="form-select form-select-solid"
                                                                        data-control="select2" data-hide-search="true"
                                                                        required>
                                                                        <option value="">Select Top-up Type</option>
                                                                        <option value="operational">Operational</option>
                                                                        <option value="sales">Sales</option>
                                                                    </select>
                                                                </div>
                                                            </div>


                                                            <div class="d-flex flex-column mb-8">
                                                                <label class="form-label required">Amount</label>
                                                                <div class="row py-5 mb-3" id="currencyPrice">

                                                                </div>
                                                            </div>




                                                            <div class="row">


                                                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                    <label
                                                                        class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                                        <span class="required">Coins</span>


                                                                        <span class="ms-1" data-bs-toggle="tooltip"
                                                                            title="Specify a card holder's name">
                                                                            <i
                                                                                class="ki-outline ki-information-5 text-gray-500 fs-6"></i></span>
                                                                    </label>
                                                                    <!--end::Label-->

                                                                    <input type="text"
                                                                        class="form-control form-control-solid coins_get"
                                                                        placeholder="" name="coins" value="" />

                                                                </div>
                                                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                    <!--begin::Label-->
                                                                    <label
                                                                        class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                                        <span class="required">Price</span>


                                                                        <span class="ms-1" data-bs-toggle="tooltip"
                                                                            title="Specify a card holder's name">
                                                                            <i
                                                                                class="ki-outline ki-information-5 text-gray-500 fs-6"></i></span>
                                                                    </label>
                                                                    <!--end::Label-->

                                                                    <input type="text"
                                                                        class="form-control form-control-solid price_get"
                                                                        placeholder="" name="price" value="" />
                                                                    <input type="hidden" value="{{ @$client->id }}"
                                                                        name="company_id" id="company_id_topup">


                                                                    {{-- <input type="hidden" value="" class="gsttax_hidden_value" name="gst"> --}}

                                                                    <div class="d-flex justify-content-end">
                                                                        <!--begin::Section-->
                                                                        <div class="mw-300px">
                                                                            
                                                                            <br>

                                                                            <div class="d-flex flex-stack mb-3">
                                                                                <div class="fw-semibold pe-10 text-gray-600 fs-7"
                                                                                    style="display:contents;">
                                                                                    <label class="form-label"
                                                                                        style="padding-top:10px;">Discount(%)
                                                                                    </label>

                                                                                    <input type="number"
                                                                                        class="form-control form-control-solid"
                                                                                        name="discount" id="discount"
                                                                                        value="" placeholder="0.00"
                                                                                        style="width: 190px;">


                                                                                </div>

                                                                            </div>

                                                                            <!--begin::Item-->
                                                                            <div class="d-flex flex-stack mb-3">
                                                                                <!--begin::Accountname-->
                                                                                <div class="fw-semibold pe-10 text-gray-600 fs-7"
                                                                                    style="display:contents;">
                                                                                    <label class="form-label"
                                                                                        style="padding-top:10px;">GST(%):
                                                                                    </label>
                                                                                    <input type="number"
                                                                                        class="form-control form-control-solid gsttax_hidden_value"
                                                                                        name="gst" id="gst"
                                                                                        value="" placeholder="0.00"
                                                                                        style="width: 190px;">
                                                                                    <input type="hidden" value=""
                                                                                        class="gst_amount_hidden_value"
                                                                                        name="gst_amount">
                                                                                </div>
                                                                                <!--end::Accountname-->

                                                                                
                                                                            </div>
                                                                            <!--end::Item-->


                                                                            <!--begin::Item-->
                                                                            <div class="d-flex flex-stack mb-3">
                                                                                <!--begin::Code-->
                                                                                <div class="fw-semibold pe-10 text-gray-600 fs-7"
                                                                                    style="display:contents;">
                                                                                    <label class="form-label"
                                                                                        style="padding-top:10px;"> Total
                                                                                    </label>

                                                                                    <input type="text" value=""
                                                                                        class="form-control form-control-solid total_price_hidden_value"
                                                                                        name="total_price"
                                                                                        style="width: 190px;">
                                                                                </div>
                                                                                <!--end::Code-->

                                                                                
                                                                            </div>
                                                                            <!--end::Item-->
                                                                        </div>
                                                                        <!--end::Section-->
                                                                    </div>


                                                                </div>

                                                            </div>


                                                            {{-- ////////////////////////////////// --}}

                                                            <div class="row">
                                                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                    <select id="payment-mode-selector"
                                                                        name="paymentmodeselector"
                                                                        class="form-select form-select-solid">
                                                                        <option value="">Select Payment Mode</option>
                                                                        <option value="Cash">Cash</option>
                                                                        <option value="Cheque">Cheque</option>
                                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                                        <option value="Payment Link">Payment Link</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                    <select id="payment-method-selector"
                                                                        name="paymentmethodselector"
                                                                        class="form-select form-select-solid">

                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="cash" class="payment-method">
                                                                <p>Please provide the cash amount directly to our
                                                                    representative.</p>
                                                            </div>

                                                            <!-- Cheque Payment -->
                                                            <div id="cheque" class="payment-method">
                                                                <p>
                                                                    Please issue a cheque in favor of <strong>Company
                                                                        Name</strong> and provide the following details:
                                                                </p>
                                                                <div class="row">
                                                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                                                        <label for="cheque-number">Cheque Number:</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-solid"
                                                                            id="cheque-number" name="chequenumber">
                                                                    </div>
                                                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                                                        <label for="cheque-date">Cheque Date:</label>
                                                                        <input type="date"
                                                                            class="form-control form-control-solid"
                                                                            id="cheque-date" name="chequedate">
                                                                    </div>
                                                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                                                        <label for="bank-name">Bank Name:</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-solid"
                                                                            id="bank-name" name="bankname">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Bank Transfer -->
                                                            <div id="bank_transfer" class="payment-method">
                                                                <p>
                                                                    After completing the transfer, please provide the
                                                                    transaction ID and the bank reference number below.
                                                                </p>
                                                                <div class="row">
                                                                    <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                        <label for="transaction-id">Transaction ID:</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-solid"
                                                                            id="transaction-id" name="transactionid">
                                                                    </div>
                                                                    <div class="col-xxl-6 mb-5 mb-xl-10">
                                                                        <label for="bank-reference">Bank Reference
                                                                            Number:</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-solid"
                                                                            id="bank-reference" name="bankreference">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Online Payment -->
                                                            <div id="online" class="payment-method">
                                                                <p>Please click the button below to proceed to our secure online
                                                                    payment gateway. <button type="button" class="paymentLinkGenerate" onclick="paymentLinkGenerate()">Payment Link Generate</button></p>
                                                            </div>
                                                            <div class="paymentLinkshow" style="display:none">
                                                                <label class="form-label">Payment Link</label>
                                                                <input type="text" class="form-control form-control-solid" id="paymentLinkshow" value="" readonly/>
                                                            </div>

                                                            {{-- //////////////////////////////////////////// --}}


                                                            <div class="row">
                                                                <div class="col-sm-9">
                                                                    <div class="fw-bold fs-6 text-gray-800">Billing Details</div>
                                                                    <p><span id="usernamebill">{{ @$user_name }}</span><p>
                                                                    <p>{{ @$company_name }}
                                                                    <p>
                                                                    <p><span id="billing_address">{{ @$locArr }}</span> <p>
                                                                    <p><span id="billing_gst">{{ @$gstin }}</span> <p>
                                                                    <p><span id="billing_pan">{{ @$pan_no }}</span> <p>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <a href="javascript:;" data-id="{{ $client->company_id }}" class="btn btn-primary show-billing-address"
                                                                        id="" style="float: right;">Edit Details</a>
                                                                </div>
                                                            </div>




                                                            <button id="successButtonoperationaltopup" class="btn btn-success"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <button id="errorButtonoperationaltopup" class="btn btn-danger"
                                                                style="margin: 1px; width: 100%;"></button>
                                                            <!--begin::Actions-->
                                                            <div class="text-center pt-15">


                                                                <button type="submit" id="operational_top_up_new_submit"
                                                                    class="btn btn-primary">
                                                                    <span class="indicator-label indicator-label-top-up">
                                                                        Submit
                                                                    </span>
                                                                    <span class="indicator-progress indicator-progress-top-up">
                                                                        Please wait... <span
                                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                            <!--end::Actions-->
                                                        </form>
                                                        <!--end::Form-->
                                                    </div>
                                                </div>
                                                <!--end::Tab content-->
                                                @if ($link_payments)
                                                    <p><strong>1.</strong>Pending Payment<br>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                                            id="paymentLinktextToCopy">{{ $link_payments->payment_link }}</span>
                                                        <button class="copy-button" onclick="paymentLinkcopyText('{{ $link_payments->payment_link}}')">Click to
                                                            Copy Link</button></p>
                                                @endif

                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Chart widget 8-->
                                    </div>

                                </div>


                            </div>

                        </div>
                    </div>
                    <!--end::migration Content-->
                    <!--begin::packages Content-->
                    <div class="tab-pane" id="packages" role="tabpanel">
                        <!--begin::Contacts header-->
                        <div class="card-header cursor-pointer px-0" style="display: block;">

                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="">


                                <thead id="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="min-w-200px">Package</th>
                                        <th class="min-w-150px">Duration</th>
                                        <th class="min-w-150px">Total Coins</th>
                                        <th class="min-w-150px">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @php

                                        $coin_packages_free = DB::table('coin_packages')
                                            ->join(
                                                'coin_package_types',
                                                'coin_package_types.id',
                                                '=',
                                                'coin_packages.coin_package_type_id',
                                            )
                                            ->join('coin_orders', 'coin_orders.coin_package_id', '=', 'coin_packages.id')
                                            ->join('companies', 'companies.id', '=', 'coin_orders.company_id')
                                            ->join('company_coins', 'company_coins.company_id', '=', 'companies.id')
                                            ->select(
                                                'coin_packages.*',
                                                'coin_package_types.title',
                                                'company_coins.free_pack_status',
                                            )
                                            ->where('coin_packages.custome_pck', 2)
                                            ->where('coin_packages.event_type', '=', 'operational')
                                            ->orderByDesc('coin_orders.id')
                                            ->take(1)
                                            ->get();
                                    @endphp
                                    @if ($coin_packages_free)
                                        @foreach ($coin_packages_free as $packages_free)
                                            <tr class="fw-bolder text-muted">
                                                <td class="min-w-200px">{{ $packages_free->title }}</td>
                                                <td class="min-w-150px">
                                                    {{ $packages_free->days ? $packages_free->days : 0 }} Days</td>
                                                <td class="min-w-150px">{{ $packages_free->coins }}</td>
                                                <td class="min-w-150px">
                                                    <div class="col-sm-6 col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-8"></label>

                                                            <div class="col-sm-4">
                                                                <div
                                                                    class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                                    <input type="checkbox"
                                                                        class="js-switch form-check-input checkStatusOperational"
                                                                        name="checkStatusOperational"
                                                                        @if (@$packages_free->free_pack_status == 'active') checked @endif
                                                                        data-color="#99d683" data-size="small" />
                                                                </div>
                                                                <a href="javascript:void(0);"
                                                                    class="text-muted fw-bold d-block fs-7 checkStatusOperational_text">{{ @$packages_free->free_pack_status == 'active' ? 'Active' : 'Inactive' }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>


                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">
                                    Current Package
                                </h3>

                                <a href="<?php echo url('super-admin/custompackages/create/' . @$client->id); ?>?packageType={{ encrypt('operational') }}"
                                    class="btn btn-primary align-self-center" style="position: absolute; right: 18px;">New
                                    Package</a>
                            </div>

                            <div class="card-body px-0 py-7">
                                <div class="table-responsive">

                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                        id="">

                                        <tbody id="">
                                            @if (@$company_coins)

                                                @foreach ($company_coins as $package)
                                                    <tr class="fw-bolder text-muted">
                                                        <th class="min-w-200px">Package</th>
                                                        <th class="min-w-200px">Status</th>
                                                        <th class="min-w-150px">Duration</th>
                                                        <th class="min-w-150px">Total Coins</th>
                                                        <th class="min-w-150px">Remaining Coins</th>
                                                    </tr>


                                                    <tr class="fw-bolder text-muted">
                                                        <td class="text-dark fs-6"><?php echo @$package['title'] ? @$package['title'] : ''; ?></td>
                                                        <td class="text-dark fs-6">
                                                            @if (strtotime(@$package['end_date']) >= strtotime(date('Y-m-d')) && @$package['status'] == 'valid')
                                                                Active
                                                            @else
                                                                Expired
                                                            @endif
                                                        </td>
                                                        <td class="text-dark fs-6"><?php echo @$package['start_date'] ? date('d-M-Y', strtotime(@$package['start_date'])) : ''; ?> to
                                                            <?php echo @$package['end_date'] ? date('d-M-Y', strtotime(@$package['end_date'])) : ''; ?></td>
                                                        <td class="text-dark fs-6"><?php echo @$package['order_coins']; ?></td>
                                                        <td class="text-dark fs-6"><?php echo @$package['coins']; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="5">
                                                            <h3 class="fw-bolder m-0">
                                                                Credit Usage Overview per Activity
                                                            </h3>
                                                        </td>
                                                    </tr>


                                                    {{-- <tr>
                                        <td colspan="4">
                                        <span class="badge bg-primary"><?php echo @$package['title'] ? @$package['title'] : ''; ?></span>
                                        <span class="badge bg-info"><?php echo @$package['start_date'] ? date('d-M-Y', strtotime(@$package['start_date'])) : ''; ?> to <?php echo @$package['end_date'] ? date('d-M-Y', strtotime(@$package['end_date'])) : ''; ?></span>
                                        <span class="badge bg-success">Total Coins - <?php echo @$package['order_coins']; ?></span>
                                        <span class="badge bg-warning">Remain Coins - <?php echo @$package['coins']; ?></span>  
                                        </td>
                                    </tr> --}}

                                                    <tr class="fw-bolder text-muted">
                                                        <th colspan="2" class="min-w-200px">Event</th>
                                                        <th class="min-w-150px">Deductions</th>
                                                        <th class="min-w-150px">Deduction Type</th>
                                                        <th class="min-w-150px">Minimum Coins Required</th>
                                                    </tr>

                                                    @foreach ($package['coin_order_events'] as $package_active)
                                                        <tr>
                                                            <td colspan="2">
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_active->title }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_active->deductions }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_active->deduction_type }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    {{ @$package_active->required_coin }}
                                                                </a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Card title-->
                            <!--begin::Action-->
                            <!--<a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Contact</a>-->
                            <!--end::Action-->
                        </div>
                        <!--end::Contacts header-->
                        <!--begin::Contacts Content-->

                        <!--end::Contacts Content-->
                    </div>
                    <!--end::packages Content-->
                    <!--begin::Requisitions Content-->
                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <!--begin::Sub tabs-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">All Jobs</h3>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Actions-->
                            <div class="d-flex align-items-center py-1">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1 me-3">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" id="inputJobSearch" name="inputJobSearch"
                                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                                        placeholder="Search..." />
                                </div>
                                <!--end::Search-->
                                <div class="card-toolbar">
                                    <!--begin::Toolbar-->
                                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                        @if (in_array('add_jobs', $userPermissions))
                                            <!--begin::Add Requisitions-->
                                            {{-- echo url('service_company/jobs/create'); --}}
                                            <a href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}">
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                                <path
                                                                    d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                    fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                <path
                                                                    d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                    fill="#000000" fill-rule="nonzero" />
                                                            </g>
                                                        </svg>
                                                    </span> Add Job</button>
                                            </a>
                                            <!--end::Add Requisitions-->
                                        @endif
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                            </div>
                            <!--end::Actions-->
                        </div>

                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
                            {{-- <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all-requisitions">All Req.</a> </li> --}}
                            {{-- <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li> --}}
                        </ul>
                        <!--end::Sub tabs-->
                        <!--begin::Requisitions Content-->
                        <div class="tab-content" id="myTabContent">
                            <!--begin::All Req.-->
                            <div class="tab-pane fade show active" id="all-requisitions" role="tabpanel">
                                <!--begin::All Req. header-->
                                <!--end::All Req. header-->
                                <!--begin::All Req. Content-->
                                <div class="card-body px-0 py-7">
                                    {{-- @if (in_array('view_jobs', $userPermissions)) --}}
                                    <div class="loadingMessage loadingMessagejobListAjax" style="display: none; position: absolute;">Processing...</div>
                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="allreq">
                                        <thead>
                                            <tr class="fw-bolder text-muted">
                                                <th class="w-10px pe-0">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_table_users .form-check-input1"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="">Jobs</th>
                                                <th class="">Exp.</th>
                                                <th class="">Salary</th>
                                                <th class="">Location</th>
                                                <th class="">Status</th>
                                                <th class="">Date</th>
                                                <th class="">HM | CP</th>
                                                <th class="">S.Partners</th>
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
                                        <tbody class="text-gray-600 fw-bold" id="jobListAjax">
                                       </tbody>
                                    </table>
                                    <div id="paginationJobs"></div>
                                    {{-- @endif  --}}
                                </div>
                                <!--end::All Req. Content-->
                            </div>
                            <!--end::All Req.-->
                            <!--end::Client Req.-->
                            <div class="tab-pane fade" id="client-requisitions" role="tabpanel">
                                <!--begin::Client Req. header-->
                                <div class="card-header cursor-pointer px-0">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bolder m-0">HCL Requisitions</h3>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Client Req. header-->
                                <!--begin::Client Req. Content-->
                                <div class="card-body px-0 py-7">
                                    <!--begin:Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="fw-bolder text-muted">
                                                <th class="w-10px pe-0">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_table_users .form-check-input2"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="">Requisition</th>
                                                <th class="">Exp.</th>
                                                <th class="">Salary</th>
                                                <th class="">Location</th>
                                                <th class="">Status</th>
                                                <th class="">Date</th>
                                                <th class="">HM | CP</th>
                                                <th class="">S.Partners</th>
                                                <th class="">TOT</th>
                                                <th class="">SOU</th>
                                                <th class="">SCR</th>
                                                <th class="">EVA</th>
                                                <th class="">NEO</th>
                                                <th class="">OFF</th>
                                                <th class="">JOI</th>
                                                <th class="">REJ</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">
                                            <!--begin::Table row-->
                                            <tr class="">
                                                <!--begin::Checkbox-->
                                                <td class="">
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input form-check-input2" type="checkbox"
                                                            value="1" />
                                                    </div>
                                                </td>
                                                <!--end::Checkbox-->
                                                <!--begin::Requisitions=-->
                                                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                    <div
                                                        class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                        <a href="javascript:void(0);">
                                                            <div
                                                                class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                                U</div>
                                                        </a> </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::User details-->
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">UI/UX
                                                            Designer</a> <a href="javascript:void(0);"
                                                            class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
                                                    </div>
                                                    <!--begin::User details-->
                                                </td>
                                                <!--end::Requisitions-->
                                                <!--begin::Exp.-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <span
                                                            class="text-dark fw-bolder fs-6">22+</span> <span
                                                            class="text-muted fw-bold text-muted d-block fs-7">18+</span>
                                                    </div>
                                                </td>
                                                <!--end::Exp.-->
                                                <!--begin::Salary-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <span
                                                            class="text-dark fw-bolder fs-6">50 LPA</span> <span
                                                            class="fw-bold d-block fs-7">30 LPA</span> </div>
                                                </td>
                                                <!--end::Salary-->
                                                <!--begin::Location-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <span
                                                            class="text-dark fw-bolder fs-6">Gurgaon</span>
                                                        <div class="d-flex align-items-center"> <span
                                                                class="fw-bold text-muted d-block fs-7">Haryana</span>
                                                            &nbsp;|&nbsp; <span
                                                                class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                            <div> </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!--end::Location-->
                                                <!--begin::Status | Requisition ID-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="fs-6 hold-bg text-hover-warning">Hold</a> <span
                                                            class="fw-bold text-muted d-block fs-7">R2</span> </div>
                                                </td>
                                                <!--end::Status | Requisition ID-->
                                                <!--begin::Date | Openings-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <span
                                                            class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span
                                                            class="text-muted fw-bold d-block fs-7">774</span> </div>
                                                </td>
                                                <!--end::Date | Openings-->
                                                <!--begin::Hiring Manager | Contact Person-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                        <div class="symbol-group symbol-hover">
                                                            <div class="symbol symbol-30 symbol-circle"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Pawan"> <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="" data-original-title="More users list..">
                                                                <span class="symbol-label font-weight-bold">1+</span> </div>
                                                        </div>
                                                    </div>
                                </div>

                                </td>

                                <!--end::Hiring Manager | Contact Person-->
                                <!--begin::Staffing Partners-->
                                <td class="">
                                    <div class="d-flex flex-column">
                                        <div class="symbol-group symbol-hover">
                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="" data-original-title="Pradeep"> <img
                                                    alt="Pic"
                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg">
                                            </div>
                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="" data-original-title="Abinesh"> <img
                                                    alt="Pic"
                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg">
                                            </div>
                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-original-title="More users list.."> <span
                                                    class="symbol-label font-weight-bold">2+</span> </div>
                                        </div>
                                    </div>
                                </td>
                                <!--end::Staffing Partners-->
                                <!--begin::TOT-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a
                                            href="javascript:void(0);"
                                            class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a> </div>
                                </td>
                                <!--end::TOT-->
                                <!--begin::SOU-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                </td>
                                <!--end::SOU-->
                                <!--begin::SCR-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">23</a> </div>
                                </td>
                                <!--end::SCR-->
                                <!--begin::EVA-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                </td>
                                <!--end::EVA-->
                                <!--begin::NEO-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">24</a> </div>
                                </td>
                                <!--end::NEO-->
                                <!--begin::OFF-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">83</a> </div>
                                </td>
                                <!--end::OFF-->
                                <!--begin::JOI-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">49</a> </div>
                                </td>
                                <!--end::JOI-->
                                <!--begin::REJ-->
                                <td class="">
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                </td>
                                <!--end::REJ-->
                                </tr>
                                <!--end::Table row-->
                                </tbody>

                                <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Client Req. Content-->
                        </div>
                            <!--end::Client Req.-->
                            <!--begin::Custom Req.-->
                        <div class="tab-pane fade" id="custom-requisitions" role="tabpanel">
                            <!--begin::Custom Req. header-->
                            <div class="card-header cursor-pointer px-0">
                                <!--begin::Card title-->
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Custom Requisitions</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Actions-->
                                <div class="d-flex align-items-center py-1">
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                            <!--begin::Add Requisitions-->
                                            <a href="{{ url('service_company/jobs/create') }}">
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                    <!--begin::Svg Icon-->
                                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                                <path
                                                                    d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                    fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                <path
                                                                    d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                    fill="#000000" fill-rule="nonzero" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    Add Requisitions </button>
                                            </a>
                                            <!--end::Add Requisitions-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Custom Req. header-->
                            <!--begin::Custom Req. Content-->
                            <div class="card-body px-0 py-7">
                                <!--begin:Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input3"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Requisition</th>
                                            <th class="">Exp.</th>
                                            <th class="">Salary</th>
                                            <th class="">Location</th>
                                            <th class="">Status</th>
                                            <th class="">Date</th>
                                            <th class="">HM | CP</th>
                                            <th class="">S.Partners</th>
                                            <th class="">TOT</th>
                                            <th class="">SOU</th>
                                            <th class="">SCR</th>
                                            <th class="">EVA</th>
                                            <th class="">NEO</th>
                                            <th class="">OFF</th>
                                            <th class="">JOI</th>
                                            <th class="">REJ</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="text-gray-600 fw-bold">
                                        <!--begin::Table row-->
                                        <tr class="">
                                            <!--begin::Checkbox-->
                                            <td class="">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input form-check-input3" type="checkbox"
                                                        value="1" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
                                            <!--begin::Requisitions=-->
                                            <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label border-radius-5"> <img
                                                                src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3"
                                                                alt="Company Logo" class="border-radius-5 w-100"> </div>
                                                    </a> </div>
                                                <!--end::Avatar-->
                                                <!--begin::User details-->
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary fs-6">Frontend
                                                        Developer</a> <a href="javascript:void(0);"
                                                        class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl
                                                        Technologies</a> </div>
                                                <!--begin::User details-->
                                            </td>
                                            <!--end::Requisitions-->
                                            <!--begin::Exp.-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">30+</span> <span
                                                        class="text-muted fw-bold text-muted d-block fs-7">27+</span> 
                                                        
                                                </div>
                                            </td>
                                            <!--end::Exp.-->
                                            <!--begin::Salary-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70
                                                        LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> 
                                                </div>
                                            </td>
                                            <!--end::Salary-->
                                            <!--begin::Location-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Noida</span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp;
                                                        <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Location-->
                                            <!--begin::Status | Requisition ID-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="fs-6 closed-bg text-hover-danger">Closed</a> <span
                                                        class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div>
                                            </td>
                                            <!--end::Status | Requisition ID-->
                                            <!--begin::Date | Openings-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span
                                                        class="text-muted fw-bold d-block fs-7">33</span> </div>
                                            </td>
                                            <!--end::Date | Openings-->
                                            <!--begin::Hiring Manager | Contact Person-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Priyanka"> <img alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list.."> <span
                                                                class="symbol-label font-weight-bold">1+</span> </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Hiring Manager | Contact Person-->
                                            <!--begin::Staffing Partners-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title=""
                                                            data-original-title="One"> <img alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title=""
                                                            data-original-title="Two"> <img alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list.."> <span
                                                                class="symbol-label font-weight-bold">2+</span> </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Staffing Partners-->
                                            <!--begin::TOT-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a
                                                        href="javascript:void(0);"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a>
                                                </div>
                                            </td>
                                            <!--end::TOT-->
                                            <!--begin::SOU-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">345</a> </div>
                                            </td>
                                            <!--end::SOU-->
                                            <!--begin::SCR-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                            </td>
                                            <!--end::SCR-->
                                            <!--begin::EVA-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">94</a> </div>
                                            </td>
                                            <!--end::EVA-->
                                            <!--begin::NEO-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">03</a> </div>
                                            </td>
                                            <!--end::NEO-->
                                            <!--begin::OFF-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                            </td>
                                            <!--end::OFF-->
                                            <!--begin::JOI-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">45</a> </div>
                                            </td>
                                            <!--end::JOI-->
                                            <!--begin::REJ-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">540</a> </div>
                                            </td>
                                            <!--end::REJ-->
                                        </tr>
                                        <!--end::Table row-->
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Custom Req. Content-->
                        </div>
                        <!--end::Custom Req.-->
                        <div class="tab-pane fade" id="jobs" role="tabpanel">Jobs - Content</div>
                    </div>
                        <!--end::Requisitions Content-->
                    </div>
                    <!--begin::Requisitions Content-->
                    <!--begin::timeline Content-->
                    <div class="tab-pane" id="timeline" role="tabpanel">
                        {{-- <div class="card-header cursor-pointer px-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Timeline</h3>
                        </div>
                        </div> --}}
                        <div class="card-body px-0 py-7">

                            <div class="card card-custom mx-2">
                                <div class="card-header card-header-tabs-line">
                                    <div class="card-toolbar">
                                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x"
                                            role="tablist">
                                            <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab"
                                                    href="#salestab"> <span class="nav-text font-weight-bold">Operational
                                                        Package</span> </a> </li>
                                            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab"
                                                    href="#operationaltab"> <span class="nav-text font-weight-bold">Sales
                                                        Package</span> </a> </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-body py-0">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="salestab" role="tabpanel">
                                            <div class="card-body px-0 py-7">
                                                <div class="timeline">
                                                    @if (@$coin_transection_logs)

                                                        @foreach ($coin_transection_logs as $transection_log)
                                                            <div class="timeline-item">

                                                                <!--begin::Timeline line-->
                                                                <div class="timeline-line w-40px"></div>
                                                                <!--end::Timeline line-->
                                                                <!--begin::Timeline icon-->
                                                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                                    <div class="symbol-label bg-light">
                                                                        <!--begin::Svg Icon-->
                                                                        <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="24px" height="24px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <polygon points="0 0 24 0 24 24 0 24">
                                                                                    </polygon>
                                                                                    <path
                                                                                        d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                        fill="#000000" fill-rule="nonzero"
                                                                                        opacity="0.3">
                                                                                    </path>
                                                                                    <path
                                                                                        d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                        fill="#000000" fill-rule="nonzero">
                                                                                    </path>
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </div>
                                                                </div>
                                                                <!--end::Timeline icon-->
                                                                <!--begin::Timeline content-->
                                                                <div class="timeline-content mb-10 mt-n1">

                                                                    <!--begin::Table-->
                                                                    <table
                                                                        class="table align-middle table-row-dashed fs-6 gy-0 mb-5"
                                                                        id="">
                                                                        <!--begin::Table body-->
                                                                        <tbody class="text-gray-600 fw-bold">
                                                                            <!--begin::Table row-->
                                                                            <tr>
                                                                                <!--begin::Date-->
                                                                                <td class="min-w-110px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder fs-6">
                                                                                            {{ date('d M Y', strtotime($transection_log->created_at)) }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="text-muted fw-bold d-block fs-7">
                                                                                            {{ date('h:i:A', strtotime($transection_log->created_at)) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Date-->

                                                                                <td class="min-w-285px" style="width: 285px;">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder fs-6">
                                                                                            {{ $transection_log->event }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="fw-bold text-muted d-block fs-7">
                                                                                            {{ ucwords($transection_log->event_type) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>

                                                                                <!--begin::Status-->
                                                                                <td class="min-w-150px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <div class="symbol-group symbol-hover">
                                                                                            <div class="symbol symbol-30 symbol-circle"
                                                                                                data-toggle="tooltip"
                                                                                                data-placement="top"
                                                                                                title="{{ $transection_log->company_name ? $transection_log->name : 'By Default' }}"
                                                                                                style="    text-align: center;">
                                                                                                <div class="symbol symbol-30 symbol-circle"
                                                                                                    data-bs-placement="top"
                                                                                                    data-toggle="tooltip2"
                                                                                                    data-html="true"
                                                                                                    title="{{ $transection_log->company_name ? $transection_log->name : 'By Default' }}">
                                                                                                    <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100"
                                                                                                        style="float:left">
                                                                                                        <a href="javascript:void(0);"
                                                                                                            data-row-id="11"
                                                                                                            class="show-detail border-radius-100"
                                                                                                            id="kt_activities3_toggle">
                                                                                                            <div
                                                                                                                class="symbol-label fs-8 fs-8 bg-dark text-secondary border-radius-100">
                                                                                                                {{ $transection_log->company_name ? substr($transection_log->name, 0, 1) : 'B' }}
                                                                                                            </div>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <span
                                                                                                    class="text-muted fw-bold d-block fs-7">{{ $transection_log->company_name ? $transection_log->company_name : 'By Default' }}
                                                                                                </span>
                                                                                            </div>

                                                                                        </div>

                                                                                        <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                                                                        <!--data-toggle="tooltip"
                                                                                        title="User Company2">
                                                                                        User Co </a> -->

                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Stage-->


                                                                                <!--begin::Requisitions=-->
                                                                                @if ($transection_log->settlement_coins <= 0)
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder  fs-6"> 
                                                                                                @if($transection_log->transection_type==='credit')
                                                                                                {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                                                @else
                                                                                                {{ $transection_log->coins }} Coins
                                                                                                @endif
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                @else
                                                                                    <!--end::Requisitions=-->
                                                                                    <!--begin::Interview Mode=-->
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                                            <span
                                                                                                class="text-dark fw-bolder  fs-6">
                                                                                                {{ $transection_log->settlement_coins }}
                                                                                                Coins
                                                                                            </span>
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                                <!--end::Interview Mode=-->
                                                                                <!--begin::Interviewer Feedback=-->
                                                                                <td class="min-w-70px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                                        <span
                                                                                            class="fw-bold text-muted d-block fs-7">
                                                                                            Balance
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Interviewer Feedback-->
                                                                            </tr>
                                                                            <!--end::Table row-->
                                                                        </tbody>
                                                                        <!--end::Table body-->
                                                                    </table>
                                                                    <!--end::Table-->
                                                                </div>
                                                                <!--end::Timeline content-->
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center">
                                                            <p>No transaction logs available.</p>
                                                        </div>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="operationaltab" role="tabpanel">
                                            <div class="card-body px-0 py-7">
                                                <div class="timeline">
                                                    @if (@$coin_transection_logs_sales)

                                                        @foreach ($coin_transection_logs_sales as $transection_log)
                                                            <div class="timeline-item">

                                                                <!--begin::Timeline line-->
                                                                <div class="timeline-line w-40px"></div>
                                                                <!--end::Timeline line-->
                                                                <!--begin::Timeline icon-->
                                                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                                    <div class="symbol-label bg-light">
                                                                        <!--begin::Svg Icon-->
                                                                        <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="24px" height="24px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <polygon points="0 0 24 0 24 24 0 24">
                                                                                    </polygon>
                                                                                    <path
                                                                                        d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                        fill="#000000" fill-rule="nonzero"
                                                                                        opacity="0.3">
                                                                                    </path>
                                                                                    <path
                                                                                        d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                        fill="#000000" fill-rule="nonzero">
                                                                                    </path>
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </div>
                                                                </div>
                                                                <!--end::Timeline icon-->
                                                                <!--begin::Timeline content-->
                                                                <div class="timeline-content mb-10 mt-n1">

                                                                    <!--begin::Table-->
                                                                    <table
                                                                        class="table align-middle table-row-dashed fs-6 gy-0 mb-5"
                                                                        id="">
                                                                        <!--begin::Table body-->
                                                                        <tbody class="text-gray-600 fw-bold">
                                                                            <!--begin::Table row-->
                                                                            <tr>
                                                                                <!--begin::Date-->
                                                                                <td class="min-w-110px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder fs-6">
                                                                                            {{ date('d M Y', strtotime($transection_log->created_at)) }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="text-muted fw-bold d-block fs-7">
                                                                                            {{ date('h:i:A', strtotime($transection_log->created_at)) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Date-->

                                                                                <td class="min-w-285px" style="width: 285px;">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder fs-6">
                                                                                            {{ $transection_log->event }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="fw-bold text-muted d-block fs-7">
                                                                                            {{ ucwords($transection_log->event_type) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>

                                                                                <!--begin::Status-->
                                                                                <td class="min-w-150px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <div class="symbol-group symbol-hover">
                                                                                            <div class="symbol symbol-30 symbol-circle"
                                                                                                data-toggle="tooltip"
                                                                                                data-placement="top"
                                                                                                title="{{ $transection_log->company_name ? $transection_log->name : 'By Default' }}"
                                                                                                style="    text-align: center;">
                                                                                                <div class="symbol symbol-30 symbol-circle"
                                                                                                    data-bs-placement="top"
                                                                                                    data-toggle="tooltip2"
                                                                                                    data-html="true"
                                                                                                    title="{{ $transection_log->company_name ? $transection_log->name : 'By Default' }}">
                                                                                                    <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100"
                                                                                                        style="float:left">
                                                                                                        <a href="javascript:void(0);"
                                                                                                            data-row-id="11"
                                                                                                            class="show-detail border-radius-100"
                                                                                                            id="kt_activities3_toggle">
                                                                                                            <div
                                                                                                                class="symbol-label fs-8 fs-8 bg-dark text-secondary border-radius-100">
                                                                                                                {{ $transection_log->company_name ? substr($transection_log->name, 0, 1) : 'B' }}
                                                                                                            </div>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <span
                                                                                                    class="text-muted fw-bold d-block fs-7">{{ $transection_log->company_name ? $transection_log->company_name : 'By Default' }}
                                                                                                </span>
                                                                                            </div>

                                                                                        </div>

                                                                                        <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                                                                        <!--data-toggle="tooltip"
                                                                                        title="User Company2">
                                                                                        User Co </a> -->

                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Stage-->


                                                                                <!--begin::Requisitions=-->
                                                                                @if ($transection_log->settlement_coins <= 0)
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder  fs-6"> 
                                                                                                @if($transection_log->transection_type==='credit')
                                                                                                {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                                                @else
                                                                                                {{ $transection_log->coins }} Coins
                                                                                                @endif
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                @else
                                                                                    <!--end::Requisitions=-->
                                                                                    <!--begin::Interview Mode=-->
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                                            <span
                                                                                                class="text-dark fw-bolder  fs-6">
                                                                                                {{ $transection_log->settlement_coins }}
                                                                                                Coins
                                                                                            </span>
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                                <!--end::Interview Mode=-->
                                                                                <!--begin::Interviewer Feedback=-->
                                                                                <td class="min-w-70px">
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                                        <span
                                                                                            class="fw-bold text-muted d-block fs-7">
                                                                                            Balance
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <!--end::Interviewer Feedback-->
                                                                            </tr>
                                                                            <!--end::Table row-->
                                                                        </tbody>
                                                                        <!--end::Table body-->
                                                                    </table>
                                                                    <!--end::Table-->
                                                                </div>
                                                                <!--end::Timeline content-->
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center">
                                                            <p>No transaction logs available.</p>
                                                        </div>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>
                        <!--end::timeline Content-->


                    </div>

                    <!--begin::boost keyword Content-->
                    <div class="tab-pane" id="boostkeyword" role="tabpanel">
                            <form id="boost_keyword_form" class="form" action="#">
                                    <div class="card-body px-0 py-7">
                                        
                                            <div class="col-lg-12 mt-7">
                                                <input type="hidden" name="keyword_compant_id" id="keyword_compant_id" value="{{ @$client->id }}" />
                                                <label class="form-label required fw-bolder">Enter Keywords:</label>
                                                <input class="form-control form-control-solid" name="tags" value="{{ $boostkeywords }}" placeholder="Enter Keywords" value="" id="tags"/>
                                                <span class="form-text text-muted">Please select your keywords</span> </div>
                                            <!--end::Tags-->
                                            </div>
                                            <div class="text-center pt-15">
            
            
                                                <button type="submit" id="operational_top_up_new_submit" class="btn btn-primary">
                                                    <span class="indicator-label indicator-label-top-up">
                                                        Submit
                                                    </span>
                                                    <span class="indicator-progress indicator-progress-top-up">
                                                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                    </span>
                                                </button>
                                    </div>
                            </form>

                    </div>
                    <!--end::boost keyword Content-->

                </div>
            </div>
            <!--end::Body-->
        </div>
            <!--end:Tabs-->
    </div>
    <!--end::Content-->
</div>
<!--end::Body-->



<!--begin::Footer-->
<div class="card-footer py-5 text-center d-none" id="kt_activities_footer"> <a
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">
        &nbsp;
        <span class="svg-icon svg-icon-3 svg-icon-primary"></span>
    </a> </div>
<!--end::Footer-->

</div>






<script>

var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input)


        var country_id = "{{ $countrie_id }}";
        var state_id = "{{ $state_id }}";

        // alert(state_id);
        var city_id = "{{ $city_id }}";
        setTimeout(() => {
            $('#country').val(country_id).change();
        }, 200);


        $(document).on('change', '#country', function() {
            const country = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown = $('#city');

            var countrie_id = $('#country').val();
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
                $('#gstin_text').text('TAXIN')
            }


            // Reset State and City Dropdowns
            stateDropdown.empty().append('<option value="">Select State</option>').prop('disabled', true);
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (country) {
                // Fetch States via AJAX
                $.ajax({
                    url: '{{ route('get.states') }}',
                    method: 'GET',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(state) {
                                stateDropdown.append(
                                    `<option value="${state.id}">${state.name}</option>`);
                            });
                            stateDropdown.prop('disabled', false);
                        }
                        $('#state').val(state_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch states. Please try again.');
                    }
                });
            }
        });

        // Handle State Change
        $(document).on('change', '#state', function() {
            const state = $(this).val();
            const cityDropdown = $('#city');

            // Reset City Dropdown
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (state) {
                // Fetch Cities via AJAX
                $.ajax({
                    url: '{{ route('get.cities') }}',
                    method: 'GET',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(city) {
                                cityDropdown.append(
                                    `<option value="${city.id}">${city.name}</option>`);
                            });
                            cityDropdown.prop('disabled', false);
                        }
                        $('#city').val(city_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch cities. Please try again.');
                    }
                });
            }
        });




        $(document).on('submit', '#show-billing-form', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '{{ route('form.submit') }}', // Route to handle form submission
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token header
                },
                success: function (response) {
                    // Handle success
                    Swal.fire("Success", "Billing Address updated successfully ", "success").then(
                                        function() {
                                            $('#usernamebill').text(response.usernamebill);
                                            $('#billing_address').text(response.street);
                                            $('#billing_gst').text(response.billing_gst);
                                            $('#billing_pan').text(response.billing_pan);
                                            $('#kt_activitiesApplication_close').click();
                                        });
                },
                error: function (response) {
                    // Handle error
                    console.log(response);
                    alert('Form submission failed');
                }
            });
        });




    $(document).ready(function() {


        // Payment Method Selector
        const paymentSelector = document.getElementById('payment-mode-selector');
        const paymentMethods = document.querySelectorAll('.payment-method');

        // Function to hide all payment sections
        function hideAllMethods() {
            paymentMethods.forEach(method => method.classList.remove('active'));
        }

        // Event listener for payment method selection
        paymentSelector.addEventListener('change', function() {

            hideAllMethods(); // Hide all sections
            const selectedMethod = this.value;
            if (selectedMethod) {
                if (selectedMethod == 'Cash') {
                    var activeSection = 'cash';
                } else if (selectedMethod == 'Cheque') {
                    var activeSection = 'cheque';
                } else if (selectedMethod == 'Bank Transfer') {
                    var activeSection = 'bank_transfer';
                } else if (selectedMethod == 'Payment Link') {
                    var activeSection = 'online';
                }
                document.getElementById(activeSection).classList.add('active');
            }
        });


    });


    function copyText() {
        // Get the text to be copied
        var textToCopy = document.getElementById("textToCopy").innerText;

        // Create a temporary input element
        var tempInput = document.createElement("input");
        tempInput.value = textToCopy;
        document.body.appendChild(tempInput);

        // Select the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text
        document.execCommand("copy");

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // Provide feedback to the user
        alert("Text copied to clipboard: " + textToCopy);
    }

        function paymentLinkcopyText(link) {
            // Get the text to be copied
            var textToCopy = link;

            // Create a temporary input element
            var tempInput = document.createElement("input");
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);

            // Select the text
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text
            document.execCommand("copy");

            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Provide feedback to the user
            alert("Link copied to clipboard: " + textToCopy);
        }

            $(document).on('click','.TeamListOpen',function(){
                fetchTeams();
            });

            $(document).ready(function() {
                    
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
					url: `{!! route('superadmin.company.contactdata') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
                        clientId: "{{ @$client->id }}",
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
<script>
    $(document).ready(function() {
        $('#successButtoncan').hide();
        $('#errorButtoncan').hide();

        $('#successButtoncanJob').hide();
        $('#errorButtoncanJob').hide();

        $('#successButtonclient').hide();
        $('#errorButtonclient').hide();

        $('#successButtonoperationaltopup').hide();
        $('#errorButtonoperationaltopup').hide();

        $('#inputJobSearch').keyup(function() {
            fetchCompanyJobs();
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });


        $(document).on('click', '#editCompanyWallet', function() {


            $('#kt_activitiesWallet_toggle').trigger('click');
            var id = "{{ @$client->id }}";
            var url = "{{ route('superadmin.custompackages.editWallet', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesWallet_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesWallet_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });




        $(document).on('click', '.comment_submit', function() {

            var id = $(this).attr('rel');
            var comment = $('#comment_' + id).val();
            if (comment == '') {
                return false;
            }



            var url = "{{ route('superadmin.custompackages.submitcomment', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'id': id,
                    'comment': comment
                },
                cache: false,
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire("Success", 'Invoice comment submit successfully !',
                            "success")
                        // myPackageHistory._fnDraw();
                    } else {
                        Swal.fire("Warning", 'Opps something wrong, please try again !',
                            "info");
                        // myPackageHistory._fnDraw();
                    }
                }
            });


        });


    });

            $(document).on('click','.JobListOpen',function(){
                        fetchCompanyJobs();
            });
                    
            $(document).on('click', '#paginationJobs .page-link', function(e) {
                    e.preventDefault();

                    const page = $(this).data('page');
                    if (!page || $(this).hasClass('disabled')) return;

                    // Disable the clicked pagination link
                    $(this).addClass('disabled');

                    // Fetch data
                    fetchCompanyJobs(page, $('#search').val()).then(() => {
                        // Re-enable the links after successful data fetch
                        $('#paginationJobs .page-link').removeClass('disabled');
                    }).catch(() => {
                        // Re-enable the links even if there's an error
                        $('#paginationJobs .page-link').removeClass('disabled');
                    });
                    
            });
           
            
            var page_no = localStorage.getItem('currentPageJobs');
            if (!page_no) {
                page_no = 1;
            }

			function fetchCompanyJobs(page = page_no, search = '') {
				$('.loadingMessagejobListAjax').css({ display: 'block' });
				$.ajax({
					url: `{!! route('superadmin.company.all_data') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputJobSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
                        clientId: "{{ @$client->id }}",
						hid_compant_id: $('#hid_compant_id').val(),
						sortingfilter: $('#sortingfilter').val()
					},
					success: function(response) {
						$('.loadingMessagejobListAjax').css({ display: 'none' });
                        localStorage.setItem('currentPageJobs', 1);
						$('#jobListAjax').html(response.html);
						$('#paginationJobs').html(response.pagination);
					}
				});
			}




/**
    var allreq = $('#allreq').dataTable({

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
        "pageLength": 25,
        colReorder: true,
        ajax: {
            'url': '{!! route('superadmin.company.all_data') !!}',
            "data": function(d) {
                d.search = $('#inputJobSearch').val(), d.client_id = $('#hid_client_id').val(), d
                    .compant_id = $('#hid_compant_id').val()
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
                "targets": 0
            },
            {
                "width": "100px",
                "targets": 1,
                "class": "d-flex align-items-center border-0"
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
    new $.fn.dataTable.FixedHeader(allreq);

    **/




    var company_id = $('#hid_compant_id').val();
    var myPackageHistory = $('#myPackageHistory').dataTable({
        responsive: true,
        processing: true,
        ordering: false, //disable ordering       			
        destroy: true,
        stateSave: true,
        "bLengthChange": false,
        sDom: 'lrtip',
        //responsive		: true,
        // processing		: true,				
        searching: false,
        serverSide: true,
        ajax: '{!! route('superadmin.custompackages.myPackageHistory') !!}?company_id=' + company_id,
        language: {
            //"url": "<?php echo __('app.datatable'); ?>" 
        },
        "fnDrawCallback": function(oSettings) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        columns: [{
            data: 'Name',
            name: 'Name'
        }]
    });
    new $.fn.dataTable.FixedHeader(myPackageHistory);



</script>
