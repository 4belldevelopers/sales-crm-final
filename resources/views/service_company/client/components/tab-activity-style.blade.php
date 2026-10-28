<style>
/* ==========================================================================
   Client Drawer Activity Tab — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-activity-wrapper, .cd-activity-wrapper *,
.cd-activity-header-card, .cd-activity-header-card *,
.cd-activity-tabs-card, .cd-activity-tabs-card *,
.cd-activity-stream, .cd-activity-stream *,
.cd-activity-card, .cd-activity-card * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.cd-activity-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

.cd-activity-stream {
    width: 100% !important;
    max-width: 100% !important;
    overflow: visible !important;
}

/* ==========================================================================
   1. Tier 1: Top Header Toolbar Card
   ========================================================================== */
.cd-activity-header-card {
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
}

.cd-activity-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-activity-header-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: #eff6ff !important;
    color: #009ef7 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    flex-shrink: 0 !important;
}

.cd-activity-header-title {
    font-size: 14.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
}

.cd-activity-header-sub {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

/* Health Status Pulse Chip */
.cd-activity-health-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    text-transform: capitalize !important;
    line-height: 1.4 !important;
}

.cd-activity-health-pill.health-active {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}

.cd-activity-health-pill.health-inactive {
    background: #fffbeb !important;
    color: #d97706 !important;
    border: 1px solid #fde68a !important;
}

.cd-activity-health-pill.health-stale {
    background: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca !important;
}

.cd-activity-pulse-dot {
    width: 7px !important;
    height: 7px !important;
    border-radius: 50% !important;
    display: inline-block !important;
}

.health-active .cd-activity-pulse-dot {
    background: #10b981 !important;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7) !important;
    animation: cdPulseGreen 2s infinite !important;
}

.health-inactive .cd-activity-pulse-dot {
    background: #f59e0b !important;
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7) !important;
    animation: cdPulseAmber 2s infinite !important;
}

.health-stale .cd-activity-pulse-dot {
    background: #ef4444 !important;
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7) !important;
    animation: cdPulseRed 2s infinite !important;
}

@keyframes cdPulseGreen {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

@keyframes cdPulseAmber {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(245, 158, 11, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

@keyframes cdPulseRed {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(239, 68, 68, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.cd-activity-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-activity-search-subcard {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    height: 34px !important;
    width: 250px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-activity-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-activity-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 12px !important;
    flex-shrink: 0 !important;
}

.cd-activity-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    line-height: 34px !important;
    font-family: inherit !important;
}

.cd-activity-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-activity-search-clear {
    background: none !important;
    border: none !important;
    color: #94a3b8 !important;
    font-size: 11px !important;
    padding: 0 !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    visibility: hidden;
}

.cd-activity-search-clear.visible {
    visibility: visible !important;
}

.cd-activity-search-clear:hover {
    color: #475569 !important;
}

.cd-activity-btn-refresh {
    height: 34px !important;
    width: 34px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-activity-btn-refresh:hover {
    background: #f8fafc !important;
    color: #009ef7 !important;
    border-color: #cbd5e1 !important;
}

.cd-activity-btn-refresh.spinning i {
    animation: cdSpin 0.75s linear infinite !important;
}

@keyframes cdSpin {
    100% { transform: rotate(360deg); }
}

/* ==========================================================================
   2. Tier 2: Filter Sub-Tabs Card
   ========================================================================== */
.cd-activity-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 6px 10px !important;
    margin-bottom: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 6px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    overflow-x: auto !important;
    white-space: nowrap !important;
    scrollbar-width: thin !important;
    -webkit-overflow-scrolling: touch !important;
}

.cd-activity-tabs-card::-webkit-scrollbar {
    height: 4px !important;
}
.cd-activity-tabs-card::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 4px !important;
}

.cd-activity-tab-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    padding: 6px 12px !important;
    border-radius: 7px !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
    flex-shrink: 0 !important;
}

.cd-activity-tab-btn:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
}

.cd-activity-tab-btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

.cd-activity-tab-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1px 7px !important;
    border-radius: 10px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    min-width: 20px !important;
    line-height: 1.4 !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-activity-tab-btn.active .cd-activity-tab-badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* ==========================================================================
   3. Tier 3: Activity Stream & Continuous Connecting Timeline
   ========================================================================== */
.cd-activity-stream {
    position: relative !important;
    padding-left: 20px !important;
    margin-top: 4px !important;
}

/* Continuous Connecting Line */
.cd-activity-stream::before {
    content: '' !important;
    position: absolute !important;
    top: 14px !important;
    bottom: 14px !important;
    left: 8px !important;
    width: 2px !important;
    border-left: 2px dashed #cbd5e1 !important;
    z-index: 1 !important;
}

/* Day Group Divider */
.cd-activity-day-group {
    position: relative !important;
    margin-bottom: 16px !important;
}

.cd-activity-day-header {
    position: relative !important;
    z-index: 2 !important;
    margin-bottom: 10px !important;
    margin-left: -12px !important;
}

.cd-activity-date-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    padding: 3px 10px !important;
    border-radius: 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}

