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
                    $profile_sidebar_css = 'bench_show_application_detail';

                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = explode(',', $row->skills);

                    $percentage=0;
                    $matches = $row->matched_skill_count;
                    $totalElements = count($requiredSkillsForDisplay ?? []);
                    $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                    $matchPercentage = min($matchPercentage, 100);
                    $percentage = round($matchPercentage, 1);

                    // Match % badge — color reflects the numeric match score, same
                    // thresholds/colors used across the other apptracker tabs: >90
                    // green, 50-90 orange, 0<50 red, exactly 0 neutral grey.
                    if ($percentage == 0) {
                        $matchBg = '#f1f5f9'; $matchColor = '#64748b';
                    } elseif ($percentage < 50) {
                        $matchBg = '#fee2e2'; $matchColor = '#dc2626';
                    } elseif ($percentage <= 90) {
                        $matchBg = '#fef3c7'; $matchColor = '#d97706';
                    } else {
                        $matchBg = '#dcfce7'; $matchColor = '#16a34a';
                    }
                    $matchBadge = '<span class="badge my-1" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;">Match '.$percentage.'%</span>';

                    // Matched skill highlighting, three tiers — mirrors
                    // internal_bench_ajax.blade.php: a match against a STARRED
                    // (mandatory) requirement is a different signal than a match
                    // against a skill that was just typed but not starred.
                    // $row->matched_skills and $row->matched_mandatory_skills are
                    // both computed server-side (GeneralMethod::matchedSkillsList())
                    // in dinternal_bench_data_ajax()'s must+should hybrid query.
                    $matchedSkillsArr = $row->matched_skills ?? [];
                    $mandatoryMatched = $row->matched_mandatory_skills ?? [];
                    $optionalMatched = array_values(array_udiff($matchedSkillsArr, $mandatoryMatched, 'strcasecmp'));
                    $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));
                    $maxUnmatchedShown = 6;
                    $unmatchedShown = array_slice($unmatchedSkillsArr, 0, $maxUnmatchedShown);
                    $unmatchedRemaining = max(count($unmatchedSkillsArr) - count($unmatchedShown), 0);

                    foreach ($mandatoryMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dbeafe;color:#1d4ed8;font-weight:600;border:1px solid #bfdbfe;" title="Matches a mandatory (starred) skill"><span style="color:#eab308;">★</span> ' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($optionalMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;" title="Matches a required skill">' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($unmatchedShown as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                    }
                    if ($unmatchedRemaining > 0) {
                        $moreTitle = implode(', ', array_map('ucfirst', array_slice($unmatchedSkillsArr, $maxUnmatchedShown)));
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;color:#7e8299;" title="'.htmlspecialchars($moreTitle, ENT_QUOTES).'">+' . $unmatchedRemaining . ' more</span>';
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
                    $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                        '';
                    $pay_rate_currency = $currencySymbole[$jobCurrency ?: 0] ?? '';
                    $bill_rate_currency = $currencySymbole[$jobCurrency ?: 0] ?? '';

                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[@$min_salary_currency ?: 0];
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                    $bill_rate_amount = $convertedAmounEctc[(int)($sal_mode ?? 0)] ?? 0;
                    $pay_rate_amount = $convertedAmounCtc[(int)($sal_mode ?? 0)] ?? 0;

                    

                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);

                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount ? @$payFormatViewSalary->amount : 0);
                    $pay_rate_amount = explode('.', $pay_rate_amount)[0];
                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount ? @$billFormatViewSalary->amount : 0);
                    $bill_rate_amount = explode('.', $bill_rate_amount)[0];
                    $pay_rate_mode = $salaryMode[$jobMode ?: 0] ?? '';
                    $bill_rate_mode = $salaryMode[$jobMode ?: 0] ?? '';

                    echo '<div class="d-flex flex-column">
					 <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$bill_rate_currency .
                        @$bill_rate_amount .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $pay_rate_mode .
                        '</span></a>
					 <span class="text-muted fw-bold d-block fs-7">' .
                        $bill_rate_currency .
                        @$pay_rate_amount .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $bill_rate_mode .
                        '</span></span>
					</div>';
                @endphp
            </td>
            <td>
                @php
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    $npLabel = $npArr[(int)($row->serving_notice_period_mode ?? 0)] ?? 'Notice period';
                    if ($row->serving_notice_period_mode == 0) {
                        $npDate = $row->np_id . ' Days';
                        if ($npDate == 1) {
                            $npDate = $row->np_id . ' Day';
                        }
                    }
                    if ($row->serving_notice_period_mode == 1) {
                        $originalDate = $row->np_id;
                        $npDate = 'SNP LWD ' . date('d-M-y', strtotime($originalDate));
                        $today = date('Y-m-d');
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $npDate = "Immediate";
                              $npLabel = 'Immediate';
                            }
                    } elseif ($row->serving_notice_period_mode == 2) {
                        $npDate == '';
                    }

                    // Availability color: Immediate = green, numeric days notice =
                    // orange, a real serving last-working-day date = purple. !important
                    // is required because Bootstrap/Metronic's .text-dark utility class
                    // sets color with !important, which otherwise beats a plain inline
                    // style — so the class is dropped below and color is fully
                    // inline-controlled instead.
                    if ($npLabel === 'Immediate') {
                        $npColor = '#16a34a';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColor = '#d97706';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $npColor = '#7c3aed';
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

                    // Row 2 is now Education (merged in from the removed Qualification
                    // column) instead of state — same qualification_name field/truncation
                    // used there.
                    echo '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row_city_name) . ', ' . strtoupper($row_country_name) .
                        '">' .
                        ucwords(mb_substr($row_city_name, 0, 10)) . ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) .
                        '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '">' .
                        ucwords(mb_substr(@$row->qualification_name, 0, 14)) .
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

                    $html = '';
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        ->where('source_jobid', @$jobid)
                        ->count();

                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();

                    if($row->status==2){
                        if ($existingCandidatesQuery > 0) {
                            $html =
                                '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a> </div>';
                        } else {
                            $html =
                                '<div class="d-flex flex-column"> 
                                <a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="1"  data-row-id="' .
                                @$row->id .
                                '" data-row-jobid="' .
                                @$jobid .
                                '">
                                    Add to Job
                                </a> 
                            </div>';
                        }
                    }else{
                        if($row->status==1){
                            $html =' <a href="javascript:void(0);" data-row-id="985" class="fw-bold fs-6  stage_sub_stage interview text-hover-primary" data-toggle="modal" data-target="#stage_sub_stage">
                             &nbsp;&nbsp;Pool&nbsp;&nbsp;</a>';
                        }elseif($row->status==4){
                            $html =' <a href="javascript:void(0);" data-row-id="985" class="fw-bold fs-6  stage_sub_stage hold-bg text-hover-warning" data-toggle="modal" data-target="#stage_sub_stage">
                                Pause</a>';
                        }elseif($row->status==3){
                            $html =' <a href="javascript:void(0);" data-row-id="985" class="fw-bold fs-6  stage_sub_stage active-bg text-hover-success" data-toggle="modal" data-target="#stage_sub_stage">
                                Placed</a>';
                        }
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
