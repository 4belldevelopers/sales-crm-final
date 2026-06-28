@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
@endphp
 @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
				$endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp
@push('head-script')
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

        #customHm {
            width: 60px !important;
        }
        .iti__flag {
        margin-top: 2px !important;
      }
    </style>
@endpush
@php
$match = collect($directClientDetail->staffing_company)->first(function ($item) use ($company_id) {
    return ($item['from_company_id'] ?? null) == $company_id
        || ($item['to_company_id'] ?? null) == $company_id;
});
$from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
$to_company_id   = ($match['from_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

@endphp


<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $directClientDetail->client_id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        <div class="card mb-5 mb-xl-5 mx-2">
            <div class="card-body pt-5 pb-0 px-5">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <div class="me-7 mb-4">
                        @php
                            $bgTextDecore = $strVal = '';
                            $str = trim(@$directClientDetail->name);
                            $str = $strVal = strtolower($str[0]);
                            $str = ord($str);
                            if ($str >= 97 && $str <= 101) {
                                $bgTextDecore = 'bg-light-danger text-danger';
                            } elseif ($str >= 102 && $str <= 106) {
                                $bgTextDecore = 'bg-light-warning text-warning';
                            } elseif ($str >= 107 && $str <= 111) {
                                $bgTextDecore = 'bg-light-primary text-primary';
                            } elseif ($str >= 112 && $str <= 116) {
                                $bgTextDecore = 'bg-light-success text-success';
                            } elseif ($str >= 117 && $str <= 121) {
                                $bgTextDecore = 'bg-dark text-secondary';
                            } else {
                                $bgTextDecore = 'bg-light-secondary text-dark';
                            }

                            $background_image = '';
                            if (
                                !empty(@$directClientDetail->logo_logical_path) &&
                                strlen(@$directClientDetail->logo_logical_path) > 10
                            ) {
                                $background_image = @$directClientDetail->logo_logical_path;
                            }

                            $logo_path = '';//url('img/client-default.png');
                            if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
                                $logo_path = @$directClientDetail->logo;
                            }

                        @endphp
                        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <a
                                href="javascript:void(0);">
                                @php
                                    if (!empty($logo_path)) {
                                        echo '<img src="' . @$logo_path . '" height="100px" width="100px"  />';
                                    } else {
                                        echo '<div class="symbol-label fs-1 ' .
                                            @$bgTextDecore .
                                            ' border-radius-5"><span>' .
                                            ucwords(@$strVal) .
                                            '</span></div>';
                                    }
                                @endphp
                            </a>
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-gray-900 fs-2 fw-bolder me-2">
                                        {{ ucwords(@$directClientDetail->name) }} </span>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Verified"> <span class="svg-icon svg-icon-1 svg-icon-success">
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
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
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

                                        <?php
                                        $industries_name = '';
                                        if (trim($directClientDetail->industry) != '') {
                                            $industries_name = $industries[@$directClientDetail->industries ? @$directClientDetail->industries : 0];
                                        }
                                        echo @$industries_name;
                                        ?>

                                    </a> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
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
                                        @php
                                            echo ucwords($city_name);
                                            echo ' , ';
                                            echo ucwords($country_name);
                                        @endphp
                                    </a>
                                    <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 d-none">
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
                                        </span> Badge </a>
                                </div>
                            </div>
                            <div class="d-flex mt-4"> <a href="javascript:void(0);" type="button"
                                    class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Active</a>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="row w-75"> <span class="">
                                        @php
                                            $tags = @$directClientDetail->tags;
                                            $tags = explode(',', $tags);
                                            foreach ($tags as $key => $val) {
                                                echo '<span class="badge badge-light">' . ucwords($val) . '</span>  ';
                                            }
                                        @endphp
                                    </span> </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--begin::separator-->
                {{-- <div class="separator separator-solid my-7"></div> --}}
                <div class="d-flex align-items-center flex-wrap pb-7 d-none">
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
                                @php
                                    if ($directClientDetail->company_type > 0) {
                                        echo $companyType[@$directClientDetail->industries_type];
                                    }
                                @endphp
                            </span> 
                        </div>
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
                        <div class="d-flex flex-column text-dark-75">
                            <span class="fw-bolder fs-6">Employees</span>
                            <span class="fw-bolder fs-3">{{ number_format(@$directClientDetail->company_size) }}</span>
                        </div>
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
                        <div class="d-flex flex-column text-dark-75">
                            <span class="fw-bolder fs-6">Active Jobs</span>
                            <span class="fw-bolder fs-3">{{ number_format(@$active_requisition) }}</span>
                        </div>
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
                                    // dd($directClientDetail->founded);
                                    $foundedDate = @$directClientDetail->founded;
                                    $finalDate = $currentDate - $foundedDate;
                                    echo @$foundedDate;
                                @endphp
                            </span> </div>
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
                        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span
                                class="fw-bolder fs-3">
                                @php

                                    $currentDate = $createDate = $finalDate = 0;
                                    $currentDate = date('Y');
                                    $createDate = date('Y', strtotime(@$directClientDetail->to_created_at));
                                    $finalDate = $currentDate - $createDate;

                                    $finalDate = GeneralMethod::timeAgo($directClientDetail->to_created_at);
                                    // dd($directClientDetail->created_at);
                                    echo @$finalDate;
                                @endphp
                            </span> </div>
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


                            @php
                                foreach ($client_contact_info as $cci) {
                                    // dd(@$cci->name);
                                    $bgTextDecore = GeneralMethod::icoinText(@$cci->name);
                                    echo '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                        ucwords(@$cci->name) .
                                        '" >
                                                        <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                        ucwords(@$cci->name) .
                                        '" >
                                                        <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                                                        <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                                                            <div class="symbol-label fs-8 fs-8 ' .
                                        $bgTextDecore .
                                        ' border-radius-100">' .
                                        substr($cci->name, 0, 1) .
                                        '</div>
                                                        </a>		 
                                                        </div>
                                                        </div>
                                                        </div>';
                                }
                            @endphp
                        </div>
                    </div>
                    <!--end: Item-->
                </div>
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
                        <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab" href="#overview">
                                <span class="nav-text font-weight-bold">Overview</span> </a> </li>
                        @if(@$all_tab_show)        
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#contacts"> <span
                                    class="nav-text font-weight-bold">Contacts</span> </a> </li>
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#requisitions"> <span
                                    class="nav-text font-weight-bold">Jobs</span> </a> </li>
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidate_list">
                                <span class="nav-text font-weight-bold">Candidate</span> </a>
                        </li>
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interview_tracker">
                                <span class="nav-text font-weight-bold">Interview Tracker</span> </a>
                        </li>
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidate_joining">
                                <span class="nav-text font-weight-bold">Joinings</span> </a>
                        </li> 
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidateDashboard"> <span class="nav-text font-weight-bold">Candidate Dashboard</span> </a> </li> 
                        @endif          
                    </ul>
                </div>
            </div>
            <!--end::Tabs Header-->
            <!--Begin::Body-->
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                  <!--begin::Overview Content-->
                  <div class="tab-pane active" id="overview" role="tabpanel">
                      {{-- <div class="card-header cursor-pointer px-0">
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Direct Client</h3>
                      </div>
                    </div> --}}
                      <div class="card-body px-0 py-7">
                          <div class="row">
                              <div class="col-lg-12 mb-7">
                                  @php
                                      $description = '';
                                      $description = @$directClientDetail->description
                                          ? @$directClientDetail->description
                                          : @$directClientDetail->companies_description;
                                      echo $description;
                                  @endphp
                              </div>
                              <div class="col-lg-12">
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
                                      <div class="col-lg-9"> <a href="javascript:void(0);"
                                              class="fw-bold fs-6 text-primary">{{ @$directClientDetail->website }}</a>
                                      </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              {{ $industries_name }} </span> </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              {{ number_format(@$directClientDetail->company_size) }}+ employees,
                                          </span> </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              @php
                                                  echo ucwords($city_name);
                                                  echo ' , ';
                                                  echo ucwords($country_name);
                                              @endphp
                                          </span> </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Type</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              @php
                                                  if ($directClientDetail->company_type > 0) {
                                                      echo $companyType[@$directClientDetail->industries_type];
                                                  }
                                              @endphp

                                          </span> </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              @php
                                                  $currentDate = $foundedDate = $finalDate = 0;
                                                  $currentDate = date('Y');
                                                  $foundedDate = @$directClientDetail->founded;
                                                  $finalDate = $currentDate - $foundedDate;
                                                  echo @$foundedDate;
                                              @endphp
                                          </span> </div>
                                  </div>
                                  <div class="row mb-7">
                                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>
                                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                                              @php
                                                  $tags = @$directClientDetail->tags;
                                                  $tags = explode(',', $tags);
                                                  $cnt = 0;
                                                  foreach ($tags as $key => $val) {
                                                      if ($cnt > 0) {
                                                          echo ', ';
                                                      }
                                                      echo '<span class="">' . ucwords($val) . '</span> ';
                                                      $cnt++;
                                                  }
                                              @endphp
                                          </span> </div>
                                  </div>
                              </div>
                              <div class="col-lg-6 d-none">
                                  <div class="row mb-7">
                                      <label class="col-lg-4 fw-bold text-muted fs-6">Notice Period</label>
                                      <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">15
                                              Days</span> </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="tab-pane" id="contacts" role="tabpanel">
                      {{-- <div class="card-header cursor-pointer px-0">
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Contact List</h3>
                      </div>
                    </div> --}}
                      <div class="card-body px-0 py-7">
                          <table class="table align-middle table-row-dashed fs-6 gy-5" id="staffingContactList">
                              <thead>
                                  <tr class="fw-bolder text-muted">
                                      <th class="w-10px pe-0">
                                          <div
                                              class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                              <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                  data-kt-check-target="#kt_table_users .form-check-input2"
                                                  value="1" />
                                          </div>
                                      </th>
                                      <th class="min-w-100px">Name</th>
                                      <th class="min-w-150px">Contacts</th>
                                      <th class="min-w-15px">Status</th>
                                  </tr>
                              </thead>
                          </table>
                      </div>
                  </div>

                  <!--begin::candidate_list Candidate List-->
                  <div class="tab-pane" id="candidate_list" role="tabpanel">

                      <div class="card-body px-0 py-7">
                          <!--begin::Table-->
                          <div class="loadingMessage loadingMessagecandidatetList" style="display: none; position: absolute;">Processing...</div>
                          <table class="table align-middle table-row-dashed fs-6 gy-5">
                              <!--begin::Table head-->
                              <thead>
                                  <!--begin::Table row-->

                                  <tr class="fw-bolder text-muted">

                                      <th class="min-w-150px">Name</th>
                                      <th class="min-w-15px">Exp.</th>
                                      <th class="min-w-15px">Salary</th>
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Qualification</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
                                      <th class="min-w-15px">Jobs</th>
                                      <th class="min-w-15px">Stage</th>
                                  </tr>
                                  <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <tbody id="candidatetList">

                              </tbody>

                          </table>
                          <!--end::Table-->
                          <div id="paginationcandidate">
                              <!-- Pagination links will be appended here -->
                          </div>
                      </div>
                      <!--end::Contacts Content-->
                  </div>
                  <!--end::candidate_list Candidate List-->
                  
                <!--begin::candidateDashboard Content-->
                
                        <div class="tab-pane" id="candidateDashboard" role="tabpanel">
                            <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
                            <div class="card-body px-0 pt-0 pb-0">
                                <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">

                                <div class="row gx-5 gx-xl-10 mb-xl-10">
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                                            @php
                                            $active_count = $counts->get('active', 0);
                                            $hold_count = $counts->get('hold', 0);
                                            $inactive_count = $counts->get('inactive', 0);
                                            $alljobs_count = $active_count + $hold_count + $inactive_count;
                                            @endphp
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Currency-->
                                                        <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                                        <!--end::Currency-->

                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $inactive_count+$active_count+$hold_count }}</span>
                                                        <!--end::Amount-->

                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-success fs-base">                                
                                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                            2.2%
                                                        </span>             --}}
                                                        <!--end::Badge--> 
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">All Jobs</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->      
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body pt-2 pb-4 d-flex align-items-center">
                                                <!--begin::Chart-->
                                                <div class="d-flex flex-center me-5 pt-2">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$directClientDetail->client_id; ?>" 
                                                        style="min-width: 170px; min-height: 170px" 
                                                        data-kt-size="170" 
                                                        data-kt-line="11">
                                                        <span></span>
                                                        <canvas id="allJobGrapg_<?php echo @$directClientDetail->client_id; ?>" height="70" width="70"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Chart-->
                                                
                                                <!--begin::Labels-->
                                                <div class="d-flex flex-column content-justify-center w-100">
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #22c55e"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Active Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $active_count }}</div>
                                                        <!--end::Stats-->
                                                    </div>
                                                    <!--end::Label-->

                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ffab00"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Hold Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $hold_count }}</div>
                                                        <!--end::Stats-->                    
                                                    </div>
                                                    <!--end::Label-->

                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ff0202"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Closed Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class=" fw-bolder text-gray-700 text-xxl-end">{{ $inactive_count }}</div>
                                                        <!--end::Stats-->                      
                                                    </div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Labels-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>                       
                                    </div>
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush  h-md-50 mb-5 mb-xl-10">
                                        <!--begin::Card widget 6-->
                                        <div class="card card-flush  h-md-50 mb-5 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Currency-->
                                                        <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                                        <!--end::Currency-->

                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotal}}</span>
                                                        <!--end::Amount-->

                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-success fs-base">                                
                                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                            2.6%
                                                        </span>             --}}
                                                        <!--end::Badge--> 
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Added in last 7 days</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->           
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex align-items-end px-3 pb-10">
                                                <!--begin::Chart-->
                                                <div id="kt_card_widget_6_chart_<?php echo @$directClientDetail->client_id; ?>" class="w-100" style="height: 200px">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$directClientDetail->client_id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_left_chart_<?php echo @$directClientDetail->client_id; ?>" height="170"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Chart-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Card widget 6--> 
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-cus-12 col-xl-12 col-xxl-12 mb-5 mb-xl-0">
                                        <div class="card card-flush overflow-hidden h-md-100">
                                            <!--begin::Header-->
                                            <div class="card-header py-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">   
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$total_candidates}} </span>         
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Added In {{ date('d M y',strtotime($startOfMonth)) }} To {{ date('d M y',strtotime($endOfMonth)) }}</span>
                                                    {{-- <span class="text-gray-500 mt-1 fw-semibold fs-6">Users from all channels</span> --}}
                                                </h3>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">  
                                                <!--begin::Chart-->
                                                {{-- <div style="position: absolute; bottom: 50px; width: 222px; height: 53px; background: #ffffff; z-index: 99;"> </div> --}}
                                                <div id="kt_charts_widget_3" class="min-h-auto">

                                                    <canvas id="myChart_<?php echo @$directClientDetail->client_id; ?>"></canvas>

                                                    
                        
                                                    {{-- <div id="chart-container" style="width: 600px; height: 420px; margin: auto;"></div> --}}
                                                </div><br>
                                                <!--end::Chart--> 
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-5 gx-xl-10 mb-xl-10">
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaloffer}}</span>
                                                        <!--end::Amount-->
                                                        
                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-danger fs-base">                                
                                                            <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i> 
                                                            2.2%
                                                        </span>             --}}
                                                        <!--end::Badge-->                
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Offers In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>             
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->         
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex align-items-end pt-0">
                                                <!--begin::Progress-->
                                                

                                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$directClientDetail->client_id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_offer_chart_<?php echo @$directClientDetail->client_id; ?>" height="170"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Progress-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>                        
                                    </div>
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                        
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">                
                                                    <!--begin::Amount-->
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaljoin}}</span>
                                                    <!--end::Amount-->           

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Joined In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->           
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                                <!--begin::Title-->
                                                <!--end::Title-->

                                                <!--begin::Users group-->
                                                <div class="symbol-group symbol-hover flex-nowrap">

                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$directClientDetail->client_id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_join_chart_<?php echo @$directClientDetail->client_id; ?>" height="170"></canvas>
                                                    </div>


                                                        
                                                </div>
                                                <!--end::Users group-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>
                                    </div>

                                </div>

                                <!--Start Interview Section-->
                                    <div class="row gy-5 g-xl-10">
                                        <!--begin::Col-->
                                        <div class="col-xl-4 mb-xl-10">
                                            
                                            <!--begin::List widget 17-->
                                            <div class="card card-flush h-xl-100">
                                                <!--begin::Header-->
                                                <div class="card-header pt-7">
                                                    <!--begin::Title-->
                                                    <h3 class="card-title align-items-start flex-column">
                                                        <span class="card-label fw-bold text-gray-800">Upcoming Interviews</span>
                                                    </h3>
                                                    <!--end::Title-->
                                                    <!--end::Toolbar-->
                                                </div>
                                                <!--end::Header-->

                                                <!--begin::Body-->
                                                <div class="card-body pt-0"> 
                                                    
                                                        <!--begin::Content-->                                  
                                                        <div class="d-flex flex-stack my-5">
                                                            <span class="text-gray-500 fs-7 fw-bold"></span>
                                                            
                                                            <span class="text-gray-500 fw-bold fs-7"></span>
                                                        </div>                   
                                                        <!--end::Content-->

                                                        @if (count($upcomingData) > 0)
                                                            @foreach ($upcomingData as $rowArr)

                                                                @php
                                                                $row = (object) $rowArr;
                                                                $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                                $schedule_date_explode = explode(" ",$schedule_date);
                                                                @endphp
                                                    
                                                                <!--begin::Item-->
                                                                <div class="d-flex flex-stack">                 
                                                                    <!--begin::Wrapper-->                                  
                                                                    <div class="d-flex align-items-center me-3">
                                                                        <!--begin::Icon-->                         
                                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-40px h-40px mr-2 text-center" style="display: inline-grid">
                                                                        <span style="font-size: 12px; font-weight: 600;">{{$schedule_date_explode[0]}}</span><span style="color: #373bed; font-size: 17px; font-weight: 900;">{{$schedule_date_explode[1]}}</span>                
                                                                        </a>                 
                                                                        <!--end::Icon-->

                                                                        <!--begin::Section-->                                  
                                                                        <div class="flex-grow-1">
                                                                            <a href="javascript:;" class="text-gray-800 text-hover-primary fs-5 fw-bold lh-0">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</a>
                                                                            
                                                                            <span class="text-gray-500 fw-semibold d-block fs-7">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                                        </div>                   
                                                                        <!--end::Section-->
                                                                    </div>                   
                                                                    <!--end::Wrapper-->                 

                                                                    <!--begin::Value-->
                                                                    <span class="text-gray-800 fw-bold fs-6">
                                                                        {{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}} <br>
                                                                        <span class="text-gray-500 fw-semibold d-block fs-7">
                                                                        {{ucwords(mb_substr($row->mode_name, 0, 13))}} 
                                                                        <span> 
                                                                    </span>
                                                                    <!--end::Value-->               
                                                                </div>
                                                                <!--end::Item-->

                                                                <!--begin::Separator-->
                                                                <div class="separator separator-dashed my-4"></div>
                                                                <!--end::Separator-->
                                                            @endforeach
                                                        @else
                                                            <table border="1" width="100%" cellpadding="10">
                                                                <tbody>
                                                                        <tr>
                                                                            <td colspan="4" align="center">No records found</td>
                                                                        </tr>
                                                                </tbody>
                                                            </table>
                                                        @endif                                   
                                                    
                                                </div>
                                                <!--end::Body-->
                                            </div>
                                            <!--end::List widget 17-->     
                                        </div>
                                        <!--end::Col-->      

                                        <!--begin::Col-->
                                        <div class="col-xl-8 mb-5 mb-xl-10">
                                            
                                            <!--begin::Table widget 6-->
                                            <div class="card card-flush h-xl-100">
                                                <!--begin::Header-->
                                                <div class="card-header pt-7" style="position: relative">
                                                    <!--begin::Title-->
                                                    <h3 class="card-title align-items-start flex-column">			
                                                        <span class="card-label fw-bold text-gray-800">Candidates Interviews</span>
                                                    </h3>
                                                    <!--end::Title-->
                                                    <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                                    <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetmonth(this.value)">
                                                            <option value="1">Last 6 Months</option>
                                                            <option value="0">Last 7 Days</option>
                                                        </select>
                                                    </span>
                                                    
                                                </div>
                                                <!--end::Header-->

                                                <!--begin::Body-->
                                                <div class="card-body">       
                                                    <!--begin::Tab Content-->
                                                    <div class="tab-content">
                                                            <!--begin::Tap pane-->
                                                            <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1_<?php echo @$directClientDetail->client_id; ?>" role="tabpanel">
                                                                <!--begin::Table container-->

                                                                <div id="kt_card_widget_6_chart_<?php echo @$directClientDetail->client_id; ?>" class="w-100" style="height: 200px">

                                                                    <div class="inetview_recent_chart_<?php echo @$directClientDetail->client_id; ?> d-none" id="kt_card_widget_4_chart_<?php echo @$directClientDetail->client_id; ?>" 
                                                                        style="min-width: 100%; min-height: 200px">
                                                                        <canvas id="inetview_recent_chart_<?php echo @$directClientDetail->client_id; ?>" height="300"></canvas>
                                                                    </div>
                                                                    
                                                                    <div class="inetview_recent_chart_month_<?php echo @$directClientDetail->client_id; ?>" id="kt_card_widget_4_chart" 
                                                                            style="min-width: 100%; min-height: 200px">
                                                                            <canvas id="inetview_recent_chart_month_<?php echo @$directClientDetail->client_id; ?>" height="230"></canvas>
                                                                    </div>

                                                                </div>

                                                                    
                                                                <!--end::Table-->
                                                            </div>
                                                            <!--end::Tap pane-->
                                                    </div>
                                                    <!--end::Tab Content-->        
                                                </div>
                                                <!--end: Card Body-->
                                            </div>
                                            <!--end::Table widget 6-->   
                                        </div>
                                        <!--end::Col-->      
                                    </div>
                                <!--End Interview Section-->


                                </div>
                            </div>
                            </div>
                        </div>
                <!--end begin::candidateDashboard Content-->

                  <!--begin::interview_tracker Candidate List-->
                  <div class="tab-pane" id="interview_tracker" role="tabpanel">

                      <div class="card-body px-0 py-7">
                          <!--begin::Table-->
                          <div class="loadingMessage loadingMessagecandidatetListinterviewtracker" style="display: none; position: absolute;">Processing...</div>
                          <table class="table align-middle table-row-dashed fs-6 gy-5">
                              <!--begin::Table head-->
                              <thead>
                                  <!--begin::Table row-->

                                  <tr class="fw-bolder text-muted">

                                      <th class="min-w-150px">Name</th>
                                      <th class="min-w-15px">Job</th>
                                      <th class="min-w-15px">Interview Mode</th>
                                      <th class="min-w-15px">Interviewers</th>
                                      <th class="min-w-15px">Source</th>
                                      <th class="min-w-15px">Interview Date</th>
                                      <th class="min-w-15px">Feedback</th>
                                      <th class="min-w-15px">Rating</th>
                                      <th class="min-w-15px">Stage</th>
                                  </tr>
                                  <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <tbody id="candidatetListinterviewtracker">

                              </tbody>

                          </table>
                          <!--end::Table-->
                          <div id="paginationcandidateinterviewtracker">
                              <!-- Pagination links will be appended here -->
                          </div>
                      </div>
                      <!--end::Contacts Content-->
                  </div>
                  <!--end::interview_tracker Candidate List-->

                  <!--begin::candidate_joining Candidate List-->
                  <div class="tab-pane" id="candidate_joining" role="tabpanel">

                      <div class="card-body px-0 py-7">
                          <!--begin::Table-->
                          <div class="loadingMessage loadingMessagecandidatetListJoin" style="display: none; position: absolute;">Processing...</div>
                          <table class="table align-middle table-row-dashed fs-6 gy-5">
                              <!--begin::Table head-->
                              <thead>
                                  <!--begin::Table row-->

                                  <tr class="fw-bolder text-muted">

                                      <th class="min-w-150px">Name</th>
                                      <th class="min-w-15px">Exp.</th>
                                      <th class="min-w-15px">Salary</th>
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Qualification</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
                                      <th class="min-w-15px">Jobs</th>
                                      <th class="min-w-15px">Stage</th>
                                  </tr>
                                  <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <tbody id="candidatetListJoin">

                              </tbody>

                          </table>
                          <!--end::Table-->
                          <div id="paginationcandidatejoin">
                              <!-- Pagination links will be appended here -->
                          </div>
                      </div>
                      <!--end::Contacts Content-->
                  </div>
                  <!--end::candidate_joining Candidate List-->


                  <!--begin::Requisitions Content-->
                  <div class="tab-pane" id="requisitions" role="tabpanel">
                      <div class="tab-content" id="myTabContent">
                          <!--begin::All Req.-->
                          <div class="tab-pane fade show active" id="all-requisitions" role="tabpanel">
                              {{-- <div class="card-header cursor-pointer px-0">
                            <div class="card-title m-0">
                              <h3 class="fw-bolder m-0">All Requisition</h3>
                            </div>
                          </div> --}}
                              <div class="card-body px-0 py-7">
                                  @if (in_array('view_jobs', $userPermissions))
                                  <div class="loadingMessage loadingMessagestaffingpartnerjob" style="display: none; position: absolute;">Processing...</div>
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
                                                  <th class="" id="customHm"><i class="fa fa-users"
                                                          aria-hidden="true" style="font-size:17px"></i></th>
                                                  <th class=""><i class="fa fa-handshake" aria-hidden="true"
                                                          style="font-size:17px"></i></th>
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
                                          <tbody id="staffingpartnerjob"></tbody>
                                      </table>
                                      <div id="paginationstaffingpartnerjob"></div>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end:Tabs-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Body-->
