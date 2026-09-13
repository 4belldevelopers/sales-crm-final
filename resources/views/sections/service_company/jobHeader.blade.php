
{{-- ══════════════════════════════════════════════
     Jobs Hero Card  (title + stats + tab row)
══════════════════════════════════════════════ --}}
@php
    $showTop = $tabactive['showTop'] ?? true;
    $showStats = $tabactive['showStats'] ?? true;
@endphp

<div class="jh-wrap {{ $showTop ? '' : 'jh-wrap-stats-only' }}">

    {{-- ── Top row: title / subtitle / action buttons ── --}}
    @if($showTop)
    <div class="jh-top">
        <div>
            <div class="jh-title">Jobs Tracker</div>
            <div class="jh-sub">Manage, track and hire candidates faster.</div>
        </div>
        <div class="jh-actions">
            @if(in_array("add_jobs", $tabactive['userPermissions'] ?? []))
            <button class="jh-btn-primary" data-toggle="modal" data-target="#add_staffing_partners" id="AddjobSection">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                Add Job
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Stat cards ── --}}
    @if($showStats)
    <div class="jh-stats">

        <a href="{{ $tabactive['allRequisition_url'] }}" class="jh-card jh-card-source-all {{ !empty($tabactive['allRequisition_active']) ? 'jh-card-active' : '' }}">
            <div class="jh-icon jh-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                </svg>
            </div>
            <div class="jh-card-body">
                <div class="jh-lbl">Total Jobs</div>
                <div class="jh-count-row">
                    <div class="jh-num" id="{{ $tabactive['allRequisition_id'] }}">{{ $tabactive['allRequisition'] }}</div>
                    <span class="jh-count-link">View Jobs List</span>
                </div>
            </div>
        </a>

        @if($tabactive['productAllowCompany'])
        <a href="{{ $tabactive['clientRequisition_url'] }}" class="jh-card jh-card-source-direct {{ !empty($tabactive['clientRequisition_active']) ? 'jh-card-active' : '' }}">
            <div class="jh-icon jh-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div class="jh-card-body">
                <div class="jh-lbl">Direct Client Jobs</div>
                <div class="jh-count-row">
                    <div class="jh-num" id="{{ $tabactive['clientRequisition_id'] }}">{{ $tabactive['clientRequisition'] }}</div>
                    <span class="jh-count-link">View Jobs List</span>
                </div>
            </div>
        </a>

        <a href="{{ $tabactive['customRequisition_url'] }}" class="jh-card jh-card-source-internal {{ !empty($tabactive['customRequisition_active']) ? 'jh-card-active' : '' }}">
            <div class="jh-icon jh-icon-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <div class="jh-card-body">
                <div class="jh-lbl">Internal Jobs</div>
                <div class="jh-count-row">
                    <div class="jh-num" id="{{ $tabactive['customRequisition_id'] }}">{{ $tabactive['customRequisition'] }}</div>
                    <span class="jh-count-link">View Jobs List</span>
                </div>
            </div>
        </a>

        <a href="{{ $tabactive['jobRequest_url'] }}" class="jh-card jh-card-source-request {{ !empty($tabactive['jobRequest_active']) ? 'jh-card-active' : '' }}">
            <div class="jh-icon jh-icon-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <div class="jh-card-body">
                <div class="jh-lbl">Job Requests</div>
                <div class="jh-count-row">
                    <div class="jh-num" id="{{ $tabactive['jobRequest_id'] }}">{{ $tabactive['jobRequest'] }}</div>
                    <span class="jh-count-link">View Jobs List</span>
                </div>
            </div>
        </a>
        @endif

    </div>
    @endif
</div>

{{-- ── Tab row (below the hero card) ── --}}
@if($tabactive['showTabs'] ?? true)
<div class="jh-tabs-card">
    <a href="#" class="jh-tab jh-tab-active" data-status="all" onclick="filterJobsByStatus('all'); return false;">
        <span class="jh-tab-icon jh-tab-icon-blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        </span>
        All Statuses
        <span class="jh-badge jh-badge-blue">{{ $tabactive['allTabCount'] ?? $tabactive['allRequisition'] }}</span>
    </a>
    <div class="jh-tab-divider"></div>
    <a href="#" class="jh-tab" data-status="active" onclick="filterJobsByStatus('active'); return false;">
        <span class="jh-tab-icon jh-tab-icon-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </span>
        Hiring
        <span class="jh-badge jh-badge-green">{{ $tabactive['hiringCount'] ?? 0 }}</span>
    </a>
    <div class="jh-tab-divider"></div>
    <a href="#" class="jh-tab" data-status="hold" onclick="filterJobsByStatus('hold'); return false;">
        <span class="jh-tab-icon jh-tab-icon-orange">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/>
            </svg>
        </span>
        On Hold
        <span class="jh-badge jh-badge-orange">{{ $tabactive['holdCount'] ?? 0 }}</span>
    </a>
    <div class="jh-tab-divider"></div>
    <a href="#" class="jh-tab" data-status="inactive" onclick="filterJobsByStatus('inactive'); return false;">
        <span class="jh-tab-icon jh-tab-icon-red">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </span>
        Closed
        <span class="jh-badge jh-badge-red">{{ $tabactive['closedCount'] ?? 0 }}</span>
    </a>
    <div class="jh-tab-divider"></div>
    <a href="#" class="jh-tab" data-status="removed" onclick="filterJobsByStatus('removed'); return false;">
        <span class="jh-tab-icon" style="background:#f1f1f4;color:#7e8299;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
        </span>
        Removed
        <span class="jh-badge" style="background:#f1f1f4;color:#7e8299;">{{ $tabactive['removedCount'] ?? 0 }}</span>
    </a>
</div>
@endif
