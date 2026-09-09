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
    $row->id = $row->candidate_id;
    $row->thirdparty_company_id = $row->ClientComID;
    $jobMode = @$modeFilter;
    $jobCurrency = @$currencyFilter;
    $symbols = @$symbolsFilter;

    @endphp
        <tr style="height: 102px; position: relative;">
            <!-- check start  -->
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' .
                        $row->id .
                        '" />
                  </div>';
                @endphp
            </td>
            <!-- check end  -->
            <!-- Name start  -->
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

                    $duplicate = $row->duplicate ?? 0;
                    $flag = 0;
                    if ($duplicate == 1) {
                        $flag = $duplicate;
                    }
                    $covid = '';

                    $company_id = auth()->user()->company_id;
                    $profile_sidebar_css = 'show-application-detail';
                    if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                        $profile_sidebar_css = 'client-show-application-detail';
                    }
                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = $row->matched_skills;//explode(',', $row->skills);

                    $percentage=0;
                    $matches = $row->matched_skill_count;
                    $totalElements = count($requiredSkills);
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
                    // Tooltip breaking down WHY this candidate scored this
                    // percentage — matched vs missing skills — so the badge
                    // isn't just an unexplained number. Uses a plain native
                    // title attribute since this partial is AJAX-injected.
                    $missingSkills = array_values(array_filter($requiredSkills, function ($required) use ($skillArr) {
                        foreach ($skillArr as $matched) {
                            if (GeneralMethod::skillMatchesPhrasePrefix($required, $matched)) return false;
                        }
                        return true;
                    }));
                    $matchTooltipParts = [];
                    if (!empty($skillArr)) {
                        $matchTooltipParts[] = 'Matched: ' . implode(', ', array_map('ucfirst', $skillArr));
                    }
                    if (!empty($missingSkills)) {
                        $matchTooltipParts[] = 'Missing: ' . implode(', ', array_map('ucfirst', $missingSkills));
                    }
                    $matchTooltipText = !empty($matchTooltipParts) ? implode(' — ', $matchTooltipParts) : 'No skills specified in filter';

                    $matchBadge = '<span class="badge my-1" title="'.htmlspecialchars($matchTooltipText, ENT_QUOTES).'" style="background:' . $matchBg . ';color:' . $matchColor . ';font-weight:600;cursor:help;">Match ' . $percentage . '%</span>';

                    // Matched skill highlighting, three tiers — mirrors
                    // Similar Candidates' similar_existing_ajax.blade.php.
                    $mandatoryMatched = $row->matched_mandatory_skills ?? [];
                    $optionalMatched = array_values(array_udiff($skillArr, $mandatoryMatched, 'strcasecmp'));
                    $allSkillsList = $row->all_skills ?? $skillArr;
                    $unmatchedSkills = array_values(array_udiff($allSkillsList, $skillArr, 'strcasecmp'));
                    $maxUnmatchedShown = 6;
                    $unmatchedShown = array_slice($unmatchedSkills, 0, $maxUnmatchedShown);
                    $unmatchedRemaining = max(count($unmatchedSkills) - count($unmatchedShown), 0);

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
                        $moreTitle = implode(', ', array_map('ucfirst', array_slice($unmatchedSkills, $maxUnmatchedShown)));
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;color:#7e8299;" title="'.htmlspecialchars($moreTitle, ENT_QUOTES).'">+' . $unmatchedRemaining . ' more</span>';
                    }

                    if ($flag == 1) {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                            <a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
                            '" class=" ' .
                            $profile_sidebar_css .
                            ' btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
                            <span class="fs-3">' .
                            strtoupper(@$strVal) .
                            '</span>
                            <span class="pulse-ring"></span>
                            </a>
                            </div>                  
                            <div class="d-flex flex-column">
                            <a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
                            '"  class="text-danger fw-bolder text-hover-primary fs-6  ' .
                            $profile_sidebar_css .
                            ' " data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 22)) .
                            $covid .
                            '</a>
                            <a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
                            '"  class="text-danger fw-bold d-block fs-7 text-hover-primary ' .
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
                    } else {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                            <a href="javascript:void(0);"   data-row-id="' .
                            @$row->wiseId .
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
                            @$row->wiseId .
                            '"  class="text-dark fw-bolder text-hover-primary fs-6 ' .
                            $profile_sidebar_css .
                            ' " data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 22)) .
                            $covid .
                            '</a>
                            <a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
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
                    }
                @endphp
            </td>
            <!-- Name end  -->
            <!-- Exp start  -->
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
            <!-- Exp end  -->
            <!-- Salary start  -->
            <td>
                @php
                    $current_ctc_cur = '';
                    $current_ctc_cur = $currencySymbole[@$jobCurrency];

                    $current_ectc_cur = '';
                    $current_ectc_cur = $currencySymbole[@$jobCurrency];

                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[@$min_salary_currency];
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                    $bill_rate_amount = $convertedAmounEctc[$sal_mode];
                    $pay_rate_amount = $convertedAmounCtc[$sal_mode];


                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);

                    $ctc = '';
                    $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $ctc = explode('.', $ctc)[0];
                    $ectc = '';
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                    $ectc = explode('.', $ectc)[0];

                    $current_ctc_mode = '';
                    $current_ctc_mode = @$salaryMode[$jobMode];

                    $current_ectc_mode = '';
                    $current_ectc_mode = @$salaryMode[$jobMode];

                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$current_ectc_cur .
                        @$ectc .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $current_ectc_mode .
                        '</span></a>
                            <span class="text-muted fw-bold d-block fs-7">' .
                        $current_ctc_cur .
                        @$ctc .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $current_ctc_mode .
                        '</span></span>
                        </div>';
                @endphp
            </td>
            <!-- Salary end  -->
            <!-- Notice start  -->
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
                    } else {
                        $np = $row->np_id;
                    }

                    if ($row->serving_notice_period_mode == 1) {
                        $originalDate = $row->np_id;
                        $np = 'SNP LWD ' . date('d-M-y', strtotime($originalDate));
                        $today = date('Y-m-d');
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }
                    }

                    // Notice period color: Immediate = green, numeric days notice =
                    // orange, a real serving last-working-day date = purple. !important
                    // is required because Bootstrap/Metronic's .text-dark utility class
                    // sets color with !important, which otherwise beats a plain inline
                    // style — so the class is dropped below and color is fully
                    // inline-controlled instead.
                    $npColorStyle = 'color:#181c32 !important;';
                    if ($np === 'Immediate') {
                        $npColorStyle = 'color:#16a34a !important;';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColorStyle = 'color:#d97706 !important;';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $npColorStyle = 'color:#7c3aed !important;';
                    }

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fw-bolder fs-6" style="' .
                        $npColorStyle .
                        '">' .
                        @$np .
                        '</a>
                   <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .htmlspecialchars(ucwords(@$row->current_company), ENT_QUOTES, 'UTF-8') .
                        '">' . htmlspecialchars(ucwords(mb_substr(@$row->current_company, 0, 10)), ENT_QUOTES, 'UTF-8') .
                        '</span>
                  </div>';
                @endphp
            </td>
            <!-- Notice end  -->
            <!-- Location start  -->
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
            <!-- Location end  -->
            <!-- Jobs start  -->
            <td>
                @php

                    $removed_status = $icon = '';
                    if ($row->final_status == 2) {
                        $removed_status = '';
                    }

                    //$icon				=	'&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';
                    if ($row->thirdparty_company_id == $company_id) {
                        //$icon			=	'&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    }

                    $job_title = '';
                    $job_title = $row->title;
                    $job_title = ucwords(mb_substr($job_title, 0, 15));

                    $client_name = '';
                    $client_name = $row->client_name;
                    $client_name = ucwords(mb_substr($client_name, 0, 15));
                    if ($row->client_comapny_id != $company_id) {
                        $client_name = $row->company_name;
                    }

                    echo '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        $row->title .
                        '">' .
                        $job_title .
                        '</a>
                    	<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        $row->client_name .
                        '">' .
                        $client_name .
                        $icon .
                        $removed_status .
                        '</span>
                  	</div>';

                @endphp
            </td>
            <!-- Jobs end  -->
            <!-- Date start  -->
            {{-- <td>
                @php

                    $created_at = '';
                    $created_at = $newDate = date('d M , Y', strtotime($row->created_at));

                    echo '<div class="d-flex flex-column">
                    			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$created_at .
                        '</a> 
                  			</div>';
                @endphp
            </td> --}}
            <!-- Date end  -->
            <!-- Status start  -->
            <td>
                @php
                    // Job status: mirrors jobs.status ('active'/'hold'/'inactive') as
                    // stored in Elasticsearch — only these 3 states are shown, as a
                    // colored badge (green/orange/red), matching the Match% badge style
                    // used elsewhere on this page.
                    $job_status_get = GeneralMethod::jobDetails($row->jobId);
                    $rawStatus = strtolower(@$job_status_get->status);

                    if ($rawStatus == 'hold') {
                        $status = 'Hold';
                        $statusBg = '#fef3c7'; $statusColor = '#d97706';
                    } elseif ($rawStatus == 'inactive') {
                        $status = 'Closed';
                        $statusBg = '#fee2e2'; $statusColor = '#dc2626';
                    } else {
                        $status = 'Hiring';
                        $statusBg = '#dcfce7'; $statusColor = '#16a34a';
                    }

                    // Row 2: Job ID + Internal/Client tag — same recommendation_text
                    // logic this page already used.
                    $recommendation_text = __('messages.internal_tag');
                    if ($row->client_comapny_id != $company_id) {
                        $recommendation_text = __('messages.clinet_tag');
                    }

                    echo '<div class="d-flex flex-column">
                       <span class="badge my-1" style="background:' .
                        $statusBg .
                        ';color:' .
                        $statusColor .
                        ';font-weight:600;width:fit-content;">' .
                        $status .
                        '</span>
                      <span class="fw-bold text-muted d-block fs-7">' .
                        @$row->jobId .
                        ' -' .
                        $recommendation_text .
                        '</span>
                    </div>';
                @endphp
            </td>
            <!-- Status end  -->
            <!-- Source start  -->
            <td>
                @php
                if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                    $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                    $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                    $rowpostedby = $row->rec_candidate_assign_id ? $row->posted_by :  $rowpostedby;
                }else{
                    $rowpostedby = $row->posted_by;
                }

                    $bgTextDecore = $strVal = '';
                    $str = trim(@$rowpostedby);
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

                    $posted_by_image = '';
                    $posted_by_image = ''; //url('public/avatar.png');
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = @$row->posted_by_image;
                    }

                    $icon =
                        '<div data-row-id="' .
                        $row->id .
                        '" class="symbol symbol-30 symbol-circle overflow-hidden me-2 border-radius-5 client-show-detail" title="' .
                        @$rowpostedby .
                        '" style="float:left">
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
                    if (!empty($posted_by_image)) {
                        $icon =
                            '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' .
                            @$rowpostedby .
                            '">
							<img alt="Pic" src="' .
                            @$posted_by_image .
                            '">
						   </div>';
                    }

                    // Row 2 is the recruiter's represented company — same
                    // post_company_name (falling back to company_name) field used on the
                    // Similar Candidates tab of the main apptracker page.
                    $post_company_name = @$row->post_company_name;
                    if (!$post_company_name) {
                        $post_company_name = @$row->company_name;
                    }

                    echo '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$rowpostedby .
                        '">' .
                        ucfirst(mb_substr($rowpostedby, 0, 10)) .
                        '</a>
						  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$post_company_name .
                        '">' .
                        mb_substr(@$post_company_name, 0, 10) .
                        '</span>
                        </div>';
                @endphp
            </td>
            <!-- Source end  -->
            <!-- Stage start  -->
            <td>
                @php
                    $stage_default_css = 'stage_sub_stage';

                    if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                        $stage_default_css = 'client_stage_sub_stage';
                    }

                    if (
                        $row->thirdparty_company_id > 0 &&
                        $row->thirdparty_company_id == $company_id &&
                        $row->master_status_id != 8
                    ) {
                        $stage_default_css = '';
                    }

                    $name = '';
                    if ($row->master_status_id == 8) {
                        $name = '';
                    }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')
                            ->select('*')
                            ->where('id', $row->child_status_id)
                            ->first();
                        if (!empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }
                    echo '<div class="d-flex flex-column">
                        <a href="javascript.void(0);" data-row-id="' .
                        @$row->id .
                        '" class="d-flex flex-column ' .
                        $stage_default_css .
                        '" data-toggle="modal" data-target="#' .
                        $stage_default_css .
                        '">
                            <span class="badge fw-bolder fs-6 ' .
                        @$row->css_style .
                        ' px-2 py-1">' .
                        ucwords(mb_substr($row->name, 0, 10)) .
                        '</span>
                            <span class="text-muted fw-bold d-block fs-7">' .
                        @$name .
                        '</span>
                        </a>
                    </div>';

                @endphp
            </td>
            <!-- Stage end  -->
            <!-- Action start  -->
            <td>
                @php
                    $source_from_id = @$row->id;
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        //->where('source_from_type', 0)
                        ->where('source_jobid', @$jobid)
                        /*->orWhere(function ($query) use($source_from_id, $jobid) {
																						 $query->where('source_application_id', '=', $source_from_id)
																						 ->where('source_from_type', '=', '1')
																						 ->where('source_jobid', '=', $jobid);
																			 })*/
                        ->count();

                    $source_from_type = 0;
                    $public_bench_conter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_type', '=', 2)
                        ->where('source_company_id', '=', $company_id)
                        ->where('source_application_id', '=', @$row->id)
                        ->count();

                    if ($public_bench_conter > 0) {
                        $source_from_type = 2;
                    }

                    $internal_bench_conter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_type', '=', 1)
                        ->where('source_company_id', '=', $company_id)
                        ->where('source_application_id', '=', @$row->id)
                        ->count();

                    if ($internal_bench_conter > 0) {
                        $source_from_type = 1;
                    }

                    if ($recCounter > 0) {
                        $html =
                            '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a> </div>';
                    } else {
                        $public_bench_conter = DB::table('rec_candidate_assign')
                            ->select('id')
                            ->where('source_from_type', '=', 2)
                            ->where('source_company_id', '=', $company_id)
                            ->where('source_jobid', '=', $jobid)
                            ->where('parent_id', '=', @$row->id)
                            ->count();
                        $internal_bench_conter = DB::table('rec_candidate_assign')
                            ->select('id')
                            ->where('source_from_type', '=', 1)
                            ->where('source_company_id', '=', $company_id)
                            ->where('source_jobid', '=', $jobid)
                            ->where('parent_id', '=', @$row->id)
                            ->count();

                            $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();


                        if ($public_bench_conter > 0) {
                            $html =
                                '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a> </div>';
                        } elseif ($internal_bench_conter > 0) {
                            $html =
                                '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a> </div>';
                        }elseif($existingCandidatesQuery){
                            $html =
                            '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a> </div>';
                        } else {
                            $html =
                                '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-from="existing" data-row-source_from_type="' .
                                @$source_from_type .
                                '"  data-row-id="' .
                                @$row->candidate_id .
                                '" data-row-jobid="' .
                                @$jobid .
                                '">
										Add to Job
									</a> 
								</div>';
                        }
                    }
                    echo $html;
                @endphp
            </td>
            <!-- Action end  -->

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="11" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
