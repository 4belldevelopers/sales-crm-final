@php
    use Carbon\Carbon;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            // V2 Phase 2 & 3: Use pre-computed metrics passed from data_ajax() to eliminate N+1 sub-queries in Blade view
            $rawRank = $row->pre_rank ?? $row->rank ?? 'N/A';
            $isInvited = empty($row->password) || strtolower($row->status ?? '') === 'invited';
            $isRanked = ! $isInvited && is_numeric($rawRank) && (int) $rawRank > 0 && strtolower($row->status ?? '') === 'active';
            $rankDisplay = $isRanked ? '#' . (int) $rawRank : 'N/A';
            $rankMonthDisplay = $isRanked ? '(' . ($row->pre_rank_month ?? Carbon::now()->startOfMonth()->subMonth()->format('M Y')) . ')' : '';
            $scoreVal = $isRanked ? ($row->pre_score ?? $row->score ?? 0) : 0;
            $teamRanks = (object) ['rank' => $rankDisplay, 'score' => $scoreVal, 'rankMonth' => $rankMonthDisplay];

            // ---- Avatar background tint (kept from the original first-letter logic) ----
            $strVal = strtolower(trim(@$row->name)[0] ?? '');
            $ascii = ord($strVal);
            if ($ascii >= 97 && $ascii <= 101)      { $avatarClass = 'bg-light-danger text-danger'; }
            elseif ($ascii >= 102 && $ascii <= 106) { $avatarClass = 'bg-light-warning text-warning'; }
            elseif ($ascii >= 107 && $ascii <= 111) { $avatarClass = 'bg-light-primary text-primary'; }
            elseif ($ascii >= 112 && $ascii <= 116) { $avatarClass = 'bg-light-success text-success'; }
            elseif ($ascii >= 117 && $ascii <= 121) { $avatarClass = 'bg-dark text-white'; }
            else                                    { $avatarClass = 'bg-light-secondary text-dark'; }

            // ---- Pre-computed Workload metrics ----
            $activeJobs  = $row->pre_active_jobs ?? 0;
            $captainJobs = $row->pre_captain_jobs ?? 0;
            $trmTalents  = $row->pre_trm_talents ?? 0;

            // ---- Role pill colour ----
            // Title Case for display (data is stored lowercase/inconsistently) — see memory.
            $roleName = ucwords(strtolower(@$row->display_name));
            $roleLower = strtolower($roleName);
            if (str_contains($roleLower, 'admin'))     { $rolePill = 'role-admin'; }
            elseif (str_contains($roleLower, 'lead'))  { $rolePill = 'role-lead'; }
            else                                       { $rolePill = 'role-default'; }

            // ---- Rank medal for the top 3 ----
            $medal = match ((int) @$teamRanks->rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => '' };

            // ---- Last active (relative) + freshness dot ----
            $lastActive = '—'; $dotClass = 'dot-gray';
            if (!empty($row->last_login)) {
                try {
                    $la = Carbon::parse($row->last_login);
                    $lastActive = $la->diffForHumans();
                    $mins = $la->diffInMinutes(Carbon::now());
                    $dotClass = $mins <= 60 ? 'dot-green' : ($mins <= 1440 ? 'dot-amber' : 'dot-gray');
                } catch (\Throwable $e) {}
            }

            $status_disabled = ((@$row->root_user == 1) || (@$row->status != 'active')) ? 'disabled' : '';
        @endphp
        <tr>
            {{-- Member --}}
            <td>
                <div class="d-flex align-items-center">
                    <div class="team-avatar {{ $avatarClass }} show-detail-team me-3" data-row-id="{{ $row->id }}"
                        style="cursor:pointer;">
                        @if (!empty($row->image))
                            <img src="{{ $row->image }}" alt="">
                        @else
                            {{ strtoupper($strVal) }}
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="team-member-name show-detail-team"
                            data-row-id="{{ $row->id }}" title="{{ ucwords(strtolower(@$row->name)) }}">
                            {{ ucwords(strtolower(mb_substr(@$row->name, 0, 30))) }}
                        </a>
                        @if (!empty($row->designation))
                            <span class="team-member-role">{{ ucwords(strtolower($row->designation)) }}</span>
                        @endif
                        <span class="team-member-mail">{{ $row->email }}</span>
                    </div>
                </div>
            </td>

            {{-- Rank --}}
            <td>
                <div class="d-flex flex-column">
                    <span class="team-rank-badge">{!! $medal !!} {{ $teamRanks->rank }}</span>
                    @if (!empty($teamRanks->rankMonth))
                        <span class="team-rank-month">{{ $teamRanks->rankMonth }}</span>
                    @endif
                    <span class="team-score-link teamViewScore"
                        data-user-id="{{ $row->id }}">View Score</span>
                </div>
            </td>

            {{-- Workload --}}
            <td>
                <div class="team-workload">
                    <div class="team-wl-item wl-blue">
                        <span class="team-wl-num"><i class="fa-solid fa-briefcase"></i>{{ $activeJobs }}</span>
                        <span class="team-wl-label">Active Jobs</span>
                    </div>
                    <div class="team-wl-item wl-purple">
                        <span class="team-wl-num"><i class="fa-solid fa-crown"></i>{{ $captainJobs }}</span>
                        <span class="team-wl-label">Captain Jobs</span>
                    </div>
                    <div class="team-wl-item wl-green">
                        <span class="team-wl-num"><i class="fa-solid fa-users"></i>{{ $trmTalents }}</span>
                        <span class="team-wl-label">TRM Talents</span>
                    </div>
                </div>
            </td>

            {{-- Role --}}
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="team-role-pill {{ $rolePill }}">
                        {{ $roleName }}
                    </span>
                    @if ($row->root_user != 1)
                        <button class="team-role-edit-btn update-user-role" data-row-id="{{ $row->id }}" title="Edit Role">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    @endif
                </div>
            </td>

            {{-- Status --}}
            <td>
                @if (!empty($row->password))
                    <div class="team-status-cell">
                        <label class="team-switch">
                            <input type="checkbox" class="chkStatus" value="active"
                                {{ $row->status == 'active' ? 'checked' : '' }}
                                onclick="handleClick(this, {{ $row->id }});"
                                {{ $row->root_user == 1 ? 'disabled' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span
                            class="team-status-text status_text {{ $row->status == 'active' ? 'text-success' : 'text-danger' }} fw-bold">{{ $row->status == 'active' ? 'Active' : 'Inactive' }}</span>
                    </div>
                @else
                    <a href="javascript:;" class="text-primary fw-bold fs-7"
                        onclick="reSendhandleClick(this, {{ $row->id }});">Resend Mail <i class="fa-solid fa-arrows-rotate"></i></a>
                    <span class="team-status-text status_text text-danger fw-bold d-block">Invited</span>
                @endif
            </td>

            {{-- Last Active --}}
            <td>
                <span class="team-lastactive"><span class="team-dot {{ $dotClass }}"></span>{{ $lastActive }}</span>
            </td>

            {{-- Actions --}}
            <td style="text-align:right;">
                <div class="team-action-wrap">
                    <button class="team-action-btn" onclick="toggleTeamMenu(this)"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    <div class="team-action-menu">
                        <a class="team-action-item show-detail-team" data-row-id="{{ $row->id }}">View Profile</a>
                        @if ($row->root_user != 1)
                            <a class="team-action-item update-user-role" data-row-id="{{ $row->id }}">Edit Role</a>
                            @if (empty($row->password))
                                <a class="team-action-item" onclick="reSendhandleClick(this, {{ $row->id }});">Resend Invite</a>
                            @endif
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" style="text-align:center; color:#7c8398; padding:32px;">
            No team members found
        </td>
    </tr>
@endif
