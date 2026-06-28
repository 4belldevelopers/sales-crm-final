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
    // dd($row);
    $jobs = @$row->jobs;
    $array = collect($jobs)
    ->unique('job_id')
    ->values()
    ->all();
    // dd(@$row->full_name,$array);

    // $jobIds = array_values(array_unique(array_column($jobs, 'job_id')));
    $masterStatusIds = array_column($array, 'master_status_id');
    $bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($row->id);
    $applicationCounter = $bench_explore_listing_counter->applicationCounter;

    // dd($jobs,@$row->full_name);

    // Count how many times each value appears
    $counts = array_count_values($masterStatusIds);
    $countForZero = $applicationCounter->allCandidate ?? 0;
    $row->Sourcing = $applicationCounter->Sourcing ?? 0;
    $row->Screening = $applicationCounter->Screening ?? 0;
    $row->Evaluation = $applicationCounter->Evaluation ?? 0;
    $row->Negotiation = $applicationCounter->Negotiation ?? 0;
    $row->Offered = $applicationCounter->Offered ?? 0;
    $row->Joined = $applicationCounter->Joined ?? 0;
    $row->Rejected = $applicationCounter->Rejected ?? 0;
    $row->InReview = $applicationCounter->Interview ?? 0;
   

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
                    if (empty($limit)) {
                        if (count($skillArr) > 0) {
                            foreach ($skillArr as $sval) {
                                $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                            }
                        }
                    } else {
                        $skilltagclass = 'd-none';
                    }
                    $viewCount = DB::table('bench_count_view_download')
                        ->where('type', '=', 1)
                        ->where('status', '=', 0)
                        ->where('candidate_id', '=', $row->id)
                        ->count();
                    $downloadCount = DB::table('bench_count_view_download')
                        ->where('type', '=', 2)
                        ->where('status', '=', 0)
                        ->where('candidate_id', '=', $row->id)
                        ->count();
                        $logo_path = url('public/ad-label-icon.svg');
                        $skillArr = DB::table('benchboostkeywordcandidates')->where('benchboostkeywordcandidates.candidate_id', @$row->id)->pluck('skill_name')->toArray();
                        $award = '';
                        if(@$row->ads_switch=='Yes'){
                            $award = '<span class="badge badge-light-danger my-1">Ads</span>';
                        }

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
					<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
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
				<div class="" style="position: absolute; bottom: 0; display: flex; right: 21px;">
				 <div style="display: flex; margin-top: 10px;"> 
				    <span style="margin-top: 2px;">
					 <i class="fa fa-eye text-muted fw-bold d-block fs-6"></i>
					 </span>
					<span class="text-muted fw-bold d-block fs-7" style="margin-left: 6px;"> ' .
                        @$viewCount .
                        '</span>
				 </div>

				 <div style="display: flex; margin-top: 10px;margin-left: 34px;"> 
				    <span style="margin-top: 0px;">
					 <i class="fa fa-download text-muted fw-bold d-block fs-6"></i>
					 </span>
					<span class="text-muted fw-bold d-block fs-7" style="margin-left: 6px;"> ' .
                        @$downloadCount .
                        '</span>
				 </div>

				
				</div>
				<div class="' .
                        $skilltagclass .
                        '" style="position: absolute; bottom: 0;">
                                    '.$award.' <span class="badge badge-light-success my-1">Skills:</span>
                                 
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
                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
                    $company_id 			=	auth()->user()->company_id;
                    $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$currencyBenchshowMode->currency_code,$currencyBenchshowMode->show_mode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$currencyBenchshowMode->currency_code,$currencyBenchshowMode->show_mode);
                    
                    $pay_rate_currency = $currencySymbole[$payFormatViewSalary->currency];
                    $pay_rate_mode = $salaryMode[$payFormatViewSalary->mode];
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode];

                    echo '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        $bill_rate_currency .
                        $billFormatViewSalary->amount.
                        '/' .
                        mb_substr($bill_rate_mode,0,1) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7">' .
                        @$pay_rate_currency .
                        $payFormatViewSalary->amount .
                        '/' .
                        mb_substr($pay_rate_mode,0,1) .
                        '</span>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $outsourcing_type = 'Full-time';
                    if ($row->outsourcing_type == 2) {
                        $outsourcing_type = 'Contract';
                    }
                    

                    echo ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                        @$row->qualification_name .
                        '" style="text-transform: uppercase;!important">' .
                        @$row->qualification_name .
                        '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' .
                        $outsourcing_type .
                        '">' .
                        mb_substr($outsourcing_type, 0, 12) .
                        '</span> 
						</div>';
                @endphp
            </td>
            <td>
                @php
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    $titleNotic = $npArr[$row->serving_notice_period_mode];
                    if ($row->serving_notice_period_mode == 0) {
                        $npDate = $row->np_id == 1 ? $row->np_id . ' Day' : $row->np_id . ' Days';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $originalDate = $row->np_id;
                        $npDate = date('d-M-y', strtotime($originalDate));
                        $today = date('Y-m-d'); 
                        if(date('Y-m-d', strtotime($npDate)) < $today){
                            $titleNotic = "Immediate";
                            $npDate = "";
                        }
                    } elseif ($row->serving_notice_period_mode == 2) {
                        $originalDate = $row->np_id;
                        $npDate = date('d-M-y', strtotime($originalDate));
                        $today = date('Y-m-d'); 
                        if(date('Y-m-d', strtotime($row->np_id)) < $today){
                            $npDate = "";
                        }
                    } else {
                        $npDate = '';
                    }
                    echo '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$titleNotic .'</a> 
					  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id .'">' .$npDate .'</span>
					</div>';
                @endphp
            </td>
            <td>
                @php

                    $country = $state = $row_state_name = $row_country_name = $city = $row_city_name = '';
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

                    if (empty($row->state)) {
                        $row_state_name = '----';
                    } else {
                        $row_state = DB::table('state_list')
                            ->select('*')
                            ->where('id', @$row->state)
                            ->first();
                        $row_state_name = @$row_state->state;
                    }

                    $row_city = DB::table('city_list')
                        ->select('*')
                        ->where('city_id', @$row->city_id)
                        ->first();
                    $row_city_name = @$row_city->city;
                    $row_city_name = @$row->city_name;
                    $row_state_name = @$row->state_name;
                    $row_country_name = @$row->country_name;

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
                    $subS = '';
                    if ($row->master_status_name == 'Bench') {
                        $subS = 'Live';
                        if ($row->superadminstatus == 0) {
                            $subS = 'Pending';
                        }
                    }
                    $result = GeneralMethod::bench_status_subline($row->status);
					$subS = ($result->name) ?? '';
                    echo '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="fw-bold fs-6  stage_sub_stage ' .
                        $row->css_style .
                        '" data-toggle="modal" data-target="#stage_sub_stage" >
					  	' .
                        $row->master_status_name .
                        '</a>
                      <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="'.$subS.'">' .
                        $subS .
                        '</span>
                    </div>';
                @endphp
            </td>
            <td>
                @php
                    $created_at = '';
                     $hmDataName = DB::table('bench_assign')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('bench_assign.bench_applications_id', '=', $row->id)
                        ->where('bench_assign.company_id', '=', $company_id)
                        ->where('users.company_id', '=', $company_id)
                        ->first();

                    $created_at = GeneralMethod::ymdTodmyNotTime($row->created_at); //$newDate = date("d M , Y", strtotime($row->created_at));
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        $created_at .
                        '</a>
                        <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$hmDataName->name.'</span></div>';
                @endphp
            </td>
            <td>
                @php
                    $hmCount = DB::table('bench_assign')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('bench_assign.bench_applications_id', '=', $row->id)
                        ->where('bench_assign.company_id', '=', $company_id)
                        ->where('users.company_id', '=', $company_id)
                        ->count();

                    $hmData = DB::table('bench_assign')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('bench_assign.bench_applications_id', '=', $row->id)
                        ->where('bench_assign.company_id', '=', $company_id)
                        ->where('users.company_id', '=', $company_id)
                        ->orderBy('users.name', 'ASC')
                        ->skip(0)
                        ->take(1)
                        ->get();

                    $html = $temp = '';
                    $html .= '<div data-row-id="' .$row->id .'" class="d-flex flex-column hmb-listing">
                            <div class="symbol-group symbol-hover">';
                    foreach ($hmData as $pval) {
                        $logo_path = str_replace('index.php', '', url('public/avatar.png'));
                        if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $logo_path = @$pval->image;
                        }
                        $html .=
                            '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' .
                            @$pval->name .
                            '"><img alt="Pic" src="' .
                            @$logo_path .
                            '"></div>';
                    }
                    $html .=
                        '<div data-row-id="' .
                        $row->id .
                        '"   class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
                                        <span class="symbol-label font-weight-bold">' .
                        $hmCount .
                        '+</span>
                                      </div>';
                    $html .= '</div>
                        </div>';
                    echo $html;
                @endphp
            </td>
            <td>
                @php
                    $stage = 'all';
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '">' .$countForZero .'</a>
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-muted fw-bold d-block fs-7 text-hover-primary">' .
                        @$row->InReview .
                        '</a>
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 1;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Sourcing .
                        '</a> 
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 2;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Screening .
                        '</a>
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 3;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        $row->Evaluation .
                        '</a>
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 4;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Negotiation .
                        '</a>
                      </div>';

                @endphp
            </td>
            <td>
                @php
                    $stage = 5;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Offered .
                        '</a>
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 6;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Joined .
                        '</a>
                      </div>';
                @endphp
            </td>
            <td>
                @php
                    $stage = 7;
                    echo '<div class="d-flex flex-column">
                        <a href="' .
                        url('/service_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) .
                        '" class="text-dark fw-bolder fs-6 text-hover-primary">' .
                        @$row->Rejected .
                        '</a>
                      </div>';
                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="18" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
