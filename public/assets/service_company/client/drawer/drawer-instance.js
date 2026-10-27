/**
 * JERO Service Company Drawer - Scoped Drawer Instance
 * Architecture: Ephemeral Scoped Instance Model with Fail-Closed Lifecycle Guarantee
 * 
 * Invariants:
 * 1. Each drawer opening creates an isolated ClientDrawerInstance with a unique UUID v4
 * 2. window.__activeClientDrawer is strictly an ephemeral pointer, never a state store
 * 3. Lifecycle: UNINITIALIZED -> LOADING -> READY -> DESTROYING -> DESTROYED
 * 4. P0-1: Once DESTROYED, all operations fail closed (requests, transitions, DOM writes)
 * 5. P0-5: 100% leak-free teardown (aborts all requests, unbinds namespaced listeners)
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    /**
     * Generate secure UUID v4
     * @returns {string}
     */
    function generateUUID() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0;
            var v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * @param {Object} context
     * @param {string|number} context.canonicalClientId
     * @param {string} context.clientType - 'internal' | 'direct'
     * @param {string|number} [context.companyId]
     * @param {HTMLElement|string} [context.container]
     */
    function ClientDrawerInstance(context) {
        if (!context || !context.canonicalClientId) {
            throw new Error('[ClientDrawerInstance] Requires context with canonicalClientId');
        }

        this.drawerInstanceId = generateUUID();
        this._lifecycleState = 'UNINITIALIZED';

        this.context = {
            entityType: 'client',
            canonicalClientId: String(context.canonicalClientId),
            clientType: String(context.clientType || 'internal').toLowerCase(),
            companyId: context.companyId || (window.CURRENT_COMPANY_ID || 0),
            openedAt: Date.now()
        };

        this.containerSelector = context.container || (this.context.clientType === 'direct' ? '#kt_activitiesDirectClient' : '#kt_activitiesClient');

        // Initialize Telemetry
        this.telemetry = this._createTelemetry();

        // Initialize Core Sub-Managers
        this.requestManager = new window.ClientDrawerRequestManager(this);
        this.renderer = new window.ClientDrawerUIRenderer(this);
        this.coordinator = new window.ClientDrawerTabCoordinator(this);

        // Bind global active pointer
        window.__activeClientDrawer = this;

        // Stamp authoritative identity on root DOM element
        this._stampDOMIdentity();

        this._lifecycleState = 'READY';

        console.info('[ClientDrawerInstance] Initialized drawer instance: ' + this.drawerInstanceId + ' for client: ' + this.context.canonicalClientId + ' (' + this.context.clientType + ')');
    }

    /**
     * Stamp authoritative metadata onto drawer DOM container
     */
    ClientDrawerInstance.prototype._stampDOMIdentity = function() {
        var $el = $(this.containerSelector);
        if ($el.length) {
            $el.attr('data-drawer-instance-id', this.drawerInstanceId);
            $el.attr('data-canonical-client-id', this.context.canonicalClientId);
            $el.attr('data-client-type', this.context.clientType);
        }
    };

    /**
     * Get root jQuery element of drawer
     * @returns {jQuery}
     */
    ClientDrawerInstance.prototype.getRoot = function() {
        return $(this.containerSelector);
    };

    /**
     * Check if instance is destroyed or in destroying state (P0-1 Fail-Closed)
     * @returns {boolean}
     */
    ClientDrawerInstance.prototype.isDestroyed = function() {
        return this._lifecycleState === 'DESTROYED' || this._lifecycleState === 'DESTROYING';
    };

    /**
     * Check if instance is currently the active open drawer
     * @returns {boolean}
     */
    ClientDrawerInstance.prototype.isActive = function() {
        return !this.isDestroyed() && window.__activeClientDrawer === this;
    };

    /**
     * Telemetry Factory with controlled failureReason enum
     */
    ClientDrawerInstance.prototype._createTelemetry = function() {
        var self = this;
        return {
            logSuccess: function(tab, reqContext, durationMs, httpStatus) {
                console.info('[CLIENT_DRAWER_TAB]', {
                    client: self.context.canonicalClientId,
                    type: self.context.clientType,
                    drawer: self.drawerInstanceId,
                    tab: tab,
                    subTab: reqContext.filter || 'default',
                    requestId: reqContext.requestId,
                    durationMs: durationMs,
                    apiStatus: httpStatus,
                    status: 'READY'
                });
            },
            logFailure: function(reason, error, reqContext, durationMs, httpStatus) {
                var tab = reqContext ? reqContext.tab : 'drawer';
                var reqId = reqContext ? reqContext.requestId : null;
                console.warn('[CLIENT_DRAWER_TAB]', {
                    client: self.context.canonicalClientId,
                    type: self.context.clientType,
                    drawer: self.drawerInstanceId,
                    tab: tab,
                    subTab: reqContext ? reqContext.filter : null,
                    requestId: reqId,
                    durationMs: durationMs || 0,
                    apiStatus: httpStatus || null,
                    status: 'ERROR',
                    failureReason: reason,
                    error: error
                });
            },
            logEvent: function(tab, status, payload, meta) {
                console.debug('[CLIENT_DRAWER_EVENT]', {
                    client: self.context.canonicalClientId,
                    drawer: self.drawerInstanceId,
                    tab: tab,
                    status: status,
                    payload: payload,
                    meta: meta
                });
            }
        };
    };

    /**
     * Attach Namespaced Event Listener scoped to this instance
     * Automatically unbinds on destroy() to guarantee 0 event leaks
     * @param {string} event
     * @param {string} selector
     * @param {Function} handler
     */
    ClientDrawerInstance.prototype.on = function(event, selector, handler) {
        if (this.isDestroyed()) return;
        var ns = '.drawerInstance_' + this.drawerInstanceId.replace(/[^a-zA-Z0-9]/g, '_');
        var namespacedEvent = event.split(' ').map(function(e) { return e + ns; }).join(' ');
        var self = this;

        $(document).on(namespacedEvent, selector, function(e) {
            if (self.isDestroyed() || !self.isActive()) return;
            return handler.call(this, e, self);
        });
    };

    /**
     * Complete Teardown & Destruction (P0-1 & P0-5 Guarantee)
     */
    ClientDrawerInstance.prototype.destroy = function() {
        if (this.isDestroyed()) return;
        this._lifecycleState = 'DESTROYING';

        console.info('[ClientDrawerInstance] Destroying instance: ' + this.drawerInstanceId + ' for client: ' + this.context.canonicalClientId);

        // 1. Unbind all instance-namespaced event listeners
        var ns = '.drawerInstance_' + this.drawerInstanceId.replace(/[^a-zA-Z0-9]/g, '_');
        $(document).off(ns);

        // 2. Abort all in-flight network requests
        if (this.requestManager) {
            this.requestManager.destroy();
            this.requestManager = null;
        }

        // 3. Destroy TabCoordinator
        if (this.coordinator) {
            this.coordinator.destroy();
            this.coordinator = null;
        }

        // 4. Destroy UIRenderer
        if (this.renderer) {
            this.renderer.destroy();
            this.renderer = null;
        }

        // 5. Clear DOM identity stamp
        var $el = $(this.containerSelector);
        if ($el.length) {
            $el.removeAttr('data-drawer-instance-id');
            $el.removeAttr('data-canonical-client-id');
            $el.removeAttr('data-client-type');
        }

        // 6. Reset global pointer if pointing to this instance
        if (window.__activeClientDrawer === this) {
            window.__activeClientDrawer = null;
        }

        this._lifecycleState = 'DESTROYED';
    };

    window.ClientDrawerInstance = ClientDrawerInstance;

})(window);
