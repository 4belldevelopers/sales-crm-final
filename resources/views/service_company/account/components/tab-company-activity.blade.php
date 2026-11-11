<style>
/* ==========================================================================
   Company Activity & Audit Timeline — Master Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   Complies with Rule 20: Modern Card + Sub-Card Enterprise Design System
   ========================================================================== */

.coa-wrapper {
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* ── Tier 1: Top Header Toolbar Card ── */
.coa-header-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 10px 16px !important;
  margin-bottom: 10px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 12px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  flex-wrap: wrap !important;
  width: 100% !important;
}

.coa-header-left {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  min-width: 0 !important;
}

.coa-header-icon {
  width: 34px !important;
  height: 34px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 14px !important;
  flex-shrink: 0 !important;
}

.coa-header-title {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.2 !important;
  margin: 0 !important;
}

.coa-header-sub {
  font-size: 11px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}

.coa-health-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
  padding: 1px 7px !important;
  border-radius: 999px !important;
  font-size: 10px !important;
  font-weight: 600 !important;
}

.coa-pulse-dot {
  width: 6px !important;
  height: 6px !important;
  border-radius: 50% !important;
  background: #10b981 !important;
  animation: coaPulse 2s infinite !important;
}

@keyframes coaPulse {
  0% { transform: scale(0.95); opacity: 1; }
  50% { transform: scale(1.3); opacity: 0.6; }
  100% { transform: scale(0.95); opacity: 1; }
}

.coa-header-right {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}

.coa-search-subcard {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 10px !important;
  height: 32px !important;
  width: 220px !important;
  transition: all 0.15s ease-in-out !important;
}

.coa-search-subcard:focus-within {
  background: #ffffff !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1) !important;
}

.coa-search-subcard i.fa-search {
  color: #94a3b8 !important;
  font-size: 11px !important;
  margin-right: 6px !important;
}

.coa-search-input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  font-size: 11.5px !important;
  color: #1e293b !important;
  width: 100% !important;
  padding: 0 !important;
  font-family: inherit !important;
}

.coa-search-clear {
  display: none !important;
  background: none !important;
  border: none !important;
  color: #94a3b8 !important;
  font-size: 11px !important;
  cursor: pointer !important;
  padding: 0 !important;
  margin-left: 4px !important;
}

.coa-search-clear.visible {
  display: inline-flex !important;
}

.coa-select {
  height: 32px !important;
  font-size: 11.5px !important;
  border-radius: 8px !important;
  border: 1px solid #e2e8f0 !important;
  font-weight: 500 !important;
  background-color: #ffffff !important;
  padding: 0 10px !important;
  color: #1e293b !important;
}

.coa-btn-refresh {
  width: 32px !important;
  height: 32px !important;
  border-radius: 8px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  color: #475569 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.15s ease-in-out !important;
  font-size: 12px !important;
}

.coa-btn-refresh:hover {
  background: #f8fafc !important;
  color: #2563eb !important;
  border-color: #bfdbfe !important;
}

/* ── Tier 2: Filter Sub-Tabs Card ── */
.coa-tabs-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 6px 10px !important;
  margin-bottom: 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  overflow-x: auto !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
  scrollbar-width: none !important;
}

.coa-tabs-card::-webkit-scrollbar {
  display: none !important;
}

.coa-tab-btn {
  background: transparent !important;
  border: none !important;
  border-radius: 7px !important;
  padding: 6px 12px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 7px !important;
  cursor: pointer !important;
  transition: all 0.15s ease-in-out !important;
  white-space: nowrap !important;
  font-family: inherit !important;
}

.coa-tab-btn:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}

.coa-tab-btn.active {
  background: #009ef7 !important;
  color: #ffffff !important;
}

.coa-tab-badge {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 1px 6px !important;
  border-radius: 999px !important;
  background: #e2e8f0 !important;
  color: #475569 !important;
  line-height: 1.2 !important;
}

