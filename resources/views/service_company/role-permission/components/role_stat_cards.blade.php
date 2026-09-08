<div class="rp-stats">
    <div class="rp-stat-card">
        <div class="rp-stat-icon" style="background:#eef0fb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="rp-stat-val">{{ $totalRolesCount }}</div>
            <div class="rp-stat-lbl">Total Roles</div>
            <div class="rp-stat-sub" style="color:#5e72e4;">All roles in system</div>
        </div>
    </div>
    <div class="rp-stat-card">
        <div class="rp-stat-icon" style="background:#e8f8f0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="rp-stat-val">{{ $systemRolesCount }}</div>
            <div class="rp-stat-lbl">System Roles</div>
            <div class="rp-stat-sub" style="color:#27ae60;">Built-in roles</div>
        </div>
    </div>
    <div class="rp-stat-card">
        <div class="rp-stat-icon" style="background:#fff8e6;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="rp-stat-val">{{ $customRolesCount }}</div>
            <div class="rp-stat-lbl">Custom Roles</div>
            <div class="rp-stat-sub" style="color:#f39c12;">User-defined</div>
        </div>
    </div>
    <div class="rp-stat-card">
        <div class="rp-stat-icon" style="background:#eef0fb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div>
            <div class="rp-stat-val">{{ $totalAssignedUsers }}</div>
            <div class="rp-stat-lbl">Assigned Users</div>
            <div class="rp-stat-sub" style="color:#5e72e4;">Across all roles</div>
        </div>
    </div>
    <div class="rp-stat-card">
        <div class="rp-stat-icon" style="background:#eef0fb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
        </div>
        <div>
            <div class="rp-stat-val">{{ $totalPermGroups }}</div>
            <div class="rp-stat-lbl">Permission Groups</div>
            <div class="rp-stat-sub" style="color:#5e72e4;">System groups</div>
        </div>
    </div>
</div>