</div>


<script>
            var myChartId = "<?php echo @$directClientDetail->client_id; ?>";

            var showDataLabels = {
                          id: 'showDataLabels',
                          afterDatasetsDraw(chart) {
                              const { ctx } = chart;
                              chart.data.datasets.forEach((dataset, i) => {
                                  const meta = chart.getDatasetMeta(i);
                                  meta.data.forEach((bar, index) => {
                                      const value = dataset.data[index];
                                      ctx.save();
                                      ctx.fillStyle = 'black';
                                      ctx.font = 'bold 12px sans-serif';
                                      ctx.textAlign = 'center';
                                      ctx.fillText(value, bar.x, bar.y - 1.5);
                                      ctx.restore();
                                  });
                              });
                          }
            };

            var showDataLabelsvertical = {
                id: 'showDataLabelsvertical',
                afterDatasetsDraw(chart) {
                    var { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            var value = dataset.data[index];
                            ctx.save();
                            ctx.fillStyle = 'black';
                            ctx.font = 'bold 12px sans-serif';
                            ctx.textAlign = 'left';  // align left for horizontal bars
                            ctx.textBaseline = 'middle';

                            // place text at the end of bar (right side)
                            ctx.fillText(value, bar.x + 1, bar.y);
                            ctx.restore();
                        });
                    });
                }
            };

            function loadCountryChart() {

            var ctx = document.getElementById(`myChart_${myChartId}`).getContext('2d');
            

                var countryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined','Rejected'],
                    datasets: [{
                        label: 'Users',
                        data: ["{{$inreview_count}}", "{{$sourcing_count}}", "{{$screening_count}}", "{{$evaluation_count}}", "{{$negotiation_count}}", "{{$offer_count}}", "{{$joined_count}}","{{$Rejected_count}}"],
                        backgroundColor: [
                            '#bdbd26', // inreview_count
                            '#000000', // sourcing_count
                            '#c000ff', // screening_count
                            '#2896ff', // evaluation_count
                            '#ff4098', // negotiation_count
                            '#ffab00', // offer_count
                            '#06c732',
                            '#ff0202' // joined_count // joined_count
                        ],
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y', // makes it horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value <= 10) return value;
                                    else if (value % 100 === 0) return value.toLocaleString();
                                    else return '';
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [showDataLabelsvertical] // ✅ custom labels plugin
            });
               }

                setTimeout(() => {
                  loadCountryChart();
             }, 300);  


             // Get the canvas element
        
            var ctx_job_div = document.getElementById(`allJobGrapg_${myChartId}`).getContext('2d');
              // Create the donut chart
            var allJobGrapg = new Chart(ctx_job_div, {
                type: 'doughnut',
                data: {
                    labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'], // Labels for the chart
                    datasets: [{
                        label: 'Total',
                        data: ['{{ $active_count }}', '{{ $hold_count }}', '{{ $inactive_count }}'], // Values for the chart
                        backgroundColor: [
                            '#22c55e', // Green for 'Active Jobs'
                            '#ffab00', // Blue for 'Hold Jobs'
                            '#ff0202'  // Gray for 'Closed Jobs'
                        ],
                        hoverOffset: 4 // Highlight on hover
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide the legend in the chart area
                        }
                    },
                    cutout: '70%' // To create the donut hole
                }
            });


            
            var candidates_left_chart = document.getElementById(`candidates_left_chart_${myChartId}`).getContext('2d');

            var allJobGraphBar = new Chart(candidates_left_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            
            var candidates_offer_chart = document.getElementById(`candidates_offer_chart_${myChartId}`).getContext('2d');

            var allofferGraphBar = new Chart(candidates_offer_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommoffer) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommoffer) !!},
                        backgroundColor: [
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00' 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            
            var candidates_join_chart = document.getElementById(`candidates_join_chart_${myChartId}`).getContext('2d');

            var alljoinGraphBar = new Chart(candidates_join_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommjoin) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommjoin) !!},
                        backgroundColor: [
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732'  
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart_month = document.getElementById(`inetview_recent_chart_month_${myChartId}`).getContext('2d');

            var netview_recentGraphBarMonth = new Chart(inetview_recent_chart_month, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabelsMonth) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValuesMonth) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart = document.getElementById(`inetview_recent_chart_${myChartId}`).getContext('2d');

            var netview_recentGraphBar = new Chart(inetview_recent_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabels) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            function filtersdatagetmonth(valSelected){
                if(valSelected==1){
                      $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
                }else{
                      $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
                }

            }

            
    </script>

    
