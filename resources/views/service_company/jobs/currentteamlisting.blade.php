@php
/**
 * Modern Hiring Team Drawer — God-Level Diamond Standard (Rule 20)
 *
 * Implements the drawer-based Add/View Team interface for the Team sub-card on Jobs Tracker.
 * Architecture: 100% Client-First reactive state, Poppins font, 15.6" screen responsiveness.
 * Tenancy: tenant-scoped by company_id, synchronizes with service_company.jobs.hmlistingupdate.
 */
$activeJobsMap = !empty($activeJobsCounts) ? $activeJobsCounts : [];
if (empty($activeJobsMap)) {
    try {
        $activeJobsMap = \Illuminate\Support\Facades\DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->where('jobs.status', '=', 'active')
            ->where('job_assign.company_id', '=', auth()->user()->company_id)
            ->groupBy('job_assign.user_id')
            ->select('job_assign.user_id', \Illuminate\Support\Facades\DB::raw('count(distinct job_assign.job_id) as total'))
            ->pluck('total', 'job_assign.user_id')
            ->toArray();
    } catch (\Exception $e) {
        $activeJobsMap = [];
    }
}

$teamRoster = [];
$assignedList = [];
$captainId = null;

foreach ($team as $m) {
    $mId = (int)$m->id;
    $isAssigned = ($m->job_assign_id > 0 && (string)$m->job_id === (string)$currentJobId);
    $isCaptain = ($isAssigned && $m->spok_user_id && (int)$m->spok_user_id === $mId);
    if ($isCaptain) {
        $captainId = $mId;
    }
    $activeJobs = (int)($activeJobsMap[$mId] ?? 0);

    $memberData = [
        'id' => $mId,
        'name' => (string)($m->name ?? 'Team Member'),
        'role' => (string)($m->designation ?: ($m->display_name ?: 'Recruiter')),
        'email' => (string)($m->email ?? ''),
        'activeJobs' => $activeJobs,
        'isAssigned' => $isAssigned,
        'isCaptain' => $isCaptain,
    ];
    $teamRoster[] = $memberData;
    if ($isAssigned) {
        $assignedList[] = $memberData;
    }
}

// Fallback: If members assigned but no spokesperson recorded, earliest assigned is Captain
if ($captainId === null && count($assignedList) > 0) {
    $captainId = $assignedList[0]['id'];
    foreach ($teamRoster as &$r) {
        if ($r['id'] === $captainId) {
            $r['isCaptain'] = true;
        }
    }
    unset($r);
    $assignedList[0]['isCaptain'] = true;
}
@endphp

<style>
/* ==========================================================================
   Modern Hiring Team Drawer (v7.2 Diamond Standard - 15.6" Screen Fit)
   ========================================================================== */
#kt_activitiesTEAM.drawer, #kt_activitiesTEAM {
    width: 600px !important;
    max-width: 100vw !important;
    font-family: 'Poppins', sans-serif !important;
    box-shadow: -4px 0 28px rgba(0, 0, 0, 0.16) !important;
    background: #ffffff !important;
    z-index: 10050 !important;
}
@media (min-width: 992px) {
    #kt_activitiesTEAM.drawer, #kt_activitiesTEAM {
        width: 600px !important;
        min-width: 560px !important;
        max-width: 640px !important;
    }
}
@media (min-width: 576px) and (max-width: 991.98px) {
    #kt_activitiesTEAM.drawer, #kt_activitiesTEAM {
        width: min(600px, 92vw) !important;
    }
}
@media (max-width: 575.98px) {
    #kt_activitiesTEAM.drawer, #kt_activitiesTEAM {
        width: 100vw !important;
    }
}

.hiring-team-drawer-wrapper {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    max-height: 100% !important;
    overflow: hidden !important;
    background: #ffffff !important;
    font-family: 'Poppins', sans-serif !important;
    color: #0f172a !important;
}

