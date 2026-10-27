@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
     @php
          $row = (object) $rowArr;
          
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
                        '<div data-row-id="' .
                        $row->job_id .
                        '" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 client-show-detail" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->job_id .
                        '" class="show-detail">
                      <div class="symbol-label ' .
                        $bgTextDecore .
                        ' border-radius-5">' .
                        ucwords($strVal) .
                        '</div>
                    </a>
                  </div>';

                    $logo_path = url('img/client-default.png');
                    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }

                    if (!empty($logo_path)) {
                        $icon =
                            '<div data-row-id="' .
                            $row->job_id .
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
                    if (!empty($row->skillArr) && is_array($row->skillArr)) {
                        foreach ($row->skillArr as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1">' . htmlspecialchars(ucfirst($sval)) . '</span> ';
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
                        $row->job_id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 client-show-detail " data-toggle="tooltip1" title="" data-bs-original-title="2">
									' .
                        ucwords(mb_substr(@$row->title, 0, 15)) .
                        '
								</a> 
								<a href="javascript:void(0);" data-row-id="' .
                        $row->job_id .
                        '" class="fw-bold text-muted d-block fs-7 text-hover-primary client-show-detail ">
									' .
                        ucwords(mb_substr(@$row->company_name, 0, 15)) .
                        '
								</a> 
							</div> 
						</div> 
						<div class="' .
                        $skilltagclass .
                        '" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  ' .
                        $skilltag .
                        '';
                @endphp
            </td>
            <td class="">
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
            <td class="">
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
            <td class="">
                @php
                    $cityName = !empty($row->city_name) ? mb_substr($row->city_name, 0, 20) : 'Flexible';
                    $employment_type = ($row->employment_type == 1) ? 'Contract' : 'Full-time';

                    if ($row->work_type == 'yes') {
                        echo '<div class="d-flex flex-column">
                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords($employment_type) . '">' . ucwords(mb_substr($employment_type, 0, 10)) . '</span>
                                </div>
                              </div>';
                    } elseif ($row->work_type == 'no') {
                        echo '<div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark fw-bolder fs-6">' . ucwords($cityName) . '</span>
                                </div>
                                <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords($employment_type) . '">' . ucwords(mb_substr($employment_type, 0, 10)) . '</span>
                              </div>';
                    } else {
                        echo '<div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark fw-bolder fs-6">Hybrid</span>
                                </div>
                                <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords($employment_type) . '">' . ucwords(mb_substr($employment_type, 0, 10)) . '</span>
                              </div>';
                    }
                @endphp
            </td>
            <td class="">
                @php
                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                    $status = ucwords($row->status);
                    $class = $job_status[$row->status];
                    $recommendation_id = $row->recommendation_relation_id;
                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }
                    $final_status = '';
                    if ($row->final_status == 2 || $row->final_status == 3) {
                        $status = 'Removed';
                        $class = 'closed-bg';
                    }

                    $ulink =
                        '<a href="javascript.void(0);" data-row-id="' .
                        @$row->job_id .
                        '" class="d-flex flex-column ' .
                        @$class .
                        ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                        $status .
                        '</a>';

                    $recommendation_text = 'Custom';
                    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $recommendation_text = 'Client';
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
                        @$row->job_id .
                        ' -' .
                        $recommendation_text .
                        '</span>
                    </div>';
                @endphp
            </td>
            <td class="">
                @php
                    $total_positions = '';
                    $total_positions = number_format($row->total_positions);

                    $created_at = '';
                    $created_at = $row->created_at;
                    $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                    echo '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' .
                        $newDate .
                        '</span>
                        <span class="text-muted fw-bold d-block fs-7">' .
                        $total_positions .
                        ' Opening</span>
                    </div>';
                @endphp
            </td>
            <td class="">
                @php
                    $newDate = GeneralMethod::ymdTodmyNotTime($row->from_created_at);
                    $html =
                        '<div data-row-id="' .
                        $row->recommendation_relation_id .
                        '" data-row-recommendation_relation_id="' .
                        $row->company_name_to .
                        '" class="client-terms-listing"><a href="javascript:void(0);">
                <span class="text-dark fw-bolder fs-6">' .
                        $row->company_name_to .
                        '</span>
                <span class="text-muted fw-bold d-block fs-7">Req Date, ' .
                        $newDate .
                        '</span></a>
                </div>';
                    echo $html;
                @endphp
            </td>
            <td class="">
                @php
                    $feedback = ucwords(mb_substr(@$feedback_status[@$row->conversations_status]['name'], 0, 20));
                    if (empty($feedback)) {
                        $feedback = '<button type="button" class="btn btn-secondary">Add Note</button>';
                    }

                    echo '<div class="d-flex flex-column show-feedback"  data-row-job_id="' .
                        $row->job_id .
                        '" data-row-conversations_id="' .
                        $row->conversation_id .
                        '" data-row-recommendation_relation_id="' .
                        $row->id .
                        '">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 ">' .
                        @$feedback .
                        '</a>
				<span class="text-muted fw-bold text-muted d-block fs-7"></span>
				</div>';
                @endphp
            </td>
            <td class="">
                @php
                    $url_path = $total_application = $inreview_counter = '';
                    $url_path = 'javascript:;'; 

                    $job_id = $row->job_id;

                    $url_path = 'javascript:;'; 

                    $applicationCounter = number_format((int) (@$row->allCandidate ?? 0));
                    $inreview_counter = number_format((int) (@$row->InReview ?? 0));

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
            <td class="">
                @php
                    $job_id = '';
                    $master_status_id = 1;
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = $total_application = $inreview_counter = '';
                    $url_path = 'javascript:;'; //url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
                    
                    
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
            <td class="">
                @php
                    $master_status_id = 2;
                    $job_id = '';
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = '';
                    $url_path = 'javascript:;'; //  url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

            
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
            <td class="">
                @php
                    $master_status_id = 3;
                    $job_id = '';
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = '';
                    $url_path = 'javascript:;'; //url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

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
            <td class="">
                @php
                    $master_status_id = 4;
                    $job_id = '';
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = '';
                    $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                   
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
            <td class="">
                @php
                    $master_status_id = 5;
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = ''; //
                    $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                    
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
            <td class="">
                @php
                    $master_status_id = 6;
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = ''; //
                    $url_path = 'javascript:;'; 

                    
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
            <td class="">
                @php
                    $master_status_id = 7;
                    $cnt = 0;
                    $job_id = $row->job_id;
                    $cnt = GeneralMethod::statusCountThirdparty($row->to_company_id, $job_id, $master_status_id);
                    $url_path = '';
                    $url_path = 'javascript:;'; 

                    
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

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
