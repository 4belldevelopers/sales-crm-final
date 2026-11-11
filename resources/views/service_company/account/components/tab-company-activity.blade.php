@include('service_company.client.components.tab-activity-style')

<div class="cd-activity-wrapper" id="coaWrapper">

  {{-- ── TIER 1: TOP HEADER TOOLBAR CARD ── --}}
  <div class="cd-activity-header-card">
    <div class="cd-activity-header-left">
      <div class="cd-activity-header-icon">
        <i class="fas fa-clock-rotate-left"></i>
      </div>
      <div>
        <div class="cd-activity-header-title">Company Activity &amp; Audit Timeline</div>
        <div class="cd-activity-header-sub">
          <span>Live recruitment lifecycle &amp; team activity across organization</span>
          <span class="cd-activity-health-pill health-active" id="coaHealthPill" title="Company Activity Freshness Health">
            <span class="cd-activity-pulse-dot"></span>
            <span id="coaHealthText">Active</span>
          </span>
          <span id="coaSyncPill" class="badge badge-light-primary" style="font-size: 10px; padding: 2px 7px; font-weight: 600;" title="Live Real-Time Activity">
            <i class="fas fa-wifi me-1 text-success"></i>
            <span id="coaSyncText">Live</span>
          </span>
        </div>
      </div>
    </div>

    <div class="cd-activity-header-right">
      {{-- Dynamic 300ms Debounced Search Sub-Card --}}
      <div class="cd-activity-search-subcard">
        <i class="fas fa-search"></i>
        <input type="text" id="coaSearchInput" placeholder="Search Activity..." autocomplete="off">
        <button type="button" class="cd-activity-search-clear" id="coaSearchClear" title="Clear search">
          <i class="fas fa-times"></i>
        </button>
      </div>

      {{-- Timeframe Filter Dropdown (Date) --}}
      <select id="coaTimeframeSelect" class="form-select form-select-sm" style="width: 120px; height: 34px; font-size: 11.5px; border-radius: 8px; border: 1px solid #e2e8f0; font-weight: 500;">
        <option value="all">All Time</option>
        <option value="today">Today</option>
        <option value="7d">Last 7 Days</option>
        <option value="30d">Last 30 Days</option>
      </select>

      {{-- Refresh Button --}}
      <button type="button" class="cd-activity-btn-refresh" id="coaRefreshBtn" title="Refresh Activity Feed">
        <i class="fas fa-sync-alt"></i>
      </button>
    </div>
  </div>

  {{-- ── TIER 2: FILTER SUB-TABS CARD ── --}}
  <div class="cd-activity-tabs-card" id="coaFilterTabs">
    <button type="button" class="cd-activity-tab-btn active" data-category="all">
      <span>All Events</span>
      <span class="cd-activity-tab-badge" id="coaCountAll">0</span>
    </button>
    <button type="button" class="cd-activity-tab-btn" data-category="pipeline">
      <span>Pipeline</span>
      <span class="cd-activity-tab-badge" id="coaCountPipeline">0</span>
    </button>
    <button type="button" class="cd-activity-tab-btn" data-category="interviews">
      <span>Interviews</span>
      <span class="cd-activity-tab-badge" id="coaCountInterviews">0</span>
    </button>
    <button type="button" class="cd-activity-tab-btn" data-category="offers">
      <span>Offers &amp; Joining</span>
      <span class="cd-activity-tab-badge" id="coaCountOffers">0</span>
    </button>
    <button type="button" class="cd-activity-tab-btn" data-category="jobs">
      <span>Jobs</span>
      <span class="cd-activity-tab-badge" id="coaCountJobs">0</span>
    </button>
    <button type="button" class="cd-activity-tab-btn" data-category="team">
      <span>Team</span>
      <span class="cd-activity-tab-badge" id="coaCountTeam">0</span>
    </button>
  </div>

  {{-- ── TIER 3: ACTIVITY STREAM CONTAINER ── --}}
  {{-- Skeleton Placeholders --}}
  <div id="coaSkeletonLoader" style="display: none;">
    <div class="cd-activity-skeleton" style="background:#fff; border:1px solid #f1f5f9; border-radius:10px; padding:16px 20px; margin-bottom:12px;">
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:14px; width:35%; margin-bottom:10px;"></div>
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:11px; width:70%; margin-bottom:8px;"></div>
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:11px; width:50%;"></div>
    </div>
    <div class="cd-activity-skeleton" style="background:#fff; border:1px solid #f1f5f9; border-radius:10px; padding:16px 20px; margin-bottom:12px;">
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:14px; width:40%; margin-bottom:10px;"></div>
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:11px; width:80%; margin-bottom:8px;"></div>
      <div style="background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:cdPulseGreen 1.5s infinite; border-radius:4px; height:11px; width:45%;"></div>
    </div>
  </div>

  {{-- Continuous Connecting Line & Activity Cards Stream --}}
  <div class="cd-activity-stream" id="coaCardsStream">
    {{-- Mapped AJAX Activity Cards Injected Here --}}
  </div>

  {{-- Pagination / Load More --}}
  <div id="coaLoadMoreWrap" class="text-center py-3" style="display: none;">
    <button type="button" class="btn btn-sm btn-light fw-bold" id="coaLoadMoreBtn" style="border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; font-size: 11.5px; padding: 7px 18px;">
      <i class="fas fa-angle-double-down me-1"></i> Load Older Activities
    </button>
  </div>

