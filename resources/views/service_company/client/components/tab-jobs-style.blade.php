<style>
/* ==========================================================================
   Client Drawer Jobs Tab — Scoped Enterprise Metronic Styles (Rule 10)
   Optimized for 15.6" Screens (1366x768 and 1920x1080)
   ========================================================================== */

.cl-jobs-container {
    width: 100%;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* 5-Option Status Filter Group */
.cl-jobs-filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.cl-jobs-filter-group .btn {
    font-size: 12px;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
}

.cl-jobs-filter-group .btn.active {
    background-color: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
}

.cl-jobs-filter-group .btn.active .badge {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Job Row Grid Structure (Progressive Disclosure) */
.cl-job-row {
    display: grid;
    grid-template-columns: 2.8fr 1.3fr 1.1fr 2.1fr 0.7fr;
    gap: 12px;
    align-items: center;
    background: #ffffff;
    border: 1px solid #eff2f5;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 8px;
    transition: box-shadow 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

.cl-job-row:hover {
    border-color: #b5e4f9;
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.06);
}

/* Col 1: Job Info & Branding */
.cl-job-col-info {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.cl-job-logo-box {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    color: #ffffff;
    overflow: hidden;
    background: #f1f5f9;
}

.cl-job-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cl-job-meta-title {
    min-width: 0;
}

.cl-job-title-link {
    font-size: 13.5px;
    font-weight: 600;
    color: #181c32;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    cursor: pointer;
}

.cl-job-title-link:hover {
    color: #009ef7;
}

.cl-job-dept-text {
    font-size: 11.5px;
    color: #7e8299;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cl-job-client-tag {
    font-size: 11px;
    color: #a1a5b7;
    margin-top: 1px;
}

/* Col 2: Experience & Compensation */
.cl-job-col-exp {
    font-size: 12px;
    color: #475569;
    line-height: 1.4;
}

.cl-job-exp-row, .cl-job-sal-row {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.cl-job-exp-row svg, .cl-job-sal-row svg {
    width: 13px;
    height: 13px;
    color: #94a3b8;
}

.cl-job-type-pill {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 500;
    padding: 1px 7px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #475569;
    margin-top: 2px;
}

/* Col 3: Status Pill & Updated Timestamp */
.cl-job-col-status {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.badge-hiring {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: 0.2px;
}

.badge-hiring.active-bg {
    background: #e8fff3;
    color: #50cd89;
    border: 1px solid #b7f5d4;
}

.badge-hiring.hold-bg {
    background: #fff8dd;
    color: #ffc700;
    border: 1px solid #ffe79e;
}

.badge-hiring.closed-bg {
    background: #ffeef1;
    color: #f1416c;
    border: 1px solid #f8c1cb;
}

.badge-hiring.inactive-bg {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}

.js-status-timestamp {
    font-size: 10.5px;
    color: #94a3b8;
}

/* Col 4: Pipeline Compact Strip */
.cl-job-col-pipeline {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
    overflow-x: auto;
}

.cl-pipeline-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    padding: 3px 4px;
    border-radius: 5px;
    text-decoration: none;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: background 0.1s ease;
}

.cl-pipeline-pill:hover {
    background: #e2e8f0;
}

.cl-pipeline-label {
    font-size: 9px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}

.cl-pipeline-count {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
}

/* Col 5: Actions */
.cl-job-col-actions {
    display: flex;
    justify-content: flex-end;
}

/* Metronic Empty State */
.cl-jobs-empty-state {
    text-align: center;
    padding: 48px 16px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    margin: 20px 0;
}

.cl-jobs-empty-state svg {
    width: 48px;
    height: 48px;
    color: #94a3b8;
    margin-bottom: 12px;
}

.cl-jobs-empty-title {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
}

.cl-jobs-empty-desc {
    font-size: 12px;
    color: #64748b;
}

/* 1366x768 Screen Adjustment */
@media (max-width: 1400px) {
    .cl-job-row {
        grid-template-columns: 2.5fr 1.3fr 1.1fr 2.3fr 0.6fr;
        gap: 8px;
        padding: 10px 12px;
    }
    .cl-job-title-link {
        font-size: 12.5px;
    }
    .cl-pipeline-pill {
        min-width: 32px;
        padding: 2px 3px;
    }
}

/* Colored Status Dot */
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
    margin-right: 4px;
}
.badge-hiring.active-bg .status-dot { background-color: #50cd89; }
.badge-hiring.hold-bg .status-dot   { background-color: #ffc700; }
.badge-hiring.closed-bg .status-dot { background-color: #f1416c; }
.badge-hiring.inactive-bg .status-dot { background-color: #64748b; }

/* Filter Group styling */
.cl-jobs-filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.cl-jobs-filter-group .btn {
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    background: #f5f8fa;
    color: #5e6278;
    border: 1px solid #e4e6ef;
    transition: all 0.15s ease;
}

.cl-jobs-filter-group .btn:hover {
    background: #eef3f7;
    color: #181c32;
}

.cl-jobs-filter-group .btn.active {
    background: #181c32;
    color: #ffffff;
    border-color: #181c32;
}

.cl-jobs-filter-group .btn.active .badge {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}
</style>