.coa-tab-btn.active .coa-tab-badge {
  background: rgba(255, 255, 255, 0.25) !important;
  color: #ffffff !important;
}

/* ── Tier 3: Timeline Stream Container ── */
.coa-stream {
  width: 100% !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
}

.coa-timeline-item {
  display: flex !important;
  gap: 14px !important;
  position: relative !important;
}

.coa-timeline-node {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  width: 36px !important;
  flex-shrink: 0 !important;
}

.coa-actor-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 50% !important;
  background: #e0f2fe !important;
  border: 2px solid #ffffff !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: hidden !important;
  z-index: 2 !important;
}

.coa-actor-avatar img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
}

.coa-actor-initials {
  font-size: 11px !important;
  font-weight: 700 !important;
  color: #0284c7 !important;
  letter-spacing: -0.02em !important;
}

.coa-timeline-track {
  width: 2px !important;
  flex-grow: 1 !important;
  background: #e2e8f0 !important;
  margin-top: 4px !important;
  margin-bottom: -10px !important;
}

.coa-timeline-item:last-child .coa-timeline-track {
  display: none !important;
}

/* Timeline Sub-Card */
.coa-card {
  flex: 1 !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 12px 16px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  transition: all 0.15s ease-in-out !important;
  min-width: 0 !important;
}

.coa-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 3px 8px rgba(15, 23, 42, 0.07) !important;
}

.coa-card-head {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 10px !important;
  margin-bottom: 6px !important;
  flex-wrap: wrap !important;
}

.coa-card-head-left {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}

.coa-badge {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 2px 7px !important;
  border-radius: 6px !important;
  line-height: 1.25 !important;
}

.coa-action-title {
  font-size: 12.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
}

.coa-card-time {
  font-size: 11px !important;
  color: #64748b !important;
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
}

.coa-bullet {
  color: #94a3b8 !important;
}

.coa-card-body {
  margin-bottom: 8px !important;
}

.coa-entity-title {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #1e293b !important;
  margin: 0 0 3px 0 !important;
  line-height: 1.3 !important;
}

.coa-description {
  font-size: 11.5px !important;
  color: #475569 !important;
  margin: 0 !important;
  line-height: 1.4 !important;
}

.coa-card-foot {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 10px !important;
  padding-top: 8px !important;
  border-top: 1px dashed #f1f5f9 !important;
  font-size: 11px !important;
}

.coa-actor-info {
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
  color: #64748b !important;
}

.coa-actor-name {
  font-weight: 600 !important;
  color: #1e293b !important;
}

.coa-link-btn {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #2563eb !important;
  text-decoration: none !important;
  transition: color 0.15s ease-in-out !important;
}

.coa-link-btn:hover {
  color: #1d4ed8 !important;
  text-decoration: underline !important;
}

/* Empty State */
.coa-empty-state {
  background: #ffffff !important;
  border: 1px dashed #cbd5e1 !important;
  border-radius: 10px !important;
  padding: 36px 20px !important;
  text-align: center !important;
  width: 100% !important;
}

.coa-empty-icon {
  width: 44px !important;
  height: 44px !important;
  border-radius: 50% !important;
  background: #f1f5f9 !important;
  color: #94a3b8 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 18px !important;
  margin-bottom: 10px !important;
}

.coa-empty-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #1e293b !important;
  margin-bottom: 4px !important;
}

.coa-empty-sub {
  font-size: 11.5px !important;
  color: #64748b !important;
  max-width: 420px !important;
  margin: 0 auto !important;
  line-height: 1.4 !important;
}

/* Skeleton Loaders */
.coa-skeleton-card {
  background: #ffffff !important;
  border: 1px solid #f1f5f9 !important;
  border-radius: 10px !important;
  padding: 14px 18px !important;
  margin-bottom: 10px !important;
}

.coa-skeleton-line {
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%) !important;
  background-size: 200% 100% !important;
  animation: coaSkeleton 1.5s infinite !important;
  border-radius: 4px !important;
  margin-bottom: 8px !important;
}