<script>
    var table = $('#staffingContactList').dataTable({

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
            'url': '{!! route('service_company.consultancy.staffing_contact_list') !!}/' + {{ $directClientDetail->company_id }},
            "data": function(d) {
                d.search = $('#inputSearch').val(), d.client_id = $('#hid_client_id').val()
            }
        },
        "drawCallback": function(settings) {
            $('[data-toggle="tooltip1"]').tooltip();
        },

        "columnDefs": [{
                "width": "50px",
                "targets": 0
            },
            {
                "width": "100px",
                "targets": 1,
                "class": "align-items-center border-0"
            },
            {
                "width": "50px",
                "targets": 2
            },
            {
                "width": "200px",
                "targets": 3
            }
        ],
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
                searchable: false
            },
            {
                data: 'Contacts',
                name: 'Contacts',
                orderable: false,
                searchable: false
            },
            {
                data: 'Status',
                name: 'Status',
                orderable: false,
                searchable: false
            }
        ]


    });
    new $.fn.dataTable.FixedHeader(table);

    setTimeout(() => {
        $('.iti__flag-container').each(function(index) {
            var cnt = $(this).attr('data-row');
            const flagContainer = document.getElementById(`phoneDisplay_${cnt}`);
            var calling_code = $(this).attr('data-code');
            
            const flagDiv = document.createElement("div");
            flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

            const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
            const dialCodeSpan = document.createElement("span");
            dialCodeSpan.textContent = dialCode;

            // Append the flag and dial code
            flagContainer.appendChild(flagDiv);
            flagContainer.appendChild(dialCodeSpan);
        });
    }, 2000);

