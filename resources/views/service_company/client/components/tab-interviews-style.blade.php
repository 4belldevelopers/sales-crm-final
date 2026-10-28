<style>
/* ==========================================================================
   Client Drawer Interviews Tab — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   Tenancy Scoped & Strict CQRS (100% Elasticsearch Reads / 0% MySQL Reads)
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-it-wrapper, .cd-it-wrapper *,
.cd-it-header-card, .cd-it-header-card *,
.cd-it-tabs-card, .cd-it-tabs-card *,
.cd-it-stream, .cd-it-stream *,
.cd-it-pagination-card, .cd-it-pagination-card * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-it-wrapper,
.cd-it-stream {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* ==========================================================================
   1. Tier 1: Top Header Toolbar Card
   ========================================================================== */
.cd-it-header-card {
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

.cd-it-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-it-header-icon {
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

.cd-it-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
    margin: 0 !important;
}

.cd-it-header-badge {
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

.cd-it-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-it-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-it-search-subcard {
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

.cd-it-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-it-search-subcard i {
    color: #94a3b8 !important;
    font-size: 12px !important;
    flex-shrink: 0 !important;
}

.cd-it-search-subcard input {
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

.cd-it-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-it-search-clear {
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

.cd-it-search-clear:hover {
    color: #ef4444 !important;
}

.cd-it-add-btn {
    height: 36px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 0 14px !important;
    white-space: nowrap !important;
}

/* ==========================================================================
   2. Tier 2: Sub-Tabs Filter Card with White Background
   ========================================================================== */
.cd-it-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    max-width: 100% !important;
}

.cd-it-tab-btn {
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

.cd-it-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Active Card-Based Tab: Single Highlight Rule */
.cd-it-tab-btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

/* Dynamic Count Badges Inside Sub-Tabs */
.cd-it-tab-badge {
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

.cd-it-tab-btn.active .cd-it-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.cd-it-tab-btn:not(.active) .cd-it-tab-badge.badge-primary {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}
.cd-it-tab-btn:not(.active) .cd-it-tab-badge.badge-warning {
    background: #fef3c7 !important;
    color: #b45309 !important;
}
.cd-it-tab-btn:not(.active) .cd-it-tab-badge.badge-success {
    background: #dcfce7 !important;
    color: #15803d !important;
}

/* ==========================================================================
   3. Tier 3: Master Primary Candidate Interview Card
   ========================================================================== */
.cd-it-card {
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

.cd-it-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px) !important;
}

/* ==========================================================================
   4. Upper Sub-Cards Grid (6 Discrete Enterprise Sub-Cards)
   Strictly Zero Horizontal Scrollbar via fractional units & minmax(0, ...)
   ========================================================================== */
.cd-it-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2fr) minmax(0, 2.1fr) minmax(0, 1.8fr) minmax(0, 1.7fr) minmax(0, 1.5fr) minmax(0, 1.5fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    max-width: 100% !important;
    align-items: stretch !important;
}

/* Individual Sub-Card Frame */
.cd-it-subcard {
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

/* Scope & Status Badges */
.cd-badge-scope {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    line-height: 1.3 !important;
    display: inline-flex !important;
    align-items: center !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}
.scope-direct,
.it-job-type-direct {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}
.scope-internal,
.it-job-type-internal {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}

.cd-status-hiring {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    background: #dcfce7 !important;
    color: #15803d !important;
    line-height: 1.3 !important;
    flex-shrink: 0 !important;
}

.cd-followup-pill {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
}
.cd-followup-pill.confirmed { background: #f0fdf4 !important; color: #16a34a !important; border: 1px solid #bbf7d0 !important; }
.cd-followup-pill.info, .cd-followup-pill.rescheduled { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.cd-followup-pill.danger, .cd-followup-pill.no-response { background: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; }

/* Sub-Card 1: Candidate Info */
.cd-avatar-box {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 13px !important;
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

.cd-it-cand-name {
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
.cd-it-cand-name:hover {
    color: #009ef7 !important;
}

.cd-it-cand-role {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
    margin-top: 1px !important;
}

.cd-it-cand-company {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #475569 !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 2: Target Requisition */
.cd-it-job-title {
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
.cd-it-job-title:hover {
    color: #009ef7 !important;
}

.cd-it-job-meta {
    font-size: 10.5px !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    color: #64748b !important;
}

.cd-it-job-id {
    font-weight: 600 !important;
    color: #0284c7 !important;
}

.cd-it-job-emp {
    font-size: 10px !important;
    color: #64748b !important;
    font-weight: 500 !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-dot-sep {
    color: #cbd5e1 !important;
    font-size: 10px !important;
    margin: 0 2px !important;
}

/* Sub-Card 3: Schedule & Interview Link */
.cd-it-dt-date {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-it-dt-time-mode {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #475569 !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-it-dt-time-val {
    font-weight: 600 !important;
    color: #0f172a !important;
}

.cd-it-dt-mode-val {
    color: #64748b !important;
}

.cd-it-link-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #009ef7 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2.5px 7px !important;
    border-radius: 5px !important;
    text-decoration: none !important;
    width: fit-content !important;
    margin-top: 3px !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}
.cd-it-link-pill:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
}

/* Sub-Card 4: Recruiter Info */
.cd-it-recruiter-name {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-it-recruiter-comp {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-it-submitted-at {
    font-size: 9.5px !important;
    font-weight: 500 !important;
    color: #94a3b8 !important;
    margin-top: 3px !important;
    display: flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
}

/* Sub-Card 5: Reusable Dynamic Panelists Card */
.cd-it-panelist-card {
    position: relative !important;
    overflow: visible !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
}

/* Single Panelist (Rule 1: Avatar on top, Name below, Company below) */
.cd-it-panelist-single {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 3px !important;
    width: 100% !important;
    min-width: 0 !important;
}

.cd-panel-avatar-single {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1.5px solid #ffffff !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
}

.cd-panel-avatar-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 50% !important;
}

.cd-it-panelist-meta {
    width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-it-panelist-name {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-it-panelist-company {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
    margin-top: 1px !important;
}

/* Multiple Panelists Overlapping Avatars (Rule 2: Reference Design) */
.cd-it-panelist-multiple {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    width: 100% !important;
    min-width: 0 !important;
    cursor: pointer !important;
    position: relative !important;
}

.cd-panel-avatar-stack {
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
}

.cd-panel-avatar {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.1) !important;
    position: relative !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
    margin-left: -8px !important;
    transition: transform 0.12s ease !important;
}

.cd-panel-avatar:first-child {
    margin-left: 0 !important;
    z-index: 1 !important;
}
.cd-panel-avatar:nth-child(2) { z-index: 2 !important; }
.cd-panel-avatar:nth-child(3) { z-index: 3 !important; }
.cd-panel-avatar:nth-child(4) { z-index: 4 !important; }

.cd-panel-avatar:hover {
    transform: translateY(-2px) scale(1.08) !important;
    z-index: 10 !important;
}

/* Red Circular +N Indicator matching user reference image */
.cd-panel-avatar-extra {
    background: #dc2626 !important;
    color: #ffffff !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.3) !important;
    z-index: 5 !important;
}

.cd-it-panelist-more-text {
    color: #0284c7 !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
}

/* Empty / No Panelist State (Rule 4) */
.cd-it-panelist-empty {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: flex-start !important;
    gap: 2px !important;
    min-height: 48px !important;
    color: #94a3b8 !important;
}

.cd-it-empty-dash {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #94a3b8 !important;
    line-height: 1 !important;
}

.cd-it-empty-sub {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
}

.cd-it-empty-pill {
    background: #f1f5f9 !important;
    color: #94a3b8 !important;
    border-color: #e2e8f0 !important;
}

.cd-it-panel-count-pill {
    font-size: 8.5px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
}

/* Complete Popover on Hover / Click (Rule 2) */
.cd-it-popover-card {
    display: none !important;
    position: absolute !important;
    bottom: calc(100% + 8px) !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background: #0f172a !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.45), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;
    z-index: 99999 !important;
    min-width: 210px !important;
    max-width: 280px !important;
    pointer-events: none !important;
}

.cd-it-popover-card::after {
    content: '' !important;
    position: absolute !important;
    top: 100% !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    border-width: 6px !important;
    border-style: solid !important;
    border-color: #0f172a transparent transparent transparent !important;
}

.cd-it-panelist-card:hover .cd-it-popover-card,
.cd-it-panelist-multiple:hover .cd-it-popover-card,
.cd-it-panelist-single:hover .cd-it-popover-card {
    display: block !important;
    animation: cdPopoverFadeIn 0.15s ease-out forwards !important;
}

@keyframes cdPopoverFadeIn {
    from { opacity: 0; transform: translate(-50%, 4px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

.cd-it-popover-header {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
    padding-bottom: 5px !important;
    margin-bottom: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.cd-it-popover-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 4px 0 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
}
.cd-it-popover-row:last-child {
    border-bottom: none !important;
}

.cd-popover-avatar {
    width: 22px !important;
    height: 22px !important;
    min-width: 22px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    background: #1e293b !important;
    color: #38bdf8 !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
}

.cd-popover-avatar img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.cd-popover-info {
    min-width: 0 !important;
    flex-grow: 1 !important;
}

.cd-popover-name {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #f8fafc !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-popover-company {
    font-size: 9.5px !important;
    color: #94a3b8 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

/* Sub-Card 6: Status & Feedback */
.cd-it-att-pill {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    line-height: 1.25 !important;
}
.cit-fb-scheduled   { background: #e8fff3 !important; color: #16a34a !important; border: 1px solid #bbf7d0 !important; }
.cit-fb-attended    { background: #f1faff !important; color: #009ef7 !important; border: 1px solid #c4e9fd !important; }
.cit-fb-noshow      { background: #fff5f8 !important; color: #f1416c !important; border: 1px solid #fecdd6 !important; }
.cit-fb-rescheduled { background: #fff8dd !important; color: #d97706 !important; border: 1px solid #ffe89e !important; }
.cit-fb-cancelled   { background: #f5f8fa !important; color: #7e8299 !important; border: 1px solid #e4e6ef !important; }
.cit-fb-default     { background: #f5f8fa !important; color: #5e6278 !important; border: 1px solid #e4e6ef !important; }

.cd-it-rec-pill {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
}

.cd-it-rating {
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #f59e0b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
}

/* ==========================================================================
   5. Sub-Card 6: Bottom Activity Strip (Stage & Substage)
   ========================================================================== */
.cd-it-card-bottom {
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

.cd-it-bottom-left {
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

.cd-it-stage-lbl {
    color: #64748b !important;
    font-weight: 600 !important;
    font-size: 9.5px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

/* Semantic Candidate Pipeline Stage Badges */
.cd-it-stage-badge {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 7px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    line-height: 1.3 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
}
.cd-stage-in-review { background: #fef9c3 !important; color: #854d0e !important; border: 1px solid #fde047 !important; }
.cd-stage-sourcing  { background: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }
.cd-stage-screening { background: #f3e8ff !important; color: #6b21a8 !important; border: 1px solid #d8b4fe !important; }
.cd-stage-interview { background: #eff6ff !important; color: #1e40af !important; border: 1px solid #bfdbfe !important; }
.cd-stage-offer     { background: #ffedd5 !important; color: #9a3412 !important; border: 1px solid #fed7aa !important; }
.cd-stage-joined    { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
.cd-stage-rejected  { background: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca !important; }

.cd-it-substage-badge {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 1.5px 7px !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
}

.cd-it-source-subtext {
    font-size: 10px !important;
    color: #64748b !important;
}
.cd-it-source-subtext strong {
    color: #0f172a !important;
}

.cd-it-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

/* ==========================================================================
   6. Tier 4: Enterprise Pagination Card
   ========================================================================== */
.cd-it-pagination-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    margin-top: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}

.cd-it-pagi-info {
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
}
.cd-it-pagi-info strong {
    color: #0f172a !important;
    font-weight: 700 !important;
}

.cd-it-pagi-controls {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.cd-it-pagi-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 32px !important;
    padding: 0 12px !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #334155 !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
}
.cd-it-pagi-btn:hover:not(.disabled) {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
.cd-it-pagi-btn.disabled {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
}

.cd-it-pagi-indicator {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    padding: 0 6px !important;
}

.cd-it-pagi-size-select {
    height: 30px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 0 8px !important;
    color: #334155 !important;
    background: #ffffff !important;
    outline: none !important;
    cursor: pointer !important;
}
.cd-it-pagi-size-select:focus {
    border-color: #009ef7 !important;
}

/* ==========================================================================
   7. Loading, Error & Empty State Cards
   ========================================================================== */
.cd-it-empty,
.cd-it-loading,
.cd-it-error {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 36px 20px !important;
    text-align: center !important;
    color: #64748b !important;
    font-size: 12.5px !important;
    margin: 8px 0 !important;
}

.cd-it-error {
    border-color: #fca5a5 !important;
    color: #dc2626 !important;
}

/* ==========================================================================
   8. Responsive Scaling for 15.6" Screens (1366x768 & 1920x1080) & Smaller
   ========================================================================== */
@media (max-width: 1440px) {
    .cd-it-card-grid {
        grid-template-columns: minmax(0, 1.9fr) minmax(0, 2fr) minmax(0, 1.7fr) minmax(0, 1.6fr) minmax(0, 1.45fr) minmax(0, 1.45fr) !important;
        gap: 6px !important;
        padding: 8px 10px !important;
    }
}

@media (max-width: 1200px) {
    .cd-it-card-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
}

@media (max-width: 768px) {
    .cd-it-card-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
}

@media (max-width: 480px) {
    .cd-it-card-grid {
        grid-template-columns: minmax(0, 1fr) !important;
    }
}
</style>
<script src="{{ asset('assets/js/components/jero-panelist-component.js') }}"></script>
