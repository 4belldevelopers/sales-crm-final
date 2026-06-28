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
    @endphp
        <tr style="height: 0px; position: relative;">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
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

                    $profile_detail = 'show-application-detail';
                    if ($row->thirdparty_company_id == $company_id) {
                        $profile_detail = 'client-show-application-detail';
                    }

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->wiseID .
                        '" class="' .
                        $profile_detail .
                        '">
                      <div data-row-id="' .
                        $row->wiseID .
                        '"  class="symbol-label ' .
                        $bgTextDecore .
                        ' border-radius-5 ' .
                        $profile_detail .
                        '">' .
                        ucwords($strVal) .
                        '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->wiseID .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $profile_detail .
                        '" data-toggle="tooltip" title="' .
                        ucwords(@$row->full_name) .
                        '">' .
                        ucwords(mb_substr(@$row->full_name, 0, 20)) .
                        '</a>
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->wiseID .
                        '" class="fw-bold text-muted d-block fs-7 ' .
                        $profile_detail .
                        ' text-hover-primary" data-toggle="tooltip" title="' .
                        ucwords(@$row->current_designation) .
                        '">' .
                        ucwords(mb_substr(@$row->current_designation, 0, 20)) .
                        '</a>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $icon = '&nbsp;<i class="fas fa-hand-scissors" style="color:#00CC00"></i>';
                    if ($row->thirdparty_company_id == $company_id) {
                        $icon = '&nbsp;<i class="fas fa-link" style="color: #0000CC"></i></a>';
                    }

                    $client_name = '';
                    $client_title_name = '';

                    if (@$row->companyIdCheck == auth()->user()->company_id) {
                        $client_name_com = GeneralMethod::companyDetailsByClient($row->job_id);
                        if (!empty($client_name_com)) {
                            $client_title_name = $client_name_com;
                            $client_name = ucwords(mb_substr($client_title_name, 0, 25));
                        } else {
                            $client_title_name = $row->client_name;
                            $client_name = ucwords(mb_substr($client_title_name, 0, 25));
                        }
                    } else {
                        $client_title_name = $row->company_name;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 25));
                    }

                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                        ucwords($row->job_title) .
                        '">' .
                        ucwords(mb_substr($row->job_title, 0, 20)) .
                        '</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                        ucwords($client_title_name) .
                        '">' .
                        ucwords(mb_substr($client_name, 0, 22)) .
                        '
                  </div>';
                @endphp

            </td>
            <td>
                @php

                    $status = $active = $hold = $inactive = $class = $recommendation_id = '';

                    // $job_status = $row->job_status;
                    
                    $job_status_get = GeneralMethod::jobDetails($row->job_id);
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
                        $status = 'REMOVED';
                        $class = 'closed-bg';
                    }

                    $ulink =
                        '<a href="javascript:void(0);" class="d-flex flex-column ' .
                        @$class .
                        '  text-hover-success" >' .
                        $status .
                        '</a>';

                    $recommendation_text = __('messages.clinet_tag');
                    if (@$row->companyIdCheck == auth()->user()->company_id) {
                        $recommendation_text = __('messages.internal_tag');
                        $ulink =
                            '<a href="javascript:void(0);" class="fs-6 ' .
                            @$class .
                            ' text-hover-success">' .
                            $status .
                            '</a>';
                    }

                    echo '<div class="d-flex flex-column">' .
                        $ulink .
                        '
                        <span class="fw-bold text-muted d-block fs-7">' .
                        @$row->job_id .
                        ' -' .
                        $recommendation_text .
                        '</span>
                        </div>';

                @endphp
            </td>
            <td>
                @php
                    $interLink = '';
                    if ($row->interview_mode == 2) {
                        $schedule_date = $row->schedule_date;
                        $current_date = date('Y-m-d H:i:s');
                        $schedule_timestamp = strtotime($schedule_date);
                        $current_timestamp = strtotime($current_date);

                        $url = $row->interview_mention_link;
                        $urlComponents = parse_url($url);

                        if ($schedule_timestamp >= $current_timestamp) {
                            if ($urlComponents === false) {
                                $interLink =
                                    '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>';
                            }

                            $scheme = $urlComponents['scheme'] ?? '';

                            if ($scheme === 'http' || $scheme === 'https') {
                                $interLink =
                                    '<a href="' .
                                    $row->interview_mention_link .
                                    '" class="d-flex flex-column" target="_blank">Interview Link</a>';
                            } else {
                                $interLink =
                                    '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>';
                            }
                        }
                    } else {
                        $interLink = '';
                    }
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        ucwords(mb_substr($row->mode_name, 0, 10)) .
                        '</a> 
                    <!--<span class="text-muted fw-bold d-block fs-7">' .
                        ucwords(mb_substr($row->mode_lavel_name, 0, 10)) .
                        '</span>--> 
                  </div>' .
                        $interLink;
                @endphp
            </td>
            <td>
                @php

                    $contactHtml = $real_company_id = $interviewers = '';
                    $interviewers = $row->interviewers;
                    $real_company_id = $row->real_company_id;

                    $contactHtml = '<div class="d-flex flex-column">
                    							<div class="symbol-group symbol-hover">';
                    if (!empty($interviewers)) {
                        $interviewersArr = explode(' , ', trim($interviewers));
                        // $interviewersData = DB::table('users')
                        //     ->select('users.id', 'users.name', 'users.image')
                        //     ->whereIn('users.name', $interviewersArr)
                        //     ->where('users.company_id', '=', $real_company_id)
                        //     ->get();
                        if (!empty($interviewersArr)) {
                            foreach ($interviewersArr as $intVal) {
                                $bgTextDecore = GeneralMethod::icoinText(@$intVal);
                                $image_path = '';
                                // if ($intVal != '') {
                                //     $image_path = $intVal->image;
                                // }
                                if (!empty($image_path)) {
                                    $contactHtml .=
                                        '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip1" data-html="true" title="' .
                                        ucwords(@$intVal) .
                                        '" >
								<img class="me-2" alt="Pic" src="' .
                                        $image_path .
                                        '">
								</div>';
                                } else {
                                    $contactHtml .=
                                        '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="' .
                                        ucwords(@$intVal) .
                                        '" >
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
								<a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
									<div class="symbol-label fs-8 fs-8 ' .
                                        $bgTextDecore .
                                        ' border-radius-100">' .
                                        mb_substr($intVal, 0, 1) .
                                        '</div>
								</a>		 
								</div>
								</div>';
                                }
                            }
                        }
                    }
                    $contactHtml .= '</div>
                    			</div>';
                    echo $contactHtml;
                @endphp
            </td>
            <td>
                @php
                    $bgTextDecore = GeneralMethod::icoinText(@$row->posted_by);
                    $posted_by_image = '';
                    if (!empty($row->posted_by_image)) {
                        $posted_by_image = @$row->posted_by_image;
                    }
                    echo '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$row->posted_by .
                        '">' .
                        mb_substr($row->posted_by, 0, 10) .
                        '</a>
								  </div>
								  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                        @$row->company_name .
                        '">' .
                        mb_substr(@$row->company_name, 0, 10) .
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

                    $feedback = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 20));
                    $customHtml = 'show-feedback_no';

                    if ($row->thirdparty_company_id == $company_id) {
                        $customHtml = '';
                        if (empty($feedback)) {
                            $feedback = '<span class=""> Pending Feedback</span>';
                        }
                    } else {
                        if (empty($feedback)) {
                            $feedback = '<span class="">-----</span>';
                            $available_status_name = '';
                        }
                    }
                    $feedbackdataView ='';
                    $feedbackdata = DB::table('interview_skills_feedback')->where('interview_id',$row->id)->orderByDesc('feedback_id')->count();
                    if($feedbackdata>0){
                        $feedbackdataView = '';
                    }

                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $customHtml .
                        '">' .
                        @$feedback .
                        '</a>
                        <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        ucwords(mb_substr(@$available_status_name, 0, 20)) .
                        '</span>
                        </div>'.$feedbackdataView;

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
                        str_replace("index.php","",@$imagePath) .
                        '" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        @$text .
                        '</span>';
                @endphp
            </td>
            <td>
                @php
                     $name = '';
                    if ($row->master_status_id == 8) {
                        $name = 'Private';
                    }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $name = @$row->child_status_name;
                    }
                    echo '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 ' .
                        @$row->css_style .
                        '">' .
                        mb_substr(@$row->master_status_name, 0, 10) .
                        '</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7 px-3 py-1">' .$name .'</span>
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