/* --- Top Assigned Section --- */
.ht-drawer-assigned-section {
    background: #f8fafc !important;
    padding: 14px 20px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    flex-shrink: 0 !important;
    max-height: 32vh !important;
    overflow-y: auto !important;
}
.ht-assigned-header {
    display: flex !important;
    align-items: baseline !important;
    justify-content: space-between !important;
    margin-bottom: 8px !important;
}
.ht-assigned-title {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    color: #475569 !important;
}
.ht-assigned-sub {
    font-size: 11px !important;
    color: #94a3b8 !important;
    font-weight: 400 !important;
}
.ht-assigned-empty {
    font-size: 12px !important;
    color: #94a3b8 !important;
    font-style: italic !important;
    padding: 6px 0 !important;
}
.ht-assigned-cards-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)) !important;
    gap: 8px !important;
}
.ht-assigned-card {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 7px 10px !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    transition: all 0.2s ease !important;
}
.ht-assigned-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06) !important;
}
.ht-assigned-card.is-captain {
    border-color: #fde68a !important;
    background: #fffdf5 !important;
}
.ht-assigned-avatar {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    color: #ffffff !important;
    flex-shrink: 0 !important;
    text-transform: uppercase !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08) !important;
    line-height: 1 !important;
    margin: 0 !important;
}
.ht-assigned-info {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    overflow: hidden !important;
}
.ht-assigned-name {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}
.ht-assigned-role {
    font-size: 10px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}
.ht-assigned-actions {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}
.ht-captain-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    background: #fef3c7 !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 12px !important;
    line-height: 1 !important;
    white-space: nowrap !important;
}
.ht-captain-badge i {
    color: #f59e0b !important;
    font-size: 10px !important;
}
.ht-make-captain-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    background: #ffffff !important;
    color: #64748b !important;
    border: 1px solid #cbd5e1 !important;
    font-size: 10px !important;
    font-weight: 500 !important;
    padding: 2px 7px !important;
    border-radius: 12px !important;
    line-height: 1 !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
}
.ht-make-captain-btn:hover {
    background: #fffbeb !important;
    color: #b45309 !important;
    border-color: #fde68a !important;
}
.ht-card-remove-btn {
    width: 22px !important;
    height: 22px !important;
    min-width: 22px !important;
    border-radius: 50% !important;
    border: none !important;
    background: transparent !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    transition: all 0.15s ease !important;
    padding: 0 !important;
    line-height: 1 !important;
}
.ht-card-remove-btn:hover {
    background: #fee2e2 !important;
    color: #ef4444 !important;
}

/* --- Search & Filter Bar --- */
.ht-drawer-search-bar {
    padding: 12px 20px !important;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
}
.ht-search-input-wrap {
    position: relative !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
}
.ht-search-icon {
    position: absolute !important;
    left: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    font-size: 16px !important;
    pointer-events: none !important;
    line-height: 1 !important;
}
.ht-search-input {
    width: 100% !important;
    height: 40px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    padding: 0 36px 0 38px !important;
    font-size: 13px !important;
    font-family: 'Poppins', sans-serif !important;
    color: #0f172a !important;
    outline: none !important;
    transition: all 0.2s ease !important;
    box-sizing: border-box !important;
}
.ht-search-input:focus {
    background: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
}
.ht-search-input::placeholder {
    color: #94a3b8 !important;
}
.ht-search-clear {
    position: absolute !important;
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    border: none !important;
    background: #cbd5e1 !important;
    color: #ffffff !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10px !important;
    padding: 0 !important;
    line-height: 1 !important;
    transition: background 0.15s ease !important;
}
.ht-search-clear:hover {
    background: #94a3b8 !important;
}
.ht-filter-pills {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}
.ht-pill {
    padding: 5px 12px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    line-height: 1.3 !important;
}
.ht-pill:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
.ht-pill.active {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #2563eb !important;
    font-weight: 600 !important;
}