</script>
<script>

    // var allreq = $('#allreq').dataTable({

    //     responsive: true,
    //     processing: true,
    //     serverSide: true,
    //     destroy: true,
    //     ordering: false, //disable ordering
    //     "bLengthChange": false, //thought this line could hide the LengthMenu
    //     sDom: 'lrtip',
    //     oLanguage: {
    //         sLengthMenu: "_MENU_",
    //     },
    //     "pageLength": 25,
    //     colReorder: true,
    //     ajax: {
    //         'url': '{!! route('service_company.consultancy.allreq') !!}/' + {{ $directClientDetail->company_id }},
    //         "data": function(d) {
    //             d.search = $('#inputSearch').val(), d.client_id = $('#hid_client_id').val()
    //         }
    //     },

    //     "drawCallback": function(settings) {
    //         $('[data-toggle="tooltip1"]').tooltip();
    //     },
    //     "columnDefs": [{
    //             "width": "50px",
    //             "targets": 0
    //         },
    //         {
    //             "width": "100px",
    //             "targets": 1,
    //             "class": "d-flex align-items-center border-0"
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 2
    //         },
    //         {
    //             "width": "100px",
    //             "targets": 3
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 4
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 5
    //         },
    //         {
    //             "width": "200px",
    //             "targets": 6
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 7
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 8
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 9
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 10
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 11
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 12
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 13
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 14
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 15
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 16
    //         }
    //     ],

    //     columns: [{
    //             data: 'chkbox',
    //             name: 'chkbox',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Requisition',
    //             name: 'Requisition',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Exp',
    //             name: 'Exp',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Salary',
    //             name: 'Salary',
    //             orderable: false,
    //             searchable: false,
    //             width: '17%'
    //         },
    //         {
    //             data: 'Location',
    //             name: 'Location',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'Status',
    //             name: 'Status',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Date',
    //             name: 'Date',
    //             orderable: false,
    //             searchable: false,
    //             width: '20%'
    //         },
    //         {
    //             data: 'HM',
    //             name: 'HM',
    //             orderable: false,
    //             searchable: false,
    //             width: '20%'
    //         },

    //         {
    //             data: 'Partners',
    //             name: 'Partners',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'TOT',
    //             name: 'TOT',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SOU',
    //             name: 'SOU',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SCR',
    //             name: 'SCR',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'EVA',
    //             name: 'EVA',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'NEO',
    //             name: 'NEO',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'OFF',
    //             name: 'OFF',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'JOI',
    //             name: 'JOI',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'REJ',
    //             name: 'REJ',
    //             orderable: false,
    //             searchable: false
    //         }

    //     ]

    // });
    // new $.fn.dataTable.FixedHeader(allreq);



    setTimeout(() => {
        $('#allreq tr th').removeAttr('style');
    }, 2000);




    $(document).ready(function() {

        fetchCandidates();
        fetchCandidatesJoin();
        fetchCandidatesInverViewTracker();
        fetchstaffingpartnerJobs();

        $('#search').on('keyup', function() {
            const search = $(this).val();
            fetchCandidates(1, search);
        });

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

        $(document).on('click', '#paginationcandidatejoin .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            // Disable the clicked pagination link
            $(this).addClass('disabled');

            // Fetch data
            fetchCandidatesJoin(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationcandidatejoin .page-link').removeClass('disabled');
            }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationcandidatejoin .page-link').removeClass('disabled');
            });
        });

        $(document).on('click', '#paginationcandidateinterviewtracker .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            // Disable the clicked pagination link
            $(this).addClass('disabled');

            // Fetch data
            fetchCandidatesInverViewTracker(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationcandidateinterviewtracker .page-link').removeClass('disabled');
            }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationcandidateinterviewtracker .page-link').removeClass('disabled');
            });
        });

        $(document).on('click', '#paginationstaffingpartnerjob .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            // Disable the clicked pagination link
            $(this).addClass('disabled');

            // Fetch data
            fetchstaffingpartnerJobs(page, $('#search').val()).then(() => {
                // Re-enable the links after successful data fetch
                $('#paginationstaffingpartnerjob .page-link').removeClass('disabled');
            }).catch(() => {
                // Re-enable the links even if there's an error
                $('#paginationstaffingpartnerjob .page-link').removeClass('disabled');
            });
        });


    });

    function fetchCandidates(page = 1, search = '') {
        $('.loadingMessagecandidatetList').css({ display: 'block' });
        var clientid = '{{ $directClientDetail->company_id }}';//'{{ $directClientDetail->to_company_id }}';
        $.ajax({
            url: `{{ url('service_company/staffing-client-candidate-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.loadingMessagecandidatetList').css({ display: 'none' });
                $('#candidatetList').html(response.html);
                $('#paginationcandidate').html(response.pagination);
            }
        });
    }

    function fetchCandidatesJoin(page = 1, search = '') {
        $('.loadingMessagecandidatetListJoin').css({ display: 'block' });
        var clientid = '{{ $directClientDetail->company_id }}';//'{{ $directClientDetail->to_company_id }}';
        $.ajax({
            url: `{{ url('service_company/staffing-client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.loadingMessagecandidatetListJoin').css({ display: 'none' });
                $('#candidatetListJoin').html(response.html);
                $('#paginationcandidatejoin').html(response.pagination);
            }
        });
    }

    function fetchCandidatesInverViewTracker(page = 1, search = '') {
        $('.loadingMessagecandidatetListinterviewtracker').css({ display: 'block' });
        var clientid = '{{ $directClientDetail->company_id }}';//'{{ $directClientDetail->to_company_id }}';
        $.ajax({
            url: `{{ url('service_company/staffing-client-candidate-interview-tracker') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.loadingMessagecandidatetListinterviewtracker').css({ display: 'none' });
                $('#candidatetListinterviewtracker').html(response.html);
                $('#paginationcandidateinterviewtracker').html(response.pagination);
            }
        });
    }



    function fetchstaffingpartnerJobs(page = 1, search = '') {
        $('.loadingMessagestaffingpartnerjob').css({ display: 'block' });
        var clientid = '{{ $directClientDetail->company_id }}';
        $.ajax({
            url: `{{ route('service_company.consultancy.allreq_ajax') }}?page=${page}`,
            type: 'GET',
            data: {
                search: $('#inputSearch').val(),
                client_id:$('#hid_client_id').val(),
                id: clientid
            },
            success: function(response) {
                $('.loadingMessagestaffingpartnerjob').css({ display: 'none' });
                $('#staffingpartnerjob').html(response.html);
                $('#paginationstaffingpartnerjob').html(response.pagination);
            }
        });
    }



</script>
