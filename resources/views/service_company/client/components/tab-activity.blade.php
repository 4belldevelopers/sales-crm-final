<div class="cd-activity-wrapper" id="cdActivityWrapper">

    {{-- TIER 1: TOP HEADER TOOLBAR CARD --}}
    <div class="cd-activity-header-card">
        <div class="cd-activity-header-left">
            <div class="cd-activity-header-icon">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="cd-activity-header-title">Client Activity &amp; Audit Feed</div>
                <div class="cd-activity-header-sub">
                    <span>Live recruitment timeline &amp; stakeholder activity</span>
                    <span class="cd-activity-health-pill health-active" id="cdActivityHealthPill" title="Client Activity Freshness Health">
                        <span class="cd-activity-pulse-dot"></span>
                        <span id="cdActivityHealthText">Active</span>
                    </span>
                    <span id="cdActivitySyncPill" class="badge badge-light-primary" style="font-size: 10px; padding: 2px 7px; font-weight: 600;" title="Live Reverb WebSocket State">
                        <i class="fas fa-wifi me-1" id="cdActivitySyncIcon"></i>
                        <span id="cdActivitySyncText">Live</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="cd-activity-header-right">
            {{-- Dynamic 300ms Debounced Search Sub-Card --}}
            <div class="cd-activity-search-subcard">
                <i class="fas fa-search"></i>
                <input type="text" id="cdActivitySearchInput" placeholder="Filter by candidate, role, actor..." autocomplete="off">
                <button type="button" class="cd-activity-search-clear" id="cdActivitySearchClear" title="Clear search">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Timeframe Filter Dropdown --}}
            <select id="cdActivityTimeframeSelect" class="form-select form-select-sm" style="width: 120px; height: 34px; font-size: 11.5px; border-radius: 8px; border: 1px solid #e2e8f0; font-weight: 500;">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
            </select>

            {{-- Refresh Button --}}
            <button type="button" class="cd-activity-btn-refresh" id="cdActivityRefreshBtn" title="Refresh Activity Feed">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- TIER 2: FILTER SUB-TABS CARD --}}
    <div class="cd-activity-tabs-card" id="cdActivityFilterTabs">
        <button type="button" class="cd-activity-tab-btn active" data-category="all">
            <span>All Events</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountAll">0</span>
        </button>
        <button type="button" class="cd-activity-tab-btn" data-category="pipeline">
            <span>Pipeline</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountPipeline">0</span>
        </button>
        <button type="button" class="cd-activity-tab-btn" data-category="interviews">
            <span>Interviews</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountInterviews">0</span>
        </button>
        <button type="button" class="cd-activity-tab-btn" data-category="offers">
            <span>Offers &amp; Joining</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountOffers">0</span>
        </button>
        <button type="button" class="cd-activity-tab-btn" data-category="jobs">
            <span>Jobs</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountJobs">0</span>
        </button>
        <button type="button" class="cd-activity-tab-btn" data-category="crm">
            <span>CRM &amp; Notes</span>
            <span class="cd-activity-tab-badge" id="cdActivityCountCrm">0</span>
        </button>
    </div>

    {{-- TIER 3: ACTIVITY STREAM CONTAINER --}}
    {{-- Skeleton Placeholders (displayed during fetch) --}}
    <div id="cdActivitySkeletonLoader" style="display: none;">
        <div class="cd-activity-skeleton">
            <div class="cd-skeleton-line" style="width: 35%; height: 14px;"></div>
            <div class="cd-skeleton-line" style="width: 70%; height: 11px;"></div>
            <div class="cd-skeleton-line" style="width: 50%; height: 11px;"></div>
        </div>
        <div class="cd-activity-skeleton">
            <div class="cd-skeleton-line" style="width: 40%; height: 14px;"></div>
            <div class="cd-skeleton-line" style="width: 80%; height: 11px;"></div>
            <div class="cd-skeleton-line" style="width: 45%; height: 11px;"></div>
        </div>
        <div class="cd-activity-skeleton">
            <div class="cd-skeleton-line" style="width: 30%; height: 14px;"></div>
            <div class="cd-skeleton-line" style="width: 65%; height: 11px;"></div>
            <div class="cd-skeleton-line" style="width: 55%; height: 11px;"></div>
        </div>
    </div>

    {{-- Stream Container for Dynamic Cards --}}
    <div class="cd-activity-stream" id="cdActivityCardsStream">
        {{-- Mapped AJAX Activity Cards Injected Here --}}
    </div>

    {{-- Defensive Pagination Container --}}
    <div id="cdActivityPaginationContainer" class="text-center py-3" style="display: none;">
        <button type="button" class="btn btn-sm btn-light fw-bold" id="cdActivityLoadMoreBtn" style="border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; font-size: 11.5px; padding: 6px 16px;">
            <i class="fas fa-angle-double-down me-1"></i> Load Older Activities
        </button>
    </div>

