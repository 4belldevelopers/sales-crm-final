@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();

    // Valid days from features table (id=4) — cached for 24h to eliminate per-request MySQL hit.
    $benchExpireDays = cache()->remember('feature:expire_days:4', 86400, function () {
        $benchExpireFeature = DB::table('features')->where('id', 4)->first();
        return (int) (@$benchExpireFeature->days ?: 30);
    });

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $row->id = $row->candidate_id;
    $jobMode = @$modeFilter;
    $jobCurrency = @$currencyFilter;
    $symbols = @$symbolsFilter;
    @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" type="checkbox" value="' .
                        @$row->id .
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

                    $duplicate = $row->duplicate ?? 0;
                    $flag = 0;
                    if ($duplicate == 1) {
                        $flag = $duplicate;
                    }
                    $covid = '';
                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = explode(',', $row->skills);

                    $targetJobId = (!empty($jobid) && (int)$jobid > 0) ? (int)$jobid : (int)(@$row->job_id ?: (@$row->source_jobid ?: session()->get('job_id')));
                    if ($targetJobId > 0) {
                        $percentageString = GeneralMethod::skill_percentage($targetJobId, $skillArr);
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
                    $matchBadge = '<span class="badge my-1" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;">Match '.$percentage.'%</span>';

                    // Matched skill highlighting, three tiers — mirrors
                    // internal_bench_ajax.blade.php: a match against a STARRED
                    // (mandatory) requirement is a different signal than a match
                    // against a skill that was just typed but not starred.
                    // $row->matched_skills and $row->matched_mandatory_skills are
                    // both computed server-side (GeneralMethod::matchedSkillsList())
                    // in candidates_request_data_ajax()'s must+should hybrid query.
                    $matchedSkillsArr = $row->matched_skills ?? [];
                    $mandatoryMatched = $row->matched_mandatory_skills ?? [];
                    $optionalMatched = array_values(array_udiff($matchedSkillsArr, $mandatoryMatched, 'strcasecmp'));
                    $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));
                    foreach ($mandatoryMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dbeafe;color:#1d4ed8;font-weight:600;border:1px solid #bfdbfe;" title="Matches a mandatory (starred) skill"><span style="color:#eab308;">★</span> ' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($optionalMatched as $sval) {
                        $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;" title="Matches a required skill">' . ucfirst($sval) . '</span>  ';
                    }
                    foreach ($unmatchedSkillsArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                    }

                    if ($flag == 1) {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->id .
                            '" class="pbench_show_application_detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
					<span class="fs-3">' .
                            strtoupper(@$strVal) .
                            '</span>
					<span class="pulse-ring"></span>
					</a>
					</div>                  
					<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->id .
                            '"  class="text-danger fw-bolder text-hover-primary fs-6 pbench_show_application_detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 7)) .
                            $covid .
                            '</a>
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->id .
                            '"  class="text-danger fw-bold d-block fs-7 text-hover-primary pbench_show_application_detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 7)) .
                            '</a>
					</div>
					<div class="' .
                            $skilltagclass .
                            '" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;; white-space: nowrap; width: 94%; overflow: hidden;">
                                    '.$matchBadge.'
                                 
                                  ' .
                            $skilltag .
                            '
                                         
                                   
                                    </div> 
					';
                    } else {
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
					<a href="javascript:void(0);"   data-row-id="' .
                            @$row->id .
                            '" class="pbench_show_application_detail">
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
                            '"  class="text-dark fw-bolder text-hover-primary fs-6 pbench_show_application_detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->full_name) .
                            '">' .
                            ucwords(mb_substr(@$row->full_name, 0, 10)) .
                            $covid .
                            '</a>
					<a href="javascript:void(0);"  data-row-id="' .
                            @$row->id .
                            '"  class="text-muted fw-bold d-block fs-7 text-hover-primary pbench_show_application_detail" data-toggle="tooltip" title="' .
                            ucwords(@$row->current_designation) .
                            '">' .
                            ucwords(mb_substr(@$row->current_designation, 0, 10)) .
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

                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly,$symbols,$jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,$symbols,$jobMode);

                    $ctc = '';
                    $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $ectc = '';
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

                    // Drop the decimal portion for display only (thousandsCurrencyFormat
                    // always returns 2 decimals), and use the full mode word ("Annual"/
                    // "Monthly"/"Hourly") rendered smaller than the amount.
                    $ctc = explode('.', $ctc)[0];
                    $ectc = explode('.', $ectc)[0];

                    $current_ctc_mode = @$salaryMode[@$jobMode];
                    $current_ectc_mode = @$salaryMode[@$jobMode];

                    $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 d-none">' .
                        @$current_ctc_cur .
                        @$ctc .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $current_ctc_mode .
                        '</span></a>
                    <span class="text-dark fw-bolder fs-6">' .
                        $current_ectc_cur .
                        @$ectc .
                        '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' .
                        $current_ectc_mode .
                        '</span></span>
					<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                        $outsourcing_type .
                        '</span>
                  </div>';
                @endphp
            </td>
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
                        $np = $row->np_id;
                        if ($np != 'Immediate') {
                            $np = date('d-M-Y', strtotime($row->np_id));
                            $today = date('Y-m-d'); 
                            if(date('Y-m-d', strtotime($row->np_id)) < $today){
                              $np = "Immediate";
                            }
                        }
                    } else {
                        $np = $row->np_id;
                    }

                    // Notice period color: Immediate = green, numeric days notice =
                    // orange, a real serving last-working-day date = purple (prefixed
                    // "SNP LWD"). !important is required because Bootstrap/Metronic's
                    // .text-dark utility class sets color with !important, which
                    // otherwise beats a plain inline style.
                    $npColorStyle = 'color:#181c32 !important;';
                    if ($np === 'Immediate') {
                        $npColorStyle = 'color:#16a34a !important;';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $npColorStyle = 'color:#d97706 !important;';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $np = 'SNP LWD ' . $np;
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

                    $state = $row_state_name = '';

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

                    $city = $row_city_name = '';

                    if (!empty($row->city_name)) {
                        $row_city_name = $row->city_name;
                    } elseif (@$row->country == 101 && !empty($row->city_id)) {
                        // Memoized fallback if older document didn't include city_name
                        $row_city_name = cache()->remember('city_name:' . (int) $row->city_id, 86400, function () use ($row) {
                            $row_city = DB::table('city_list')
                                ->select('city')
                                ->where('city_id', (int) $row->city_id)
                                ->first();
                            return $row_city ? $row_city->city : '';
                        });
                    } else {
                        $row_city_name = @$row->city ?: '';
                    }

                    // Row 2 is now Education (merged in from the removed Education
                    // column) instead of state — same qualification_name field the
                    // removed column used.
                    echo '<div class="d-flex flex-column">
                       <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row_city_name) .
                        ', ' .
                        strtoupper($row_country_name) .
                        '">' .
                        ucwords(mb_substr($row_city_name, 0, 20)) .
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
                    $posted_by_image = '';
                    $posted_by_image = url('public/avatar.png');
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                    }
                    $dateupdate = GeneralMethod::ymdTodmyNotTime($row->updated_at);
                    $comission_type = $row->comission_type == 1 ? 'Free' : 'Paid';

                    echo '<div class="d-flex flex-column">
				  <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="" data-bs-original-title="">' .
                        $dateupdate .
                        '</span>
				 <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                        $comission_type .
                        '</span>
				  </div>
				  ';

                @endphp
            </td>
            <td>
                @php
                    // Expiry date = created_at + configured days (features id=4)
                    $expireDate = '';
                    $expireColor = '#d97706';
                    if (!empty($row->created_at)) {
                        $expireTs = strtotime("+{$benchExpireDays} days", strtotime($row->created_at));
                        $expireDate = date('d M y', $expireTs);
                        $expireColor = ($expireTs < time() || $row->ac_status == 2) ? '#dc2626' : '#d97706';
                    }

                    $name = '';

                    $isAdded = ($row->ac_status == 1 || (isset($assignedCandidateIds) && in_array((int)@$row->candidate_id, $assignedCandidateIds, true)));

                    if ($row->ac_status == 2) {
                        echo '<div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger">Expired</a>
                                <span class="fw-bold d-block fs-7 mt-1 text-center" style="color:' . $expireColor . ';" title="Request expires ' . $benchExpireDays . ' days after being sent">Exp: ' . $expireDate . '</span>
                        </div>';
                    } elseif ($isAdded) {
                        echo '<div class="d-flex flex-column">
                                <button class="btn btn-sm" disabled style="background-color: #059669; color: #fff; opacity: 1; cursor: default; border: none; font-weight: 600;">Added</button>
                                <span class="fw-bold d-block fs-7 mt-1 text-center" style="color:' . $expireColor . ';" title="Request expires ' . $benchExpireDays . ' days after being sent">Exp: ' . $expireDate . '</span>
                        </div>';
                    } elseif ($row->ac_status == 0) {
                        echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" data-row-id="' . $targetJobId .'" data-recomnded-id="' . @$row->rec_candidate_assign_id .'" data-condidate-id="' .@$row->id .'" class="btn btn-sm btn-primary add_to_list">Add to job</a>
                            <span class="fw-bold d-block fs-7 mt-1 text-center" style="color:' . $expireColor . ';" title="Request expires ' . $benchExpireDays . ' days after being sent">Exp: ' . $expireDate . '</span>
                        </div>';
                    } else {
                        echo '<div class="d-flex flex-column">
				      <a href="javascript:void(0);" class="btn btn-sm btn-danger">Removed</a>
                      <span class="fw-bold d-block fs-7 mt-1 text-center" style="color:' . $expireColor . ';" title="Request expires ' . $benchExpireDays . ' days after being sent">Exp: ' . $expireDate . '</span>
			      </div>';
                    }
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
