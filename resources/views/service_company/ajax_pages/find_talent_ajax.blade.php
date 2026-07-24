@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency        = GeneralMethod::currency();
    $symbolsValue    = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue    = !empty($symbolsValue) ? $symbolsValue : [
        0 => "INR", 1 => "EUR", 2 => "JPY",
        3 => "GBP", 4 => "USD", 5 => "AED",
    ];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;

        /* ── Avatar colour ── */
        $bgTextDecore = $strVal = '';
        $str = trim(@$row->full_name);
        $str = $strVal = strtolower($str[0]);
        $str = ord($str);
        if      ($str >= 97  && $str <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
        elseif  ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
        elseif  ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
        elseif  ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
        elseif  ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
        else                                 { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }
        $profile_sidebar_css = 'pbench_show_application_detail';

        /* ── Skills (from bench_candidate_skill_experience — star-rated) ── */
        $skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $row->id)
            ->get();
        $skillArr = explode(',', $row->skills);
        $skilltag = '';
        $viewMore = 0;
        if ($skillExperiences->isNotEmpty()) {
            foreach ($skillExperiences as $key => $exp) {
                if ($key < 5) {
                    $skilltag .= '<span class="badge badge-light my-1" style="font-weight:400;color:#3f4254;">'
                        . ucfirst($exp->skill_key) . '</span> ';
                } else {
                    $viewMore++;
                }
            }
        } else {
            foreach (array_slice($skillArr, 0, 5) as $sval) {
                $sval = trim($sval);
                if ($sval) $skilltag .= '<span class="badge badge-light my-1" style="font-weight:400;color:#3f4254;">' . ucfirst($sval) . '</span> ';
            }
        }
        if ($viewMore > 0) {
            $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;color:#7e8299;">+' . $viewMore . ' more</span>';
        }

        /* ── Experience ── */
        $experience = trim((string) @$row->total_exp);
        if (strpos($experience, '.') !== false) {
            $parts = explode('.', $experience);
            $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
        } else {
            $formattedExperience = $experience;
        }
        $rel_experience = trim((string) @$row->rel_exp);
        if (strpos($rel_experience, '.') !== false) {
            $parts = explode('.', $rel_experience);
            $rel_formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $rel_experience;
        } else {
            $rel_formattedExperience = $rel_experience;
        }
        $workMode = trim((string) @$row->wm); // Hybrid / Remote / On-site etc.

        /* ── Availability ── */
        $npArr  = ['Notice period', 'Available from', 'Immediate'];
        $npDate = $row->np_id;
        $modeNp = (int)($row->serving_notice_period_mode ?? 0);
        if ($modeNp == 0) {
            $npDate = $row->np_id . ($row->np_id == 1 ? ' Day' : ' Days');
        } elseif ($modeNp == 1) {
            $formatted = date('d-M-y', strtotime($row->np_id));
            if (date('Y-m-d', strtotime($row->np_id)) < date('Y-m-d')) {
                $npDate = 'Immediate';
                $modeNp = 2;
            } else {
                $npDate = 'SNP LWD ' . $formatted;
            }
        } else {
            $npDate = '';
        }
        if ($npDate === 'Immediate' || $modeNp == 2) { $npColorStyle = 'color:#16a34a !important;'; }
        elseif ($modeNp == 0)                        { $npColorStyle = 'color:#d97706 !important;'; }
        elseif ($modeNp == 1)                        { $npColorStyle = 'color:#7c3aed !important;'; }
        else                                         { $npColorStyle = 'color:#181c32 !important;'; }
        $npLabel       = $npArr[$modeNp] ?? 'Notice period';
        $outsourcingLabel = $row->outsourcing_type == 1 ? 'Full-time' : 'Contract';

        /* ── Bench status badge ── */
        $benchStatusMap = [
            1 => ['label' => 'Pool',   'bg' => '#dbeafe', 'color' => '#1d4ed8'],
            2 => ['label' => 'Active', 'bg' => '#dcfce7', 'color' => '#15803d'],
            3 => ['label' => 'Placed', 'bg' => '#f3e8ff', 'color' => '#7c3aed'],
            4 => ['label' => 'Hold',   'bg' => '#fef3c7', 'color' => '#d97706'],
        ];
        $benchStatus = $benchStatusMap[(int)($row->status ?? 2)] ?? $benchStatusMap[2];

        /* ── Location ── */
        $row_city_name    = @$row->city_name;
        $row_country_name = @$row->country_name;

        /* ── Expected Salary (bill rate) ── */
        $currency_code        = $symbolsValue[$min_salary_currency ?? 0] ?? 'INR';
        $billFormatViewSalary = GeneralMethod::formatViewSalary($row->bill_rate_amount, $currency_code, $sal_mode ?? 0);
        $bill_rate_currency   = $currencySymbole[$billFormatViewSalary->currency] ?? '';
        $bill_rate_mode       = $salaryMode[$sal_mode ?? 0] ?? '';
        $bill_rate_amount     = GeneralMethod::formatK($billFormatViewSalary->amount);
        $comission_label      = $row->comission_type == 1 ? 'Free' : 'Paid';
        $comission_color      = $row->comission_type == 1 ? '#16a34a' : '#16a34a'; // both green ✓
    @endphp

        <tr style="height: 102px; position: relative;">

            {{-- Talent --}}
            <td>
                @php
                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                        <a href="javascript:void(0);" data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
                            <div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column" style="overflow:hidden;">
                        <div class="d-flex align-items-center gap-1">
                            <a href="javascript:void(0);" data-row-id="' . @$row->id . '"
                               class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . '"
                               data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">'
                               . ucwords(mb_substr(@$row->full_name, 0, 18)) . '
                            </a>
                            <span style="background:' . $benchStatus['bg'] . ';color:' . $benchStatus['color'] . ';font-size:9px;font-weight:700;padding:1px 6px;border-radius:4px;white-space:nowrap;">' . $benchStatus['label'] . '</span>
                        </div>
                        <a href="javascript:void(0);" data-row-id="' . @$row->id . '"
                           class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . '"
                           data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">'
                           . ucwords(mb_substr(@$row->current_designation, 0, 22)) . '
                        </a>
                    </div>
                    <div style="position:absolute;bottom:0;white-space:nowrap;width:94%;overflow:hidden;">
                        <span style="font-size:10px;font-weight:600;color:#7e8299;margin-right:4px;">Skills</span>'
                        . $skilltag .
                    '</div>';
                @endphp
            </td>

            {{-- Experience --}}
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $formattedExperience . ' Y</a>'
                        . ($workMode ? '<span class="fw-bold d-block" style="font-size:10px;color:#7c3aed;">' . $workMode . '</span>' : '<span class="text-muted fw-bold d-block fs-7">—</span>') .
                    '</div>';
                @endphp
            </td>

            {{-- Skills (star-rated from bench_candidate_skill_experience) --}}
            <td>
                @php
                    $skillHtml = '';
                    foreach ($skillExperiences as $key => $exp) {
                        if ($key >= 3) break;
                        $skillHtml .= '<div class="skill-item">
                            <span class="skill-name">' . ucfirst($exp->skill_key) . '</span>
                            <div class="stars">
                                <svg class="star_skill ' . (@$exp->rating >= 1 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill ' . (@$exp->rating >= 2 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill ' . (@$exp->rating >= 3 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill ' . (@$exp->rating >= 4 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill ' . (@$exp->rating >= 5 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                        </div>';
                    }
                    echo $skillHtml ?: '<span class="text-muted fs-7">—</span>';
                @endphp
            </td>

            {{-- Availability --}}
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="fw-bolder fs-6" style="' . $npColorStyle . '">' . $npLabel . '</a>
                        <span class="text-muted fw-bold d-block fs-7">' . $outsourcingLabel . '</span>
                    </div>';
                @endphp
            </td>

            {{-- Location --}}
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                           data-toggle="tooltip" title="' . ucwords($row_city_name) . ', ' . ucwords($row_country_name) . '">'
                           . ucwords(mb_substr($row_city_name, 0, 12)) . ', ' . ucwords(mb_substr($row_country_name, 0, 10)) . '
                        </a>
                        <span class="text-muted fw-bold d-block fs-7"
                              data-toggle="tooltip" title="' . @$row->qualification_name . '">'
                              . strtoupper(mb_substr(@$row->qualification_name, 0, 14)) . '
                        </span>
                    </div>';
                @endphp
            </td>

            {{-- Expected Salary --}}
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'
                            . @$bill_rate_currency . $bill_rate_amount
                            . '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' . $bill_rate_mode . '</span>
                        </a>
                        <span class="fw-bold d-block fs-7" style="color:' . $comission_color . ';">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                            ' . $comission_label . '
                        </span>
                        <span class="fw-bold d-block fs-7" style="color:#6d7991;">Service</span>
                    </div>';
                @endphp
            </td>

            {{-- Action --}}
            <td style="text-align:center;">
                @php
                    $html = '';
                    if ($row->status == 2) {
                        if ($row->countforadd >= 3) {
                            $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                        } else {
                            /* Build candidate skill string for match % feature */
                            $ftCandSkills = [];
                            if ($skillExperiences->isNotEmpty()) {
                                foreach ($skillExperiences as $se) { $ftCandSkills[] = strtolower(trim($se->skill_key)); }
                            } else {
                                foreach ($skillArr as $sv) { $sv = strtolower(trim($sv)); if ($sv) $ftCandSkills[] = $sv; }
                            }
                            $ftSkillStr = htmlspecialchars(implode(',', $ftCandSkills));

                            $html = '<div class="d-flex flex-column" style="margin-bottom:6px;">
                                <a href="javascript:void(0);" class="btn btn-sm btn-primary openSideBar"
                                   data-row-source_from_type="2"
                                   data-row-id="' . @$row->id . '"
                                   data-row-jobid="' . @$jobid . '"
                                   data-cname="' . htmlspecialchars(ucwords((string)@$row->full_name)) . '"
                                   data-cdesig="' . htmlspecialchars(ucwords((string)@$row->current_designation)) . '"
                                   data-cexp="' . htmlspecialchars($formattedExperience) . '"
                                   data-cctc="' . htmlspecialchars(trim(strip_tags($currencySymbole[$billFormatViewSalary->currency] ?? '') . ' ' . GeneralMethod::formatK($billFormatViewSalary->amount))) . '"
                                   data-cnp="' . htmlspecialchars($modeNp == 2 ? 'Immediate' : $npDate) . '"
                                   data-cloc="' . htmlspecialchars(trim((string)@$row_city_name)) . '"
                                   data-ctype="' . htmlspecialchars($outsourcingLabel) . '"
                                   data-skills="' . $ftSkillStr . '">Add to Job</a>
                            </div>';
                        }
                    } else {
                        $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                    }
                    echo $html;
                @endphp
                <a href="javascript:;" class="fw-bold fs-7 text-hover-primary {{ $profile_sidebar_css }}" data-row-id="{{ $row->id }}">View Profile</a>
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">
        <td colspan="7" style="text-align: center; color: black;">
            No data available in table
        </td>
    </tr>
@endif
