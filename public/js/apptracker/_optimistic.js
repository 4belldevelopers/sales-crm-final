/**
 * _optimistic.js — 16ms Optimistic UI updates for Apptracker stage moves.
 *
 * When a recruiter moves a candidate to a new pipeline stage the visual badge
 * on the row updates immediately (on the next paint frame, ~16ms) without
 * waiting for the server round-trip or Reverb WS confirmation. If the AJAX
 * call fails the change is automatically rolled back to its previous state.
 *
 * API:
 *   AppTracker.optimistic.move(opts)
 *
 *   opts = {
 *     rowSel      : CSS selector for the candidate row (string)
 *     badgeSel    : CSS selector for the stage badge within the row (string)
 *     newLabel    : Display text for the new stage
 *     newClass    : CSS class(es) for the new stage badge
 *     oldLabel    : Display text of the current stage (for rollback)
 *     oldClass    : CSS class(es) of the current stage badge (for rollback)
 *     request     : Function that returns a jQuery $.ajax promise
 *     onSuccess   : Optional callback(response) after confirmed save
 *     onRollback  : Optional callback() after a failed save + rollback
 *   }
 *
 * Integration (call from the stage-move click handler):
 *   AppTracker.optimistic.move({
 *     rowSel   : '#row-' + applicationId,
 *     badgeSel : '.at-stage-badge',
 *     newLabel : 'Screening',
 *     newClass : 'badge-light-info',
 *     oldLabel : 'Sourcing',
 *     oldClass : 'badge-light-primary',
 *     request  : function () {
 *         return $.ajax({ url: moveUrl, type: 'POST', data: formData });
 *     },
 *   });
 */
(function ($, window, requestAnimationFrame) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    AppTracker.optimistic = {
        /**
         * Perform an optimistic stage-move update.
         */
        move: function (opts) {
            var $row   = $(opts.rowSel);
            var $badge = $row.find(opts.badgeSel);

            if (!$row.length || !$badge.length) {
                // Row not in DOM — skip optimistic, just fire request.
                opts.request().then(opts.onSuccess || $.noop, opts.onRollback || $.noop);
                return;
            }

            // 1. Capture current state for rollback.
            var previousLabel = opts.oldLabel || $badge.text().trim();
            var previousClass = opts.oldClass || '';

            // 2. Visual update on the next paint frame (~16ms).
            requestAnimationFrame(function () {
                $badge
                    .removeClass(previousClass)
                    .addClass(opts.newClass)
                    .text(opts.newLabel);

                // Mark the row as "pending" so it's visually dimmed during the wait.
                $row.addClass('at-optimistic-pending');
            });

            // 3. Fire the AJAX request.
            opts.request()
                .done(function (response) {
                    // Confirmed — remove the pending marker.
                    requestAnimationFrame(function () {
                        $row.removeClass('at-optimistic-pending');
                    });
                    if (typeof opts.onSuccess === 'function') {
                        opts.onSuccess(response);
                    }
                })
                .fail(function () {
                    // Rollback the optimistic update.
                    requestAnimationFrame(function () {
                        $badge
                            .removeClass(opts.newClass)
                            .addClass(previousClass)
                            .text(previousLabel);
                        $row.removeClass('at-optimistic-pending');
                        $row.addClass('at-optimistic-failed');
                        // Remove the failure indicator after 2s.
                        setTimeout(function () {
                            $row.removeClass('at-optimistic-failed');
                        }, 2000);
                    });
                    if (typeof opts.onRollback === 'function') {
                        opts.onRollback();
                    }
                });
        },
    };

}(jQuery, window, window.requestAnimationFrame || function (fn) { setTimeout(fn, 16); }));
