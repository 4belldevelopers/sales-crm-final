@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $available_status = GeneralMethod::available_status();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
      $row = (object) $rowArr;
    @endphp
        <tr>
            {{-- Candidate name + avatar --}}
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0]);
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101)      { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    elseif ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    elseif ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    elseif ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    elseif ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                    else                                { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }

                    echo '<div class="d-flex align-items-center gap-2">
                        <div class="symbol symbol-circle symbol-38px overflow-hidden flex-shrink-0 custom-show-application-detail" data-row-id="' . $row->job_application_id . '">
                          <a href="javascript:void(0);" data-row-id="' . $row->job_application_id . '" class="custom-show-application-detail">
                            <div class="symbol-label ' . $bgTextDecore . ' border-radius-5 custom-show-application-detail">' . ucwords($strVal) . '</div>
                          </a>
                        </div>
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" data-row-id="' . $row->job_application_id . '" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">'
                          . ucwords(mb_substr(@$row->full_name, 0, 22)) . '</a>
                          <span class="text-muted fw-bold d-block fs-7">' . ucwords(mb_substr(@$row->current_designation, 0, 22)) . '</span>
                        </div>
                    </div>';
                @endphp
            </td>
            {{-- Job + client --}}
            <td>
                @php
                    $company_client_name = $row->company_name;
                    if (auth()->user()->company_id == $row->job_company_id) {
                        $company_client_name = $row->client_name;
                    }
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row->job_title) . '">'
                        . ucwords(mb_substr($row->job_title, 0, 20)) . '</a>
                        <span class="text-muted fw-bold d-block fs-7">' . ucwords(mb_substr($company_client_name, 0, 20)) . '</span>
                    </div>';
                @endphp
            </td>
            {{-- Mode pill --}}
            <td>
                @php
                    $modeLower = strtolower(trim(@$row->mode_name ?? ''));
                    if (str_contains($modeLower, 'video') || str_contains($modeLower, 'online'))                                    { $modeCls = 'cit-mode-online'; }
                    elseif (str_contains($modeLower, 'phone') || str_contains($modeLower, 'call'))                                  { $modeCls = 'cit-mode-phone'; }
                    elseif (str_contains($modeLower, 'person') || str_contains($modeLower, 'office') || str_contains($modeLower, 'offline')) { $modeCls = 'cit-mode-offline'; }
                    else { $modeCls = 'cit-mode-default'; }

                    $levelTag = !empty($row->mode_lavel_name)
                        ? '<span class="cit-level-tag">' . ucwords(mb_substr($row->mode_lavel_name, 0, 14)) . '</span>'
                        : '';
                    echo '<div class="d-flex flex-column align-items-start gap-1">
                        <span class="cit-mode-pill ' . $modeCls . '">' . ucwords(mb_substr(@$row->mode_name, 0, 12)) . '</span>
                        ' . $levelTag . '
                    </div>';
                @endphp
            </td>
            {{-- Interviewers avatar stack (preserve JS, DB query unchanged) --}}
            <td>
                @php
                    $html = '';
                    $interviewers = $row->interviewers;
                    if (!empty($interviewers)) {
                        $interviewersArr = explode(',', $interviewers);
                        $interviewersData = DB::table('users')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('users.company_id', '=', $row->company_id)
                            ->whereIn('users.name', $interviewersArr)
                            ->get();
                        foreach ($interviewersData as $intVal) {
                            $bgd = GeneralMethod::icoinText($intVal->name);
                            if (!empty($intVal->image)) {
                                $html .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . $intVal->name . '"><img alt="image" src="' . $intVal->image . '"></div>';
                            } else {
                                $html .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . ucwords($intVal->name) . '"><div class="symbol-label fs-8 ' . $bgd . '">' . mb_substr($intVal->name, 0, 1) . '</div></div>';
                            }
                        }
                    }
                    echo '<div class="symbol-group symbol-hover">' . ($html ?: '<span class="text-muted fs-7">—</span>') . '</div>';
                @endphp
            </td>
            {{-- Source badge --}}
            <td>
                @php
                    $src = trim(@$row->source ?? '');
                    echo '<span class="cit-source-badge" data-toggle="tooltip" title="' . $src . '">' . (mb_strlen($src) ? mb_substr($src, 0, 14) : '—') . '</span>';
                @endphp
            </td>
            {{-- Interview date + time --}}
            <td>
                @php
                    echo '<div class="d-flex flex-column">
                        <span class="cit-date-bold">' . Carbon::parse($row->schedule_date)->format('d M, Y') . '</span>
                        <span class="cit-date-time">' . $row->schedule_to_time . ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '') . '</span>
                    </div>';
                @endphp
            </td>
            {{-- Feedback status pill (preserve show-feedback JS hook) --}}
            <td>
                @php
                    $available_status_name = '';
                    if ($row->available_status > 0) {
                        $available_status_name = @$available_status[@$row->available_status]['name'];
                    }
                    $feedback = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 22));
                    $asLower = strtolower($available_status_name);
                    if (str_contains($asLower, 'select'))                              { $fbCls = 'cit-fb-selected'; }
                    elseif (str_contains($asLower, 'reject'))                          { $fbCls = 'cit-fb-rejected'; }
                    elseif (str_contains($asLower, 'hold'))                            { $fbCls = 'cit-fb-hold'; }
                    elseif (str_contains($asLower, 'next') || str_contains($asLower, 'round')) { $fbCls = 'cit-fb-next'; }
                    else                                                               { $fbCls = 'cit-fb-default'; }

                    $fbLabel = $available_status_name ? ucwords(mb_substr($available_status_name, 0, 14)) : '—';
                    $fbSub   = $feedback ? '<span class="cit-date-time">' . mb_substr($feedback, 0, 14) . '</span>' : '';
                    echo '<div class="d-flex flex-column align-items-start gap-1">
                        <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="cit-fb-pill ' . $fbCls . ' show-feedback">' . $fbLabel . '</a>
                        ' . $fbSub . '
                    </div>';
                @endphp
            </td>
            {{-- Star rating CSS (no img dependency) --}}
            <td>
                @php
                    $average_star_rating = (int) ($row->average_star_rating ?? 0);
                    $ratingLabels = [1=>'Bad', 2=>'Poor', 3=>'Average', 4=>'Good', 5=>'Excellent'];
                    $starsHtml = '<div class="cit-stars">';
                    for ($s = 1; $s <= 5; $s++) {
                        $starsHtml .= $s <= $average_star_rating
                            ? '<i class="fas fa-star cit-star-fill"></i>'
                            : '<i class="fas fa-star cit-star-empty"></i>';
                    }
                    $starsHtml .= '</div>';
                    echo $starsHtml;
                    if ($average_star_rating > 0) {
                        echo '<div class="cit-rating-label">' . ($ratingLabels[$average_star_rating] ?? '') . '</div>';
                    }
                @endphp
            </td>
            {{-- Stage badge (preserve interview_stage_sub_stage / interview_client_stage_sub_stage JS selectors) --}}
            <td>
                @php
                    $stage_default_css = 'interview_stage_sub_stage';
                    if ($row->thirdparty_company_id > 0) {
                        $stage_default_css = 'interview_client_stage_sub_stage';
                    }
                    $stageSub = '';
                    if ($row->master_status_id == 8) { $stageSub = 'Private'; }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                        if (!empty($childStatus)) { $stageSub = @$childStatus->name; }
                    }
                    $stageSubHtml = $stageSub ? '<span class="text-muted fw-bold d-block fs-7 mt-1">' . $stageSub . '</span>' : '';
                    echo '<a href="javascript:void(0);" data-row-id="' . @$row->job_application_id . '" class="d-flex flex-column align-items-start ' . $stage_default_css . '" data-toggle="modal">
                        <span class="badge fw-bolder ' . @$row->css_style . ' px-2 py-1" style="font-size:11px">' . mb_substr(@$row->master_status_name, 0, 12) . '</span>
                        ' . $stageSubHtml . '
                    </a>';
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" style="text-align:center; padding:40px 20px; color:#a1a5b7; font-size:13px;">
            <i class="fas fa-calendar-alt" style="font-size:32px; color:#e4e6ef; display:block; margin-bottom:10px;"></i>
            No interviews scheduled for this client.
        </td>
    </tr>
@endif
