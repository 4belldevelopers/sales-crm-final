<style>
/* ==========================================================================
   Client Drawer Jobs Tab — Master Enterprise 7-Column Layout (Screenshot 2 Match)
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller Screens
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) across All Screen Sizes
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-jobs-wrapper, .cd-jobs-wrapper *,
.cd-jobs-header-card, .cd-jobs-header-card *,
.cd-jobs-tabs-card, .cd-jobs-tabs-card *,
.jobx, .jobx *,
.table-card, .table-card *,
.table-header, .table-header *,
.job-row, .job-row *,
.job-row-skills, .job-row-skills * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-jobs-wrapper {
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

.jobx {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* Master Table Card */
.table-card,
.jobx .table-card {
    background: #ffffff !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
    border: 1px solid #e4e6ef !important;
    overflow: hidden !important;
    margin-bottom: 16px !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* 7-Column Grid Layout — Pure Responsive Fraction Units with minmax(0, ...)
   Guarantees all 7 columns shrink gracefully on 15.6" (1366x768) without horizontal scroll */
.table-header,
.jobx .table-header,
.job-row,
.jobx .job-row {
    display: grid !important;
    grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(0, 1.2fr) minmax(0, 0.85fr) minmax(0, 0.85fr) minmax(0, 1.8fr) !important;
    align-items: start !important;
    column-gap: 8px !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

/* Table Header */
.table-header,
.jobx .table-header {
    padding: 9px 12px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    align-items: center !important;
}

.th,
.jobx .th {
    font-size: 10.5px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    min-width: 0 !important;
}

.th i,
.jobx .th i {
    font-size: 11px !important;
}

/* Job Row */
.job-row,
.jobx .job-row {
    padding: 10px 12px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    transition: background 0.12s ease !important;
    background: #ffffff !important;
}

.job-row:hover,
.jobx .job-row:hover {
    background: #f8fafc !important;
}

/* Col 1: Job Details */
.col-job,
.jobx .col-job {
    display: flex !important;
    gap: 8px !important;
    align-items: flex-start !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.job-logo,
.jobx .job-logo {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    max-width: 36px !important;
    border-radius: 8px !important;
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    background: #5850ec !important;
    overflow: hidden !important;
}

.job-logo img,
.jobx .job-logo img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.job-info,
.jobx .job-info {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.job-title,
.jobx .job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #181c32 !important;
    line-height: 1.3 !important;
    margin-bottom: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
}

.job-dept,
.jobx .job-dept {
    font-size: 11px !important;
    color: #64748b !important;
    margin-bottom: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.job-client,
.jobx .job-client {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

/* Col 2: Experience & Salary */
.col-exp,
.jobx .col-exp {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    min-width: 0 !important;
    padding-top: 1px !important;
    overflow: hidden !important;
}

.exp-row,
.jobx .exp-row,
.sal-row,
.jobx .sal-row {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: #181c32 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.exp-row i,
.jobx .exp-row i,
.sal-row i,
.jobx .sal-row i {
    font-size: 11px !important;
    color: #94a3b8 !important;
    flex-shrink: 0 !important;
}

.emp-row,
.jobx .emp-row {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    line-height: 1.2 !important;
}

.emp-row i,
.jobx .emp-row i {
    font-size: 10.5px !important;
}

/* Col 3: Hiring & Location */
.col-hl,
.jobx .col-hl {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    min-width: 0 !important;
    padding-top: 1px !important;
    overflow: hidden !important;
}

.badge-hiring,
.jobx .badge-hiring {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    width: fit-content !important;
    cursor: default !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.status-dot,
.jobx .status-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.badge-hiring.active-bg { color: #16a34a !important; }
.badge-hiring.active-bg .status-dot { background-color: #16a34a !important; }

.badge-hiring.hold-bg { color: #d97706 !important; }
.badge-hiring.hold-bg .status-dot { background-color: #d97706 !important; }

.badge-hiring.closed-bg { color: #dc2626 !important; }
.badge-hiring.closed-bg .status-dot { background-color: #dc2626 !important; }

.badge-hiring.inactive-bg { color: #64748b !important; }
.badge-hiring.inactive-bg .status-dot { background-color: #64748b !important; }

.loc-row,
.jobx .loc-row,
.mode-row,
.jobx .mode-row {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    color: #64748b !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.loc-row i,
.jobx .loc-row i,
.mode-row i,
.jobx .mode-row i {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    flex-shrink: 0 !important;
}

.loc-badge,
.jobx .loc-badge {
    background: #e0f2fe !important;
    color: #0284c7 !important;
    padding: 0 4px !important;
    border-radius: 8px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
}

/* Col 4: Hiring Manager */
.col-hm,
.jobx .col-hm {
    min-width: 0 !important;
    padding-top: 1px !important;
    overflow: hidden !important;
}

.hm-name,
.jobx .hm-name {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #181c32 !important;
    margin-bottom: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.hm-title,
.jobx .hm-title {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-bottom: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.hm-client-tag,
.jobx .hm-client-tag {
    font-size: 10.5px !important;
    color: #2563eb !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

/* Col 5 & 6: Team & Agency (Read-Only) */
.col-action,
.jobx .col-action {
    min-width: 0 !important;
    padding-top: 1px !important;
    overflow: hidden !important;
}

.team-meta,
.jobx .team-meta,
.agency-meta,
.jobx .agency-meta {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.match-meta,
.jobx .match-meta {
    font-size: 10.5px !important;
    color: #181c32 !important;
    font-weight: 600 !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

/* Col 7: Pipeline Stages (Read-Only) */
.col-pipeline,
.jobx .col-pipeline {
    padding-top: 1px !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.pipeline-stages,
.jobx .pipeline-stages {
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 2px !important;
    margin-bottom: 3px !important;
    width: 100% !important;
}

.stage,
.jobx .stage {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 1px !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.stage-label,
.jobx .stage-label {
    font-size: 7.5px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    line-height: 1 !important;
}

.stage-badge,
.jobx .stage-badge {
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

.s-inw, .jobx .s-inw { background: #fef9c3 !important; color: #854d0e !important; }
.s-sou, .jobx .s-sou { background: #f1f5f9 !important; color: #334155 !important; }
.s-scr, .jobx .s-scr { background: #fae8ff !important; color: #86198f !important; }
.s-eva, .jobx .s-eva { background: #e0f2fe !important; color: #0369a1 !important; }
.s-neg, .jobx .s-neg { background: #fce7f3 !important; color: #9d174d !important; }
.s-off, .jobx .s-off { background: #ffedd5 !important; color: #9a3412 !important; }
.s-joi, .jobx .s-joi { background: #dcfce7 !important; color: #166534 !important; }
.s-rej, .jobx .s-rej { background: #fee2e2 !important; color: #991b1b !important; }

.pipeline-total,
.jobx .pipeline-total {
    font-size: 9.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.pipeline-total strong,
.jobx .pipeline-total strong {
    color: #181c32 !important;
    font-weight: 700 !important;
}

/* Bottom Skills & Date Bar */
.job-row-skills,
.jobx .job-row-skills {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
    padding: 3px 12px 9px 56px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    font-size: 10.5px !important;
    width: 100% !important;
    background: #ffffff !important;
    overflow: hidden !important;
}

.job-row:last-of-type + .job-row-skills,
.job-row-skills:last-child,
.jobx .job-row:last-of-type + .job-row-skills,
.jobx .job-row-skills:last-child {
    border-bottom: none !important;
}

.job-row-skills .job-date,
.jobx .job-row-skills .job-date {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-right: 4px !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
}

.job-row-skills .job-date i,
.jobx .job-row-skills .job-date i {
    font-size: 10px !important;
    color: #94a3b8 !important;
}

.job-row-skills .sk-tag,
.jobx .job-row-skills .sk-tag {
    display: inline-flex !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 10px !important;
    font-weight: 500 !important;
    border: 1px solid #e2e8f0 !important;
    white-space: nowrap !important;
    line-height: 1.4 !important;
}

.job-empty,
.jobx .job-empty {
    padding: 36px 14px !important;
    text-align: center !important;
    color: #94a3b8 !important;
    font-size: 12.5px !important;
}

/* ==========================================================================
   Responsive Scaling for 15.6" Screens (1366x768 & 1920x1080) & Smaller
   ========================================================================== */
@media (max-width: 1440px) {
    .table-header,
    .jobx .table-header,
    .job-row,
    .jobx .job-row {
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.1fr) minmax(0, 1.1fr) minmax(0, 1.15fr) minmax(0, 0.8fr) minmax(0, 0.8fr) minmax(0, 1.75fr) !important;
        column-gap: 6px !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .job-row-skills,
    .jobx .job-row-skills {
        padding-left: 48px !important;
        padding-right: 8px !important;
    }
    .job-title,
    .jobx .job-title {
        font-size: 12px !important;
    }
    .stage-badge,
    .jobx .stage-badge {
        max-width: 16px !important;
        height: 14px !important;
        font-size: 8px !important;
    }
}

@media (max-width: 1100px) {
    .table-header,
    .jobx .table-header,
    .job-row,
    .jobx .job-row {
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr) minmax(0, 0.75fr) minmax(0, 0.75fr) minmax(0, 1.6fr) !important;
        column-gap: 4px !important;
    }
    .job-row-skills,
    .jobx .job-row-skills {
        padding-left: 42px !important;
    }
}
</style>