</div>

<script>
/**
 * Master Sliding Drawer Controllers & Company Activity Timeline Engine
 * Guarantees zero page navigation when viewing Requisition or Candidate profiles.
 */
(function() {

  // 1. DEDICATED READ-ONLY JOB DRAWER CONTROLLER
  window.openJobDrawer = function(jobId, options) {
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

    // Immediate fresh skeleton loader
    $('#kt_activitiesJob_show_body').html(
      '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
      '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
      '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Requisition Details…</div>' +
      '  <div class="text-muted fs-7">Retrieving verified requisition details</div>' +
      '</div>'
    );

    // Metronic Drawer Activation
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
          $drawer.find('.job-action-btn.edit, a[href*="jobs/edit"], a[href*="/edit"], .info-card-edit, .btn-edit, .spartner-listing, .hm-listing, .status_stage, .job-drawer-add-candidate, .btn-primary:contains("Add Team"), .btn-primary:contains("Add Agency")').remove();
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

  // 2. DEDICATED READ-ONLY CANDIDATE DRAWER CONTROLLER
  window.openCandidateDrawer = function(candidateId, options) {
    if (!candidateId) return;
    var isReadOnly = true;
    if (typeof options === 'object' && options !== null && options.readOnly !== undefined) {
      isReadOnly = !!options.readOnly;
    } else if (typeof options === 'boolean') {
      isReadOnly = options;
    }

    var $drawer = $('#kt_activitiesApplication');
    if (!$drawer.length) {
      $('body').append(`
        <div id="kt_activitiesApplication_toggle" style="display:none;"></div>
        <div id="kt_activitiesApplication" class="bg-body bg-custom-bar" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesApplication_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
          <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar"></div>
        </div>
      `);
      $drawer = $('#kt_activitiesApplication');
    }

    var sid = String(candidateId);
    $('#application_show_right_bar').html(
      '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
      '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
      '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Candidate Profile…</div>' +
      '  <div class="text-muted fs-7">Retrieving verified application details</div>' +
      '</div>'
    );

    if (isReadOnly) {
      $drawer.addClass('drawer-readonly-view');
    } else {
      $drawer.removeClass('drawer-readonly-view');
    }

    // Metronic Drawer Activation
    if (typeof KTDrawer !== 'undefined') {
      var drawerInst = KTDrawer.getInstance(document.getElementById('kt_activitiesApplication'));
      if (drawerInst) {
        drawerInst.show();
      } else {
        $('#kt_activitiesApplication_toggle').trigger('click');
      }
    } else {
      $('#kt_activitiesApplication_toggle').trigger('click');
    }

    $drawer.css('z-index', '999999');

    var currentPage = $('.custom-pagination .page-link.active').data('page') || 1;
    var url = "{{ route('service_company.job-applications.show', ':id') }}";
    url = url.replace(':id', sid) + "?read_only=" + (isReadOnly ? 1 : 0) + "&currentPage=" + currentPage;

    $.ajax({
      type: 'GET',
      url: url,
      success: function(response) {
        if (response && response.view) {
          $('#application_show_right_bar').html(response.view);
          if (isReadOnly) {
            $drawer.addClass('drawer-readonly-view');
            $drawer.find('.cdr-btn-primary, a[href*="job-applications/edit"], a[href*="/edit"], .jw-card-edit-btn, .edit-note, .delete-note, #hdr_move_stage_btn, .btn-edit, [data-action="edit"]').hide();
          }
          if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
        } else if (response && response.status === 'error') {
          $('#application_show_right_bar').html(
            '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
            '  <div class="text-danger fw-bold fs-4 mb-2">Notice</div>' +
            '  <div class="text-gray-700 fs-6">' + (response.message || 'Candidate details could not be loaded.') + '</div>' +
            '</div>'
          );
        }
      },
      error: function(xhr) {
        var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to connect to server. Please refresh and try again.';
        $('#application_show_right_bar').html(
          '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
          '  <div class="text-danger fw-bold fs-4 mb-2">Error Loading Profile</div>' +
          '  <div class="text-gray-700 fs-6">' + msg + '</div>' +
          '</div>'
        );
      }
    });
  };

  // 3. MASTER COMPANY ACTIVITY MANAGER
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
        $('#coaLoadMoreBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Loading...');
      } else {
        self.currentPage = 1;
        $('#coaSkeletonLoader').show();
        $('#coaCardsStream').hide();
        $('#coaRefreshBtn').find('i').addClass('fa-spin');
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
          $('#coaRefreshBtn').find('i').removeClass('fa-spin');
          $('#coaLoadMoreBtn').html('<i class="fas fa-angle-double-down me-1"></i> Load Older Activities');

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

            // Sync with Overview Tab Recent Activity
            self.renderOverviewTimeline(self.cachedItems);
          }
        },
        error: function(xhr, status) {
          if (status === 'abort') return;
          $('#coaSkeletonLoader').hide();
          $('#coaCardsStream').show();
          $('#coaRefreshBtn').find('i').removeClass('fa-spin');
          $('#coaLoadMoreBtn').html('<i class="fas fa-angle-double-down me-1"></i> Load Older Activities');

          if (!append) {
            $('#coaCardsStream').html(
              '<div class="cd-activity-empty-state">' +
              '  <div class="cd-activity-empty-icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>' +
              '  <div class="cd-activity-empty-title">Unable to Load Company Activities</div>' +
              '  <div class="cd-activity-empty-text">An unexpected error occurred while fetching company events. Please click refresh to try again.</div>' +
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
          '<div class="p-4 text-center text-muted" style="font-size:11.5px;">No recent company activities recorded.</div>'
        );
        return;
      }

      var html = '<div class="cd-activity-stream" style="padding-left:14px; margin-top:0;">';
      topItems.forEach(function(act) {
        var cat = act.category || 'pipeline';
        var job = act.job || {};
        var cand = act.candidate || {};
        var actor = act.actor || {};
        var title = (cat === 'jobs') ? (job.title || 'Requisition') : (cand.name || 'Candidate');
        var subtext = act.activity_label || (act.description || 'Activity logged');
        var timeAgo = act.time_ago || act.formatted_date || 'Recently';
        var badgeText = act.status_text || 'Active';
        var dotClass = matchDot(cat);

        var drawerClick = '';
        if (cat === 'jobs' && job.id) {
          drawerClick = 'onclick="if(window.openJobDrawer){window.openJobDrawer(\'' + job.id + '\', {readOnly:true});}"';
        } else if (cand.id || cand.wise_id) {
          var cid = cand.wise_id || cand.id;
          drawerClick = 'onclick="if(window.openCandidateDrawer){window.openCandidateDrawer(\'' + cid + '\', {readOnly:true});}"';
        }

        html += `
          <div class="cd-activity-card" style="margin-bottom:8px; cursor:pointer;" ${drawerClick}>
            <div class="cd-activity-node-dot ${dotClass}" style="left:-16px; top:12px; width:13px; height:13px;"></div>
            <div style="padding:10px 14px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
              <div style="min-width:0; flex:1;">
                <div style="font-size:12.5px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${title}</div>
                <div style="font-size:11px; color:#64748b; margin-top:2px;">${subtext} &bull; <span style="color:#1e293b; font-weight:600;">${actor.name || 'Team Member'}</span></div>
              </div>
              <div style="flex-shrink:0; text-align:right;">
                <span class="cd-status-badge status-active" style="font-size:9.5px; padding:2px 6px;">${badgeText}</span>
                <div style="font-size:10px; color:#94a3b8; font-weight:500; margin-top:3px;">${timeAgo}</div>
              </div>
            </div>
          </div>
        `;
      });
      html += '</div>';

      $container.html(html);

      function matchDot(c) {
        if (c === 'jobs') return 'dot-status-active stream-jobs';
        if (c === 'interviews') return 'dot-status-interview stream-interviews';
        if (c === 'offers') return 'dot-status-active stream-offers';
        if (c === 'team') return 'dot-status-crm stream-crm';
        return 'dot-status-pipeline stream-pipeline';
      }
    }
  };

  // Event Listeners
  $(document).ready(function() {
    // Category Sub-tabs Click
    $(document).on('click', '#coaFilterTabs .cd-activity-tab-btn', function() {
      var $btn = $(this);
      $('#coaFilterTabs .cd-activity-tab-btn').removeClass('active');
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

    // Delegated click for Job drawer
    $(document).on('click', '.show-detail-open, [data-entity-type="job"]', function(e) {
      var jobId = $(this).data('id') || $(this).data('entity-id');
      if (jobId && window.openJobDrawer) {
        e.preventDefault();
        window.openJobDrawer(jobId, { readOnly: true });
      }
    });

    // Delegated click for Candidate drawer
    $(document).on('click', '.show-application-detail, [data-entity-type="candidate"]', function(e) {
      var candId = $(this).data('row-id') || $(this).data('id') || $(this).data('entity-id');
      if (candId && window.openCandidateDrawer) {
        e.preventDefault();
        window.openCandidateDrawer(candId, { readOnly: true });
      }
    });

    // Initialize CompanyActivityManager
    CompanyActivityManager.init();
  });
})();
</script>
