@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $jobDetails = GeneralMethod::jobDetails($row->jobId);
    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$row->jobId);

    $from_company_id = $relationget->from_company_id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $recommendation_id = $relationget->id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $jobDetails->comId;
    $row->clients_name = $jobDetails->client_name;

    
    
    $job_assign = $jobDetails->job_assign;
    $jobAssign = collect($job_assign);


    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' .
                        $jobDetails->id .
                        '" />
                  </div>';
                @endphp
            </td>
            <td class=" d-flex align-items-center">
                @php
                    $clients_name = '';
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$jobDetails->title);
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
                    $clients_name = ucwords(mb_substr(@$jobDetails->clients_name, 0, 15));

                    $logo_path = '';
                    //$logo_path				=	url('img/client-default.png');
                    if (!empty($jobDetails->logo_logical_path)) {
                        $logo_path = @$jobDetails->logo_logical_path;
                    }

                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $recommendation_text =
                            '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                        $profile_detail_class = 'client-show-detail';
                        $clients_name = ucwords(mb_substr(@$jobDetails->company_name, 0, 15));

                        if (!empty(@$jobDetails->logo) && strlen(@$jobDetails->logo) > 10) {
                            $logo_path = @$jobDetails->logo;
                        }
                    }

                    if (@$jobDetails->self_company == 1) {
                        if (!empty(@$jobDetails->logo) && strlen(@$jobDetails->logo) > 10) {
                            $logo_path = @$jobDetails->logo;
                        }
                    }
                    $logo_path = @$jobDetails->logo;
                    $clients_name = ucwords(mb_substr(@$jobDetails->company_name, 0, 15));

                    $icon =
                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' .
                        $jobDetails->id .
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

                    if (!empty($logo_path)) {
                        $icon =
                            '<div data-row-id="' .
                            $jobDetails->id .
                            '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' .
                            $profile_detail_class .
                            ' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="' .
                            @$logo_path .
                            '" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                    }

                    $skilltag = '';
                    $skillArr = [];
                    $skillArr = DB::table('job_application_skills')
                        ->select('*')
                        ->where('job_application_id', $bench_id)
                        ->pluck('skills')->toArray();
                        // dd($skillArr);
                        $skillArrc_skills = array_column($jobDetails->job_metronics_skills, 'skill_name');
                        //  dd($skillArr, $skillArrc_skills);
                        // $skillArrc_skills = explode(',', $row->c_skills);
                        $percentage =  GeneralMethod::skill_percentage($jobDetails->id,$skillArr);

                    if (!empty($skillArrc_skills)) {
                        foreach ($skillArrc_skills as $sval) {
                            $skilltag .=
                                '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                        }
                    }

                    echo $icon .
                        '<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' .
                        $jobDetails->id .
                        '"   data-row-job_applications_id="' .
                        $bench_id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $profile_detail_class .
                        '"  data-toggle="tooltip1" title="' .
                        ucwords(@$jobDetails->title) .
                        '" >' .
                        ucwords(mb_substr(@$jobDetails->title, 0, 15)) .
                        '</a> 
					<a href="javascript:void(0);" data-row-id="' .
                        $jobDetails->id .
                        '"   data-row-job_applications_id="' .
                        $bench_id .
                        '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' .
                        $profile_detail_class .
                        '">' .
                        $clients_name .
                        '</a>
									</div>
										<div style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
														<span class="badge badge-light my-1">'.$percentage.'</span>
													
													' .
                        $skilltag .
                        '
															
													
														</div> 
									';

                @endphp
            </td>
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' .
                        @$jobDetails->max_exp .
                        '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        @$jobDetails->min_exp .
                        '+</span>
                  </div>';
                @endphp

            </td>
            <td>
                @php

                    $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary =
                        '';
                    $min_sal_mode = strtoupper(mb_substr($salaryMode[$jobDetails->min_sal_mode], 0, 1));
                    $max_sal_mode = strtoupper(mb_substr($salaryMode[$jobDetails->max_sal_mode], 0, 1));
                    $min_salary_currency = $currencySymbole[@$jobDetails->min_salary_currency];
                    $max_salary_currency = $currencySymbole[@$jobDetails->max_salary_currency];
                    $max_salary = GeneralMethod::thousandsCurrencyFormat($jobDetails->max_salary);
                    $min_salary = GeneralMethod::thousandsCurrencyFormat($jobDetails->min_salary);
                    echo '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' .
                        $min_salary_currency .
                        $max_salary .
                        '/' .
                        $max_sal_mode .
                        '</span>                    
                    <span class="fw-bold d-block fs-7">' .
                        $min_salary_currency .
                        $min_salary .
                        '/' .
                        $min_sal_mode .
                        '</span>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $country_id = $country_name = '';
                    $jobid = $jobDetails->id;
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
                    if ($jobDetails->employment_type == 1) {
                        $employment_type = 'Contract';
                    }
                    // dd($jobCitySelected,$row->work_type);

                    if ($jobDetails->work_type == 'yes') {
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
                    } elseif ($jobDetails->work_type == 'no' || $jobDetails->work_type =='other') {
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
                    } elseif ($jobDetails->work_type == 'any') {
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
                    $status = ucwords($jobDetails->status);
                    $class = $job_status[$jobDetails->status];

                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }

                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }
                    $recommendation_text = __('messages.internal_tag');
                    if ($jobDetails->comId != auth()->user()->company_id) {
                        $recommendation_text = __('messages.clinet_tag');
                    }

                    $ulink =
                        '<a href="javascript.void(0);" data-row-id="' .
                        @$jobDetails->id .
                        '" class="d-flex flex-column ' .
                        @$class .
                        ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                        $status .
                        '</a>';
                    echo '<div class="d-flex flex-column">
                       ' .
                        $ulink .
                        '
                      <span class="fw-bold text-muted d-block fs-7">' .
                        @$jobDetails->id .'-'.$recommendation_text.
                        '</span>
                    </div>';

                @endphp
            </td>
            <td>
                @php

                    $total_positions = '';
                    $total_positions = number_format($jobDetails->total_positions);

                    
                    $hiringManager = '--------';
                    if(count($jobDetails->client_contact_info_jobs)>0){
                        $hiringManager = $jobDetails->client_contact_info_jobs[0]['contact_person_name'];
                    }

                    if ($jobDetails->comId != auth()->user()->company_id) {
                        $jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $jobDetails->comId)
                        ->filter()
                        ->map(fn ($c) => ($c))
                        ->values()
                        ->all();
                        if(count($jobAssignListCaptain)>0 && $jobDetails->comId != auth()->user()->company_id){
                            $hiringManager = ucfirst($jobAssignListCaptain[0]['user_name']);
                        }
                        
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
                        '</span>
                    </div>';
                @endphp
            </td>
            <td>
                @php
                    $schedule_date = $className = $schedule_time = '';
                    
                    $html = '';
                    if ($row->schedule_date) {
                        $schedule_date = @$row->schedule_date;
                        $schedule_date = date('d M, Y', strtotime($schedule_date));
                        $timezone_abbreviation = ($row->timezone_abbreviation) ? ' ('.$row->timezone_abbreviation.')' : '';
                        $schedule_time = @$row->schedule_to_time. ' '.$timezone_abbreviation;
                        // $schedule_time = Carbon::parse($schedule_date)->format('h:i A');
                    } else {
                        $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                    }
                    
                    echo '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . @$schedule_date .'</span>
                        <span class="text-muted fw-bold d-block fs-7' . @$schedule_time .' px-1">' . @$schedule_time .'</span>
                        </div>';
                @endphp
            </td>
            <td>
                @php
                    $name = '';

                    $job_wise_condidates = DB::table('add_job_wise_condidates')
                    ->join('master_status','master_status.id', '=','add_job_wise_condidates.master_status_id')
                    ->select('add_job_wise_condidates.master_status_id','add_job_wise_condidates.child_status_id','master_status.name','master_status.css_style')
                    ->where('add_job_wise_condidates.job_id','=',$jobDetails->id)
                    ->where('add_job_wise_condidates.job_application_id','=',$bench_id)->first();

                    if ($row->job_master_status_id == 8) {
                        $name = "Private";
                    }
                    if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')
                            ->select('*')
                            ->where('id', $row->childStatus)
                            ->first();
                        if (!empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }
                    $stage_default_css = 'stage_sub_stage';
                    $html = '';
                    if ($job_wise_condidates->master_status_id == 8) {
                        $html =
                            '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' .
                            @$jobDetails->id .
                            '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' .@$row->css_style .' px-2 py-1">' .ucwords(mb_substr($row->name, 0, 10)) .
                            '</span>
								<span class="text-muted fw-bold d-block fs-7 ">' .
                            @$name .
                            '</span>
								</a>
								</div>';
                    } else {
                        $html =
                            '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' .
                            @$jobDetails->id .
                            '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' .
                            @$row->css_style .
                            ' px-2 py-1">' .
                            ucwords(mb_substr($row->name, 0, 10)) .
                            '</span>
								<span class="text-muted fw-bold d-block fs-7">' .
                            @$name .
                            '</span>
								</span>
								</div>';
                    }
                    echo $html;
                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="10" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
