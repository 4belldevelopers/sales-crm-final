{{--
  Rule 20 Tier 3: Enterprise Primary Role Card + 5 Discrete Upper Sub-Cards + Bottom Audit Strip
  Zero DB queries executed inside view; consumes pre-computed $allRoleStats from RolePermissionService.
--}}
@php
    $roleId             = $rlist->id;
    $roleStat           = $allRoleStats[$roleId] ?? [];
    $uCount             = (int) ($roleStat['userCount'] ?? 0);
    $roleUsers          = $roleStat['users'] ?? collect();
    $grpCount           = (int) ($roleStat['grpCount'] ?? 0);
    $totalAssignedPerms = (int) ($roleStat['totalAssignedPerms'] ?? 0);
    $totalSystemPerms   = max(1, (int) ($totalPermissions ?? 1));
    $overallCovPct      = min(100, (int) round(($totalAssignedPerms / $totalSystemPerms) * 100));
    $coverage           = $roleStat['coverage'] ?? [];
    $palette            = $cardPalette[$loop->index % count($cardPalette)];
    $access             = $roleStat['access'] ?? (function_exists('rpAccessLevel') ? rpAccessLevel($grpCount, $rlist->name ?? '') : ['label' => 'Low Access', 'class' => 'badge-low']);
    $isCustom           = (bool) ($rlist->manual_add ?? false);
    $roleType           = $isCustom ? 'custom' : 'system';
    $roleCode           = ($isCustom ? 'CUS-' : 'SYS-') . str_pad($roleId, 2, '0', STR_PAD_LEFT);
    $isProtected        = (($rlist->priority ?? 1) == 0) || (strtolower($rlist->name ?? '') === 'admin');
    $grpAll             = $rlist->grouped_permissions ?? collect();
    $grpKeys            = $grpAll instanceof \Illuminate\Support\Collection ? $grpAll->keys()->all() : array_keys((array) $grpAll);
    $grpLabels          = array_map(fn ($gk) => \App\Helper\GeneralMethod::roleGroupLabel($gk), $grpKeys);
    $grpShown           = array_slice($grpLabels, 0, 4);
    $grpExtra           = count($grpLabels) > 4 ? count($grpLabels) - 4 : 0;
    $detailJson         = $roleStat['detailJson'] ?? '{}';
    $createdOn          = $rlist->created_at ? \Carbon\Carbon::parse($rlist->created_at)->format('d M, Y') : '—';
    $updatedOn          = $rlist->updated_at ? \Carbon\Carbon::parse($rlist->updated_at)->format('d M, Y') : '—';

    $userSearchTokens = $roleUsers instanceof \Illuminate\Support\Collection
        ? $roleUsers->map(fn ($u) => trim(($u->name ?? '') . ' ' . ($u->email ?? '')))->implode(' ')
        : '';
    $searchCorpus = strtolower(implode(' ', [
        ucwords($rlist->display_name ?? ''),
        $roleCode,
        $isCustom ? 'custom role' : 'system role',
        $access['label'] ?? '',
        implode(' ', $grpLabels),
        $userSearchTokens,
    ]));

    $coverageShortLabels = [
        'Recruitment'        => 'Recruitment',
        'Talent Marketplace' => 'Marketplace',
        'Sales & CRM'        => 'Sales & CRM',
        'Admin & Security'   => 'Admin & Sec.',
    ];

    if (!function_exists('rpCardIcon')) {
        function rpCardIcon($name, $stroke) {
            $icons = [
                'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
                'users'        => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                'user'         => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
                'message'      => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
                'currency'     => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
                'shield'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                'settings'     => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
            ];
            $d = $icons[$name] ?? $icons['settings'];
            return '<svg viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>';
        }
    }
@endphp

