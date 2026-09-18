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
    $jobMode = @$modeFilter;
    $jobCurrency = @$currencyFilter;
    $symbols = @$symbolsFilter;
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
                    $profile_sidebar_css = 'website-show-application-detail';

                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = $row->matched_skills;
                    $allCandidateSkills = array_filter(array_map('trim', explode(',', (string) @$row->skills)));

                    $targetJobId = $row->job_id ?? ($jobid ?? 0);
                    if ($targetJobId > 0) {
                        $percentageString = GeneralMethod::skill_percentage($targetJobId, $allCandidateSkills);
                        $percentage = (int) round((float) rtrim($percentageString ?: '0%', '%'));
                    } else {
                        $matches = $row->matched_skill_count ?? 0;
                        $totalElements = count($requiredSkillsForDisplay ?? []);
                        $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                        $percentage = (int) round(min($matchPercentage, 100));
                    }

                    // Match % badge — standardized thresholds: >=85 green, 50-84 orange, <50 red
                    $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
                    $matchBg = $matchStyle['bg'];
                    $matchColor = $matchStyle['color'];
                    $matchBadge = '<span class="badge my-1" style="background-color:' . $matchBg . ';color:' . $matchColor . ';font-weight:600;">Match ' . $percentage . '%</span>';

                    // Matched skill highlighting, three tiers — mirrors
                    // internal_bench_ajax.blade.php: a match against a STARRED
                    // (mandatory) requirement is a different signal than a match
                    // against a skill that was just typed but not starred.
                    // $row->matched_skills and $row->matched_mandatory_skills are
                    // both computed server-side (GeneralMethod::matchedSkillsList())
                    // in career_apply_data_ajax()'s must+should hybrid query.
                    $mandatoryMatched = $row->matched_mandatory_skills ?? [];
                    $optionalMatched = array_values(array_udiff($skillArr, $mandatoryMatched, 'strcasecmp'));
                    $unmatchedSkillsArr = array_values(array_udiff($allCandidateSkills, $skillArr, 'strcasecmp'));
                    foreach ($mandatoryMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dbeafe;color:#1d4ed8;font-weight:600;border:1px solid #bfdbfe;" title="Matches a mandatory (starred) skill"><span style="color:#eab308;">★</span> ' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($optionalMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;" title="Matches a required skill">' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($unmatchedSkillsArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
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
                                            '.$matchBadge.'
                                        
                                        ' .
                        $skilltag .
                        '
                  </div>';
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

                    $pay_rate_currency = '';
                    $pay_rate_currency = $currencySymbole[@$jobCurrency];

                    $bill_rate_currency = '';
                    $bill_rate_currency = $currencySymbole[@$jobCurrency];

                    $pay_rate_amount = '';

                    $currencymain = GeneralMethod::currency(); // Currency code
                    // dd($currency);
                    $currency = $currencymain[@$jobCurrency];
                    // dd($currency);
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);

                    $exchangeRates = [
                        'INR' => 1,
                        'EUR' => 90.33, // Example exchange rate for EUR to INR
                        'JPY' => 0.68, // Example exchange rate for JPY to INR
                        'GBP' => 103.7, // Example exchange rate for GBP to INR
                        'USD' => 83.63, // Example exchange rate for USD to INR
                        'AED' => 20.38, // Example exchange rate for AED to INR
                    ];
                    // dd($currency);
                    // $currencydd  = $currencymain[$currency];
                    $exchangeRateInrToEur = 1 / $exchangeRates[$currency];

                    // dd($convertedAmounEctc, $convertedAmounCtc,$sal_mode,$convertedAmounEctc[$sal_mode]);

                    // Convert INR to EUR
                    $bill_rate_amount = $convertedAmounEctc[$sal_mode];
                    $pay_rate_amount = $convertedAmounCtc[$sal_mode];


                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);


                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $pay_rate_amount = explode('.', $pay_rate_amount)[0];

                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                    $bill_rate_amount = explode('.', $bill_rate_amount)[0];
                    // dd( $bill_rate_amount,$pay_rate_amount);
                    $pay_rate_mode = '';
                    $pay_rate_mode = @$salaryMode[@$jobMode];
                    //$current_ctc_mode		= mb_substr($salaryMode[0], 0, 1);

                    $bill_rate_mode = '';
                    $bill_rate_mode = @$salaryMode[@$jobMode];
                    //$current_ectc_mode		= mb_substr($salaryMode[0], 0, 1);

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$bill_rate_currency .
                        @$bill_rate_amount .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $bill_rate_mode .
                        '</span></a>
                    <span class="text-muted fw-bold d-block fs-7">' .
                        $pay_rate_currency .
                        @$pay_rate_amount .
                        '<span style="font-size:.85em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $pay_rate_mode .
                        '</span></span>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    $npLabel = $npArr[$row->serving_notice_period_mode];
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
                              $npLabel = 'Immediate';
                            }
                    } else {
                        $npDate = $row->np_id;
                    }

                    if ($npLabel === 'Immediate') {
                        $npColor = '#16a34a';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColor = '#d97706';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $npColor = '#7c3aed';
                        $npDate = 'SNP LWD ' . $npDate;
                    } else {
                        $npColor = '#181c32';
                    }

                    echo '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="fw-bolder fs-6" style="color:' . $npColor . ' !important;">' .
                        $npLabel .
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
                    $row_country_name = @$row->country_name;
                    $row_state_name = @$row->state_name;
                    $row_city_name = @$row->city_name;
                    echo '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row_city_name) . ', ' . strtoupper($row_country_name) .
                        '">' .
                        ucwords(mb_substr($row_city_name, 0, 10)) . ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) .
                        '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '">' .
                        ucwords(mb_substr(@$row->qualification_name, 0, 20)) .
                        '</span>
                    </div>';
                @endphp
            </td>
            <td>
                @php
                    $updated_at = '';
                    $updated_at = GeneralMethod::ymdTodmyNotTime($row->updated_at); //$newDate = date("d M , Y", strtotime($row->updated_at));
                    echo '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' .
                        @$updated_at .
                        '</span> </div>';
                @endphp
            </td>
            <td>
                @php
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        ->where('source_from_type', 1)
                        ->where('source_jobid', @$jobid)
                        ->count();

                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();

                     if($row->status==1){
                      
                        $html =
                            '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a><span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title=""> '.@$row->posted_by.'</span> </div>';
                     }else{
                        
                       
                            $html ='   <a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="3"  data-row-id="' .
                                @$row->id .
                                '" data-row-jobid="' .
                                @$jobid .
                                '">
                                                    Add to Job
                                                </a> ';

                     }       


                    
                    echo $html;

                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="8" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