@keyframes coaSkeleton {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.coa-load-more-wrap {
  text-align: center !important;
  margin-top: 14px !important;
}

.coa-btn-load-more {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 8px !important;
  padding: 7px 18px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #334155 !important;
  cursor: pointer !important;
  transition: all 0.15s ease-in-out !important;
}

.coa-btn-load-more:hover {
  background: #f8fafc !important;
  border-color: #94a3b8 !important;
  color: #0f172a !important;
}
</style>

<div class="coa-wrapper" id="coaWrapper">
  {{-- ── Tier 1: Top Header Toolbar Card ── --}}
  <div class="coa-header-card">
    <div class="coa-header-left">
      <div class="coa-header-icon">
        <i class="fas fa-clock-rotate-left fa-solid"></i>
      </div>
      <div>
        <h3 class="coa-header-title">Company Activity &amp; Audit Timeline</h3>
        <div class="coa-header-sub">
          <span>Live recruitment lifecycle &amp; team actions across the organization</span>
          <span class="coa-health-pill" id="coaHealthPill">
            <span class="coa-pulse-dot"></span>
            <span id="coaHealthText">Active</span>
          </span>
        </div>
      </div>
    </div>

    <div class="coa-header-right">
      {{-- 300ms Debounced Search Sub-Card --}}
      <div class="coa-search-subcard">
        <i class="fas fa-search"></i>
        <input type="text" class="coa-search-input" id="coaSearchInput" placeholder="Search Company Activity..." autocomplete="off" />
        <button type="button" class="coa-search-clear" id="coaSearchClear" title="Clear Search">
          <i class="fas fa-times"></i>
        </button>
      </div>

      {{-- Timeframe Dropdown --}}
      <select class="form-select form-select-sm coa-select" id="coaTimeframeSelect">
        <option value="all">All Time</option>
        <option value="today">Today</option>
        <option value="7d">Last 7 Days</option>
        <option value="30d">Last 30 Days</option>
      </select>

      {{-- Refresh Button --}}
      <button type="button" class="coa-btn-refresh" id="coaRefreshBtn" title="Refresh Activity Feed">
        <i class="fas fa-sync-alt"></i>
      </button>
    </div>
  </div>

  {{-- ── Tier 2: Filter Sub-Tabs Card ── --}}
  <div class="coa-tabs-card" id="coaFilterTabs">
    <button type="button" class="coa-tab-btn active" data-category="all">
      <span>All Events</span>
      <span class="coa-tab-badge" id="coaCountAll">0</span>
    </button>
    <button type="button" class="coa-tab-btn" data-category="pipeline">
      <span>Pipeline</span>
      <span class="coa-tab-badge" id="coaCountPipeline">0</span>
    </button>
    <button type="button" class="coa-tab-btn" data-category="interviews">
      <span>Interviews</span>
      <span class="coa-tab-badge" id="coaCountInterviews">0</span>
    </button>
    <button type="button" class="coa-tab-btn" data-category="offers">
      <span>Offers &amp; Joining</span>
      <span class="coa-tab-badge" id="coaCountOffers">0</span>
    </button>
    <button type="button" class="coa-tab-btn" data-category="jobs">
      <span>Requisitions</span>
      <span class="coa-tab-badge" id="coaCountJobs">0</span>
    </button>
    <button type="button" class="coa-tab-btn" data-category="team">
      <span>Team &amp; Access</span>
      <span class="coa-tab-badge" id="coaCountTeam">0</span>
    </button>
  </div>

  {{-- ── Tier 3: Primary Activity Stream Container ── --}}
  <div id="coaSkeletonLoader" style="display: none;">
    <div class="coa-skeleton-card">
      <div class="coa-skeleton-line" style="width: 35%; height: 14px;"></div>
      <div class="coa-skeleton-line" style="width: 75%; height: 11px;"></div>
      <div class="coa-skeleton-line" style="width: 50%; height: 11px;"></div>
    </div>
    <div class="coa-skeleton-card">
      <div class="coa-skeleton-line" style="width: 40%; height: 14px;"></div>
      <div class="coa-skeleton-line" style="width: 80%; height: 11px;"></div>
      <div class="coa-skeleton-line" style="width: 45%; height: 11px;"></div>
    </div>
  </div>

  <div class="coa-stream" id="coaCardsStream">
    {{-- Injected via AJAX from company-activity-cards-ajax --}}
  </div>

  <div class="coa-load-more-wrap" id="coaLoadMoreWrap" style="display: none;">
    <button type="button" class="coa-btn-load-more" id="coaLoadMoreBtn">Load More Activities</button>
  </div>
</div>

<script>
/**
 * CompanyActivityManager
 *
 * Dedicated client-side engine managing the Company Profile Activity Timeline and Overview stream.
 * 100% scoped to company users' actions across all departments and requisitions.
 * Zero dependency on client_id.
 */
(function() {
  window.CompanyActivityManager = {
    endpoint: "{{ route('service_company.account.activity_data_ajax') }}",
    activeCategory: 'all',
    activeTimeframe: 'all',
    searchQuery: '',
    currentPage: 1,
    hasMore: false,
    searchTimer: null,
    inFlightXhr: null,
    cachedItems: [],

    init: function() {
      this.loadActivity(false);
    },

    loadActivity: function(append) {
      var self = this;
      if (append) {
        self.currentPage++;
      } else {
        self.currentPage = 1;
        $('#coaSkeletonLoader').show();
        $('#coaCardsStream').hide();
      }

      if (self.inFlightXhr && typeof self.inFlightXhr.abort === 'function') {
        self.inFlightXhr.abort();
      }

      var params = {
        category: self.activeCategory,
        timeframe: self.activeTimeframe,
        search: self.searchQuery,
        page: self.currentPage,
        per_page: 15
      };

      self.inFlightXhr = $.ajax({
        url: self.endpoint,
        type: 'GET',
        data: params,
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
          $('#coaSkeletonLoader').hide();
          $('#coaCardsStream').show();

          if (res && res.success) {
            self.hasMore = res.pagination ? !!res.pagination.has_more : false;

            if (append) {
              if (res.data && Array.isArray(res.data)) {
                self.cachedItems = self.cachedItems.concat(res.data);
              }
              if (res.html) {
                $('#coaCardsStream').append(res.html);
              }
            } else {
              self.cachedItems = (res.data && Array.isArray(res.data)) ? res.data : [];
              $('#coaCardsStream').html(res.html || '');
            }

            // Update KPI tab count badges
            if (res.kpi) {
              self.updateKpis(res.kpi);
            }

            // Update Health Indicator
            if (res.health && res.health.label) {
              $('#coaHealthText').text(res.health.label);
            }

            // Toggle Load More button
            if (self.hasMore) {
              $('#coaLoadMoreWrap').show();
            } else {
              $('#coaLoadMoreWrap').hide();
            }

            // Sync with Overview Tab: Render top 5 activities into #cdOverviewRecentActivityList
            self.renderOverviewTimeline(self.cachedItems);
          }
        },
        error: function(xhr, status) {
          if (status === 'abort') return;
          $('#coaSkeletonLoader').hide();
          $('#coaCardsStream').show();
          if (!append) {
            $('#coaCardsStream').html(
              '<div class="coa-empty-state">' +
              '  <div class="coa-empty-icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>' +
              '  <div class="coa-empty-title">Unable to Load Company Activities</div>' +
              '  <div class="coa-empty-sub">An unexpected error occurred while fetching company events. Please click refresh to try again.</div>' +
              '</div>'
            );
          }
        }
      });
    },

    updateKpis: function(kpi) {
      $('#coaCountAll').text(kpi.all || 0);
      $('#coaCountPipeline').text(kpi.pipeline || 0);
      $('#coaCountInterviews').text(kpi.interviews || 0);
      $('#coaCountOffers').text(kpi.offers || 0);
      $('#coaCountJobs').text(kpi.jobs || 0);
      $('#coaCountTeam').text(kpi.team || 0);
    },

    renderOverviewTimeline: function(items) {
      var $container = $('#cdOverviewRecentActivityList');
      if (!$container.length) return;

      var topItems = (items || []).slice(0, 5);
      if (!topItems.length) {
        $container.html(
          '<div class="p-3 text-center text-muted" style="font-size:11.5px;">No recent company activities found.</div>'
        );
        return;
      }

      var html = '<div class="cd-timeline-stream">';
      topItems.forEach(function(act) {
        var dotColor = act.badge_color || '#3b82f6';
        var badgeBg = act.badge_bg || '#eff6ff';
        var badgeColor = act.badge_color || '#2563eb';
        var linkHtml = act.link ? ' href="' + act.link + '" target="_blank"' : '';

        html += '<div class="cd-tl-card">' +
          '  <div class="cd-tl-card-header">' +
          '    <div class="cd-tl-card-left">' +
          '      <span class="cd-tl-dot" style="background:' + dotColor + ';"></span>' +
          '      <a' + linkHtml + ' class="cd-tl-title text-hover-primary">' + (act.title || 'Company Event') + '</a>' +
          '      <span class="cd-tl-sub">' + (act.description || '') + '</span>' +
          '    </div>' +
          '    <div class="cd-tl-card-right">' +
          '      <span class="badge" style="background:' + badgeBg + '; color:' + badgeColor + '; font-size:10px; font-weight:600; padding:2px 7px; border-radius:6px;">' + (act.badge_text || 'Active') + '</span>' +
          '      <span class="cd-tl-time">' + (act.time_ago || 'Recently') + '</span>' +
          '    </div>' +
          '  </div>' +
          '  <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top border-dashed border-gray-200" style="font-size:10.5px; color:#64748b;">' +
          '    <span>Performed by <strong style="color:#1e293b;">' + (act.actor_name || 'Team Member') + '</strong></span>' +
          '    <span>' + (act.formatted_date || '') + '</span>' +
          '  </div>' +
          '</div>';
      });
      html += '</div>';

      $container.html(html);
    }
  };

  // Event Handlers
  $(document).ready(function() {
    // Category Sub-tabs Click
    $(document).on('click', '#coaFilterTabs .coa-tab-btn', function() {
      var $btn = $(this);
      $('#coaFilterTabs .coa-tab-btn').removeClass('active');
      $btn.addClass('active');
      CompanyActivityManager.activeCategory = $btn.data('category') || 'all';
      CompanyActivityManager.loadActivity(false);
    });

    // Timeframe Change
    $(document).on('change', '#coaTimeframeSelect', function() {
      CompanyActivityManager.activeTimeframe = $(this).val() || 'all';
      CompanyActivityManager.loadActivity(false);
    });

    // Refresh Click
    $(document).on('click', '#coaRefreshBtn', function() {
      CompanyActivityManager.loadActivity(false);
    });

    // Load More Click
    $(document).on('click', '#coaLoadMoreBtn', function() {
      CompanyActivityManager.loadActivity(true);
    });

    // 300ms Debounced Search
    $(document).on('input', '#coaSearchInput', function() {
      var val = $.trim($(this).val());
      if (val.length > 0) {
        $('#coaSearchClear').addClass('visible');
      } else {
        $('#coaSearchClear').removeClass('visible');
      }

      clearTimeout(CompanyActivityManager.searchTimer);
      CompanyActivityManager.searchTimer = setTimeout(function() {
        CompanyActivityManager.searchQuery = val;
        CompanyActivityManager.loadActivity(false);
      }, 300);
    });

    // Clear Search
    $(document).on('click', '#coaSearchClear', function() {
      $('#coaSearchInput').val('');
      $(this).removeClass('visible');
      CompanyActivityManager.searchQuery = '';
      CompanyActivityManager.loadActivity(false);
    });

    // Initialize CompanyActivityManager
    CompanyActivityManager.init();
  });
})();
</script>
