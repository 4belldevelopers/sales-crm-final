{{--
  Rule 20 Tier 1 & Tier 2: Search/Filter Toolbar Card + Dedicated White Sub-Tabs Card
  Dynamic 300ms debounced search (with instant clear button) & single-active primary blue sub-tabs.
--}}
@php
    $tabAllCount        = (int) ($totalRolesCount ?? 0);
    $tabSystemCount     = (int) ($systemRolesCount ?? 0);
    $tabCustomCount     = (int) ($customRolesCount ?? 0);
    $tabHighCount       = (int) ($highAccessCount ?? 0);
    $tabStaffedCount    = (int) ($staffedRolesCount ?? 0);
    $tabUnassignedCount = (int) ($unassignedRolesCount ?? 0);
@endphp

{{-- ── TIER 1: Search & Filter Toolbar Card ── --}}
<div class="rp-toolbar-card">
    <div class="rp-search-subcard" id="rpSearchSubcard">
        <i class="fas fa-search"></i>
        <input type="text"
               id="roleSearch"
               placeholder="Search roles by name, code, permission group, or assigned user..."
               autocomplete="off"
               spellcheck="false">
        <button type="button"
                class="rp-search-clear-btn"
                id="btnRoleSearchClear"
                style="display: none;"
                title="Clear search">
            <i class="fas fa-times"></i>
        </button>
        <span class="rp-search-kbd" title="Press / to focus search">/</span>
    </div>

    <div class="rp-inline-filters">
        <div class="rp-filter-pill">
            <span class="rp-filter-pill-lbl">
                <i class="fas fa-layer-group"></i> Type:
            </span>
            <select class="rp-filter-select" id="filterRoleType" aria-label="Filter by Role Type">
                <option value="">All Types</option>
                <option value="system">System Role</option>
                <option value="custom">Custom Role</option>
            </select>
        </div>

        <div class="rp-filter-pill">
            <span class="rp-filter-pill-lbl">
                <i class="fas fa-shield-alt"></i> Access:
            </span>
            <select class="rp-filter-select" id="filterAccessLevel" aria-label="Filter by Access Level">
                <option value="">All Levels</option>
                <option value="badge-high">High Access</option>
                <option value="badge-medium">Medium Access</option>
                <option value="badge-low">Low Access</option>
            </select>
        </div>

        <div class="rp-filter-pill">
            <span class="rp-filter-pill-lbl">
                <i class="fas fa-sort-amount-down"></i> Sort:
            </span>
            <select class="rp-filter-select" id="sortRoles" aria-label="Sort Roles">
                <option value="users">Most Users</option>
                <option value="name">Name A–Z</option>
                <option value="perms">Permission Groups</option>
                <option value="total_perms">Total Permissions</option>
            </select>
        </div>

        <button type="button" class="rp-reset-btn" id="clearFilters" title="Reset all filters and tabs">
            <i class="fas fa-undo-alt"></i>
            <span>Reset</span>
        </button>
    </div>
</div>

{{-- ── TIER 2: Dedicated White Sub-Tabs Card (Single Active Primary Blue #009ef7) ── --}}
<div class="rp-tabs-card">
    <div class="rp-tabs-group" role="tablist" aria-label="Role category filters">
        <button type="button" class="rp-tab-btn js-rp-tab active" data-tab="all" onclick="applyRoleSubTab('all')">
            <span>All Roles</span>
            <span class="rp-tab-badge rp-num">{{ $tabAllCount }}</span>
        </button>
        <button type="button" class="rp-tab-btn js-rp-tab" data-tab="system" onclick="applyRoleSubTab('system')">
            <span>System Roles</span>
            <span class="rp-tab-badge badge-system rp-num">{{ $tabSystemCount }}</span>
        </button>
        <button type="button" class="rp-tab-btn js-rp-tab" data-tab="custom" onclick="applyRoleSubTab('custom')">
            <span>Custom Roles</span>
            <span class="rp-tab-badge badge-custom rp-num">{{ $tabCustomCount }}</span>
        </button>
        <button type="button" class="rp-tab-btn js-rp-tab" data-tab="high_access" onclick="applyRoleSubTab('high_access')">
            <span>High Access</span>
            <span class="rp-tab-badge badge-high rp-num">{{ $tabHighCount }}</span>
        </button>
        <button type="button" class="rp-tab-btn js-rp-tab" data-tab="staffed" onclick="applyRoleSubTab('staffed')">
            <span>Staffed</span>
            <span class="rp-tab-badge badge-staffed rp-num">{{ $tabStaffedCount }}</span>
        </button>
        <button type="button" class="rp-tab-btn js-rp-tab" data-tab="unassigned" onclick="applyRoleSubTab('unassigned')">
            <span>Unassigned</span>
            <span class="rp-tab-badge badge-unassigned rp-num">{{ $tabUnassignedCount }}</span>
        </button>
    </div>

    <div class="rp-tabs-right-meta">
        <span class="rp-showing-text">
            Showing <strong class="rp-num" id="rpVisibleCount">{{ $tabAllCount }}</strong> of <span class="rp-num">{{ $tabAllCount }}</span> roles
        </span>
        <span class="rp-live-chip" title="Multi-tenant RBAC policy cache active">
            <span class="rp-live-dot"></span>
            <span>RBAC Active</span>
        </span>
    </div>
</div>
