<style>
/* ==========================================================================
   Client Drawer Jobs Tab — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-jobs-wrapper, .cd-jobs-wrapper *,
.cd-jobs-header-card, .cd-jobs-header-card *,
.cd-jobs-tabs-card, .cd-jobs-tabs-card *,
.cd-jobs-container, .cd-jobs-container *,
.jobx, .jobx * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-jobs-wrapper,
.cd-jobs-container,
.jobx {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* ==========================================================================
   1. Top Header Card (Card-Based Design with Search Bar at Right Corner)
   ========================================================================== */
.cd-jobs-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    max-width: 100% !important;
}

.cd-jobs-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-jobs-header-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    flex-shrink: 0 !important;
}

.cd-jobs-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
}

.cd-jobs-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-jobs-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-jobs-search-subcard {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 36px !important;
    width: 280px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-jobs-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-jobs-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 12.5px !important;
    flex-shrink: 0 !important;
}

.cd-jobs-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    line-height: 36px !important;
    font-family: inherit !important;
}

.cd-jobs-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 12px !important;
}

.cd-jobs-search-clear {
    background: none !important;
    border: none !important;
    color: #94a3b8 !important;
    font-size: 11px !important;
    padding: 0 !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cd-jobs-search-clear:hover {
    color: #475569 !important;
}

.cd-jobs-add-btn {
    height: 36px !important;
    padding: 0 14px !important;
    font-size: 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border-radius: 8px !important;
}

/* ==========================================================================
   2. Sub-Tabs in Card-Based Design with White Background
   ========================================================================== */
.cd-jobs-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    max-width: 100% !important;
}

.cd-jobs-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
}

/* Individual Card-Based Sub-Tab Button */
.cd-jobs-tab-btn,
.cl-jobs-filter-group .btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.cd-jobs-tab-btn:hover,
.cl-jobs-filter-group .btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Active Card-Based Tab: Highlighted */
.cd-jobs-tab-btn.active,
.cl-jobs-filter-group .btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

/* Dynamic Count Badges Inside Sub-Tabs */
.cd-jobs-tab-badge,
.cl-jobs-filter-group .btn .badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 20px !important;
    height: 18px !important;
    padding: 0 6px !important;
    border-radius: 10px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    line-height: 1 !important;
    transition: all 0.15s ease !important;
}

.cd-jobs-tab-btn.active .cd-jobs-tab-badge,
.cl-jobs-filter-group .btn.active .badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

/* Status-specific subtle badge colors when NOT active */
.cd-jobs-tab-btn:not(.active) .cd-jobs-tab-badge.badge-hiring,
.cl-jobs-filter-group .btn:not(.active) .badge.badge-light-success {
    background: #dcfce7 !important;
    color: #15803d !important;
}
.cd-jobs-tab-btn:not(.active) .cd-jobs-tab-badge.badge-hold,
.cl-jobs-filter-group .btn:not(.active) .badge.badge-light-warning {
    background: #fef3c7 !important;
    color: #b45309 !important;
}
.cd-jobs-tab-btn:not(.active) .cd-jobs-tab-badge.badge-closed,
.cl-jobs-filter-group .btn:not(.active) .badge.badge-light-danger {
    background: #fee2e2 !important;
    color: #b91c1c !important;
}
.cd-jobs-tab-btn:not(.active) .cd-jobs-tab-badge.badge-inactive,
.cl-jobs-filter-group .btn:not(.active) .badge.badge-light-secondary {
    background: #f1f5f9 !important;
    color: #64748b !important;
}

/* ==========================================================================
   3. Master Primary Job Card
   ========================================================================== */
.cd-job-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease-in-out !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

.cd-job-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* ==========================================================================
   4. Upper Sub-Cards Grid (5 Compact, Discrete Enterprise Sub-Cards)
   ========================================================================== */
.cd-job-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.4fr) minmax(0, 1.05fr) minmax(0, 0.85fr) minmax(0, 1.6fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    max-width: 100% !important;
    align-items: stretch !important;
}

/* Individual Sub-Card Frame */
.cd-job-subcard {
    background: #f8fafc !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 8px !important;
    padding: 7px 9px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-subcard-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 5px !important;
    gap: 4px !important;
}

