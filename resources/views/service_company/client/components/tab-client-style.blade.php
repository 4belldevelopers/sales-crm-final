<style>
/* ==========================================================================
   Service Company Clients Listing — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-client-wrapper,
.cd-client-header-card,
.cd-client-tabs-card,
.cd-client-stream,
.cd-client-card {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

.cd-client-wrapper *,
.cd-client-header-card *:not(i):not([class*="fa-"]):not([class*="bi-"]):not([class*="ki-"]):not(.svg-icon),
.cd-client-tabs-card *:not(i):not([class*="fa-"]):not([class*="bi-"]):not([class*="ki-"]):not(.svg-icon),
.cd-client-stream *:not(i):not([class*="fa-"]):not([class*="bi-"]):not([class*="ki-"]):not(.svg-icon),
.cd-client-card *:not(i):not([class*="fa-"]):not([class*="bi-"]):not([class*="ki-"]):not(.svg-icon) {
    box-sizing: border-box !important;
}

/* ── Explicit FontAwesome Glyphs Preservation (Strict Protection Against Poppins Override) ── */
.cd-client-wrapper i[class*="fa-"],
.cd-client-header-card i[class*="fa-"],
.cd-client-tabs-card i[class*="fa-"],
.cd-client-stream i[class*="fa-"],
.cd-client-card i[class*="fa-"],
i.fas, i.fa-solid, [class*="fa-solid"], [class*="fas "] {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 900 !important;
    font-style: normal !important;
    display: inline-block !important;
}

.cd-client-wrapper i.far,
.cd-client-header-card i.far,
.cd-client-tabs-card i.far,
.cd-client-stream i.far,
.cd-client-card i.far,
i.far, i.fa-regular, [class*="fa-regular"], [class*="far "] {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 400 !important;
    font-style: normal !important;
    display: inline-block !important;
}

.cd-client-wrapper i.fab,
.cd-client-header-card i.fab,
.cd-client-tabs-card i.fab,
.cd-client-stream i.fab,
.cd-client-card i.fab,
i.fab, i.fa-brands, [class*="fa-brands"], [class*="fab "] {
    font-family: "Font Awesome 6 Brands", "FontAwesome" !important;
    font-style: normal !important;
    display: inline-block !important;
}

