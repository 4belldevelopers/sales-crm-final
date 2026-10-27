/**
 * JERO Service Company Drawer - Tab Coordinator
 * Architecture: Authoritative State Machine, Request Deduplication & Sub-Tab Dispatch Invariant
 * 
 * Rules:
 * 1. Single authoritative in-memory state machine per tab
 * 2. Request deduplication: same tab + same filter + same client + same instance => ignore duplicate activations
 * 3. Mandatory Sub-Tab Dispatch Invariant: all sub-tabs dispatch through setFilter()
 * 4. Zero DOM node count checks for lifecycle state
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var VALID_STATUSES = ['UNINITIALIZED', 'LOADING', 'READY', 'EMPTY', 'ERROR', 'CANCELLED', 'REFRESHING'];

    function ClientDrawerTabCoordinator(instance) {
        if (!instance) {
            throw new Error('[TabCoordinator] Requires parent ClientDrawerInstance');
        }
        this.instance = instance;
        this.state = {
            overview:        { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            recent_activity: { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            jobs:            { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            candidates:      { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            contacts:        { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            interviews:      { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            offers:          { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null },
            performance:     { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null }
        };
        this.activeTab = 'overview';
    }

    /**
     * Resolve tab name canonical alias
     * @param {string} rawTab
     * @returns {string}
     */
    ClientDrawerTabCoordinator.prototype.normalizeTabName = function(rawTab) {
        if (!rawTab) return 'overview';
        var clean = String(rawTab).replace('#', '').trim().toLowerCase();
        if (clean === 'requisitions' || clean === 'jobs' || clean === 'job') return 'jobs';
        if (clean === 'candidate_list' || clean === 'candidates') return 'candidates';
        if (clean === 'interview_tracker' || clean === 'interviews') return 'interviews';
        if (clean === 'candidate_joining' || clean === 'joining' || clean === 'offers') return 'offers';
        if (clean === 'activity' || clean === 'recent_activity') return 'recent_activity';
        if (clean === 'contacts' || clean === 'client_contacts') return 'contacts';
        if (clean === 'client_overview' || clean === 'overview') return 'overview';
        if (clean === 'candidatedashboard' || clean === 'performance' || clean === 'dashboard' || clean === 'analytics') return 'performance';
        return clean;
    };

    /**
     * Get tab state snapshot
     * @param {string} tabName
     * @returns {Object}
     */
    ClientDrawerTabCoordinator.prototype.getState = function(tabName) {
        var key = this.normalizeTabName(tabName);
        if (!this.state[key]) {
            this.state[key] = { status: 'UNINITIALIZED', filter: 'all', requestId: null, correlationId: null, lastLoadedAt: null };
        }
        return this.state[key];
    };

    /**
     * Perform State Transition in the Finite State Machine
     * @param {string} tabName
     * @param {string} nextStatus
     * @param {Object} [payload]
     */
    ClientDrawerTabCoordinator.prototype.transition = function(tabName, nextStatus, payload) {
        if (this.instance.isDestroyed()) return;

        var key = this.normalizeTabName(tabName);
        var current = this.getState(key);
        var prevStatus = current.status;

        if (!VALID_STATUSES.includes(nextStatus)) {
            console.error('[TabCoordinator] Invalid status transition: ' + nextStatus);
            return;
        }

        current.status = nextStatus;
        if (payload) {
            if (payload.filter !== undefined) current.filter = payload.filter;
            if (payload.requestId !== undefined) current.requestId = payload.requestId;
            if (payload.correlationId !== undefined) current.correlationId = payload.correlationId;
            if (payload.error !== undefined) current.error = payload.error;
        }

        if (nextStatus === 'READY' || nextStatus === 'EMPTY') {
            current.lastLoadedAt = Date.now();
        }

        this.instance.telemetry.logEvent(key, nextStatus, payload, {
            transition: prevStatus + ' -> ' + nextStatus
        });
    };

    /**
     * Activate tab with request deduplication
     * @param {string} tabName
     * @param {Object} [options]
     */
    ClientDrawerTabCoordinator.prototype.activateTab = function(tabName, options) {
        if (this.instance.isDestroyed()) return;
        options = options || {};

        var key = this.normalizeTabName(tabName);
        var state = this.getState(key);
        var filter = options.filter || state.filter || 'all';

        this.activeTab = key;

        // P0-4: Request Deduplication
        // If not forced: ignore duplicate activations if already LOADING or READY with identical criteria
        if (!options.force) {
            if (state.status === 'LOADING' && state.filter === filter) {
                console.debug('[TabCoordinator] Deduplicated click: ' + key + ' is currently LOADING');
                return;
            }
            if (state.status === 'READY' && state.filter === filter) {
                console.debug('[TabCoordinator] Deduplicated click: ' + key + ' is already READY');
                return;
            }
        }

        // Delegate to active drawer's blade loader if available and not called from loader
        if (!options.fromLoader) {
            var isDirect = this.instance.context.clientType === 'direct';
            if (isDirect && typeof window.loadDirectTabContent === 'function') {
                window.loadDirectTabContent(key, options.force || false, filter);
                return;
            } else if (!isDirect && typeof window.loadInternalTabContent === 'function') {
                window.loadInternalTabContent(key, options.force || false, filter);
                return;
            }
        }

        var adapter = window.ClientDrawerTabs ? (window.ClientDrawerTabs[key] || window.ClientDrawerTabs[tabName]) : null;
        if (!adapter || typeof adapter.load !== 'function') {
            console.warn('[TabCoordinator] No adapter found for tab: ' + key);
            return;
        }

        adapter.load(this.instance, {
            filter: filter,
            search: options.search || '',
            force: options.force || false
        });
    };

    /**
     * Mandatory Sub-Tab Dispatch Invariant:
     * All sub-tab filter actions MUST dispatch through this method!
     * @param {string} tabName
     * @param {string} filterValue
     */
    ClientDrawerTabCoordinator.prototype.setFilter = function(tabName, filterValue) {
        if (this.instance.isDestroyed()) return;

        var key = this.normalizeTabName(tabName);
        var state = this.getState(key);

        // If filter is unchanged and already READY, do not refetch
        if (state.filter === filterValue && state.status === 'READY') {
            console.debug('[TabCoordinator] Sub-tab filter unchanged: ' + filterValue);
            return;
        }

        state.filter = filterValue;
        this.activateTab(key, { filter: filterValue, force: true });
    };

    /**
     * Retry loading a failed tab
     * @param {string} tabName
     */
    ClientDrawerTabCoordinator.prototype.retryTab = function(tabName) {
        if (this.instance.isDestroyed()) return;
        var key = this.normalizeTabName(tabName);
        this.activateTab(key, { force: true });
    };

    /**
     * Clean teardown of TabCoordinator
     */
    ClientDrawerTabCoordinator.prototype.destroy = function() {
        this.instance = null;
        this.state = null;
    };

    window.ClientDrawerTabCoordinator = ClientDrawerTabCoordinator;

})(window);
