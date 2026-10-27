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
            $city = $rowObj->city_name ?? '';
            $country_name = $rowObj->country_name ?? '';
            $departmentArr = $rowObj->ind_name ?? '';
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

        <div class="tbl-r is-pending-request" data-pending-id="{{ $invitationId }}">
            {{-- Col 1: Checkbox --}}
            <div class="tbl-cell-check">
                <input type="checkbox" class="tbl-cb" value="{{ $rowTargetId }}" onclick="event.stopPropagation()">
            </div>

            {{-- Col 2: Company Logo + Name + [⚡ Request] Pill + Industry/Size --}}
            <div class="tbl-cell-client show-detail-direct" data-row-id="{{ $rowTargetId }}" title="{{ $company_name }}">
                <div class="cl-company-wrapper">
                    <div class="cl-logo-box">
                        @if (!empty($background_image) && strlen($background_image) > 10)
                            <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                            <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                        @else
                            <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="cl-details-box">
                        <div class="d-flex align-items-center gap-2">
                            <span class="cl-name">{{ $company_name }}</span>
                            <span class="badge" style="background:#fef3c7; color:#d97706; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; border:1px solid #fde68a;">⚡ Request</span>
                        </div>
                        @if(!empty($departmentArr))
                            <div class="cl-ind" title="{{ $departmentArr }}">{{ $departmentArr }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Col 3: Location --}}
            <div class="tbl-cell-loc">
                @if(!empty($city))
                    <div class="cl-loc-city" title="{{ $city }}">{{ $city }}</div>
                @endif
                @if(!empty($country_name))
                    <div class="cl-loc-country">{{ ucwords($country_name) }}</div>
                @endif
                @if(empty($city) && empty($country_name))
                    <div class="cl-loc-empty">-</div>
                @endif
            </div>

            {{-- Col 4: Date (3 Lines: Received Date, Expiration SLA, Direct Client) --}}
            <div class="tbl-cell-type">
                <div class="cl-date-box">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span class="fw-bold text-gray-800">{{ $newDate }}</span>
                </div>
                <div class="cl-conn-caption text-danger" style="font-size:11px; font-weight:500;">Expires {{ $expiresFormatted }}</div>
                <div class="cl-type-name type-direct fw-bold" style="color:#2563eb; font-size:11px;">Direct Client</div>
            </div>

            {{-- Col 5: Last Activity (3 Lines: Received X ago, Active in system, Action Needed) --}}
            <div class="tbl-cell-activity">
                <div class="cl-act-main fw-bold text-gray-800">Received {{ $daysAgo }}</div>
                <div class="cl-act-desc text-muted">Active in system</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    <span class="bullet bullet-dot bg-warning animate-pulse" style="width:7px; height:7px;"></span>
                    <span class="text-warning fw-bold" style="font-size:11px;">Action Needed</span>
                </div>
            </div>

            {{-- Col 6: Jobs (Order: Hiring, Hold, Closed) --}}
            <div class="tbl-cell-jobs">
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

            {{-- Col 7: Action Hub (Accept / Decline / Terms) --}}
            <div class="tbl-cell-req-actions">
                <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="1" data-row-from_company="{{ $from_company_id }}" style="background:#10b981; color:#fff; border:none;" title="Accept Direct Client Partnership">Accept</button>
                <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="-1" data-row-from_company="{{ $from_company_id }}" style="background:#ef4444; color:#fff; border:none;" title="Decline Partnership Request">Decline</button>
                <button type="button" class="btn btn-sm btn-light fw-bold px-2 py-1.5 show-detail-direct" data-row-id="{{ $rowTargetId }}" style="background:#f1f5f9; color:#2563eb; border:1px solid #e2e8f0;" title="View Client Details & Terms">Terms</button>
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

        $departmentArr = (@$row->ind_name) ? @$row->ind_name : @$row->main_ind_name;
        $client_id = $row->client_id ?? ($row->id ?? 0);
        $rowTargetId = $isDirect ? ($row->client_id ?: ($row->from_company_id ?: ($row->company_id ?: ($row->id ?? 0)))) : ($row->client_id ?: ($row->id ?? 0));

        /* ── location ── */
        $country_name = $stateName = $city = '';
        if (!empty($row->client_location) && count($row->client_location) > 0) {
            $country_name = $row->client_location[0]['country_name'] ?? '';
            $stateName = $row->client_location[0]['state_name'] ?? '';
            $city = $row->client_location[0]['city_name'] ?? '';
        }

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

    <div class="tbl-r">
        {{-- Checkbox --}}
        <div class="tbl-cell-check">
            <input type="checkbox" class="tbl-cb" value="{{ $rowTargetId }}" onclick="event.stopPropagation()">
        </div>

        {{-- Client (with Company Logo / Initials Avatar) --}}
        <div class="tbl-cell-client {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" title="{{ ucwords(@$client_Or_CompanyName) }}">
            <div class="cl-company-wrapper">
                <div class="cl-logo-box">
                    @if (!empty($background_image) && strlen($background_image) > 10)
                        <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                        <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                    @else
                        <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                    @endif
                </div>
                <div class="cl-details-box">
                    <div class="cl-name">
                        {{ ucwords(@$client_Or_CompanyName) }}
                    </div>
                    @if(!empty($departmentArr))
                        <div class="cl-ind" title="{{ @$departmentArr }}">{{ @$departmentArr }}</div>
                    @endif
                    @if(!empty($companySizeText))
                        <div class="cl-size">{{ $companySizeText }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Location (NO ICON as requested) --}}
        <div class="tbl-cell-loc">
            @if(!empty($locMain))
                <div class="cl-loc-city" title="{{ $locMain }}">{{ $locMain }}</div>
            @endif
            @if(!empty($country_name))
                <div class="cl-loc-country">{{ ucwords($country_name) }}</div>
            @endif
            @if(empty($locMain) && empty($country_name))
                <div class="cl-loc-empty">-</div>
            @endif
        </div>

        {{-- Connected On & Type --}}
        <div class="tbl-cell-type">
            <div class="cl-date-box">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>{{ $newDate }}</span>
            </div>
            <div class="cl-conn-caption">Connected on</div>
            <div class="cl-type-name {{ $isDirect ? 'type-direct' : 'type-internal' }} {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}">
                {{ $client_type }}
            </div>
        </div>

        {{-- Last Activity --}}
        <div class="tbl-cell-activity">
            <div class="cl-act-main">{{ $daysAgo ? $daysAgo : 'No activity yet' }}</div>
            <div class="cl-act-desc">{{ $daysAgo ? 'Active in system' : 'Be the first to add a job or candidate.' }}</div>
            <div class="cl-act-status {{ $job_active > 0 ? 'status-active' : 'status-inactive' }}">
                <span class="cl-act-status-dot"></span>
                <span>{{ $job_active > 0 ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>

        {{-- Jobs (Order: Hiring, Hold, Closed with conditional status colors) --}}
        <div class="tbl-cell-jobs">
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

        {{-- Redesigned Hiring Pipeline Column: Sourcing, Screening, Evaluation, Negotiations, Offer, Joined + Application: and Interview --}}
        <div class="tbl-cell-pipeline">
            <div class="cl-pipeline-flow">
                <div class="cl-pipe-step" title="Sourcing: {{ $sourced }}">
                    <span class="cl-pipe-pill pill-sourcing">{{ $sourced }}</span>
                    <span class="cl-pipe-label">Sourcing</span>
                </div>
                <div class="cl-pipe-step" title="Screening: {{ $screening }}">
                    <span class="cl-pipe-pill pill-screening">{{ $screening }}</span>
                    <span class="cl-pipe-label">Screening</span>
                </div>
                <div class="cl-pipe-step" title="Evaluation: {{ $evaluated }}">
                    <span class="cl-pipe-pill pill-evaluation">{{ $evaluated }}</span>
                    <span class="cl-pipe-label">Evaluation</span>
                </div>
                <div class="cl-pipe-step" title="Negotiations: {{ $negotiations }}">
                    <span class="cl-pipe-pill pill-negotiations">{{ $negotiations }}</span>
                    <span class="cl-pipe-label">Negotiations</span>
                </div>
                <div class="cl-pipe-step" title="Offer: {{ $offers }}">
                    <span class="cl-pipe-pill pill-offer">{{ $offers }}</span>
                    <span class="cl-pipe-label">Offer</span>
                </div>
                <div class="cl-pipe-step" title="Joined: {{ $joined }}">
                    <span class="cl-pipe-pill pill-joined">{{ $joined }}</span>
                    <span class="cl-pipe-label">Joined</span>
                </div>
            </div>
            <div class="cl-pipeline-aggregates">
                <div class="cl-pipe-agg-pill agg-application" title="Total Applications: {{ $allCandidates }}">
                    <span class="cl-pipe-agg-label">Application:</span>
                    <span class="cl-pipe-agg-val">{{ $allCandidates }}</span>
                </div>
                <div class="cl-pipe-agg-pill agg-interview" title="Total Interviews: {{ $interviews }}">
                    <span class="cl-pipe-agg-label">Interview:</span>
                    <span class="cl-pipe-agg-val">{{ $interviews }}</span>
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
