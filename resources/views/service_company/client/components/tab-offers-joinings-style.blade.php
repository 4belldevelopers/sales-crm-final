<style>
/* ==========================================================================
   Client Drawer Offer & Joining Tab — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-oj-wrapper, .cd-oj-wrapper *,
.cd-oj-header-card, .cd-oj-header-card *,
.cd-oj-tabs-card, .cd-oj-tabs-card *,
.cd-oj-stream, .cd-oj-stream * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-oj-wrapper,
.cd-oj-stream {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* ==========================================================================
   1. Tier 1: Top Header Toolbar Card
   ========================================================================== */
.cd-oj-header-card {
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

.cd-oj-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-oj-header-icon {
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

.cd-oj-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
    margin: 0 !important;
}

.cd-oj-header-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1.5px 8px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 999px !important;
    line-height: 1.3 !important;
}

.cd-oj-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-oj-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-oj-search-subcard {
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

.cd-oj-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-oj-search-subcard i {
    color: #94a3b8 !important;
    font-size: 12px !important;
    flex-shrink: 0 !important;
}

.cd-oj-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-family: inherit !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    line-height: 36px !important;
}

.cd-oj-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-oj-search-clear {
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    padding: 2px 4px !important;
    font-size: 11px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cd-oj-search-clear:hover {
    color: #ef4444 !important;
}

.cd-oj-action-subcard {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-oj-action-subcard:hover {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    color: #009ef7 !important;
}

/* ==========================================================================
   2. Tier 2: Sub-Tabs Card with White Background
   ========================================================================== */
.cd-oj-tabs-card {
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

.cd-oj-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
}

.cd-oj-tab-btn {
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

.cd-oj-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Active Card-Based Tab: Single Highlight Rule */
.cd-oj-tab-btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

/* Dynamic Count Badges Inside Sub-Tabs */
.cd-oj-tab-badge {
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

.cd-oj-tab-btn.active .cd-oj-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.cd-oj-tab-btn:not(.active) .cd-oj-tab-badge.badge-warning {
    background: #fef3c7 !important;
    color: #b45309 !important;
}
.cd-oj-tab-btn:not(.active) .cd-oj-tab-badge.badge-primary {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}
.cd-oj-tab-btn:not(.active) .cd-oj-tab-badge.badge-success {
    background: #dcfce7 !important;
    color: #15803d !important;
}

.cd-oj-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    display: inline-block !important;
}
.cd-oj-dot.dot-warning { background: #f59e0b !important; }
.cd-oj-dot.dot-primary { background: #3b82f6 !important; }
.cd-oj-dot.dot-success { background: #10b981 !important; }

/* ==========================================================================
   3. Tier 3: Master Primary Candidate Offer/Joining Card
   ========================================================================== */
.cd-oj-card {
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

.cd-oj-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px) !important;
}

/* ==========================================================================
   4. Upper Sub-Cards Grid (5 Discrete Enterprise Sub-Cards)
   Strictly Zero Horizontal Scrollbar via fractional units & minmax(0, ...)
   ========================================================================== */
.cd-oj-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.9fr) minmax(0, 1.4fr) minmax(0, 1.45fr) minmax(0, 1.35fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    max-width: 100% !important;
    align-items: stretch !important;
}

/* Individual Sub-Card Frame */
.cd-oj-subcard {
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

/* Dual Badges in Sub-Card 1 Header */
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

.cd-oj-stage-outline {
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

.cd-oj-stage-outline.stage-offered {
    background: #fff7ed !important;
    color: #ea580c !important;
    border: 1px solid #fed7aa !important;
}

.cd-oj-stage-outline.stage-joined {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
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
.status-hiring { background: #dcfce7 !important; color: #15803d !important; }

/* Sub-Card 1: Candidate Details Body */
.cd-details-body {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}

.cd-oj-avatar-box {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
}

.avatar-red    { background: #fee2e2 !important; color: #dc2626 !important; }
.avatar-orange { background: #fef3c7 !important; color: #d97706 !important; }
.avatar-blue   { background: #dbeafe !important; color: #2563eb !important; }
.avatar-green  { background: #dcfce7 !important; color: #16a34a !important; }
.avatar-purple { background: #f3e8ff !important; color: #9333ea !important; }
.avatar-gray   { background: #f1f5f9 !important; color: #475569 !important; }

.cd-details-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-oj-cand-name {
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

.cd-oj-cand-name:hover {
    color: #009ef7 !important;
}

.cd-oj-cand-role {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
    margin-top: 1px !important;
}

.cd-oj-cand-meta {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10px !important;
    color: #94a3b8 !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-oj-cand-id {
    font-weight: 600 !important;
    color: #0284c7 !important;
}

.cd-oj-cand-employer {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    font-weight: 500 !important;
    color: #64748b !important;
}

/* Sub-Card 2: Job Requisition */
.cd-oj-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
}

.cd-job-meta-row {
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

.cd-job-client {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    font-weight: 600 !important;
    color: #475569 !important;
}

.cd-job-emp-row {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 2px !important;
    min-width: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    font-size: 10px !important;
    color: #64748b !important;
}

.cd-job-emp-type {
    font-weight: 600 !important;
    color: #2563eb !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    flex-shrink: 0 !important;
}

/* Sub-Card 3: Compensation */
.cd-val-salary {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-val-sub {
    font-size: 10px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-oj-comp-change {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    text-decoration: none !important;
    cursor: pointer !important;
}
.cd-oj-comp-change:hover {
    text-decoration: underline !important;
}

/* Sub-Card 4: Offer Status */
.cd-oj-status-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 3px 9px !important;
    border-radius: 999px !important;
    width: fit-content !important;
    line-height: 1.2 !important;
}

.pill-offered {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}

.pill-joined {
    background: #f0fdf4 !important;
    color: #16a34a !important;
    border: 1px solid #bbf7d0 !important;
}

.cd-oj-subtext {
    font-size: 10px !important;
    color: #64748b !important;
    margin-top: 3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-oj-substage-badge {
    display: inline-block !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    margin-top: 2px !important;
    background: #fff7ed !important;
    color: #c2410c !important;
    border: 1px solid #ffedd5 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 100% !important;
}

.cd-oj-substage-badge.badge-joined {
    background: #f0fdf4 !important;
    color: #15803d !important;
    border: 1px solid #dcfce7 !important;
}

/* Sub-Card 5: Joining Schedule */
.cd-val-date {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-oj-countdown-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    margin-top: 2px !important;
    line-height: 1.25 !important;
    width: fit-content !important;
}

.countdown-soon     { color: #dc2626 !important; background: #fef2f2 !important; border: 1px solid #fecaca !important; }
.countdown-tomorrow { color: #d97706 !important; background: #fef3c7 !important; border: 1px solid #fde68a !important; }
.countdown-today    { color: #b45309 !important; background: #fef3c7 !important; border: 1px solid #fde68a !important; }
.countdown-overdue  { color: #b91c1c !important; background: #fee2e2 !important; border: 1px solid #fecaca !important; }
.countdown-joined   { color: #16a34a !important; background: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; }
.countdown-neutral  { color: #64748b !important; background: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; }

/* ==========================================================================
   5. Bottom Strip Sub-Card: Recruiter, Offer Timeline & Stage Action
   ========================================================================== */
.cd-oj-card-bottom {
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

.cd-oj-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-oj-bottom-left strong {
    color: #0f172a !important;
}

.cd-oj-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.cd-oj-stage-action-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    background: #f0f9ff !important;
    border: 1px solid #bae6fd !important;
    border-radius: 6px !important;
    padding: 3px 8px !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    line-height: 1.2 !important;
}

.cd-oj-stage-action-btn:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
}

.cd-bullet {
    color: #cbd5e1 !important;
    font-size: 11px !important;
}

/* ==========================================================================
   6. Clean Empty State
   ========================================================================== */
.cd-oj-empty {
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
    .cd-oj-card-grid {
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.8fr) minmax(0, 1.35fr) minmax(0, 1.4fr) minmax(0, 1.3fr) !important;
        gap: 6px !important;
        padding: 8px 10px !important;
    }
    .cd-oj-subcard {
        padding: 6px 8px !important;
    }
    .cd-oj-cand-name,
    .cd-oj-job-title {
        font-size: 12px !important;
    }
}

@media (max-width: 1100px) {
    .cd-oj-card-grid {
        grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.7fr) minmax(0, 1.25fr) minmax(0, 1.3fr) minmax(0, 1.2fr) !important;
        gap: 5px !important;
        padding: 6px 8px !important;
    }
    .cd-oj-card-bottom {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
    }
}
</style>
