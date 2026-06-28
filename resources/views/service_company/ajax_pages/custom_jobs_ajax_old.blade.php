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
    $targetJobId = $row->id;

    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    // dd($relationget);

    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;
    
    $job_assign = $row->job_assign;
    $jobAssign = collect($job_assign);
    
    @endphp
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
                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
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

                    $logo_path = @$row->logo_logical_path;
                    if ($row->self_company == 1) {
                        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                            $logo_path = @$row->logo;
                        }
                    }
                    $logo_path = GeneralMethod::companyDetails($row->id);
                    if (!empty($logo_path)) {
                        $icon =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
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
                    $skillArr = [];
                    
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

                    echo $icon .
                        '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip1" title="' .
                        ucwords(@$row->title) .
                        '" >' .
                        ucwords(mb_substr(@$row->title, 0, 30)) .
                        '</a> 
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">' .
                        ucwords(mb_substr(@$row->clients_name, 0, 30)) .
                        '</a>
                  </div>
                  
                   <div style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light-success fs-base py-0">Skills:</span>
                                 
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
                        @$row->max_exp .
                        '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        @$row->min_exp .
                        '+</span>
                  </div>';
                @endphp

            </td>
            <td>
                @php
                    $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                    $min_sal_mode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode], 0, 1));
                    $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));

                    $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                    $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

                    $max_salary = '';
                    $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                    $min_salary = '';
                    $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                    echo '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' .
                        $min_salary_currency .
                        $max_salary .
                        '/' .
                        $min_sal_mode .
                        '</span>                    
                    <span class="fw-bold d-block fs-7">' .
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
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' .
                            ucwords(@$employment_type) .
                            '" >' .
                            ucwords(mb_substr(@$employment_type, 0, 10)) .
                            '</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">Remote</span>
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
                    $status = $active = $hold = $inactive = $class = '';
                    $status = ucwords($row->status);
                    $class = $job_status[$row->status];
                    
                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }

                    $link =
                        '<a href="javascript.void(0);" data-row-id="' .
                        @$row->id .
                        '" class="d-flex flex-column ' .
                        @$class .
                        '  status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                        $status .
                        ' </a>';

                    echo '<div class="d-flex flex-column">
                        ' .
                        $link .
                        ' 
                      <span class="fw-bold text-muted d-block fs-7">' .
                        @$row->id .
                        '-'.__('messages.internal_tag').'</span>
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
                    

                     $recommendation_id = $row->recommendation_id;
                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $row->comId)
                        ->filter()
                        ->map(fn ($c) => ($c))
                        ->values()
                        ->all();
                        $clientjobAssignListCaptain = '';
                        if(count($jobAssignListCaptain)>0 && $row->comId != auth()->user()->company_id){
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
                
    
                    $jobId = $row->id;

                    $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)->count();

                    $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                    ->filter(function ($row) {
                            return $row['user_id'] == $row['spok_user_id'];
                    })->sortBy('user_name')->take(1)->values();

                    $html = '';
                    $html .=
                        '<div class="d-flex flex-column hm-listing" data-row-id="' .
                        $row->id .
                        '">
                    						<div class="symbol-group symbol-hover">';
                    $temp = '';
                    foreach ($partnersData as $pval) {
                        $pval = (object) $pval;
                        $bgTextDecore = GeneralMethod::icoinText(@$pval->user_name);
                        $logo_path = ''; //url('public/avatar.png');
                        if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
                        }
                        if (!empty($logo_path)) {
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
                        '"   class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' .
                        $partnersCount .
                        '+</span>
													  </div>';
                    $html .= '</div>
                    					<div class="fs-7 fw-bold text-muted">Team</div></div>';
                    echo $html;

                @endphp
            </td>
            <td>
                @php
                    $html = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        // Direct Client Job

                        $jobId = $row->id;

                        $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)->count();

                        $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();

                        $html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
                        $temp = '';
                        foreach ($partnersData as $pval) {
                            $pval = (object) $pval;
                            $logo_path = url('public/avatar.png');
                            if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                                $logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
                            }

                            $html .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                @$pval->user_name .
                                '">
                            						<img alt="Pic" src="' .
                                @$logo_path .
                                '">
                          						</div>';
                        }
                        $html .=
                            '<div data-row-id="' .
                            $row->id .
                            '"   class="symbol symbol-30 symbol-circle symbol-light team-listing" data-bs-placement="top" data-toggle="tooltip2" title="More team list..">
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
                            '<div class="d-flex flex-column spartner-listing" data-row-id="' .
                            $row->id .
                            '">
			             <div class="symbol-group symbol-hover">';

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
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">' .
                        $inreview_counter .
                        '</a>
                    </div>';
                @endphp
            </td>
            <td>
                @php
                    $master_status_id = 1;
                    $job_id = '';
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 1); // number_format($row->Sourcing);
                    if ($cnt == 0) {
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 2); // number_format($row->Screening);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 3); // number_format($row->Interview);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 4); // number_format($row->Negotiation);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
                            '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                            @$cnt .
                            '</a></div>';
                    }
                @endphp
            </td>
            <td>
                @php
                    $master_status_id = 5;
                    $job_id = '';
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 5); // number_format($row->Offered);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 6); // number_format($row->Joined);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
                    $cnt = 0;
                    $job_id = $row->id;
                    $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, 7); // number_format($row->Rejected);
                    if ($cnt == 0) {
                        $cnt = '<b>.</b>';
                        echo '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                    } else {
                        echo '<div class="d-flex flex-column"><a href="' .
                            url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            ) .
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
