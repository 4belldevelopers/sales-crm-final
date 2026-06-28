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

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->thirdparty_company_id = $row->ClientComID;

    $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$row->jobId)->first();
    $jobMode = $jobData->jobMode ?? @$row->current_ctc_mode;
    $jobCurrency = $jobData->jobCurrency ?? @$row->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";

    @endphp
        <tr>
            <td style="height: 102px; position: relative;">
                @php

                    $bgTextDecore = $strVal = '';
                    if (!empty(@$row->full_name)) {
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
                        $profile_sidebar_css = 'show-application-detail';
                        if ($row->thirdparty_company_id > 0) {
                            $profile_sidebar_css = 'client-show-application-detail';
                        }
                        $skilltag = '';
                        $skilltagclass = '';
                        $percentage = '';
                        $skillArr = explode(',', $row->skills);
                        if(@$row->wiseId){
                            $percentage =  GeneralMethod::skill_percentage($row->jobId,$skillArr);
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
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 30)) .
                            '</a>
                                <a href="javascript:void(0);" data-row-id="' .
                            @$row->wiseId .
                            '"
                                    class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                            $profile_sidebar_css .
                            '"
                                    data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 20)) .
                            '</a>
                            </div>
                            <div class="' .
                                $skilltagclass .
                                '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light my-1"> '.$percentage.'</span>
                                 
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

                    $current_ctc_cur = '';
                    $current_ctc_cur = $currencySymbole[@$jobCurrency];

                    $current_ectc_cur = '';
                    $current_ectc_cur = $currencySymbole[@$jobCurrency];

                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_cur = $currencySymbole[@$jobCurrency];
                    }

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
                    // $row->current_company = (@$row->current_company) ? @$row->apl_company_name : @$row->apl_company_name;
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

                    echo ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" style="text-transform: uppercase;!important">' .
                        ucwords(mb_substr(@$row->qualification_name, 0, 10)) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        strtoupper(@$row->skills) .
                        '"></span> 
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
                        ucfirst(mb_substr($row_state_name, 0, 10)) .
                        ' | ' .
                        strtoupper(mb_substr($row_country_name, 0, 2)) .
                        '</span>
                    </div>';

                @endphp
            </td>
            <td>
                @php
                if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                        $rowpostedby = $row->rec_candidate_assign_id ? $row->posted_by :  $rowpostedby;
                    }else{
                        $rowpostedby = $row->posted_by;
                    }
                    
                    
                    $post_company_name = @$row->post_company_name;
                    if (!$post_company_name) {
                        $post_company_name = @$row->company_name;
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

                    echo ' <div class="d-flex flex-column">
                  				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$rowpostedby .
                        '">' .
                        ucfirst(mb_substr($rowpostedby, 0, 10)) .
                        '</a>
                  			</div>
							<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        @$post_company_name.
                        '">' .
                        ucwords(mb_substr(@$post_company_name, 0, 10)) .
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
                    $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
                    if (!empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    } else {
                        $client_title_name = $row->client_name;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    }

                    echo '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($jobtitle) .
                        '">' .$jobtitle.'</a> 
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($client_name) .
                        '">' .$client_name.
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
