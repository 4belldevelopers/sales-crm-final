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

@if (!empty($isRequest))
    <style>
        .is-pending-invite .cl-subcards-grid {
            grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.05fr) minmax(0, 1.15fr) minmax(0, 2.3fr) !important;
        }
    </style>
    {{-- ── TAB 6: PENDING INVITATIONS STREAM (Rule 20 Enterprise Card + Sub-Card Design) ── --}}
    @if (count($invitations) > 0)
        @foreach ($invitations as $inv)
            @php
                $invId = $inv->id;
                $invEmail = $inv->to_email;
                $isRegistered = !empty($inv->to_company_id);
                $hasCustomName = !empty($inv->company_name) && strtolower(trim($inv->company_name)) !== strtolower(trim($invEmail));
                $company_name = $hasCustomName ? ucwords($inv->company_name) : $invEmail;
                $ind_name = !empty($inv->ind_name) ? $inv->ind_name : 'Staffing & Recruitment';
                
                $logo_path = '';
                if (!empty($inv->website) && empty($inv->logo)){
                    $domainGet = GeneralMethod::getDomain($inv->website);
                    $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                    $google = GeneralMethod::getDomainIcon($googleIcon, $invId, true);
                    $logo_path  = ($google) ? $googleIcon : '';
                }
                if (!empty($inv->logo) && strlen($inv->logo) > 10) {
                    $logo_path = $inv->logo;
                }

                $bgTextDecore = GeneralMethod::icoinText($company_name);
                $initials = strtoupper(implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode(' ', trim($company_name)))));
                if (strlen($initials) > 3) {
                    $initials = substr($initials, 0, 2);
                }
                if (empty($initials)) {
                    $initials = 'AG';
                }

                $city = $inv->city_name ?? '';
                $state = $inv->state_name ?? '';
                $country = $inv->country_name ?? '';

                // Resolve Tenant Timezone from Settings
                $tenantTimezone = $tenantTimezone ?? GeneralMethod::defaultTimeZone($company_id ?? null);

                // Date & Expiry Calculations (7-Day TTL Window) in Tenant Timezone
                $createdAt = !empty($inv->from_created_at) ? Carbon::parse($inv->from_created_at)->setTimezone($tenantTimezone) : Carbon::now($tenantTimezone);
                $expiresAt = !empty($inv->expires_at) ? Carbon::parse($inv->expires_at)->setTimezone($tenantTimezone) : (clone $createdAt)->addDays(7);
                
                $tzShort = $createdAt->format('T');
                $dateSentFormatted = $createdAt->format('d M, Y • h:i A') . ' ' . $tzShort;
                $timeAgo = GeneralMethod::timeAgo($createdAt);
                
                $expiryDateFormatted = $expiresAt->format('d M, Y • h:i A') . ' ' . $tzShort;
                $now = Carbon::now($tenantTimezone);
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

                // Singular Authoritative Status Pill (Zero Repetition Across Sub-Cards)
                $statusLabel = 'Pending Registration';
                $statusClass = 'pill-warning';
                if ($inv->to_invitation_status == -1) {
                    $statusLabel = 'Declined';
                    $statusClass = 'pill-inactive';
                } elseif ($isRegistered) {
                    $statusLabel = 'Awaiting Acceptance';
                    $statusClass = 'pill-active';
                } elseif ($isExpired) {
                    $statusLabel = 'Expired';
                    $statusClass = 'pill-inactive';
                }

                // Resolve Official Website (Replacing Direct Email Outreach)
                $displayWebsite = !empty($inv->website) ? $inv->website : (str_contains($invEmail, '@') ? substr(strrchr($invEmail, '@'), 1) : null);
                if (!empty($displayWebsite)) {
                    $displayWebsite = rtrim(preg_replace('~^https?://(www\.)?~i', '', $displayWebsite), '/');
                }
            @endphp

            <div class="cl-primary-card tbl-r is-pending-invite" data-invitation-id="{{ $invId }}" data-agency-name="{{ e($company_name) }}">
                <div class="cl-subcards-grid">
                    <!-- SUB-CARD 1: Agency Invitee Identity -->
                    <div class="cl-subcard cl-subcard-identity">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Invited agency partner details">Agency</span>
                            <div class="cl-subcard-badges">
                                <span class="cl-scope-pill pill-internal type-internal" title="Outbound partnership invitation">Invited Partner</span>
                                <span class="cl-status-pill {{ $statusClass }}" title="Authoritative invitation status: {{ $statusLabel }}">
                                    <span class="cl-status-pill-dot"></span>
                                    <span>{{ $statusLabel }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="cl-identity-body">
                            <div class="cl-logo-box" title="Agency logo or initials">
                                @if (!empty($logo_path))
                                    <img src="{{ $logo_path }}" alt="{{ e($initials) }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                    <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                                @else
                                    <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="cl-details-box min-w-0">
                                <div class="cl-name text-truncate" title="{{ e($company_name) }}">{{ $company_name }}</div>
                                <div class="cl-ind text-truncate" title="Specialization / Industry: {{ e($ind_name) }}">{{ $ind_name }}</div>
                                @if (!empty($displayWebsite))
                                    <div class="cl-size text-primary text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size:10.5px; color:#0284c7;" title="Official Website: {{ e($displayWebsite) }}">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                        </svg>
                                        <span class="text-truncate">{{ $displayWebsite }}</span>
                                    </div>
                                @elseif ($hasCustomName)
                                    <div class="cl-size text-muted text-truncate" title="Contact Email: {{ e($invEmail) }}">{{ $invEmail }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 2: Location (Clean & Non-Redundant) -->
                    <div class="cl-subcard cl-subcard-loc">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Agency primary operating headquarters">Location</span>
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
                                @if ($state)
                                    <div class="cl-loc-state text-truncate" title="State: {{ e($state) }}">{{ $state }}</div>
                                @endif
                                @if ($country)
                                    <div class="cl-loc-country text-truncate" title="Country: {{ e($country) }}">{{ $country }}</div>
                                @endif
                                @if (!$city && !$state && !$country)
                                    <div class="cl-loc-empty" style="color:#64748b; font-size:11px;" title="Location will be populated when the agency accepts and joins">Unspecified</div>
                                    <div style="font-size:9.5px; color:#94a3b8;">Set on registration</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 3: Partnership & Network Scope (Zero Coin Text) -->
                    <div class="cl-subcard cl-subcard-jobs">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Partnership and onboarding status">Engagement</span>
                        </div>
                        <div class="cl-jobs-body tbl-cell-jobs d-flex flex-column justify-content-center" style="gap:4px;">
                            <div class="d-flex align-items-center gap-1.5" style="font-size:11px;">
                                @if ($isRegistered)
                                    <span class="badge badge-light-success py-0.5 px-2 fw-bold text-truncate" style="font-size:10px;">Platform Partner</span>
                                @else
                                    <span class="badge badge-light-primary py-0.5 px-2 fw-bold text-truncate" style="font-size:10px;">Direct Outreach</span>
                                @endif
                            </div>
                            <div class="text-muted text-truncate" style="font-size:10.5px; color:#64748b;" title="{{ $isRegistered ? 'Registered on JERO Network' : 'Registration invitation pending' }}">
                                {{ $isRegistered ? 'Registered Firm' : 'Awaiting sign-up' }}
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 4: INVITATION ACTIONS (Equal Size Buttons) -->
                    <div class="cl-subcard cl-subcard-pipeline">
                        <div class="cl-subcard-head d-flex align-items-center justify-content-between">
                            <span class="cl-subcard-title" title="Quick invitation management actions">Actions</span>
                            <span class="badge {{ $expiryBadgeClass }} py-0.5 px-2" style="font-size:9.5px; font-weight:700;" title="Remaining TTL window">
                                {{ $expiryCountdown }}
                            </span>
                        </div>
                        <div class="cl-pipe-body tbl-cell-pipeline d-flex align-items-center justify-content-center h-100" style="padding:4px 0;">
                            <!-- Equal Size Action Buttons (50% / 50%) -->
                            <div class="d-flex align-items-center gap-2 w-100">
                                <button type="button" 
                                        class="btn btn-sm btn-light-primary fw-bold resend-agency-invite flex-fill text-truncate" 
                                        data-id="{{ $invId }}" 
                                        data-email="{{ e($invEmail) }}" 
                                        data-company-id="{{ $inv->to_company_id ?? 0 }}"
                                        style="height:32px; padding:0 8px; font-size:11px; display:inline-flex; align-items:center; justify-content:center; gap:5px; border-radius:6px; white-space:nowrap; border:1.5px solid #bfdbfe; background:#eff6ff; width:50%; flex:1 1 50%; transition:all 0.2s;" 
                                        title="Resend invitation email to {{ e($invEmail) }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <polyline points="23 4 23 10 17 10"/>
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                    </svg>
                                    <span class="text-truncate">Resend Invite</span>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-light-danger fw-bold cancel-agency-invite flex-fill text-truncate" 
                                        data-invitation-id="{{ $invId }}" 
                                        style="height:32px; padding:0 8px; font-size:11px; display:inline-flex; align-items:center; justify-content:center; gap:4px; border-radius:6px; white-space:nowrap; border:1.5px solid #fecaca; background:#fef2f2; width:50%; flex:1 1 50%; transition:all 0.2s;" 
                                        title="Cancel this invitation request">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    <span class="text-truncate">Cancel Request</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM STRIP (TIER 5): AUDIT TIMELINE WITH TIMEZONE & DEDUCTED COINS -->
                <div class="cl-card-bottom-strip d-flex align-items-center justify-content-between flex-wrap gap-2" style="padding:7px 14px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:11px;">
                    <!-- LEFT: Invitation Sent & Expiry with Timezone -->
                    <div class="d-flex align-items-center flex-wrap gap-2 text-muted">
                        <!-- Sent -->
                        <div class="d-flex align-items-center gap-1.5" title="Invitation sent on {{ $dateSentFormatted }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span style="color:#64748b;">Invitation Sent:</span>
                            <strong class="text-gray-900">{{ $dateSentFormatted }}</strong>
                            <span class="text-primary fw-semibold" style="font-size:10px;">({{ $timeAgo }})</span>
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

                    <!-- RIGHT: 5 Coins Deducted & Audit Reference ID -->
                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size:10.5px;">
                        <span class="d-flex align-items-center gap-1" style="color:#475569;" title="5 JERO Coins deducted for invitation outreach">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v12M15 9.5a3.5 3.5 0 0 0-5 0 3.5 3.5 0 0 0 0 5 3.5 3.5 0 0 0 5 0"></path>
                            </svg>
                            <span style="font-weight:600; color:#334155;">5 coins deducted</span>
                        </span>
                        <span style="color:#cbd5e1; font-weight:bold;">•</span>
                        <span>Ref: <strong class="text-gray-700">#INV-{{ str_pad($invId, 5, '0', STR_PAD_LEFT) }}</strong></span>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="cl-empty-state">
            <div class="cl-empty-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="cl-empty-title">No pending invitations</div>
            <div class="cl-empty-desc">You have no active pending agency invitations. Use the "Invite Agency" button above to connect with new staffing partners.</div>
        </div>
    @endif
@else
    {{-- ── TABS 1 TO 5: CONNECTED AGENCIES STREAM (Rule 20 Enterprise Card + Sub-Card Design) ── --}}
    @if (count($data) > 0)
        @foreach ($data as $rowArr)
            @php
                $row = (object) $rowArr;
                
                // Batch pre-hydrated relation lookup or fallback
                $relationget = isset($batchRelations[$row->company_id]) 
                    ? $batchRelations[$row->company_id] 
                    : GeneralMethod::getRelationData($row->company_id, $company_id);

                $from_company_id   = $relationget->from_company_id ?? null;
                $recommendation_id = $relationget->to_company_id ?? null;
                $final_status      = $relationget->final_status ?? null;
                $to_created_at     = $relationget->to_created_at ?? null;
                $created_at        = $relationget->created_at ?? null;

                $row->final_status = $final_status;
                $row->staffing_company_relation_id = $recommendation_id;
                $row->to_company_id = $recommendation_id;
                $row->from_company_id = $from_company_id;
                $row->to_created_at = ($to_created_at) ? $to_created_at : $created_at;

                // Batch pre-hydrated industry lookup or fallback
                $td_industries = isset($batchIndustries[$row->industries]) 
                    ? (object) $batchIndustries[$row->industries] 
                    : DB::table('td_industries')->where('ind_id', '=', $row->industries)->first();

                $logo_path = '';
                if (!empty($row->website) && empty($row->logo)){
                    $domainGet = GeneralMethod::getDomain($row->website);
                    $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                    $google = GeneralMethod::getDomainIcon($googleIcon, $row->id, true);
                    $logo_path  = ($google) ? $googleIcon : '';
                }
                if (!empty($row->logo) && strlen($row->logo) > 10) {
                    $logo_path = $row->logo;
                }

                $company_name = ucwords($row->company_name);
                $ind_name = $td_industries ? $td_industries->ind_name : 'Staffing & Recruitment';
                $client_type_css = 'show-detail-direct';
                    
                $bgTextDecore = GeneralMethod::icoinText($row->company_name);
                $initials = strtoupper(implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode(' ', trim($row->company_name)))));
                if (strlen($initials) > 3) {
                    $initials = substr($initials, 0, 2);
                }

                $client_id = $row->client_id;
                
                // Batch pre-hydrated location lookup or fallback
                $locInfo = isset($batchLocations[$client_id]) 
                    ? $batchLocations[$client_id] 
                    : DB::table('client_location')
                        ->select(
                            'client_location.id',
                            'client_location.city',
                            'client_location.address',
                            'client_location.postcode',
                            'client_location.country_id',
                            'city_list.city as city_name',
                            'state_list.state'
                        )
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                        ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                        ->where('client_id', $client_id)
                        ->first();

                $country_name = '';
                $locInfostate = '';
                if ($locInfo) {
                    if (@$locInfo->country_id > 0) {
                        $countryArr = GeneralMethod::getCountryName($locInfo->country_id);
                        if (is_array($countryArr) && count($countryArr) > 0) {
                            foreach ($countryArr as $cval) {
                                $country_name = ucwords($cval['name']);
                            }
                            if (isset($locInfo->state_name)) {
                                $locInfostate = $locInfo->state_name;
                            } elseif (isset($locInfo->state)) {
                                $locInfostate = $locInfo->state;
                            }
                        }
                    }
                }

                $city = @$locInfo->city_name ?: @$locInfo->city;
                $to_created_at = $row->to_created_at;
                $newDate = $to_created_at ? date('d M, Y', strtotime($to_created_at)) : '';
                $timeAgo = $to_created_at ? GeneralMethod::timeAgo($to_created_at) : 'Recently';

                // Batch pre-hydrated job stats lookup or fallback
                $RowJobs = isset($batchAgencyStats[$row->company_id])
                    ? $batchAgencyStats[$row->company_id]
                    : GeneralMethod::agencyJobsCountAllstatus($row->company_id, $company_id);

                $Active = $RowJobs->job_active ?? 0;
                $Hold   = $RowJobs->job_hold ?? 0;
                $Closed = $RowJobs->job_inactive ?? 0;

                // Batch pre-hydrated candidate stats lookup or fallback
                $candidatesProfiles = isset($batchAgencyStats[$row->company_id])
                    ? $batchAgencyStats[$row->company_id]
                    : GeneralMethod::agencyCandidatesCountList($row->company_id, $company_id);

                $all_candidates = $candidatesProfiles->all_candidates ?? 0;
                $Joined         = $candidatesProfiles->Joined ?? 0;
                $offers         = $candidatesProfiles->offers ?? 0;
                $sourced        = $candidatesProfiles->sourced ?? 0;
                $screening      = $candidatesProfiles->screening ?? 0;
                $evaluated      = $candidatesProfiles->evaluated ?? 0;
                $negotiation    = $candidatesProfiles->negotiation ?? 0;
                $rejected       = $candidatesProfiles->rejected ?? 0;
                $all_interview  = $candidatesProfiles->all_interview ?? 0;

                $isDirect = ($final_status == 1);
                $scopeLabel = $isDirect ? 'Direct Agency' : 'Marketplace Partner';
                $scopeClass = $isDirect ? 'pill-direct type-direct' : 'pill-internal type-internal';

                /* ── company size (Employee Strength) ── */
                $compSizeObj = $batchCompanySizes[$row->company_id] ?? null;
                $fromSize = $row->company_size_from ?? ($compSizeObj->company_size_from ?? null);
                $toSize = $row->company_sizes ?? $row->company_size ?? ($compSizeObj->company_size ?? null);
                $companySizeText = '';
                if (!empty($fromSize) && !empty($toSize)) {
                    $companySizeText = number_format((int)$fromSize) . ' - ' . number_format((int)$toSize) . ' employees';
                } elseif (!empty($toSize)) {
                    $companySizeText = is_numeric($toSize) ? number_format((int)$toSize) . ' employees' : $toSize . ' employees';
                }
            @endphp

            <!-- ── Tier 3 Primary Card Stream (Rule 20 Enterprise Card + Sub-Card Design) ── -->
            <div class="cl-primary-card tbl-r {{ $Active > 0 ? 'is-active-client' : 'is-inactive-client' }}" 
                 data-row-id="{{ $row->client_id }}" 
                 data-client-type="direct" 
                 data-active-jobs="{{ $Active }}" 
                 data-agency-name="{{ e($company_name) }}">
                
                <div class="cl-subcards-grid">
                    <!-- SUB-CARD 1: Agency Identity -->
                    <div class="cl-subcard cl-subcard-identity">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Staffing agency verified identity">Agency Identity</span>
                            <div class="cl-subcard-badges">
                                <span class="cl-status-pill {{ $Active > 0 ? 'pill-active' : 'pill-inactive' }}" title="{{ $Active > 0 ? 'Active: Working on ' . $Active . ' live job requirements' : 'Inactive: 0 active job requirements allocated' }}">
                                    <span class="cl-status-pill-dot"></span>
                                    <span>{{ $Active > 0 ? 'Active' : 'Inactive' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="cl-identity-body">
                            <div class="cl-logo-box" title="{{ e($company_name) }}">
                                @if (!empty($logo_path))
                                    <img src="{{ $logo_path }}" alt="{{ e($initials) }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                    <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                                @else
                                    <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="cl-details-box">
                                <div class="cl-name {{ $client_type_css }}" data-row-id="{{ $row->client_id }}" title="Click to view agency profile: {{ e($company_name) }}">{{ $company_name }}</div>
                                <div class="cl-ind" title="Primary Industry Specialization: {{ e($ind_name) }}">{{ $ind_name }}</div>
                                @if(!empty($companySizeText))
                                    <div class="cl-size" title="Employee Strength: {{ e($companySizeText) }}">{{ $companySizeText }}</div>
                                @else
                                    <div class="cl-size text-muted" title="Employee strength unlisted">—</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 2: Location -->
                    <div class="cl-subcard cl-subcard-loc">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Agency primary operating headquarters">Location</span>
                        </div>
                        <div class="cl-loc-body">
                            <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" title="Geographic Location">
                                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="cl-loc-lines">
                                @if ($city)
                                    <div class="cl-loc-city" title="City: {{ e($city) }}">{{ $city }}</div>
                                @endif
                                @if ($locInfostate)
                                    <div class="cl-loc-state" title="State/Province: {{ e($locInfostate) }}">{{ $locInfostate }}</div>
                                @endif
                                @if ($country_name)
                                    <div class="cl-loc-country" title="Country: {{ e($country_name) }}">{{ $country_name }}</div>
                                @endif
                                @if (!$city && !$locInfostate && !$country_name)
                                    <div class="cl-loc-empty" title="Location not specified">—</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 3: Shared Jobs Status -->
                    <div class="cl-subcard cl-subcard-jobs">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title" title="Job requirements allocated to this agency">Shared Jobs</span>
                        </div>
                        <div class="cl-jobs-body tbl-cell-jobs">
                            <div class="cl-job-line {{ $Active > 0 ? 'has-count cl-job-hiring' : 'no-count' }}" title="Active shared jobs: {{ $Active }}">
                                <span class="cl-job-bullet {{ $Active > 0 ? 'bullet-hiring' : 'bullet-neutral' }}"></span>
                                <span class="cl-job-num">{{ $Active }}</span>
                                <span class="cl-job-label">Active</span>
                            </div>
                            <div class="cl-job-line {{ $Hold > 0 ? 'has-count cl-job-hold' : 'no-count' }}" title="Jobs on hold: {{ $Hold }}">
                                <span class="cl-job-bullet {{ $Hold > 0 ? 'bullet-hold' : 'bullet-neutral' }}"></span>
                                <span class="cl-job-num">{{ $Hold }}</span>
                                <span class="cl-job-label">Hold</span>
                            </div>
                            <div class="cl-job-line {{ $Closed > 0 ? 'has-count cl-job-closed' : 'no-count' }}" title="Closed / Filled jobs: {{ $Closed }}">
                                <span class="cl-job-bullet {{ $Closed > 0 ? 'bullet-closed' : 'bullet-neutral' }}"></span>
                                <span class="cl-job-num">{{ $Closed }}</span>
                                <span class="cl-job-label">Closed</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 4: Candidate Pipeline Flow -->
                    <div class="cl-subcard cl-subcard-pipeline">
                        <div class="cl-subcard-head">
                            <div class="cl-subcard-title-wrap">
                                <svg class="cl-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                                </svg>
                                <span class="cl-subcard-title" title="Candidate pipeline funnel and stage conversion">Candidate Pipeline</span>
                            </div>
                        </div>
                        <div class="cl-pipe-body tbl-cell-pipeline" title="Pipeline Summary: Total Submissions: {{ $all_candidates }} | Sourcing: {{ $sourced }} | Screening: {{ $screening }} | Evaluation: {{ $evaluated }} | Interviews: {{ $all_interview }} | Offers: {{ $offers }} | Joined: {{ $Joined }}">
                            <div class="cl-pipeline-flow">
                                <div class="cl-pipe-step step-sourcing {{ $sourced > 0 ? 'has-count' : '' }}" title="Sourcing Stage: {{ $sourced }} candidates submitted">
                                    <span class="cl-pipe-pill cl-pipe-num pill-sourcing">{{ $sourced }}</span>
                                    <span class="cl-pipe-label" title="Sourcing Stage">Sourcing</span>
                                </div>
                                <div class="cl-pipe-step step-screening {{ $screening > 0 ? 'has-count' : '' }}" title="Screening Stage: {{ $screening }} candidates">
                                    <span class="cl-pipe-pill cl-pipe-num pill-screening">{{ $screening }}</span>
                                    <span class="cl-pipe-label" title="Screening Stage">Screening</span>
                                </div>
                                <div class="cl-pipe-step step-evaluation {{ $evaluated > 0 ? 'has-count' : '' }}" title="Evaluation Stage: {{ $evaluated }} candidates">
                                    <span class="cl-pipe-pill cl-pipe-num pill-evaluation">{{ $evaluated }}</span>
                                    <span class="cl-pipe-label" title="Evaluation Stage">Evaluation</span>
                                </div>
                                <div class="cl-pipe-step step-negotiations {{ $negotiation > 0 ? 'has-count' : '' }}" title="Negotiations Stage: {{ $negotiation }} candidates">
                                    <span class="cl-pipe-pill cl-pipe-num pill-negotiations">{{ $negotiation }}</span>
                                    <span class="cl-pipe-label" title="Negotiations Stage">Negotiation</span>
                                </div>
                                <div class="cl-pipe-step step-offer {{ $offers > 0 ? 'has-count' : '' }}" title="Offers Extended: {{ $offers }} candidates">
                                    <span class="cl-pipe-pill cl-pipe-num pill-offer">{{ $offers }}</span>
                                    <span class="cl-pipe-label" title="Offers Extended">Offer</span>
                                </div>
                                <div class="cl-pipe-step step-joined {{ $Joined > 0 ? 'has-count' : '' }}" title="Candidates Joined: {{ $Joined }} verified hires">
                                    <span class="cl-pipe-pill cl-pipe-num pill-joined">{{ $Joined }}</span>
                                    <span class="cl-pipe-label" title="Candidates Joined">Joined</span>
                                </div>
                                <div class="cl-pipe-step step-rejected {{ $rejected > 0 ? 'has-count' : '' }}" title="Rejected Submissions: {{ $rejected }}">
                                    <span class="cl-pipe-pill cl-pipe-num pill-rejected">{{ $rejected }}</span>
                                    <span class="cl-pipe-label" title="Rejected">Rejected</span>
                                </div>
                                <div class="cl-pipe-divider" aria-hidden="true"></div>
                                <div class="cl-pipe-step step-interview {{ $all_interview > 0 ? 'has-count' : '' }}" title="Interviews Conducted: {{ $all_interview }} candidates">
                                    <span class="cl-pipe-pill cl-pipe-num pill-interview">{{ $all_interview }}</span>
                                    <span class="cl-pipe-label" title="Interviews Conducted">Interview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM STRIP: Connected Date, Last Activity, Submissions Badge & Actions -->
                <div class="cl-card-bottom-strip">
                    <div class="cl-bottom-meta">
                        <span class="cl-meta-item" title="Partnership agreement establishment date">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>Partnered: <strong>{{ $newDate ?: 'Recently' }}</strong></span>
                        </span>
                        <span class="cl-meta-dot">•</span>
                        <span class="cl-meta-item" title="Recency of candidate submissions or workflow activity">
                            <span class="cl-dot cl-dot-green"></span>
                            <span>Activity: <strong>{{ $timeAgo ?: 'Active this week' }}</strong></span>
                        </span>
                        <span class="cl-meta-dot">•</span>
                        <span class="cl-meta-item cl-meta-apps" title="Total candidate resumes submitted by this agency across all shared jobs">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Submissions:</span>
                            <span class="cl-apps-badge {{ $all_candidates > 0 ? 'has-apps' : '' }}">{{ $all_candidates }}</span>
                        </span>
                    </div>
                    <div class="cl-bottom-actions">
                        <button type="button" class="btn btn-sm btn-light-primary fw-bold px-3 py-1.5 {{ $client_type_css }}" data-row-id="{{ $row->client_id }}" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; white-space: nowrap !important; line-height: 1 !important; font-size: 11px; border-radius: 6px;" title="Open agency profile drawer, contacts, jobs, and candidates">
                            <span>View Agency</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block; vertical-align: middle; flex-shrink: 0;"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="cl-empty-state">
            <div class="cl-empty-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            <div class="cl-empty-title">No agencies found</div>
            <div class="cl-empty-desc">No agencies match your selected filters or search query. Try clearing filters or searching with different keywords.</div>
        </div>
    @endif
@endif