/* ==========================================================================
   4. Activity Card — 3-Tier Enterprise Card
   ========================================================================== */
.cd-activity-card {
    position: relative !important;
    z-index: 2 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.15s ease-in-out !important;
    overflow: visible !important;
}

.cd-activity-card.cd-menu-open {
    z-index: 1050 !important;
}

.cd-activity-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 3px 8px rgba(15, 23, 42, 0.07) !important;
}

/* Left Timeline Dot Node */
.cd-activity-node-dot {
    position: absolute !important;
    left: -20px !important;
    top: 18px !important;
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    background: #ffffff !important;
    border: 2px solid #009ef7 !important;
    z-index: 3 !important;
    box-shadow: 0 0 0 2px #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cd-activity-node-dot::after {
    content: '' !important;
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #009ef7 !important;
}

.cd-activity-node-dot.stream-pipeline,
.cd-activity-node-dot.dot-status-pipeline {
    border-color: #009ef7 !important;
}
.cd-activity-node-dot.stream-pipeline::after,
.cd-activity-node-dot.dot-status-pipeline::after {
    background: #009ef7 !important;
}

.cd-activity-node-dot.stream-interviews,
.cd-activity-node-dot.dot-status-interview {
    border-color: #7239ea !important;
}
.cd-activity-node-dot.stream-interviews::after,
.cd-activity-node-dot.dot-status-interview::after {
    background: #7239ea !important;
}

/* 🟢 Active Requisition / Offered / Joined → Green */
.cd-activity-node-dot.stream-offers,
.cd-activity-node-dot.dot-status-active {
    border-color: #10b981 !important;
}
.cd-activity-node-dot.stream-offers::after,
.cd-activity-node-dot.dot-status-active::after {
    background: #10b981 !important;
}

.cd-activity-node-dot.stream-jobs {
    border-color: #10b981 !important;
}
.cd-activity-node-dot.stream-jobs::after {
    background: #10b981 !important;
}

/* 🟠 Hold → Orange */
.cd-activity-node-dot.dot-status-hold {
    border-color: #f97316 !important;
}
.cd-activity-node-dot.dot-status-hold::after {
    background: #f97316 !important;
}

/* 🔴 Closed / Removed → Red */
.cd-activity-node-dot.dot-status-closed,
.cd-activity-node-dot.dot-status-removed {
    border-color: #ef4444 !important;
}
.cd-activity-node-dot.dot-status-closed::after,
.cd-activity-node-dot.dot-status-removed::after {
    background: #ef4444 !important;
}

.cd-activity-node-dot.stream-crm,
.cd-activity-node-dot.dot-status-crm {
    border-color: #0ea5e9 !important;
}
.cd-activity-node-dot.stream-crm::after,
.cd-activity-node-dot.dot-status-crm::after {
    background: #0ea5e9 !important;
}

.cd-activity-card-body {
    padding: 12px 16px !important;
}

/* Upper Sub-Cards Grid */
.cd-activity-subcard-grid {
    display: grid !important;
    grid-template-columns: minmax(200px, 1.2fr) minmax(220px, 1.6fr) minmax(180px, 1.2fr) minmax(150px, auto) !important;
    gap: 12px !important;
    align-items: center !important;
}

/* Discrete Sub-Card 1: Event & Category Sub-Card */
.cd-activity-subcard-entity {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
}

.cd-activity-entity-avatar {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
}

.cd-activity-entity-avatar img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.cd-activity-entity-meta {
    min-width: 0 !important;
}

.cd-activity-entity-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-activity-entity-sub {
    font-size: 11px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 1px !important;
}

/* Discrete Sub-Card 2: Context / Transition / Content Sub-Card */
.cd-activity-subcard-detail {
    min-width: 0 !important;
}

.cd-activity-stage-transition {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
}

.cd-activity-stage-badge {
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    white-space: nowrap !important;
}

/* ==========================================================================
   Standardized Stage & Status Badges
   🟢 Active Requisition / Active / Offered / Joined → Green
   🟠 Hold → Orange
   🔴 Closed → Red
   🔴 Removed → Red
   ========================================================================== */
