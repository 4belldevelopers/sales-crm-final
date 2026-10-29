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

                // Communication Representative status
                $isClientRep = !empty($row->is_client_rep);
                $isAgencyRep = !empty($row->is_agency_rep);

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
            @endphp

            <!-- Primary Team Member Card -->
            <div class="team-card js-team-card" 
                 data-user-id="{{ $row->id }}" 
                 data-is-client-rep="{{ $isClientRep ? '1' : '0' }}" 
                 data-is-agency-rep="{{ $isAgencyRep ? '1' : '0' }}"
                 style="border-left: 3.5px solid {{ $cardAccent }};">
                
                <!-- Upper Discrete Sub-Cards Grid (5 Sections) -->
                <div class="team-card-grid">
                    
                    <!-- Sub-Card 1: Member Identity & Contact -->
                    <div class="tm-subcard tm-subcard-profile">
                        <div class="tm-subcard-header">
                            <span class="tm-subcard-label"><i class="fa-regular fa-user me-1 text-muted"></i>Member</span>
                            @if(!$isInvited)
                                <span class="tm-badge-status {{ strtolower($row->status ?? '') === 'active' ? 'status-active' : 'status-inactive' }}">
                                    {{ strtolower($row->status ?? '') === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            @else
                                <span class="tm-badge-status status-invited">Invited</span>
                            @endif
                        </div>
                        <div class="tm-subcard-body">
                            <div class="d-flex align-items-center">
                                <div class="team-avatar {{ $avatarClass }} show-detail-team me-3" data-row-id="{{ $row->id }}" style="cursor:pointer;" title="View Profile">
                                    @if (!empty($row->image))
                                        <img src="{{ $row->image }}" alt="{{ $rawName }}">
                                    @else
                                        {{ strtoupper($strVal) }}
                                    @endif
                                </div>
                                <div class="d-flex flex-column overflow-hidden">
                                    <a href="javascript:void(0);" class="team-member-name show-detail-team" data-row-id="{{ $row->id }}" title="{{ ucwords(strtolower($rawName)) }}">
                                        {{ ucwords(strtolower(mb_substr($rawName, 0, 24))) }}
                                    </a>
                                    @if (!empty($row->designation))
                                        <span class="team-member-role" title="{{ $row->designation }}">{{ ucwords(strtolower($row->designation)) }}</span>
                                    @else
                                        <span class="team-member-role text-muted">Team Member</span>
                                    @endif
                                    <span class="team-member-mail" title="{{ $row->email }}">
                                        <i class="fa-regular fa-envelope me-1"></i>{{ $row->email }}
                                    </span>
                                    @if(!empty($phone))
                                        <span class="team-member-phone" title="{{ $phone }}">
                                            <i class="fa-solid fa-phone me-1"></i>{{ $phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Communication Representation Role & One-Click Actions -->
                    <div class="tm-subcard tm-subcard-rep">
                        <div class="tm-subcard-header">
                            <span class="tm-subcard-label"><i class="fa-solid fa-bullhorn me-1 text-primary"></i>Representation</span>
                            @if($isClientRep && $isAgencyRep)
                                <span class="badge-rep-pill badge-rep-dual" title="Default representative for both Clients & Agencies"><i class="fa-solid fa-star me-1"></i>Dual Rep</span>
                            @elseif($isClientRep)
                                <span class="badge-rep-pill badge-rep-client" title="Default representative for Client communication"><i class="fa-solid fa-user-tie me-1"></i>Client Rep</span>
                            @elseif($isAgencyRep)
                                <span class="badge-rep-pill badge-rep-agency" title="Default representative for Agency communication"><i class="fa-solid fa-building-user me-1"></i>Agency Rep</span>
                            @else
                                <span class="badge-rep-pill badge-rep-standard">Standard</span>
                            @endif
                        </div>
                        <div class="tm-subcard-body">
                            <!-- Scope explanation in layman terms -->
                            <div class="team-rep-scope-box">
                                @if($isClientRep && $isAgencyRep)
                                    <div class="rep-scope-desc text-primary fw-semibold">
                                        <i class="fa-solid fa-check-double me-1 text-success"></i>Primary Face for Clients &amp; Agencies
                                    </div>
                                    <div class="rep-scope-hint">Shown on direct client views and shared agency jobs.</div>
                                @elseif($isClientRep)
                                    <div class="rep-scope-desc text-primary fw-semibold">
                                        <i class="fa-solid fa-circle-check me-1 text-success"></i>Client Communication Face
                                    </div>
                                    <div class="rep-scope-hint">Shown to Direct Clients on requisitions &amp; submissions.</div>
                                @elseif($isAgencyRep)
                                    <div class="rep-scope-desc text-success fw-semibold">
                                        <i class="fa-solid fa-circle-check me-1 text-success"></i>Agency Communication Face
                                    </div>
                                    <div class="rep-scope-hint">Default contact shown to Partner Agencies on invitations.</div>
                                @else
                                    <div class="rep-scope-desc text-muted">
                                        @if($agencyShares > 0)
                                            <i class="fa-solid fa-diagram-project me-1 text-primary"></i>Represents <strong>{{ $agencyShares }}</strong> shared agency {{ $agencyShares === 1 ? 'job' : 'jobs' }}
                                        @else
                                            <i class="fa-regular fa-circle-dot me-1 text-muted"></i>Internal team contributor
                                        @endif
                                    </div>
                                    <div class="rep-scope-hint">Not assigned as default company representative.</div>
                                @endif
                            </div>

                            <!-- Simple Layman Action Buttons -->
                            <div class="team-rep-btn-row">
                                @if(!$isClientRep)
                                    <button type="button" class="btn-rep-toggle btn-rep-client" onclick="setRepresentative({{ $row->id }}, 'client', '{{ addslashes($rawName) }}')" title="Make this user the company face shown to Clients">
                                        <i class="fa-solid fa-user-check me-1"></i>Set as Client Rep
                                    </button>
                                @endif
                                @if(!$isAgencyRep)
                                    <button type="button" class="btn-rep-toggle btn-rep-agency" onclick="setRepresentative({{ $row->id }}, 'agency', '{{ addslashes($rawName) }}')" title="Make this user the company face shown to Agencies">
                                        <i class="fa-solid fa-building-user me-1"></i>Set as Agency Rep
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Workload & Requisition Scope -->
                    <div class="tm-subcard tm-subcard-workload">
                        <div class="tm-subcard-header">
                            <span class="tm-subcard-label"><i class="fa-solid fa-briefcase me-1 text-muted"></i>Workload &amp; Reach</span>
                        </div>
                        <div class="tm-subcard-body">
                            <div class="tm-workload-grid">
                                <div class="tm-wl-cell" title="Active Jobs Assigned">
                                    <span class="tm-wl-val"><i class="fa-solid fa-briefcase text-primary me-1"></i>{{ $activeJobs }}</span>
                                    <span class="tm-wl-lbl">Active Jobs</span>
                                </div>
                                <div class="tm-wl-cell" title="SPOC / Captain on Jobs">
                                    <span class="tm-wl-val text-purple"><i class="fa-solid fa-crown text-purple me-1"></i>{{ $captainJobs }}</span>
                                    <span class="tm-wl-lbl">Captain SPOC</span>
                                </div>
                                <div class="tm-wl-cell" title="Bench Talents Managed">
                                    <span class="tm-wl-val text-success"><i class="fa-solid fa-users text-success me-1"></i>{{ $trmTalents }}</span>
                                    <span class="tm-wl-lbl">TRM Talents</span>
                                </div>
                                <div class="tm-wl-cell" title="Jobs Shared with Partner Agencies">
                                    <span class="tm-wl-val text-indigo"><i class="fa-solid fa-share-nodes text-indigo me-1"></i>{{ $agencyShares }}</span>
                                    <span class="tm-wl-lbl">Agency Shares</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 4: Performance & Rank -->
                    <div class="tm-subcard tm-subcard-rank">
                        <div class="tm-subcard-header">
                            <span class="tm-subcard-label"><i class="fa-solid fa-chart-line me-1 text-muted"></i>Performance</span>
                        </div>
                        <div class="tm-subcard-body">
                            <div class="d-flex flex-column align-items-start">
                                <div class="tm-rank-val">
                                    {!! $medal !!} <strong>{{ $teamRanks->rank }}</strong>
                                    @if (!empty($teamRanks->rankMonth))
                                        <span class="tm-rank-month">{{ $teamRanks->rankMonth }}</span>
                                    @endif
                                </div>
                                <span class="team-score-link teamViewScore mt-2" data-user-id="{{ $row->id }}" title="Click to view detailed performance scoreboard">
                                    <i class="fa-solid fa-trophy me-1 text-warning"></i>Score: <strong>{{ $scoreVal }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 5: Role & Status Controls -->
                    <div class="tm-subcard tm-subcard-controls">
                        <div class="tm-subcard-header">
                            <span class="tm-subcard-label"><i class="fa-solid fa-shield-halved me-1 text-muted"></i>Access &amp; Role</span>
                        </div>
                        <div class="tm-subcard-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="team-role-pill {{ $rolePill }}" title="Assigned system role">
                                    {{ $roleName }}
                                </span>
                                @if ($row->root_user != 1)
                                    <button type="button" class="team-role-edit-btn update-user-role" data-row-id="{{ $row->id }}" title="Edit Role">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-1">
                                @if (!empty($row->password))
                                    <div class="team-status-cell">
                                        <label class="team-switch">
                                            <input type="checkbox" class="chkStatus" value="active"
                                                {{ $row->status == 'active' ? 'checked' : '' }}
                                                onclick="handleClick(this, {{ $row->id }});"
                                                {{ $row->root_user == 1 ? 'disabled' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <span class="team-status-text status_text {{ $row->status == 'active' ? 'text-success' : 'text-danger' }} fw-bold ms-2">
                                            {{ $row->status == 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                @else
                                    <a href="javascript:;" class="text-primary fw-semibold fs-8" onclick="reSendhandleClick(this, {{ $row->id }});">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Resend Invite
                                    </a>
                                @endif

                                <div class="team-action-wrap">
                                    <button class="team-action-btn" onclick="toggleTeamMenu(this)" title="More Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="team-action-menu">
                                        <a class="team-action-item show-detail-team" data-row-id="{{ $row->id }}">
                                            <i class="fa-regular fa-id-card me-2"></i>View Profile
                                        </a>
                                        @if ($row->root_user != 1)
                                            <a class="team-action-item update-user-role" data-row-id="{{ $row->id }}">
                                                <i class="fa-solid fa-user-shield me-2"></i>Edit Role
                                            </a>
                                            @if (empty($row->password))
                                                <a class="team-action-item" onclick="reSendhandleClick(this, {{ $row->id }});">
                                                    <i class="fa-solid fa-paper-plane me-2"></i>Resend Invitation
                                                </a>
                                            @endif
                                        @endif
                                        <a class="team-action-item" onclick="setRepresentative({{ $row->id }}, 'client', '{{ addslashes($rawName) }}')">
                                            <i class="fa-solid fa-user-tie me-2 text-primary"></i>Set as Client Representative
                                        </a>
                                        <a class="team-action-item" onclick="setRepresentative({{ $row->id }}, 'agency', '{{ addslashes($rawName) }}')">
                                            <i class="fa-solid fa-building-user me-2 text-success"></i>Set as Agency Representative
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Activity & Attributions Strip -->
                <div class="team-card-bottom">
                    <div class="tm-bottom-left">
                        <span class="tm-lastactive">
                            <span class="team-dot {{ $dotClass }}"></span>
                            Last Active: <strong>{{ $lastActive }}</strong>
                        </span>
                        @if ($row->root_user == 1)
                            <span class="tm-bullet">&bull;</span>
                            <span class="tm-root-badge"><i class="fa-solid fa-crown me-1 text-warning"></i>Organization Owner</span>
                        @endif
                    </div>
                    <div class="tm-bottom-right">
                        @if($agencyShares > 0)
                            <span class="tm-agency-share-pill">
                                <i class="fa-solid fa-diagram-project me-1 text-primary"></i>
                                <span>Authoritative Representative on <strong>{{ $agencyShares }}</strong> shared agency {{ $agencyShares === 1 ? 'job' : 'jobs' }}</span>
                                <span class="tm-bullet">&bull;</span>
                                <a href="javascript:void(0);" onclick="openAgencySharesModal({{ $row->id }}, '{{ addslashes($rawName) }}')" class="tm-inspect-link">
                                    Inspect Mapping
                                </a>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@else
    <div class="team-empty-card">
        <i class="fa-solid fa-user-group fa-3x mb-3 text-muted"></i>
        <div class="fw-bold fs-6 text-gray-800 mb-1">No Team Members Found</div>
        <div class="text-muted fs-7">No team members match the selected filter criteria.</div>
    </div>
@endif
