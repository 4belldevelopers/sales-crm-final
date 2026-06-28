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
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0	=>	"INR",
        1	=>	"EUR",
        2	=>	"JPY",
        3	=>	"GBP",
        4	=>	"USD",
        5	=>	"AED"
     ];

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->id = $row->candidate_id;
    $row->condidate_id = $row->wiseId;
    $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$jobid)->first();
    $jobMode = $jobData->jobMode ?? @$row->current_ctc_mode;
    $jobCurrency = $jobData->jobCurrency ?? @$row->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";

    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
															<input class="form-check-input chk_list_item" type="checkbox" value="' .
                        @$row->id .
                        '" />
                        </div>';
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = $strVal = $tredmark = '';
                    $str = trim(@$row->full_name);
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

                    $flag = 0;
                    $duplicate = 0;
                    $duplicate = $row->duplicate;
                    $company_id = auth()->user()->roles[0]->company_id;
                    $app_duplicate = auth()->user()->company->app_duplicate;
                    $app_duplicate_days = auth()->user()->company->app_duplicate_days;

                    $covid = '';
                    if ($duplicate == 1) {
                        // CHECK RECORD IS DUPLICATE
                        if ($app_duplicate == 1 || $app_duplicate == 2) {
                            // CHECK SYSTEM MODE SET IN DUPLICATE
                            if ($app_duplicate == 1) {
                                // CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
                                $application_cnt = DB::table('job_applications')
                                    ->select('*')
                                    ->where('company_id', $company_id)
                                    ->where('job_id', $row->job_id)
                                    ->where('email', $row->email)
                                    ->orWhere('phone', $row->phone)
                                    ->where(
                                        'job_applications.created_at',
                                        '>',
                                        DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'),
                                    )
                                    ->orderby('job_applications.id', 'DESC')
                                    ->count();
                                if ($application_cnt > 1) {
                                    $flag = 1;
                                } else {
                                    $flag = 0;
                                }
                            } elseif ($app_duplicate == 2) {
                                // CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2
                                $application_cnt = DB::table('job_applications')
                                    ->select('job_applications.id')
                                    ->where('company_id', $company_id)
                                    ->where('job_applications.email', $row->email)
                                    ->orWhere('phone', $row->phone)
                                    ->where(
                                        'job_applications.created_at',
                                        '>',
                                        DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'),
                                    )
                                    //->where('job_applications.duplicate', '=', 1 )
                                    ->orderby('job_applications.id', 'DESC')
                                    ->count();

                                if ($application_cnt > 1) {
                                    $flag = 1;
                                } else {
                                    $flag = 0;
                                }
                            } else {
                                $flag = 0;
                            }
                        } else {
                            $flag = 0;
                        }
                    } else {
                        $flag = 0;
                    }

                    if (@$row->condidate_type == 'bench_condidate') {
                        $tredmark = '[Bench]';
                    }
                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = explode(',', $row->skills);
                    $percentage =  GeneralMethod::skill_percentage($jobid,$skillArr);
                    if (count($skillArr) > 0) {
                        foreach ($skillArr as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                        }
                    }

                    if ($flag == 1) {
                        // client-show-application-detail
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                <a href="javascript:void(0);"  data-row-id="' .
                            @$row->condidate_id .
                            '" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
                                <span class="fs-3">' .
                            strtoupper(@$strVal) .
                            '</span>
                                <span class="pulse-ring"></span>
                                </a>
                                </div>                  
                                <div class="d-flex flex-column">
                                <a href="javascript:void(0);"  data-row-id="' .
                            @$row->condidate_id .
                            '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 30)) .
                            $covid .
                            '<sup style="color: #ef7c62 !important;
                                                                                                                font-weight: bold;">' .
                            $tredmark .
                            '</sup></a>
                                <a href="javascript:void(0);"  data-row-id="' .
                            @$row->condidate_id .
                            '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 30)) .
                            '</a>
                                </div>
                                <div class="' .
                            $skilltagclass .
                            '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                                <span class="badge badge-light my-1">'.$percentage.'</span>
                                            
                                            ' .
                            $skilltag .
                            '
                                                    
                                            
                                                </div> 
                                ';
                    } else {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                        <a href="javascript:void(0);"   data-row-id="' .
                            @$row->condidate_id .
                            '" class="show-application-detail">
                        <div class="symbol-label ' .
                            @$bgTextDecore .
                            ' border-radius-5">' .
                            strtoupper(@$strVal) .
                            '</div>
                        </a>
                        </div>                  
                        <div class="d-flex flex-column">
                        <a href="javascript:void(0);"  data-row-id="' .
                            @$row->condidate_id .
                            '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 30)) .
                            $covid .
                            '<sup style="color: #ef7c62 !important;
                        font-weight: bold;">' .
                            $tredmark .
                            '</sup></a>
                        <a href="javascript:void(0);"  data-row-id="' .
                            @$row->condidate_id .
                            '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 30)) .
                            '</a>
                        </div>
                        <div class="' .
                            $skilltagclass .
                            '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                        <span class="badge badge-light my-1">'.$percentage.'</span>
                                    
                                    ' .
                            $skilltag .
                            '
                                        </div> ';
                    }
                @endphp
            </td>
            <td>
                @php
                    $total_exp = $row->total_exp;
                    if($total_exp == '0.'){
                    $total_exp = str_replace('.','',$total_exp);
                    }
                    $rel_exp = $row->rel_exp;
                    if($total_exp == '0.'){
                    $rel_exp = str_replace('.','',$rel_exp);
                    }
                    if($total_exp==0 && $rel_exp==0){
                        $rel_exp = ''; 
                    }

                        $experience = trim((string) @$row->total_exp);
                        // Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
                        if (strpos($experience, '.') !== false) {
                            $parts = explode('.', $experience);
                            if (isset($parts[1])) {
                                // If decimals are exactly "00", remove them
                                if ($parts[1] === '00') {
                                    $formattedExperience = $parts[0];
                                } else {
                                    $formattedExperience = $experience; // keep as-is like 3.0, 5.6, 9.10
                                }
                            } else {
                                $formattedExperience = $experience;
                            }
                        } else {
                            $formattedExperience = $experience;
                        }

                        $rel_experience = trim((string) @$row->rel_exp);
                        // Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
                        if (strpos($rel_experience, '.') !== false) {
                            $parts = explode('.', $rel_experience);
                            if (isset($parts[1])) {
                                // If decimals are exactly "00", remove them
                                if ($parts[1] === '00') {
                                    $rel_formattedExperience = $parts[0];
                                } else {
                                    $rel_formattedExperience = $rel_experience; // keep as-is like 3.0, 5.6, 9.10
                                }
                            } else {
                                $rel_formattedExperience = $rel_experience;
                            }
                        } else {
                            $rel_formattedExperience = $rel_experience;
                        }

                        echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .$formattedExperience . ' Y' . 
                        '</a>
                        <span class="text-muted fw-bold d-block fs-7">' . $rel_formattedExperience . ' Y'. 
                        '</span>
                    </div>';
                @endphp

            </td>
            <td>
                @php

                    $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = '';

                    $current_ctc_cur = $currencySymbole[@$jobCurrency];
                    $current_ectc_cur = $currencySymbole[@$jobCurrency];
                    $cur = $jobCurrency;
                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_cur = $currencySymbole[@$jobCurrency];
                        $cur = $jobCurrency;
                    }
                    $current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                    $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                    $mod = $jobMode;
                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                        $mod = $jobMode;
                    }

                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[$cur];
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);

                    $ectc = $convertedAmounEctc[$mod];
                    $ctc = $convertedAmounCtc[$mod];
                    
                    
                    
                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$mod);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$mod);


                    $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

                    // if ($row->condidate_type == 'bench_condidate') {
                    // 	$ectc = GeneralMethod::thousandsCurrencyFormat($ectc);
                    // }

                    $dnone = '';
                    $dnonetype = 'd-none';
                    if ($row->condidate_type == 'bench_condidate') {
                        $dnone = 'd-none';
                        $dnonetype = '';
                    }
                    $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';
                    echo '<div class="d-flex flex-column">
                       <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        $current_ectc_cur .
                        @$ectc .
                        '/' .
                        $current_ectc_mode .
                        '</a>
                         <span class="text-muted fw-bold d-block fs-7 ' .
                        $dnone .
                        '">' .
                        @$current_ctc_cur .
                        @$ctc .
                        '/' .
                        $current_ctc_mode .
                        '</span>
                         <span class="text-muted fw-bold text-muted d-block fs-7 ' .
                        $dnonetype .
                        '" data-toggle="tooltip">' .
                        $outsourcing_type .
                        '</span>
                     </div>';

                @endphp
            </td>
            <td>
                @php

                    $np = $row->np_id;
                    if (empty($np)) {
                        $np = 'N/A';
                    }
                    if ($row->serving_notice_period_mode == 0) {
                        if (!empty($row->np_id)) {
                            $np = $row->np_id . '  Days';
                        }
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $np = $row->np_id;
                        if ($np != 'Immediate') {
                            $np = date('d-M-Y', strtotime($row->np_id));
                            $today = date('Y-m-d'); 
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }
                        }
                    }
                    echo '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$np .
                        '</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .htmlspecialchars(ucwords(@$row->current_company), ENT_QUOTES, 'UTF-8') .
                        '">' . htmlspecialchars(ucwords(mb_substr(@$row->current_company, 0, 10)), ENT_QUOTES, 'UTF-8') .
                        '</span>
						</div>';
                @endphp
            </td>
            <td>
                @php

                    $schedule_date = $className = '';
                    $intRowCount = DB::table('interview_schedules')
                        ->select('*')
                        ->where('job_application_id', $row->id)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->count();
                    $intRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where('job_application_id', $row->id)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();
                    $html = '';
                    if ($intRowCount > 0) {
                        $schedule_date = @$intRow->schedule_date;
                        $schedule_date = date('d M, Y', strtotime($schedule_date));
                    } else {
                        $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                    }

                    if ($intRowCount > 0) {
                        $feedback_status_name = '';
                        if ($intRow->feedback >= 0) {
                            $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                            $className = @$feedback_status[@$intRow->feedback]['classname'];
                        } else {
                            $feedback_status_name = 'Pending';
                            $className = 'pending-bg';
                        }
                    } else {
                        $feedback_status_name = '';
                    }

                    echo ' <div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '" style="text-transform: uppercase;!important">' .
                        ucwords(mb_substr(@$row->qualification_name, 0, 10)) .
                        '</a>
							<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="">' .
                        $feedback_status_name .
                        '</span> 
							</div>';

                @endphp
            </td>
            <td>
                @php

                    $schedule_date = $className = $schedule_time = '';
                    $intRowCount = DB::table('interview_schedules')
                        ->select('*')
                        ->where('interview_job_wise_con_id', $row->condidate_id)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->count();
                    $intRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where('interview_job_wise_con_id', $row->condidate_id)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();
                    $html = '';
                    if ($intRowCount > 0) {
                        $schedule_date = @$intRow->schedule_date;
                        $schedule_date = date('d M, Y', strtotime($schedule_date));
                        $schedule_time = Carbon::parse($intRow->schedule_date)->format('h:i A');
                    } else {
                        $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                    }

                    if ($intRowCount > 0) {
                        $feedback_status_name = '';
                        if ($intRow->feedback >= 0) {
                            $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                            $className = @$feedback_status[@$intRow->feedback]['classname'];
                        } else {
                            $feedback_status_name = 'Pending';
                            $className = 'pending-bg';
                        }
                    } else {
                        $feedback_status_name = '';
                    }
                    echo '<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">' .
                        @$schedule_date .
                        '</span>
						<span class="text-muted fw-bold d-block fs-7 ' .
                        @$className .
                        ' px-1">' .
                        @$schedule_time .
                        '</span>
						</div>';

                @endphp
            </td>
            <td>
                @php

                    $country = $row_country_name = '';

                    if (empty($row->country)) {
                        $row_country_name = '----';
                    } else {
                        if (!empty(@$row->country)) {
                            if (is_numeric(@$row->country)) {
                                $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                                foreach ($country_name_Arr as $cval) {
                                    $row_country_name = ucwords($cval['name']);
                                }
                            }
                        }
                    }

                    $state = '';
                    $row_state_name = '';
                    if (empty($row->state)) {
                        $row_state_name = '----';
                    } else {
                        if (@$row->country == 101) {
                            $row_state_name = @$row->state_name;
                        } else {
                            $state = @$row->state;
                            if (is_numeric(@$row->country) && is_numeric($state)) {
                                if ($state > 0) {
                                    $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                                    foreach ($state_name_Arr as $sval) {
                                        $row_state_name = ucwords($sval['name']);
                                    }
                                }
                            }
                        }
                    }

                    $city = '';
                    $row_city_name = '';

                    if (@$row->country == 101) {
                        $row_city = DB::table('city_list')
                            ->select('*')
                            ->where('city_id', @$row->city_id)
                            ->first();
                        $row_city_name = @$row_city->city;
                    } else {
                        $row_city_name = @$row->city;
                    }

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row_city_name) .
                        '">' .
                        ucwords(mb_substr($row_city_name, 0, 10)) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($row_state_name) .
                        ' | ' .
                        strtoupper($row_country_name) .
                        '">' .
                        ucfirst(mb_substr($row_state_name, 0, 5)) .
                        ' | ' .
                        strtoupper(mb_substr($row_country_name, 0, 2)) .
                        '</span>
                </div>';

                @endphp
            </td>
            <td>
                @php
                    $posted_by_image = '';
                    $posted_by_image = url('public/avatar.png');
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                    } 
                    $post_company_name = @$row->post_company_name;
                    if (!$post_company_name) {
                        $post_company_name = @$row->company_name;
                    }
                    echo '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$row->posted_by .
                        '">' .
                        mb_substr($row->posted_by, 0, 10) .
                        '</a>
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$post_company_name .
                        '">' .
                        mb_substr(@$post_company_name, 0, 10) .
                        '</span>
								</div>';

                @endphp
            </td>
            <td>
                @php

                    $name = '';
                    if ($row->job_master_status_id == 8) {
                        $name = 'Private';
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
                    if ($row->ClientComID == $company_id) {
                        $stage_default_css = '';
                    }

                    $html = '';
                    if ($row->job_master_status_id == 8) {
                        $html =
                            '<div class="d-flex flex-column">
												<a href="javascript.void(0);" data-row-id="' .
                            @$row->condidate_id .
                            '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
													<span class="badge fw-bolder fs-6 ' .
                            @$row->css_style .
                            ' px-2 py-1">' .
                            ucwords(mb_substr($row->name, 0, 10)) .
                            '</span>
													<span class="text-muted fw-bold d-block fs-7 px-3 py-1">' .
                            @$name .
                            '</span>
												</a>
											</div>';
                    } else {
                        $html =
                            '<div class="d-flex flex-column">
												<span href="javascript.void(0);" data-row-id="' .
                            @$row->condidate_id .
                            '" class="d-flex flex-column ">
													<span class="badge fw-bolder fs-6 ' .
                            @$row->css_style .
                            ' px-2 py-1">' .
                            ucwords(mb_substr($row->name, 0, 10)) .
                            '</span>
													<span class="text-muted fw-bold d-block fs-7 px-3 py-1">' .
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
