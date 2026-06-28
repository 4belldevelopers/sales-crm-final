@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    // dd(count($data));
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
     @php
        $row = (object) $rowArr;
        $row->id = $row->job_id;
    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
                @endphp
            </td>
            <td class="d-flex align-items-center">
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
                        '" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 client-show-detail" style="float:left">
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
                            '" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 client-show-detail">
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
                        $skillArr = $row->skillArr;
                        // $skillArr = DB::table('job_metronics_skills')
                        //     ->select('*')
                        //     ->where('job_id', $row->id)
                        //     ->get();
                        if (!empty($skillArr)) {
                            foreach ($skillArr as $sval) {
                                $skilltag .=
                                    '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                            }
                        }
                    } else {
                        $skilltagclass = 'd-none';
                    }

                    echo ' <div class=" d-flex align-items-center border-0" >
							
							' .
                        $icon .
                        '
						   
                           <div class="d-flex flex-column"> 
						   		<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 client-show-detail " data-toggle="tooltip1" title="" data-bs-original-title="2">
									' .
                        ucwords(mb_substr(@$row->title, 0, 15)) .
                        '
								</a> 
								<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="fw-bold text-muted d-block fs-7 text-hover-primary client-show-detail ">
									' .
                        ucwords(mb_substr(@$row->company_name, 0, 15)) .
                        '
								</a> 
							</div> 
						</div> 
						<div class="' .
                        $skilltagclass .
                        '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  ' .
                        $skilltag .
                        '';

                @endphp
            </td>
            <td>
                @php
                    echo '<div class="d-flex flex-column"> 
								<span class="text-dark fw-bolder fs-6">' .
                        @$row->max_exp .
                        ' Y</span> 
								<span class="text-muted fw-bold text-muted d-block fs-7">' .
                        @$row->min_exp .
                        ' Y</span> 
							</div>';
                @endphp

            </td>
            <td>
                @php

                    $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                    $min_sal_mode = $salaryMode[$row->min_sal_mode];
                    $max_sal_mode = $salaryMode[$row->max_sal_mode];

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
                    <span class="fw-bold text-muted d-block fs-7">' .
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
                    $recommendation_id = $row->recommendation_relation_id;
                    
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
                   
                    // dd($row); from_company_id job_assign
                     $captain = DB::table('job_assign')
                     ->select('users.name')
                    ->join('users','users.id','=','job_assign.spok_user_id')
                    ->where('job_assign.company_id','=',$row->from_company_id)
                    ->where('job_assign.job_id','=',$row->id)
                    ->where('job_assign.spok_user_id','!=','')
                    ->first();
                    if($captain){
                        $hiringManager = $captain->name ?? null;
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

                    $from_company_id = $row->from_company_id;
                    $partnersCount = DB::table('job_assign')
                        ->join('users', 'users.id', '=', 'job_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('job_assign.job_id', '=', $row->id)
                        ->where('job_assign.company_id', '=', $from_company_id)
                        ->where('users.company_id', '=', $from_company_id)
                        ->count();

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
                    $jobId = $row->id;

                    $html = '';
                    $html .=
                        '<div data-row-id="' .
                        $row->id .
                        '" class="d-flex flex-column client-hm-listing">
                    						<div class="symbol-group symbol-hover" style="display: inline-block;">';
                    $temp = '';
                    foreach ($partnersData as $pval) {
                        $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                        $logo_path = ''; //url('public/avatar.png');
                        if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $logo_path = @$pval->image;
                        }
                        if (!empty($logo_path)) {
                            $html .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                                @$pval->name .
                                '" style="display: inline-flex;">
                            						<img alt="Pic" src="' .
                                @$logo_path .
                                '">
                          						</div>';
                        } else {
                            $html .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                ucwords(@$pval->name) .
                                '" style="display: inline-flex;">
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
                        '<div  class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' .
                        $partnersCount .
                        '+</span>
													  </div><div class="fs-7 fw-bold text-muted">Client</div>';
                    $html .= '</div>
                    					</div>';
                    echo $html;
                @endphp
            </td>
            <td>
                @php

                    $html =
                        '<div data-row-id="' .
                        $row->id .
                        '" data-row-recommendation_relation_id="' .
                        $row->recommendation_relation_id .
                        '" class="client-terms-listing"><a href="javascript:;" class="btn btn-sm btn-light">View</a></div>';
                    echo $html;
                @endphp
            </td>
            <td>
                @php
                    if($row->final_status==1){
                      echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-success">
                            Accepted
                        </a> 
                        </div>';
                    }elseif($row->final_status==-1){
                    echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Rejected
                        </a> 
                        </div>';
                    }elseif($row->final_status==2){
                      echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Removed
                        </a> 
                        </div>';
                    }elseif($row->final_status==3){
                    echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Expired
                        </a> 
                        </div>';
                    }else{
                    echo '<div class="d-flex my-4"> 
                        <a href="javascript:void(0);" data-row-status="ignore" data-row-id="' .
                                $row->id .
                                '"  data-row-recommendation_relation_id="' .
                                $row->recommendation_relation_id .
                                '" class="btn btn-sm btn-light me-2 request_reject">
                            Reject
                        </a> 
                        <a href="javascript:void(0);" data-row-status="accept" data-row-id="' .
                                $row->id .
                                '" data-row-recommendation_relation_id="' .
                                $row->recommendation_relation_id .
                                '" class="btn btn-sm btn-primary request_approval">
                            Accept
                        </a> 
                        </div>';
                    }
                    
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
