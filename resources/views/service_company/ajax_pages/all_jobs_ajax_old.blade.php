
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
    @foreach ($data as $row)
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
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
                            $row->thirdparty_counter > 0 &&
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

                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
                    
                    $recommendation_id = $row->recommendation_id;
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
                        $from_company_id = $row->from_company_id;

                        $partnersCount = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $from_company_id)
                            ->where('users.company_id', '=', $from_company_id)
                            ->count();
                        // dd($partnersCount);
                        $partnersData = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $from_company_id)
                            ->where('users.company_id', '=', $from_company_id)
                            ->orderBy('users.name', 'ASC')
                            ->skip(0)
                            ->take(1)
                            ->get();
                        // dd($partnersData);

                        $jobId = $row->id;
                        $html = $temp = '';
                        $showCount = 0;
                        $html .=
                            '<div class="d-flex flex-column">
					 <div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover client-hm-listing">';
                        foreach ($partnersData as $pval) {
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                            $image_path = ''; //url('public/avatar.png')

                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $image_path = @$pval->image;
                            }
                            // if($showCount < 1){

                            if (!empty($image_path)) {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->name .
                                    '">
						    <img class="me-2" alt="Pic" src="' .
                                    @$image_path .
                                    '">
						</div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->name) .
                                    '" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->name, 0, 1) .
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
                        $partnersCount = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $company_id)
                            ->where('users.company_id', '=', $company_id)
                            ->count();
                        $partnersData = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $company_id)
                            ->where('users.company_id', '=', $company_id)
                            ->whereColumn('job_assign.user_id', 'job_assign.spok_user_id')
                            ->orderBy('users.name', 'ASC')
                            ->skip(0)
                            ->take(1)
                            ->get();
                            

                        $jobId = $row->id;
                        $html = $temp = '';
                        $showCount = 0;
                        $html .=
                            '<div class="d-flex flex-column">
					<div data-row-id="' .
                            $row->id .
                            '"  class="symbol-group symbol-hover hm-listing">';
                        foreach ($partnersData as $pval) {
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                            $image_path = ''; //url('public/avatar.png');
                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $image_path = @$pval->image;
                            }

                            // if($showCount < 1){
                            if (!empty($image_path)) {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->name .
                                    '">
						     <img class="me-2" alt="Pic" src="' .
                                    @$image_path .
                                    '">
						</div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->name) .
                                    '" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->name, 0, 1) .
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
                        $partnersCount = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $company_id)
                            ->where('users.company_id', '=', $company_id)
                            ->count();

                        $partnersData = DB::table('job_assign')
                            ->join('users', 'users.id', '=', 'job_assign.user_id')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('job_assign.job_id', '=', $row->id)
                            ->where('job_assign.company_id', '=', $company_id)
                            ->where('users.company_id', '=', $company_id)
                            ->whereColumn('job_assign.user_id', 'job_assign.spok_user_id')
                            ->orderBy('users.name', 'ASC')
                            ->skip(0)
                            ->take(1)
                            ->get();

                        $jobId = $row->id;

                        $html .=
                            '<div class="d-flex flex-column">
                    						<div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover team-listing">';
                        $temp = '';
                        foreach ($partnersData as $pval) {
                            $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                            $logo_path = '';
                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $logo_path = @$pval->image;
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                    @$pval->name .
                                    '">
                            						<img alt="Pic" src="' .
                                    @$logo_path .
                                    '">
                          						</div>';
                            } else {
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                    ucwords(@$pval->name) .
                                    '" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 ' .
                                    $bgTextDecore .
                                    ' border-radius-100">' .
                                    mb_substr($pval->name, 0, 1) .
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

                        $recommendedDataListCnt = DB::table('recommendation_relation');
                        $recommendedDataListCnt = $recommendedDataListCnt->select(
                            'companies.id',
                            'companies.company_name',
                            'companies.logo',
                        );
                        $recommendedDataListCnt = $recommendedDataListCnt->join(
                            'companies',
                            'companies.id',
                            'recommendation_relation.to_company_id',
                        );
                        $recommendedDataListCnt = $recommendedDataListCnt->join(
                            'jobs',
                            'jobs.id',
                            'recommendation_relation.job_id',
                        );
                        //$recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
                        /*$recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('job_applications', function($join) use ($jobId)
											   {
													   $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
													   $join->on('job_applications.job_id', '=', 'jobs.id' );
											   });*/
                        $recommendedDataListCnt = $recommendedDataListCnt->where('companies.id', '!=', $company_id);
                        $recommendedDataListCnt = $recommendedDataListCnt->where(
                            'recommendation_relation.final_status',
                            1,
                        );
                        $recommendedDataListCnt = $recommendedDataListCnt->where(
                            'recommendation_relation.job_id',
                            '=',
                            $jobId,
                        );
                        $recommendedDataListCnt = $recommendedDataListCnt->groupBy('companies.id');
                        $recommendedDataListCnt = $recommendedDataListCnt->count();

                        $recommendedDataList = DB::table('recommendation_relation');
                        $recommendedDataList = $recommendedDataList->select(
                            'companies.id',
                            'companies.company_name',
                            'companies.logo',
                        );
                        $recommendedDataList = $recommendedDataList->join(
                            'companies',
                            'companies.id',
                            'recommendation_relation.to_company_id',
                        );
                        $recommendedDataList = $recommendedDataList->join(
                            'jobs',
                            'jobs.id',
                            'recommendation_relation.job_id',
                        );
                        //$recommendedDataList 	=	$recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
                        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
                            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
                            $join->on('job_applications.job_id', '=', 'jobs.id');
                        });
                        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', $company_id);
                        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
                        $recommendedDataList = $recommendedDataList->where(
                            'recommendation_relation.job_id',
                            '=',
                            $jobId,
                        );
                        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
                        $recommendedDataList = $recommendedDataList->skip(0)->take(3)->get();

                        $html =
                            '<div class="d-flex flex-column">
			        <div data-row-id="' .
                            $row->id .
                            '" class="symbol-group symbol-hover spartner-listing">';

                        foreach ($recommendedDataList as $pval) {
                            $image_path = url('img/client-default.png');
                            if (!empty(@$pval->logo) && strlen(@$pval->logo) > 10) {
                                $image_path = url('public/user-uploads/company-logo/' . @$pval->logo);
                            }

                            $html .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                @$pval->company_name .
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
                    $applicationCounter = DB::table('add_job_wise_condidates as job_applications')
                        ->select('job_applications.id')
                        ->where('job_applications.job_id', '=', $row->id)
                        ->where('job_applications.thirdparty_company_id', '=', 0)
                        ->orWhere(function ($query) use ($job_id) {
                            return $query
                                ->where('job_applications.thirdparty_company_id', '>', 0)
                                ->where('job_applications.job_id', '=', $job_id)
                                ->where('job_applications.master_status_id', '!=', 8)
                                ->where('job_applications.master_status_id', '!=', 9);
                        })
                        ->count();

                    $inreview_counter = DB::table('add_job_wise_condidates as job_applications')
                        ->select('job_applications.id')
                        ->where('job_applications.job_id', '=', $row->id)
                        ->where('job_applications.thirdparty_company_id', '=', 0)
                        ->where('job_applications.master_status_id', '=', 8)
                        ->count();

                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all',
                        );

                        $applicationCounter = DB::table('add_job_wise_condidates as job_applications')
                            ->select('job_applications.id')
                            ->where('job_applications.job_id', '=', $row->id)
                            ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                            ->count();
                        $inreview_counter = DB::table('add_job_wise_condidates as job_applications')
                            ->select('job_applications.id')
                            ->where('job_applications.job_id', '=', $row->id)
                            ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                            ->where('job_applications.master_status_id', '=', 8)
                            ->count();
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
            <td>
                @php
                    $job_id = '';
                    $master_status_id = 1;
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = $row->Sourcing;
                    $url_path = $total_application = $inreview_counter = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($job_id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = $row->thirdparty_Sourcing;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php

                    $master_status_id = 2;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = 0;
                    $cnt = $row->Screening;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($job_id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = $row->thirdparty_Screening;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php

                    $master_status_id = 3;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = 0;
                    $cnt = $row->Interview;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($job_id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = $row->thirdparty_Interview;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php

                    $master_status_id = 4;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = 0;
                    $cnt = $row->Negotiation;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($job_id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = number_format($row->thirdparty_Negotiation);
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php
                    $master_status_id = 5;
                    $cnt = 0;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = $row->Offered;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($job_id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = $row->thirdparty_Offered;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php

                    $master_status_id = 6;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = 0;
                    $cnt = $row->Joined;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($row->id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                    if ($row->recommendation_id > 0 && @$row->to_company_id === auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                       
                        // $cnt = $row->thirdparty_Joined;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>
            <td>
                @php
                    $master_status_id = 7;
                    $job_id = '';
                    $job_id = $row->id;
                    $cnt = 0;
                    $cnt = $row->Rejected;
                    $url_path = '';
                    $url_path = url(
                        '/service_company/apptracker/' .
                            GeneralMethod::value_encrypt($row->id) .
                            '/' .
                            $master_status_id,
                    );
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id); 
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $url_path = url(
                            '/service_company/apptracker/clientapp/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        // $cnt = $row->thirdparty_Rejected;
                        $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                    }
                    
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            $url_path .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }

                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="17" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
