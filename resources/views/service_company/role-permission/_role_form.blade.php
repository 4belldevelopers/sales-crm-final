{{--
    Shared Add / Edit Role form body (rendered inside the right-side #roleFormDrawer).
    Purpose: one screenshot-2 styled layout used for BOTH "Add New Role" and "Edit Role",
    driven by the REAL grouped permissions so it keeps saving through saveRole / updateRole.

    Expected variables:
      $isEdit          bool    — edit vs add (add = brand new role, everything on by default)
      $permissions     Collection grouped by group_name → [{id,name,display_name,status}]
      $permission_role array   — permission ids already assigned (edit); [] for add
      $role_row        object|null — role being edited (has display_name)
      $role_users      Collection — users assigned to the role (edit); empty for add
      $id              int     — role id (0 for add)

    Field names are intentionally unchanged so the existing backend keeps working:
      role_name, role[] (permission ids incl. 14/15/16/17),
      all_requisition (radio → perm 58), add_team_on_requisition (radio → perm 59).
--}}
@php
    $isEdit          = $isEdit          ?? false;
    $permission_role = $permission_role ?? [];
    $role_row        = $role_row        ?? null;
    $roleUsers       = $role_users      ?? collect();
    $roleName        = $role_row ? ucwords($role_row->display_name) : '';
    $isSubAdmin      = $role_row && $role_row->display_name === 'Sub Administrator';

    /* Helper: is a given permission id "on" for this role? (edit = assigned, add = default on) */
    $permOn = function ($pid) use ($isEdit, $permission_role) {
        return $isEdit ? in_array($pid, $permission_role) : true;
    };

    /* Display grouping: map real DB group_names onto the product's IA sections (display only —
       no data is invented, unmapped groups fall through to "Other Modules"). Order + subtitles
       match the Roles & Permissions tree agreed with the product owner. */
    $sectionConfig = [
        'CRM / Sales' => [
            'icon'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/>',
            'subtitle' => 'Lead & Client Management',
            'groups'   => ['Clients'],
        ],
        'Recruitment' => [
            'icon'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'subtitle' => 'Jobs & Candidate Management',
            'groups'   => ['Jobs', 'Job Applications', 'Requisition', 'Schedule', 'Offer', 'Joining', 'Team On Requisition', 'Salary', 'Salary Break'],
        ],
        'Agencies' => [
            'icon'     => '<path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-3"/>',
            'subtitle' => 'Agency & Vendor Management',
            'groups'   => ['Staffing Partners'],
        ],
        'Staffing Business' => [
            'icon'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'subtitle' => 'Talent Pool Management',
            'groups'   => ['Bench'],
        ],
        'Administrator' => [
            'icon'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'subtitle' => 'System & Security Management',
            'groups'   => ['Roles & Permissions', 'Manage Settings', 'Settings', 'Subscription', 'Team'],
        ],
        'Reports' => [
            'icon'     => '<line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/>',
            'subtitle' => 'Analytics & Performance',
            'groups'   => ['Reports'],
        ],
        'Website' => [
            'icon'     => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
            'subtitle' => 'Career Website Management',
            'groups'   => ['Reports & Analytics'],
        ],
    ];

    /* Normalise the incoming groups by a lower-cased key for tolerant matching */
    $groupsByKey = [];
    foreach ($permissions as $gName => $perms) {
        $groupsByKey[strtolower(trim($gName))] = ['name' => $gName, 'perms' => $perms];
    }
    $usedKeys = [];

    /* Build the ordered section → [groups] structure actually present for this company */
    $sections = [];
    foreach ($sectionConfig as $secName => $cfg) {
        $mods = [];
        foreach ($cfg['groups'] as $gWant) {
            $k = strtolower(trim($gWant));
            if (isset($groupsByKey[$k])) { $mods[] = $groupsByKey[$k]; $usedKeys[$k] = true; }
        }
        if (!empty($mods)) $sections[] = ['name' => $secName, 'icon' => $cfg['icon'], 'subtitle' => $cfg['subtitle'], 'mods' => $mods];
    }
    /* Anything not mapped above → "Other Modules" so nothing is ever hidden from the admin */
    $otherMods = [];
    foreach ($groupsByKey as $k => $g) { if (empty($usedKeys[$k])) $otherMods[] = $g; }
    if (!empty($otherMods)) {
        $sections[] = ['name' => 'Other Modules', 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>', 'subtitle' => 'Ungrouped permissions', 'mods' => $otherMods];
    }

    /* Bucket a group's real permissions into the 3 fixed table columns (Add / Edit / View) by
       matching keywords in their display_name. Anything that doesn't fit (e.g. Delete, Share)
       is kept as "other" and preserved via a hidden input so this simplified 3-column grid
       never silently revokes a permission the role already had. */
    $bucketAction = function ($displayName) {
        $d = strtolower($displayName);
        if (str_contains($d, 'add') || str_contains($d, 'create')) return 'add';
        if (str_contains($d, 'edit') || str_contains($d, 'update') || str_contains($d, 'manage')) return 'edit';
        if (str_contains($d, 'view') || str_contains($d, 'read')) return 'view';
        return 'other';
    };
    $bucketModPerms = function ($modPerms) use ($bucketAction) {
        $buckets = ['add' => null, 'edit' => null, 'view' => null];
        $other = [];
        foreach ($modPerms as $p) {
            $p = (object) $p;
            if ($p->status == 0) continue;
            $action = $bucketAction($p->display_name);
            if ($action !== 'other' && $buckets[$action] === null) {
                $buckets[$action] = $p;
            } else {
                $other[] = $p;
            }
        }
        return [$buckets, $other];
    };

    /* Job Requisition extra permissions (excluded from $permissions on purpose) */
    $jobActions = ['Add' => 14, 'View' => 15, 'Edit' => 16, 'Delete' => 17];
    $allJobsOn  = $isEdit ? in_array(58, $permission_role) : true;   // perm 58 → all_requisition
    $addTeamOn  = $isEdit ? in_array(59, $permission_role) : true;   // perm 59 → add_team_on_requisition

    /* Assigned-user avatars (edit) */
    $avColors  = ['#5e72e4','#1a73e8','#27ae60','#e74c3c','#f39c12','#9b59b6'];
    $userCount = $roleUsers->count();
@endphp

<form id="kt_modal_add_role_form" name="kt_modal_add_role_form" class="rpf-form" action="#" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @if($isEdit)
        <input type="hidden" name="role_id" id="role_id" value="{{ $id }}">
    @endif

    {{-- ── Header (full-bleed bar; Save / Discard / Cancel live here — the footer is removed) ── --}}
    <div class="rpf-head">
        <div class="rpf-head-inner rpf-container">
            <div class="rpf-head-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div class="rpf-head-titles">
                <div class="rpf-head-title-row">
                    <h3 class="rpf-head-title">{{ $isEdit ? 'Edit Role' : 'Add Role' }}</h3>
                    @if($isEdit)<span class="rpf-badge-active">Active</span>@endif
                </div>
                <p class="rpf-head-sub">{{ $isEdit ? 'Manage permissions and access control for this role.' : 'Create a custom role with specific permissions for your team.' }}</p>
            </div>

            {{-- Action buttons (moved from footer into the header) --}}
            <div class="rpf-head-actions">
                <button type="button" class="rpf-fbtn" onclick="closeRoleForm()">Cancel</button>
                <button type="reset" class="rpf-fbtn danger">Discard Changes</button>
                <button type="button" id="submit-frm-btn" class="rpf-fbtn primary" data-kt-roles-modal-action="submit">
                    <span class="indicator-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ $isEdit ? 'Save Role' : 'Create Role' }}
                    </span>
                    <span class="indicator-progress">Please wait…<span class="spinner-border spinner-border-sm align-middle"></span></span>
                </button>
            </div>

            <button type="button" class="rpf-head-close" onclick="closeRoleForm()" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- ── Scrollable body (full-width scroll track; content centered/capped inside) ── --}}
    <div class="rpf-body">
    <div class="rpf-body-inner rpf-container">

        {{-- Affected-users warning (edit, only when the role has users) --}}
        @if($isEdit && $userCount > 0)
        <div class="rpf-warn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span class="rpf-warn-text">Changes to this role will affect <b>{{ $userCount }} active {{ \Illuminate\Support\Str::plural('user', $userCount) }}</b>.</span>
        </div>
        @endif

        {{-- Hero / summary card — one single row: Role Name | Description | Assigned Users | Access Level | Permissions --}}
        <div class="rpf-summary">
            <div class="rpf-sum-col rpf-sum-name">
                <label class="rpf-field-label">Role Name <span class="req">*</span></label>
                @if($isSubAdmin)
                    <input class="rpf-input" value="{{ $roleName }}" readonly>
                    <input type="hidden" name="role_name" id="role_name" value="{{ $roleName }}">
                @else
                    <input class="rpf-input" name="role_name" id="role_name" placeholder="Enter a role name" value="{{ $roleName }}">
                @endif
            </div>
            <div class="rpf-sum-col rpf-sum-desc">
                <label class="rpf-field-label">Description</label>
                <input class="rpf-input" disabled value="{{ $isEdit ? 'Limited access with the permissions selected below.' : 'Custom role with tailored permissions.' }}">
            </div>
            <div class="rpf-sum-col rpf-sum-users">
                <label class="rpf-field-label">Assigned Users</label>
                @if($userCount > 0)
                    <div class="rpf-summary-users">
                        @foreach($roleUsers->take(3) as $i => $u)
                            <span class="rpf-uav" style="background:{{ $avColors[$i % count($avColors)] }};">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                        @endforeach
                        <span class="rpf-users-count">{{ $userCount }} {{ \Illuminate\Support\Str::plural('User', $userCount) }}</span>
                    </div>
                @else
                    <div class="rpf-users-count" style="padding-top:6px;">No users assigned yet</div>
                @endif
            </div>
            <div class="rpf-sum-col rpf-sum-access">
                <label class="rpf-field-label">Access Level</label>
                <div><span class="rpf-access-pill" style="color:#e67e22;background:#fff3e0;">Medium</span></div>
            </div>
            <div class="rpf-sum-col rpf-sum-perms">
                <div class="rpf-prog-top">
                    <span class="rpf-field-label" style="margin:0;">Permissions</span>
                    <span class="rpf-prog-frac"><span class="rpf-prog-enabled">0</span> <span>/ <span class="rpf-prog-total">0</span> Enabled</span></span>
                </div>
                <div class="rpf-prog-track"><div class="rpf-prog-fill" style="width:0;"></div></div>
                <div class="rpf-prog-pct" style="margin-top:4px;">0%</div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="rpf-tabs">
            <div class="rpf-tab active" data-tab="all">All Permissions</div>
            <div class="rpf-tab" data-tab="module">Module Permissions</div>
            <div class="rpf-tab" data-tab="advanced">Advanced Permissions</div>
        </div>

        {{-- Quick actions + search + collapse --}}
        <div class="rpf-toolbar">
            <span class="rpf-quick-label">Quick&nbsp;Actions:</span>
            <button type="button" class="rpf-qbtn" data-rpf="select-all">Select All</button>
            <button type="button" class="rpf-qbtn" data-rpf="clear-all">Clear All</button>
            <button type="button" class="rpf-qbtn" data-rpf="view-only">View Only</button>
            <button type="button" class="rpf-qbtn primary" data-rpf="full-access">Full Access</button>
            <div class="rpf-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" data-rpf="search" placeholder="Search permissions…">
            </div>
            <button type="button" class="rpf-qbtn" data-rpf="collapse"><span class="rpf-collapse-lbl">Collapse All</span></button>
        </div>

        {{-- Hidden master toggle preserved for the "Administrator / Select all" behaviour --}}
        <label style="display:none;"><input type="checkbox" class="rpf-master"></label>

        {{-- Two-column: module list + summary rail --}}
        <div class="rpf-grid">

            {{-- Main: fixed-column permission table (Permission Groups | Add | Edit | View) --}}
            <div class="rpf-main">
                <div class="rpf-ptable">
                    <div class="rpf-ptable-head">
                        <div class="rpf-th rpf-th-group">Permission Groups</div>
                        <div class="rpf-th rpf-th-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add
                        </div>
                        <div class="rpf-th rpf-th-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </div>
                        <div class="rpf-th rpf-th-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </div>
                    </div>

                    @foreach($sections as $sec)
                    <div class="rpf-sec-group" data-sec-name="{{ strtolower($sec['name']) }}">
                        <div class="rpf-sec-line" onclick="this.closest('.rpf-sec-group').classList.toggle('collapsed')">
                            <span class="rpf-sec-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $sec['icon'] !!}</svg>
                            </span>
                            <div class="rpf-sec-names">
                                <div class="rpf-sec-title">{{ $sec['name'] }}</div>
                                <div class="rpf-sec-subtitle">{{ $sec['subtitle'] }}</div>
                            </div>
                            <span class="rpf-sec-count">{{ count($sec['mods']) }}</span>
                            <svg class="rpf-sec-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>

                        <div class="rpf-sec-rows">
                            @foreach($sec['mods'] as $mod)
                            @php
                                $modName = \App\Helper\GeneralMethod::roleGroupLabel($mod['name']);
                                [$buckets, $otherPerms] = $bucketModPerms($mod['perms']);
                            @endphp
                            <div class="rpf-row" data-mod-name="{{ strtolower($modName) }}">
                                <div class="rpf-row-label">{{ $modName }}</div>
                                @foreach(['add', 'edit', 'view'] as $actionKey)
                                <div class="rpf-row-cell">
                                    @if($buckets[$actionKey])
                                        @php $p = $buckets[$actionKey]; $checked = $permOn($p->id); @endphp
                                        <label class="rpf-chk rpf-chk-center">
                                            <input type="checkbox" class="rpf-perm-check" name="role[]" value="{{ $p->id }}"
                                                   data-action="{{ ucfirst($actionKey) }}" data-group="{{ $modName }}"
                                                   aria-label="{{ ucfirst($actionKey) }} {{ $modName }}"
                                                   {{ $checked ? 'checked' : '' }}>
                                            <span class="rpf-chk-box"></span>
                                        </label>
                                    @endif
                                </div>
                                @endforeach

                                {{-- Preserve any permission that doesn't fit Add/Edit/View (e.g. Delete) so this
                                     3-column grid never silently revokes it from the role on save --}}
                                @foreach($otherPerms as $op)
                                    @if($permOn($op->id))
                                        <input type="hidden" name="role[]" value="{{ $op->id }}">
                                    @endif
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Aside rail --}}
            <div class="rpf-aside">

                {{-- [0] Permission Summary donut --}}
                <div class="rpf-acard">
                    <p class="rpf-acard-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                        Permission Summary
                    </p>
                    <div class="rpf-donut-wrap">
                        <div class="rpf-donut">
                            <svg width="84" height="84" viewBox="0 0 84 84">
                                <circle cx="42" cy="42" r="34" fill="none" stroke="#eef0f5" stroke-width="9"/>
                                <circle class="rpf-donut-ring" cx="42" cy="42" r="34" fill="none" stroke="#5e72e4" stroke-width="9" stroke-linecap="round" stroke-dasharray="213.6" stroke-dashoffset="213.6"/>
                            </svg>
                            <div class="rpf-donut-center">
                                <span class="rpf-donut-num">0</span>
                                <span class="rpf-donut-cap">Enabled</span>
                            </div>
                        </div>
                        <div class="rpf-legend">
                            <div class="rpf-legend-row"><span class="rpf-legend-dot" style="background:#5e72e4;"></span>Enabled<span class="rpf-legend-val rpf-legend-enabled">0</span></div>
                            <div class="rpf-legend-row"><span class="rpf-legend-dot" style="background:#e0e4ee;"></span>Disabled<span class="rpf-legend-val rpf-legend-disabled">0</span></div>
                            <div class="rpf-legend-row"><span class="rpf-legend-dot" style="background:#f39c12;"></span>Partial<span class="rpf-legend-val rpf-legend-partial">0</span></div>
                        </div>
                    </div>
                </div>

                {{-- [1] Access Scope (Job Visibility) → all_requisition (perm 58) --}}
                <div class="rpf-acard">
                    <p class="rpf-acard-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Access Scope (Job Visibility)
                    </p>
                    <label class="rpf-radio rpf-scope-row">
                        <input type="radio" name="all_requisition" value="1" {{ $allJobsOn ? 'checked' : '' }}>
                        <span class="rpf-radio-dot"></span>
                        <span class="rpf-radio-lbl">All Jobs<small>Can see every job in the company</small></span>
                    </label>
                    <label class="rpf-radio rpf-scope-row">
                        <input type="radio" name="all_requisition" value="0" {{ $allJobsOn ? '' : 'checked' }}>
                        <span class="rpf-radio-dot"></span>
                        <span class="rpf-radio-lbl">Assigned Jobs Only<small>Only jobs assigned to the user</small></span>
                    </label>
                    <label class="rpf-radio rpf-scope-row">
                        <input type="radio" name="scope_team_jobs" value="team" disabled>
                        <span class="rpf-radio-dot"></span>
                        <span class="rpf-radio-lbl">Team Jobs<small>Coming soon</small></span>
                    </label>
                </div>

                {{-- [2] Advanced Permissions → Job actions (14-17) + add_team_on_requisition (perm 59) --}}
                <div class="rpf-acard">
                    <p class="rpf-acard-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Advanced Permissions
                    </p>
                    <div class="rpf-adv-grid">
                        <div class="rpf-field-label" style="margin-bottom:2px;">Job Actions</div>
                        @foreach($jobActions as $label => $pid)
                        <div class="rpf-adv-row">
                            <label class="rpf-chk">
                                <input type="checkbox" class="rpf-perm-check" name="role[]" value="{{ $pid }}" data-action="{{ $label }}" {{ $permOn($pid) ? 'checked' : '' }}>
                                <span class="rpf-chk-box"></span>
                                <span class="rpf-chk-lbl">{{ $label }} Jobs</span>
                            </label>
                        </div>
                        @endforeach

                        <div class="rpf-field-label" style="margin:10px 0 2px;">Add Team Member On Job</div>
                        <div class="rpf-adv-row" style="gap:16px;">
                            <label class="rpf-radio">
                                <input type="radio" name="add_team_on_requisition" value="1" {{ $addTeamOn ? 'checked' : '' }}>
                                <span class="rpf-radio-dot"></span><span class="rpf-radio-lbl">Yes</span>
                            </label>
                            <label class="rpf-radio">
                                <input type="radio" name="add_team_on_requisition" value="0" {{ $addTeamOn ? '' : 'checked' }}>
                                <span class="rpf-radio-dot"></span><span class="rpf-radio-lbl">No</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
