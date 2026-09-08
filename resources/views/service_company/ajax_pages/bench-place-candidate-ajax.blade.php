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
        <tr class="placed-row" data-candidate-id="{{ $row->id }}">
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

                        $flag = (!empty(@$row->is_duplicate) || @$row->duplicate == 1) ? 1 : 0;
                        $company_id = auth()->user()->company_id;
                        $profile_sidebar_css = 'show-application-detail';
                        if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                            $profile_sidebar_css = 'show-application-detail';
                        }

                        if (@$row->condidate_type == 'bench_condidate') {
                            $tredmark = '<span style="display:inline-block;font-size:9px;font-weight:600;padding:1px 6px;border-radius:4px;background:#e8f0fe;color:#3b5bdb;margin-left:4px;vertical-align:middle;letter-spacing:.3px;">Bench</span>';
                        } else {
                            $tredmark = '';
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

                        $expLocBits = [];
                        if (!empty(@$row->total_exp)) {
                            $expLocBits[] = rtrim(@$row->total_exp, '.') . ' Yrs';
                        }
                        if (!empty(@$row->city)) {
                            $expLocBits[] = ucwords(@$row->city);
                        } elseif (!empty(@$row->city_name)) {
                            $expLocBits[] = ucwords(@$row->city_name);
                        }
                        $expLocLine = implode(' &middot; ', $expLocBits);

                        echo '<div class="d-flex align-items-start">
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 position-relative flex-shrink-0">
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $profile_sidebar_css . '">' . $nameImg . '</a>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $nameLinkClass . ' fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . '" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' .
                                        ucwords(mb_substr(@$row->full_name, 0, 22)) . $tredmark . '</a>
                                    <a href="javascript:void(0);" data-row-id="' . @$row->wiseId . '" class="' . $subLinkClass . ' fw-bold d-block fs-8 text-hover-primary ' . $profile_sidebar_css . '" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' .
                                        ucwords(mb_substr(@$row->current_designation, 0, 24)) . '</a>
                                    <span class="text-muted fw-semibold fs-9 d-block">' . $expLocLine . '</span>
                                    <div class="mt-1">' . $skilltag . '</div>
                                </div>
                              </div>';
                    }
                @endphp
            </td>
            <td>
                @php
                    $joinedRaw = @$row->of_joined_date;
                    $hasJoined = !empty($joinedRaw) && $joinedRaw != '0000-00-00 00:00:00';

                    $benchRaw = @$row->created_at;
                    $hasBench = !empty($benchRaw);

                    // Denormalized offer release date from ES document
                    $offerRaw = @$row->offer_released_at;
                    $hasOffer = !empty($offerRaw);

                    $dateFmt = 'd M y';
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
                        // Non-bench candidates: 4-node timeline — Sourcing → Evaluation → Offer → Joined
                        // Read stage dates directly from ES denormalized document fields
                        $sourcingRaw  = @$row->sourcing_date;
                        $evalRaw      = @$row->evaluation_date;
                        $tlOfferRaw   = @$row->offer_released_at ?: $offerRaw;
                        $tlJoinedRaw  = $joinedRaw;

                        $hasSourcing  = !empty($sourcingRaw);
                        $hasEval      = !empty($evalRaw);
                        $hasTlOffer   = !empty($tlOfferRaw);
                        $hasTlJoined  = !empty($tlJoinedRaw);

                        $sourcingLabel = $hasSourcing  ? date($dateFmt, strtotime($sourcingRaw))  : '--';
                        $evalLabel     = $hasEval      ? date($dateFmt, strtotime($evalRaw))      : '--';
                        $tlOfferLabel  = $hasTlOffer   ? date($dateFmt, strtotime($tlOfferRaw))   : '--';
                        $tlJoinedLabel = $hasTlJoined  ? date($dateFmt, strtotime($tlJoinedRaw))  : '--';

                        $sourcingNodeCls   = $hasSourcing ? 'is-done' : 'is-static';
                        $lineAfterSourcing = $hasSourcing ? 'is-done' : '';
                        $evalNodeCls       = $hasEval     ? 'is-done' : 'is-static';
                        $lineAfterEval     = $hasEval     ? 'is-done' : '';
                        $tlOfferNodeCls    = $hasTlOffer  ? 'is-done' : 'is-static';
                        $lineAfterTlOffer  = $hasTlOffer  ? 'is-done' : '';
                        $tlJoinedNodeCls   = $hasTlJoined ? 'is-done' : 'is-static';

                        if ($hasSourcing && $hasTlJoined) {
                            $durationDays = (int) floor(abs(\Carbon\Carbon::parse($sourcingRaw)->diffInSeconds(\Carbon\Carbon::parse($tlJoinedRaw))) / 86400);
                            $durationText = 'Total Duration: ' . $durationDays . ' Days';
                        }

                        $timeline = '
                            <div class="placed-timeline-track">
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $sourcingNodeCls . '" data-toggle="tooltip" title="Sourcing"></div>
                                    <span class="placed-timeline-node-label">' . $sourcingLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterSourcing . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $evalNodeCls . '" data-toggle="tooltip" title="Evaluation"></div>
                                    <span class="placed-timeline-node-label">' . $evalLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterEval . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $tlOfferNodeCls . '" data-toggle="tooltip" title="Offer"></div>
                                    <span class="placed-timeline-node-label">' . $tlOfferLabel . '</span>
                                </div>
                                <span class="placed-timeline-line ' . $lineAfterTlOffer . '"></span>
                                <div class="placed-timeline-node-wrap">
                                    <div class="placed-timeline-node ' . $tlJoinedNodeCls . '" data-toggle="tooltip" title="Joined"></div>
                                    <span class="placed-timeline-node-label">' . $tlJoinedLabel . '</span>
                                </div>
                            </div>
                            <div class="placed-timeline-labels">
                                <span>Sourcing</span><span>Evaluation</span><span>Offer</span><span>Joined</span>
                            </div>';
                    }

                    echo '<div class="placed-timeline">' . $timeline . '
                            <div class="text-muted fw-bold fs-9 mt-1">' . $durationText . '</div>
                          </div>';
                @endphp
            </td>
            {{-- Client & Job Details: read directly from ES candidate document _source --}}
            <td>
                @php
                    $job_title = ucwords(mb_substr(@$row->title, 0, 18));
                    $client_title_name = @$row->client_name;
                    $client_name = ucwords(mb_substr($client_title_name, 0, 18));

                    $recommendation_id = @$row->recommendation_id;

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

                    // Read job location directly from ES document properties (location / city_name / state_name)
                    $jobLocationLine = @$row->location ?: implode(', ', array_filter([@$row->city_name, @$row->state_name]));

                    echo '<div class="d-flex flex-column">
                            <span class="placed-detail-line"><i class="fa-solid fa-building text-muted"></i>
                                <span class="text-muted fw-bold fs-8" data-toggle="tooltip" title="' . @$client_title_name . '">' . @$client_name . '</span>
                            </span>
                            <span class="placed-detail-line"><i class="fa-solid fa-briefcase text-muted"></i>
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . @$row->title . '">' . $job_title . '</a>
                            </span>' .
                            ($jobLocationLine ? '<span class="placed-detail-line"><i class="fa-solid fa-location-dot text-muted"></i>
                                <span class="text-muted fw-semibold fs-9">' . e($jobLocationLine) . '</span>
                            </span>' : '') . '
                            <span class="placed-detail-line mt-1"><i class="fa-solid ' . $recommendation_icon . ' ' . $recommendation_cls . '"></i>
                                <span class="fw-bold fs-9 text-muted">' . $recommendation_text . ' &middot; ' . @$row->jobId . '</span>
                            </span>
                          </div>';
                @endphp
            </td>
            {{-- Recruiter: read pre-indexed spokperson_name directly from ES document _source --}}
            <td>
                @php
                    $rowpostedby = !empty(@$row->spokperson_name) ? @$row->spokperson_name : @$row->posted_by;

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

                    // Build fee label from saved commercial fields
                    $ccServiceType = @$row->commercial_service_type;
                    $ccFeeMethod   = @$row->commercial_fee_method;
                    $ccFeeBase     = @$row->commercial_fee_base;
                    $ccFeePct      = @$row->commercial_fee_pct;
                    $ccFeeFixed    = @$row->commercial_fee_fixed;
                    $ccModel       = @$row->commercial_model;

                    if ($ccServiceType === 'free') {
                        $ccFeeLabel = 'Free Placement';
                    } elseif ($ccFeeMethod === 'percentage' && $ccFeePct) {
                        $baseLabel  = $ccFeeBase === 'offered' ? 'CTC' : 'Billing';
                        $ccFeeLabel = $ccFeePct . '% (' . $baseLabel . ')';
                    } elseif ($ccFeeMethod === 'fixed' && $ccFeeFixed) {
                        $ccFeeLabel = '₹' . number_format($ccFeeFixed, 0) . ' Fixed Fee';
                    } else {
                        $ccFeeLabel = '--';
                    }

                    $isContractType    = stripos($employment_type, 'contract') !== false || stripos($employment_type, 'hour') !== false;
                    $marginLabel       = $isContractType ? 'Gross Margin' : '(Placement Fee)';
                    $ccModelLabel      = $ccModel ? ucfirst($ccModel) : $employment_type;

                    echo '<div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6">' . $offerAmont . '</span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="text-dark fw-semibold fs-9" data-cc-fee>' . e($ccFeeLabel) . ' <span class="text-muted fw-normal">' . $marginLabel . '</span></span>
                            </span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-user-tie text-muted"></i>
                                <span class="text-muted fw-semibold fs-9" data-cc-model>' . e($ccModelLabel) . '</span>
                            </span>
                            <a href="javascript:void(0);" class="placed-change-link commercial-change-btn"
                               data-candidate-id="' . (int)@$row->id . '"
                               data-is-bench="' . (@$row->condidate_type == 'bench_condidate' ? 1 : 0) . '"
                               data-amount="' . (float)@$row->of_offer_amount . '"
                               data-employment-type="' . e(@$employment_type) . '"
                               data-service-type="' . e(@$row->commercial_service_type) . '"
                               data-commercial-model="' . e(@$row->commercial_model) . '"
                               data-fee-method="' . e(@$row->commercial_fee_method) . '"
                               data-fee-base="' . e(@$row->commercial_fee_base) . '"
                               data-fee-pct="' . (float)@$row->commercial_fee_pct . '"
                               data-fee-fixed="' . (float)@$row->commercial_fee_fixed . '"
                               data-billing-amount="' . (float)@$row->commercial_billing_amount . '"
                               data-billing-cycle="' . e(@$row->commercial_billing_cycle) . '"
                               data-margin-pct="' . (float)@$row->commercial_margin_pct . '"
                               data-replacement-period="' . e(@$row->commercial_replacement_period) . '"
                               data-internal-note="' . e(@$row->commercial_internal_note) . '"
                            ><i class="fa-solid fa-pen"></i>Change</a>
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
                    $joinedRawForAssign = @$row->of_joined_date;
                    $joinedIsoForAssign = (!empty($joinedRawForAssign) && $joinedRawForAssign != '0000-00-00 00:00:00')
                        ? date('Y-m-d', strtotime($joinedRawForAssign)) : '';

                    // Read saved assignment status from ES (falls back to 'working' if not yet saved)
                    $savedAssignStatus = @$row->assignment_status ?: 'working';
                    $adStatusMap = [
                        'working'   => ['label' => 'Currently Working', 'icon' => 'fa-circle-check',  'cls' => 'is-success'],
                        'completed' => ['label' => 'Project Completed',  'icon' => 'fa-check-double',   'cls' => 'is-info'   ],
                        'left'      => ['label' => 'Candidate Left',     'icon' => 'fa-circle-xmark',  'cls' => 'is-danger' ],
                    ];
                    $adStatus      = $adStatusMap[$savedAssignStatus] ?? $adStatusMap['working'];
                    $adEndDateRaw  = @$row->assignment_expected_end_date;
                    $adEndDateDisp = (!empty($adEndDateRaw) && $adEndDateRaw !== '0000-00-00')
                        ? date('d M Y', strtotime($adEndDateRaw)) : '--';

                    echo '<div class="d-flex flex-column">
                            <span class="placed-status-pill ' . $adStatus['cls'] . '" data-ad-status>
                                <i class="fa-solid ' . $adStatus['icon'] . ' me-1" data-ad-status-icon></i><span data-ad-status-label>' . $adStatus['label'] . '</span>
                            </span>
                            <span class="text-muted fw-semibold fs-9 mt-2">Expected End</span>
                            <span class="placed-commercial-line">
                                <i class="fa-solid fa-calendar-days text-muted"></i>
                                <span class="text-dark fw-semibold fs-9" data-ad-end-date>' . $adEndDateDisp . '</span>
                            </span>
                            <a href="javascript:void(0);" class="placed-change-link assignment-change-btn"
                               data-candidate-id="' . (int)@$row->id . '"
                               data-is-bench="' . (@$row->condidate_type == 'bench_condidate' ? 1 : 0) . '"
                               data-name="' . e(@$row->full_name) . '"
                               data-designation="' . e(@$row->current_designation) . '"
                               data-exp-loc="' . e(@$expLocLine) . '"
                               data-client="' . e(@$client_name) . '"
                               data-employment-type="' . e(@$employment_type) . '"
                               data-joined-date="' . $joinedIsoForAssign . '"
                               data-avatar-class="' . @$bgTextDecore . '"
                               data-avatar-letter="' . strtoupper(@$strVal) . '"
                               data-assignment-status="' . e(@$row->assignment_status) . '"
                               data-expected-end-date="' . e(@$row->assignment_expected_end_date) . '"
                               data-client-location="' . e(@$row->assignment_client_location) . '"
                               data-exit-reason="' . e(@$row->assignment_exit_reason) . '"
                               data-project-outcome="' . e(@$row->assignment_project_outcome) . '"
                               data-remarks="' . e(@$row->assignment_remarks) . '"
                               data-internal-notes="' . e(@$row->assignment_internal_notes) . '"
                               data-replacement-required="' . (int)@$row->assignment_replacement_required . '"
                               data-replacement-status="' . e(@$row->assignment_replacement_status) . '"
                               data-replacement-requested-on="' . e(@$row->assignment_replacement_requested_on) . '"
                               data-replacement-reason="' . e(@$row->assignment_replacement_reason) . '"
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
