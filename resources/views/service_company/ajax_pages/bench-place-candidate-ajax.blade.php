@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    @endphp
        <tr class="placed-row">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox"  data-jobId="' .
                        $row->jobId .
                        '" value="' .
                        $row->candidate_id .
                        '" />
                  </div>';
                @endphp
            </td>
            {{-- Candidate: name, designation, skill match badges — real ES data, unchanged logic --}}
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
                            if ($app_duplicate == 1 || $app_duplicate == 2) {
                                if ($app_duplicate == 1) {
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
                                    $flag = $application_cnt > 1 ? 1 : 0;
                                } elseif ($app_duplicate == 2) {
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
                                        ->orderby('job_applications.id', 'DESC')
                                        ->count();
                                    $flag = $application_cnt > 1 ? 1 : 0;
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
                        }

                        if (@$row->condidate_type == 'bench_condidate') {
                            $tredmark = '[Bench]';
                        }

                        $photo = '';
                        if (!empty(@$row->photo)) {
                            $photo = @$row->photo;
                        }

                        if (!empty($photo)) {
                            $nameImg =
                                '<img src="' . @$photo . '" alt="image" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-1 bg-success rounded-circle border border-2 border-white h-10px w-10px"></div>';
                        } else {
                            if ($flag == 1) {
                                $nameImg = '<span class="fs-6 fw-bolder">' . strtoupper(@$strVal) . '</span>';
                            } else {
                                $nameImg =
                                    '<div class="symbol-label placed-avatar ' .
                                    @$bgTextDecore .
                                    '">' .
                                    strtoupper(@$strVal) .
                                    '</div>';
                            }
                        }

                        $skilltag = '';
                        $skillArr = explode(',', $row->skills);
                        $percentage = GeneralMethod::skill_percentage($row->job_id, $skillArr);
                        if (count($skillArr) > 0) {
                            $shown = 0;
                            foreach ($skillArr as $sval) {
                                if (trim($sval) === '' || $shown >= 2) {
                                    continue;
                                }
                                $skilltag .= '<span class="badge placed-skill-badge">' . ucfirst(trim($sval)) . '</span>';
                                $shown++;
                            }
                            if (count(array_filter($skillArr, fn($s) => trim($s) !== '')) > 2) {
                                $skilltag .= '<span class="badge placed-skill-badge">+' . (count(array_filter($skillArr, fn($s) => trim($s) !== '')) - 2) . '</span>';
                            }
                        }

                        $nameLinkClass = $flag == 1 ? 'text-danger' : 'text-dark';
                        $subLinkClass = $flag == 1 ? 'text-danger' : 'text-muted';

                        echo '<div class="d-flex align-items-start">
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 position-relative flex-shrink-0">
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $profile_sidebar_css . '">' . $nameImg . '</a>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $nameLinkClass . ' fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . '" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' .
                                        ucwords(mb_substr(@$row->full_name, 0, 22)) . $covid .
                                        '<sup class="text-warning fw-bold ms-1">' . $tredmark . '</sup></a>
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $subLinkClass . ' fw-bold d-block fs-8 text-hover-primary ' . $profile_sidebar_css . '" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' .
                                        ucwords(mb_substr(@$row->current_designation, 0, 24)) . '</a>
                                    <div class="mt-1">' . $skilltag . '</div>
                                </div>
                              </div>';
                    }
                @endphp
            </td>
            {{-- Placement Timeline: only "Joined" is real (of_joined_date); Offer node is a static
                 milestone marker since no offer-date field exists in the ES document yet. --}}
            <td>
                @php
                    $joined_date = $joined_time = $date = '';
                    $date = @$row->of_joined_date;
                    $sinceText = '';
                    if ($date != '0000-00-00 00:00:00' && !empty($date)) {
                        $joined_date = date('d M', strtotime($date));
                        $joined_time = date('d M Y', strtotime($date));
                        $days = now()->diffInDays(\Carbon\Carbon::parse($date), false);
                        $sinceText = abs(round($days)) . ' days since joining';
                    }

                    echo '<div class="placed-timeline">
                            <div class="placed-timeline-track">
                                <div class="placed-timeline-node is-static" data-toggle="tooltip" title="Offer stage"></div>
                                <span class="placed-timeline-line"></span>
                                <div class="placed-timeline-node is-done" data-toggle="tooltip" title="Joined ' . $joined_time . '"></div>
                            </div>
                            <div class="d-flex flex-column mt-1">
                                <span class="text-dark fw-bolder fs-8">Joined ' . $joined_date . '</span>
                                <span class="text-muted fw-bold fs-9">' . $sinceText . '</span>
                            </div>
                          </div>';
                @endphp
            </td>
            {{-- Client & Job Details: real job title / client / job-status --}}
            <td>
                @php
                    $removed_status = $icon = '';
                    $job_title = ucwords(mb_substr($row->title, 0, 18));
                    $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
                    if (!empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                    } else {
                        $client_title_name = $row->client_name;
                    }
                    $client_name = ucwords(mb_substr($client_title_name, 0, 18));

                    $job_status_get = GeneralMethod::jobDetails($row->jobId);
                    $job_status = $job_status_get->status;
                    $status = ucwords($job_status);
                    $class = $job_status_arr[$job_status];
                    $recommendation_id = $row->recommendation_id;

                    if ($status == 'Active') {
                        $status = 'Hiring';
                    }
                    if ($status == 'Inactive') {
                        $status = 'Closed';
                    }

                    $recommendation_text = __('messages.clinet_tag');
                    if (
                        ($recommendation_id > 0 && @$row->client_comapny_id == auth()->user()->company_id) ||
                        @$row->client_comapny_id == auth()->user()->company_id
                    ) {
                        $recommendation_text = __('messages.internal_tag');
                    }

                    echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . $row->title . '">' . $job_title . '</a>
                            <span class="text-muted fw-bold d-block fs-8" data-toggle="tooltip" title="' . @$client_title_name . '">' . @$client_name . '</span>
                            <span class="fw-bold fs-9 ' . @$class . '">' . $status . ' &middot; ' . $recommendation_text . '</span>
                          </div>';
                @endphp
            </td>
            {{-- Recruiter: real posted_by / company --}}
            <td>
                @php
                    if ($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0) {
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                        $rowpostedby = $row->rec_candidate_assign_id ? $row->posted_by : $rowpostedby;
                    } else {
                        $rowpostedby = $row->posted_by;
                    }

                    $post_company_name = @$row->post_company_name ?: @$row->company_name;

                    echo '<div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$rowpostedby . '">' . ucfirst(mb_substr($rowpostedby, 0, 14)) . '</span>
                            <span class="text-muted fw-bold d-block fs-8" data-toggle="tooltip" title="' . @$post_company_name . '">' . mb_substr(@$post_company_name, 0, 16) . '</span>
                          </div>';
                @endphp
            </td>
            {{-- Commercials: real offer amount / employment type --}}
            <td>
                @php
                    $employment_type = $row->of_employment_type ?? '-----';
                    $offer_currency = $currencySymbole[$row->of_offer_currency ? @$row->of_offer_currency : 0];
                    $offer_amount = GeneralMethod::thousandsCurrencyFormat($row->of_offer_amount ? @$row->of_offer_amount : 0);
                    $offer_mode = $salaryModeOffer[$row->of_offer_mode ? @$row->of_offer_mode : 0];

                    if (@$row->confidential == 1) {
                        $offerAmont = 'Confidential';
                        $allow = GeneralMethod::confidentialAmontShow($row->jobId);
                        if ($allow) {
                            $offerAmont = @$offer_currency . ' ' . @$offer_amount . '/' . mb_substr(@$offer_mode, 0, 1);
                        }
                    } else {
                        $offerAmont = @$offer_currency . ' ' . @$offer_amount . '/' . mb_substr(@$offer_mode, 0, 1);
                    }

                    echo '<div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6">' . $offerAmont . '</span>
                            <span class="text-muted fw-bold d-block fs-8">' . $employment_type . '</span>
                          </div>';
                @endphp
            </td>
            {{-- Current Assignment: real placed status + real sub-status from bench_status_subline() --}}
            <td>
                @php
                    $result = GeneralMethod::bench_status_subline($row->status);
                    $subS = $result->name ?? '';

                    echo '<div class="d-flex flex-column">
                            <span class="placed-status-pill ' . $row->css_style . '">
                                <i class="fa-solid fa-circle-check fs-7 me-1"></i>Placed
                            </span>
                            <span class="text-muted fw-bold d-block fs-8 mt-1">' . $subS . '</span>
                          </div>';
                @endphp
            </td>
            {{-- Actions: kebab menu — the only wired item reuses the existing stage/status modal --}}
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-light placed-action-btn" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical fs-6"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item stage_sub_stage" data-row-id="{{ @$row->wiseId }}">
                                View Placement Status
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px;">
        <td colspan="8" class="text-center text-muted">
            No data available in table
        </td>
    </tr>
@endif