</div>

<script>
/**
 * Master Client Activity Feed Manager
 * Strict CQRS Client-Side State Machine & Reverb Acceleration Engine
 */
(function() {
    if (window.ClientActivityManager) {
        return; // Already registered singleton
    }

    var ClientActivityManager = {
        endpoint: '{!! route('service_company.clients.activity_data_ajax') !!}',
        activeClientId: null,
        isCustom: false,
        partnerCompanyId: null,
        activeCategory: 'all',
        activeTimeframe: 'all',
        searchQuery: '',
        nextCursor: null,
        hasMore: false,
        isLoading: false,
        inFlightXhr: null,
        searchTimer: null,
        currentGeneration: 0,

        // Distributed State Machine
        syncState: 'OFFLINE', // 'LIVE' | 'SYNCING' | 'RECONNECTING' | 'OFFLINE'
        syncGeneration: 0,
        lastContiguousReconciledSequence: 0,
        incomingSyncBuffer: [],
        reverbChannel: null,

        // Idempotency Deduplication Cache (LRU up to 500 event IDs)
        lruCache: new Set(),
        maxLruSize: 500,

        /**
         * Initializes or switches the active client context.
         */
        initClient: function(clientId, isCustom, partnerCompanyId, forceRefresh) {
            var cId = parseInt(clientId, 10);
            if (!cId || isNaN(cId)) return;

            // Check if switching to new client or forced refresh
            var isNewClient = (this.activeClientId !== cId);
            this.activeClientId = cId;
            this.isCustom = !!isCustom;
            this.partnerCompanyId = partnerCompanyId || null;

            if (isNewClient || forceRefresh) {
                this.resetState(isNewClient);
                if (isNewClient) {
                    this.setupReverbChannel();
                }
            }

            this.loadActivity(false, !!forceRefresh);
        },

        resetState: function(fullReset) {
            if (this.inFlightXhr) {
                try { this.inFlightXhr.abort(); } catch(e) {}
                this.inFlightXhr = null;
            }
            this.nextCursor = null;
            this.hasMore = false;
            this.lruCache.clear();
            this.incomingSyncBuffer = [];
            this.lastContiguousReconciledSequence = 0;
            this.currentGeneration++;
            this.syncGeneration++;
            this.setSyncState('SYNCING');

            if (fullReset) {
                this.activeCategory = 'all';
                this.activeTimeframe = 'all';
                this.searchQuery = '';
                $('#cdActivitySearchInput').val('');
                $('#cdActivitySearchClear').removeClass('visible');
                $('#cdActivityTimeframeSelect').val('all');
                $('#cdActivityFilterTabs .cd-activity-tab-btn').removeClass('active');
                $('#cdActivityFilterTabs .cd-activity-tab-btn[data-category="all"]').addClass('active');
                $('#cdActivityCardsStream').empty();
                $('#cdActivityPaginationContainer').hide();
                this.updateKpiCounters({ all: 0, pipeline: 0, interviews: 0, offers: 0, jobs: 0, crm: 0 });
            }
        },

        setSyncState: function(state) {
            this.syncState = state;
            var $pill = $('#cdActivitySyncPill');
            var $icon = $('#cdActivitySyncIcon');
            var $text = $('#cdActivitySyncText');

            if (!$pill.length) return;

            $pill.removeClass('badge-light-primary badge-light-warning badge-light-danger badge-light-success');

            if (state === 'LIVE') {
                $pill.addClass('badge-light-success');
                $icon.attr('class', 'fas fa-wifi me-1 text-success');
                $text.text('Live');
            } else if (state === 'SYNCING') {
                $pill.addClass('badge-light-primary');
                $icon.attr('class', 'fas fa-rotate me-1 text-primary fa-spin');
                $text.text('Syncing');
            } else if (state === 'RECONNECTING') {
                $pill.addClass('badge-light-warning');
                $icon.attr('class', 'fas fa-satellite-dish me-1 text-warning');
                $text.text('Reconnecting');
            } else {
                $pill.addClass('badge-light-secondary');
                $icon.attr('class', 'fas fa-circle-xmark me-1 text-secondary');
                $text.text('Offline');
            }
        },

        /**
         * Loads activity data from authoritative CQRS endpoint.
         */
        loadActivity: function(append, forceRefresh) {
            if (!this.activeClientId || this.isLoading) return;

            var self = this;
            var thisGen = this.currentGeneration;
            this.isLoading = true;

            if (!append) {
                $('#cdActivitySkeletonLoader').show();
                $('#cdActivityCardsStream').hide();
                $('#cdActivityPaginationContainer').hide();
                $('#cdActivityRefreshBtn').addClass('spinning');
            } else {
                $('#cdActivityLoadMoreBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Loading...');
            }

            var params = {
                client_id: this.activeClientId,
                custom: this.isCustom ? 1 : 0,
                partner_company_id: this.partnerCompanyId || '',
                category: this.activeCategory,
                timeframe: this.activeTimeframe,
                q: this.searchQuery || ''
            };

            if (forceRefresh) {
                params.refresh = 1;
            }

            if (append && this.nextCursor) {
                params.cursor = this.nextCursor;
            }

            this.inFlightXhr = $.ajax({
                url: this.endpoint,
                type: 'GET',
                data: params,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    if (thisGen !== self.currentGeneration) return; // Stale client response

                    if (res && res.success) {
                        self.nextCursor = res.pagination ? res.pagination.next_cursor : null;
                        self.hasMore = res.pagination ? !!res.pagination.has_more : false;

                        // Update KPIs
                        if (res.kpi) {
                            self.updateKpiCounters(res.kpi);
                        }

                        // Update Health status
                        if (res.health) {
                            self.updateHealthStatus(res.health);
                        }

                        // Populate LRU cache & Contiguous Sequence
                        if (res.data && Array.isArray(res.data)) {
                            res.data.forEach(function(item) {
                                if (item.event_id) self.lruCache.add(item.event_id);
                                var seq = parseInt(item.client_activity_sequence, 10);
                                if (!isNaN(seq) && seq > self.lastContiguousReconciledSequence) {
                                    self.lastContiguousReconciledSequence = seq;
                                }
                            });
                        }

                        // Render Cards
                        if (append) {
                            if (res.html) {
                                $('#cdActivityCardsStream').append(res.html);
                            }
                        } else {
                            $('#cdActivityCardsStream').html(res.html || '').show();
                        }

                        // Update pagination button
                        if (self.hasMore && self.nextCursor) {
                            $('#cdActivityPaginationContainer').show();
                            $('#cdActivityLoadMoreBtn').html('<i class="fas fa-angle-double-down me-1"></i> Load Older Activities');
                        } else {
                            $('#cdActivityPaginationContainer').hide();
                        }

                        self.setSyncState('LIVE');
                    } else {
                        var errMsg = (res && res.error) ? res.error.message : 'Failed to fetch activity feed';
                        if (!append) {
                            $('#cdActivityCardsStream').html('<div class="alert alert-danger m-3">' + errMsg + '</div>').show();
                        }
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    if (thisGen !== self.currentGeneration) return;

                    var errMsg = 'Error loading activity feed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error && xhr.responseJSON.error.message) {
                        errMsg = xhr.responseJSON.error.message;
                    }
                    if (!append) {
                        $('#cdActivityCardsStream').html('<div class="alert alert-danger m-3">' + errMsg + '</div>').show();
                    }
                },
                complete: function() {
                    self.isLoading = false;
                    self.inFlightXhr = null;
                    $('#cdActivitySkeletonLoader').hide();
                    $('#cdActivityRefreshBtn').removeClass('spinning');
                }
            });
        },

        updateKpiCounters: function(kpi) {
            $('#cdActivityCountAll').text(kpi.all || 0);
            $('#cdActivityCountPipeline').text(kpi.pipeline || 0);
            $('#cdActivityCountInterviews').text(kpi.interviews || 0);
            $('#cdActivityCountOffers').text(kpi.offers || 0);
            $('#cdActivityCountJobs').text(kpi.jobs || 0);
            $('#cdActivityCountCrm').text(kpi.crm || 0);
        },

        updateHealthStatus: function(health) {
            var $pill = $('#cdActivityHealthPill');
            var $text = $('#cdActivityHealthText');
            if (!$pill.length) return;

            $pill.removeClass('health-active health-inactive health-stale');
            var status = health.status || 'active';

            if (status === 'active') {
                $pill.addClass('health-active');
                $text.text('Active');
            } else if (status === 'inactive') {
                $pill.addClass('health-inactive');
                $text.text('Inactive');
            } else {
                $pill.addClass('health-stale');
                $text.text('Stale');
            }
        },

        /**
         * Reverb Acceleration Layer (Thin Envelope & Gap-Free Catchup)
         */
        setupReverbChannel: function() {
            var self = this;
            if (!window.Echo || !this.activeClientId) return;

            var channelName = 'private-client.' + this.activeClientId + '.activity';

            if (this.reverbChannel) {
                try { window.Echo.leave(this.reverbChannel); } catch(e) {}
            }

            this.reverbChannel = channelName;

            try {
                window.Echo.private(channelName)
                    .listen('.client.activity.occurred', function(envelope) {
                        self.handleIncomingEnvelope(envelope);
                    });

                // Listen to connection state changes
                if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                    window.Echo.connector.pusher.connection.bind('connected', function() {
                        self.onReverbReconnect();
                    });
                    window.Echo.connector.pusher.connection.bind('disconnected', function() {
                        self.setSyncState('RECONNECTING');
                    });
                }
            } catch(e) {
                console.warn('[ActivityManager] Echo listener warning:', e);
            }
        },

        onReverbReconnect: function() {
            var self = this;
            this.setSyncState('SYNCING');
            this.syncGeneration++;
            var curSyncGen = this.syncGeneration;

            // Execute catch-up reconciliation
            $.ajax({
                url: self.endpoint,
                type: 'GET',
                data: {
                    client_id: self.activeClientId,
                    custom: self.isCustom ? 1 : 0,
                    partner_company_id: self.partnerCompanyId || '',
                    category: self.activeCategory,
                    timeframe: self.activeTimeframe
                },
                dataType: 'json',
                success: function(res) {
                    if (curSyncGen !== self.syncGeneration) return;
                    if (res && res.success && res.html) {
                        $('#cdActivityCardsStream').html(res.html);
                        if (res.kpi) self.updateKpiCounters(res.kpi);
                        if (res.health) self.updateHealthStatus(res.health);

                        // Drain buffer
                        self.drainSyncBuffer();
                    }
                    self.setSyncState('LIVE');
                },
                error: function() {
                    self.setSyncState('LIVE');
                }
            });
        },

        handleIncomingEnvelope: function(thinEvent) {
            if (!thinEvent || !thinEvent.event_id) return;

            // LRU Check
            if (this.lruCache.has(thinEvent.event_id)) return;

            var seq = parseInt(thinEvent.client_activity_sequence, 10);

            // Buffer during SYNCING state
            if (this.syncState === 'SYNCING') {
                this.incomingSyncBuffer.push(thinEvent);
                return;
            }

            // Gap check: if sequence gap detected, trigger sync
            if (seq > this.lastContiguousReconciledSequence + 1) {
                this.onReverbReconnect();
                return;
            }

            // Contiguous or immediate event: fetch full card before advancing sequence
            var self = this;
            this.fetchAndPrependEvent(thinEvent.event_id, seq);
        },

        fetchAndPrependEvent: function(eventId, seq) {
            var self = this;
            $.ajax({
                url: self.endpoint,
                type: 'GET',
                data: {
                    client_id: self.activeClientId,
                    custom: self.isCustom ? 1 : 0,
                    category: self.activeCategory,
                    timeframe: self.activeTimeframe
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.success && res.html) {
                        $('#cdActivityCardsStream').html(res.html);
                        if (res.kpi) self.updateKpiCounters(res.kpi);
                        if (res.health) self.updateHealthStatus(res.health);
                        self.lruCache.add(eventId);
                        if (seq && seq > self.lastContiguousReconciledSequence) {
                            self.lastContiguousReconciledSequence = seq;
                        }
                    }
                }
            });
        },

        drainSyncBuffer: function() {
            var self = this;
            var buffer = this.incomingSyncBuffer.slice();
            this.incomingSyncBuffer = [];

            buffer.sort(function(a, b) {
                return (a.client_activity_sequence || 0) - (b.client_activity_sequence || 0);
            });

            buffer.forEach(function(envelope) {
                if (!self.lruCache.has(envelope.event_id)) {
                    self.lruCache.add(envelope.event_id);
                }
            });
        },

        toggleKebabMenu: function(e, btn) {
            e.stopPropagation();
            var $btn = $(btn);
            var $card = $btn.closest('.cd-activity-card');
            var $menu = $btn.siblings('.cd-activity-kebab-menu');
            var wasOpen = $menu.hasClass('show');

            // Close all open menus and remove elevation from cards
            $('.cd-activity-kebab-menu').removeClass('show dropup');
            $('.cd-activity-card').removeClass('cd-menu-open');

            if (!wasOpen) {
                // Elevate this card's stacking context
                $card.addClass('cd-menu-open');

                // Viewport-aware vertical placement
                var btnOffset = $btn.offset();
                var btnHeight = $btn.outerHeight();
                var menuHeight = 160;
                var windowBottom = $(window).scrollTop() + $(window).height();

                var $scrollContainer = $btn.closest('.scroll-y, [data-kt-scroll="true"], #kt_activities_scroll');
                var containerBottom = $scrollContainer.length ? ($scrollContainer.offset().top + $scrollContainer.innerHeight()) : windowBottom;
                var effectiveBottom = Math.min(windowBottom, containerBottom);

                var spaceBelow = effectiveBottom - (btnOffset.top + btnHeight);
                if (spaceBelow < menuHeight + 15 && (btnOffset.top - $(window).scrollTop()) > menuHeight) {
                    $menu.addClass('dropup');
                } else {
                    $menu.removeClass('dropup');
                }

                $menu.addClass('show');
            }
        },

        refreshFeed: function() {
            this.nextCursor = null;
            this.loadActivity(false, true);
        },

        openNoteModal: function() {
            if (typeof window.openClientNotesModal === 'function') {
                window.openClientNotesModal();
            } else {
                alert('Add Note modal feature is active.');
            }
        }
    };

    window.ClientActivityManager = ClientActivityManager;

    /**
     * Dedicated View Profile opener for Candidates (strictly Read-Only).
     * Prevents stale data and strips/suppresses all Edit functionality.
     */
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
            console.warn('Candidate Drawer #kt_activitiesApplication not found in DOM.');
            return;
        }

        var sid = String(candidateId);
        // Anti-stale DOM reset: immediately display fresh skeleton loader
        if (window.__activeCandidateDrawerId !== sid) {
            window.__activeCandidateDrawerId = null;
            $('#application_show_right_bar').html(
                '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
                '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
                '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Candidate Profile…</div>' +
                '  <div class="text-muted fs-7">Retrieving verified application details</div>' +
                '</div>'
            );
        }

        if (isReadOnly) {
            $drawer.addClass('drawer-readonly-view');
        } else {
            $drawer.removeClass('drawer-readonly-view');
        }

        // Metronic drawer activation
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
                    window.__activeCandidateDrawerId = sid;
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

    /**
     * Dedicated View Profile opener for Jobs (strictly Read-Only).
     * Prevents stale data and strips/suppresses all Edit functionality.
     */
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
            window.open(`{{ url('service_company/jobs/viewmore') }}/${jobId}`, '_blank');
            return;
        }

        if (isReadOnly) {
            $drawer.addClass('drawer-readonly-view');
        } else {
            $drawer.removeClass('drawer-readonly-view');
        }

        // Anti-stale DOM reset: immediately show loader
        $('#kt_activitiesJob_show_body').html(
            '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
            '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
            '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Requisition Details…</div>' +
            '  <div class="text-muted fs-7">Retrieving verified requisition details</div>' +
            '</div>'
        );

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

        var url = "{{ route('service_company.jobs.viewmore', ':id') }}?viewmore=viewmore" + (isReadOnly ? '&readonly=1' : '');
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
                    $drawer.find('.job-action-btn.edit, a[href*="jobs/edit"], a[href*="/edit"], .info-card-edit, .btn-edit').hide();
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

    window.openCandidateReadOnlyDrawer = function(candidateId) {
        window.openCandidateDrawer(candidateId, { readOnly: true });
    };

    // Clean up read-only state when drawers are closed
    $(document).on('click', '#kt_activitiesApplication_close, #kt_client_activitiesApplication_close', function() {
        $('#kt_activitiesApplication').removeClass('drawer-readonly-view');
    });
    $(document).on('click', '#kt_activitiesJob_close', function() {
        $('#kt_activitiesJob_show').removeClass('drawer-readonly-view');
    });

    // Bind DOM events
    $(document).ready(function() {
        // Category Sub-tabs Click
        $(document).on('click', '#cdActivityFilterTabs .cd-activity-tab-btn', function() {
            var $btn = $(this);
            $('#cdActivityFilterTabs .cd-activity-tab-btn').removeClass('active');
            $btn.addClass('active');

            ClientActivityManager.activeCategory = $btn.data('category') || 'all';
            ClientActivityManager.nextCursor = null;
            ClientActivityManager.loadActivity(false);
        });

        // Timeframe select change
        $(document).on('change', '#cdActivityTimeframeSelect', function() {
            ClientActivityManager.activeTimeframe = $(this).val() || 'all';
            ClientActivityManager.nextCursor = null;
            ClientActivityManager.loadActivity(false);
        });

        // Refresh button click
        $(document).on('click', '#cdActivityRefreshBtn', function() {
            ClientActivityManager.refreshFeed();
        });

        // Load More button click
        $(document).on('click', '#cdActivityLoadMoreBtn', function() {
            ClientActivityManager.loadActivity(true);
        });

        // 300ms Debounced Search
        $(document).on('input', '#cdActivitySearchInput', function() {
            var val = $(this).val();
            var $clearBtn = $('#cdActivitySearchClear');
            if (val && val.length > 0) {
                $clearBtn.addClass('visible');
            } else {
                $clearBtn.removeClass('visible');
            }

            clearTimeout(ClientActivityManager.searchTimer);
            ClientActivityManager.searchTimer = setTimeout(function() {
                ClientActivityManager.searchQuery = val;
                ClientActivityManager.nextCursor = null;
                ClientActivityManager.loadActivity(false);
            }, 300);
        });

        // Clear Search Click
        $(document).on('click', '#cdActivitySearchClear', function() {
            $('#cdActivitySearchInput').val('');
            $(this).removeClass('visible');
            ClientActivityManager.searchQuery = '';
            ClientActivityManager.nextCursor = null;
            ClientActivityManager.loadActivity(false);
        });

        // Close kebab dropdown on click outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.cd-activity-kebab-wrapper').length) {
                $('.cd-activity-kebab-menu').removeClass('show dropup');
                $('.cd-activity-card').removeClass('cd-menu-open');
            }
        });

        // Close kebab dropdown on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.cd-activity-kebab-menu').removeClass('show dropup');
                $('.cd-activity-card').removeClass('cd-menu-open');
            }
        });

        // Close kebab dropdown on scroll
        $(document).on('scroll.cdActivityKebab', '.scroll-y, [data-kt-scroll="true"], #kt_activities_scroll', function() {
            $('.cd-activity-kebab-menu').removeClass('show dropup');
            $('.cd-activity-card').removeClass('cd-menu-open');
        });

        // Listen for external ATS stage/status changes to dynamically refresh feed
        $(document).on('ats:activity:refresh ats:stage:updated CandidateStatusChanged', function() {
            if ($('#recent_activity').is(':visible') && window.ClientActivityManager) {
                window.ClientActivityManager.refreshFeed();
            }
        });
    });
})();
</script>
