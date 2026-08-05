/**
 * _echo.js — Resilient Laravel Echo / Reverb WebSocket client for Apptracker.
 *
 * Listens on the public pipeline.{jobId} channel for StageMoved events and
 * triggers counter badge refreshes in <50ms. Includes:
 *   - Automatic reconnect with exponential back-off (max 60s interval)
 *   - HTTP polling fallback (every 60s) when Reverb is unreachable
 *   - Integration with AppTracker.visibility so polling pauses on hidden tabs
 *
 * Usage (call once per Apptracker page, after Echo is initialised):
 *   AppTracker.echo.init({ jobId: 123, onStageMove: function(payload) { … } });
 *   AppTracker.echo.destroy(); // cleanup on SPA page unload
 *
 * onStageMove(payload) receives { jobId, applicationId, newStatus }.
 */
(function ($, window) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    var _jobId        = null;
    var _onStageMove  = null;
    var _channel      = null;
    var _pollTimer    = null;
    var _wsConnected  = false;
    var _retryDelay   = 3000;   // ms, doubles on each failure up to 60 000
    var _maxDelay     = 60000;
    var _pollInterval = 60000;  // ms — HTTP fallback polling interval

    /* ── Internal helpers ──────────────────────────────────────────────── */

    function fireCallback(payload) {
        if (typeof _onStageMove === 'function') {
            try { _onStageMove(payload); } catch (e) {}
        }
    }

    function startPolling() {
        if (_pollTimer) { return; }
        _pollTimer = setInterval(function () {
            if (!AppTracker.visibility.isVisible()) { return; }
            // Fire with a synthetic payload: no specific move, just "refresh counters".
            fireCallback({ jobId: _jobId, applicationId: null, newStatus: null });
        }, _pollInterval);
    }

    function stopPolling() {
        if (_pollTimer) {
            clearInterval(_pollTimer);
            _pollTimer = null;
        }
    }

    function subscribeChannel() {
        if (!window.Echo || !_jobId) { return; }
        try {
            _channel = window.Echo.channel('pipeline.' + _jobId)
                .listen('.StageMoved', function (payload) {
                    _wsConnected = true;
                    _retryDelay  = 3000;  // reset back-off on successful event
                    stopPolling();        // WS working — no need to poll
                    fireCallback(payload);
                });
        } catch (e) {
            onWsError();
        }
    }

    function onWsError() {
        _wsConnected = false;
        startPolling();  // fall back to HTTP polling until WS reconnects
        setTimeout(function () {
            // Attempt to re-subscribe with back-off
            if (_channel) {
                try { window.Echo.leaveChannel('pipeline.' + _jobId); } catch (e) {}
                _channel = null;
            }
            subscribeChannel();
            _retryDelay = Math.min(_retryDelay * 2, _maxDelay);
        }, _retryDelay);
    }

    /* ── Public API ────────────────────────────────────────────────────── */

    AppTracker.echo = {
        /**
         * Initialise the Echo subscription for a job's pipeline channel.
         *
         * @param {Object} opts
         * @param {number}   opts.jobId        The job whose pipeline to watch.
         * @param {Function} opts.onStageMove  Called with { jobId, applicationId, newStatus }.
         */
        init: function (opts) {
            _jobId       = opts.jobId;
            _onStageMove = opts.onStageMove;

            if (!window.Echo) {
                // Echo not loaded — start polling only.
                startPolling();
                return;
            }

            // Wire Echo connection-state hooks if the connector exposes them.
            var connector = window.Echo.connector;
            if (connector && connector.pusher) {
                connector.pusher.connection.bind('disconnected', onWsError);
                connector.pusher.connection.bind('connected', function () {
                    _wsConnected = true;
                    stopPolling();
                });
            }

            subscribeChannel();

            // Visibility: pause polling when tab is hidden, re-fetch on show.
            if (AppTracker.visibility) {
                AppTracker.visibility.onShow(function () {
                    if (!_wsConnected) {
                        fireCallback({ jobId: _jobId, applicationId: null, newStatus: null });
                    }
                });
            }
        },

        destroy: function () {
            stopPolling();
            if (_channel && window.Echo) {
                try { window.Echo.leaveChannel('pipeline.' + _jobId); } catch (e) {}
            }
            _channel     = null;
            _jobId       = null;
            _onStageMove = null;
            _wsConnected = false;
        },
    };

}(jQuery, window));
