@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

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

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->thirdparty_company_id = $row->ClientComID;

    $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$row->jobId)->first();
    $jobMode = $jobData->jobMode ?? @$row->current_ctc_mode;
    if(!empty($modeFilter)){
       $jobMode = @$modeFilter;
    }
    $jobCurrency = $jobData->jobCurrency ?? @$row->current_ctc_cur;
    if(!empty($currencyFilter)){
        $jobCurrency = @$currencyFilter;
    }
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";
    if(!empty($modeFilter)){
        $symbols = @$symbolsFilter;
    }
    

    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" data-jobId="' .
                        $row->jobId .'" data-wiseId="' .$row->wiseId .'" value="' .$row->candidate_id .'" />
                  </div>';
                @endphp
            </td>
            <td>
                @php

                    $bgTextDecore = $strVal = $tredmark = '';
                    $str = trim(@$row->full_name);
                    if (!empty($str)) {
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

                        $company_id = auth()->user()->company_id;
                        $profile_sidebar_css = 'show-application-detail';
                        if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                            $profile_sidebar_css = 'show-application-detail';
                            // client-show-application-detail
                        }

                        if (@$row->condidate_type == 'bench_condidate') {
                            $tredmark = '[Bench]';
                        }

                        $photo = '';
                        if (!empty(@$row->photo)) {
                            //   $photo			=	 url('/public/user-uploads/candidate-photos/'.$row->photo);
                            $photo = @$row->photo;
                        }

                        if (!empty($photo)) {
                            $nameImg =
                                '<div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="' .
                                @$photo .
                                '" alt="image" style="width: 40px; height: 40px;">
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                            </div>';
                        } else {
                            if ($flag == 1) {
                                $nameImg = '<span class="fs-3">' . strtoupper(@$strVal) . '</span>';
                            } else {
                                $nameImg =
                                    '<div class="symbol-label ' .
                                    @$bgTextDecore .
                                    ' border-radius-5">' .
                                    strtoupper(@$strVal) .
                                    '</div>';
                            }
                        }


                                                
                        $skilltag = '';
                        $skilltagclass = '';
                        $skillArr = explode(',', $row->skills);

                        if(@$row->wiseId){
                            $percentage =  GeneralMethod::skill_percentage($row->jobId,$skillArr);
                        }
                        

                        // dd($per,$job_skills,$skillArr);

                        // dd($skillArr);
                        if (empty($limit)) {
                            if (count($skillArr) > 0) {
                                foreach ($skillArr as $key => $sval) {
                                    $skilltag .=
                                        '<span class="badge badge-light my-1 skill" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                                }
                            }
                        } else {
                            $skilltagclass = 'd-none';
                        }

                        if ($flag == 1) {
                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
																				<a href="javascript:void(0);"  data-row-id="' .
                                @$row->wiseId .
                                '" class=" ' .
                                $profile_sidebar_css .
                                ' btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
																				' .
                                $nameImg .
                                '
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
                                ucwords(mb_substr(@$row->full_name, 0, 30)) .
                                $covid .
                                '<sup style="color: #0865dd !important;
																				font-weight: bold;">' .
                                $tredmark .
                                '</sup></a>
																				<a href="javascript:void(0);"  data-row-id="' .
                                @$row->wiseId .
                                '"  class="text-danger fw-bold d-block fs-7 text-hover-primary ' .
                                $profile_sidebar_css .
                                ' " data-toggle="tooltip" title="' .
                                ucwords(@$row->current_designation) .
                                '">' .
                                ucwords(mb_substr(@$row->current_designation, 0, 30)) .
                                '</a>
																				</div>
																				<div class="' .
                                $skilltagclass .
                                '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light my-1"> '.$percentage.'</span>
                                 
                                  ' .
                                $skilltag .
                                '
                                         
                                   
                                    </div> 	';
                        } else {
                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
																				<a href="javascript:void(0);"   data-row-id="' .
                                @$row->wiseId .
                                '" class="' .
                                $profile_sidebar_css .
                                '">
																				' .
                                $nameImg .
                                '
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
                                ucwords(mb_substr(@$row->full_name, 0, 30)) .
                                $covid .
                                '<sup style="color: #0865dd !important;
																				font-weight: bold;">' .
                                $tredmark .
                                '</sup></a>
																				<a href="javascript:void(0);"  data-row-id="' .
                                @$row->wiseId .
                                '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                                $profile_sidebar_css .
                                ' " data-toggle="tooltip" title="' .
                                ucwords(@$row->current_designation) .
                                '">' .
                                ucwords(mb_substr(@$row->current_designation, 0, 30)) .
                                '</a>
																				</div>
																				<div class="' .
                                $skilltagclass .
                                '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light my-1"> '.$percentage.'</span>
                                 
                                  ' .
                                $skilltag .
                                '
                                         
                                   
                                    </div> ';
                        }
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

                    // echo $formattedExperience . ' Y';

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

                    if (in_array('salary_view', $userPermissions)) {
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
                    } else {
                        echo '<div class="d-flex flex-column">
					<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Confidential</a>
						<span class="text-muted fw-bold d-block fs-7 ' .
                            $dnone .
                            '">Confidential</span>
					</div>';
                    }

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

                    if ($row->condidate_type === 'bench_condidate') {
                        $row->current_company = @$row->apl_company_name;
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
                    echo ' <div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '" style="text-transform: uppercase;!important">' .
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
                    $bgTextDecore = GeneralMethod::icoinText(@$row->posted_by);
                    $posted_by_image = '';
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = @$row->posted_by_image;
                    }

                   

                    if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                        $rowpostedby = $row->rec_candidate_assign_id ? $row->posted_by :  $rowpostedby;
                    }else{
                        $rowpostedby = $row->posted_by;
                    }
                    // $rowpostedby = ucwords($row->posted_by); post_company_name
                    
                    $post_company_name = @$row->post_company_name;
                    if (!$post_company_name) {
                        $post_company_name = @$row->company_name;
                    }


                    echo '<div class="d-flex flex-column">
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$rowpostedby.
                        '">' .
                        ucfirst(mb_substr($rowpostedby, 0, 10)) .
                        '</a>
												
						  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$post_company_name .
                        '">' .
                        mb_substr(@$post_company_name, 0, 10) .
                        '</span>';
                    // }
                @endphp
            </td>
            <td>
                @php
                    $removed_status = $icon = '';
                    if ($row->final_status == 2) {
                        // $removed_status = '<div class=" fw-bolder fs-6 closed-bg px-2 py-1">REMOVED</div>';
                    }
                    //$icon				=	'&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';
                    if ($row->thirdparty_company_id == $company_id) {
                        //$icon			=	'&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    }
                    $job_title = '';
                    $job_title = $row->title;
                    $job_title = ucwords(mb_substr($job_title, 0, 15));
                    $client_name = '';
                    $client_title_name = '';
                    $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
                    if (!empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    } else {
                        $client_title_name = $row->client_name;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    }

                    echo '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        $row->title .
                        '">' .
                        $job_title .
                        '</a>
                    	<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$client_title_name .
                        '">' .
                        @$client_name .
                        $icon .
                        $removed_status .
                        '</span>
                  	</div>';
                @endphp
            </td>
            <td>
                @php
                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';

                    // $job_status = $row->job_status;
                    
                    $job_status_get = GeneralMethod::jobDetails($row->jobId);
                    $job_status = $job_status_get->status;

                    $status = ucwords($job_status);
                    $class = '';
                    //$class			=	$job_status_arr['active'];
                    $class = $job_status_arr[$job_status];
                    $recommendation_id = $row->recommendation_id;
                    
                    
                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }

                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }

                    $final_status = '';
                    if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
                        //$status = 'REMOVED';
                        // $class = "closed-bg";
                    }

                    $ulink =
                        '<a href="javascript:void(0);" class="d-flex flex-column ' .
                        @$class .
                        '  text-hover-success" >' .
                        $status .
                        '</a>';

                    $recommendation_text = __('messages.clinet_tag');
                    if (
                        ($recommendation_id > 0 && @$row->client_comapny_id == auth()->user()->company_id) ||
                        @$row->client_comapny_id == auth()->user()->company_id
                    ) {
                        $recommendation_text = __('messages.internal_tag');
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
                        @$row->jobId .
                        ' -' .
                        $recommendation_text .
                        '</span>
                </div>';
                @endphp
            </td>

            <td>
                @php
                    $stage_default_css = 'stage_sub_stage';

                    if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                        $stage_default_css = 'client_stage_sub_stage';
                    }
                    if (
                        $row->thirdparty_company_id > 0 &&
                        $row->thirdparty_company_id == $company_id &&
                        $row->job_master_status_id != 8
                    ) {
                        $stage_default_css = '';
                    }
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