<div class="rp-primary-card {{ $uCount > 0 ? 'is-staffed' : 'is-unassigned' }}"
     data-role-type="{{ $roleType }}"
     data-role-name="{{ strtolower(ucwords($rlist->display_name ?? '')) }}"
     data-search-text="{{ e($searchCorpus) }}"
     data-user-count="{{ $uCount }}"
     data-grp-count="{{ $grpCount }}"
     data-perm-count="{{ $totalAssignedPerms }}"
     data-access-level="{{ $access['class'] }}"
     data-role-id="{{ $roleId }}"
     data-role-code="{{ $roleCode }}"
     data-role-detail='{{ $detailJson }}'
     onclick="openRoleFormFromCard(this)">

    <div class="rp-entity-grid">
        {{-- ── SUB-CARD 1: Role Identity ── --}}
        <div class="rp-subcard rp-subcard-identity">
            <div class="rp-subcard-head">
                <span class="rp-subcard-label">Role Identity</span>
                <div class="rp-subcard-badges">
                    <span class="rp-scope-pill {{ $isCustom ? 'scope-custom' : 'scope-system' }}">
                        {{ $isCustom ? 'Custom Role' : 'System Role' }}
                    </span>
                    <span class="rp-access-pill {{ $access['class'] }}">
                        {{ $access['label'] }}
                    </span>
                </div>
            </div>
            <div class="rp-identity-body">
                <div class="rp-role-icon-box" style="background: {{ $palette['bg'] }}; border-color: {{ $palette['stroke'] }}24;">
                    {!! rpCardIcon($palette['icon'], $palette['stroke']) !!}
                </div>
                <div class="rp-identity-text">
                    <div class="rp-role-title-row">
                        <span class="rp-role-name" title="{{ ucwords($rlist->display_name) }}">
                            {{ ucwords($rlist->display_name) }}
                        </span>
                    </div>
                    <div class="rp-role-meta-line">
                        <span class="rp-role-code rp-num">#{{ $roleCode }}</span>
                        <span class="rp-meta-dot">•</span>
                        <span class="rp-role-kind">{{ $isCustom ? 'Tenant-defined policy' : 'Built-in system role' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SUB-CARD 2: Domain Coverage ── --}}
        <div class="rp-subcard rp-subcard-coverage">
            <div class="rp-subcard-head">
                <span class="rp-subcard-label">Domain Coverage</span>
                <span class="rp-micro-pill rp-pill-neutral rp-num" title="{{ $totalAssignedPerms }} of {{ $totalSystemPerms }} system permissions">
                    {{ $totalAssignedPerms }}/{{ $totalSystemPerms }} Perms
                </span>
            </div>
            <div class="rp-cov-list">
                @foreach($coverage as $bucketName => $bucket)
                    @php
                        $shortLabel = $coverageShortLabels[$bucketName] ?? $bucketName;
                        $pct        = (int) ($bucket['pct'] ?? 0);
                        $barColor   = $bucket['color'] ?? '#2563eb';
                    @endphp
                    <div class="rp-cov-row" title="{{ $bucketName }}: {{ $bucket['assigned'] ?? 0 }}/{{ $bucket['total'] ?? 0 }} ({{ $pct }}%)">
                        <span class="rp-cov-name">{{ $shortLabel }}</span>
                        <div class="rp-cov-track">
                            <div class="rp-cov-fill" style="width: {{ $pct }}%; background: {{ $barColor }};"></div>
                        </div>
                        <span class="rp-cov-frac rp-num">{{ $bucket['assigned'] ?? 0 }}/{{ $bucket['total'] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── SUB-CARD 3: Permission Groups ── --}}
        <div class="rp-subcard rp-subcard-modules">
            <div class="rp-subcard-head">
                <span class="rp-subcard-label">Permission Groups</span>
                <span class="rp-micro-pill rp-pill-blue rp-num">{{ $grpCount }} {{ \Illuminate\Support\Str::plural('Group', $grpCount) }}</span>
            </div>
            <div class="rp-modules-body">
                @if(count($grpShown) > 0)
                    <div class="rp-perm-tags">
                        @foreach($grpShown as $gLabel)
                            <span class="rp-perm-tag" title="{{ $gLabel }}">{{ $gLabel }}</span>
                        @endforeach
                        @if($grpExtra > 0)
                            <button type="button"
                                    class="rp-perm-tag rp-perm-more"
                                    onclick="event.stopPropagation(); openRoleDrawer(this.closest('.rp-primary-card'), 'permissions');"
                                    title="Inspect all {{ $grpCount }} permission groups">
                                +{{ $grpExtra }} more
                            </button>
                        @endif
                    </div>
                @else
                    <div class="rp-empty-subtext">No permission groups assigned</div>
                @endif
            </div>
        </div>

        {{-- ── SUB-CARD 4: Assigned Users ── --}}
        <div class="rp-subcard rp-subcard-members">
            <div class="rp-subcard-head">
                <span class="rp-subcard-label">Assigned Users</span>
                <span class="rp-micro-pill {{ $uCount > 0 ? 'rp-pill-emerald' : 'rp-pill-neutral' }} rp-num">
                    {{ $uCount }} {{ $uCount === 1 ? 'User' : 'Users' }}
                </span>
            </div>
            <div class="rp-members-body"
                 onclick="event.stopPropagation(); openRoleDrawer(this.closest('.rp-primary-card'), 'users');"
                 title="{{ $uCount > 0 ? 'Click to view all assigned members' : 'No users assigned to this role' }}">
                @if($uCount > 0)
                    <div class="rp-avatars-stack">
                        @foreach($roleUsers->take(4) as $uIdx => $rUser)
                            @php
                                $parts = preg_split('/\s+/', trim((string) ($rUser->name ?? '')));
                                $initials = count($parts) >= 2
                                    ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1))
                                    : strtoupper(mb_substr($parts[0] ?? '?', 0, 2));
                            @endphp
                            <div class="rp-mini-av"
                                 style="background: {{ $avColors[$uIdx % count($avColors)] }};"
                                 title="{{ $rUser->name }} ({{ $rUser->email }})">
                                {{ $initials ?: '?' }}
                            </div>
                        @endforeach
                        @if($uCount > 4)
                            <div class="rp-mini-av rp-mini-av-more">+{{ $uCount - 4 }}</div>
                        @endif
                    </div>
                    <div class="rp-member-names">
                        @php
                            $firstUser = $roleUsers->first();
                            $firstUserName = $firstUser ? $firstUser->name : '';
                        @endphp
                        <span class="rp-member-primary">{{ $firstUserName }}</span>
                        @if($uCount > 1)
                            <span class="rp-member-extra">+{{ $uCount - 1 }} {{ \Illuminate\Support\Str::plural('other', $uCount - 1) }}</span>
                        @elseif($firstUser && !empty($firstUser->email))
                            <span class="rp-member-extra">{{ $firstUser->email }}</span>
                        @endif
                    </div>
                @else
                    <div class="rp-empty-subtext">
                        <i class="far fa-user-circle"></i>
                        <span>No users assigned</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── SUB-CARD 5: Governance & Actions ── --}}
        <div class="rp-subcard rp-subcard-governance" onclick="event.stopPropagation()">
            <div class="rp-subcard-head">
                <span class="rp-subcard-label">Governance</span>
                @if($isProtected)
                    <span class="rp-micro-pill rp-pill-amber" title="Built-in protected system role">
                        <i class="fas fa-lock" style="font-size: 8px; margin-right: 2px;"></i> Protected
                    </span>
                @else
                    <span class="rp-micro-pill rp-pill-neutral">Configurable</span>
                @endif
            </div>
            <div class="rp-gov-actions">
                <button type="button"
                        class="rp-act-btn rp-act-view"
                        onclick="event.stopPropagation(); openRoleDrawer(this.closest('.rp-primary-card'), 'overview');"
                        title="Quick overview, coverage & members">
                    <i class="far fa-eye"></i>
                    <span>Overview</span>
                </button>
                <button type="button"
                        class="rp-act-btn rp-act-edit"
                        onclick="event.stopPropagation(); openRoleFormFromCard(this.closest('.rp-primary-card'));"
                        title="Configure role permissions">
                    <i class="fas fa-sliders-h"></i>
                    <span>Configure</span>
                </button>
                @if($isCustom)
                    <button type="button"
                            class="rp-act-btn rp-act-del role_delete"
                            data-id="{{ $roleId }}"
                            onclick="event.stopPropagation();"
                            title="Delete custom role">
                        <i class="far fa-trash-alt"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ── SUB-CARD 6: Bottom Audit & Quick-Inspect Strip ── --}}
    <div class="rp-entity-bottom" onclick="event.stopPropagation()">
        <div class="rp-bottom-left">
            <span class="rp-bottom-item">
                <i class="far fa-calendar-plus"></i>
                <span>Created: <strong class="rp-bottom-strong">{{ $createdOn }}</strong></span>
            </span>
            <span class="rp-dot-sep">•</span>
            <span class="rp-bottom-item">
                <i class="far fa-clock"></i>
                <span>Updated: <strong class="rp-bottom-strong">{{ $updatedOn }}</strong></span>
            </span>
            <span class="rp-dot-sep">•</span>
            <span class="rp-bottom-item">
                <i class="fas fa-chart-pie"></i>
                <span>Policy Coverage: <strong class="rp-bottom-strong rp-num">{{ $overallCovPct }}%</strong></span>
            </span>
            @if($access['class'] === 'badge-high')
                <span class="rp-dot-sep">•</span>
                <span class="rp-bottom-audit-warn">
                    <i class="fas fa-shield-alt"></i>
                    <span>Elevated Access Scope</span>
                </span>
            @endif
        </div>
        <div class="rp-bottom-right">
            <button type="button"
                    class="rp-bottom-link"
                    onclick="openRoleDrawer(this.closest('.rp-primary-card'), 'permissions');">
                <span>Inspect Permissions ({{ $totalAssignedPerms }})</span>
                <i class="fas fa-chevron-right"></i>
            </button>
            <button type="button"
                    class="rp-bottom-link"
                    onclick="openRoleDrawer(this.closest('.rp-primary-card'), 'users');">
                <span>Members ({{ $uCount }})</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
