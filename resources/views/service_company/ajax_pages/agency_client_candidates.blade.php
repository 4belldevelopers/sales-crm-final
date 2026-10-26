@php
    use App\Helper\GeneralMethod;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0	=>	"INR",
        1	=>	"EUR",
        2	=>	"JPY",
        3	=>	"GBP",
        4	=>	"USD",
        5	=>	"AED"
     ];

    $jobIds = collect($data)->pluck('jobId')->filter()->unique()->toArray();
    $cityIds = collect($data)->pluck('city_id')->filter()->unique()->toArray();
    $childStatusIds = collect($data)->pluck('child_status_id')->filter()->unique()->toArray();

    $jobsMap = !empty($jobIds) ? DB::table('jobs')->select('id', 'max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')->whereIn('id', $jobIds)->get()->keyBy('id') : collect();
    $citiesMap = !empty($cityIds) ? DB::table('city_list')->select('city_id', 'city')->whereIn('city_id', $cityIds)->get()->keyBy('city_id') : collect();
    $childStatusMap = !empty($childStatusIds) ? DB::table('candidate_status')->select('id', 'name')->whereIn('id', $childStatusIds)->get()->keyBy('id') : collect();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->thirdparty_company_id = $row->ClientComID;

    $jobData = $jobsMap->get($row->jobId);
    $jobMode = $jobData->jobMode ?? @$row->current_ctc_mode;
    $jobCurrency = $jobData->jobCurrency ?? @$row->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";

    @endphp
        <tr>
            <td style="height: 102px; position: relative;">
                @php

                    $bgTextDecore = $strVal = '';
                    $fullName = !empty($row->full_name) ? $row->full_name : (!empty($row->name) ? $row->name : (!empty($row->candidate_name) ? $row->candidate_name : trim((@$row->first_name ?? '') . ' ' . (@$row->last_name ?? ''))));
                    $designationName = !empty($row->current_designation) ? $row->current_designation : (!empty($row->designation_name) ? $row->designation_name : (@$row->designation ?? ''));

                    if (!empty($fullName)) {
                        $str = trim($fullName);
                        $strVal = strtolower($str[0] ?? 'a');
                        $str = ord($strVal);
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
                        $profile_sidebar_css = 'show-application-detail';
                        if ($row->thirdparty_company_id > 0) {
                            $profile_sidebar_css = 'client-show-application-detail';
                        }
                        $skilltag = '';
                        $skilltagclass = '';
                        $percentage = '';
                        $matchBadge = '';
                        $skillArr = explode(',', $row->skills);
                        if(@$row->wiseId){
                            $percentage =  GeneralMethod::skill_percentage($row->jobId,$skillArr);
                            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
                            $matchBadge = '<span class="badge my-1" style="background:'.$matchStyle['bg'].';color:'.$matchStyle['color'].';font-weight:600;">Match '.$percentage.'</span>';
                        }
                        if (count($skillArr) > 0) {
                                foreach ($skillArr as $key => $sval) {
                                    $skilltag .=
                                        '<span class="badge badge-light my-1 skill" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                                }
                            }

                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                style="float:left">
                                <a href="javascript:void(0);" data-row-id="' .
                            @$row->wiseId .
                            '"
                                    class="' .
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
                                <a href="javascript:void(0);" data-row-id="' .
                            @$row->wiseId .
                            '"
                                    class="text-dark fw-bolder text-hover-primary fs-6 ' .
                            $profile_sidebar_css .
                            '"
                                    data-toggle="tooltip" title="' .
                            htmlspecialchars(ucwords($fullName), ENT_QUOTES, 'UTF-8') .
                            '">' .
                            htmlspecialchars(ucwords(mb_substr($fullName, 0, 30)), ENT_QUOTES, 'UTF-8') .
                            '</a>
                                <a href="javascript:void(0);" data-row-id="' .
                            @$row->wiseId .
                            '"
                                    class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                            $profile_sidebar_css .
                            '"
                                    data-toggle="tooltip" title="' .
                            htmlspecialchars(ucwords($designationName), ENT_QUOTES, 'UTF-8') .
                            '">' .
                            htmlspecialchars(ucwords(mb_substr($designationName, 0, 20)), ENT_QUOTES, 'UTF-8') .
                            '</a>
                            </div>
                            <div class="' .
                                $skilltagclass .
                                '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    ' . $matchBadge . '
                                 
                                  ' .
                                $skilltag .
                                '
                                         
                                   
                                    </div> 	
                            
                            ';
                    } else {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                style="float:left">&nbsp;</div>';
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

                    $safeJobCur = @$jobCurrency ?? 0;
                    $current_ctc_cur = $current_ectc_cur = $currencySymbole[$safeJobCur] ?? ($currencySymbole[\App\Services\CurrencyService::resolveCurrencyCode($safeJobCur)] ?? '');

                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);

                    $ctc = '';
                    $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $ectc = '';
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                    if ($row->condidate_type == 'bench_condidate') {
                        $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                    }

                    $current_ctc_mode = '';
                    $current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);

                    $current_ectc_mode = '';
                    $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
                    }
                    $dnone = '';
                    if ($row->condidate_type == 'bench_condidate') {
                        $dnone = 'd-none';
                    }

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
                  </div>';

                @endphp

            </td>
            <td>
                @php

                    $np = $row->np_id;
                    if (empty(@$np)) {
                        $np = 'N/A';
                    }
                    if (@$row->serving_notice_period_mode == 0) {
                        if (!empty(@$row->np_id)) {
                            $np = $row->np_id . '  Days';
                        }
                    } elseif (@$row->serving_notice_period_mode == 1) {
                        $np = $row->np_id;
                        if ($np != 'Immediate') {
                            $np = GeneralMethod::ymdTodmyNotTime(@$row->np_id); //date("d-M-Y",strtotime(@$row->np_id));
                            $today = date('Y-m-d'); 
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }
                        }
                    } else {
                        $np = $row->np_id;
                    }
                    $currentCompany = !empty($row->current_company) ? $row->current_company : (!empty($row->apl_company_name) ? $row->apl_company_name : (!empty($row->company_name) ? $row->company_name : ''));
                    echo '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        @$np .
                        '</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .htmlspecialchars(ucwords($currentCompany), ENT_QUOTES, 'UTF-8') .
                        '">' . htmlspecialchars(ucwords(mb_substr($currentCompany, 0, 20)), ENT_QUOTES, 'UTF-8') .
                        '</span>
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
                            } else {
                                $row_country_name = @$row->country;
                            }
                        }
                    }

                    $state = '';
                    $row_state_name = '';
                    if (empty($row->state)) {
                        $row_state_name = '';
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
                            } else {
                                $row_state_name = @$row->state;
                            }
                        }
                    }

                    $city = '';
                    $row_city_name = '';

                    if (@$row->country == 101) {
                        $row_city = $citiesMap->get(@$row->city_id);
                        $row_city_name = @$row_city->city;
                    } else {
                        $row_city_name = @$row->city;
                    }
                    if (empty($row_city_name) && !empty($row->city_name)) {
                        $row_city_name = $row->city_name;
                    }

                    $countryCode = '';
                    if (!empty($row_country_name) && $row_country_name != '----') {
                        $countryCode = strtoupper(mb_substr($row_country_name, 0, 2));
                    }

                    $locationTitle = trim(ucwords($row_city_name) . ($countryCode ? ', ' . $countryCode : ''), ', ');
                    if (empty($locationTitle)) {
                        $locationTitle = '----';
                    }

                    $locationSub = !empty($row->qualification_name) ? strtoupper($row->qualification_name) : (!empty($row_state_name) ? ucwords($row_state_name) : '');

                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        htmlspecialchars($locationTitle, ENT_QUOTES, 'UTF-8') .
                        '">' .
                        htmlspecialchars($locationTitle, ENT_QUOTES, 'UTF-8') .
                        '</a>
                        <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        htmlspecialchars($locationSub, ENT_QUOTES, 'UTF-8') .
                        '">' .
                        htmlspecialchars(mb_substr($locationSub, 0, 15), ENT_QUOTES, 'UTF-8') .
                        '</span>
                    </div>';

                @endphp
            </td>
            <td>
                @php
                if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                    }else{
                        $rowpostedby = $row->posted_by;
                    }
                    if (empty($rowpostedby) && !empty($row->spokperson_name)) {
                        $rowpostedby = $row->spokperson_name;
                    }

                    $post_company_name = @$row->submitted_by_company_name ?: (@$row->post_company_name ?: (@$row->uc_company_name ?: (@$row->from_company_name ?: (@$row->company_name ?: ''))));

                    echo ' <div class="d-flex flex-column">
                  				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        htmlspecialchars(@$rowpostedby, ENT_QUOTES, 'UTF-8') .
                        '">' .
                        htmlspecialchars(ucfirst(mb_substr($rowpostedby, 0, 20)), ENT_QUOTES, 'UTF-8') .
                        '</a>
                  			</div>
							<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        htmlspecialchars(@$post_company_name, ENT_QUOTES, 'UTF-8') .
                        '">' .
                        htmlspecialchars(ucwords(mb_substr(@$post_company_name, 0, 20)), ENT_QUOTES, 'UTF-8') .
                        '</span>';

                @endphp
            </td>
            <td>
                @php

                    $UploadDate = $className = '';
                    $UploadDate = @$row->created_at;
                    $UploadDate = date('d M, Y', strtotime($UploadDate));
                    $recommendation_text = __('messages.clinet_tag');
                    if (@$row->client_comapny_id == auth()->user()->company_id) {
                        $recommendation_text = __('messages.internal_tag');
                    }
                    echo '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$UploadDate .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="">' .
                                $row->jobId .
                                '-' .
                                $recommendation_text .
                                '</span> 
                    </div>';

                @endphp
            </td>
            <td>
                @php

                    $jobtitle = $client_name = '';
                    $jobtitle = ucwords(mb_substr(@$row->title, 0, 10));
                    $client_name = ucwords(mb_substr(@$row->client_name, 0, 10));

                    echo '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($jobtitle) .
                        '">' .
                        ucwords(mb_substr($jobtitle, 0, 6)) .
                        '</a> 
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($client_name) .
                        '">' .
                        ucwords(mb_substr($client_name, 0, 6)) .
                        '</span>  
					</div>';

                @endphp
            </td>
            <td>
                @php

                    $stage_default_css = 'stage_sub_stage';
                    if ($row->thirdparty_company_id > 0) {
                        $stage_default_css = 'client_stage_sub_stage';
                    }
                    $name = '';
                    if ($row->job_master_status_id == 8) {
                        $name = 'Private';
                    }

                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $childStatus = $childStatusMap->get($row->child_status_id);
                        if (!empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }
                    echo '<div class="d-flex flex-column">
                    <a href="javascript.void(0);" data-row-id="' .
                        @$row->wiseId .
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
                        <span class="text-muted fw-bold d-block fs-7 px-3 py-1">' .
                        @$name .
                        '</span>
                    </a>
                </div>';

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
