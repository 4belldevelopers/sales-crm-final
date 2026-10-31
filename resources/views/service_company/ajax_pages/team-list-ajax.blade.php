@php
    use Carbon\Carbon;
@endphp
@if (count($data) > 0)
    <div class="team-cards-container">
        @foreach ($data as $rowArr)
            @php
                $row = (object) $rowArr;

                // Rank & Scores
                $rawRank = $row->pre_rank ?? $row->rank ?? 'N/A';
                $isInvited = empty($row->password) || strtolower($row->status ?? '') === 'invited';
                $isRanked = ! $isInvited && is_numeric($rawRank) && (int) $rawRank > 0 && strtolower($row->status ?? '') === 'active';
                $rankDisplay = $isRanked ? '#' . (int) $rawRank : 'N/A';
                $rankMonthDisplay = $isRanked ? '(' . ($row->pre_rank_month ?? Carbon::now()->startOfMonth()->subMonth()->format('M Y')) . ')' : '';
                $scoreVal = $isRanked ? ($row->pre_score ?? $row->score ?? 0) : 0;
                $teamRanks = (object) ['rank' => $rankDisplay, 'score' => $scoreVal, 'rankMonth' => $rankMonthDisplay];

                // Avatar initials & background tint
                $rawName = trim(@$row->name ?? 'User');
                $strVal = strtolower($rawName[0] ?? 'u');
                $ascii = ord($strVal);
                if ($ascii >= 97 && $ascii <= 101)      { $avatarClass = 'bg-light-danger text-danger'; }
                elseif ($ascii >= 102 && $ascii <= 106) { $avatarClass = 'bg-light-warning text-warning'; }
                elseif ($ascii >= 107 && $ascii <= 111) { $avatarClass = 'bg-light-primary text-primary'; }
                elseif ($ascii >= 112 && $ascii <= 116) { $avatarClass = 'bg-light-success text-success'; }
                elseif ($ascii >= 117 && $ascii <= 121) { $avatarClass = 'bg-dark text-white'; }
                else                                    { $avatarClass = 'bg-light-secondary text-dark'; }

                // Role pill & System Role fallback
                $roleName = ucwords(strtolower(@$row->display_name ?? (@$row->role_name ?? 'Team Member')));
                $roleLower = strtolower($roleName);
                if (str_contains($roleLower, 'admin'))     { $rolePill = 'role-admin'; }
                elseif (str_contains($roleLower, 'lead'))  { $rolePill = 'role-lead'; }
                else                                       { $rolePill = 'role-default'; }

                // Designation Priority: 1. User Registration Designation -> 2. System Role fallback
                $userDesignation = !empty($row->designation) ? trim($row->designation) : '';
                $designation = !empty($userDesignation) ? $userDesignation : $roleName;

                // Pre-computed metrics
                $activeJobs   = (int) ($row->pre_active_jobs ?? 0);
                $captainJobs  = (int) ($row->pre_captain_jobs ?? 0);
                $trmTalents   = (int) ($row->pre_trm_talents ?? 0);
                $agencyShares = (int) ($row->pre_agency_shares ?? 0);

                // Communication Representative & Channel Roles
                $isClientRep = !empty($row->is_client_rep);
                $isAgencyRep = !empty($row->is_agency_rep);
                $channels = (array) ($row->channels ?? []);
                if ($isClientRep && !in_array('client', $channels, true)) {
                    $channels[] = 'client';
                }
                if ($isAgencyRep && !in_array('vendor', $channels, true)) {
                    $channels[] = 'vendor';
                }

                // Rank medal
                $medal = match ((int) @$teamRanks->rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => '' };

                // Last active relative timestamp + freshness dot
                $lastActive = 'Never logged in';
                $dotClass = 'dot-gray';
                if (!empty($row->last_login)) {
                    try {
                        $la = Carbon::parse($row->last_login);
                        $lastActive = $la->diffForHumans();
                        $mins = $la->diffInMinutes(Carbon::now());
                        $dotClass = $mins <= 60 ? 'dot-green' : ($mins <= 1440 ? 'dot-amber' : 'dot-gray');
                    } catch (\Throwable $e) {}
                }

                // Phone formatting
                $phone = '';
                if (!empty($row->mobile)) {
                    $phone = (!empty($row->calling_code) ? '+' . ltrim($row->calling_code, '+') . ' ' : '') . $row->mobile;
                }

                // Card accent color
                $cardAccent = '#cbd5e1';
                if (strtolower($row->status ?? '') === 'active') {
                    $cardAccent = ($isClientRep || $isAgencyRep) ? '#009ef7' : '#10b981';
                } elseif ($isInvited) {
                    $cardAccent = '#f59e0b';
                }

                // Prepare user payload for the Assign Communication Contacts Right Drawer
                $priorities = (array) ($row->priorities ?? []);
                $userData = [
                    'id' => (int) $row->id,
                    'name' => $rawName,
                    'email' => (string) ($row->email ?? ''),
                    'phone' => (string) $phone,
                    'image' => (string) ($row->image ?? ''),
                    'initial' => strtoupper($strVal),
                    'avatar_class' => $avatarClass,
                    'role' => $roleName,
                    'designation' => $designation,
                    'status' => strtolower($row->status ?? 'active'),
                    'last_active' => $lastActive,
                    'channels' => $channels,
                    'priorities' => $priorities,
                ];
            @endphp

            <!-- Primary Team Member Card (Rule 20 Enterprise Card + Sub-Card Design) -->
            <div class="tm-primary-card js-team-card" 
                 data-user-id="{{ $row->id }}" 
                 data-is-client-rep="{{ $isClientRep ? '1' : '0' }}" 
                 data-is-agency-rep="{{ $isAgencyRep ? '1' : '0' }}"
                 style="border-left: 3.5px solid {{ $cardAccent }};">
                
                <!-- Discrete 5 Upper Sub-Cards Grid (Strictly 1 Row on 15.6") -->
                <div class="tm-subcards-grid">
                    
                    <!-- SUB-CARD 1: Member Identity (Name 1st row, Designation 2nd row, Email 3rd row) -->
                    <div class="tm-subcard tm-subcard-identity">
                        <div class="tm-subcard-head">
                            <div class="tm-subcard-title-wrap">
                                <svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span class="tm-subcard-title">Member Identity</span>
                            </div>
                            @if ($row->root_user == 1)
                                <span class="tm-subcard-badge badge-owner" title="Organization Owner">
                                    <i class="fa-solid fa-crown text-warning me-1"></i>Owner
                                </span>
                            @else
                                <span class="tm-subcard-badge badge-member">
                                    • Member
                                </span>
                            @endif
                        </div>
                        <div class="tm-identity-body">
                            <div class="tm-avatar {{ $avatarClass }} show-detail-team" data-row-id="{{ $row->id }}" role="button" tabindex="0" title="View {{ $rawName }} Profile" style="cursor: pointer;">
                                @if (!empty($row->image))
                                    <img src="{{ $row->image }}" alt="{{ $rawName }}">
                                @else
                                    {{ strtoupper($strVal) }}
                                @endif
                            </div>
                            <div class="tm-identity-details">
                                {{-- 1st Row: Member Name --}}
                                <span class="tm-member-name show-detail-team" data-row-id="{{ $row->id }}" role="button" tabindex="0" title="View {{ ucwords(strtolower($rawName)) }} Profile">
                                    {{ ucwords(strtolower(mb_substr($rawName, 0, 24))) }}
                                </span>

                                {{-- 2nd Row: Designation just below user name (Priority: Designation -> Role) --}}
                                <div class="tm-member-designation" title="{{ !empty($userDesignation) ? 'Designation: ' . $userDesignation : 'System Role: ' . $roleName }}">
                                    <i class="fa-solid fa-id-badge text-muted"></i>
                                    <span>{{ ucwords(strtolower(mb_substr($designation, 0, 26))) }}</span>
                                </div>

                                {{-- 3rd Row: Email ID --}}
                                <div class="tm-member-email-wrap">
                                    <i class="fa-regular fa-envelope text-muted"></i>
                                    <a href="mailto:{{ $row->email }}" class="tm-member-email-link" title="{{ $row->email }}">
                                        {{ mb_substr($row->email, 0, 26) }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 2: Dedicated System Role Card (Change Role Directly from Here) -->
                    <div class="tm-subcard tm-subcard-role">
                        <div class="tm-subcard-head">
                            <div class="tm-subcard-title-wrap">
                                <svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span class="tm-subcard-title">System Role</span>
                            </div>
                            @if ($row->root_user == 1)
                                <span class="tm-subcard-badge badge-owner">Full Access</span>
                            @else
                                <span class="tm-subcard-badge badge-role">Editable</span>
                            @endif
                        </div>
                        <div class="tm-role-body">
                            <div class="tm-role-badge-row">
                                <span class="tm-role-badge team-role-pill {{ $rolePill }}" title="Assigned system role">
                                    <i class="fa-solid fa-shield-halved me-1"></i>{{ $roleName }}
                                </span>
                            </div>
                            <div class="tm-role-action-row">
                                @if ($row->root_user != 1)
                                    <button type="button" class="btn-change-role update-user-role" data-row-id="{{ $row->id }}" title="Change role and permissions directly from here">
                                        <i class="fa-solid fa-user-gear me-1 text-primary"></i>Change Role
                                    </button>
                                @else
                                    <span class="tm-role-locked-note" title="Primary owner role is authoritative and cannot be changed">
                                        <i class="fa-solid fa-lock text-muted"></i>Owner Role
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 3: Workload & Ops -->
                    <div class="tm-subcard tm-subcard-workload">
                        <div class="tm-subcard-head">
                            <div class="tm-subcard-title-wrap">
                                <svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                                <span class="tm-subcard-title">Workload &amp; Ops</span>
                            </div>
                            <span class="tm-subcard-badge badge-count">{{ $activeJobs + $captainJobs + $trmTalents }} Ops</span>
                        </div>
                        <div class="tm-workload-body">
                            <div class="tm-wl-row" title="Active Jobs Assigned">
                                <i class="fa-solid fa-briefcase text-primary me-1"></i><span><strong>{{ $activeJobs }}</strong> Active Jobs</span>
                            </div>
                            <div class="tm-wl-row" title="SPOC / Captain on Jobs">
                                <i class="fa-solid fa-crown text-purple me-1"></i><span><strong>{{ $captainJobs }}</strong> Captain Jobs</span>
                            </div>
                            <div class="tm-wl-row" title="Bench Talents Managed (Bench Talent RM)">
                                <i class="fa-solid fa-users text-success me-1"></i><span><strong>{{ $trmTalents }}</strong> Bench Talent RM</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 4: Dedicated Account Status Card (3 Clear Rows per Rule 20) -->
                    <div class="tm-subcard tm-subcard-status">
                        <div class="tm-subcard-head">
                            <div class="tm-subcard-title-wrap">
                                <svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                    <line x1="12" y1="2" x2="12" y2="12"/>
                                </svg>
                                <span class="tm-subcard-title">Account Status</span>
                            </div>
                        </div>
                        <div class="tm-status-body">
                            @if (!empty($row->password))
                                {{-- Row 1: Toggle / status control icon --}}
                                <div class="tm-status-row tm-status-row-control">
                                    <label class="ios-switch" title="Toggle active status">
                                        <input type="checkbox" class="chkStatus" id="status_change_{{ $row->id }}" value="active"
                                            {{ $row->status == 'active' ? 'checked' : '' }}
                                            onclick="handleClick(this, {{ $row->id }});"
                                            {{ $row->root_user == 1 ? 'disabled' : '' }}>
                                        <span class="ios-slider"></span>
                                    </label>
                                </div>
                                
                                {{-- Row 2: Active / Inactive status --}}
                                <div class="tm-status-row tm-status-row-label">
                                    <span class="tm-status-text status_text {{ $row->status == 'active' ? 'text-success' : 'text-muted' }}">
                                        {{ ucfirst($row->status ?? 'Active') }}
                                    </span>
                                </div>

                                {{-- Row 3: Supporting status information or text --}}
                                <div class="tm-status-row tm-status-row-sub">
                                    @if ($row->root_user == 1)
                                        <span class="tm-status-subnote">Owner (Always Active)</span>
                                    @else
                                        <span class="tm-status-subnote">Click switch to toggle</span>
                                    @endif
                                </div>
                            @else
                                {{-- Row 1: Toggle / status control icon --}}
                                <div class="tm-status-row tm-status-row-control">
                                    <button type="button" class="btn-resend-invite-icon" onclick="reSendhandleClick(this, {{ $row->id }});" title="Resend invitation email">
                                        <i class="fa-solid fa-paper-plane text-warning" style="font-size: 11px;"></i>
                                    </button>
                                </div>

                                {{-- Row 2: Active / Inactive status --}}
                                <div class="tm-status-row tm-status-row-label">
                                    <span class="tm-status-text text-warning">Awaiting Signup</span>
                                </div>

                                {{-- Row 3: Supporting status information or text --}}
                                <div class="tm-status-row tm-status-row-sub">
                                    <a href="javascript:;" class="btn-resend-invite" onclick="reSendhandleClick(this, {{ $row->id }});" title="Resend invitation email">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Resend Invite
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SUB-CARD 5: Contact Details Visibility (Marketplace Enquiries) -->
                    <div class="tm-subcard tm-subcard-comm">
                        <div class="tm-subcard-head">
                            <div class="tm-subcard-title-wrap">
                                <i class="fa-solid fa-address-book text-primary" style="font-size: 11px;"></i>
                                <span class="tm-subcard-title">Contact Details Visibility</span>
                            </div>
                            @if (!empty($channels) && count($channels) > 0)
                                <span class="tm-subcard-badge badge-rep" title="Publicly listed for {{ count($channels) }} enquiry types">
                                    <i class="fa-solid fa-eye text-success me-1"></i>Public ({{ count($channels) }})
                                </span>
                            @else
                                <span class="tm-subcard-badge badge-role" title="Contact details kept private">
                                    <i class="fa-solid fa-eye-slash me-1"></i>Private
                                </span>
                            @endif
                        </div>
                        <div class="tm-comm-body">
                            @if (!empty($channels) && count($channels) > 0)
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    @if (in_array('client', $channels, true))
                                        <span class="tm-comm-chip client" title="Contact details shared for Sales &amp; Client Enquiries">
                                            <i class="fa-solid fa-phone me-1"></i>Sales &amp; Client Enquiries
                                        </span>
                                    @endif
                                    @if (in_array('vendor', $channels, true) || in_array('agency', $channels, true))
                                        <span class="tm-comm-chip vendor" title="Contact details shared for Agency Enquiries">
                                            <i class="fa-solid fa-phone me-1"></i>Agency Enquiries
                                        </span>
                                    @endif
                                    @if (in_array('recruitment', $channels, true) || in_array('bench', $channels, true))
                                        <span class="tm-comm-chip recruitment" title="Contact details shared for Bench Talent Enquiries">
                                            <i class="fa-solid fa-phone me-1"></i>Bench Talent Enquiries
                                        </span>
                                    @endif
                                    <button type="button" class="btn-edit-comm-icon" data-user='@json($userData)' onclick="openAssignCommDrawer(this)" title="Edit contact sharing channels">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-between w-100 gap-1 flex-wrap">
                                    <span class="text-muted" style="font-size: 10.5px; font-weight: 500;">
                                        <i class="fa-solid fa-lock text-muted me-1"></i> No Details Shared (Keep Private)
                                    </span>
                                    <button type="button" class="btn-assign-comm-empty" data-user='@json($userData)' onclick="openAssignCommDrawer(this)" title="Share contact details for marketplace enquiries">
                                        <i class="fa-solid fa-plus me-1 text-primary"></i>Share Details
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- SUB-CARD 6: BOTTOM ACTIVITY & PERFORMANCE STRIP (Single Row Standard) -->
                <div class="tm-card-bottom-strip">
                    <div class="tm-bottom-left-meta">
                        <span class="tm-meta-item" title="Registration Date">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>Joined: <strong>{{ !empty($row->created_at) ? Carbon::parse($row->created_at)->format('d M, Y') : 'Recently' }}</strong></span>
                        </span>
                        <span class="tm-meta-dot">•</span>
                        <span class="tm-meta-item" title="Last Login Freshness">
                            <span class="dot-pulse {{ $dotClass }}"></span>
                            <span>Last active: <strong>{{ $lastActive }}</strong></span>
                        </span>
                        @if (!empty($phone))
                            <span class="tm-meta-dot">•</span>
                            <span class="tm-meta-item" title="Direct Phone">
                                <i class="fa-solid fa-phone" style="font-size: 10px;"></i>
                                <span>{{ $phone }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="tm-bottom-right-group">
                        <!-- Performance Score & Rank (Swapped to bottom strip) -->
                        <div class="tm-perf-strip-box">
                            <span class="tm-perf-rank" title="Performance Rank">
                                {!! $medal !!} <strong>{{ $teamRanks->rank }}</strong>
                                @if (!empty($teamRanks->rankMonth))
                                    <span class="tm-perf-month">{{ $teamRanks->rankMonth }}</span>
                                @endif
                            </span>
                            @if ($isRanked)
                                <span class="tm-meta-dot">•</span>
                                <span class="tm-perf-score" title="Score Points">Score: <strong>{{ $teamRanks->score }} pts</strong></span>
                            @endif
                            <button type="button" class="btn-view-score teamViewScore" data-user-id="{{ $row->id }}" title="Click to view detailed performance scoreboard">
                                <i class="fa-solid fa-chart-line me-1"></i>View Score
                            </button>
                        </div>

                        <button type="button" class="btn-view-profile show-detail-team" data-row-id="{{ $row->id }}" title="View Full Profile">
                            <span>View Profile</span>
                            <i class="fa-solid fa-arrow-right" style="font-size: 9px;"></i>
                        </button>
                    </div>
                </div>

            </div>
        @endforeach

        <div class="d-flex align-items-center justify-content-between pt-2 pb-1 px-1 text-muted fs-8">
            <div>Showing 1 to {{ count($data) }} of {{ $total ?? count($data) }} members</div>
        </div>
    </div>
@else
    <div class="team-empty-card">
        <i class="fa-solid fa-user-group fa-3x mb-3 text-muted"></i>
        <div class="fw-bold fs-6 text-gray-800 mb-1">No Team Members Found</div>
        <div class="text-muted fs-7">No team members match the selected filter criteria.</div>
    </div>
@endif
