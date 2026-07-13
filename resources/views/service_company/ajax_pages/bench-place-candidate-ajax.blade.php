@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
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
                        } elseif (@$row->condidate_type != 'bench_condidate') {
                            $tredmark = '[Tracker]';
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

                        // Experience + current city — both are real fields already stored on the
                        // ES candidate document (job_applications.total_exp / .city), same source
                        // used by the sort/filter logic elsewhere in this controller.
                        $expLocBits = [];
                        if (!empty(@$row->total_exp)) {
                            $expLocBits[] = rtrim(@$row->total_exp, '.') . ' Yrs';
                        }
                        if (!empty(@$row->city)) {
                            $expLocBits[] = ucwords(@$row->city);
                        }
                        $expLocLine = implode(' &middot; ', $expLocBits);

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
                                    <span class="text-muted fw-semibold fs-9 d-block">' . $expLocLine . '</span>
                                    <div class="mt-1">' . $skilltag . '</div>
                                </div>
                              </div>';
                    }
                @endphp
            </td>
            {{-- Placement Timeline: the full Bench -> Offer -> Joined -> Placed journey.
                 Bench date = job_applications.created_at (the table backing this ES index),
                 looked up by the candidate's own primary key — one indexed PK lookup, same
                 cost profile as the duplicate-check / job_assign lookups already done per row
                 above. Joined date = of_joined_date (already real). Offer date comes from the
                 Offer Tracker module: offer_job_applications.application_id -> offer_id, then
                 the offer_create_main_tds row for that offer with candidate_status = 1
                 ("release", per the schema comment / the same enum ServiceCompanyApplicationOfferController
                 already relies on for its Released KPI) — its created_at is the moment that
                 offer version was released to the candidate. Placed = same event as Joined in
                 this data model (there is no separate "placed" timestamp distinct from
                 joining), so it reuses the joined date. --}}
            <td>
                @php
                    $joinedRaw = @$row->of_joined_date;
                    $hasJoined = !empty($joinedRaw) && $joinedRaw != '0000-00-00 00:00:00';

                    // created_at is already indexed on the ES candidate doc (see
                    // ElasticCandidatesIndex.php), so no extra DB query is needed here.
                    $benchRaw = @$row->created_at;
                    $hasBench = !empty($benchRaw);

                    $offerRaw = null;
                    if (!empty(@$row->candidate_id)) {
                        $offerAppId = DB::table('offer_job_applications')->where('application_id', $row->candidate_id)->value('id');
                        if ($offerAppId) {
                            $offerRaw = DB::table('offer_create_main_tds')
                                ->where('offer_id', $offerAppId)
                                ->where('candidate_status', 1)
                                ->orderByDesc('id')
                                ->value('created_at');
                        }
                    }
                    $hasOffer = !empty($offerRaw);

                    $dateFmt = 'd M y'; // e.g. 02 May 26
                    $benchLabel = $hasBench ? date($dateFmt, strtotime($benchRaw)) : '--';
                    $offerLabel = $hasOffer ? date($dateFmt, strtotime($offerRaw)) : '--';
                    $joinedLabel = $hasJoined ? date($dateFmt, strtotime($joinedRaw)) : '--';

                    $durationText = '';
                    if ($hasBench && $hasJoined) {
                        $durationDays = (int) floor(abs(\Carbon\Carbon::parse($benchRaw)->diffInSeconds(\Carbon\Carbon::parse($joinedRaw))) / 86400);
                        $durationText = 'Total Duration: ' . $durationDays . ' Days';
                    }

                    $isBench = (@$row->condidate_type == 'bench_condidate');

                    $offerNodeCls  = $hasOffer  ? 'is-done' : 'is-static';
                    $joinedNodeCls = $hasJoined ? 'is-done' : 'is-static';
                    $placedNodeCls = $hasJoined ? 'is-done' : 'is-static';
                    $lineAfterOffer  = $hasOffer  ? 'is-done' : '';
                    $lineAfterJoined = $hasJoined ? 'is-done' : '';

                    if ($isBench) {
                        // Bench candidates: 4-node timeline — Bench → Offer → Joined → Placed
                        $benchNodeCls  = $hasBench ? 'is-done' : 'is-static';
                        $lineAfterBench = $hasBench ? 'is-done' : '';

                        $timeline = '
                            <div class="placed-timeline-track">
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $benchNodeCls . '" data-toggle="tooltip" title="Bench"></div>
                                    <span class="placed-timeline-node-label">' . $benchLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterBench . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $offerNodeCls . '" data-toggle="tooltip" title="Offer"></div>
                                    <span class="placed-timeline-node-label">' . $offerLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterOffer . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $joinedNodeCls . '" data-toggle="tooltip" title="Joined"></div>
                                    <span class="placed-timeline-node-label">' . $joinedLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterJoined . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $placedNodeCls . '" data-toggle="tooltip" title="Placed"></div>
                                    <span class="placed-timeline-node-label">' . $joinedLabel . '</span>
                                </div>
                            </div>
                            <div class="placed-timeline-labels">
                                <span>Bench</span><span>Offer</span><span>Joined</span><span>Placed</span>
                            </div>';
                    } else {
                        // Non-bench / tracker candidates: 3-node timeline — Offer → Joined → Placed
                        $timeline = '
                            <div class="placed-timeline-track">
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $offerNodeCls . '" data-toggle="tooltip" title="Offer"></div>
                                    <span class="placed-timeline-node-label">' . $offerLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterOffer . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $joinedNodeCls . '" data-toggle="tooltip" title="Joined"></div>
                                    <span class="placed-timeline-node-label">' . $joinedLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterJoined . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $placedNodeCls . '" data-toggle="tooltip" title="Placed"></div>
                                    <span class="placed-timeline-node-label">' . $joinedLabel . '</span>
                                </div>
                            </div>
                            <div class="placed-timeline-labels">
                                <span>Offer</span><span>Joined</span><span>Placed</span>
                            </div>';
                    }

                    echo '<div class="placed-timeline">' . $timeline . '
                            <div class="text-muted fw-bold fs-9 mt-1">' . $durationText . '</div>
                          </div>';
                @endphp
            </td>
            {{-- Client & Job Details: real job title / client / job-status --}}
            <td>
                @php
                    $removed_status = $icon = '';
                    $job_title = ucwords(mb_substr($row->title, 0, 18));
                    // client_name is already indexed on the ES candidate doc, so this reads
                    // straight from it instead of the heavy companyDetailsByClient() aggregate
                    // query (LEFT JOIN + ~15 SUM/CASE clauses) that used to run per row.
                    $client_title_name = @$row->client_name;
                    $client_name = ucwords(mb_substr($client_title_name, 0, 18));

                    $job_status_get = GeneralMethod::jobDetails($row->jobId);
                    $recommendation_id = $row->recommendation_id;

                    // Internal Client / Direct Client — same real client-relationship check the
                    // job-status logic used before, just relabeled with this app's own existing
                    // translation strings (messages.internal_client_tag / direct_clinet_tag),
                    // the same ones the client-listing pages already use for this exact concept.
                    $recommendation_text = __('messages.direct_clinet_tag');
                    $recommendation_icon = 'fa-handshake';
                    $recommendation_cls = 'text-warning';
                    if (
                        ($recommendation_id > 0 && @$row->client_comapny_id == auth()->user()->company_id) ||
                        @$row->client_comapny_id == auth()->user()->company_id
                    ) {
                        $recommendation_text = __('messages.internal_client_tag');
                        $recommendation_icon = 'fa-building-shield';
                        $recommendation_cls = 'text-primary';
                    }

                    // job location — reuses the $job_status_get document already fetched above
                    // for job status, so this adds no extra query.
                    $jobLocationBits = array_filter([@$job_status_get->city, @$job_status_get->state]);
                    $jobLocationLine = implode(', ', $jobLocationBits);

                    echo '<div class="d-flex flex-column">
                            <span class="placed-detail-line"><i class="fa-solid fa-building text-muted"></i>
                                <span class="text-muted fw-bold fs-8" data-toggle="tooltip" title="' . @$client_title_name . '">' . @$client_name . '</span>
                            </span>
                            <span class="placed-detail-line"><i class="fa-solid fa-briefcase text-muted"></i>
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . $row->title . '">' . $job_title . '</a>
                            </span>' .
                            ($jobLocationLine ? '<span class="placed-detail-line"><i class="fa-solid fa-location-dot text-muted"></i>
                                <span class="text-muted fw-semibold fs-9">' . $jobLocationLine . '</span>
                            </span>' : '') . '
                            <span class="placed-detail-line mt-1"><i class="fa-solid ' . $recommendation_icon . ' ' . $recommendation_cls . '"></i>
                                <span class="fw-bold fs-9 text-muted">' . $recommendation_text . ' &middot; ' . @$row->jobId . '</span>
                            </span>
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

                    echo '<div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$rowpostedby . '">' . ucfirst(mb_substr($rowpostedby, 0, 16)) . '</span>
                            <span class="text-muted fw-semibold d-block fs-9">Recruiter</span>
                          </div>';
                @endphp
            </td>
            {{-- Commercials: offer amount + employment type are real. Gross Margin / Placement
                 Fee has no backing field anywhere in the schema (no commission/margin column
                 on job_applications, bench_applications or bench_place), so that figure is a
                 view-only placeholder ("--") rather than an invented percentage. Which label
                 to show (Gross Margin vs Placement Fee) IS driven by the real employment type,
                 since contract/hourly placements are margin-billed and fulltime placements are
                 fee-billed. "Change" is a static, unwired action per the requested view-only
                 treatment — swap for a real handler once there's an endpoint to edit terms. --}}
            <td>
                @php
                    $employment_type = $row->of_employment_type ?? '-----';
                    $offer_currency = $currencySymbole[$row->of_offer_currency ? @$row->of_offer_currency : 0];
                    $offer_amount = GeneralMethod::thousandsCurrencyFormat($row->of_offer_amount ? @$row->of_offer_amount : 0);
                    // display-only: drop the decimal remainder (e.g. "1,730,242.80" -> "1,730,242")
                    $offer_amount = explode('.', $offer_amount)[0];
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

                    $isContractType = stripos($employment_type, 'contract') !== false || stripos($employment_type, 'hour') !== false;
                    $marginLabel = $isContractType ? 'Gross Margin' : '(Placement Fee)';

                    echo '<div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6">' . $offerAmont . '</span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="text-dark fw-semibold fs-9">-- <span class="text-muted fw-normal">' . $marginLabel . '</span></span>
                            </span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-user-tie text-muted"></i>
                                <span class="text-muted fw-semibold fs-9">' . $employment_type . '</span>
                            </span>
                            <a href="javascript:void(0);" class="placed-change-link commercial-change-btn" data-candidate-id="' . (int)@$row->candidate_id . '" data-is-bench="' . (@$row->condidate_type == 'bench_condidate' ? 1 : 0) . '" data-amount="' . (float) @$row->of_offer_amount . '" data-employment-type="' . @$employment_type . '"><i class="fa-solid fa-pen"></i>Change</a>
                          </div>';
                @endphp
            </td>
            {{-- Current Assignment: this listing's ES query already hard-filters to status=3
                 ("Work Assigned"), so bench_status_subline() always returns the same real
                 sub-status here — every row is honestly "Currently Working", not a fabricated
                 mix. The Project Completed / Candidate Left pill styles are built and ready
                 (placed-status-pill.is-info / .is-danger) for whenever the backend actually
                 differentiates these states. Expected End date and "Change" have no backing
                 field/endpoint yet, so they render as view-only placeholders. --}}
            <td>
                @php
                    $result = GeneralMethod::bench_status_subline($row->status);
                    $subS = $result->name ?? '';
                    $assignmentLabel = ($row->status == 3 || @$row->condidate_type != 'bench_condidate') ? 'Currently Working' : ucwords($subS);

                    $joinedRawForAssign = @$row->of_joined_date;
                    $joinedIsoForAssign = (!empty($joinedRawForAssign) && $joinedRawForAssign != '0000-00-00 00:00:00')
                        ? date('Y-m-d', strtotime($joinedRawForAssign)) : '';

                    echo '<div class="d-flex flex-column">
                            <span class="placed-status-pill is-success">
                                <i class="fa-solid fa-circle-check me-1"></i>' . $assignmentLabel . '
                            </span>
                            <span class="text-muted fw-semibold fs-9 mt-2">Expected End</span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-calendar-days text-muted"></i>
                                <span class="text-dark fw-semibold fs-9">--</span>
                            </span>
                            <a href="javascript:void(0);" class="placed-change-link assignment-change-btn"
                               data-candidate-id="' . (int)@$row->candidate_id . '"
                               data-is-bench="' . (@$row->condidate_type == 'bench_condidate' ? 1 : 0) . '"
                               data-name="' . e(@$row->full_name) . '"
                               data-designation="' . e(@$row->current_designation) . '"
                               data-exp-loc="' . e(@$expLocLine) . '"
                               data-client="' . e(@$client_name) . '"
                               data-employment-type="' . e(@$employment_type) . '"
                               data-joined-date="' . $joinedIsoForAssign . '"
                               data-avatar-class="' . @$bgTextDecore . '"
                               data-avatar-letter="' . strtoupper(@$strVal) . '"
                            ><i class="fa-solid fa-pen"></i>Change</a>
                          </div>';
                @endphp
            </td>
            {{-- Actions: kebab menu. "View Placement Status" reuses the existing stage/status
                 modal. "Delete" has no backing endpoint (no delete route exists for this
                 listing), so it's a view-only confirm-then-preview action — it never removes
                 the row, to avoid anyone believing a real record was deleted. --}}
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
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item text-danger placed-row-delete-btn" data-candidate-name="{{ e(@$row->full_name) }}">
                                <i class="fa-solid fa-trash-can me-2"></i>Delete
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