.cd-subcard-label {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Canonical Status Badges & Client Type Classification */
.cd-header-badges {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

.cd-badge-client-type {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    line-height: 1.3 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.cd-badge-client-type i {
    font-size: 8px !important;
}

.cd-badge-client-type.type-direct {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}

.cd-badge-client-type.type-internal {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}

.cd-status-badge {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    line-height: 1.3 !important;
    flex-shrink: 0 !important;
}

.status-hiring   { background: #dcfce7 !important; color: #15803d !important; }
.status-hold     { background: #fef3c7 !important; color: #b45309 !important; }
.status-closed   { background: #fee2e2 !important; color: #b91c1c !important; }
.status-inactive { background: #f1f5f9 !important; color: #64748b !important; }

/* Sub-Card 1: Job Details */
.cd-details-body {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}

.cd-logo-box {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    color: #009ef7 !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
}

.cd-logo-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    background-color: #ffffff !important;
}

.cd-logo-box span {
    color: #009ef7 !important;
    font-weight: 700 !important;
    font-size: 14px !important;
}

.cd-details-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
    transition: color 0.12s ease !important;
}

.cd-job-title:hover {
    color: #009ef7 !important;
}

.cd-job-meta {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-job-id {
    font-weight: 600 !important;
    color: #0284c7 !important;
}

.cd-job-dept {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-job-company {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-job-client-row {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 2px !important;
    min-width: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
}

.cd-job-client-name {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 155px !important;
}

.cd-job-emp-type {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    flex-shrink: 0 !important;
}

.cd-job-emp-type i {
    font-size: 9px !important;
}

/* Sub-Card 2: Compensation & Experience */
.cd-val-salary {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-val-exp {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #475569 !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-val-exp i {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    flex-shrink: 0 !important;
}

.cd-val-type {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-val-loc {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-loc-badge {
    background: #e0f2fe !important;
    color: #0284c7 !important;
    padding: 0 4px !important;
    border-radius: 8px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
}

.cd-bullet {
    color: #cbd5e1 !important;
    font-size: 8px !important;
}

/* Sub-Card 3: Team / Ownership */
.cd-val-name {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-val-sub {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 4: Agency */
.cd-agency-val {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0284c7 !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-agency-empty {
    font-size: 13px !important;
    color: #94a3b8 !important;
    font-weight: 500 !important;
}

/* Sub-Card 5: Hiring Pipeline (8 Stages + Counters) */
.cd-pipeline-track {
    display: flex !important;
    align-items: center !important;
    gap: 2px !important;
    margin-bottom: 4px !important;
    width: 100% !important;
}

.cd-track-stage {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 1px !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.cd-track-label {
    font-size: 7px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    line-height: 1 !important;
    letter-spacing: 0.2px !important;
}

.cd-track-badge {
    width: 100% !important;
    max-width: 18px !important;
    height: 15px !important;
    border-radius: 3px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 8.5px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
}

.s-inw { background: #fef9c3 !important; color: #854d0e !important; }
.s-sou { background: #f1f5f9 !important; color: #334155 !important; }
.s-scr { background: #fae8ff !important; color: #86198f !important; }
.s-eva { background: #e0f2fe !important; color: #0369a1 !important; }
.s-neg { background: #fce7f3 !important; color: #9d174d !important; }
.s-off { background: #ffedd5 !important; color: #9a3412 !important; }
.s-joi { background: #dcfce7 !important; color: #166534 !important; }
.s-rej { background: #fee2e2 !important; color: #991b1b !important; }

.cd-track-summary {
    font-size: 9.5px !important;
    color: #64748b !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    line-height: 1.2 !important;
}

.cd-track-summary strong {
    color: #0f172a !important;
    font-weight: 700 !important;
}

/* ==========================================================================
   5. Bottom Strip Sub-Card: Timeline, Hiring Manager & Skills Activity
   ========================================================================== */
.cd-job-card-bottom {
    background: #ffffff !important;
    border-top: 1px solid #f1f5f9 !important;
    padding: 6px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    width: 100% !important;
    overflow: hidden !important;
}

.cd-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    flex-shrink: 0 !important;
    white-space: nowrap !important;
}

.cd-bottom-skills {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
    white-space: nowrap !important;
    min-width: 0 !important;
}

.cd-bottom-skills::-webkit-scrollbar {
    display: none !important;
}

.cd-skill-pill {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1px 7px !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 10px !important;
    font-weight: 500 !important;
    border: 1px solid #e2e8f0 !important;
    white-space: nowrap !important;
    line-height: 1.4 !important;
    flex-shrink: 0 !important;
}

/* ==========================================================================
   6. Clean Empty State
   ========================================================================== */
.cd-jobs-empty {
    padding: 42px 16px !important;
    text-align: center !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    color: #94a3b8 !important;
}

/* ==========================================================================
   Responsive Scaling for 15.6" Screens (1366x768 & 1920x1080) & Smaller
   ========================================================================== */
@media (max-width: 1440px) {
    .cd-job-card-grid {
        grid-template-columns: minmax(0, 2.4fr) minmax(0, 1.3fr) minmax(0, 0.95fr) minmax(0, 0.8fr) minmax(0, 1.55fr) !important;
        gap: 6px !important;
        padding: 8px 10px !important;
    }
    .cd-job-subcard {
        padding: 6px 8px !important;
    }
    .cd-job-title {
        font-size: 12px !important;
    }
    .cd-track-badge {
        max-width: 16px !important;
        height: 14px !important;
        font-size: 8px !important;
    }
}

@media (max-width: 1100px) {
    .cd-job-card-grid {
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.2fr) minmax(0, 0.9fr) minmax(0, 0.75fr) minmax(0, 1.45fr) !important;
        gap: 4px !important;
        padding: 6px 8px !important;
    }
    .cd-job-card-bottom {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
    }
}

/* ==========================================================================
   Master Job Drawer Read-Only View Rules
   Suppresses all Edit/Update/Delete/Status controls defensively
   ========================================================================== */
#kt_activitiesJob_show.drawer-readonly-view .job-action-btn.edit,
#kt_activitiesJob_show.drawer-readonly-view a[href*="jobs/edit"],
#kt_activitiesJob_show.drawer-readonly-view a[href*="/edit"],
#kt_activitiesJob_show.drawer-readonly-view .info-card-edit,
#kt_activitiesJob_show.drawer-readonly-view .btn-edit,
#kt_activitiesJob_show.drawer-readonly-view .spartner-listing,
#kt_activitiesJob_show.drawer-readonly-view .hm-listing,
#kt_activitiesJob_show.drawer-readonly-view .status_stage,
#kt_activitiesJob_show.drawer-readonly-view .job-drawer-add-candidate,
#kt_activitiesJob_show.drawer-readonly-view #editPopup,
#kt_activitiesJob_show.drawer-readonly-view #candidatePopup,
#kt_activitiesJob_show.drawer-readonly-view .delete-question,
#kt_activitiesJob_show.drawer-readonly-view .remove-question,
#kt_activitiesJob_show.drawer-readonly-view .toggle-question,
#kt_activitiesJob_show.drawer-readonly-view [id^="addQuestionBtn_"],
#kt_activitiesJob_show.drawer-readonly-view .jd-empty-state-action[onclick*="addQuestionBtn"],
#kt_activitiesJob_show.drawer-readonly-view a[href*="settings#terms"] {
    display: none !important;
    pointer-events: none !important;
}

#kt_activitiesJob_show {
    z-index: 999999 !important;
}

.cd-job-card {
    cursor: pointer !important;
}
.cd-job-card:hover {
    border-color: #009ef7 !important;
    box-shadow: 0 4px 14px rgba(0, 158, 247, 0.12) !important;
}
.cd-job-card:hover .cd-job-title {
    color: #009ef7 !important;
}
</style>

<script>
window.openJobDrawer = window.openJobDrawer || function(jobId, options) {
    if (!jobId) return;
    var isReadOnly = true;
    if (typeof options === 'object' && options !== null && options.readOnly !== undefined) {
        isReadOnly = !!options.readOnly;
    } else if (typeof options === 'boolean') {
        isReadOnly = options;
    }

    var $drawer = $('#kt_activitiesJob_show');
    if (!$drawer.length) {
        $('body').append(`
            <div id="kt_activitiesJob_show_toggle" style="display:none;"></div>
            <div id="kt_activitiesJob_show" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
                data-kt-drawer-toggle="#kt_activitiesJob_show_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
                <div class="card shadow-none rounded-0 w-100">
                    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_show_body"></div>
                </div>
            </div>
        `);
        $drawer = $('#kt_activitiesJob_show');
    }

    if (isReadOnly) {
        $drawer.addClass('drawer-readonly-view');
    } else {
        $drawer.removeClass('drawer-readonly-view');
    }

    // Anti-stale DOM reset: immediately show loader
    $('#kt_activitiesJob_show_body').html(
        '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
        '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
        '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Requisition Details…</div>' +
        '  <div class="text-muted fs-7">Retrieving verified requisition details</div>' +
        '</div>'
    );

    if (typeof KTDrawer !== 'undefined') {
        var drawerInst = KTDrawer.getInstance(document.getElementById('kt_activitiesJob_show'));
        if (drawerInst) {
            drawerInst.show();
        } else {
            $('#kt_activitiesJob_show_toggle').trigger('click');
        }
    } else {
        $('#kt_activitiesJob_show_toggle').trigger('click');
    }

    $drawer.css('z-index', '999999');

    var url = "{{ route('service_company.jobs.viewmore', ':id') }}?viewmore=viewmore" + (isReadOnly ? '&readonly=1&read_only=1' : '');
    url = url.replace(':id', jobId);

    $.ajax({
        type: 'GET',
        url: url,
        success: function(response) {
            if (response && response.view) {
                $('#kt_activitiesJob_show_body').html(response.view);
            } else if (typeof response === 'string') {
                $('#kt_activitiesJob_show_body').html(response);
            }
            if (isReadOnly) {
                $drawer.addClass('drawer-readonly-view');
                $drawer.find('.job-action-btn.edit, a[href*="jobs/edit"], a[href*="/edit"], .info-card-edit, .btn-edit, .spartner-listing, .hm-listing, .status_stage, .job-drawer-add-candidate').remove();
            }
        },
        error: function() {
            $('#kt_activitiesJob_show_body').html(
                '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                '  <div class="text-danger fw-bold fs-4 mb-2">Error</div>' +
                '  <div class="text-gray-700 fs-6">Unable to load requisition details. Please try again.</div>' +
                '</div>'
            );
        }
    });
};

window.openJobDetailsDrawer = window.openJobDrawer;
if (typeof JobStateManager === 'undefined') window.JobStateManager = {};
window.JobStateManager.openJobDrawer = window.openJobDrawer;

// ==========================================================================
// Browser-First Pipeline & Formatting Engine (0% Server Load)
// ==========================================================================
window.formatGlobalCompensation = window.formatGlobalCompensation || function(minSal, maxSal, currencyCodeOrId) {
    var symbolMap = {
        'INR': '₹', '132': '₹', '0': '₹',
        'USD': '$', '1': '$',
        'EUR': '€', '2': '€',
        'GBP': '£', '3': '£',
        'AED': 'AED',
        'CAD': 'CA$',
        'AUD': 'A$',
        'SGD': 'S$',
        'JPY': '¥'
    };
    var currKey = String(currencyCodeOrId || '132').toUpperCase().trim();
    var sym = symbolMap[currKey] || (isNaN(currKey) ? currKey : '$');

    function fmtK(num) {
        num = parseFloat(num) || 0;
        if (num <= 0) return '0';
        if (num >= 1000000) {
            var v = Math.round((num / 1000000) * 10) / 10;
            return (v % 1 === 0 ? v.toFixed(0) : v.toString()) + 'M';
        }
        if (num >= 1000) {
            var v = Math.round((num / 1000) * 10) / 10;
            return (v % 1 === 0 ? v.toFixed(0) : v.toString()) + 'K';
        }
        return Math.round(num).toString();
    }

    var minNum = parseFloat(minSal) || 0;
    var maxNum = parseFloat(maxSal) || 0;
    var minK = fmtK(minNum);
    var maxK = fmtK(maxNum);

    if (minNum > 0 && maxNum > 0) {
        return sym + ' ' + minK + ' – ' + maxK;
    } else if (minNum > 0) {
        return sym + ' ' + minK;
    } else if (maxNum > 0) {
        return sym + ' ' + maxK;
    }
    return sym + ' 0';
};

window.ClientJobsPipelineEngine = window.ClientJobsPipelineEngine || {
    recalculate: function(clientType) {
        var isDirect = (clientType === 'direct')
            || ($('#kt_activitiesDirectClient').length > 0 && $('#kt_activitiesDirectClient').is(':visible'));
        var containerSelector = isDirect 
            ? '#kt_activitiesDirectClient #directclientJobsListajax' 
            : '#kt_activitiesClient #clientJobsListajax';
        var $container = $(containerSelector);
        if (!$container.length) return;

        var $cards = $container.find('.cd-job-card');
        if (!$cards.length) return;

        var candidates = [];
        if (isDirect && window.ClientCandidateStoreDirect && Array.isArray(window.ClientCandidateStoreDirect.allCandidates)) {
            candidates = window.ClientCandidateStoreDirect.allCandidates;
        } else if (!isDirect && window.ClientCandidateStore && Array.isArray(window.ClientCandidateStore.allCandidates)) {
            candidates = window.ClientCandidateStore.allCandidates;
        }

        // 0% Server Load: If candidates are not hydrated yet in browser memory, keep server-rendered projection
        if (!candidates || candidates.length === 0) {
            return;
        }

        $cards.each(function() {
            var $card = $(this);
            var jobId = parseInt($card.attr('data-job-id') || $card.data('job-id'), 10);
            if (!jobId) return;

            // Strict Job-Level Scope: Filter candidates strictly belonging to this job
            var jobCands = candidates.filter(function(cand) {
                var cJobId = parseInt(cand.job_id || cand.jobId, 10);
                return cJobId === jobId;
            });

            var counts = {
                1: 0, // SOU
                2: 0, // SCR
                3: 0, // EVA
                4: 0, // NEG
                5: 0, // OFF
                6: 0, // JOI
                7: 0, // REJ
                8: 0  // INW
            };

            var totalApps = 0;
            var totalInterviews = 0;
            var totalOffers = 0;
            var totalJoined = 0;
            var totalSourcing = 0;

            jobCands.forEach(function(cand) {
                var jSid = parseInt(cand.job_master_status_id, 10);
                var mSid = parseInt(cand.master_status_id, 10);
                var sid = (!isNaN(jSid) && jSid > 0) ? jSid : ((!isNaN(mSid) && mSid > 0) ? mSid : 0);

                var isInReview = (sid === 8);
                if (!isInReview) {
                    var sName = String(cand.child_status_name || cand.master_status_name || cand.name || '').toLowerCase().trim();
                    if (sid <= 0 && (sName === 'inreview' || sName === 'in review')) {
                        isInReview = true;
                    }
                }

                // Status 8 (In Review) and Status 9 (Archived) must NOT be included anywhere for Direct Client or Agency
                if (isInReview || sid === 8 || sid === 9 || jSid === 8 || jSid === 9 || mSid === 8 || mSid === 9) {
                    return; // Dropped client-side
                }

                if (sid === 1) {
                    counts[1]++;
                    totalSourcing++;
                    totalApps++;
                } else if (sid === 2) {
                    counts[2]++;
                    totalApps++;
                } else if (sid === 3) {
                    counts[3]++;
                    totalInterviews++;
                    totalApps++;
                } else if (sid === 4) {
                    counts[4]++;
                    totalInterviews++;
                    totalApps++;
                } else if (sid === 5) {
                    counts[5]++;
                    totalOffers++;
                    totalApps++;
                } else if (sid === 6) {
                    counts[6]++;
                    totalJoined++;
                    totalApps++;
                } else if (sid === 7) {
                    counts[7]++;
                    totalApps++;
                } else {
                    var sName = String(cand.child_status_name || cand.master_status_name || cand.name || '').toLowerCase().trim();
                    if (sName.indexOf('inreview') !== -1 || sName.indexOf('in review') !== -1 || sName.indexOf('archived') !== -1) { return; }
                    else if (sName.indexOf('sourc') !== -1) { counts[1]++; totalSourcing++; totalApps++; }
                    else if (sName.indexOf('screen') !== -1) { counts[2]++; totalApps++; }
                    else if (sName.indexOf('eval') !== -1 || sName.indexOf('interview') !== -1) { counts[3]++; totalInterviews++; totalApps++; }
                    else if (sName.indexOf('negot') !== -1) { counts[4]++; totalInterviews++; totalApps++; }
                    else if (sName.indexOf('offer') !== -1) { counts[5]++; totalOffers++; totalApps++; }
                    else if (sName.indexOf('join') !== -1 || sName.indexOf('onboard') !== -1) { counts[6]++; totalJoined++; totalApps++; }
                    else if (sName.indexOf('reject') !== -1) { counts[7]++; totalApps++; }
                    else { counts[1]++; totalSourcing++; totalApps++; }
                }
            });

            // Update 8 track badge elements
            $card.find('.s-inw').text(counts[8]);
            $card.find('.s-sou').text(counts[1]);
            $card.find('.s-scr').text(counts[2]);
            $card.find('.s-eva').text(counts[3]);
            $card.find('.s-neg').text(counts[4]);
            $card.find('.s-off').text(counts[5]);
            $card.find('.s-joi').text(counts[6]);
            $card.find('.s-rej').text(counts[7]);

            // Update track summary strip: strictly Applications and Interviews with counters
            $card.find('.cd-track-summary').html(
                `<span>Applications: <strong>${totalApps}</strong></span>` +
                `<span class="cd-bullet">&bull;</span>` +
                `<span>Interviews: <strong>${totalInterviews}</strong></span>`
            );
        });
    }
};

window.recalculateJobsTabPipelines = window.recalculateJobsTabPipelines || function(clientType) {
    if (window.ClientJobsPipelineEngine && typeof window.ClientJobsPipelineEngine.recalculate === 'function') {
        window.ClientJobsPipelineEngine.recalculate(clientType);
    }
};
</script>
