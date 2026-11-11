@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
@endphp

<style>
    .is-pending-request .cl-subcards-grid,
    .is-inbound-invite .cl-subcards-grid {
        grid-template-columns: minmax(0, 2.4fr) minmax(0, 1.15fr) minmax(0, 1.85fr) minmax(0, 1.6fr) !important;
    }
    .cl-status-pill.pill-warning {
        background: #fffbeb !important;
        color: #d97706 !important;
        border: 1px solid #fde68a !important;
    }
    .cl-status-pill.pill-warning .cl-status-pill-dot {
        background: #d97706 !important;
    }
    .is-pending-request.cl-primary-card {
        border-left: 3.5px solid #f59e0b !important;
        background: #ffffff !important;
    }
    .is-pending-request.cl-primary-card:hover {
        border-color: #cbd5e1 !important;
        border-left-color: #f59e0b !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    }
    .is-pending-request .tbl-cell-req-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 2px 0 !important;
        width: 100% !important;
    }
    .is-pending-request .cl-req-btn-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
        max-width: 255px !important;
        margin: 0 auto !important;
    }
    .is-pending-request .cl-req-btn {
        height: 31px !important;
        padding: 0 8px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        letter-spacing: 0.15px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        border-radius: 6px !important;
        flex: 1 1 0% !important;
        max-width: 82px !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        transition: all 0.15s ease-in-out !important;
        cursor: pointer !important;
    }
    .is-pending-request .cl-req-btn-accept {
        background: #10b981 !important;
        border: 1px solid #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2) !important;
    }
    .is-pending-request .cl-req-btn-accept:hover {
        background: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3) !important;
    }
    .is-pending-request .cl-req-btn-decline {
        background: #ffffff !important;
        border: 1px solid #fecaca !important;
        color: #ef4444 !important;
    }
    .is-pending-request .cl-req-btn-decline:hover {
        background: #fef2f2 !important;
        border-color: #f87171 !important;
        color: #dc2626 !important;
    }
    .is-pending-request .cl-req-btn-terms {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
    }
    .is-pending-request .cl-req-btn-terms:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
