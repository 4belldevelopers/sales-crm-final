@extends('layouts.superadmin_layout')
@php
    use App\Helper\GeneralMethod;
    $job_status = GeneralMethod::job_status();
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();

@endphp
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <style>
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
            #kt_activitiesDefaultPackage {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDefaultPackage {
                width: 1200px;
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
        .table-custom-class tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}

    </style>
@endpush

@section('content')
    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $status }}<span
                        class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small
                        class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
            </div>
            @if (@$showAction)
                <div class="d-flex align-items-center py-1">
                    @if (@$reject)
                        <span class="svg-icon svg-icon-primary svg-icon-1x"> Auto Approved </span>&nbsp;&nbsp;&nbsp;
                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                            @php
                                $bench_status = DB::table('global_settings')->select('public_market_job_status', 'id')->first();
                            @endphp
                            <input class="form-check-input chkStatus" type="checkbox" value="{{ @$bench_status->id }}"
                                name="staus" {{ @$bench_status->public_market_job_status ? 'checked' : '' }}>
                        </div> &nbsp;&nbsp;&nbsp;
                    @endif

                    <a href="javascript:void(0);" type="button" data-type="approve"
                        class="btn btn-sm btn-flex btn-light-primary clickbtn approve fw-bolder me-3 d-none"
                        data-toggle="modal" data-target="#approve"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
                        </span> Approve
                    </a>
                    @if (@$reject)
                        <a href="javascript:void(0);" type="button" data-type="disapprove"
                            class="btn btn-sm btn-flex btn-light-danger reject_mail disapprove fw-bolder me-3 d-none"
                            data-toggle="modal" data-target="#disapprove"> <span
                                class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
                            </span> Reject
                        </a>
                    @endif

                </div>
            @endif
        </div>
    </div>


    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">

                        <div id="benchListingTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div id="benchListingTable_processing" class="dataTables_processing card"
                                style="display: none;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 no-footer dataTable table-custom-class"
                                id="benchListingTable" role="grid" aria-describedby="benchListingTable_info"
                                style="width: 1293px;">
                                <thead>
                                    <tr class="fw-bolder text-muted" role="row">
                                        <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 40px;">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input datatable_chk chk_list" type="checkbox"
                                                    data-kt-check="true" data-kt-check-target="#myTable .form-check-input"
                                                    value="1">
                                            </div>
                        </div>
                        </th>
                        <th class="align-items-center sorting_disabled" rowspan="1" colspan="1"
                            style="width: 171px;">Jobs

                        </th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 34px;">Exp</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 137px;">Salary</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Location
                        </th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Status	
                        </th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 83px;">Date</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 63px;">Terms</th>
                        </tr>
                        </thead>
                        @php
                            $sr = 1;
                            $salaryMode = GeneralMethod::salaryMode();
                            $currencySymbole = GeneralMethod::currencySymbole();
                        @endphp
                        @if ($newJobs)
                            @foreach ($newJobs as $row)
                                <tr>
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input chk_list_item " name="chk_list_item[]"
                                                type="checkbox" data-jobid="{{ @$row->id }}"
                                                value="{{ @$row->id }}">
                                        </div>
                                    </td>
                                    <td class="bench_show_application_detail" data-id="{{ @$row->id }}">
                                        @php
                                            $bgTextDecore = $strVal = '';
                                            $str = trim(@$row->title);
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

                                            $icon =
                                                '<div data-row-id="' .
                                                $row->id .
                                                '" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="' .
                                                $row->id .
                                                '" class="show-detail">
                                          <div class="symbol-label ' .
                                                $bgTextDecore .
                                                ' border-radius-5">' .
                                                ucwords($strVal) .
                                                '</div>
                                        </a>
                                      </div>';

                                            $logo_path = ''; //url('img/client-default.png');
                                            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                                            	$logo_path = @$row->logo;
                                            }

                                            if (!empty($logo_path)) {
                                                $icon =
                                                    '<div data-row-id="' .
                                                    $row->id .
                                                    '" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 show-detail">
                                                            <a href="javascript:void(0);">
                                                                <div class="symbol-label border-radius-5">
                                                                <img src="' .
                                                    $logo_path .
                                                    '" alt="Company Logo" class="border-radius-5 w-100">
                                                                </div>
                                                            </a>
                                                        </div>';
                                            }

                                            $skilltag = '';
                                            $skilltagclass = '';
                                            if (empty($limit)) {
                                                $skillArr = DB::table('job_metronics_skills')
                                                    ->select('*')
                                                    ->where('job_id', $row->id)
                                                    ->get();
                                                if (!empty($skillArr)) {
                                                    foreach ($skillArr as $sval) {
                                                        $skilltag .=
                                                            '<span class="badge badge-light my-1">' .
                                                            ucfirst($sval->skill_name) .
                                                            '</span>  ';
                                                    }
                                                }
                                            } else {
                                                $skilltagclass = 'd-none';
                                            }
                                        @endphp


                                        <div class=" d-flex align-items-center border-0">

                                            {!! $icon !!}

                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                                    class="text-dark fw-bolder text-hover-primary fs-6 show-detail "
                                                    data-toggle="tooltip1" data-row-id="{{$row->id}}" title="" data-bs-original-title="2">
                                                    {{ ucwords(substr(@$row->title, 0, 15)) }}
                                                </a>
                                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                                    class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail" data-row-id="{{$row->id}}">
                                                    {{ $row->company_name}}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="{{ $skilltagclass }}" style="position: absolute; bottom: 0;">
                                            <span class="badge badge-light-success my-1">Skills:</span>

                                            {!! $skilltag !!}


                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ @$row->max_exp }} +</span>
                                            <span
                                                class="text-muted fw-bold text-muted d-block fs-7">{{ @$row->min_exp }}+</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $sal_mode = 0;
                                            $min_salary_currency = 0;
                                            $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary =
                                                '';
                                            $min_sal_mode = strtoupper(substr($salaryMode[$sal_mode], 0, 1));
                                            $max_sal_mode = strtoupper(substr($salaryMode[$sal_mode], 0, 1));
                                            $salary_currency = $currencySymbole[@$min_salary_currency];

                                            $cur = $min_salary_currency;
                                            $mod = $sal_mode;
                                            $currencymain = GeneralMethod::currency(); // Currency code
                                            $currency = $currencymain[$cur];
                                            $convertedAmounEctc = GeneralMethod::conversionRates($currency,$row->max_yearly,0);
                                            $convertedAmounCtc = GeneralMethod::conversionRates($currency,$row->min_yearly, 0);

                                            $ectc = $convertedAmounEctc[$mod];
                                            $ctc = $convertedAmounCtc[$mod];

                                            $max_salary = '';
                                            $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
                                            $min_salary = '';
                                            $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);
                                        @endphp

                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-dark fw-bolder fs-6">{!! $salary_currency !!} {{ $max_salary . '/' . $min_sal_mode }}</span>
                                            <span
                                                class="fw-bold text-muted d-block fs-7">{!! $salary_currency !!} {{ $min_salary . '/' . $max_sal_mode }}</span>
                                        </div>
                                    </td>
                                    <td>
                                     @php
	
                                        $country_id = $country_name = '';
                                        $jobid = $row->id;
                                        $client_loc = DB::table('client_location_jobs')
                                            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                            ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                                            ->where('client_location_jobs.job_id', $jobid)
                                            ->first();
                        
                                        $country_id = @$client_loc->country_id;
                                        if (@$country_id > 0) {
                                            $country_name = GeneralMethod::getCountryName($country_id);
                                            foreach ($country_name as $cval) {
                                                $country_name = ucwords($cval['sortname']);
                                            }
                                        }
                        
                                        $city = substr(@$client_loc->city, 0, 6);
                                        // if (@$client_loc->country_id == 101) {
                                            $city = substr(@$client_loc->city_name, 0, 6);
                                        // }
                        
                        
                        
                                        $jobLocation = DB::table('client_location_jobs')
                                            ->where('client_location_jobs.job_id', $jobid)->count();
                        
                                        $jobCitySelected = DB::table('client_city_jobs')
                                            ->where('client_city_jobs.job_id', $jobid)->count();
                        
                                            $employment_type = 'Full-time';
                                            if($row->employment_type==1){
                                                 $employment_type = 'Contract';
                                            }
                        
                                        if ($row->work_type == 'yes') {
                                           echo ' <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                                <div class="d-flex align-items-center">
                                                  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
                                                  </div>
                                                </div>
                                              </div>';
                                        } elseif ($row->work_type == 'no') {
                        
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                $client_loc = DB::table('client_city_jobs')
                                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                    ->select('city_list.city as city_name','countries.country_name')
                                                    ->where('client_city_jobs.job_id', $jobid)
                                                    ->first();
                                                    $city = substr(@$client_loc->city_name, 0, 21);
                                                    $country_name = substr(@$client_loc->country_name, 0, 6);
                        
                                               echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                          <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
                                                        </div>
                                                        
                                                      <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
                                                      </div>
                                                    </div>
                                                  </div>';
                                            } else {
                                               echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                          <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                        </div>
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
                                                  </div>
                                                </div>
                                              </div>';
                                            }
                        
                                        } elseif ($row->work_type == 'any') {
                        
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                $client_loc = DB::table('client_city_jobs')
                                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                    ->select('city_list.city as city_name','countries.country_name')
                                                    ->where('client_city_jobs.job_id', $jobid)
                                                    ->first();
                                                    $city = substr(@$client_loc->city_name, 0, 21);
                                                    $country_name = substr(@$client_loc->country_name, 0, 6);
                                                    
                                               echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                          <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
                                                        </div>
                                                        
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
                                                      </div>
                                                    </div>
                                                  </div>';
                                            } else {
                                               echo ' <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center">
                                                  <span class="text-dark fw-bolder fs-6">Remote</span>
                                                  &nbsp;|&nbsp;
                                                          <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                        </div>
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
                        
                                                  </div>
                                                </div>
                                              </div>';
                                            }
                        
                                        }
                                        @endphp
                        
                                    </td>
                                    <td>
                                        @php
                                            $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                                            $status = ucwords($row->status);
                                            $class = $job_status[$row->status];
                                            $recommendation_id = $row->recommendation_relation_id;
                                            if ($status == 'Inactive') {
                                                $status = 'Closed';
                                            }
                                            $final_status = '';
                                            if (
                                                ($row->final_status == 2 || $row->final_status == 3) &&
                                                @$row->to_company_id == auth()->user()->company_id
                                            ) {
                                                $status = 'Removed';
                                                $class = 'closed-bg';
                                            }

                                            $ulink =
                                                '<a href="javascript.void(0);" data-row-id="' .
                                                @$row->id .
                                                '" class="d-flex flex-column ' .
                                                @$class .
                                                ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                                                $status .
                                                '</a>';

                                            $recommendation_text = 'Custom';
                                            if (
                                                $recommendation_id > 0 &&
                                                @$row->to_company_id == auth()->user()->company_id
                                            ) {
                                                $recommendation_text = 'Client';
                                                $ulink =
                                                    '<a href="javascript:void(0);" class="fs-6 ' .
                                                    @$class .
                                                    ' text-hover-success">' .
                                                    $status .
                                                    '</a>';
                                            }
                                        @endphp

                                        <div class="d-flex flex-column">
                                            {!! $ulink !!}
                                            <span class="fw-bold text-muted d-block fs-7">R
                                                {{ @$row->id . ' -' . $recommendation_text }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $total_positions = '';
                                            $total_positions = number_format($row->total_positions);

                                            $created_at = '';
                                            $created_at = $row->created_at;
                                            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                                        @endphp
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ $newDate }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ $total_positions  }}
                                                Opening</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div data-row-id="{{ $row->id }}"
                                            data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}"
                                            class="client-terms-listing"><i class="fa fa-eye"
                                                style="font-size: 20px;"></i></div>

                                    </td>


                                </tr>
                                @php
                                    $sr++;
                                @endphp
                            @endforeach
                        @endif
                        <tbody>

                        </tbody>

                        </table>

                        <x-custom-pagination :paginator="$newJobs" />  
                    </div>








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



    <!--begin::Mass Mail - Modal-->
    <div class="modal fade" id="disapprove" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Bench Candidate Reject</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_reject_mail_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Mass Mail - Modal-->



    <div id="kt_activitiesBenchApplication_toggle"></div>
    <div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true"
        data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle"
        data-kt-drawer-close="#kt_activitiesBenchApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar"></div>
    </div>



    <!--start terms::Content-->

