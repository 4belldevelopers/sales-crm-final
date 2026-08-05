/**
 * _tabSwap.js — View Transitions API + CSS shimmer skeleton for Apptracker.
 *
 * Intercepts Apptracker tab switches and wraps the content swap in
 * document.startViewTransition() so the browser cross-fades the old and new
 * panels at 60fps with no blank-screen flicker. Falls back to an instant swap
 * when the View Transitions API is unsupported (all Firefox, older Safari).
 *
 * Also exposes AppTracker.skeleton so AJAX callers can show/hide shimmer rows
 * while data is in flight.
 *
 * API:
 *   AppTracker.tabSwap.swap(containerSel, renderFn)
 *       — wraps renderFn() in a transition; renderFn must be synchronous.
 *
 *   AppTracker.skeleton.show(containerSel)
 *       — adds .at-loading to the container (shows shimmer, hides content)
 *   AppTracker.skeleton.hide(containerSel)
 *       — removes .at-loading
 *   AppTracker.skeleton.build(rowCount)
 *       — returns HTML for `rowCount` shimmer rows to inject into .at-skeleton-wrapper
 *
 * Integration example:
 *   AppTracker.skeleton.show('#tab-body');
 *   AppTracker.tabSwap.swap('#tab-body', function () {
 *       $('#tab-body').html(responseHtml);
 *   });
 *   AppTracker.skeleton.hide('#tab-body');
 */
(function ($, window, document) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    /* ── Skeleton API ───────────────────────────────────────────────────── */
    AppTracker.skeleton = {
        show: function (sel) {
            $(sel).addClass('at-loading');
        },
        hide: function (sel) {
            $(sel).removeClass('at-loading');
        },
        /**
         * Builds HTML for shimmer placeholder rows.
         * Inject the returned string into a .at-skeleton-wrapper element.
         */
        build: function (rowCount) {
            rowCount = rowCount || 8;
            var rows  = '';
            for (var i = 0; i < rowCount; i++) {
                rows +=
                    '<div class="at-skeleton-row">' +
                        '<span class="at-skeleton-line at-skeleton-avatar"></span>' +
                        '<div class="at-skeleton-text-group">' +
                            '<span class="at-skeleton-line at-skeleton-text at-w-80"></span>' +
                            '<span class="at-skeleton-line at-skeleton-text at-w-60"></span>' +
                        '</div>' +
                        '<span class="at-skeleton-line at-skeleton-badge"></span>' +
                    '</div>';
            }
            return rows;
        },
    };

    /* ── Tab-swap with View Transitions API ─────────────────────────────── */
    AppTracker.tabSwap = {
        /**
         * Wraps a DOM mutation in startViewTransition for a smooth cross-fade.
         *
         * @param {string}   containerSel  CSS selector for the panel being swapped.
         * @param {Function} renderFn      Synchronous function that mutates the DOM.
         */
        swap: function (containerSel, renderFn) {
            if (
                typeof document.startViewTransition === 'function' &&
                !window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ) {
                // Add the view-transition-name to the element so the CSS
                // ::view-transition-new/old rules in _skeleton.css animate it.
                $(containerSel).addClass('at-tab-panel');
                document.startViewTransition(function () {
                    renderFn();
                });
            } else {
                // Fallback: instant swap (no animation support or user prefers none).
                renderFn();
            }
        },
    };

}(jQuery, window, document));
