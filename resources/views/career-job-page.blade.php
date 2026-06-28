<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bench Candidates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('demo/css/style-bench-candidates.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link rel="canonical" href="" />
    <link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
         html {
                scroll-behavior: auto !important; /* disable smooth hash scrolling */
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

            .custom-pagination {
        display: flex;
        gap: 5px;
        justify-content: flex-end; /* Align pagination to the left */
        margin-top: 15px;
    }

    .custom-pagination .page-link {
        padding: 0.375rem 0.75rem;
        /* border: 1px solid #ddd; */
        cursor: pointer;
        /* background-color: #f8f9fa; */
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0.475rem;
        height: 2.5rem;
        min-width: 2.5rem;
        font-weight: 500;
        font-size: 1.075rem;
        /* border-radius: 5px; */
    }

    .custom-pagination .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination-info {
        text-align: left; /* Align info to the left */
        margin-bottom: 10px;
        font-weight: bold;
    }

    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end; /* Align entire pagination block to the left */
        width: 100%;
    }
    .previous_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .next_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .active_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }

        
    </style>
</head>

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    
@endphp


<body>

    <main>
      
       
        <div class="candidate-section" id="candidate-section">
            <div class="candidate-list">



            <div class="container text-center" style="margin-top: 40px; margin-bottom: 40px;">
                <!-- Title -->
                <h2 class="fw-bold mb-3" style="color:#002855;font-size:33px">Search & Apply for Jobs</h2>
                
                <!-- Subtitle -->
                <p class="mb-4" style="font-size:16px; color:#555;">
                    Shape your future with us. Search and apply for jobs with a seamless process designed to get you hired faster.
                </p>

                <!-- Search Box -->
                <div class="d-flex justify-content-center" style="background: #fff; border-radius: 8px; box-shadow: 0 18px 40px rgba(25, 15, 9, .1); display: inline-block;padding: 10px;width: 100%;">
                    <form class="d-flex w-100" method="GET" action="">
                    <input type="text" 
                            class="form-control me-2 skillSearchValue" 
                            id="skillSearchValue" 
                            name="custom_search"
                            value="{{ request('keyword') }}"
                            placeholder="Your keyword..." 
                            style="border-radius: 8px; padding:12px;border: none !important;"/>
                    <button type="submit"  
                            class="btn btn-primary px-4 search-button" 
                            id="skillSearch" 
                            style="border-radius: 8px;width: 122px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                    </form>
                </div>

                <!-- Popular Searches -->
                {{-- <div class="mt-3" style="font-size:15px; color:#222;text-align: left;">
                    <strong>Popular Searches:</strong> 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">JavaScript</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">PHP</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Laravel</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">.NET</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Java</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">React</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Node.js</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Angular</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Spring Boot</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">Python</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">MySQL</a>, 
                    <a href="javascript:;" class="stat-label" style="text-decoration:none; color:#111822;">PostgreSQL</a>
                </div> --}}
            </div>




                <div class="d-flex mb-15" style="position: relative;">
                    <div class="text-start" style="position: absolute;left: 0;top: 9px;"> 
                        <h2>Active Jobs Opening List ({{$total}})</h2>
                    </div>
                    <div class="text-end" style="position: absolute;right: 90px;"> 
                        <form method="GET" action="">
                            <div class="search-and-filter">
                                
                                <select class="filter-select" name="custom_hiringtype" id="custom_hiringtype" onchange="this.form.submit()">
                                    <option value="All">Sort</option>
                                    <option value="1">Newest to Oldest (Date)</option>
                                    <option value="2">Oldest to Newest (Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="5">A to Z (Company Wise)</option>
                                    <option value="6">Z to A (Company Wise)</option>
                                    <option value="7">Full-Time Jobs Only</option>
                                    <option value="8">Contract Jobs Only</option>
                                </select>                
                            </div>
                        </form>
                    </div>
                </div>
            
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th>Name</th>
                            <th>Exp</th>
                            <th>Salary</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                   <tbody>
    

                    @if (count($data) > 0)
                        @foreach ($data as $rowArr)
                        @php
                        $row = (object) $rowArr;

                        $targetJobId = $row->id;


                        


                        $match = collect($row->recommendation_relation)->first(function ($item) use ($company_id) {
                            return ($item['from_company_id'] ?? null) == $company_id
                                || ($item['to_company_id'] ?? null) == $company_id;
                        });
                        $from_company_id = ($match['to_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
                        $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

                        $row->final_status   = ($match['to_company_id'] ?? null) == $company_id ? $match['final_status'] : null;

                        $final_status = collect($row->recommendation_relation)->first(function ($item) use ($targetJobId) {
                            return $item['job_id'] == $targetJobId;
                        })['final_status'] ?? null;

                       $job_assign_name = collect($row->job_assign)
                        ->first(function ($item) use ($company_id) {
                            return ($item['company_id'] ?? null) == $company_id
                                && (int)($item['spok_user_id'] ?? 0) === (int)($item['user_id'] ?? -1);
                        })['user_name'] ?? null;

                        // $row->final_status = $final_status;
                        $row->recommendation_id =$recommendation_id;
                        $row->to_company_id = $recommendation_id;
                        $row->from_company_id = $from_company_id;
                        $row->companyidcount= $row->comId;
                        $row->clients_name = @$account->company_name;

                        // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['to_company_id'] == 2)['final_status'] ?? null;

                        // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 2)['final_status'] ?? null;

                        // $final_status_three = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 3)['final_status'] ?? null;



                        @endphp
                            <tr style="height: 102px; position: relative;">
                            
                                <td class=" d-flex align-items-center">
                                    @php
                                        $clients_name = '';
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

                                        $profile_detail_class = 'show-detail';
                                        $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 30));

                                        //  dd($ddudud);

                                        $recommendation_id = '';
                                        $recommendation_id = $row->recommendation_id;
                                        $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

                                        $logo_path = '';
                                        //$logo_path				=	url('img/client-default.png');
                                        if (!empty($row->logo_logical_path)) {
                                            $logo_path = @$row->logo_logical_path;

                                            if (
                                                $recommendation_id > 0 &&
                                                @$row->to_company_id == $company_id
                                            ) {
                                                $logo_path = @$row->logo;
                                            }
                                        }

                                        if ($recommendation_id > 0 && @$row->to_company_id == @$company_id) {
                                            $recommendation_text =
                                                '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                                            $profile_detail_class = 'client-show-detail';
                                            $clients_name = ucwords(mb_substr(@$row->company_name, 0, 30));

                                            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                                                $logo_path = @$row->logo;
                                            }
                                        }

                                        if (@$row->self_company == 1) {
                                            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                                                $logo_path = @$row->logo;
                                            }
                                        }
                                        $logo_path = GeneralMethod::openCompanyDetails($row->id,$company_id);
                                        $icon =
                                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                <a href="javascript:void(0);" data-row-id="' .
                                            $row->id .
                                            '" class="' .
                                            $profile_detail_class .
                                            '">
                                                <div class="symbol-label ' .
                                            $bgTextDecore .
                                            ' border-radius-5">' .
                                            ucwords($strVal) .
                                            '</div>
                                                </a>
                                            </div>';

                                        $skilltag = '';
                                        $skilltagclass = '';
                                        if (empty($limit)) {
                                            $skillArr = DB::table('job_metronics_skills')
                                                ->select('*')
                                                ->where('job_id', $row->id)
                                                ->where('job_metronics_skills.company_id', '=', $row->comId)
                                                ->get();
                                            if (!empty($skillArr)) {
                                                foreach ($skillArr as $sval) {
                                                    $skilltag .=
                                                        '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                                                }
                                            }
                                        } else {
                                            $skilltagclass = 'd-none';
                                        }

                                        if (!empty($logo_path)) {
                                            $icon =
                                                '<div data-row-id="' .
                                                $row->id .
                                                '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' .
                                                $profile_detail_class .
                                                ' border-radius-5">
                                                                    <a href="javascript:void(0);">
                                                                        <div class="symbol-label border-radius-5">
                                                                            <img src="' .
                                                str_replace('index.php', '', @$logo_path) .
                                                '" alt="Company Logo" class="border-radius-5 w-100">
                                                                        </div>
                                                                    </a>
                                                                </div>';
                                        }

                                        echo $icon .
                                            '<div class="d-flex flex-column">
                                                <a href="javascript:void(0);" data-row-id="' .
                                            $row->id .
                                            '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                                            $profile_detail_class .
                                            '"  data-toggle="tooltip1" title="' .
                                            ucwords(@$row->title) .
                                            '" >' .
                                            ucwords(mb_substr(@$row->title, 0, 30)) .
                                            '</a> 
                                                <a href="javascript:void(0);" data-row-id="' .
                                            $row->id .
                                            '" class="fw-bold text-muted d-block fs-7 text-hover-primary ' .
                                            $profile_detail_class .
                                            '">' .
                                            $clients_name .
                                            '</a>
                                            </div>

                                            <div class="' .
                                            $skilltagclass .
                                            '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                                                <span class="badge badge-light-success my-1">Skills:</span>

                                                            ' .
                                            $skilltag .
                                            '

                                                    </div>';
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo '<div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">' .
                                            @$row->max_exp .
                                            ' y</span>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                            @$row->min_exp .
                                            ' y</span>
                                    </div>';
                                    @endphp
                                </td>
                                <td>
                                    @php
                                            $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary =
                                                '';
                                            $min_sal_mode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode], 0, 1));
                                            $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));
                                            $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                                            $max_salary_currency = $currencySymbole[@$row->max_salary_currency];
                                            $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                                            $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
                                            echo '<div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">' .
                                                $min_salary_currency .
                                                $max_salary .
                                                '/' .
                                                $min_sal_mode .
                                                '</span>                    
                                            <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                                $min_salary_currency .
                                                $min_salary .
                                                '/' .
                                                $max_sal_mode .
                                                '</span>
                                        </div>';
                                    @endphp
                                </td>
                                <td>
                                    @php

                                        $country_id = $country_name = '';
                                        $jobid = $row->id;
                                        $client_loc = DB::table('client_location_jobs')
                                            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                            ->select(
                                                'client_location.address',
                                                'client_location.city',
                                                'client_location.postcode',
                                                'client_location.country_id',
                                                'city_list.city as city_name',
                                            )
                                            ->where('client_location_jobs.job_id', $jobid)
                                            ->first();

                                        $country_id = @$client_loc->country_id;
                                        if (@$country_id > 0) {
                                            $country_name = GeneralMethod::getCountryName($country_id);
                                            foreach ($country_name as $cval) {
                                                $country_name = ucwords($cval['sortname']);
                                            }
                                        }

                                        $city = mb_substr(@$client_loc->city, 0, 6);
                                        // if (@$client_loc->country_id == 101) {
                                        $city = mb_substr(@$client_loc->city_name, 0, 6);
                                        // }

                                        $jobLocation = DB::table('client_location_jobs')
                                            ->where('client_location_jobs.job_id', $jobid)
                                            ->count();

                                        $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $jobid)->count();

                                        $employment_type = 'Full-time';
                                        if ($row->employment_type == 1) {
                                            $employment_type = 'Contract';
                                        }

                                        if ($row->work_type == 'yes') {
                                            echo ' <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                                <div class="d-flex align-items-center">
                                                <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                ucwords(@$employment_type) .
                                                '" >' .
                                                ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                '</span>
                                                </div>
                                                </div>
                                            </div>';
                                        } elseif ($row->work_type == 'no' || $row->work_type =='other') {
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                $client_loc = DB::table('client_city_jobs')
                                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                    ->select('city_list.city as city_name', 'countries.country_name')
                                                    ->where('client_city_jobs.job_id', $jobid)
                                                    ->first();
                                                $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                $country_name = mb_substr(@$client_loc->country_name, 0, 6);

                                                echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-dark fw-bolder fs-6">' .
                                                    ucwords(mb_substr(@$city, 0, 10)) .
                                                    '</span>
                                                        </div>
                                                        
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                    ucwords(@$employment_type) .
                                                    '" >' .
                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                    '</span>
                                                    </div>
                                                    </div>
                                                </div>';
                                            } else {
                                                echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                        </div>
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                    ucwords(@$employment_type) .
                                                    '" >' .
                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                    '</span>
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
                                                    ->select('city_list.city as city_name', 'countries.country_name')
                                                    ->where('client_city_jobs.job_id', $jobid)
                                                    ->first();
                                                $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                $country_name = mb_substr(@$client_loc->country_name, 0, 6);

                                                echo ' <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-dark fw-bolder fs-6">' .
                                                    ucwords(mb_substr(@$city, 0, 10)) .
                                                    '</span>
                                                        </div>
                                                        
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                    ucwords(@$employment_type) .
                                                    '" >' .
                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                    '</span>
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
                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                    ucwords(@$employment_type) .
                                                    '" >' .
                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                    '</span>

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
                                        $recommendation_id = $row->recommendation_id;
                                        if ($status == 'Inactive') {
                                            $status = 'Closed';
                                        }
                                        $final_status = '';
                                        if (
                                            ($row->final_status == 2 || $row->final_status == 3) &&
                                            @$row->to_company_id == @$company_id
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

                                        $recommendation_text = __('messages.internal_tag');
                                        if ($recommendation_id > 0 && @$row->to_company_id == @$company_id) {
                                            $recommendation_text = __('messages.clinet_tag');
                                            $ulink =
                                                '<a href="javascript:void(0);" class="fs-6 ' .
                                                @$class .
                                                ' text-hover-success">' .
                                                $status .
                                                '</a>';
                                        }

                                        echo '<div class="d-flex flex-column">
                                        ' .
                                            $ulink .
                                            '
                                        <span class="fw-bold text-muted d-block fs-7">R' .
                                            @$row->id .
                                            ' -' .
                                            $recommendation_text .
                                            '</span>
                                        </div>';
                                    @endphp
                                </td>
                                <td>
                                    @php

                                        $total_positions = '';
                                        $total_positions = number_format($row->total_positions);
                                        $hiringManager = '--------';
                                        // dd($row);
                                        if(!empty($job_assign_name)){
                                            $hiringManager = ucwords($job_assign_name);
                                        }

                                        $created_at = '';
                                        $created_at = $row->created_at;
                                        $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                                        echo '<div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">' .
                                            $newDate .
                                            '</span>
                                            <span class="text-muted fw-bold d-block fs-7">' .
                                            $hiringManager .
                                            ' </span>
                                        </div>';

                                    @endphp
                                </td>
                                 <td>
                                     <div class="d-flex flex-column"> 
                                            <a href="{{ url('job-details/' .(@$row->id) . '/'.$company_id) }}" class="btn btn-sm btn-primary openSideBar" data-row-source_from_type="2" data-row-id="{{$row->id}}" data-row-jobid="" style="width: 115px;">View Details</a> 
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        @else
                            <tr style="height: 102px; position: relative; background: #fffcfc7a;">

                                <td colspan="7" style="text-align: center; color: black;">
                                    No data available in table
                                </td>

                            </tr>
                    @endif
                               
                   

                    </tbody>
                </table>
               <?php
$totalPages = $last_page;
$currentPage = $current_page;

// How many pages to show on each side of current
$side = 2;

// Always show first and last page
?>
<div class="pagination-wrapper">
    <div class="custom-pagination">

        <!-- Previous Button -->
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>" class="page-link">Previous</a>
        <?php else: ?>
            <span class="page-link disabled">Previous</span>
        <?php endif; ?>

        <!-- First Page -->
        <?php if ($currentPage > ($side + 2)): ?>
            <a href="?page=1" class="page-link">1</a>
            <span class="page-link">...</span>
        <?php endif; ?>

        <!-- Page Numbers Around Current -->
        <?php
        $start = max(1, $currentPage - $side);
        $end   = min($totalPages, $currentPage + $side);

        for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <a href="?page=<?= $i ?>" class="page-link active"><?= $i ?></a>
            <?php else: ?>
                <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- Last Page -->
        <?php if ($currentPage < $totalPages - ($side + 1)): ?>
            <span class="page-link">...</span>
            <a href="?page=<?= $totalPages ?>" class="page-link"><?= $totalPages ?></a>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Next</a>
        <?php else: ?>
            <span class="page-link disabled">Next</span>
        <?php endif; ?>

    </div>
</div>

                
            </div>

            </div>
        </div>
    </main>


    
<script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}{{ TM_VERSION }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script>
<script src="{{ asset('front/assets/js/number-to-words.js') }}"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).on('click', '.stat-label', function(e) {
        var skill = $(this).text();
       $('.skillSearchValue').val(skill);
     });
  function sendHeight() {
    window.parent.postMessage(
      { frameHeight: document.body.scrollHeight },
      "*"
    );
  }

  // Send height on load
  window.onload = sendHeight;

  // Also send height if page resizes (content changes)
  window.onresize = sendHeight;
</script>



</body>

</html>
