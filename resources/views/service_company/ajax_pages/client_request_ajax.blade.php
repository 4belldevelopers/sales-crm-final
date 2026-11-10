@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    $company_id = Auth::user()->company_id ?? null;
    $tenantTimezone = GeneralMethod::defaultTimeZone($company_id);
    $now = Carbon::now($tenantTimezone);
@endphp

<style>
    .is-pending-request .cl-subcards-grid,
    .is-inbound-invite .cl-subcards-grid {
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.1fr) minmax(0, 1.1fr) minmax(0, 2.3fr) !important;
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
</style>

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $company_name = ucwords($row->company_name ?? 'Client');
            $client_id = $row->client_id ?? 0;
            $from_company_id = $row->from_company_id ?? 0;
            $staffing_company_invitation_id = $row->staffing_company_invitation_id ?? ($row->id ?? 0);
            $target_id = $client_id ?: ($from_company_id ?: $staffing_company_invitation_id);

            $city = $row->city_name ?? ($row->city ?? '');
            $country_name = $row->country_name ?? ($row->country ?? '');
            $ind_name = !empty($row->ind_name) ? $row->ind_name : 'Staffing & Recruiting';

            // Resolve Timestamps & Expiry with Tenant Timezone
            $created_at = !empty($row->from_created_at) ? $row->from_created_at : ($row->created_at ?? '');
            $createdAt = !empty($created_at) ? Carbon::parse($created_at)->setTimezone($tenantTimezone) : Carbon::now($tenantTimezone);
            $expiresAt = !empty($row->expires_at) ? Carbon::parse($row->expires_at)->setTimezone($tenantTimezone) : (clone $createdAt)->addDays(7);
            
            $tzShort = $createdAt->format('T');
            $dateSentFormatted = $createdAt->format('d M, Y • h:i A') . ' ' . $tzShort;
            $expiryDateFormatted = $expiresAt->format('d M, Y • h:i A') . ' ' . $tzShort;
            $daysAgo = GeneralMethod::timeAgo($createdAt);

            $isExpired = $now->greaterThan($expiresAt);

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
            $to_invitation_status = (int) ($row->to_invitation_status ?? 0);
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
            if (!empty($row->logo) && strlen($row->logo) > 10) {
                $background_image = $row->logo;
            } elseif (!empty($row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
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
            $displayWebsite = !empty($row->website) ? $row->website : null;
            if (!empty($displayWebsite)) {
                $displayWebsite = rtrim(preg_replace('~^https?://(www\.)?~i', '', $displayWebsite), '/');
            }
        @endphp

        <div class="cl-primary-card tbl-r is-pending-request is-inbound-invite" data-pending-id="{{ $staffing_company_invitation_id }}" data-row-id="{{ $target_id }}" data-client-type="request">
            <div class="cl-subcards-grid">
                <!-- SUB-CARD 1: Client Identity -->
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

                <!-- SUB-CARD 2: Location -->
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

                <!-- SUB-CARD 3: Partnership Scope -->
                <div class="cl-subcard cl-subcard-jobs">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title" title="Partnership and engagement scope">Engagement</span>
                    </div>
                    <div class="cl-jobs-body tbl-cell-jobs d-flex flex-column justify-content-center" style="gap:4px;">
                        <div class="d-flex align-items-center gap-1.5" style="font-size:11px;">
                            <span class="badge badge-light-primary py-0.5 px-2 fw-bold text-truncate" style="font-size:10px;">Direct Invitation</span>
                        </div>
                        <div class="text-muted text-truncate" style="font-size:10.5px; color:#64748b;" title="Direct client partnership request">
                            Talent & Hiring Partner
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: Action Center (Equal Size Buttons) -->
                <div class="cl-subcard cl-subcard-pipeline">
                    <div class="cl-subcard-head d-flex align-items-center justify-content-between">
                        <span class="cl-subcard-title" title="Partnership decision actions">Actions</span>
                        <span class="badge {{ $expiryBadgeClass }} py-0.5 px-2" style="font-size:9.5px; font-weight:700;" title="Remaining decision window">
                            {{ $expiryCountdown }}
                        </span>
                    </div>
                    <div class="cl-pipe-body tbl-cell-req-actions d-flex align-items-center justify-content-center h-100" style="padding:4px 0;">
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
                            <div class="d-flex align-items-center gap-1.5 w-100">
                                <!-- Accept Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-success fw-bold request_event flex-fill text-truncate" 
                                        data-row-id="{{ $staffing_company_invitation_id }}" 
                                        data-row-event="1" 
                                        data-row-from_company="{{ $from_company_id }}" 
                                        style="height:32px; padding:0 8px; font-size:11px; display:inline-flex; align-items:center; justify-content:center; gap:4px; border-radius:6px; background:#10b981; border:none; color:#ffffff; width:33.33%; flex:1 1 33.33%; transition:all 0.2s;" 
                                        title="Accept Direct Client Partnership">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span class="text-truncate">Accept</span>
                                </button>

                                <!-- Decline Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-light-danger fw-bold request_event flex-fill text-truncate" 
                                        data-row-id="{{ $staffing_company_invitation_id }}" 
                                        data-row-event="-1" 
                                        data-row-from_company="{{ $from_company_id }}" 
                                        style="height:32px; padding:0 8px; font-size:11px; display:inline-flex; align-items:center; justify-content:center; gap:4px; border-radius:6px; background:#fef2f2; border:1.5px solid #fecaca; color:#ef4444; width:33.33%; flex:1 1 33.33%; transition:all 0.2s;" 
                                        title="Decline Partnership Request">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    <span class="text-truncate">Decline</span>
                                </button>

                                <!-- Terms Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-light fw-bold show-detail-direct view-request-terms flex-fill text-truncate" 
                                        data-row-id="{{ $target_id }}" 
                                        data-client-type="request" 
                                        style="height:32px; padding:0 8px; font-size:11px; display:inline-flex; align-items:center; justify-content:center; gap:4px; border-radius:6px; background:#eff6ff; border:1.5px solid #bfdbfe; color:#2563eb; width:33.33%; flex:1 1 33.33%; transition:all 0.2s;" 
                                        title="Review Partnership Terms & Profile">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                    <span class="text-truncate">Terms</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BOTTOM STRIP (TIER 5): AUDIT TIMELINE WITH TIMEZONE & VERIFIED OUTREACH -->
            <div class="cl-card-bottom-strip d-flex align-items-center justify-content-between flex-wrap gap-2" style="padding:7px 14px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:11px;">
                <!-- LEFT: Request Received & Expiry with Timezone -->
                <div class="d-flex align-items-center flex-wrap gap-2 text-muted">
                    <!-- Received -->
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

                    <!-- Expires -->
                    <div class="d-flex align-items-center gap-1.5" title="Valid until {{ $expiryDateFormatted }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $isExpired ? '#dc2626' : '#d97706' }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        <span style="color:#64748b;">Expires:</span>
                        <strong style="color:{{ $isExpired ? '#dc2626' : '#0f172a' }};">{{ $expiryDateFormatted }}</strong>
                        <span class="badge {{ $expiryBadgeClass }} ms-1 py-0 px-1.5" style="font-size:9.5px; font-weight:700;">{{ $ttlLabel }}</span>
                    </div>
                </div>

                <!-- RIGHT: Verified Direct Outreach & Reference ID -->
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

    @endforeach
@else
    <div class="cl-empty-state">
        <div class="cl-empty-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/>
            </svg>
        </div>
        <div class="cl-empty-title">No Pending Client Requests</div>
        <div class="cl-empty-desc">You're all caught up. New client partnership requests will appear here when received.</div>
    </div>
@endif
