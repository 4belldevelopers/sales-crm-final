@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
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

@if (count($boostbenchApplications) > 0)
    @foreach ($boostbenchApplications as $row)
     @php
        $jobMode = @$modeFilter;
        $jobCurrency = @$currencyFilter;
        $symbols = @$symbolsFilter;
        // dd($row);
    @endphp
    <tr style="height: 102px; position: relative;">
        <td>
            @php
                echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' .
                    $row->id .
                    '" />
            </div>';
            @endphp
        </td>
        <td>
            @php
                $bgTextDecore = $strVal = '';
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

                $profile_sidebar_css = 'pbench_show_application_detail';

                $skilltag = '';
                $skilltagclass = '';

                $skillArr = explode(',', $row->skills);
                // $skillArr = DB::table('benchboostkeywordcandidates')->where('benchboostkeywordcandidates.candidate_id', @$row->id)->pluck('skill_name')->toArray();
                
                $percentage = '0';
                $percentage =  @$row->skill_match_percentage.'%';
                if (count($skillArr) > 0) {
                    foreach ($skillArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                    }
                }
                $logo_path = url('public/award.png');

                echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
            <a href="javascript:void(0);"   data-row-id="' .
                    @$row->id .
                    '" class="' .
                    $profile_sidebar_css .
                    '">
            <div class="symbol-label ' .
                    @$bgTextDecore .
                    ' border-radius-5">' .
                    strtoupper(@$strVal) .
                    '</div>
            </a>
            </div>                  
            <div class="d-flex flex-column">
            <a href="javascript:void(0);"  data-row-id="' .
                    @$row->id .
                    '"  class="text-dark fw-bolder text-hover-primary fs-6 ' .
                    $profile_sidebar_css .
                    ' " data-toggle="tooltip" title="' .
                    ucwords(@$row->full_name) .
                    '">' .
                    ucwords(mb_substr(@$row->full_name, 0, 22)) .
                    '<sup><img src="' .
                            str_replace('index.php', '', @$logo_path) .
                            '" alt="Company Logo" class="border-radius-5"></sup></a>
            <a href="javascript:void(0);"  data-row-id="' .
                    @$row->id .
                    '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                    $profile_sidebar_css .
                    ' " data-toggle="tooltip" title="' .
                    ucwords(@$row->current_designation) .
                    '">' .
                    ucwords(mb_substr(@$row->current_designation, 0, 22)) .
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
                $npArr = ['Notice period', 'Available from', 'Immediate'];
                $npDate = $row->np_id;
                if ($row->serving_notice_period_mode == 0) {
                    $npDate = $row->np_id . ' Days';
                    if ($npDate == 1) {
                        $npDate = $row->np_id . ' Day';
                    }
                } elseif ($row->serving_notice_period_mode == 1) {
                    $originalDate = $row->np_id;
                    $npDate = '' . date('d-M-y', strtotime($originalDate));
                     $today = date('Y-m-d'); 
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $npDate = "Immediate";
                            }
                } else {
                    $npDate = '';
                }

                echo '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                    $npArr[$row->serving_notice_period_mode] .
                    '</a> 
                <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' .
                    $row->np_id .
                    '">' .
                    $npDate .
                    '</span>

                
                </div>';
            @endphp
        </td>
        <td>
            @php
                $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                    '';
                $pay_rate_currency = $currencySymbole[@$jobCurrency];
                $bill_rate_currency = $currencySymbole[@$jobCurrency];

                $currencymain = GeneralMethod::currency(); // Currency code
                $currency = $currencymain[@$jobCurrency];
                $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                $bill_rate_amount = $convertedAmounEctc[$sal_mode];
                $pay_rate_amount = $convertedAmounCtc[$sal_mode];

                

                $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);

                $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

                $pay_rate_mode = '';
                $pay_rate_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                //$current_ctc_mode		= mb_substr($salaryMode[0], 0, 1);

                $bill_rate_mode = '';
                $bill_rate_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                //$current_ectc_mode		= mb_substr($salaryMode[0], 0, 1);
                $dnone = '';
                if ($row->condidate_type == 'bench_condidate') {
                    $dnone = 'd-none';
                }
                $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';
                echo '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                    @$bill_rate_currency .
                    @$bill_rate_amount .
                    '/' .
                    $bill_rate_mode .
                    '</a>
                <span class="text-muted fw-bold d-block fs-7 ' .
                    $dnone .
                    '">' .
                    $pay_rate_currency .
                    @$pay_rate_amount .
                    '/' .
                    $pay_rate_mode .
                    '</span>
                <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                    $outsourcing_type .
                    '</span>
            </div>';
            @endphp
        </td>
        <td>
            @php
                $comission_type = $row->comission_type == 1 ? 'Free' : 'Paid ';
                echo ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                    @$row->qualification_name .
                    '" style="text-transform: uppercase;!important">' .
                    @$row->qualification_name .
                    '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="">'.$comission_type.'</span> 
                    </div>';
            @endphp
        </td>
        <td>
            @php
                $country = '';
                $row_country_name = '';

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
                        $row_state = DB::table('state_list')
                            ->select('*')
                            ->where('id', @$row->state)
                            ->first();
                        $row_state_name = @$row_state->state;
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
                $updated_at = '';
                if($row->status==1){
                    $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 interview text-primary">
                        &nbsp;&nbsp;Pool&nbsp;&nbsp;</a>';
                }elseif($row->status==4){
                    $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 hold-bg text-warning">
                        Pause</a>';
                }elseif($row->status==3){
                    $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 text-success">
                        Placed</a>';
                }elseif($row->status==2){
                    $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 text-primary">
                        Bench</a>';
                }
                $updated_at = GeneralMethod::ymdTodmyNotTime($row->updated_at); //$newDate = date("d M , Y", strtotime($row->updated_at));
                echo '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' .
                    @$updated_at .
                    '</span>
            <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                $status_text .
                    '</span>
            </div>';
            @endphp
        </td>
        <td>
            @php
                $recCounter = 0;
                $recCounter = DB::table('rec_candidate_assign')
                    ->select('id')
                    ->where('source_from_id', @$row->id)
                    ->whereIn('source_from_type', [1, 2])
                    ->where('source_jobid', @$jobid)
                    ->count();
                    
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                        ->select('job_application_id')
                        ->where('add_job_wise_condidates.job_id', '=', $jobid)
                        ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();

                if($row->status==2){
                    if ($existingCandidatesQuery > 0) {
                        $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
                    }elseif($row->countforadd >= 3){
                        $html = '<div class="d-flex flex-column"> 
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a> 
                            </div>';
                    }else{
                    $html =
                        '<div class="d-flex flex-column"> 
                            <a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="2"  data-row-id="' .
                        @$row->id .
                        '" data-row-jobid="' .
                        @$jobid .
                        '">
                            Add to Job
                            </a> 
                            </div>';
                    }
                }else{
                    $html = '<div class="d-flex flex-column"> 
                            <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a> 
                            </div>';
                }
                echo $html;
            @endphp
        </td>
    </tr>
    @endforeach
