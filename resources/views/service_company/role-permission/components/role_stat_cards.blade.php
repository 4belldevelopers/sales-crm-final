{{--
  Client-Tracker-Inspired Interactive 6-Card KPI Strip for Roles & Permissions
  Clicking any KPI card filters the Role Card stream and synchronizes Tier 2 sub-tabs.
--}}
@php
    $totalRolesVal      = (int) ($totalRolesCount ?? 0);
    $systemRolesVal     = (int) ($systemRolesCount ?? 0);
    $customRolesVal     = (int) ($customRolesCount ?? 0);
    $highAccessVal      = (int) ($highAccessCount ?? 0);
    $assignedUsersVal   = (int) ($totalAssignedUsers ?? 0);
    $staffedRolesVal    = (int) ($staffedRolesCount ?? 0);
    $totalPermGroupsVal = (int) ($totalPermGroups ?? 0);
    $uniqueModulesVal   = (int) ($uniquePermGroupsCount ?? count($groupTotals ?? []));
    $systemPct          = $totalRolesVal > 0 ? round(($systemRolesVal / $totalRolesVal) * 100) : 0;
@endphp

<div class="rp-kpi-grid" id="rpKpiGrid">
    {{-- 1. Total Roles --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter active" data-kpi-filter="all" onclick="applyRoleKpiFilter('all')">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num-row">
                    <div class="rp-kpi-num" id="kpiTotalRoles">{{ $totalRolesVal }}</div>
                    @if($customRolesVal > 0)
                        <span class="rp-kpi-pill-badge">
                            <span class="rp-kpi-dot-pulse"></span>
                            <span>{{ $customRolesVal }} custom</span>
                        </span>
                    @endif
                </div>
                <div class="rp-kpi-lbl">Total Roles</div>
            </div>
        </div>
        <div class="rp-kpi-sub slate">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span>All active RBAC roles</span>
        </div>
    </button>

    {{-- 2. System Roles --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter" data-kpi-filter="system" onclick="applyRoleKpiFilter('system')">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num" id="kpiSystemRoles">{{ $systemRolesVal }}</div>
                <div class="rp-kpi-lbl">System Roles</div>
            </div>
        </div>
        <div class="rp-kpi-sub green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>{{ $systemPct }}% built-in defaults</span>
        </div>
    </button>

    {{-- 3. Custom Roles --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter" data-kpi-filter="custom" onclick="applyRoleKpiFilter('custom')">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num" id="kpiCustomRoles">{{ $customRolesVal }}</div>
                <div class="rp-kpi-lbl">Custom Roles</div>
            </div>
        </div>
        <div class="rp-kpi-sub amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m18 15-6-6-6 6"/>
            </svg>
            <span>{{ $customRolesVal > 0 ? 'Tenant-defined policies' : 'No custom roles yet' }}</span>
        </div>
    </button>

    {{-- 4. High Access Roles --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter" data-kpi-filter="high_access" onclick="applyRoleKpiFilter('high_access')">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon rose">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num" id="kpiHighAccess">{{ $highAccessVal }}</div>
                <div class="rp-kpi-lbl">High Access</div>
            </div>
        </div>
        <div class="rp-kpi-sub rose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Elevated privilege tier</span>
        </div>
    </button>

    {{-- 5. Assigned Users --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter" data-kpi-filter="staffed" onclick="applyRoleKpiFilter('staffed')">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon indigo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num" id="kpiAssignedUsers">{{ $assignedUsersVal }}</div>
                <div class="rp-kpi-lbl">Assigned Users</div>
            </div>
        </div>
        <div class="rp-kpi-sub indigo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>
            </svg>
            <span>Across {{ $staffedRolesVal }} staffed {{ \Illuminate\Support\Str::plural('role', $staffedRolesVal) }}</span>
        </div>
    </button>

    {{-- 6. Permission Groups --}}
    <button type="button" class="rp-kpi-card rp-kpi-filter" data-kpi-filter="unassigned" onclick="applyRoleKpiFilter('unassigned')" title="Filter roles with no assigned users or review module coverage">
        <div class="rp-kpi-top">
            <div class="rp-kpi-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
            </div>
            <div class="rp-kpi-meta">
                <div class="rp-kpi-num" id="kpiPermGroups">{{ $totalPermGroupsVal }}</div>
                <div class="rp-kpi-lbl">Permission Groups</div>
            </div>
        </div>
        <div class="rp-kpi-sub purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            <span>{{ $uniqueModulesVal }} modules • {{ (int) ($unassignedRolesCount ?? 0) }} unassigned</span>
        </div>
    </button>
</div>