/* --- List Header & Hints --- */
.ht-drawer-list-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 20px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    flex-shrink: 0 !important;
}
.ht-list-title {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    color: #475569 !important;
}
.ht-list-hint {
    font-size: 10px !important;
    color: #94a3b8 !important;
}
.ht-list-hint kbd {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 3px !important;
    padding: 1px 4px !important;
    font-size: 9px !important;
    margin: 0 2px !important;
    color: #475569 !important;
}

/* --- Viewport & Rows --- */
.ht-virtual-viewport {
    flex: 1 1 auto !important;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    background: #ffffff !important;
    position: relative !important;
    outline: none !important;
}
.ht-virtual-viewport::-webkit-scrollbar,
.ht-drawer-assigned-section::-webkit-scrollbar {
    width: 6px !important;
}
.ht-virtual-viewport::-webkit-scrollbar-track,
.ht-drawer-assigned-section::-webkit-scrollbar-track {
    background: #f8fafc !important;
}
.ht-virtual-viewport::-webkit-scrollbar-thumb,
.ht-drawer-assigned-section::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 3px !important;
}
.ht-virtual-viewport::-webkit-scrollbar-thumb:hover,
.ht-drawer-assigned-section::-webkit-scrollbar-thumb:hover {
    background: #94a3b8 !important;
}

.ht-virtual-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    height: 64px !important;
    min-height: 64px !important;
    max-height: 64px !important;
    padding: 0 20px !important;
    box-sizing: border-box !important;
    border-bottom: 1px solid #f1f5f9 !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
    background: #ffffff !important;
}
.ht-virtual-row:hover {
    background: #f8fafc !important;
}
.ht-virtual-row.focused {
    background: #eff6ff !important;
    outline: none !important;
}
.ht-virtual-row.assigned {
    background: #f0fdf4 !important;
}
.ht-virtual-row.assigned:hover {
    background: #dcfce7 !important;
}
.ht-virtual-row.focused.assigned {
    background: #dcfce7 !important;
}

.ht-row-avatar {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    max-width: 38px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
    text-transform: uppercase !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    line-height: 1 !important;
    margin: 0 !important;
}
.ht-row-info {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    overflow: hidden !important;
    margin-left: 14px !important;
}
.ht-row-name {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}
.ht-row-role {
    font-size: 11px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
}
.ht-row-workload {
    flex-shrink: 0 !important;
    margin: 0 14px !important;
}
.ht-workload-tag {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    padding: 3px 9px !important;
    border-radius: 12px !important;
    white-space: nowrap !important;
    line-height: 1.2 !important;
}
.ht-workload-tag i {
    font-size: 12px !important;
}
.ht-workload-tag.low {
    color: #10b981 !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
}
.ht-workload-tag.high {
    color: #ef4444 !important;
    background: #fee2e2 !important;
    border: 1px solid #fecaca !important;
}

.ht-row-action {
    flex-shrink: 0 !important;
}
.ht-assign-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    height: 30px !important;
    padding: 0 12px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
    line-height: 1 !important;
}
.ht-virtual-row:not(.assigned) .ht-assign-btn {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}
.ht-virtual-row:not(.assigned) .ht-assign-btn:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}
.ht-virtual-row.assigned .ht-assign-btn {
    background: #ffffff !important;
    color: #16a34a !important;
    border: 1px solid #86efac !important;
}
.ht-virtual-row.assigned .ht-assign-btn:hover {
    background: #fee2e2 !important;
    color: #ef4444 !important;
    border-color: #fca5a5 !important;
}

/* Empty state */
.ht-empty-state {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 40px 20px !important;
    text-align: center !important;
}
.ht-empty-icon {
    width: 48px !important;
    height: 48px !important;
    border-radius: 50% !important;
    background: #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 22px !important;
    color: #94a3b8 !important;
    margin-bottom: 10px !important;
}
.ht-empty-title {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    margin-bottom: 4px !important;
}
.ht-empty-sub {
    font-size: 11px !important;
    color: #94a3b8 !important;
}
</style>