<div id="kt_activitiesTerms_toggle"></div>
<div id="kt_activitiesTerms" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTerms_toggle" data-kt-drawer-close="#kt_activitiesTerms_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_or_client">Terms & Condition</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTerms_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                     </g>
                  </svg>
               </span>
            </button>
         </div>
      </div>
      <div class="card-body position-relative px-0 py-0"> 

        <div class="card-body position-relative px-0 py-0" id="kt_terms_body">
          <div id="kt_terms_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_terms_body" data-kt-scroll-dependencies="#kt_terms_header, #kt_terms_footer" data-kt-scroll-offset="5px" style="">
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




@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>


    <script>
        $('body').on('click', '#editDefaultPackage', function() {

            $('#kt_activitiesDefaultPackage_toggle').trigger('click');
            var url = "{{ route('superadmin.defaultpackages.editDefaultPackage') }}";
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesDefaultPackage_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesDefaultPackage_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });





        $(document).ready(function() {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            $('.chkStatus').click(function() {
                var id = $(this).val();
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('superadmin.company.market-auto-approval') }}";
                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'POST',
                        'id': id
                    },
                    beforeSend: function() {
                        $('#approve').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == "success") {

                            //   $('#main_container').html(response.view);
                            swalWithBootstrapButtons.fire(
                                'Success!',
                                response.message,
                                'success'
                            )
                            setTimeout(() => {
                                // location.reload();
                            }, 2000);

                        }
                    }
                });



            });

            $('.clickbtn').click(function() {
                var datatype = $(this).attr('data-type');
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to approved this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes,  Do approved',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                        var token = "{{ csrf_token() }}";
                        var job_id = $(".chk_list_item:checked").map(function() {
                            return this.value;
                        }).get();
                        var url = "{{ route('superadmin.company.job-approval') }}";

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'job_id': job_id,
                                'datatype': datatype
                            },
                            beforeSend: function() {
                                $('#approve').html('<img src="' + baseurl +
                                    '" height="35px" width="100%">');
                            },
                            success: function(response) {
                                if (response.status == "success") {

                                    //   $('#main_container').html(response.view);
                                    swalWithBootstrapButtons.fire(
                                        'Success!',
                                        response.message,
                                        'success'
                                    )
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);

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

                });
            })








            $('.chk_list').click(function() {
                setTimeout(() => {
                    if ($(".chk_list").prop('checked') == true) {
                        $('.chk_list_item').attr('checked', 'checked');
                        $('.approve').removeClass("d-none");
                        $('.disapprove').removeClass("d-none");
                    } else {
                        $('.chk_list_item').removeAttr('checked');
                        $('.approve').addClass("d-none");
                        $('.disapprove').addClass("d-none");
                    }
                }, 500);
            });



            $('.chk_list_item').click(function() {

                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.approve').removeClass("d-none");
                        $('.disapprove').removeClass("d-none");
                    } else {
                        $('.approve').addClass("d-none");
                        $('.disapprove').addClass("d-none");
                    }
                }, 500);

            });


            $('.show-detailnot').click(function() {
                $('#kt_activitiesBenchApplication_toggle').trigger('click');
                var id = $(this).data('row-id');
                var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?bechshowtab=1";
                url     		=	url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#bench_application_show_right_bar').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $('#bench_application_show_right_bar').html(response.view);
                    }
                });
            });

           $('.client-terms-listing').click(function (){
   		$('#kt_activitiesTerms_toggle').trigger('click');		
   		  var id    		= $(this).data('row-id');
        var recommendation_relation_id    		= $(this).data('row-recommendation_relation_id');
        var url   		= "{{ route('service_company.jobs.termshmlisting',':id') }}";
        url     		= url.replace(':id', id)+"?recommendation_relation_id="+recommendation_relation_id;
   		  var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesTerms_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				       // $('#kt_activitiesTerms_body').html(response.view);
               $('#kt_activitiesTerms_body').html(response.terms);			
   						}
   	  			});
            
   	});





            $('.reject_mail').click(function(e) {

                e.preventDefault();
                $('#application_reject_mail_body_content').html('');
                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id = jobIdArr[0];
                var url = "{{ route('superadmin.company.job_reject_mail', ':id') }}";
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
                            $('#application_reject_mail_body_content').html(response.view);
                        }
                    }
                });

            });



        });
    </script>
@endpush
