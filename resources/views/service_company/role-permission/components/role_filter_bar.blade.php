<div class="rp-filter-card">
    <div class="rp-filter-row">
        <div class="rp-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="roleSearch" placeholder="Search roles by name...">
        </div>
        <select class="rp-filter-select" id="filterRoleType">
            <option value="">Role Type</option>
            <option value="system">System Role</option>
            <option value="custom">Custom Role</option>
        </select>
        <select class="rp-filter-select" id="filterAccessLevel">
            <option value="">Access Level</option>
            <option value="badge-high">High Access</option>
            <option value="badge-medium">Medium Access</option>
            <option value="badge-low">Low Access</option>
        </select>
        <span class="rp-filter-clear" id="clearFilters">Clear Filters</span>
        <span class="rp-sort-label">Sort by:</span>
        <select class="rp-sort-select" id="sortRoles">
            <option value="users">Most Users</option>
            <option value="name">Name A–Z</option>
            <option value="perms">Permissions</option>
        </select>
    </div>
</div>
