@php
    use App\Helper\GeneralMethod;
    use App\Services\ReferenceDataCache;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = ReferenceDataCache::feedbackStatus();
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

    $company_id = auth()->user()->company_id ?? auth()->user()->roles[0]->company_id;
    $app_duplicate = auth()->user()->company->app_duplicate ?? 0;
    $app_duplicate_days = auth()->user()->company->app_duplicate_days ?? 0;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    // dd($row);
    $row->id = $row->candidate_id;
    $row->add_job_wise_condidates_id = $row->wiseId;
    // BUG-04 fix: use pre-fetched map instead of per-row DB query (was N+1 #1)
    $jobData = isset($rowMaps['jobs'][$row->jobId]) ? (object) $rowMaps['jobs'][$row->jobId] : null;
    $jobMode = $jobData->max_sal_mode ?? @$row->current_ctc_mode;
    $jobCurrency = $jobData->max_salary_currency ?? @$row->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? \App\Services\CurrencyService::resolveCurrencyCode($jobCurrency);
    $rowJobId = $row->job_id ?: ($row->jobId ?? 0);
    $preSkillRows = $rowMaps['skills'][$rowJobId] ?? [];
    $jobRequiredSkills = collect($preSkillRows)->pluck('skill_name')->toArray();
    $skillArr = explode(',', (string) @$row->skills);
    $percentage = GeneralMethod::skill_percentage_from_array($jobRequiredSkills, $skillArr);
    $matchScore = (int) round((float) rtrim(@$percentage ?: '0', '%'));
    @endphp
        <tr style="height: 102px; position: relative;" data-candidate-id="{{ @$row->id }}" data-wise-id="{{ @$row->add_job_wise_condidates_id }}" data-job-id="{{ @$rowJobId }}">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" type="checkbox" data-Wiid="'. @$row->add_job_wise_condidates_id.
                            '" value="' . @$row->id .'" data-wiseId="' .$row->add_job_wise_condidates_id .'" data-candidate-id="' . @$row->id . '" data-match="' . $matchScore . '" data-skills="' . e(@$row->skills ?? '') . '" data-cname="' . e(@$row->full_name ?? '') . '" />
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
                    $duplicate = $row->duplicate ?? 0;

                    $covid = '';
                    // BUG-04 fix N+1 #4: look up pre-fetched duplicate map instead of per-row DB count
                    if ($duplicate == 1) {
                        // $dupKey = ($app_duplicate === 1 ? $row->job_id . ':' : '') . ($row->email ?? '') . '|' . ($row->phone ?? '');
                        // $application_cnt = $rowMaps['duplicates'][$dupKey] ?? 0;
                        $flag =$duplicate;// $application_cnt > 1 ? 1 : 0;
                    }

                    if (@$row->condidate_type == 'bench_condidate') {
                        $tredmark = '[Bench]';
                    }
                    $photo = '';
                    if (!empty(@$row->photo)) {
                        $photo = $row->photo;
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

                    // BUG-04 fix N+1 #2 & #3: use pre-fetched skill map instead of per-row DB queries
                    $rowJobId = $row->job_id ?: ($row->jobId ?? 0);
                    $preSkillRows = $rowMaps['skills'][$rowJobId] ?? [];
                    $jobRequiredSkills = collect($preSkillRows)->pluck('skill_name')->toArray();

                    $percentage = GeneralMethod::skill_percentage_from_array($jobRequiredSkills, $skillArr);

                    // Match % badge — standardized thresholds: >=85 green, 50-84 orange, <50 red
                    $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
                    $matchBg = $matchStyle['bg'];
                    $matchColor = $matchStyle['color'];
                    $matchBadge = '<span class="badge my-1 jw-row-match-badge" data-candidate-id="'.@$row->id.'" data-wise-id="'.@$row->add_job_wise_condidates_id.'" data-match-pct="'.$matchScore.'" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;">Match '.@$percentage.'</span>';
                    $matchedSkillsArr = GeneralMethod::matchedSkillsList($skillArr, $jobRequiredSkills);
                    $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));

                    foreach ($matchedSkillsArr as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;" title="Matches a required skill">' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($unmatchedSkillsArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                    }

                    if ($flag == 1) {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->add_job_wise_condidates_id .
                            '" data-wise-id="' .
                            @$row->id .
                            '" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
					' .
                            $nameImg .
                            '
					<span class="pulse-ring"></span>
					</a>
					</div>                  
					<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->add_job_wise_condidates_id .
                            '" data-wise-id="' .
                            @$row->id .
                            '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 30)) .
                            $covid .
                            '<sup style="color: #0865dd !important;
					font-weight: bold;">' .
                            $tredmark .
                            '</sup></a>
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->add_job_wise_condidates_id .
                            '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 30)) .
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
                            @$row->add_job_wise_condidates_id .
                            '" data-wise-id="' .
                            @$row->id .
                            '" class="show-application-detail">
					' .
                            $nameImg .
                            '
					</a>
					</div>                  
					<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->add_job_wise_condidates_id .
                            '" data-wise-id="' .
                            @$row->id .
                            '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 30)) .
                            $covid .
                            ' <sup style="color: #0865dd !important;
					font-weight: bold;">' .
                            $tredmark .
                            '</sup></a>
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->add_job_wise_condidates_id .
                            '" data-wise-id="' .
                            @$row->id .
                            '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 30)) .
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

                    // $experience = floatval(@$row->total_exp); // Ensure it's a valid float
                    // $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;

                    // $rel_experience = floatval(@$row->rel_exp); // Ensure it's a valid float
                    // $rel_formattedExperience = ($rel_experience == intval($rel_experience)) ? intval($rel_experience) : $rel_experience;

                        

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
            

                    $safeJobCurrency = $jobCurrency ?? 0;
                    $current_ctc_cur = $currencySymbole[$safeJobCurrency] ?? ($currencySymbole[\App\Services\CurrencyService::resolveCurrencyCode($safeJobCurrency)] ?? '');

                    $current_ectc_cur = $current_ctc_cur;
                    $cur = $safeJobCurrency;
                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_cur = $current_ctc_cur;
                        $cur = $safeJobCurrency;
                    }
                    $safeJobMode = $jobMode ?? 0;
                    $current_ectc_mode = isset($salaryMode[$safeJobMode]) ? mb_substr($salaryMode[$safeJobMode], 0, 1) : '';
                    $mod = $safeJobMode;
                    if ($row->condidate_type == 'bench_condidate') {
                        $current_ectc_mode = isset($salaryMode[$safeJobMode]) ? mb_substr($salaryMode[$safeJobMode], 0, 1) : '';
                        $mod = $safeJobMode;
                    }

                    $ctc = '';
                    $ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
                    $ectc = '';
                    $ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);
                    if ($row->condidate_type == 'bench_condidate') {
                        $ectc = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
                    }

                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[$cur] ?? \App\Services\CurrencyService::resolveCurrencyCode($cur);
                    if (!empty($row->ectc_yearly)) {
                        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                        $ctc = $convertedAmounCtc[$mod] ?? 0;
                        $ectc = $convertedAmounEctc[$mod] ?? 0;

                        $ctc = GeneralMethod::thousandsCurrencyFormat($ctc);
                        $ectc = GeneralMethod::thousandsCurrencyFormat($ectc);
                    }
                    
                    
                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$mod);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$mod);

                    // Drop the decimal portion for display only (thousandsCurrencyFormat
                    // always returns 2 decimals), and use the full mode word ("Annual"/
                    // "Monthly"/"Hourly") rendered smaller than the amount — same as CV Tracker.
                    $payAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
                    $billAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];

                    $current_ctc_mode = $salaryMode[$safeJobMode] ?? '';
                    $current_ectc_mode = $salaryMode[$safeJobMode] ?? '';

                    $dnone = '';
                    $dnonetype = 'd-none';
                    if ($row->condidate_type == 'bench_condidate') {
                        $dnonetype = '';
                        $dnone = 'd-none';
                    }
                    $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';

                    if (in_array('salary_view', (array) ($userPermissions ?? []))) {
                        echo '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                            $current_ectc_cur .
                            $billAmountFormatted .
                            '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                            $current_ectc_mode .
                            '</span></a>
						 <span class="text-muted fw-bold d-block fs-7 ' .
                            $dnone .
                            '">' .
                            @$current_ctc_cur .
                            $payAmountFormatted .
                            '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                            $current_ctc_mode .
                            '</span></span>
						 <span class="text-muted fw-bold text-muted d-block fs-7 ' .
                            $dnonetype .
                            '" data-toggle="tooltip">' .
                            $outsourcing_type .
                            '</span>
						 </div>';
                    } else {
                        echo '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">N/A</a>
						 <span class="text-muted fw-bold d-block fs-7 ' .
                            $dnone .
                            '">N/A</span>
						 <span class="text-muted fw-bold text-muted d-block fs-7 ' .
                            $dnonetype .
                            '" data-toggle="tooltip"></span>
						 </div>';
                    }
                @endphp
            </td>
            <td>
                @php

                    $np = $row->np_id;
                    if (empty($np)) {
                        $np = 'N/A';
                    }
                    if ($row->serving_notice_period_mode == 0) {
                        $np = $row->np_id . ' Days';
                        if ($row->np_id == 1) {
                            $np = $row->np_id . '  Day';
                        }
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $np = $row->np_id;
                        if ($np != 'Immediate') {
                            $np = date('d M y', strtotime($row->np_id));

                            $today = date('Y-m-d');
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }

                        }
                    }

                    // Notice period color: Immediate = green, numeric days notice =
                    // orange, a real serving last-working-day date = purple (prefixed
                    // "SNP LWD" — Serving Notice Period / Last Working Day). !important
                    // is required here because Bootstrap/Metronic's .text-dark utility
                    // class sets color with !important, which otherwise beats a plain
                    // inline style — so the class is dropped below and color is fully
                    // inline-controlled instead.
                    $npColorStyle = 'color:#181c32 !important;';
                    if ($np === 'Immediate') {
                        $npColorStyle = 'color:#16a34a !important;';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColorStyle = 'color:#d97706 !important;';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $np = 'SNP LWD ' . $np;
                        $npColorStyle = 'color:#7c3aed !important;';
                    }

                    $row->current_company = $row->current_company ? $row->current_company : @$row->apl_company_name;
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fw-bolder fs-6" style="' .
                        $npColorStyle .
                        '">' .
                        @$np .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                    htmlspecialchars(ucwords(@$row->current_company), ENT_QUOTES, 'UTF-8') .
                        '">' .
                        htmlspecialchars(ucwords(mb_substr(@$row->current_company, 0, 10)), ENT_QUOTES, 'UTF-8') .
                        '</span>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $schedule_date = $className = '';
                    $iKey = ($row->id ?? 0) . '_' . ($row->jobId ?? 0);
                    if (isset($rowMaps['interviews'])) {
                        $intRow = $rowMaps['interviews'][$iKey] ?? null;
                        $intRowCount = $intRow ? 1 : 0;
                    } else {
                        $intRow = DB::table('interview_schedules')
                            ->select('*')
                            ->where('job_application_id', $row->id)
                            ->where('interview_job_id', $row->jobId)
                            ->orderBy('interview_schedules.id', 'DESC')
                            ->first();
                        $intRowCount = $intRow ? 1 : 0;
                    }
                    $html = '';
                    $schedule_time = '';
                    if ($intRowCount > 0) {
                        $schedule_date = @$intRow->schedule_date;
                        $schedule_date = date('d M, Y', strtotime($schedule_date));
                        $timezone_abbreviation = ($intRow->timezone_abbreviation) ? ' ('.$intRow->timezone_abbreviation.')' : '';
                        $schedule_time = @$intRow->schedule_to_time. ' '.$timezone_abbreviation;
                    } else {
                        $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                    }

                    // Row 3: interview mode (Face To Face / Video Call / Telecon) until
                    // feedback has actually been recorded on the Interview Tracker page —
                    // once $intRow->feedback is a real status (not the unset/-1 default),
                    // that feedback replaces the mode here, using the same name/classname
                    // lookup (ReferenceDataCache::feedbackStatus()) already used elsewhere.
                    $feedbackGiven = false;
                    $row3Text = '';
                    $row3Class = '';
                    if ($intRowCount > 0) {
                        if ($intRow->feedback >= 0) {
                            $feedbackGiven = true;
                            $row3Text = @$feedback_status[@$intRow->feedback]['name'];
                            $row3Class = @$feedback_status[@$intRow->feedback]['classname'];
                        } else {
                            $interviewModeLabels = [1 => 'Telecon', 2 => 'Video Call', 3 => 'Face To Face', 4 => 'Other'];
                            $row3Text = $interviewModeLabels[(int) @$intRow->interview_mode] ?? '';
                        }
                    }

                    $row3Html = '';
                    if ($row3Text !== '') {
                        if ($feedbackGiven) {
                            $row3Html = '<span class="badge fw-bold ' . $row3Class . ' d-inline-flex align-items-center gap-1 mt-1" style="font-size:10.5px;width:fit-content;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                ' . $row3Text . '
                            </span>';
                        } else {
                            $row3Html = '<span class="d-flex align-items-center gap-1 fw-bold fs-7 mt-1" style="color:#3699ff;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8Z"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>
                                ' . $row3Text . '
                            </span>';
                        }
                    }

                    echo '<div class="d-flex flex-column">
                        		<span class="text-dark fw-bolder fs-6 d-flex align-items-center gap-1">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#99a1b7;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    ' .
                        @$schedule_date .
                        '</span>
								<span class="text-muted fw-bold d-block fs-7 d-flex align-items-center gap-1">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#99a1b7;flex-shrink:0;"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                    ' .
                        $schedule_time .
                        '</span>' .
                        $row3Html .
                        '
                    		</div>';
                @endphp
            </td>
            <td>
                @php

                    $row_city_name = @$row->city_name;
                    $row_country_name = @$row->country_name;

                    // Row 2 is now Education (merged in from the removed Education column)
                    // instead of state — same qualification_name field/truncation used there.
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row_city_name) .
                        ', ' .
                        strtoupper($row_country_name) .
                        '">' .
                        ucwords(mb_substr($row_city_name, 0, 10)) .
                        ', ' .
                        strtoupper(mb_substr($row_country_name, 0, 2)) .
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
                    $bgTextDecore = GeneralMethod::icoinText(@$row->posted_by);
                    $posted_by_image = '';
                    if (!empty($row->posted_by_image)) {
                        // 	$posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                        $posted_by_image = @$row->posted_by_image;
                    }

                    if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                        $spokKey = ($row->jobId ?? 0) . '_' . ($row->ClientComID ?? 0);
                        if (isset($rowMaps['spokperson_map'])) {
                            $spokName = $rowMaps['spokperson_map'][$spokKey] ?? null;
                        } else {
                            $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                            $spokName = @$spokperson->name;
                        }
                        $rowpostedby = (!empty($spokName)) ? ucwords($spokName) : ucwords($row->posted_by);
                        $rowpostedby = $row->rec_candidate_assign_id ? $row->posted_by :  $rowpostedby;
                    }else{
                        $rowpostedby = ucwords($row->posted_by);
                    }
                    
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
							
						  </div>
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
                    $name = '';
                    if ($row->job_master_status_id == 8) {
                        $name = 'Private';
                    }
                    if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                        if (isset($rowMaps['child_status_map'])) {
                            $name = $rowMaps['child_status_map'][(int) $row->childStatus] ?? $name;
                        } else {
                            $childStatus = DB::table('candidate_status')
                                ->select('*')
                                ->where('id', $row->childStatus)
                                ->first();
                            if (!empty($childStatus)) {
                                $name = @$childStatus->name;
                            }
                        }
                    }
                    echo '<div class="d-flex flex-column">
						<a href="javascript.void(0);" data-row-id="' .
                        @$row->add_job_wise_condidates_id .
                        '" class="d-flex flex-column stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage">
                        	<span class="badge fw-bolder fs-6 ' .
                        @$row->css_style .
                        ' px-2 py-1">' .
                        ucwords(mb_substr($row->name, 0, 20)) .
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

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