<div class="hiring-team-drawer-wrapper">
    <!-- 1. Top Assigned Team Section -->
    <div class="ht-drawer-assigned-section" id="recDrawerAssignedContainer"></div>

    <!-- 2. Search & Filter Bar -->
    <div class="ht-drawer-search-bar">
        <div class="ht-search-input-wrap">
            <i class="la la-search ht-search-icon"></i>
            <input type="text" id="recDrawerSearchInput" class="ht-search-input" placeholder="Search team members by name, role..." autocomplete="off" spellcheck="false">
            <button type="button" id="recDrawerSearchClear" class="ht-search-clear" style="display:none !important;" title="Clear Search">
                <i class="la la-times"></i>
            </button>
        </div>
        <div class="ht-filter-pills" role="tablist">
            <button type="button" class="ht-pill active" data-filter="all" id="pillAll">All ({{ count($teamRoster) }})</button>
            <button type="button" class="ht-pill" data-filter="available" id="pillAvailable">Available (≤ 3 jobs)</button>
            <button type="button" class="ht-pill" data-filter="busy" id="pillBusy">High Load (> 3 jobs)</button>
        </div>
    </div>

    <!-- 3. Section Header -->
    <div class="ht-drawer-list-header">
        <span class="ht-list-title" id="recDrawerAvailableCount">AVAILABLE RECRUITERS ({{ count($teamRoster) }})</span>
        <span class="ht-list-hint"><kbd>↑</kbd><kbd>↓</kbd> navigate, <kbd>Enter</kbd> toggle</span>
    </div>

    <!-- 4. Recruiter Roster List -->
    <div class="ht-virtual-viewport" id="recVirtualViewport" role="listbox" aria-label="Recruiter roster" tabindex="0">
        <div id="recVirtualItems"></div>
        <div id="recVirtualEmpty" class="ht-empty-state" style="display:none !important;">
            <div class="ht-empty-icon"><i class="la la-user-slash"></i></div>
            <div class="ht-empty-title">No team members found</div>
            <div class="ht-empty-sub" id="recVirtualEmptySub">Try searching with a different name or clear the filter.</div>
        </div>
    </div>
</div>

