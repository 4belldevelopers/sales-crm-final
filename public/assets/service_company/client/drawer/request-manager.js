/**
 * JERO Service Company Drawer - Request Manager
 * Architecture: 4-Layer Defense-in-Depth Stale-Response Protection & Request Sequencing
 * 
 * Invariants:
 * 1. Monotonically increasing sequence counters per tab
 * 2. Instant cancellation of prior in-flight requests on tab switch or filter change
 * 3. Atomic 4-layer validation (Instance UUID + Client ID + Client Type + Sequence Number)
 * 4. Fail-closed guarantee when parent instance is destroyed
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    /**
     * @param {ClientDrawerInstance} instance
     */
    function ClientDrawerRequestManager(instance) {
        if (!instance) {
            throw new Error('[RequestManager] Requires parent ClientDrawerInstance');
        }
        this.instance = instance;
        this.sequenceMap = new Map(); // tabName -> number
        this.activeXhrMap = new Map(); // tabName -> jqXHR | AbortController
    }

    /**
     * Get or initialize current sequence number for a tab
     * @param {string} tab
     * @returns {number}
     */
    ClientDrawerRequestManager.prototype.getLatestSequence = function(tab) {
        return this.sequenceMap.get(tab) || 0;
    };

    /**
     * Increment sequence number for a tab
     * @param {string} tab
     * @returns {number}
     */
    ClientDrawerRequestManager.prototype.nextSequence = function(tab) {
        var next = (this.sequenceMap.get(tab) || 0) + 1;
        this.sequenceMap.set(tab, next);
        return next;
    };

    /**
     * Abort in-flight request for a specific tab
     * @param {string} tab
     */
    ClientDrawerRequestManager.prototype.abortTab = function(tab) {
        var active = this.activeXhrMap.get(tab);
        if (active) {
            try {
                if (typeof active.abort === 'function') {
                    active.abort();
                } else if (active instanceof AbortController) {
                    active.abort();
                }
            } catch (e) {
                console.warn('[RequestManager] Error aborting tab ' + tab, e);
            }
            this.activeXhrMap.delete(tab);
            this.instance.telemetry.logEvent(tab, 'ABORTED', null, {
                failureReason: 'ABORTED'
            });
        }
    };

    /**
     * Abort all in-flight requests across all tabs
     */
    ClientDrawerRequestManager.prototype.abortAll = function() {
        var self = this;
        this.activeXhrMap.forEach(function(active, tab) {
            try {
                if (typeof active.abort === 'function') {
                    active.abort();
                } else if (active instanceof AbortController) {
                    active.abort();
                }
            } catch (e) {
                console.warn('[RequestManager] Error aborting tab ' + tab, e);
            }
        });
        this.activeXhrMap.clear();
    };

    /**
     * Dispatch an asynchronous request with 4-layer protection
     * @param {string} tab
     * @param {Object} config - jQuery ajax config object with extra callbacks
     * @returns {jqXHR|null}
     */
    ClientDrawerRequestManager.prototype.dispatch = function(tab, config) {
        var self = this;

        // Fail-closed guard
        if (this.instance.isDestroyed()) {
            console.warn('[RequestManager] Blocked dispatch on destroyed instance for tab: ' + tab);
            return null;
        }

        // 1. Abort prior request for this tab
        this.abortTab(tab);

        // 2. Generate monotonically increasing sequence ID
        var requestId = this.nextSequence(tab);
        var drawerInstanceId = this.instance.drawerInstanceId;
        var canonicalClientId = String(this.instance.context.canonicalClientId);
        var clientType = String(this.instance.context.clientType);
        var startTime = performance.now();

        // Snapshot immutable request context
        var requestSnapshot = {
            tab: tab,
            requestId: requestId,
            drawerInstanceId: drawerInstanceId,
            canonicalClientId: canonicalClientId,
            clientType: clientType,
            filter: config.filter || 'default',
            startTime: startTime
        };

        var userSuccess = config.success;
        var userError = config.error;
        var userComplete = config.complete;

        var ajaxOptions = $.extend({}, config, {
            success: function(data, textStatus, xhr) {
                var durationMs = Math.round(performance.now() - startTime);

                // Layer 0: Fail-closed check
                if (self.instance.isDestroyed()) {
                    self.instance.telemetry.logFailure('STALE_INSTANCE', 'Instance destroyed prior to response arrival', requestSnapshot, durationMs);
                    return;
                }

                // Layer 1: Drawer Instance UUID match
                if (self.instance.drawerInstanceId !== drawerInstanceId) {
                    self.instance.telemetry.logFailure('STALE_INSTANCE', 'Response drawerInstanceId mismatch', requestSnapshot, durationMs);
                    return;
                }

                // Layer 2: Canonical Client ID match
                if (String(self.instance.context.canonicalClientId) !== canonicalClientId) {
                    self.instance.telemetry.logFailure('STALE_CLIENT', 'Response canonicalClientId mismatch', requestSnapshot, durationMs);
                    return;
                }

                // Layer 3: Client Type match ('internal' vs 'direct')
                if (String(self.instance.context.clientType) !== clientType) {
                    self.instance.telemetry.logFailure('STALE_CLIENT', 'Response clientType mismatch', requestSnapshot, durationMs);
                    return;
                }

                // Layer 4: Sequence number match (Last-Write-Wins)
                var currentSeq = self.getLatestSequence(tab);
                if (currentSeq !== requestId) {
                    self.instance.telemetry.logFailure('STALE_SEQUENCE', 'Superseded sequence ID: req=' + requestId + ' vs current=' + currentSeq, requestSnapshot, durationMs);
                    return;
                }

                // All 4 layers verified: Response is Authoritative!
                self.instance.telemetry.logSuccess(tab, requestSnapshot, durationMs, xhr ? xhr.status : 200);

                if (typeof userSuccess === 'function') {
                    userSuccess.call(this, data, textStatus, xhr, requestSnapshot);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                var durationMs = Math.round(performance.now() - startTime);

                if (self.instance.isDestroyed()) {
                    return; // Ignore on destroyed instance
                }

                // If user aborted deliberately, log ABORTED without triggering UI error
                if (textStatus === 'abort' || (xhr && xhr.statusText === 'abort')) {
                    self.instance.telemetry.logFailure('ABORTED', 'Request deliberately aborted', requestSnapshot, durationMs);
                    return;
                }

                // Sequence check: Ignore error if a newer request was already dispatched
                if (self.getLatestSequence(tab) !== requestId) {
                    return;
                }

                var failureReason = 'API_ERROR';
                if (textStatus === 'timeout') {
                    failureReason = 'TIMEOUT';
                } else if (!window.navigator.onLine) {
                    failureReason = 'NETWORK_OFFLINE';
                }

                self.instance.telemetry.logFailure(failureReason, errorThrown || textStatus, requestSnapshot, durationMs, xhr ? xhr.status : null);

                if (typeof userError === 'function') {
                    userError.call(this, xhr, textStatus, errorThrown, requestSnapshot, failureReason);
                }
            },
            complete: function(xhr, textStatus) {
                // Clear active reference if this request is still recorded
                if (self.activeXhrMap.get(tab) === xhr) {
                    self.activeXhrMap.delete(tab);
                }
                if (typeof userComplete === 'function') {
                    userComplete.call(this, xhr, textStatus);
                }
            }
        });

        var xhr = $.ajax(ajaxOptions);
        this.activeXhrMap.set(tab, xhr);
        return xhr;
    };

    /**
     * Clean teardown of RequestManager
     */
    ClientDrawerRequestManager.prototype.destroy = function() {
        this.abortAll();
        this.sequenceMap.clear();
        this.instance = null;
    };

    window.ClientDrawerRequestManager = ClientDrawerRequestManager;

})(window);
