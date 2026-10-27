/**
 * JERO Service Company Drawer - UI State Renderer
 * Architecture: Strict 4-State Visual Rendering & Mandatory Pre-Mutation DOM Ownership Assertions
 * 
 * Rules:
 * 1. Zero blank panels, zero infinite spinners.
 * 2. Every tab container renders exactly: SKELETON, READY, EMPTY, or ERROR.
 * 3. Every ERROR renders a non-intrusive Correlation ID (Error ID: JD-XXXX) with a [Retry] button.
 * 4. Mandatory assertOwnership() precondition before ANY DOM mutation.
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    function ClientDrawerUIRenderer(instance) {
        if (!instance) {
            throw new Error('[UIRenderer] Requires parent ClientDrawerInstance');
        }
        this.instance = instance;
    }

    /**
     * Generate unique technical Correlation ID for production traceability
     * Example: JD-7F82A
     */
    ClientDrawerUIRenderer.prototype.generateCorrelationId = function() {
        var randomSegment = Math.random().toString(36).substring(2, 7).toUpperCase();
        return 'JD-' + randomSegment;
    };

    /**
     * Mandatory Pre-Mutation DOM Ownership Guard
     * Validates that the active instance owns the container before touching the DOM
     * @param {HTMLElement|jQuery} container
     * @returns {boolean}
     */
    ClientDrawerUIRenderer.prototype.assertOwnership = function(container) {
        var $el = $(container);
        if (!$el.length) {
            return false;
        }

        // 1. Instance alive check
        if (!this.instance || this.instance.isDestroyed() || !this.instance.isActive()) {
            if (this.instance && this.instance.telemetry) {
                this.instance.telemetry.logFailure('DOM_OWNERSHIP_FAILURE', 'Instance is destroyed or inactive');
            }
            return false;
        }

        // 2. Global pointer check
        if (window.__activeClientDrawer !== this.instance) {
            if (this.instance.telemetry) {
                this.instance.telemetry.logFailure('DOM_OWNERSHIP_FAILURE', 'Instance is not the active window pointer');
            }
            return false;
        }

        // 3. Container drawerInstanceId check (check container or nearest drawer ancestor)
        var drawerRoot = ($el.attr('data-drawer-instance-id') || $el.attr('data-canonical-client-id'))
            ? $el
            : $el.closest('[data-drawer-instance-id]');

        if (drawerRoot.length) {
            var containerInstId = drawerRoot.attr('data-drawer-instance-id') || $el.attr('data-drawer-instance-id');
            if (containerInstId && containerInstId !== this.instance.drawerInstanceId) {
                if (this.instance.telemetry) {
                    this.instance.telemetry.logFailure('DOM_OWNERSHIP_FAILURE', 'Container instance mismatch: ' + containerInstId + ' !== ' + this.instance.drawerInstanceId);
                }
                return false;
            }

            var containerClientId = drawerRoot.attr('data-canonical-client-id') || $el.attr('data-canonical-client-id');
            if (containerClientId && String(containerClientId) !== String(this.instance.context.canonicalClientId)) {
                if (this.instance.telemetry) {
                    this.instance.telemetry.logFailure('DOM_OWNERSHIP_FAILURE', 'Container client mismatch: ' + containerClientId + ' !== ' + this.instance.context.canonicalClientId);
                }
                return false;
            }
        }

        return true;
    };

    /**
     * Render Animated Metronic Skeleton Loader (<50ms perceived feedback)
     * @param {HTMLElement|jQuery} container
     * @param {string} tabName
     */
    ClientDrawerUIRenderer.prototype.renderSkeleton = function(container, tabName) {
        if (!this.assertOwnership(container)) return;
        var $el = $(container);

        var skeletonHtml = `
            <div class="cd-tab-skeleton-loader p-4" style="animation: cdShimmer 1.5s infinite linear;">
                <div class="card bg-white border border-slate-200 rounded-3 p-4 mb-3 shadow-xs">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="skeleton-box rounded-circle bg-light" style="width: 44px; height: 44px; background: #e2e8f0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-box rounded mb-2" style="width: 40%; height: 16px; background: #e2e8f0;"></div>
                            <div class="skeleton-box rounded" style="width: 25%; height: 12px; background: #f1f5f9;"></div>
                        </div>
                        <div class="skeleton-box rounded" style="width: 80px; height: 24px; background: #f1f5f9;"></div>
                    </div>
                    <div class="skeleton-box rounded mb-2" style="width: 100%; height: 12px; background: #f8fafc;"></div>
                    <div class="skeleton-box rounded" style="width: 75%; height: 12px; background: #f8fafc;"></div>
                </div>
                <div class="card bg-white border border-slate-200 rounded-3 p-4 mb-3 shadow-xs">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="skeleton-box rounded-circle bg-light" style="width: 44px; height: 44px; background: #e2e8f0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-box rounded mb-2" style="width: 50%; height: 16px; background: #e2e8f0;"></div>
                            <div class="skeleton-box rounded" style="width: 30%; height: 12px; background: #f1f5f9;"></div>
                        </div>
                        <div class="skeleton-box rounded" style="width: 80px; height: 24px; background: #f1f5f9;"></div>
                    </div>
                    <div class="skeleton-box rounded mb-2" style="width: 90%; height: 12px; background: #f8fafc;"></div>
                    <div class="skeleton-box rounded" style="width: 60%; height: 12px; background: #f8fafc;"></div>
                </div>
            </div>
        `;

        $el.html(skeletonHtml);
    };

    /**
     * Render Ready State with authoritative HTML content
     * @param {HTMLElement|jQuery} container
     * @param {string} htmlContent
     */
    ClientDrawerUIRenderer.prototype.renderReady = function(container, htmlContent) {
        if (!this.assertOwnership(container)) return;
        var $el = $(container);
        $el.html(htmlContent);
    };

    /**
     * Render Empty State UI (Never a blank canvas)
     * @param {HTMLElement|jQuery} container
     * @param {string} tabName
     * @param {string} message
     * @param {string} [subMessage]
     */
    ClientDrawerUIRenderer.prototype.renderEmpty = function(container, tabName, message, subMessage) {
        if (!this.assertOwnership(container)) return;
        var $el = $(container);

        var title = message || 'No records found';
        var desc = subMessage || 'There is currently no data available for this section.';

        var emptyHtml = `
            <div class="card border border-dashed border-slate-200 rounded-3 p-8 text-center my-4 bg-white shadow-xs" style="border-radius: 10px;">
                <div class="mb-3">
                    <i class="ki-outline ki-folder fs-3x text-muted opacity-50"></i>
                </div>
                <h5 class="text-gray-900 fw-bold mb-1 fs-6">${title}</h5>
                <p class="text-muted fs-8 mb-0">${desc}</p>
            </div>
        `;

        $el.html(emptyHtml);
    };

    /**
     * Render Error State UI with Non-Intrusive Correlation ID & [Retry]
     * @param {HTMLElement|jQuery} container
     * @param {string} tabName
     * @param {string} errorMessage
     * @param {string} correlationId
     * @param {Function} [retryCallback]
     */
    ClientDrawerUIRenderer.prototype.renderError = function(container, tabName, errorMessage, correlationId, retryCallback) {
        if (!this.assertOwnership(container)) return;
        var $el = $(container);
        var cid = correlationId || this.generateCorrelationId();
        var msg = errorMessage || 'Unable to load tab data. A network or server interruption occurred.';

        var errorHtml = `
            <div class="card border border-danger border-dashed rounded-3 p-6 text-center my-4 bg-light-danger shadow-xs" style="border-radius: 10px;">
                <div class="mb-2">
                    <i class="ki-outline ki-information-5 fs-2x text-danger"></i>
                </div>
                <h5 class="text-gray-900 fw-bold mb-1 fs-6">Unable to load ${tabName}</h5>
                <p class="text-muted fs-8 mb-3">${msg}</p>
                <div class="mb-4">
                    <span class="badge badge-light-secondary text-gray-700 fs-9 px-2 py-1 font-monospace" style="letter-spacing: 0.5px;">Error ID: ${cid}</span>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-primary px-4 py-2 fs-8 fw-semibold js-drawer-tab-retry" data-tab="${tabName}">
                        <i class="ki-outline ki-arrows-circle fs-7 me-1"></i> Retry
                    </button>
                </div>
            </div>
        `;

        $el.html(errorHtml);

        var self = this;
        $el.find('.js-drawer-tab-retry').off('click').on('click', function(e) {
            e.preventDefault();
            if (typeof retryCallback === 'function') {
                retryCallback();
            } else if (self.instance && self.instance.coordinator) {
                self.instance.coordinator.retryTab(tabName);
            }
        });
    };

    /**
     * Clean teardown of UIRenderer
     */
    ClientDrawerUIRenderer.prototype.destroy = function() {
        this.instance = null;
    };

    window.ClientDrawerUIRenderer = ClientDrawerUIRenderer;

})(window);