<script>
(function() {
    var roster = @json($teamRoster);
    var currentJobId = "{{ $currentJobId }}";
    var captainId = {{ $captainId !== null ? json_encode($captainId) : 'null' }};
    var filterTab = 'all';
    var searchQuery = '';
    var focusedIndex = 0;
    var csrfToken = "{{ csrf_token() }}";
    var updateUrlTemplate = "{{ route('service_company.jobs.hmlistingupdate', ':id') }}";
    var isProcessing = false;

    // Harmonized avatar color palette (Metronic Enterprise Diamond Standard)
    var AVATAR_COLORS = ['#3E97FF', '#7239EA', '#50CD89', '#F6C000', '#F1416C', '#009EF7'];
    function getAvatarColor(str) {
        if (!str) return AVATAR_COLORS[0];
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return AVATAR_COLORS[Math.abs(hash) % AVATAR_COLORS.length];
    }

    function getInitialLetter(name) {
        if (!name) return 'T';
        var trimmed = name.trim();
        return trimmed.charAt(0).toUpperCase();
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Get current filtered list
    function getFilteredList() {
        var query = searchQuery.trim().toLowerCase();
        return roster.filter(function(m) {
            // Filter pill constraint
            if (filterTab === 'available' && m.activeJobs > 3) return false;
            if (filterTab === 'busy' && m.activeJobs <= 3) return false;

            // Search query constraint
            if (query !== '') {
                var nameMatch = (m.name || '').toLowerCase().indexOf(query) !== -1;
                var roleMatch = (m.role || '').toLowerCase().indexOf(query) !== -1;
                if (!nameMatch && !roleMatch) return false;
            }
            return true;
        });
    }

    // Render Assigned Team Section
    function renderAssigned() {
        var container = document.getElementById('recDrawerAssignedContainer');
        if (!container) return;

        var assigned = roster.filter(function(m) { return m.isAssigned; });

        // Sort so the Captain is always first
        assigned.sort(function(a, b) {
            if (a.id === captainId) return -1;
            if (b.id === captainId) return 1;
            return a.name.localeCompare(b.name);
        });

        if (assigned.length === 0) {
            container.innerHTML =
                '<div class="ht-assigned-header">' +
                    '<span class="ht-assigned-title">ASSIGNED TEAM (0)</span>' +
                    '<span class="ht-assigned-sub">Earliest assigned member acts as Job Captain</span>' +
                '</div>' +
                '<div class="ht-assigned-empty">No recruiters assigned yet. Select from the roster below.</div>';
            return;
        }

        var cardsHtml = assigned.map(function(m) {
            var isCap = (m.id === captainId);
            var initial = getInitialLetter(m.name);
            var avatarColor = getAvatarColor(m.name);

            var captainBadge = isCap
                ? '<span class="ht-captain-badge"><i class="fa fa-star"></i> Captain</span>'
                : '<button type="button" class="ht-make-captain-btn js-make-captain" data-user-id="' + m.id + '" title="Promote to Job Captain"><i class="fa fa-star-o"></i> Make Captain</button>';

            return '' +
                '<div class="ht-assigned-card ' + (isCap ? 'is-captain' : '') + '">' +
                    '<div class="ht-assigned-avatar" style="background:' + avatarColor + '">' + escapeHtml(initial) + '</div>' +
                    '<div class="ht-assigned-info">' +
                        '<div class="ht-assigned-name" title="' + escapeHtml(m.name) + '">' + escapeHtml(m.name) + '</div>' +
                        '<div class="ht-assigned-role" title="' + escapeHtml(m.role) + '">' + escapeHtml(m.role) + '</div>' +
                    '</div>' +
                    '<div class="ht-assigned-actions">' +
                        captainBadge +
                        '<button type="button" class="ht-card-remove-btn js-remove-assigned" data-user-id="' + m.id + '" title="Remove Recruiter">' +
                            '<i class="la la-times"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>';
        }).join('');

        container.innerHTML =
            '<div class="ht-assigned-header">' +
                '<span class="ht-assigned-title">ASSIGNED TEAM (' + assigned.length + ')</span>' +
                '<span class="ht-assigned-sub">Earliest assigned member acts as Job Captain</span>' +
            '</div>' +
            '<div class="ht-assigned-cards-grid">' + cardsHtml + '</div>';
    }

    // Render Available Recruiters Roster
    function renderRoster() {
        var itemsContainer = document.getElementById('recVirtualItems');
        var emptyEl = document.getElementById('recVirtualEmpty');
        var emptySub = document.getElementById('recVirtualEmptySub');
        var countEl = document.getElementById('recDrawerAvailableCount');

        if (!itemsContainer) return;

        var filtered = getFilteredList();

        if (countEl) {
            countEl.textContent = 'AVAILABLE RECRUITERS (' + filtered.length + ')';
        }

        if (filtered.length === 0) {
            itemsContainer.innerHTML = '';
            if (emptyEl) emptyEl.style.setProperty('display', 'flex', 'important');
            if (emptySub) {
                if (roster.length === 0) {
                    emptySub.textContent = 'No recruiters are configured for this organization.';
                } else if (searchQuery.trim() !== '') {
                    emptySub.textContent = 'No team members match your search criteria. Try a different name.';
                } else {
                    emptySub.textContent = 'No team members match the selected filter. Try switching tabs.';
                }
            }
            return;
        }

        if (emptyEl) emptyEl.style.setProperty('display', 'none', 'important');

        // Bound focusedIndex
        if (focusedIndex >= filtered.length) {
            focusedIndex = Math.max(0, filtered.length - 1);
        }

        var html = filtered.map(function(m, idx) {
            var isAssigned = !!m.isAssigned;
            var isFocused = (idx === focusedIndex);
            var initial = getInitialLetter(m.name);
            var avatarColor = getAvatarColor(m.name);
            var isLowLoad = (m.activeJobs <= 3);

            var workloadClass = isLowLoad ? 'low' : 'high';
            var workloadText = m.activeJobs + ' active';

            var actionBtn = isAssigned
                ? '<button type="button" class="btn btn-sm ht-assign-btn assigned js-toggle-assign" data-user-id="' + m.id + '" title="Click to unassign"><i class="la la-check"></i> Assigned</button>'
                : '<button type="button" class="btn btn-sm ht-assign-btn js-toggle-assign" data-user-id="' + m.id + '" title="Click to assign">+ Assign</button>';

            return '' +
                '<div class="ht-virtual-row ' + (isAssigned ? 'assigned' : '') + ' ' + (isFocused ? 'focused' : '') + '" ' +
                     'data-user-id="' + m.id + '" data-index="' + idx + '" role="option" aria-selected="' + (isAssigned ? 'true' : 'false') + '">' +
                    '<div class="d-flex align-items-center" style="min-width:0; flex:1 1 auto;">' +
                        '<div class="ht-row-avatar" style="background:' + avatarColor + '">' + escapeHtml(initial) + '</div>' +
                        '<div class="ht-row-info">' +
                            '<div class="ht-row-name">' + escapeHtml(m.name) + '</div>' +
                            '<div class="ht-row-role">' + escapeHtml(m.role) + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="ht-row-workload">' +
                        '<span class="ht-workload-tag ' + workloadClass + '">' +
                            '<i class="la la-briefcase"></i> ' + workloadText +
                        '</span>' +
                    '</div>' +
                    '<div class="ht-row-action">' +
                        actionBtn +
                    '</div>' +
                '</div>';
        }).join('');

        itemsContainer.innerHTML = html;
    }

    // Toggle Assignment (Assign or Remove)
    function toggleAssignment(userId) {
        if (isProcessing) return;
        var member = roster.find(function(m) { return m.id === userId; });
        if (!member) return;

        isProcessing = true;
        var wasAssigned = !!member.isAssigned;
        var willAssign = !wasAssigned;

        // Optimistic State Mutation
        member.isAssigned = willAssign;

        var currentlyAssigned = roster.filter(function(m) { return m.isAssigned; });

        if (willAssign) {
            // If this is the only assigned member, automatically make Captain
            if (currentlyAssigned.length === 1 || !captainId) {
                captainId = member.id;
                member.isCaptain = true;
            }
        } else {
            // Member was removed
            member.isCaptain = false;
            if (captainId === member.id) {
                // Promote earliest remaining assigned member
                if (currentlyAssigned.length > 0) {
                    captainId = currentlyAssigned[0].id;
                    currentlyAssigned[0].isCaptain = true;
                } else {
                    captainId = null;
                }
            }
        }

        renderAssigned();
        renderRoster();

        var assignStatus = willAssign ? 0 : 1; // 0 = insert, 1 = delete
        var url = updateUrlTemplate.replace(':id', userId);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                '_token': csrfToken,
                'currentJobId': currentJobId,
                'currentUserId': userId,
                'assignStatus': assignStatus,
                'makespokesperson': (willAssign && member.isCaptain)
            },
            success: function(response) {
                if (typeof window.fetchCandidates === 'function') {
                    window.fetchCandidates();
                }
            },
            error: function(xhr) {
                // Rollback on network/server error
                member.isAssigned = wasAssigned;
                renderAssigned();
                renderRoster();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ text: 'Failed to update team assignment. Please try again.', icon: 'error' });
                }
            },
            complete: function() {
                isProcessing = false;
            }
        });
    }

    // Make Captain (Set spokesperson)
    function makeCaptain(userId) {
        if (isProcessing) return;
        var target = roster.find(function(m) { return m.id === userId; });
        if (!target || !target.isAssigned) return;

        isProcessing = true;
        var prevCaptainId = captainId;
        captainId = userId;

        roster.forEach(function(m) {
            m.isCaptain = (m.id === userId);
        });

        renderAssigned();
        renderRoster();

        var url = updateUrlTemplate.replace(':id', userId);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                '_token': csrfToken,
                'currentJobId': currentJobId,
                'currentUserId': userId,
                'assignStatus': 3,
                'makespokesperson': true
            },
            success: function(response) {
                if (typeof window.fetchCandidates === 'function') {
                    window.fetchCandidates();
                }
            },
            error: function(xhr) {
                // Rollback
                captainId = prevCaptainId;
                roster.forEach(function(m) {
                    m.isCaptain = (m.id === prevCaptainId);
                });
                renderAssigned();
                renderRoster();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ text: 'Failed to promote captain. Please try again.', icon: 'error' });
                }
            },
            complete: function() {
                isProcessing = false;
            }
        });
    }

    // Bind Event Listeners
    function bindEvents() {
        // Search Input
        var searchInput = document.getElementById('recDrawerSearchInput');
        var clearBtn = document.getElementById('recDrawerSearchClear');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                searchQuery = this.value;
                if (clearBtn) {
                    clearBtn.style.setProperty('display', searchQuery ? 'flex' : 'none', 'important');
                }
                focusedIndex = 0;
                renderRoster();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchQuery = '';
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                clearBtn.style.setProperty('display', 'none', 'important');
                focusedIndex = 0;
                renderRoster();
            });
        }

        // Filter Pills
        var pills = document.querySelectorAll('.ht-pill');
        pills.forEach(function(pill) {
            pill.addEventListener('click', function() {
                pills.forEach(function(p) { p.classList.remove('active'); });
                this.classList.add('active');
                filterTab = this.getAttribute('data-filter') || 'all';
                focusedIndex = 0;
                renderRoster();
            });
        });

        // Click on assigned card Remove button
        $(document).off('click', '.js-remove-assigned').on('click', '.js-remove-assigned', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var userId = parseInt($(this).attr('data-user-id'), 10);
            toggleAssignment(userId);
        });

        // Click on Make Captain button
        $(document).off('click', '.js-make-captain').on('click', '.js-make-captain', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var userId = parseInt($(this).attr('data-user-id'), 10);
            makeCaptain(userId);
        });

        // Click on Assign / Assigned button or row
        $(document).off('click', '.js-toggle-assign').on('click', '.js-toggle-assign', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var userId = parseInt($(this).attr('data-user-id'), 10);
            toggleAssignment(userId);
        });

        $(document).off('click', '.ht-virtual-row').on('click', '.ht-virtual-row', function(e) {
            if ($(e.target).closest('.js-toggle-assign').length) return;
            var userId = parseInt($(this).attr('data-user-id'), 10);
            toggleAssignment(userId);
        });

        // Keyboard Navigation in Viewport
        var viewport = document.getElementById('recVirtualViewport');
        if (viewport) {
            viewport.addEventListener('keydown', function(e) {
                var filtered = getFilteredList();
                if (filtered.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (focusedIndex < filtered.length - 1) {
                        focusedIndex++;
                        renderRoster();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (focusedIndex > 0) {
                        focusedIndex--;
                        renderRoster();
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (filtered[focusedIndex]) {
                        toggleAssignment(filtered[focusedIndex].id);
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    $('#kt_activitiesTEAM_close').click();
                }
            });
        }
    }

    // Initialize Drawer
    renderAssigned();
    renderRoster();
    bindEvents();

    // Auto-focus search input after drawer slides out
    setTimeout(function() {
        var el = document.getElementById('recDrawerSearchInput');
        if (el) el.focus();
    }, 120);
})();
</script>