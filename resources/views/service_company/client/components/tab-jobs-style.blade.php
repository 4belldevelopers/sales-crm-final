<style>
/* ==========================================================================
   Client Drawer Jobs Tab — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-jobs-container, .cd-jobs-container *,
.jobx, .jobx * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-jobs-container,
.jobx {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* ==========================================================================
   1. Dynamic Tab Filter Group (Clear Active Background / Single Highlight)
   ========================================================================== */
.cd-jobs-tabs-wrap,
.cl-jobs-filter-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 14px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-jobs-tab-btn,
.cl-jobs-filter-group .btn,
.cl-jobs-filter-group .js-job-filter-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    border-radius: 8px !important;
    background: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    transition: all 0.15s ease-in-out !important;
    display: inline-flex !important;
    align-items: center !important;
    cursor: pointer !important;
    line-height: 1.4 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.cd-jobs-tab-btn:hover,
.cl-jobs-filter-group .btn:hover {
    background: #edf2f7 !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}

/* Explicit Rule: ONLY the selected active tab receives the vibrant highlight */
.cd-jobs-tab-btn.active,
.cl-jobs-filter-group .btn.active,
.cl-jobs-filter-group .js-job-filter-btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
}

.cd-jobs-tab-btn.active .cd-tab-count,
.cd-jobs-tab-btn.active .badge,
.cl-jobs-filter-group .btn.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Tab Counts Pills */
.cd-tab-count,
.cl-jobs-filter-group .badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1px 7px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    margin-left: 6px !important;
    line-height: 1.3 !important;
}

.count-primary { background: #e0f2fe !important; color: #0284c7 !important; }
.count-success { background: #dcfce7 !important; color: #15803d !important; }
.count-warning { background: #fef3c7 !important; color: #b45309 !important; }
.count-danger  { background: #fee2e2 !important; color: #b91c1c !important; }
.count-neutral { background: #f1f5f9 !important; color: #64748b !important; }

/* ==========================================================================
   2. Master Primary Job Card
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
   3. Upper Sub-Cards Grid (5 Compact, Discrete Enterprise Sub-Cards)
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
    gap: 4px !important;
    margin-bottom: 4px !important;
    min-width: 0 !important;
}

.cd-subcard-label {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1 !important;
}

.cd-subcard-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    min-width: 0 !important;
    overflow: hidden !important;
    flex: 1 !important;
    justify-content: center !important;
}

/* ==========================================================================
   Sub-Card 1: Job Details (Company Logo, Title, #ID, Status, Type, Client)
   ========================================================================== */
.cd-details-layout {
    display: flex !important;
    align-items: flex-start !important;
    gap: 9px !important;
    min-width: 0 !important;
}

.cd-job-logo-box {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    max-width: 38px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
}

.cd-job-logo-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    padding: 2px !important;
}

.cd-job-logo-initial {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
}

.cd-details-content {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-bottom: 2px !important;
}

.cd-details-meta {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-job-id {
    font-weight: 700 !important;
    color: #2563eb !important;
}

.cd-bullet {
    color: #cbd5e1 !important;
    font-size: 8.5px !important;
}

.cd-details-submeta {
    font-size: 10px !important;
    color: #94a3b8 !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 1px !important;
    line-height: 1.2 !important;
}

/* Status Pill */
.cd-status-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 1px 6px !important;
    border-radius: 12px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
}

.cd-status-pill .dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.status-hiring   { background: #dcfce7 !important; color: #15803d !important; }
.status-hiring .dot { background-color: #16a34a !important; }

.status-hold     { background: #fef3c7 !important; color: #b45309 !important; }
.status-hold .dot   { background-color: #d97706 !important; }

.status-closed   { background: #fee2e2 !important; color: #b91c1c !important; }
.status-closed .dot { background-color: #dc2626 !important; }

.status-inactive { background: #f1f5f9 !important; color: #64748b !important; }
.status-inactive .dot { background-color: #64748b !important; }

/* ==========================================================================
   Sub-Card 2: Compensation & Experience
   ========================================================================== */
.cd-val-salary {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-val-exp {
    font-size: 11px !important;
    color: #475569 !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-val-loc {
    font-size: 10.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-loc-badge {
    background: #e0f2fe !important;
    color: #0284c7 !important;
    padding: 0 4px !important;
    border-radius: 6px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    margin-left: 2px !important;
}

/* ==========================================================================
   Sub-Card 3: Team / Ownership (No Matching Badge)
   ========================================================================== */
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
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* ==========================================================================
   Sub-Card 4: Agency (Number of Hired Agencies e.g. "3 Agencies" or "—")
   ========================================================================== */
.cd-agency-val {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-agency-empty {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #94a3b8 !important;
    line-height: 1.3 !important;
}

/* ==========================================================================
   Sub-Card 5: Hiring Pipeline (8-Stage Strip + Apps / Interviews Counter)
   ========================================================================== */
.cd-pipeline-track {
    display: flex !important;
    align-items: center !important;
    gap: 2px !important;
    width: 100% !important;
    margin-bottom: 3px !important;
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
   4. Bottom Strip Sub-Card: Timeline, Hiring Manager & Skills Activity
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
   5. Clean Empty State
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