@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

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
                        $row->candidate_id .
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
                        $profile_sidebar_css = 'client-show-application-detail';
                    }

                    $skilltag = '';
                    $skilltagclass = '';
                    // $row->matched_skills returns the candidate's own skill
                    // entries that phrase-prefix matched SOME required skill —
                    // e.g. a candidate with [Java, AWS, Java Developer] and
                    // required [Java, AWS, Node] returns 3 entries. Using
                    // count($matched_skills) as $matches inflates Match%:
                    // two required skills (Java, AWS) get reported as three
                    // matched entries, so Match 100% renders even though Node
                    // is unsatisfied, and the badge list is missing a "Node"
                    // entry to display. Distinct required skills satisfied is
                    // the correct denominator.
                    $skillArr = $row->matched_skills;
                    $candidateAllSkills = $row->all_skills ?? $skillArr;
                    $satisfiedRequired = [];
                    $representativeByRequired = [];
                    foreach ($requiredSkills as $req) {
                        foreach ($candidateAllSkills as $cand) {
                            if (GeneralMethod::skillMatchesPhrasePrefix($req, $cand)) {
                                $satisfiedRequired[$req] = true;
                                if (!isset($representativeByRequired[$req])) {
                                    $representativeByRequired[$req] = $cand;
                                }
                            }
                        }
                    }
                    $mandatorySatisfied = [];
                    $optionalSatisfied = [];
                    // Split satisfied required skills into mandatory vs
                    // optional. The controller's $row->matched_mandatory_skills
                    // is matchedSkillsList($jobSkills, $mandatorySkills), so
                    // lower-casing it gives the candidate entries that
                    // phrase-matched the starred/mandatory required skills.
                    // A satisfied required skill is "mandatory" if its
                    // representative candidate entry also appears there.
                    $mandatoryCandidateKeys = array_map('strtolower', $row->matched_mandatory_skills ?? []);
                    foreach (array_keys($satisfiedRequired) as $req) {
                        $rep = $representativeByRequired[$req] ?? null;
                        $isMandatory = $rep !== null && in_array(strtolower($rep), $mandatoryCandidateKeys, true);
                        if ($isMandatory) {
                            $mandatorySatisfied[] = $req;
                        } else {
                            $optionalSatisfied[] = $req;
                        }
                    }

                    $percentage=0;
                    $matches = $row->matched_skill_count;
                    $totalElements = count($requiredSkills);
                    $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                    $matchPercentage = min($matchPercentage, 100);
                    $percentage = round($matchPercentage, 1);

                    // Match % badge — color reflects the numeric match score, same
                    // thresholds/colors used on the Pool tab / CV Tracker: >90 green,
                    // 50-90 orange, 0<50 red, exactly 0 neutral grey.
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
                    // title attribute (not data-toggle="tooltip") since this
                    // partial is injected via AJAX on every filter/sort/page
                    // change and a Bootstrap-tooltip re-init isn't guaranteed
                    // to run against freshly-injected markup; a native title
                    // works with zero JS dependency regardless.
                    // A plain array_diff() compared required skill KEYWORDS
                    // ("java") against the candidate's own matched skill TEXT
                    // ("java developer") as exact strings — never equal, so a
                    // required skill the candidate actually has (via a
                    // differently-worded skill entry) still showed up in the
                    // tooltip as "Missing". Same phrase-prefix check
                    // matched_skills itself was computed with.
                    // missingSkills = required skills NOT in $satisfiedRequired.
                    // Previously diffed against $skillArr (candidate entries)
                    // which produced false negatives when two required skills
                    // were both phrase-matched by the same candidate entry.
                    $missingSkills = array_values(array_filter($requiredSkills, function ($required) use ($satisfiedRequired) {
                        return empty($satisfiedRequired[$required]);
                    }));
                    $matchTooltipParts = [];
                    if (!empty($satisfiedRequired)) {
                        $matchedLabels = array_map(function ($req) use ($representativeByRequired) {
                            return ucfirst($representativeByRequired[$req]);
                        }, array_keys($satisfiedRequired));
                        $matchTooltipParts[] = 'Matched: ' . implode(', ', $matchedLabels);
                    }
                    if (!empty($missingSkills)) {
                        $matchTooltipParts[] = 'Missing: ' . implode(', ', array_map('ucfirst', $missingSkills));
                    }
                    $matchTooltipText = !empty($matchTooltipParts) ? implode(' — ', $matchTooltipParts) : 'No skills specified in filter';

                    $matchBadge = '<span class="badge my-1" title="'.htmlspecialchars($matchTooltipText, ENT_QUOTES).'" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;cursor:help;">Match '.$percentage.'%</span>';

                    // Show every skill the candidate has, highlighting the
                    // ones that match the job's required skills instead of
                    // only showing the matched subset and hiding the rest —
                    // $row->all_skills is the candidate's full skill list,
                    // already computed server-side alongside matched_skills
                    // (same Elasticsearch document, no extra query).
                    // Three tiers, not two: a match against a STARRED
                    // (mandatory) requirement is a different signal than a
                    // match against a skill that was just typed but not
                    // starred — the two used to render identically. Mandatory
                    // matches get the star + the same blue used for the
                    // mandatory chip in the filter drawer, so the color
                    // language is consistent across the page; other matches
                    // stay plain green. Unmatched skills are capped so a
                    // candidate with a long skill list doesn't make the row
                    // unreadable.
                    // Skill-tag list now mirrors the satisfied-required set
                    // (one badge per matched required skill) instead of one
                    // badge per matched candidate entry. This keeps the badge
                    // count in lockstep with the Match% number, so the
                    // "Matched: Java, AWS, Node" tooltip lines up with the
                    // three green badges on screen.
                    $mandatoryMatched = $mandatorySatisfied;
                    $optionalMatched = $optionalSatisfied;
                    $allSkillsList = $candidateAllSkills;
                    $matchedRepresentativeStrings = array_map(function ($req) use ($representativeByRequired) {
                        return strtolower($representativeByRequired[$req]);
                    }, array_keys($satisfiedRequired));
                    $unmatchedSkills = array_values(array_udiff($allSkillsList, $matchedRepresentativeStrings, 'strcasecmp'));
                    $maxUnmatchedShown = 6;
                    $unmatchedShown = array_slice($unmatchedSkills, 0, $maxUnmatchedShown);
                    $unmatchedRemaining = max(count($unmatchedSkills) - count($unmatchedShown), 0);

                    foreach ($mandatoryMatched as $req) {
                        $display = ucfirst($representativeByRequired[$req] ?? $req);
                        $skilltag .= '<span class="badge my-1" style="background:#dbeafe;color:#1d4ed8;font-weight:600;border:1px solid #bfdbfe;" title="Matches a mandatory (starred) skill"><span style="color:#eab308;">★</span> ' . $display . '</span>  ';
                    }
                    foreach ($optionalMatched as $req) {
                        $display = ucfirst($representativeByRequired[$req] ?? $req);
                        $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;" title="Matches a required skill">' . $display . '</span>  ';
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
                            ucwords(mb_substr(@$row->full_name, 0, 27)) .
                            $covid .
                            '</a>
									<a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
                            '"  class="text-danger fw-bold d-block fs-7 text-hover-primary ' .
                            $profile_sidebar_css .
                            ' " data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 27)) .
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
                            ucwords(mb_substr(@$row->full_name, 0, 27)) .
                            $covid .
                            '</a>
									<a href="javascript:void(0);"  data-row-id="' .
                            @$row->wiseId .
                            '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                            $profile_sidebar_css .
                            ' " data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 27)) .
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
            <!-- Salarystart  -->
            <td>
                @php

                    $current_ctc_cur = '';
                    $ctc = '';
                    $ectc = '';
                    $current_ctc_cur = $currencySymbole[@$jobCurrency];

                    $current_ectc_cur = '';
                    $current_ectc_cur = $currencySymbole[@$jobCurrency];
                    // dd($row->ectc_yearly);
                    $currencymain = GeneralMethod::currency(); // Currency code
                    $currency = $currencymain[@$jobCurrency];
                    // dd($currency);
                    $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
                    $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
                    // dd($convertedAmounEctc,$convertedAmounCtc,$convertedAmounEctc[$sal_mode]);
                    $ectc = $convertedAmounEctc[$sal_mode];
                    $ctc = $convertedAmounCtc[$sal_mode];

                    $ctc = GeneralMethod::thousandsCurrencyFormat($ctc);

                    $ectc = GeneralMethod::thousandsCurrencyFormat($ectc);

                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);
                    $ctc = '';
                    $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $ectc = '';
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);


                    // dd($ectc,$ctc);

                    // Drop the decimal portion for display only (thousandsCurrencyFormat
                    // always returns 2 decimals), and use the full mode word ("Annual"/
                    // "Monthly"/"Hourly") rendered smaller than the amount.
                    $ctc = explode('.', $ctc)[0];
                    $ectc = explode('.', $ectc)[0];

                    $current_ctc_mode = @$salaryMode[$jobMode];
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
                    } elseif ($row->serving_notice_period_mode == 1) {
                        if (!empty($row->np_id)) {
                            $np = GeneralMethod::ymdTodmyNotTime($row->np_id);
                             $today = date('Y-m-d'); 
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }
                        }
                    } else {
                        $np = $row->np_id; //GeneralMethod::ymdTodmyNotTime($row->created_at)
                    }

                    // Notice period color: Immediate = green, numeric days notice =
                    // orange, a real serving last-working-day date = purple (prefixed
                    // "SNP LWD" — Serving Notice Period / Last Working Day). !important
                    // is required because Bootstrap/Metronic's .text-dark utility class
                    // sets color with !important, which otherwise beats a plain inline
                    // style — so the class is dropped below and color is inline-only.
                    $npColorStyle = 'color:#181c32 !important;';
                    if ($np === 'Immediate') {
                        $npColorStyle = 'color:#16a34a !important;';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColorStyle = 'color:#d97706 !important;';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $np = 'SNP LWD ' . $np;
                        $npColorStyle = 'color:#7c3aed !important;';
                    }

                    // The 2nd row (current company) was silently cutting off
                    // at 10 characters with no ellipsis — a company name any
                    // longer than that just stopped mid-word with no visual
                    // sign it was truncated (e.g. a company literally named
                    // "(formerly ..." would render as just "(formerly " and
                    // read as broken/garbled rather than truncated text).
                    $currentCompanyFull = ucwords(@$row->current_company);
                    $currentCompanyShort = ucwords(mb_substr(@$row->current_company, 0, 10));
                    if (mb_strlen(@$row->current_company) > 10) {
                        $currentCompanyShort .= '…';
                    }

                    echo '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="fw-bolder fs-6" style="' .
                        $npColorStyle .
                        '">' .
                        @$np .
                        '</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . htmlspecialchars($currentCompanyFull, ENT_QUOTES, 'UTF-8') .
                        '">' . htmlspecialchars($currentCompanyShort, ENT_QUOTES, 'UTF-8') .
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

                    // Row 2 is now Education (merged in from the removed Education
                    // column) instead of state — same qualification_name field/
                    // truncation the removed column used.
                    //
                    // Nearby-location visual badge removed per design
                    // feedback (didn't fit) — the underlying metro-cluster
                    // expansion and exact-first priority sort
                    // (resolveLocationCluster() in the controller) are
                    // untouched, this only drops the "Nearby" label.

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
                        mb_strtoupper(@$row->qualification_name) .
                        '">' .
                        mb_strtoupper(mb_substr(@$row->qualification_name, 0, 14)) .
                        '</span>
								</div>';

                @endphp
            </td>
            <!-- Location end  -->
            <!-- Jobs start  -->
            <td>
                @php
                    $removed_status = $icon = '';
                    // if ($row->final_status == 2) {
                    // 	$removed_status = '<div class=" fw-bolder fs-6 closed-bg px-2 py-1">REMOVED</div>';
                    // }

                    //$icon				=	'&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';
                    if ($row->thirdparty_company_id == $company_id) {
                        //$icon			=	'&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    }

                    $job_title = '';
                    $job_title = $row->title;
                    $job_title = ucwords($job_title);

                    $client_name = '';
                   
                    $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                    ->join('jobs', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->join('companies', 'companies.id', '=', 'jobs.company_id')
                    ->select('companies.company_name','clients.name as client_name','jobs.title','jobs.status as jobStatus','jobs.company_id as companyIdCheck')
                    ->where('add_job_wise_condidates.id', $row->id)
                    ->first();
                    $client_name = $add_job_wise_condidates->client_name;
                    // dd($add_job_wise_condidates,$row->id);
                    $client_name = ucwords($client_name);
                    if ($add_job_wise_condidates->companyIdCheck != $company_id) {
                        $client_name = $add_job_wise_condidates->company_name;
                    }

                    echo '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        $row->title .
                        '">' .
                        ucwords(mb_substr($job_title, 0, 10)) .
                        '</a>
									<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        $row->client_name .
                        '">' .ucwords(mb_substr($client_name, 0, 10)) .
                        $icon .
                        $removed_status .
                        '</span>
								</div>';
                @endphp
            </td>
            <!-- Jobs end  -->
            <!-- Status start  -->
            <td>
                @php
                 $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                    ->join('jobs', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->join('companies', 'companies.id', '=', 'jobs.company_id')
                    ->select('companies.company_name','clients.name as client_name','jobs.title','jobs.status as jobStatus','jobs.company_id as companyIdCheck')
                    ->where('add_job_wise_condidates.id', $row->id)
                    ->first();

                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                    $status = ucwords($add_job_wise_condidates->jobStatus);
                    $class = $job_status[$add_job_wise_condidates->jobStatus];
                    
                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }

                    $recommendation_text = __('messages.internal_tag');
                    if ($add_job_wise_condidates->companyIdCheck != $company_id) {
                        $recommendation_text = __('messages.clinet_tag');
                    }

                    echo '<div class="d-flex flex-column">
								   <a href="javascript:void(0);" class="fs-6 ' .
                        @$class .
                        ' text-hover-success">' .
                        $status .
                        '</a>
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
                    $rowpostedby = @$row->rec_candidate_assign_id ? $row->posted_by :  $rowpostedby;
                }else{
                    $rowpostedby = $row->posted_by;
                }
                  // dd($rowpostedby);
                 

                    $bgTextDecore = $strVal = '';
                    $str = trim(@$rowpostedby);
                    // dd($str,$str[0]);
                    $str = $strVal = strtolower($str[0]);
                    // dd($str);
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
                    $posted_by_image = ''; // url('public/avatar.png');
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = @$row->posted_by_image;
                    }
                    $post_company_name = @$row->post_company_name;
                    if (!$post_company_name) {
                        $post_company_name = @$row->company_name;
                    }

                    $icon =
                        '<div data-row-id="' .
                        $row->candidate_id .
                        '" class="symbol symbol-30 symbol-circle overflow-hidden me-2 border-radius-5 client-show-detail" title="' .
                        @$rowpostedby .
                        '" style="float:left">
								<a href="javascript:void(0);" data-row-id="' .
                        $row->candidate_id .
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
                        mb_substr(@$post_company_name, 0, 15) .
                        '</span>
							';

                @endphp
            </td>
            <!-- Source end  -->
            <!-- date start  -->
            {{-- <td>
                @php
                    $created_at = '';
                    $created_at = $newDate = GeneralMethod::ymdTodmyNotTime($row->created_at); // date("d M , Y", strtotime($row->created_at));

                    echo '<div class="d-flex flex-column">
											<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$created_at .
                        '</a> 
										</div>';
                @endphp
            </td> --}}
            <!-- date end  -->
            <!-- Action start  -->
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
                    if (!empty(@$row->childStatusId) && @$row->childStatusId > 0) {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')
                            ->select('*')
                            ->where('id', $row->childStatusId)
                            ->first();
                        if (!empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }
                    echo '<div class="d-flex flex-column">
                            <a href="javascript.void(0);" data-row-id="' .
                        @$row->candidate_id .
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
                        ucwords(mb_substr(@$name, 0, 10)) .
                        '</span>
                            </a>
                        </div>';
                @endphp
            </td>
            <!-- Stage end  -->
            <td>
                @php
                    $source_from_id = @$row->candidate_id;
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->candidate_id)
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
                        ->where('source_application_id', '=', @$row->candidate_id)
                        ->count();

                    if ($public_bench_conter > 0) {
                        $source_from_type = 2;
                    }

                    $internal_bench_conter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_type', '=', 1)
                        ->where('source_company_id', '=', $company_id)
                        ->where('source_application_id', '=', @$row->candidate_id)
                        ->count();

                    if ($internal_bench_conter > 0) {
                        $source_from_type = 1;
                    }

                    if ($recCounter > 0) {
                        $html =
                            '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a>' .
                            @$row->wiseId .
                            ' </div>';
                    } else {
                        $public_bench_conter = DB::table('rec_candidate_assign')
                            ->select('id')
                            ->where('source_from_type', '=', 2)
                            ->where('source_company_id', '=', $company_id)
                            ->where('source_jobid', '=', $jobid)
                            ->where('parent_id', '=', @$row->candidate_id)
                            ->count();
                        $internal_bench_conter = DB::table('rec_candidate_assign')
                            ->select('id')
                            ->where('source_from_type', '=', 1)
                            ->where('source_company_id', '=', $company_id)
                            ->where('source_jobid', '=', $jobid)
                            ->where('parent_id', '=', @$row->candidate_id)
                            ->count();
                            $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->candidate_id)->count();

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
                                '" data-row-wiseId="' .
                                @$row->id.
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
    {{-- Context-aware empty state: distinguishes "no candidates for this job
         at all" from "your filters/search narrowed it to zero" — with search
         now AND-ed onto every active filter, the latter is common and a bare
         "No data available" reads like a bug rather than an expected outcome
         of a tight filter combination. --}}
    <tr style="height: 160px; position: relative; background: #fffcfc7a;">
        <td colspan="12" style="text-align: center; padding: 32px 16px;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:10px;font-family:'Poppins',sans-serif;">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#c4c9d4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                @if (!empty($search ?? null))
                    <div style="font-size:14px;font-weight:600;color:#3f4254;">No candidates match &quot;{{ $search }}&quot;</div>
                    <div style="font-size:12.5px;color:#99a1b7;max-width:360px;">
                        @if (!empty($hasActiveFilters ?? false))
                            Try a different search term, or clear one of your active filters to widen the results.
                        @else
                            Try a different search term or check the spelling.
                        @endif
                    </div>
                @elseif (!empty($hasActiveFilters ?? false))
                    <div style="font-size:14px;font-weight:600;color:#3f4254;">No candidates match your current filters</div>
                    <div style="font-size:12.5px;color:#99a1b7;max-width:360px;">Try widening or clearing a filter — Experience, Salary and Location are common ones to loosen first.</div>
                @else
                    <div style="font-size:14px;font-weight:600;color:#3f4254;">No candidates found for this job yet</div>
                    <div style="font-size:12.5px;color:#99a1b7;max-width:360px;">New matches will appear here as candidates are added.</div>
                @endif
            </div>
        </td>
    </tr>
@endif
