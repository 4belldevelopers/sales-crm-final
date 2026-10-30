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

                // Role pill
                $roleName = ucwords(strtolower(@$row->display_name ?? 'Recruiter'));
                $roleLower = strtolower($roleName);
                if (str_contains($roleLower, 'admin'))     { $rolePill = 'role-admin'; }
                elseif (str_contains($roleLower, 'lead'))  { $rolePill = 'role-lead'; }
                else                                       { $rolePill = 'role-default'; }

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
                    'status' => strtolower($row->status ?? 'active'),
                    'last_active' => $lastActive,
                    'channels' => $channels,
                    'priorities' => $priorities,
                ];
            @endphp

            <!-- Primary Team Member Card (Rule 20 Enterprise Card + Sub-Card System) -->
            <div class="team-card js-team-card" 
                 data-user-id="{{ $row->id }}" 
                 data-is-client-rep="{{ $isClientRep ? '1' : '0' }}" 
                 data-is-agency-rep="{{ $isAgencyRep ? '1' : '0' }}"
                 style="border-left: 3.5px solid {{ $cardAccent }};">
                
                <!-- Discrete Sub-Cards Grid (Aligned with Modern 8-Column Roster Standard) -->
                <div class="team-card-grid">
                    
                    <!-- Sub-Card 1: Member Identity -->
                    <div class="tm-cell tm-cell-member">
                        <div class="tm-avatar {{ $avatarClass }}" title="{{ $rawName }}">
                            @if (!empty($row->image))
                                <img src="{{ $row->image }}" alt="{{ $rawName }}">
                            @else
                                {{ strtoupper($strVal) }}
                            @endif
                        </div>
                        <div class="d-flex flex-column overflow-hidden min-w-0">
                            <span class="tm-member-name" title="{{ ucwords(strtolower($rawName)) }}">
                                {{ ucwords(strtolower(mb_substr($rawName, 0, 24))) }}
                            </span>
                            @if ($row->root_user == 1)
                                <span class="tm-owner-tag" title="Organization Owner">
                                    <i class="fa-solid fa-crown text-warning me-1"></i>Organization Owner
                                </span>
                            @endif
                            <span class="tm-member-email" title="{{ $row->email }}">
                                {{ $row->email }}
                            </span>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Rank & Score -->
                    <div class="tm-cell tm-cell-rank">
                        <div class="tm-rank-val">
                            {!! $medal !!} <strong>{{ $teamRanks->rank }}</strong>
                            @if (!empty($teamRanks->rankMonth))
                                <span class="tm-rank-month">{{ $teamRanks->rankMonth }}</span>
                            @endif
                        </div>
                        <button type="button" class="tm-btn-view-score teamViewScore" data-user-id="{{ $row->id }}" title="Click to view detailed performance scoreboard">
                            View Score
                        </button>
                    </div>

                    <!-- Sub-Card 3: Workload -->
                    <div class="tm-cell tm-cell-workload">
                        <div class="tm-wl-row" title="Active Jobs Assigned">
                            <i class="fa-solid fa-briefcase text-primary me-1"></i><strong>{{ $activeJobs }}</strong> Active Jobs
                        </div>
                        <div class="tm-wl-row" title="SPOC / Captain on Jobs">
                            <i class="fa-solid fa-crown text-purple me-1"></i><strong>{{ $captainJobs }}</strong> Captain Jobs
                        </div>
                        <div class="tm-wl-row" title="Bench Talents Managed (Relationship Manager)">
                            <i class="fa-solid fa-users text-success me-1"></i><strong>{{ $trmTalents }}</strong> Relationship Manager(Bench)
                        </div>
                    </div>

                    <!-- Sub-Card 4: Role & Permissions -->
                    <div class="tm-cell tm-cell-role">
                        <span class="tm-role-badge team-role-pill {{ $rolePill }}" title="Assigned system role">
                            {{ $roleName }}
                        </span>
                        @if ($row->root_user != 1)
                            <button type="button" class="tm-btn-role-edit update-user-role" data-row-id="{{ $row->id }}" title="Edit Role">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Sub-Card 5: Corporate Communication Purpose -->
                    <div class="tm-cell tm-cell-communication">
                        @if (!empty($channels) && count($channels) > 0)
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                @if (in_array('client', $channels, true))
                                    @php $p = $priorities['client'] ?? 'Primary'; @endphp
                                    <span class="tm-comm-chip client" title="{{ $p }} contact for Client &amp; Sales">
                                        <i class="fa-solid fa-users me-1"></i>Client &amp; Sales
                                    </span>
                                @endif
                                @if (in_array('vendor', $channels, true) || in_array('agency', $channels, true))
                                    @php $p = $priorities['agency'] ?? ($priorities['vendor'] ?? 'Primary'); @endphp
                                    <span class="tm-comm-chip vendor" title="{{ $p }} contact for Agency">
                                        <i class="fa-solid fa-shapes me-1"></i>Agency
                                    </span>
                                @endif
                                @if (in_array('recruitment', $channels, true))
                                    @php $p = $priorities['recruitment'] ?? 'Primary'; @endphp
                                    <span class="tm-comm-chip recruitment" title="{{ $p }} contact for Recruitment / Talent">
                                        <i class="fa-solid fa-id-card-clip me-1"></i>Recruitment
                                    </span>
                                @endif
                                @if (in_array('finance', $channels, true))
                                    @php $p = $priorities['finance'] ?? 'Primary'; @endphp
                                    <span class="tm-comm-chip finance" title="{{ $p }} contact for Billing &amp; Invoicing">
                                        <i class="fa-solid fa-file-invoice-dollar me-1"></i>Billing &amp; Invoicing
                                    </span>
                                @endif
                                <button type="button" class="tm-btn-edit-comm" data-user='@json($userData)' onclick="openAssignCommDrawer(this)" title="Edit communication channels &amp; priorities">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                        @else
                            <button type="button" class="tm-btn-add-comm" data-user='@json($userData)' onclick="openAssignCommDrawer(this)" title="Assign corporate communication channels to this member">
                                <i class="fa-solid fa-plus me-1 text-primary"></i>Assign Purpose
                            </button>
                        @endif
                    </div>

                    <!-- Sub-Card 6: Status Toggle -->
                    <div class="tm-cell tm-cell-status">
                        @if (!empty($row->password))
                            <div class="team-status-cell">
                                <label class="tm-switch">
                                    <input type="checkbox" class="chkStatus" id="status_change_{{ $row->id }}" value="active"
                                        {{ $row->status == 'active' ? 'checked' : '' }}
                                        onclick="handleClick(this, {{ $row->id }});"
                                        {{ $row->root_user == 1 ? 'disabled' : '' }}>
                                    <span class="tm-slider"></span>
                                </label>
                                <span class="tm-status-text status_text {{ $row->status == 'active' ? 'text-success' : 'text-muted' }} ms-2">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </div>
                        @else
                            <a href="javascript:;" class="text-primary fw-semibold fs-8" onclick="reSendhandleClick(this, {{ $row->id }});">
                                <i class="fa-solid fa-arrows-rotate me-1"></i>Resend Invite
                            </a>
                        @endif
                    </div>

                    <!-- Sub-Card 7: Last Active -->
                    <div class="tm-cell tm-cell-lastactive">
                        <span class="tm-dot {{ $dotClass }}"></span>
                        <span>{{ $lastActive }}</span>
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
