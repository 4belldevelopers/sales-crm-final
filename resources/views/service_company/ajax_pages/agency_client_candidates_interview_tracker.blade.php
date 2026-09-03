@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $available_status = GeneralMethod::available_status();

    $childStatusIds = collect($data)->pluck('child_status_id')->filter()->unique()->toArray();
    $childStatusMap = !empty($childStatusIds) ? DB::table('candidate_status')->select('id', 'name')->whereIn('id', $childStatusIds)->get()->keyBy('id') : collect();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
      $row = (object) $rowArr;
      // dd($row);
    @endphp
        <tr>
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

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 custom-show-application-detail" style="float:left" data-row-id="' .
                        $row->job_application_id .
                        '" >
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->job_application_id .
                        '" class="custom-show-application-detail">
                      <div data-row-id="' .
                        $row->job_application_id .
                        '"  class="symbol-label ' .
                        $bgTextDecore .
                        ' border-radius-5 custom-show-application-detail">' .
                        ucwords($strVal) .
                        '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->job_application_id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="' .
                        ucwords(@$row->full_name) .
                        '">' .
                        ucwords(mb_substr(@$row->full_name, 0, 20)) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords(@$row->current_designation) .
                        '">' .
                        ucwords(mb_substr(@$row->current_designation, 0, 20)) .
                        '</span>
                  </div>';

                @endphp
            </td>
            <td>
                @php
                    $company_client_name = $row->company_name;
                    if(auth()->user()->company_id==$row->job_company_id){
                        $company_client_name = $row->client_name;
                    }
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row->job_title) .
                        '">' .
                        ucwords(mb_substr($row->job_title, 0, 20)) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($row->company_name) .
                        '">' .
                        ucwords(mb_substr($company_client_name, 0, 20)) .
                        '</span>
                  </div>';

                @endphp
            </td>
            <td>
                @php

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row->mode_name) .
                        '">' .
                        ucwords(mb_substr($row->mode_name, 0, 8)) .
                        '</a> 
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($row->mode_lavel_name) .
                        '">' .
                        ucwords(mb_substr($row->mode_lavel_name, 0, 8)) .
                        '</span> 
                  </div>';

                @endphp

            </td>
            <td>
                @php

                    $html = '';
                    $interviewers = $row->interviewers;
                    if (!empty($interviewers)) {
                        $interviewersArr = explode(',', $interviewers);
                        $interviewersData = DB::table('users')
                            ->select('users.id', 'users.name', 'users.image')
                            ->where('users.company_id', '=', $row->company_id) //current_company_id
                            ->whereIn('users.name', $interviewersArr)
                            ->get();
                        if (!empty($interviewersData)) {
                            foreach ($interviewersData as $intVal) {
                                $name = $image = '';
                                $name = $intVal->name;
                                $image = $intVal->image;

                                $imagePath = '';
                                $imagePath = @$image;
                                $html .=
                                    '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' .
                                    @$name .
                                    '">
													<img alt="image" src="' .
                                    @$imagePath .
                                    '">
												</div>';
                            }
                        }
                    }

                    echo ' <div class="d-flex flex-column"><div class="symbol-group symbol-hover">' . $html . '</div>';

                @endphp
            </td>
            <td>
                @php

                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->source);
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
                        @$row->source .
                        '">' .
                        mb_substr($row->source, 0, 10) .
                        '</a>
				  
                  			</div>
                    		<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords(@$row->company_name) .
                        '">' .
                        ucwords(mb_substr(@$row->company_name, 0, 25)) .
                        '</span>';

                @endphp
            </td>
            <td>
                @php

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        Carbon::parse($row->schedule_date)->format('d M, Y') .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7">' .
                        $row->schedule_to_time . ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '').
                        '</span>
                  </div>';

                @endphp
            </td>
            <td>
                @php

                    $available_status_name = '';
                    if ($row->available_status > 0) {
                        $available_status_name = @$available_status[@$row->available_status]['name'];
                    } else {
                        //$available_status_name	=	@$available_status[6]['name'];
                    }
                     $feedback = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 22));
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-toggle="tooltip" title="' .
                        ucwords($available_status_name) .
                        '">' .
                        ucwords(mb_substr($available_status_name, 0, 12)) .
                        '</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords(@$feedback) .
                        '">' .
                        ucwords(mb_substr(@$feedback, 0, 12)) .
                        '</span>
                  </div>';
                @endphp
            </td>
            <td>
                @php

                    $imagePath = $text = '';
                    $average_star_rating = '';
                    $average_star_rating = $row->average_star_rating;
                    if ($average_star_rating == 1) {
                        $imagePath = url('public/img/star/1.png');
                        $text = 'Bad';
                    } elseif ($average_star_rating == 2) {
                        $imagePath = url('public/img/star/2.png');
                        $text = 'Poor';
                    } elseif ($average_star_rating == 3) {
                        $imagePath = url('public/img/star/3.png');
                        $text = 'Average';
                    } elseif ($average_star_rating == 4) {
                        $imagePath = url('public/img/star/4.png');
                        $text = 'Best';
                    } elseif ($average_star_rating == 5) {
                        $imagePath = url('public/img/star/5.png');
                        $text = 'Very Best';
                    } else {
                        $imagePath = url('public/img/star/5-blank.png');
                        $text = 'Blank';
                    }

                    echo ' <img src="' .
                        str_replace('index.php', '', @$imagePath) .
                        '" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        @$text .
                        '</span>';

                @endphp
            </td>
            <td>
                @php

                    $stage_default_css = 'interview_stage_sub_stage';
                    if ($row->thirdparty_company_id > 0) {
                        $stage_default_css = 'interview_client_stage_sub_stage';
                    }
                    $name = '';
                    if ($row->master_status_id == 8) {
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
                        @$row->job_application_id .
                        '" class="d-flex flex-column ' .
                        $stage_default_css .
                        '" data-toggle="modal">
						<span class="badge fw-bolder fs-6 ' .
                        @$row->css_style .
                        ' px-2 py-1">' .
                        mb_substr(@$row->master_status_name, 0, 10) .
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