</form>

<script>
(function () {
    /* Submit the role form to the correct endpoint (Add → saveRole, Edit → updateRole).
       Field names are unchanged, so the existing controllers handle it as before. */
    var $btn  = $('#submit-frm-btn');
    var url   = '{{ $isEdit ? route('service_company.settings.updateRole') : route('service_company.settings.saveRole') }}';

    $btn.off('click').on('click', function () {
        var roleName = $('#role_name').val();
        if (!roleName || roleName.trim() === '') {
            var $inp = $('#role_name');
            $inp.css('border-color', '#e74c3c').focus();
            setTimeout(function () { $inp.css('border-color', ''); }, 2000);
            return;
        }
        $btn.addClass('loading');
        $.ajax({
            url: url, type: 'POST', data: $('#kt_modal_add_role_form').serialize(),
            success: function (response) {
                $btn.removeClass('loading');
                if (response.status === 'success') {
                    if (typeof swal === 'function') {
                        swal({ title: 'Success!', text: '{{ $isEdit ? "Role updated successfully!" : "Role created successfully!" }}', type: 'success', timer: 1200, showConfirmButton: false });
                    }
                    setTimeout(function () { location.reload(); }, 700);
                } else if (response.status === 'warning') {
                    if (typeof swal === 'function') {
                        swal({ title: 'Warning!', text: response.message, type: 'warning', timer: 1600, showConfirmButton: true });
                    } else { alert(response.message); }
                }
            },
            error: function () {
                $btn.removeClass('loading');
                if (typeof swal === 'function') { swal({ title: 'Error', text: 'Something went wrong. Please try again.', type: 'error' }); }
            }
        });
    });
})();
</script>