.cd-status-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    transition: all 0.15s ease-in-out !important;
}

/* 🟢 Active Requisition / Active / Offered / Joined → Green */
.cd-status-badge.status-active,
.cd-status-badge.status-hiring,
.cd-status-badge.status-open {
    background: #ecfdf5 !important;
    color: #15803d !important;
    border: 1px solid #a7f3d0 !important;
}
.cd-status-badge.status-active i,
.cd-status-badge.status-hiring i,
.cd-status-badge.status-open i {
    color: #10b981 !important;
}

/* 🟠 Hold → Orange */
.cd-status-badge.status-hold,
.cd-status-badge.status-paused {
    background: #fff7ed !important;
    color: #c2410c !important;
    border: 1px solid #fed7aa !important;
}
.cd-status-badge.status-hold i,
.cd-status-badge.status-paused i {
    color: #f97316 !important;
}

/* 🔴 Closed → Red */
.cd-status-badge.status-closed {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
}
.cd-status-badge.status-closed i {
    color: #ef4444 !important;
}

/* 🔴 Removed → Red */
.cd-status-badge.status-removed,
.cd-status-badge.status-rejected,
.cd-status-badge.status-cancelled {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
}
.cd-status-badge.status-removed i,
.cd-status-badge.status-rejected i,
.cd-status-badge.status-cancelled i {
    color: #ef4444 !important;
}

/* Pipeline standard badges (Screening, Sourcing, InReview, etc.) */
.cd-status-badge.status-pipeline {
    background: #eff6ff !important;
    color: #009ef7 !important;
    border: 1px solid #bfdbfe !important;
}
.cd-status-badge.status-pipeline i {
    color: #009ef7 !important;
}

/* Interview standard badges */
.cd-status-badge.status-interview {
    background: #f5f3ff !important;
    color: #7c3aed !important;
    border: 1px solid #ddd6fe !important;
}
.cd-status-badge.status-interview i {
    color: #7c3aed !important;
}

.cd-activity-stage-from {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
}

.cd-activity-stage-arrow {
    color: #94a3b8 !important;
    font-size: 11px !important;
}

.cd-activity-stage-to {
    background: #eff6ff !important;
    color: #009ef7 !important;
    border: 1px solid #bfdbfe !important;
}