.cd-client-wrapper,
.cd-client-stream {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* ==========================================================================
   1. Tier 1: Top Header Toolbar Card
   ========================================================================== */
.cd-client-header-card {
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

.cd-client-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-client-header-icon {
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

.cd-client-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
    margin: 0 !important;
}

.cd-client-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-client-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card */
.cd-client-search-subcard {
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

.cd-client-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-client-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 12.5px !important;
    flex-shrink: 0 !important;
}

.cd-client-search-subcard input {
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

.cd-client-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 12px !important;
}

.cd-client-search-clear {
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

.cd-client-search-clear:hover {
    color: #475569 !important;
}

.cd-client-add-btn {
    height: 36px !important;
    padding: 0 14px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    white-space: nowrap !important;
}

/* ==========================================================================
   2. Tier 2: Sub-Tabs & Filter Card
   ========================================================================== */
.cd-client-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-client-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
}

.cd-client-tab-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
    user-select: none !important;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    line-height: 1.2 !important;
}

.cd-client-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Strict Active Blue Rule */
.cd-client-tab-btn.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

.cd-client-tab-badge {
    min-width: 18px !important;
    height: 17px !important;
    padding: 0 5px !important;
    border-radius: 10px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
}

.cd-client-tab-btn.active .cd-client-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.cd-client-tab-badge.badge-direct { background: #eff6ff !important; color: #2563eb !important; }
.cd-client-tab-badge.badge-internal { background: #ecfdf5 !important; color: #059669 !important; }
.cd-client-tab-badge.badge-hiring { background: #dcfce7 !important; color: #15803d !important; }
.cd-client-tab-badge.badge-inactive { background: #f1f5f9 !important; color: #64748b !important; }
.cd-client-tab-badge.badge-request { background: #fef3c7 !important; color: #d97706 !important; }

/* Right Filter Suite (Location, Industry, Sort) */
.cd-client-filter-controls {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

/* ==========================================================================
   3. Tier 3: Master Primary Client Card Stream & 6 Discrete Sub-Cards
   ========================================================================== */
.cd-client-stream {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    width: 100% !important;
}

.cd-client-card,
.cd-client-card.tbl-r,
.tbl-req .cd-client-card.tbl-r,
div.cd-client-card.tbl-r {
    display: flex !important;
    flex-direction: column !important;
    grid-template-columns: none !important;
    column-gap: 0 !important;
    row-gap: 0 !important;
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 0 10px 0 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    transition: all 0.18s ease-in-out !important;
    overflow: hidden !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    box-sizing: border-box !important;
    align-items: stretch !important;
}

.cd-client-card:hover,
.cd-client-card.tbl-r:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* Pending Request Card Highlight */
.cd-client-card.is-pending-request,
.cd-client-card.tbl-r.is-pending-request {
    border-left: 4px solid #f59e0b !important;
    background: linear-gradient(90deg, #fffbeb 0%, #ffffff 16%) !important;
}

/* Master 5-Column Grid strictly calibrated for 15.6" screen (1366x768 & 1920x1080) */
.cd-client-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.35fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 2.15fr) !important;
    gap: 8px !important;
    padding: 10px 14px !important;
    width: 100% !important;
    max-width: 100% !important;
    align-items: stretch !important;
    box-sizing: border-box !important;
    min-width: 0 !important;
}

@media (max-width: 1200px) {
    .cd-client-card-grid {
        grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.2fr) minmax(0, 1.2fr) minmax(0, 1.1fr) minmax(0, 2fr) !important;
    }
}

@media (max-width: 992px) {
    .cd-client-card-grid {
        grid-template-columns: 1fr 1fr !important;
    }
}

@media (max-width: 576px) {
    .cd-client-card-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Individual Sub-Card Base Styling */
.cd-client-subcard {
    background: #f8fafc !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 8px !important;
    padding: 8px 10px !important;
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
    gap: 6px !important;
    margin-bottom: 6px !important;
}

.cd-subcard-label {
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    color: #94a3b8 !important;
    line-height: 1 !important;
    white-space: nowrap !important;
}

.cd-header-badges {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

/* Badges */
.cd-badge-client-type {
    display: inline-flex !important;
    align-items: center !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
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
    display: inline-flex !important;
    align-items: center !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.cd-status-badge.status-hiring {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
}

.cd-status-badge.status-inactive {
    background: #f1f5f9 !important;
    color: #64748b !important;
    border: 1px solid #e2e8f0 !important;
}

.cd-status-badge.status-request {
    background: #fef3c7 !important;
    color: #d97706 !important;
    border: 1px solid #fde68a !important;
}

/* ── Sub-Card 1: Client Profile ── */
.cd-details-body {
    display: flex !important;
    align-items: flex-start !important;
    gap: 9px !important;
    min-width: 0 !important;
}

.cd-logo-box {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
}

.cd-logo-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.cd-details-text {
    min-width: 0 !important;
    flex: 1 !important;
}

.cd-client-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    text-decoration: none !important;
    line-height: 1.25 !important;
    cursor: pointer !important;
}

.cd-client-title:hover {
    color: #009ef7 !important;
}

.cd-client-meta {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

/* ── Sub-Card 2: Location & HQ ── */
.cd-val-loc {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-loc-badge {
    font-size: 9.5px !important;
    background: #e2e8f0 !important;
    color: #475569 !important;
    border-radius: 4px !important;
    padding: 1px 4px !important;
    font-weight: 600 !important;
}

.cd-val-sub {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* ── Sub-Card 3: Job Requisitions ── */
.cd-val-jobs-main {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #047857 !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    line-height: 1.2 !important;
}

.cd-jobs-breakdown {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 11px !important;
    margin-top: 4px !important;
    color: #64748b !important;
}

.cd-jobs-pill-count {
    font-weight: 700 !important;
}

.cd-jobs-pill-count.hiring { color: #16a34a !important; }
.cd-jobs-pill-count.hold { color: #d97706 !important; }
.cd-jobs-pill-count.closed { color: #64748b !important; }

/* ── Sub-Card 4: Team & Engagement ── */
.cd-team-lead {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-team-meta {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* ── Sub-Card 5: Talent Pipeline Funnel ── */
.cd-pipeline-track {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 3px !important;
    width: 100% !important;
    margin-bottom: 6px !important;
}

.cd-track-stage {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.cd-track-badge {
    min-width: 20px !important;
    height: 18px !important;
    border-radius: 4px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 4px !important;
    line-height: 1 !important;
    width: 100% !important;
    max-width: 32px !important;
}

/* Canonical 6-stage semantic coloring */
.cd-track-badge.s-sou { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1 !important; }
.cd-track-badge.s-scr { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.cd-track-badge.s-eva { background: #f3e8ff !important; color: #7e22ce !important; border: 1px solid #d8b4fe !important; }
.cd-track-badge.s-neg { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.cd-track-badge.s-off { background: #ccfbf1 !important; color: #0f766e !important; border: 1px solid #99f6e4 !important; }
.cd-track-badge.s-joi { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #86efac !important; }

.cd-track-label {
    font-size: 8.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    margin-top: 2px !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1 !important;
}

/* Row 2: Separated Aggregate Summary Metric Pills */
.cd-pipeline-summary-bar {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    width: 100% !important;
}

.cd-pipe-metric-pill {
    flex: 1 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    padding: 3px 6px !important;
    font-size: 10.5px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    line-height: 1.2 !important;
    color: #475569 !important;
}

.cd-pipe-metric-pill.pill-apps {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
}

.cd-pipe-metric-pill.pill-int {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #1e40af !important;
}

.cd-pipe-metric-pill strong {
    font-weight: 700 !important;
    color: #0f172a !important;
}

/* ── Sub-Card 6: Bottom Activity Strip ── */
.cd-client-card-bottom {
    background: #fafbfc !important;
    border-top: 1px solid #f1f5f9 !important;
    padding: 8px 14px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    font-size: 11px !important;
    color: #64748b !important;
    min-height: 38px !important;
}

.cd-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
}

.cd-bottom-left strong {
    color: #334155 !important;
}

.cd-bottom-actions {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.cd-btn-quick-drawer {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    padding: 3px 9px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-btn-quick-drawer:hover {
    background: #f1f5f9 !important;
    color: #1d4ed8 !important;
}

.cd-btn-quick-post {
    background: #009ef7 !important;
    border: 1px solid #009ef7 !important;
    border-radius: 6px !important;
    padding: 3px 9px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    cursor: pointer !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-btn-quick-post:hover {
    background: #0088d6 !important;
    color: #ffffff !important;
}

/* Empty State */
.cd-client-empty {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 48px 20px !important;
    text-align: center !important;
    margin: 12px 0 !important;
    width: 100% !important;
}

.cd-client-empty i {
    color: #94a3b8 !important;
    font-size: 32px !important;
    margin-bottom: 12px !important;
}
</style>