</style>

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $rowObj = (object) $rowArr;
        $isPending = !empty($rowObj->is_pending_request);
    @endphp

    @if($isPending)
        @php
            $company_name = ucwords($rowObj->company_name ?? 'Client');
            $client_id = $rowObj->client_id ?? 0;
            $from_company_id = $rowObj->from_company_id ?? 0;
            $staffing_company_invitation_id = $rowObj->staffing_company_invitation_id ?? ($rowObj->id ?? 0);
            $target_id = $client_id ?: ($from_company_id ?: $staffing_company_invitation_id);

            $city = $rowObj->city_name ?? ($rowObj->city ?? '');
            $country_name = $rowObj->country_name ?? ($rowObj->country ?? '');
            $ind_name = !empty($rowObj->ind_name) ? $rowObj->ind_name : 'Staffing & Recruiting';

            $currentCompanyId = $company_id ?? (Auth::check() ? Auth::user()->company_id : null);
            $tenantTimezone = GeneralMethod::defaultTimeZone($currentCompanyId);
            $now = Carbon::now($tenantTimezone);

            // Resolve Timestamps & Expiry with Tenant Timezone
            $created_at = !empty($rowObj->from_created_at) ? $rowObj->from_created_at : ($rowObj->created_at ?? '');
            $createdAt = !empty($created_at) ? Carbon::parse($created_at)->setTimezone($tenantTimezone) : Carbon::now($tenantTimezone);
            $expiresAt = !empty($rowObj->expires_at) ? Carbon::parse($rowObj->expires_at)->setTimezone($tenantTimezone) : (clone $createdAt)->addDays(7);
            
            $tzShort = $createdAt->format('T');
            $dateSentFormatted = $createdAt->format('d M, Y • h:i A') . ' ' . $tzShort;
            $expiryDateFormatted = $expiresAt->format('d M, Y • h:i A') . ' ' . $tzShort;
            $daysAgo = GeneralMethod::timeAgo($createdAt);

            $isExpired = $now->greaterThan($expiresAt) || ((int) ($rowObj->to_invitation_status ?? 0) === -2);

            if ($isExpired) {
                $expiryCountdown = 'Expired';
                $expiryBadgeClass = 'badge-light-danger text-danger';
                $ttlLabel = 'Expired';
            } else {
                $diffDays = (int) $now->diffInDays($expiresAt);
                $diffHours = (int) $now->diffInHours($expiresAt);
                if ($diffDays >= 1) {
                    $expiryCountdown = $diffDays . ' ' . \Illuminate\Support\Str::plural('day', $diffDays) . ' left';
                    $expiryBadgeClass = ($diffDays <= 2) ? 'badge-light-warning text-warning' : 'badge-light-success text-success';
                    $ttlLabel = $diffDays . 'd remaining';
                } elseif ($diffHours >= 1) {
                    $expiryCountdown = $diffHours . ' ' . \Illuminate\Support\Str::plural('hr', $diffHours) . ' left';
                    $expiryBadgeClass = 'badge-light-danger text-danger';
                    $ttlLabel = $diffHours . 'h remaining';
                } else {
                    $diffMins = max(1, (int) $now->diffInMinutes($expiresAt));
                    $expiryCountdown = $diffMins . 'm left';
                    $expiryBadgeClass = 'badge-light-danger text-danger';
                    $ttlLabel = $diffMins . 'm remaining';
                }
            }

            // Singular Authoritative Status Pill
            $to_invitation_status = (int) ($rowObj->to_invitation_status ?? 0);
            $statusLabel = 'Action Needed';
            $statusClass = 'pill-warning';
            if ($to_invitation_status == 1) {
                $statusLabel = 'Connected';
                $statusClass = 'pill-active';
            } elseif ($to_invitation_status == -1) {
                $statusLabel = 'Declined';
                $statusClass = 'pill-inactive';
            } elseif ($to_invitation_status == 2 || $isExpired) {
                $statusLabel = 'Expired';
                $statusClass = 'pill-inactive';
            }

            // Logo / avatar calculation
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

            // Website resolve
            $displayWebsite = !empty($rowObj->website) ? $rowObj->website : null;
            if (!empty($displayWebsite)) {
                $displayWebsite = rtrim(preg_replace('~^https?://(www\.)?~i', '', $displayWebsite), '/');
            }
        @endphp

        <div class="cl-primary-card tbl-r is-pending-request is-inbound-invite" data-pending-id="{{ $staffing_company_invitation_id }}" data-row-id="{{ $target_id }}" data-client-type="request">
            {{-- Upper 4 Discrete Sub-Cards Grid --}}
            <div class="cl-subcards-grid">
                {{-- SUB-CARD 1: Client Identity --}}
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title" title="Inviting client company details">Client</span>
                        <div class="cl-subcard-badges">
                            <span class="cl-scope-pill pill-direct type-direct" title="Inbound direct client partnership request">Direct Client</span>
                            <span class="cl-status-pill {{ $statusClass }}" title="Authoritative invitation status: {{ $statusLabel }}">
                                <span class="cl-status-pill-dot {{ $statusClass === 'pill-warning' ? 'animate-pulse' : '' }}"></span>
                                <span>{{ $statusLabel }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="cl-identity-body">
                        <div class="cl-logo-box" title="Client company logo or initials">
                            @if (!empty($background_image))
                                <img src="{{ $background_image }}" alt="{{ e($initials) }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                            @else
                                <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="cl-details-box min-w-0">
                            <div class="cl-name view-request-terms text-truncate" data-row-id="{{ $target_id }}" data-client-type="request" title="{{ e($company_name) }}" style="cursor:pointer;">
                                {{ $company_name }}
                            </div>
                            <div class="cl-ind text-truncate" title="Industry: {{ e($ind_name) }}">{{ $ind_name }}</div>
                            @if (!empty($displayWebsite))
                                <div class="cl-size text-primary text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size:10.5px; color:#0284c7;" title="Official Website: {{ e($displayWebsite) }}">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                    <span class="text-truncate">{{ $displayWebsite }}</span>
                                </div>
                            @else
                                <div class="cl-size text-muted text-truncate" style="font-size:10px; color:#94a3b8;">Corporate Client</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 2: Location --}}
                <div class="cl-subcard cl-subcard-loc">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title" title="Client headquarters or primary operating location">Location</span>
                    </div>
                    <div class="cl-loc-body">
                        <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" title="Geographic location">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div class="cl-loc-lines">
                            @if ($city)
                                <div class="cl-loc-city text-truncate" title="City: {{ e($city) }}">{{ $city }}</div>
                            @endif
                            @if ($country_name)
                                <div class="cl-loc-country text-truncate" title="Country: {{ e($country_name) }}">{{ $country_name }}</div>
                            @endif
                            @if (!$city && !$country_name)
                                <div class="cl-loc-empty" style="color:#64748b; font-size:11px;">Unspecified</div>
                                <div style="font-size:9.5px; color:#94a3b8;">Set on connection</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 3: Partnership Scope --}}
                <div class="cl-subcard cl-subcard-jobs">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title" title="Partnership and engagement scope">Engagement</span>
                    </div>
                    <div class="cl-jobs-body tbl-cell-jobs d-flex flex-column justify-content-center" style="gap:4px;">
                        <div class="d-flex align-items-center gap-1.5" style="font-size:11px;">
                            <span class="badge badge-light-primary py-0.5 px-2 fw-bold text-nowrap" style="font-size:10px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:4px;">Direct Invitation</span>
                        </div>
                        <div class="text-muted text-nowrap" style="font-size:10.5px; color:#475569; font-weight:500;" title="Direct client partnership request">
                            Talent & Hiring Partner
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 4: Action Center (Enterprise Proportions & Styling) --}}
                <div class="cl-subcard cl-subcard-pipeline cl-subcard-actions">
                    <div class="cl-subcard-head d-flex align-items-center justify-content-between">
                        <span class="cl-subcard-title" title="Partnership decision actions">Actions</span>
                        <span class="badge {{ $expiryBadgeClass }} py-0.5 px-2" style="font-size:9.5px; font-weight:700; border-radius:4px;" title="Remaining decision window">
                            {{ $expiryCountdown }}
                        </span>
                    </div>
                    <div class="cl-pipe-body tbl-cell-req-actions d-flex align-items-center justify-content-center h-100" style="padding:2px 0;">
                        @if ($to_invitation_status == 1)
                            <span class="badge bg-light-success text-success fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1" style="font-size:11px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Connected</span>
                            </span>
                        @elseif ($to_invitation_status == -1)
                            <span class="badge bg-light-danger text-danger fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1" style="font-size:11px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span>Declined</span>
                            </span>
                        @elseif ($to_invitation_status == 2 || $isExpired)
                            <span class="badge bg-light-danger text-danger fw-bold px-3 py-1.5" style="font-size:11px;">Expired</span>
                        @else
                            <div class="cl-req-btn-group">
                                <!-- Accept Button -->
                                <button type="button" 
                                        class="btn btn-sm cl-req-btn cl-req-btn-accept request_event" 
                                        data-row-id="{{ $staffing_company_invitation_id }}" 
                                        data-row-event="1" 
                                        data-row-from_company="{{ $from_company_id }}" 
                                        title="Accept Direct Client Partnership">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Accept</span>
                                </button>

                                <!-- Decline Button -->
                                <button type="button" 
                                        class="btn btn-sm cl-req-btn cl-req-btn-decline request_event" 
                                        data-row-id="{{ $staffing_company_invitation_id }}" 
                                        data-row-event="-1" 
                                        data-row-from_company="{{ $from_company_id }}" 
                                        title="Decline Partnership Request">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    <span>Decline</span>
                                </button>

                                <!-- Terms Button -->
                                <button type="button" 
                                        class="btn btn-sm cl-req-btn cl-req-btn-terms show-detail-direct view-request-terms" 
                                        data-row-id="{{ $target_id }}" 
                                        data-client-type="request" 
                                        title="Review Partnership Terms & Profile">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                    <span>Terms</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BOTTOM STRIP: Audit Timeline and Reference ID with Timezone --}}
            <div class="cl-card-bottom-strip d-flex align-items-center justify-content-between flex-wrap gap-2" style="padding:7px 14px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:11px;">
                <div class="d-flex align-items-center flex-wrap gap-2 text-muted">
                    <div class="d-flex align-items-center gap-1.5" title="Request received on {{ $dateSentFormatted }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span style="color:#64748b;">Request Received:</span>
                        <strong class="text-gray-900">{{ $dateSentFormatted }}</strong>
                        <span class="text-primary fw-semibold" style="font-size:10px;">({{ $daysAgo }})</span>
                    </div>
                    <span style="color:#cbd5e1; font-weight:bold;">•</span>
                    <div class="d-flex align-items-center gap-1.5" title="Valid until {{ $expiryDateFormatted }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        <span style="color:#64748b;">Expires:</span>
                        <strong style="color:{{ $isExpired ? '#dc2626' : '#0f172a' }};">{{ $expiryDateFormatted }}</strong>
                        <span class="badge {{ $expiryBadgeClass }} ms-1 py-0 px-1.5" style="font-size:9.5px; font-weight:700;">{{ $ttlLabel }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:10.5px;">
                    <span class="d-flex align-items-center gap-1" style="color:#15803d; font-weight:600;" title="Authenticated direct client invitation">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                        <span>Verified Client Outreach</span>
                    </span>
                    <span style="color:#cbd5e1; font-weight:bold;">•</span>
                    <span>Ref: <strong class="text-gray-700">#REQ-{{ str_pad($staffing_company_invitation_id, 5, '0', STR_PAD_LEFT) }}</strong></span>
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
        if (!empty($row->client_location) && (is_array($row->client_location) || is_iterable($row->client_location))) {
            foreach ($row->client_location as $locItem) {
                $locArr = (array) $locItem;
                if (!empty($locArr['city_name'])) $city = $locArr['city_name'];
                elseif (!empty($locArr['city'])) $city = $locArr['city'];
                if (!empty($locArr['state_name'])) $stateName = $locArr['state_name'];
                elseif (!empty($locArr['state']) && !is_numeric($locArr['state'])) $stateName = $locArr['state'];
                if (!empty($locArr['country_name'])) $country_name = $locArr['country_name'];
                elseif (!empty($locArr['country'])) $country_name = $locArr['country'];
                if (!empty($city) || !empty($stateName) || !empty($country_name)) break;
            }
        }
        if (empty($city) && !empty($row->city_name)) $city = $row->city_name;
        if (empty($city) && !empty($row->city)) $city = $row->city;
        if (empty($stateName) && !empty($row->state_name)) $stateName = $row->state_name;
        if (empty($stateName) && !empty($row->state) && !is_numeric($row->state)) $stateName = $row->state;
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
        $sourced       = (int)($perf->sourced ?? 0);
        $screening     = (int)($perf->screening ?? 0);
        $evaluated     = (int)($perf->evaluated ?? 0);
        $negotiations  = (int)($perf->negotiations ?? ($perf->negotiation ?? 0));
        $offers        = (int)($perf->offers ?? 0);
        $joined        = (int)($perf->Joined ?? ($perf->joined ?? 0));
        $rejected      = (int)($perf->rejected ?? 0);
        $inReview      = (int)($perf->in_review ?? 0);
        $interviews    = (int)($perf->all_interview ?? 0);

        // Invariant 3: Total Applications derived from mutually exclusive states
        $activePipeline = ($sourced + $screening + $evaluated + $negotiations + $offers + $joined);
        $allCandidates  = ($activePipeline + $rejected + $inReview);

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

    <div class="cl-primary-card tbl-r {{ $client_type_css }} {{ $job_active > 0 ? 'is-active-client' : 'is-inactive-client' }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}" data-active-jobs="{{ (int)$job_active }}" data-client-name="{{ e($client_Or_CompanyName) }}">
        {{-- Upper 4 Discrete Sub-Cards Grid --}}
        <div class="cl-subcards-grid">
            {{-- SUB-CARD 1: Client Identity (Internal & Active at TOP) --}}
            <div class="cl-subcard cl-subcard-identity">
                <div class="cl-subcard-head">
                    <span class="cl-subcard-title">Client Identity</span>
                    <div class="cl-subcard-badges">
                        <span class="cl-scope-pill {{ $isDirect ? 'pill-direct type-direct' : 'pill-internal type-internal' }} {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" data-client-type="{{ $isDirect ? 'direct' : 'internal' }}">
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
                        $locCity = !empty($city) ? ucwords(strtolower((string) $city)) : '';
                        $locState = !empty($stateName) ? ucwords(strtolower((string) $stateName)) : '';
                        $locCountry = !empty($country_name) ? ucwords(strtolower((string) $country_name)) : '';
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
                            <span class="cl-pipe-label" title="Sourcing">Sourcing</span>
                        </div>
                        <div class="cl-pipe-step step-screening {{ $screening > 0 ? 'has-count' : '' }}" title="Screening: {{ $screening }}">
                            <span class="cl-pipe-pill pill-screening">{{ $screening }}</span>
                            <span class="cl-pipe-label" title="Screening">Screening</span>
                        </div>
                        <div class="cl-pipe-step step-evaluation {{ $evaluated > 0 ? 'has-count' : '' }}" title="Evaluation: {{ $evaluated }}">
                            <span class="cl-pipe-pill pill-evaluation">{{ $evaluated }}</span>
                            <span class="cl-pipe-label" title="Evaluation">Evaluation</span>
                        </div>
                        <div class="cl-pipe-step step-negotiations {{ $negotiations > 0 ? 'has-count' : '' }}" title="Negotiation: {{ $negotiations }}">
                            <span class="cl-pipe-pill pill-negotiations">{{ $negotiations }}</span>
                            <span class="cl-pipe-label" title="Negotiation">Negotiation</span>
                        </div>
                        <div class="cl-pipe-step step-offer {{ $offers > 0 ? 'has-count' : '' }}" title="Offer: {{ $offers }}">
                            <span class="cl-pipe-pill pill-offer">{{ $offers }}</span>
                            <span class="cl-pipe-label" title="Offer">Offer</span>
                        </div>
                        <div class="cl-pipe-step step-joined {{ $joined > 0 ? 'has-count' : '' }}" title="Joined: {{ $joined }}">
                            <span class="cl-pipe-pill pill-joined">{{ $joined }}</span>
                            <span class="cl-pipe-label" title="Joined">Joined</span>
                        </div>
                        <div class="cl-pipe-step step-rejected {{ $rejected > 0 ? 'has-count' : '' }}" title="Rejected: {{ $rejected }}">
                            <span class="cl-pipe-pill pill-rejected">{{ $rejected }}</span>
                            <span class="cl-pipe-label" title="Rejected">Rejected</span>
                        </div>
                        <div class="cl-pipe-divider" aria-hidden="true"></div>
                        <div class="cl-pipe-step step-interview {{ $interviews > 0 ? 'has-count' : '' }}" title="Interview: {{ $interviews }}">
                            <span class="cl-pipe-pill pill-interview">{{ $interviews }}</span>
                            <span class="cl-pipe-label" title="Interview">Interview</span>
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
