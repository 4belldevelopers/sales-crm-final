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

/* Canonical Status Badges */
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
    color: #ffffff !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
}

.cd-logo-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
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

/* Sub-Card 2: Experience, Compensation & Location */
.cd-val-salary {
    font-size: 11.5px !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-val-exp {
    font-size: 10.5px !important;
    margin-left: 2px !important;
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
</style>
