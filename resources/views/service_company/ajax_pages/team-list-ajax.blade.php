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

            <!-- Primary Team Member Card (Rule 20 Enterprise Card + Sub-Card System) -->
            <div class="team-card js-team-card" 
                 data-user-id="{{ $row->id }}" 
                 data-is-client-rep="{{ $isClientRep ? '1' : '0' }}" 
                 data-is-agency-rep="{{ $isAgencyRep ? '1' : '0' }}"
                 style="border-left: 3.5px solid {{ $cardAccent }};">
                
                <!-- Discrete Sub-Cards Grid (Aligned with Modern 8-Column Roster Standard) -->
                <div class="team-card-grid">
                    
                    <!-- Sub-Card 1: Member Identity -->
                    <div class="tm-subcard-cell tm-cell-member">
                        <div class="d-flex align-items-center gap-3">
                            <div class="team-avatar {{ $avatarClass }} show-detail-team" data-row-id="{{ $row->id }}" style="cursor:pointer;" title="View Profile">
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
                                <span class="team-member-mail" title="{{ $row->email }}">
                                    {{ $row->email }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Rank & Score -->
                    <div class="tm-subcard-cell tm-cell-rank">
                        <div class="d-flex flex-column align-items-start">
                            <div class="tm-rank-val">
                                {!! $medal !!} <strong>{{ $teamRanks->rank }}</strong>
                                @if (!empty($teamRanks->rankMonth))
                                    <span class="tm-rank-month">{{ $teamRanks->rankMonth }}</span>
                                @endif
                            </div>
                            <span class="badge-score-pill teamViewScore mt-1" data-user-id="{{ $row->id }}" title="Click to view detailed performance scoreboard">
                                View Score
                            </span>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Workload -->
                    <div class="tm-subcard-cell tm-cell-workload">
                        <div class="d-flex flex-column gap-1">
                            <span class="tm-wl-pill" title="Active Jobs Assigned">
                                <i class="fa-solid fa-briefcase text-primary me-1"></i><strong>{{ $activeJobs }}</strong> Active Jobs
                            </span>
                            <span class="tm-wl-pill" title="SPOC / Captain on Jobs">
                                <i class="fa-solid fa-crown text-purple me-1"></i><strong>{{ $captainJobs }}</strong> Captain Jobs
                            </span>
                            <span class="tm-wl-pill" title="Bench Talents Managed">
                                <i class="fa-solid fa-users text-success me-1"></i><strong>{{ $trmTalents }}</strong> TRM Talents
                            </span>
                        </div>
                    </div>

                    <!-- Sub-Card 4: Role & Permissions -->
                    <div class="tm-subcard-cell tm-cell-role">
                        <div class="d-flex align-items-center gap-1">
                            <span class="team-role-pill {{ $rolePill }}" title="Assigned system role">
                                {{ $roleName }}
                            </span>
                            @if ($row->root_user != 1)
                                <button type="button" class="team-role-edit-btn update-user-role" data-row-id="{{ $row->id }}" title="Edit Role">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 5: Marketplace Communication SPOC -->
                    <div class="tm-subcard-cell tm-cell-communication">
                        <div class="d-flex flex-column gap-1">
                            @if($isClientRep)
                                <div class="tm-comm-badge client" title="Designated contact for Client & Sales Communication">
                                    <i class="fa-solid fa-crown me-1 text-success"></i>Client &amp; Sales Contact
                                </div>
                            @else
                                <button type="button" class="btn-comm-action client" onclick="setRepresentative({{ $row->id }}, 'client', '{{ addslashes($rawName) }}')" title="Assign as default Client &amp; Sales Contact">
                                    <i class="fa-solid fa-building me-1 text-primary"></i>Set as Client &amp; Sales Contact
                                </button>
                            @endif

                            @if($isAgencyRep)
                                <div class="tm-comm-badge agency" title="Designated contact for Partner Agency Communication">
                                    <i class="fa-solid fa-crown me-1 text-purple"></i>Agency Contact
                                </div>
                            @else
                                <button type="button" class="btn-comm-action agency" onclick="setRepresentative({{ $row->id }}, 'agency', '{{ addslashes($rawName) }}')" title="Assign as default Agency Contact">
                                    <i class="fa-solid fa-users me-1 text-purple"></i>Set as Agency Contact
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 6: Status Toggle -->
                    <div class="tm-subcard-cell tm-cell-status">
                        @if (!empty($row->password))
                            <div class="team-status-cell">
                                <label class="team-switch">
                                    <input type="checkbox" class="chkStatus" value="active"
                                        {{ $row->status == 'active' ? 'checked' : '' }}
                                        onclick="handleClick(this, {{ $row->id }});"
                                        {{ $row->root_user == 1 ? 'disabled' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <span class="team-status-text status_text {{ $row->status == 'active' ? 'text-success' : 'text-muted' }} fw-bold ms-2">
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
                    <div class="tm-subcard-cell tm-cell-lastactive">
                        <div class="d-flex align-items-center gap-1 fs-8 text-muted">
                            <span class="team-dot {{ $dotClass }}"></span>
                            <span>{{ $lastActive }}</span>
                        </div>
                    </div>

                    <!-- Sub-Card 8: Actions -->
                    <div class="tm-subcard-cell tm-cell-actions text-center">
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
                                    <i class="fa-solid fa-building me-2 text-primary"></i>Set as Client Contact
                                </a>
                                <a class="team-action-item" onclick="setRepresentative({{ $row->id }}, 'agency', '{{ addslashes($rawName) }}')">
                                    <i class="fa-solid fa-users me-2 text-purple"></i>Set as Agency Contact
                                </a>
                                @if($agencyShares > 0)
                                    <a class="team-action-item" onclick="openAgencySharesModal({{ $row->id }}, '{{ addslashes($rawName) }}')">
                                        <i class="fa-solid fa-diagram-project me-2 text-indigo"></i>Inspect Shared Jobs
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Activity & Ownership Strip (if owner or shares) -->
                @if ($row->root_user == 1 || $agencyShares > 0)
                    <div class="team-card-bottom">
                        <div class="tm-bottom-left">
                            @if ($row->root_user == 1)
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
                @endif

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
