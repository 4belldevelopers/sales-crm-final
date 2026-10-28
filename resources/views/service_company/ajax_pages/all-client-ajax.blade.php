@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $rowObj = (object) $rowArr;
        $isPending = !empty($rowObj->is_pending_request);
    @endphp

    @if($isPending)
        @php
            $company_name = ucwords($rowObj->company_name ?? 'Client');
            $rowTargetId = $rowObj->client_id ?: ($rowObj->from_company_id ?: ($rowObj->id ?? 0));
            $invitationId = $rowObj->staffing_company_invitation_id ?? ($rowObj->id ?? 0);
            $from_company_id = $rowObj->from_company_id ?? 0;
            $city = $rowObj->city_name ?? ($rowObj->city ?? '');
            $stateName = $rowObj->state_name ?? ($rowObj->state ?? '');
            $country_name = $rowObj->country_name ?? ($rowObj->country ?? '');
            $departmentArr = !empty($rowObj->ind_name) ? $rowObj->ind_name : 'Other / Diversified Services';
            $created_at = !empty($rowObj->from_created_at) ? $rowObj->from_created_at : ($rowObj->created_at ?? '');
            $newDate = !empty($created_at) ? date('d M, Y', strtotime($created_at)) : '-';
            $daysAgo = !empty($created_at) ? GeneralMethod::timeAgo($created_at) : 'Just now';

            $expiresAt = !empty($rowObj->expires_at) ? $rowObj->expires_at : (!empty($created_at) ? date('Y-m-d H:i:s', strtotime($created_at . ' +7 days')) : null);
            $expiresFormatted = $expiresAt ? date('d M, Y', strtotime($expiresAt)) : 'in 7 days';

            $requisitionCount = (int) ($rowObj->requisition_count ?? 0);

            /* ── logo / avatar calculation ── */
            $background_image = '';
            if (!empty($rowObj->logo) && strlen($rowObj->logo) > 10) {
                $background_image = $rowObj->logo;
            } elseif (!empty($rowObj->website)) {
                $domainGet = GeneralMethod::getDomain($rowObj->website);
                if ($domainGet) {
                    $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
                }
            }
            if (!empty($background_image) && !str_starts_with($background_image, 'http') && !str_starts_with($background_image, '//')) {
                $background_image = asset($background_image);
            }

            $cleanName = trim($company_name ?: 'Client');
            $strVal = strtolower($cleanName[0] ?? 'c');
            $ascii = ord($strVal);
            if ($ascii >= 97 && $ascii <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($ascii >= 102 && $ascii <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($ascii >= 107 && $ascii <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($ascii >= 112 && $ascii <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($ascii >= 117 && $ascii <= 121) {
                $bgTextDecore = 'bg-light-info text-info';
            } else {
                $bgTextDecore = 'bg-light-primary text-primary';
            }

            $nameParts = array_values(array_filter(explode(' ', $cleanName)));
            $initials = '';
            if (count($nameParts) >= 2) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
            } elseif (count($nameParts) === 1) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
            } else {
                $initials = 'CL';
            }
        @endphp

        <div class="cl-primary-card tbl-r is-pending-request" data-pending-id="{{ $invitationId }}" data-row-id="{{ $rowTargetId }}" data-client-type="request">
            {{-- Upper 4 Discrete Sub-Cards Grid --}}
            <div class="cl-subcards-grid">
                {{-- SUB-CARD 1: Client Identity --}}
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Client Identity</span>
                        <div class="cl-subcard-badges">
                            <span class="cl-scope-pill pill-direct">Direct Client</span>
                            <span class="badge" style="background:#fef3c7; color:#d97706; font-size:9px; font-weight:700; padding:1.5px 6px; border-radius:4px; border:1px solid #fde68a;">⚡ Request</span>
                        </div>
                    </div>
                    <div class="cl-identity-body">
                        <div class="cl-logo-box">
                            @if (!empty($background_image) && strlen($background_image) > 10)
                                <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                            @else
                                <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="cl-details-box">
                            <div class="cl-name view-request-terms" data-row-id="{{ $rowTargetId }}" data-client-type="request" title="{{ $company_name }}">{{ $company_name }}</div>
                            @if(!empty($departmentArr))
                                <div class="cl-ind" title="{{ $departmentArr }}">{{ $departmentArr }}</div>
                            @else
                                <div class="cl-ind text-muted">—</div>
                            @endif
                            <div class="cl-size text-muted">—</div>
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 2: Location --}}
                <div class="cl-subcard cl-subcard-loc">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Location</span>
                    </div>
                    <div class="cl-loc-body">
                        <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        @php
                            $reqCity = !empty($city) ? ucwords($city) : '';
                            $reqState = !empty($stateName) ? ucwords($stateName) : '';
                            $reqCountry = !empty($country_name) ? ucwords($country_name) : '';
                        @endphp
                        <div class="cl-loc-lines">
                            @if(!empty($reqCity))
                                <div class="cl-loc-city" title="{{ $reqCity }}">{{ $reqCity }}</div>
                            @endif
                            @if(!empty($reqState))
                                <div class="cl-loc-state" title="{{ $reqState }}">{{ $reqState }}</div>
                            @endif
                            @if(!empty($reqCountry))
                                <div class="cl-loc-country" title="{{ $reqCountry }}">{{ $reqCountry }}</div>
                            @endif
                            @if(empty($reqCity) && empty($reqState) && empty($reqCountry))
                                <div class="cl-loc-empty">—</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 3: Jobs Status --}}
                <div class="cl-subcard cl-subcard-jobs">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Jobs Status</span>
                    </div>
                    <div class="cl-jobs-body tbl-cell-jobs">
                        <div class="cl-job-line {{ $requisitionCount > 0 ? 'has-count cl-job-hiring' : 'no-count' }}">
                            <span class="cl-job-bullet {{ $requisitionCount > 0 ? 'bullet-hiring' : 'bullet-neutral' }}"></span>
                            <span class="cl-job-num">{{ $requisitionCount }}</span>
                            <span class="cl-job-label">Hiring</span>
                        </div>
                        <div class="cl-job-line no-count">
                            <span class="cl-job-bullet bullet-neutral"></span>
                            <span class="cl-job-num">0</span>
                            <span class="cl-job-label">Hold</span>
                        </div>
                        <div class="cl-job-line no-count">
                            <span class="cl-job-bullet bullet-neutral"></span>
                            <span class="cl-job-num">0</span>
                            <span class="cl-job-label">Closed</span>
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 4: Request SLA & Activity --}}
                <div class="cl-subcard cl-subcard-pipeline">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Request Status & SLA</span>
                    </div>
                    <div class="cl-pipe-body d-flex flex-column gap-1 align-items-start" style="padding-top:2px;">
                        <div class="d-flex align-items-center gap-1">
                            <span class="bullet bullet-dot bg-warning animate-pulse" style="width:7px; height:7px;"></span>
                            <span class="text-warning fw-bold" style="font-size:11px;">Action Needed</span>
                        </div>
                        <div class="text-danger fw-semibold" style="font-size:10.5px;">Expires {{ $expiresFormatted }}</div>
                        <div class="text-muted" style="font-size:10.5px;">Active in system</div>
                    </div>
                </div>
            </div>

            {{-- BOTTOM STRIP: Received date, Last Activity, and Accept/Decline action buttons --}}
            <div class="cl-card-bottom-strip">
                <div class="cl-bottom-meta">
                    <span class="cl-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>Received: <strong>{{ $newDate }}</strong></span>
                    </span>
                    <span class="cl-meta-dot">•</span>
                    <span class="cl-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Activity: <strong>{{ $daysAgo }}</strong></span>
                    </span>
                </div>
                <div class="cl-bottom-actions tbl-cell-req-actions">
                    <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="1" data-row-from_company="{{ $from_company_id }}" style="background:#10b981; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Accept Direct Client Partnership">Accept</button>
                    <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="-1" data-row-from_company="{{ $from_company_id }}" style="background:#ef4444; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Decline Partnership Request">Decline</button>
                    <button type="button" class="btn btn-sm btn-light fw-bold px-2 py-1.5 view-request-terms" data-row-id="{{ $rowTargetId }}" data-client-type="request" style="background:#f1f5f9; color:#2563eb; border:1px solid #e2e8f0; border-radius:6px; font-size:11px;" title="View Client Details & Terms">Terms</button>
                </div>
            </div>
        </div>
    @else
    @php
        $row = (object) $rowArr;
        if (isset($relationMap) && isset($relationMap[$row->company_id])) {
            $relationget = $relationMap[$row->company_id];
        } else {
            $relationget = GeneralMethod::getRelationData($company_id, $row->company_id);
        }

        $from_company_id = $relationget->from_company_id ?? null;
        $recommendation_id = $relationget->id ?? null;
        $to_company_id = $relationget->to_company_id ?? null;
        $final_status = $relationget->final_status ?? null;
        $from_created_at = $relationget->from_created_at ?? null;

        $row->final_status = $final_status;
        $row->staffing_company_relation_id = $recommendation_id;
        $row->to_company_id = $to_company_id;
        $row->from_company_id = $from_company_id;
        $row->from_created_at = $from_created_at;

        /* ── name / type ── */
        $client_type_css = 'show-detail';
        $isDirect = false;
        if ($company_id != $row->company_id && $row->staffing_company_relation_id > 0) {
            $client_type_css = 'show-detail-direct';
            $isDirect = true;
        }

        $client_Or_CompanyName = $row->name ?? '';
        if ($company_id != $row->company_id) {
            $client_Or_CompanyName = $row->company_name ?? $row->name ?? '';
        }

        $departmentArr = (!empty($row->ind_name)) ? $row->ind_name : ((!empty($row->main_ind_name)) ? $row->main_ind_name : 'Other / Diversified Services');
        $client_id = $row->client_id ?? ($row->id ?? 0);
        $rowTargetId = $isDirect ? ($row->client_id ?: ($row->from_company_id ?: ($row->company_id ?: ($row->id ?? 0)))) : ($row->client_id ?: ($row->id ?? 0));

        /* ── location ── */
        $country_name = $stateName = $city = '';
        if (!empty($row->client_location) && count($row->client_location) > 0) {
            $country_name = $row->client_location[0]['country_name'] ?? '';
            $stateName = $row->client_location[0]['state_name'] ?? '';
            $city = $row->client_location[0]['city_name'] ?? '';
        }
        if (empty($city) && !empty($row->city_name)) $city = $row->city_name;
        if (empty($city) && !empty($row->city)) $city = $row->city;
        if (empty($stateName) && !empty($row->state_name)) $stateName = $row->state_name;
        if (empty($stateName) && !empty($row->state)) $stateName = $row->state;
        if (empty($country_name) && !empty($row->country_name)) $country_name = $row->country_name;
        if (empty($country_name) && !empty($row->country)) $country_name = $row->country;

        /* ── company size ── */
        $companySizeText = '';
        $fromSize = $row->company_size_from ?? null;
        $toSize = $row->company_sizes ?? $row->company_size ?? null;
        if (!empty($fromSize) && !empty($toSize)) {
            $companySizeText = number_format((int)$fromSize) . ' - ' . number_format((int)$toSize) . ' employees';
        } elseif (!empty($toSize)) {
            $companySizeText = is_numeric($toSize) ? number_format((int)$toSize) . ' employees' : $toSize . ' employees';
        }

        /* ── jobs (active / closed / hold) ── */
        if ($company_id == ($row->clients_company_id ?? 0)) {
            $jKey = 'internal_' . $client_id;
            $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::InternalClientJobsCountAllStatus($company_id, $client_id);
        } else {
            $jKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
            $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::DirectClientJobsCountAllStatus($from_company_id, $to_company_id);
        }
        $job_active   = (int)($jobsCount->job_active ?? 0);
        $job_closed   = (int)($jobsCount->job_inactive ?? 0);
        $job_hold     = (int)($jobsCount->job_hold ?? 0);

        /* ── performance funnel ── */
        if ($company_id != $row->company_id) {
            $pKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
            $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::DirectClientCandidatesCountList($from_company_id, $to_company_id);
        } else {
            $pKey = 'internal_' . $client_id;
            $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($client_id);
        }
        $allCandidates = (int)($perf->all_candidates ?? 0);
        $sourced       = (int)($perf->sourced ?? 0);
        $screening     = (int)($perf->screening ?? 0);
        $evaluated     = (int)($perf->evaluated ?? 0);
        $negotiations  = (int)($perf->negotiations ?? ($perf->negotiation ?? 0));
        $offers        = (int)($perf->offers ?? 0);
        $joined        = (int)($perf->Joined ?? ($perf->joined ?? 0));
        $rejected      = (int)($perf->rejected ?? 0);
        $interviews    = (int)($perf->all_interview ?? 0);

        /* ── connected on ── */
        if ($row->staffing_company_relation_id > 0 && ($row->to_company_id ?? 0) == auth()->user()->company_id) {
            $createdTimestamp = $row->from_created_at ?? $row->created_at ?? null;
        } else {
            $createdTimestamp = $row->created_at ?? null;
        }
        $newDate = $createdTimestamp ? date('d M, Y', strtotime($createdTimestamp)) : '-';
        $daysAgo = $createdTimestamp ? GeneralMethod::timeAgo($createdTimestamp) : '';

        /* ── client type tag ── */
        $client_type = __('messages.internal_client_tag');
        if ($company_id != $row->company_id) {
            $client_type = __('messages.direct_clinet_tag');
        }

        $locMain = '';
        if ($city && $stateName) {
            $locMain = ucwords($city) . ', ' . ucwords($stateName);
        } elseif ($city) {
            $locMain = ucwords($city);
        } elseif ($stateName) {
            $locMain = ucwords($stateName);
        }

        /* ── logo / avatar calculation ── */
        $background_image = '';
        if ($company_id != $row->company_id) {
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $background_image = @$row->logo;
            } elseif (!empty(@$row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                if ($domainGet) {
                    $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
                }
            }
        } else {
            // Internal client: strictly own uploaded logo or website favicon (never parent agency logo)
            if (!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path) > 10) {
                $background_image = @$row->logo_logical_path;
            } elseif (!empty(@$row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                if ($domainGet) {
                    $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
                }
            }
        }

        if (!empty($background_image) && !str_starts_with($background_image, 'http') && !str_starts_with($background_image, '//')) {
            $background_image = asset($background_image);
        }

        // initials & background color
        $cleanName = trim(@$client_Or_CompanyName ?: 'Client');
        $strVal = strtolower($cleanName[0] ?? 'c');
        $ascii = ord($strVal);
        if ($ascii >= 97 && $ascii <= 101) {
            $bgTextDecore = 'bg-light-danger text-danger';
        } elseif ($ascii >= 102 && $ascii <= 106) {
            $bgTextDecore = 'bg-light-warning text-warning';
        } elseif ($ascii >= 107 && $ascii <= 111) {
            $bgTextDecore = 'bg-light-primary text-primary';
        } elseif ($ascii >= 112 && $ascii <= 116) {
            $bgTextDecore = 'bg-light-success text-success';
        } elseif ($ascii >= 117 && $ascii <= 121) {
            $bgTextDecore = 'bg-light-info text-info';
        } else {
            $bgTextDecore = 'bg-light-primary text-primary';
        }

        $nameParts = array_values(array_filter(explode(' ', $cleanName)));
        $initials = '';
        if (count($nameParts) >= 2) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
        } elseif (count($nameParts) === 1) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
        } else {
            $initials = 'CL';
        }
    @endphp

    <div class="cl-primary-card tbl-r {{ $client_type_css }} {{ $job_active > 0 ? 'is-active-client' : 'is-inactive-client' }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}">
        {{-- Upper 4 Discrete Sub-Cards Grid --}}
        <div class="cl-subcards-grid">
            {{-- SUB-CARD 1: Client Identity (Internal & Active at TOP) --}}
            <div class="cl-subcard cl-subcard-identity">
                <div class="cl-subcard-head">
                    <span class="cl-subcard-title">Client Identity</span>
                    <div class="cl-subcard-badges">
                        <span class="cl-scope-pill {{ $isDirect ? 'pill-direct' : 'pill-internal' }} {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}">
                            {{ $client_type }}
                        </span>
                        <span class="cl-status-pill {{ $job_active > 0 ? 'pill-active' : 'pill-inactive' }}">
                            <span class="cl-status-pill-dot"></span>
                            <span>{{ $job_active > 0 ? 'Active' : 'Inactive' }}</span>
                        </span>
                    </div>
                </div>
                <div class="cl-identity-body">
                    <div class="cl-logo-box">
                        @if (!empty($background_image) && strlen($background_image) > 10)
                            <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                            <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                        @else
                            <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="cl-details-box">
                        <div class="cl-name {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}" title="{{ ucwords(@$client_Or_CompanyName) }}">
                            {{ ucwords(@$client_Or_CompanyName) }}
                        </div>
                        @if(!empty($departmentArr))
                            <div class="cl-ind" title="{{ @$departmentArr }}">{{ @$departmentArr }}</div>
                        @else
                            <div class="cl-ind text-muted">—</div>
                        @endif
                        @if(!empty($companySizeText))
                            <div class="cl-size">{{ $companySizeText }}</div>
                        @else
                            <div class="cl-size text-muted">—</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SUB-CARD 2: Location --}}
            <div class="cl-subcard cl-subcard-loc">
                <div class="cl-subcard-head">
                    <span class="cl-subcard-title">Location</span>
                </div>
                <div class="cl-loc-body">
                    <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    @php
                        $locCity = !empty($city) ? ucwords($city) : '';
                        $locState = !empty($stateName) ? ucwords($stateName) : '';
                        $locCountry = !empty($country_name) ? ucwords($country_name) : '';
                    @endphp
                    <div class="cl-loc-lines">
                        @if(!empty($locCity))
                            <div class="cl-loc-city" title="{{ $locCity }}">{{ $locCity }}</div>
                        @endif
                        @if(!empty($locState))
                            <div class="cl-loc-state" title="{{ $locState }}">{{ $locState }}</div>
                        @endif
                        @if(!empty($locCountry))
                            <div class="cl-loc-country" title="{{ $locCountry }}">{{ $locCountry }}</div>
                        @endif
                        @if(empty($locCity) && empty($locState) && empty($locCountry))
                            <div class="cl-loc-empty">—</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SUB-CARD 3: Jobs Status --}}
            <div class="cl-subcard cl-subcard-jobs">
                <div class="cl-subcard-head">
                    <span class="cl-subcard-title">Jobs Status</span>
                </div>
                <div class="cl-jobs-body tbl-cell-jobs">
                    <div class="cl-job-line {{ $job_active > 0 ? 'has-count cl-job-hiring' : 'no-count' }}">
                        <span class="cl-job-bullet {{ $job_active > 0 ? 'bullet-hiring' : 'bullet-neutral' }}"></span>
                        <span class="cl-job-num">{{ $job_active }}</span>
                        <span class="cl-job-label">Hiring</span>
                    </div>
                    <div class="cl-job-line {{ $job_hold > 0 ? 'has-count cl-job-hold' : 'no-count' }}">
                        <span class="cl-job-bullet {{ $job_hold > 0 ? 'bullet-hold' : 'bullet-neutral' }}"></span>
                        <span class="cl-job-num">{{ $job_hold }}</span>
                        <span class="cl-job-label">Hold</span>
                    </div>
                    <div class="cl-job-line {{ $job_closed > 0 ? 'has-count cl-job-closed' : 'no-count' }}">
                        <span class="cl-job-bullet {{ $job_closed > 0 ? 'bullet-closed' : 'bullet-neutral' }}"></span>
                        <span class="cl-job-num">{{ $job_closed }}</span>
                        <span class="cl-job-label">Closed</span>
                    </div>
                </div>
            </div>

            {{-- SUB-CARD 4: Hiring Pipeline (Restored Vibrant Color Combinations Even If 0) --}}
            <div class="cl-subcard cl-subcard-pipeline">
                <div class="cl-subcard-head">
                    <div class="cl-subcard-title-wrap">
                        <svg class="cl-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                        </svg>
                        <span class="cl-subcard-title">Hiring Pipeline</span>
                    </div>
                </div>
                <div class="cl-pipe-body tbl-cell-pipeline" title="Total Applications: {{ $allCandidates }} | Rejected: {{ $rejected }} | Interviews: {{ $interviews }}">
                    <div class="cl-pipeline-flow">
                        <div class="cl-pipe-step step-sourcing {{ $sourced > 0 ? 'has-count' : '' }}" title="Sourcing: {{ $sourced }}">
                            <span class="cl-pipe-pill pill-sourcing">{{ $sourced }}</span>
                            <span class="cl-pipe-label">Sourcing</span>
                        </div>
                        <div class="cl-pipe-step step-screening {{ $screening > 0 ? 'has-count' : '' }}" title="Screening: {{ $screening }}">
                            <span class="cl-pipe-pill pill-screening">{{ $screening }}</span>
                            <span class="cl-pipe-label">Screening</span>
                        </div>
                        <div class="cl-pipe-step step-evaluation {{ $evaluated > 0 ? 'has-count' : '' }}" title="Evaluation: {{ $evaluated }}">
                            <span class="cl-pipe-pill pill-evaluation">{{ $evaluated }}</span>
                            <span class="cl-pipe-label">Evaluation</span>
                        </div>
                        <div class="cl-pipe-step step-negotiations {{ $negotiations > 0 ? 'has-count' : '' }}" title="Negotiation: {{ $negotiations }}">
                            <span class="cl-pipe-pill pill-negotiations">{{ $negotiations }}</span>
                            <span class="cl-pipe-label">Negotiation</span>
                        </div>
                        <div class="cl-pipe-step step-offer {{ $offers > 0 ? 'has-count' : '' }}" title="Offer: {{ $offers }}">
                            <span class="cl-pipe-pill pill-offer">{{ $offers }}</span>
                            <span class="cl-pipe-label">Offer</span>
                        </div>
                        <div class="cl-pipe-step step-joined {{ $joined > 0 ? 'has-count' : '' }}" title="Joined: {{ $joined }}">
                            <span class="cl-pipe-pill pill-joined">{{ $joined }}</span>
                            <span class="cl-pipe-label">Joined</span>
                        </div>
                        <div class="cl-pipe-step step-rejected {{ $rejected > 0 ? 'has-count' : '' }}" title="Rejected: {{ $rejected }}">
                            <span class="cl-pipe-pill pill-rejected">{{ $rejected }}</span>
                            <span class="cl-pipe-label">Rejected</span>
                        </div>
                        <div class="cl-pipe-divider" aria-hidden="true"></div>
                        <div class="cl-pipe-step step-interview {{ $interviews > 0 ? 'has-count' : '' }}" title="Interview: {{ $interviews }}">
                            <span class="cl-pipe-pill pill-interview">{{ $interviews }}</span>
                            <span class="cl-pipe-label">Interview</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM STRIP: Connected Date, Last Activity, Applications Badge & Ghost Actions --}}
        <div class="cl-card-bottom-strip">
            <div class="cl-bottom-meta">
                <span class="cl-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>Connected: <strong>{{ $newDate }}</strong></span>
                </span>
                <span class="cl-meta-dot">•</span>
                <span class="cl-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Last Activity: <strong>{{ $daysAgo ? $daysAgo : 'No activity yet' }}</strong></span>
                </span>
                <span class="cl-meta-dot">•</span>
                <span class="cl-meta-item cl-meta-apps">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Applications:</span>
                    <span class="cl-apps-badge {{ $allCandidates > 0 ? 'has-apps' : '' }}">{{ $allCandidates }}</span>
                </span>
            </div>
            <div class="cl-bottom-actions tbl-cell-actions">
                <button type="button" class="cl-btn-act {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}" title="Quick View">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
                @if(in_array("edit_clients", $userPermissions ?? []))
                    <a href="{{ url('service_company/client/edit/'.$rowTargetId) }}" class="cl-btn-act" title="Edit Client">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </a>
                @endif
                <div class="dropdown">
                    <button type="button" class="cl-btn-act" data-bs-toggle="dropdown" aria-expanded="false" title="More options">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="1"/>
                            <circle cx="12" cy="5" r="1"/>
                            <circle cx="12" cy="19" r="1"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:8px; border:1px solid #e2e8f0; font-size:12px; min-width:140px; padding:4px 0;">
                        <li>
                            <a class="dropdown-item py-1.5 px-3 text-gray-700 {{ $client_type_css }}" href="javascript:void(0);" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}">
                                <i class="fas fa-eye text-primary me-2"></i> View Details
                            </a>
                        </li>
                        @if(in_array("edit_clients", $userPermissions ?? []))
                        <li>
                            <a class="dropdown-item py-1.5 px-3 text-gray-700" href="{{ url('service_company/client/edit/'.$rowTargetId) }}">
                                <i class="fas fa-edit text-info me-2"></i> Edit Client
                            </a>
                        </li>
                        @endif
                        @if(in_array("delete_clients", $userPermissions ?? []))
                        <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                        <li>
                            <a class="dropdown-item py-1.5 px-3 text-danger delete-client-btn" href="javascript:void(0);" data-row-id="{{ $rowTargetId }}">
                                <i class="fas fa-trash-alt text-danger me-2"></i> Delete
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
@else
    <div class="cl-empty-state">
        <div class="cl-empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <div class="cl-empty-title">No clients found</div>
        <div class="cl-empty-desc">No clients match your selected filters or search query.</div>
    </div>
@endif