@endif


@if (count($data) > 0 )
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' .
                        $row->id .
                        '" />
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = $strVal = '';
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

                    $profile_sidebar_css = 'pbench_show_application_detail';

                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = $row->matched_skills;// explode(',', $row->skills);

                    $percentage=0;
                    $matches = $row->matched_skill_count;
                    $totalElements = count($requiredSkills);
                    $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                    $matchPercentage = min($matchPercentage, 100);
                    $percentage = round($matchPercentage, 1);

                    if (count($skillArr) > 0) {
                        foreach ($skillArr as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                        }
                    }

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
				<a href="javascript:void(0);"   data-row-id="' .
                        @$row->id .
                        '" class="' .
                        $profile_sidebar_css .
                        '">
				<div class="symbol-label ' .
                        @$bgTextDecore .
                        ' border-radius-5">' .
                        strtoupper(@$strVal) .
                        '</div>
				</a>
				</div>                  
				<div class="d-flex flex-column">
				<a href="javascript:void(0);"  data-row-id="' .
                        @$row->id .
                        '"  class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $profile_sidebar_css .
                        ' " data-toggle="tooltip" title="' .
                        ucwords(@$row->full_name) .
                        '">' .
                        ucwords(mb_substr(@$row->full_name, 0, 22)) .
                        '</a>
				<a href="javascript:void(0);"  data-row-id="' .
                        @$row->id .
                        '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                        $profile_sidebar_css .
                        ' " data-toggle="tooltip" title="' .
                        ucwords(@$row->current_designation) .
                        '">' .
                        ucwords(mb_substr(@$row->current_designation, 0, 22)) .
                        '</a>
				</div>
				<div class="' .
                        $skilltagclass .
                        '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light my-1">'.$percentage.'%</span>
                                 
                                  ' .
                        $skilltag .
                        '
                                         
                                   
                                    </div> 	
				';

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
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    if ($row->serving_notice_period_mode == 0) {
                        $npDate = $row->np_id . ' Days';
                        if ($npDate == 1) {
                            $npDate = $row->np_id . ' Day';
                        }
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $originalDate = $row->np_id;
                        $npDate = '' . date('d-M-y', strtotime($originalDate));
                    } else {
                        $npDate = '';
                    }

                    echo '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        $npArr[$row->serving_notice_period_mode] .
                        '</a> 
					  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' .
                        $row->np_id .
                        '">' .
                        $npDate .
                        '</span>

					  
					</div>';
                @endphp
            </td>
            <td>
                @php
                    $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                        '';
                    $pay_rate_currency = $currencySymbole[@$min_salary_currency];
                    $bill_rate_currency = $currencySymbole[@$min_salary_currency];

                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[@$min_salary_currency];
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                    $bill_rate_amount = $convertedAmounEctc[$sal_mode];
                    $pay_rate_amount = $convertedAmounCtc[$sal_mode];

                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($pay_rate_amount);
                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($bill_rate_amount);

                    $pay_rate_mode = '';
                    $pay_rate_mode = mb_substr($salaryMode[@$sal_mode], 0, 1);
                    //$current_ctc_mode		= mb_substr($salaryMode[0], 0, 1);

                    $bill_rate_mode = '';
                    $bill_rate_mode = mb_substr($salaryMode[@$sal_mode], 0, 1);
                    //$current_ectc_mode		= mb_substr($salaryMode[0], 0, 1);
                    $dnone = '';
                    if ($row->condidate_type == 'bench_condidate') {
                        $dnone = 'd-none';
                    }
                    $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$bill_rate_currency .
                        @$bill_rate_amount .
                        '/' .
                        $bill_rate_mode .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7 ' .
                        $dnone .
                        '">' .
                        $pay_rate_currency .
                        @$pay_rate_amount .
                        '/' .
                        $pay_rate_mode .
                        '</span>
					<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                        $outsourcing_type .
                        '</span>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $comission_type = $row->comission_type == 1 ? 'Free' : 'Paid ';
                    echo ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '" style="text-transform: uppercase;!important">' .
                        @$row->qualification_name .
                        '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="">'.$comission_type.'</span> 
						</div>';
                @endphp
            </td>
            <td>
                @php
                    $country = '';
                    $row_country_name = '';

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
                            $row_state = DB::table('state_list')
                                ->select('*')
                                ->where('id', @$row->state)
                                ->first();
                            $row_state_name = @$row_state->state;
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

                    $row_country_name = @$row->country_name;
                    $row_state_name = @$row->state_name;
                    $row_city_name = @$row->city_name;

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
                    $updated_at = '';
                    if($row->status==1){
                        $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 interview text-primary">
                            &nbsp;&nbsp;Pool&nbsp;&nbsp;</a>';
                    }elseif($row->status==4){
                        $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 hold-bg text-warning">
                            Pause</a>';
                    }elseif($row->status==3){
                        $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 text-success">
                            Placed</a>';
                    }elseif($row->status==2){
                        $status_text =' <a href="javascript:void(0);" class="fw-bold fs-6 text-primary">
                            Bench</a>';
                    }
                    $updated_at = GeneralMethod::ymdTodmyNotTime($row->updated_at); //$newDate = date("d M , Y", strtotime($row->updated_at));
                    echo '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' .
                        @$updated_at .
                        '</span>
				<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                    $status_text .
                        '</span>
				</div>';
                @endphp
            </td>
            <td>
                @php
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        ->whereIn('source_from_type', [1, 2])
                        ->where('source_jobid', @$jobid)
                        ->count();
                        
                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();

                    if($row->status==2){
                        if ($existingCandidatesQuery > 0) {
                            $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
                        }elseif($row->countforadd >= 3){
                            $html = '<div class="d-flex flex-column"> 
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a> 
                                </div>';
                        }else{
                        $html =
                            '<div class="d-flex flex-column"> 
                                <a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="2"  data-row-id="' .
                            @$row->id .
                            '" data-row-jobid="' .
                            @$jobid .
                            '">
                                Add to Job
                                </a> 
                                </div>';
                        }
                    }else{
                        $html = '<div class="d-flex flex-column"> 
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a> 
                                </div>';
                    }
                    echo $html;
                @endphp
            </td>
        </tr>
    @endforeach
@else
  @if(count($boostbenchApplications) == 0 && count($data) == 0 )  
        <tr style="height: 102px; position: relative; background: #fffcfc7a;">

            <td colspan="9" style="text-align: center; color: black;">
                No data available in table
            </td>
        </tr>
    @endif
@endif
