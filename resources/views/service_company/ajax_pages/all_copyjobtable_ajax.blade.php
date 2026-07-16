@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;

    // $recommendation_id = collect($row->recommendation_relation)->contains('to_company_id', auth()->user()->company_id);
    $targetJobId = $row->id;

   

    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;

    // $match = collect($row->recommendation_relation)->first(function ($item) use ($company_id) {
    //     return ($item['from_company_id'] ?? null) == $company_id
    //         || ($item['to_company_id'] ?? null) == $company_id;
    // });
    // $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
    // $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;



    // $final_status = collect($row->recommendation_relation)->first(function ($item) use ($targetJobId) {
    //     return $item['job_id'] == $targetJobId;
    // })['final_status'] ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;

    // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['to_company_id'] == 2)['final_status'] ?? null;

    // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 2)['final_status'] ?? null;

    // $final_status_three = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 3)['final_status'] ?? null;

  

    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_copy_item" type="checkbox" value="' . @$row->id . '" />
                  </div>';
                @endphp
            </td>
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
                    $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 20));

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
                            @$row->to_company_id == auth()->user()->company_id
                        ) {
                            $logo_path = @$row->logo;
                        }
                    }

                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
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
                    $logo_path = GeneralMethod::companyDetails($row->id);
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
                        
                        if(collect($row->job_metronics_skills)->isNotEmpty()) {
                           $skillArr = $row->job_metronics_skills;
                        }
                        
                        if (!empty($skillArr)) {
                            foreach ($skillArr as $sval) {
                                $sval = (object) $sval;
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
                        ucwords(mb_substr(@$row->title, 0, 20)) .
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
                    
                    $client_city_jobs = $row->client_city_jobs;
                    $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

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

                                $firstCity = collect($client_city_jobs)->first();
                           
                                $cityName = $firstCity['city_name'] ?? null;
                                $city = mb_substr(@$cityName, 0, 21);
                                $countryName = $firstCity['country_name'] ?? null;
                                $country_name = mb_substr(@$countryName, 0, 6);

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

                                $firstCity = collect($client_city_jobs)->first();
                           
                                $cityName = $firstCity['city_name'] ?? null;
                                $city = mb_substr(@$cityName, 0, 21);
                                $countryName = $firstCity['country_name'] ?? null;
                                $country_name = mb_substr(@$countryName, 0, 6);

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

                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
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

                    $recommendation_text = __('messages.internal_tag');
                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
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
                    <span class="fw-bold text-muted d-block fs-7">' .
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
                    if(count($row->client_contact_info_jobs)>0){
                        $hiringManager = $row->client_contact_info_jobs[0]['contact_person_name'];
                    }

                    $created_at = '';
                    $created_at = $row->created_at;
                    $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                    echo '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' .
                        $newDate .
                        '</span>
                        <span class="text-muted fw-bold d-block fs-7">' .
                        ucwords(mb_substr(@$hiringManager, 0, 10)) .
                        ' </span>
                    </div>';

                @endphp
            </td>
            <td>
                @php
                    $html = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        // Direct Client Job
                        $jobId = $row->id;
                        $from_company_id = $row->from_company_id;
                        $job_assign = $row->job_assign;
                        $jobAssign = collect($job_assign);

                       $partnersCount = $jobAssign->where('company_id', $from_company_id)->where('job_id', $jobId)->count();

                       $partnersData = $jobAssign->where('company_id', $from_company_id)->where('job_id', $jobId)->take(1)->values();

                        $html = $temp = '';
                        $showCount = 0;
                        $html .=
                            '<div class="d-flex flex-column">
                    <div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover client-hm-listing">';
                        foreach ($partnersData as $pval) {
                             $pval = (object) $pval;
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->user_name);
                            $image_path = ''; //url('public/avatar.png')

                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $image_path = @$pval->image;
                            }
                            // if($showCount < 1){

                            if (!empty($image_path)) {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->user_name .
                                    '">
                            <img class="me-2" alt="Pic" src="' .
                                    @$image_path .
                                    '">
                        </div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->user_name) .
                                    '" >
                            <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                            <a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
                                <div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->user_name, 0, 1) .
                                    '</div>
                            </a>		 
                            </div>
                            </div>';
                            }
                            // }
                            $showCount++;
                        }
                        if (in_array('add_team_on_requisition', $userPermissions)) {
                            $html .=
                                '<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More Comtact list.."><span class="symbol-label font-weight-bold">' .
                                $partnersCount .
                                '+</span></div>';
                        }
                        $html .= '</div></div><div class="fs-7 fw-bold text-muted">Client</div>';
                    } else {
                        //	Custom Client Job
    
                        $jobId = $row->id;
                        $job_assign = $row->job_assign;
                        $jobAssign = collect($job_assign);

                        $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)->count();

                        $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();
                            
                        $html = $temp = '';
                        $showCount = 0;
                        $html .=
                            '<div class="d-flex flex-column">
                    <div data-row-id="' .
                            $row->id .
                            '"  class="symbol-group symbol-hover hm-listing">';
                        foreach ($partnersData as $pval) {
                            $pval = (object) $pval;
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->user_name);
                            $image_path = ''; //url('public/avatar.png');
                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $image_path = @$pval->image;
                            }

                            // if($showCount < 1){
                            if (!empty($image_path)) {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->user_name .
                                    '">
                            <img class="me-2" alt="Pic" src="' .
                                    @$image_path .
                                    '">
                        </div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->user_name) .
                                    '" >
                            <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                            <a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
                                <div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->user_name, 0, 1) .
                                    '</div>
                            </a>		 
                            </div>
                            </div>';
                            }
                            // }
                            $showCount++;
                        }
                        $html .=
                            '<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold" >' .
                            $partnersCount .
                            '+</span></div>';
                        $html .= '</div></div><div class="fs-7 fw-bold text-muted">Team</div>';
                    }
                    echo $html;

                @endphp
            </td>
            <td>
                @php

                    $html = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        // Direct Client Job

                        $jobId = $row->id;
                        $job_assign = $row->job_assign;
                        $jobAssign = collect($job_assign);

                        $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)->count();

                        $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();

            
                        $html .=
                            '<div class="d-flex flex-column">
                                            <div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover team-listing">';
                        $temp = '';
                        foreach ($partnersData as $pval) {
                            $pval = (object) $pval;
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->user_name);
                            $logo_path = '';
                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $logo_path = @$pval->image;
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->user_name .
                                    '">
                                                    <img alt="Pic" src="' .
                                    @$logo_path .
                                    '">
                                                </div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->user_name) .
                                    '" >
                            <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                            <a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
                                <div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->user_name, 0, 1) .
                                    '</div>
                            </a>		 
                            </div>
                            </div>';
                            }
                        }
                        $html .=
                            '<div data-row-id="' .
                            $row->id .
                            '"   class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More team list..">
                                                        <span class="symbol-label font-weight-bold">' .
                            $partnersCount .
                            '+</span>
                                                    </div>';
                        $html .= '</div>
                                        </div><div class="fs-7 fw-bold text-muted">Team</div>';
                    }
                    //	Custom Client Job
                    else {
                        $jobId = '';
                        $jobId = $row->id;

                        $recommendation_relation = $row->recommendation_relation;
                        $recommendationRelation = collect($recommendation_relation);
                        // dd($recommendationRelation);

                        $recommendedDataListCnt = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $jobId)->where('from_status', 1)->count();


                        $recommendedDataList = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $jobId)->where('from_status', 1)->take(1)->values();

                        $html =
                            '<div class="d-flex flex-column">
                                <div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover spartner-listing">';

                        foreach ($recommendedDataList as $pval) {
                            $pval = (object) $pval;
                            $company_details = GeneralMethod::companyDetailsGet($pval->to_company_id);
                            $image_path = url('img/client-default.png');
                            if (!empty(@$company_details->logo) && strlen(@$company_details->logo) > 10) {
                                $image_path = url('public/user-uploads/company-logo/' . @$company_details->logo);
                            }

                            $html .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                @$company_details->company_name .
                                '"><img alt="Pic" src="' .
                                @$image_path .
                                '"></div>';
                        }
                        
                        $html .=
                            '
                        <div data-row-id="' .
                            $row->id .
                            '" class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list..">
                            <span class="symbol-label font-weight-bold" >' .
                            @$recommendedDataListCnt .
                            '+</span>
                        </div>
                        </div>
                    </div><div class="fs-7 fw-bold text-muted">'.__('messages.Agency').'</div>';
                    }

                    echo $html;

                @endphp
            </td>
            <td>
                @php

                        $url_path = $total_application = $inreview_counter = '';
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/all');

                        $job_id = $row->id;
                        
                        $applicationCounter = GeneralMethod::statusCountAll($row->id);
                        $inreview_counter = GeneralMethod::statusCountAllInRe($row->id);


                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all',
                            );

                            $applicationCounter = GeneralMethod::statusCountThirdpartyAll($row->to_company_id,$row->id);
                            $inreview_counter = GeneralMethod::statusCountAllThirdpartyInRe($row->to_company_id,$row->id);
                            
                        }
                        $applicationCounter = number_format($applicationCounter);
                        $inreview_counter = number_format($inreview_counter);

                        echo '<div class="d-flex flex-column">
                            <a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$applicationCounter .
                            '</a>
                            <a href="' .
                            $url_path .
                            '" class="text-muted fw-bold d-block fs-7 text-hover-primary">' .
                            $inreview_counter .
                            '</a>
                        </div>';
                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="17" style="text-align: center; color: black;">
            No data available in table
            <br>
             @if(in_array("add_jobs", $userPermissions))
            <a  href="{{url('service_company/jobs/create')}}" target="_blank" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3  py-4" id="" style="width: 10%; margin-top: 5px;">
                <span class="svg-icon svg-icon-primary svg-icon-1x" style="display: contents;">
         <span class="svg-icon svg-icon-primary svg-icon-1x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
               <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
               </g>
            </svg>
         </span>
           <span>Add Job</span> </span> </a>
           @endif 
        </td>

    </tr>
@endif