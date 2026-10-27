@include('service_company.client.components.tab-client-style')

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
    <div class="cd-client-stream">
    @foreach ($data as $rowArr)
    @php
        $rowObj = (object) $rowArr;
        $isPending = !empty($rowObj->is_pending_request);
    @endphp

    @if($isPending)
        @php
            $company_name = ucwords($rowObj->company_name ?? 'Client');
            $rowTargetId = ($rowObj->client_id ?? null) ?: (($rowObj->from_company_id ?? null) ?: ($rowObj->id ?? 0));
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

        <!-- Master Primary Card: Pending Direct Client Request -->
        <div class="cd-client-card tbl-r is-pending-request" data-row-id="{{ $rowTargetId }}" data-pending-id="{{ $invitationId }}">
            <div class="cd-client-card-grid">
                {{-- Sub-Card 1: Client Profile & Request Status --}}
                <div class="cd-client-subcard cd-subcard-client-details">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Client Profile</span>
                        <div class="cd-header-badges">
                            <span class="cd-status-badge status-request"><i class="fas fa-bolt text-warning me-1"></i>Request</span>
                            <span class="cd-badge-client-type type-direct">Direct Client</span>
                        </div>
                    </div>
                    <div class="cd-details-body">
                        <input type="checkbox" class="tbl-cb me-1" value="{{ $rowTargetId }}" onclick="event.stopPropagation()" style="margin-top: 10px;">
                        <div class="cd-logo-box">
                            @if (!empty($background_image) && strlen($background_image) > 10)
                                <img src="{{ $background_image }}" alt="{{ $initials }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <span class="{{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                            @else
                                <span class="{{ $bgTextDecore }}">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="cd-details-text">
                            <a href="javascript:void(0);" class="cd-client-title show-detail-direct" data-row-id="{{ $rowTargetId }}" title="{{ $company_name }}">
                                {{ $company_name }}
                            </a>
                            <div class="cd-client-meta" title="{{ $departmentArr ?: 'Partnership Invitation' }}">
                                <i class="fas fa-briefcase text-muted me-1"></i>{{ $departmentArr ?: 'Direct Client Partner' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Location & HQ --}}
                <div class="cd-client-subcard cd-subcard-client-loc">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Location &amp; HQ</span>
                    </div>
                    <div>
                        <div class="cd-val-loc">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            @if(!empty($city) && !empty($country_name))
                                {{ $city }}, {{ ucwords($country_name) }}
                            @elseif(!empty($city))
                                {{ $city }}
                            @elseif(!empty($country_name))
                                {{ ucwords($country_name) }}
                            @else
                                Headquarters Not Specified
                            @endif
                        </div>
                        <div class="cd-val-sub">
                            <i class="far fa-calendar-alt text-muted me-1"></i>Received: {{ $newDate }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 3: Job Requisitions Available --}}
                <div class="cd-client-subcard cd-subcard-client-jobs">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Requisitions</span>
                    </div>
                    <div>
                        <div class="cd-val-jobs-main text-primary">
                            <i class="fas fa-briefcase me-1"></i>{{ $requisitionCount }} Positions
                        </div>
                        <div class="cd-jobs-breakdown">
                            <span>Ready to staff upon acceptance</span>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Governance & SLA --}}
                <div class="cd-client-subcard cd-subcard-client-gov">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Terms &amp; SLA</span>
                    </div>
                    <div>
                        <div class="cd-team-lead text-danger fw-bold" style="font-size: 11px;">
                            <i class="far fa-clock me-1"></i>Expires {{ $expiresFormatted }}
                        </div>
                        <div class="cd-team-meta text-warning fw-semibold">
                            <span class="bullet bullet-dot bg-warning me-1"></span>Action Required
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Action Hub --}}
                <div class="cd-client-subcard cd-subcard-client-pipeline" style="justify-content: center;">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Partnership Decision</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="1" data-row-from_company="{{ $from_company_id }}" style="background:#10b981; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Accept Direct Client Partnership">
                            <i class="fas fa-check text-white me-1"></i>Accept
                        </button>
                        <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="{{ $invitationId }}" data-row-event="-1" data-row-from_company="{{ $from_company_id }}" style="background:#ef4444; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Decline Partnership Request">
                            <i class="fas fa-times text-white me-1"></i>Decline
                        </button>
                        <button type="button" class="btn btn-sm btn-light fw-bold px-2 py-1.5 show-detail-direct" data-row-id="{{ $rowTargetId }}" style="background:#f1f5f9; color:#2563eb; border:1px solid #e2e8f0; border-radius:6px; font-size:11px;" title="View Client Details & Terms">
                            Terms
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sub-Card 6: Bottom Activity Strip --}}
            <div class="cd-client-card-bottom">
                <div class="cd-bottom-left">
                    <span><i class="far fa-clock text-muted me-1"></i>Received <strong>{{ $daysAgo }}</strong></span>
                    <span>&bull;</span>
                    <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle text-warning me-1"></i>Direct client partnership invitation awaiting decision</span>
                </div>
                <div class="cd-bottom-actions">
                    <button type="button" class="cd-btn-quick-drawer show-detail-direct" data-row-id="{{ $rowTargetId }}">
                        <i class="fas fa-file-contract me-1"></i>Review Agreement
                    </button>
                </div>
            </div>
        </div>

    @else
        @php
            $row = (object) $rowArr;
            $rowCompId = $row->company_id ?? 0;
            if (isset($relationMap) && isset($relationMap[$rowCompId])) {
                $relationget = $relationMap[$rowCompId];
            } else {
                $relationget = GeneralMethod::getRelationData($company_id, $rowCompId);
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
            $rowTargetId = $isDirect ? (($row->client_id ?? null) ?: (($row->from_company_id ?? null) ?: (($row->company_id ?? null) ?: ($row->id ?? 0)))) : (($row->client_id ?? null) ?: ($row->id ?? 0));

            /* ── location ── */
            $country_name = $stateName = $city = '';
            $locationCount = !empty($row->client_location) ? count($row->client_location) : 0;
            if ($locationCount > 0) {
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
                $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($client_id, $company_id);
            }

            $allCandidates = (int)($perf->all_candidates ?? 0);
            $sourced       = (int)($perf->sourced ?? 0);
            $screening     = (int)($perf->screening ?? 0);
            $evaluated     = (int)($perf->evaluated ?? 0);
            $negotiation   = (int)($perf->negotiation ?? 0);
            $interviews    = (int)($perf->all_interview ?? 0);
            $offers        = (int)($perf->offers ?? 0);
            $joined        = (int)($perf->Joined ?? ($perf->joined ?? 0));

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

        <!-- Master Primary Client Card -->
        <div class="cd-client-card tbl-r" data-row-id="{{ $rowTargetId }}">
            <!-- Upper Sub-Cards Grid (5 Sections) -->
            <div class="cd-client-card-grid">

                {{-- Sub-Card 1: Client Profile (Logo, Name, Industry, Size, Badges) --}}
                <div class="cd-client-subcard cd-subcard-client-details">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Client Profile</span>
                        <div class="cd-header-badges">
                            <span class="cd-badge-client-type {{ $isDirect ? 'type-direct' : 'type-internal' }}">
                                <i class="fas {{ $isDirect ? 'fa-handshake' : 'fa-building' }} me-1"></i>{{ $client_type }}
                            </span>
                            <span class="cd-status-badge {{ $job_active > 0 ? 'status-hiring' : 'status-inactive' }}">
                                {{ $job_active > 0 ? 'Hiring' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="cd-details-body">
                        <input type="checkbox" class="tbl-cb me-1" value="{{ $rowTargetId }}" onclick="event.stopPropagation()" style="margin-top: 10px;">
                        <div class="cd-logo-box">
                            @if (!empty($background_image) && strlen($background_image) > 10)
                                <img src="{{ $background_image }}" alt="{{ $initials }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <span class="{{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                            @else
                                <span class="{{ $bgTextDecore }}">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="cd-details-text">
                            <a href="javascript:void(0);" class="cd-client-title {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" title="{{ ucwords(@$client_Or_CompanyName) }}">
                                {{ ucwords(@$client_Or_CompanyName) }}
                            </a>
                            <div class="cd-client-meta" title="{{ @$departmentArr ?: 'General Industry' }}">
                                <i class="fas fa-briefcase text-muted me-1"></i>{{ @$departmentArr ?: 'General' }}
                                @if(!empty($companySizeText))
                                    <span class="mx-1">&bull;</span>
                                    <span>👥 {{ $companySizeText }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Location & HQ Hub --}}
                <div class="cd-client-subcard cd-subcard-client-loc">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Location &amp; HQ</span>
                        @if($locationCount > 1)
                            <span class="cd-loc-badge" title="{{ $locationCount }} client locations">+{{ $locationCount - 1 }} Hubs</span>
                        @endif
                    </div>
                    <div>
                        <div class="cd-val-loc" title="{{ $locMain ? $locMain . ($country_name ? ', ' . ucwords($country_name) : '') : 'Location Not Set' }}">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            @if(!empty($locMain))
                                {{ $locMain }}
                            @elseif(!empty($country_name))
                                {{ ucwords($country_name) }}
                            @else
                                Location Not Set
                            @endif
                        </div>
                        <div class="cd-val-sub">
                            <i class="far fa-calendar-check text-muted me-1"></i>Connected: {{ $newDate }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 3: Job Requisitions Hub --}}
                <div class="cd-client-subcard cd-subcard-client-jobs">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Job Requisitions</span>
                    </div>
                    <div>
                        <div class="cd-val-jobs-main {{ $job_active > 0 ? 'text-success' : 'text-muted' }}">
                            <span class="bullet bullet-dot {{ $job_active > 0 ? 'bg-success' : 'bg-secondary' }} me-1"></span>
                            {{ $job_active > 0 ? $job_active . ' Active Roles' : '0 Active Roles' }}
                        </div>
                        <div class="cd-jobs-breakdown">
                            <span title="Hiring"><span class="cd-jobs-pill-count hiring">{{ $job_active }}</span> Hiring</span>
                            <span>&bull;</span>
                            <span title="On Hold"><span class="cd-jobs-pill-count hold">{{ $job_hold }}</span> Hold</span>
                            <span>&bull;</span>
                            <span title="Closed"><span class="cd-jobs-pill-count closed">{{ $job_closed }}</span> Closed</span>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Team & Engagement --}}
                <div class="cd-client-subcard cd-subcard-client-gov">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Governance</span>
                    </div>
                    <div>
                        <div class="cd-team-lead">
                            <i class="fas fa-user-tie text-primary me-1"></i>{{ $isDirect ? 'Direct Partnership' : 'Internal Account' }}
                        </div>
                        <div class="cd-team-meta">
                            <i class="fas fa-shield-alt text-muted me-1"></i>Standard SLA Active
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Talent Pipeline Funnel --}}
                <div class="cd-client-subcard cd-subcard-client-pipeline">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Talent Pipeline</span>
                    </div>
                    <div>
                        {{-- Row 1 (Top): 6 Canonical Recruitment Stages Track --}}
                        <div class="cd-pipeline-track">
                            <div class="cd-track-stage" title="Sourcing: {{ $sourced }}">
                                <span class="cd-track-badge s-sou">{{ $sourced }}</span>
                                <span class="cd-track-label">Sourcing</span>
                            </div>
                            <div class="cd-track-stage" title="Screening: {{ $screening }}">
                                <span class="cd-track-badge s-scr">{{ $screening }}</span>
                                <span class="cd-track-label">Screening</span>
                            </div>
                            <div class="cd-track-stage" title="Evaluation: {{ $evaluated }}">
                                <span class="cd-track-badge s-eva">{{ $evaluated }}</span>
                                <span class="cd-track-label">Evaluation</span>
                            </div>
                            <div class="cd-track-stage" title="Negotiation: {{ $negotiation }}">
                                <span class="cd-track-badge s-neg">{{ $negotiation }}</span>
                                <span class="cd-track-label">Negotiation</span>
                            </div>
                            <div class="cd-track-stage" title="Offer: {{ $offers }}">
                                <span class="cd-track-badge s-off">{{ $offers }}</span>
                                <span class="cd-track-label">Offer</span>
                            </div>
                            <div class="cd-track-stage" title="Joining: {{ $joined }}">
                                <span class="cd-track-badge s-joi">{{ $joined }}</span>
                                <span class="cd-track-label">Joining</span>
                            </div>
                        </div>

                        {{-- Row 2 (Bottom): Separated Aggregate Summary Metric Pills --}}
                        <div class="cd-pipeline-summary-bar">
                            <div class="cd-pipe-metric-pill pill-apps" title="Total Applications: {{ $allCandidates }}">
                                <i class="fas fa-file-alt text-primary"></i>
                                <span>Applications:</span>
                                <strong>{{ $allCandidates }}</strong>
                            </div>
                            <div class="cd-pipe-metric-pill pill-int" title="Total Interviews: {{ $interviews }}">
                                <i class="fas fa-video text-info"></i>
                                <span>Interviews:</span>
                                <strong>{{ $interviews }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sub-Card 6: Bottom Activity Strip -->
            <div class="cd-client-card-bottom">
                <div class="cd-bottom-left">
                    <span><i class="far fa-clock text-muted me-1"></i>{{ $daysAgo ? 'Active ' . $daysAgo : 'No recent activity' }}</span>
                    <span>&bull;</span>
                    <span><i class="far fa-calendar-alt text-muted me-1"></i>Connected: <strong>{{ $newDate }}</strong></span>
                </div>
                <div class="cd-bottom-actions">
                    <button type="button" class="cd-btn-quick-drawer {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}">
                        <i class="fas fa-folder-open me-1"></i>View Drawer
                    </button>
                    @if(in_array("add_jobs", $userPermissions) && !$isDirect)
                        <a href="{{ url('service_company/jobs/curstomcreate/' . $client_id) }}" class="cd-btn-quick-post">
                            <i class="fas fa-plus me-1"></i>Post Job
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    @endforeach
    </div>
@else
    <div class="cd-client-empty">
        <i class="fas fa-building fa-2x mb-3 text-muted d-block"></i>
        <div class="fw-bold fs-6 text-gray-700 mb-1">No Clients Found</div>
        <div class="text-muted fs-7">No clients match the current filters or search query.</div>
    </div>
@endif
