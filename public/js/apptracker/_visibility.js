/**
 * _visibility.js — Page Visibility API handler for AppTracker
 *
 * Pauses periodic counter refreshes (setInterval / setTimeout chains)
 * when the browser tab is hidden, and resumes + immediately re-fetches
 * when the tab becomes visible again. This prevents stale counters on
 * returning to the tab after a long absence, and avoids wasted requests
 * while the user is away.
 *
 * API:
 *   AppTracker.visibility.onHide(fn)     → register a pause callback
 *   AppTracker.visibility.onShow(fn)     → register a resume callback
 *   AppTracker.visibility.isVisible()    → boolean
 *
 * Integration (call from the blade after loadcounter is defined):
 *   AppTracker.visibility.onShow(function() {
 *       counter();   // your existing counter refresh function
 *   });
 */
(function ($, window, document) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    var hideListeners = [];
    var showListeners = [];

    function fire(list) {
        list.forEach(function (fn) { try { fn(); } catch (e) {} });
    }

    var hidden, visibilityChange;
    if (typeof document.hidden !== 'undefined') {
        hidden           = 'hidden';
        visibilityChange = 'visibilitychange';
    } else if (typeof document.msHidden !== 'undefined') {
        hidden           = 'msHidden';
        visibilityChange = 'msvisibilitychange';
    } else if (typeof document.webkitHidden !== 'undefined') {
        hidden           = 'webkitHidden';
        visibilityChange = 'webkitvisibilitychange';
    }

    if (visibilityChange) {
        document.addEventListener(visibilityChange, function () {
            if (document[hidden]) {
                fire(hideListeners);
            } else {
                fire(showListeners);
            }
        });
    }

    AppTracker.visibility = {
        isVisible: function () {
            return !document[hidden];
        },
        onHide: function (fn) {
            if (typeof fn === 'function') { hideListeners.push(fn); }
        },
        onShow: function (fn) {
            if (typeof fn === 'function') { showListeners.push(fn); }
        },
        offHide: function (fn) {
            hideListeners = hideListeners.filter(function (cb) { return cb !== fn; });
        },
        offShow: function (fn) {
            showListeners = showListeners.filter(function (cb) { return cb !== fn; });
        },
    };

}(jQuery, window, document));
