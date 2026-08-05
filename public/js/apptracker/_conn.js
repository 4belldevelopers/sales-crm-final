/**
 * _conn.js — Network connection status monitor for AppTracker
 *
 * Watches navigator.onLine events and exposes a simple API so every
 * AJAX caller can check connectivity before firing, and so the UI can
 * show an offline banner to prevent confusing empty-state renders.
 *
 * API:
 *   AppTracker.conn.isOnline()          → boolean
 *   AppTracker.conn.onReconnect(fn)     → fn() called once on reconnect
 *   AppTracker.conn.offReconnect(fn)    → unregister a reconnect listener
 *
 * The banner element must have the attribute [data-at-offline-banner] to be
 * shown/hidden automatically. Example:
 *   <div data-at-offline-banner style="display:none;">
 *     You are offline — changes will resume when your connection returns.
 *   </div>
 */
(function ($, window, navigator) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    var reconnectListeners = [];
    var bannerSel = '[data-at-offline-banner]';

    function showBanner() {
        $(bannerSel).show();
    }

    function hideBanner() {
        $(bannerSel).hide();
    }

    function onOnline() {
        hideBanner();
        // Fire each reconnect listener once and clear the queue
        var cbs = reconnectListeners.slice();
        reconnectListeners = [];
        cbs.forEach(function (fn) { try { fn(); } catch (e) {} });
    }

    function onOffline() {
        showBanner();
    }

    $(function () {
        // Reflect initial state
        if (!navigator.onLine) {
            showBanner();
        }
    });

    window.addEventListener('online',  onOnline);
    window.addEventListener('offline', onOffline);

    AppTracker.conn = {
        isOnline: function () {
            return navigator.onLine !== false;
        },
        onReconnect: function (fn) {
            if (typeof fn === 'function') {
                reconnectListeners.push(fn);
            }
        },
        offReconnect: function (fn) {
            reconnectListeners = reconnectListeners.filter(function (cb) {
                return cb !== fn;
            });
        },
    };

}(jQuery, window, navigator));