.cd-activity-detail-note {
    font-size: 11.5px !important;
    color: #334155 !important;
    line-height: 1.4 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

/* Discrete Sub-Card 3: Actor & Authority Sub-Card */
.cd-activity-subcard-actor {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}

.cd-activity-actor-avatar {
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    flex-shrink: 0 !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
}

.cd-activity-actor-avatar img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.cd-activity-actor-meta {
    min-width: 0 !important;
}

.cd-activity-actor-attribution {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-bottom: 1px !important;
}

.cd-activity-actor-name {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.cd-activity-actor-role {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Discrete Sub-Card 4: Actions & Overflow Kebab Menu */
.cd-activity-subcard-actions {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 6px !important;
    margin-left: auto !important;
}

.cd-activity-btn-action {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 5px 11px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    background: #f8fafc !important;
    color: #009ef7 !important;
    border: 1px solid #e2e8f0 !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
    white-space: nowrap !important;
    text-decoration: none !important;
}

.cd-activity-btn-action:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
}

.cd-activity-kebab-wrapper {
    position: relative !important;
}

.cd-activity-btn-kebab {
    width: 28px !important;
    height: 28px !important;
    border-radius: 6px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 13px !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-activity-btn-kebab:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}

.cd-activity-kebab-menu {
    display: none;
    position: absolute !important;
    right: 0 !important;
    top: calc(100% + 4px) !important;
    margin-top: 0 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
    min-width: 175px !important;
    z-index: 1060 !important;
    padding: 5px 0 !important;
}

.cd-activity-kebab-menu.dropup {
    top: auto !important;
    bottom: calc(100% + 4px) !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

.cd-activity-kebab-menu.show {
    display: block !important;
}

.cd-activity-menu-item {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 7px 12px !important;
    font-size: 11.5px !important;
    color: #334155 !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
    text-decoration: none !important;
}

.cd-activity-menu-item:hover {
    background: #f8fafc !important;
    color: #009ef7 !important;
}

.cd-activity-menu-item i {
    font-size: 11px !important;
    color: #94a3b8 !important;
    width: 14px !important;
    text-align: center !important;
}

/* Activity Card Footer Strip */
.cd-activity-footer-strip {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    padding: 6px 16px !important;
    background: #f8fafc !important;
    border-top: 1px solid #f1f5f9 !important;
    border-bottom-left-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    font-size: 10.5px !important;
    color: #64748b !important;
    flex-wrap: wrap !important;
}

.cd-activity-footer-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.cd-activity-stream-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 1px 7px !important;
    border-radius: 4px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

.stream-pipeline-pill { background: #eff6ff !important; color: #0284c7 !important; }
.stream-interviews-pill { background: #f5f3ff !important; color: #7c3aed !important; }
.stream-offers-pill { background: #ecfdf5 !important; color: #059669 !important; }
.stream-jobs-pill { background: #fffbeb !important; color: #d97706 !important; }
.stream-crm-pill { background: #f0fdf4 !important; color: #15803d !important; }

.cd-activity-seq-tag {
    font-family: monospace !important;
    font-size: 10px !important;
    color: #94a3b8 !important;
}

/* ==========================================================================
   5. Skeleton Loading State
   ========================================================================== */
.cd-activity-skeleton {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 14px 16px !important;
    margin-bottom: 12px !important;
}

.cd-skeleton-line {
    height: 12px !important;
    background: #f1f5f9 !important;
    border-radius: 4px !important;
    margin-bottom: 8px !important;
    position: relative !important;
    overflow: hidden !important;
}

.cd-skeleton-line::after {
    content: '' !important;
    position: absolute !important;
    top: 0; right: 0; bottom: 0; left: 0;
    transform: translateX(-100%) !important;
    background-image: linear-gradient(90deg, rgba(255,255,255, 0) 0, rgba(255,255,255, 0.5) 20%, rgba(255,255,255, 0.7) 60%, rgba(255,255,255, 0)) !important;
    animation: cdShimmer 1.5s infinite !important;
}

@keyframes cdShimmer {
    100% { transform: translateX(100%); }
}

/* ==========================================================================
   6. Empty State
   ========================================================================== */
.cd-activity-empty-state {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 36px 20px !important;
    text-align: center !important;
    margin: 20px 0 !important;
}

.cd-activity-empty-icon {
    width: 48px !important;
    height: 48px !important;
    border-radius: 50% !important;
    background: #f8fafc !important;
    color: #94a3b8 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 20px !important;
    margin-bottom: 10px !important;
}

.cd-activity-empty-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 4px !important;
}

.cd-activity-empty-text {
    font-size: 12px !important;
    color: #64748b !important;
    max-width: 320px !important;
    margin: 0 auto !important;
}

/* ==========================================================================
   7. 15.6" Screen Responsiveness (1366x768 & 1920x1080)
   ========================================================================== */
@media (max-width: 1280px) {
    .cd-activity-subcard-grid {
        grid-template-columns: minmax(180px, 1fr) minmax(180px, 1.2fr) minmax(140px, 1fr) auto !important;
        gap: 8px !important;
    }
}

@media (max-width: 991px) {
    .cd-activity-subcard-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 10px !important;
    }
    .cd-activity-subcard-actions {
        grid-column: span 2 !important;
        justify-content: flex-start !important;
    }
}

@media (max-width: 640px) {
    .cd-activity-subcard-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-activity-header-card {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .cd-activity-search-subcard {
        width: 100% !important;
    }
}
/* ==========================================================================
   Read-Only Drawer Mode (Strict suppression of Edit and mutable controls)
   ========================================================================== */
#kt_activitiesApplication.drawer-readonly-view .cdr-btn-primary,
#kt_activitiesApplication.drawer-readonly-view a[href*="job-applications/edit"],
#kt_activitiesApplication.drawer-readonly-view a[href*="/edit"],
#kt_activitiesApplication.drawer-readonly-view .jw-card-edit-btn,
#kt_activitiesApplication.drawer-readonly-view .edit-note,
#kt_activitiesApplication.drawer-readonly-view .delete-note,
#kt_activitiesApplication.drawer-readonly-view .po-btn-edit-note,
#kt_activitiesApplication.drawer-readonly-view #hdr_move_stage_btn,
#kt_activitiesApplication.drawer-readonly-view [data-action="edit"],
#kt_activitiesApplication.drawer-readonly-view .btn-edit,
#kt_activitiesJob_show.drawer-readonly-view .job-action-btn.edit,
#kt_activitiesJob_show.drawer-readonly-view a[href*="jobs/edit"],
#kt_activitiesJob_show.drawer-readonly-view a[href*="/edit"],
#kt_activitiesJob_show.drawer-readonly-view .info-card-edit,
#kt_activitiesJob_show.drawer-readonly-view .btn-edit {
    display: none !important;
    pointer-events: none !important;
}

#kt_activitiesJob_show {
    z-index: 999999 !important;
}
</style>
