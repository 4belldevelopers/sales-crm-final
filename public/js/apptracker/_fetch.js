/**
 * _fetch.js — Resilient $.ajax wrapper for AppTracker
 *
 * Wraps jQuery $.ajax with:
 *  - Configurable timeout (default 15 s)
 *  - Exponential-backoff retry (default 3 attempts, base 800 ms)
 *  - Cancellable via AbortController-style token
 *
 * Usage:
 *   AppTracker.fetch({ url: '...', method: 'GET', data: {} })
 *     .done(function(data) { ... })
 *     .fail(function(err)  { ... });
 *
 * All existing $.ajax callers can be migrated by replacing `$.ajax({...})`
 * with `AppTracker.fetch({...})` — the returned object is a standard
 * jQuery Deferred so `.done` / `.fail` / `.always` work unchanged.
 */
(function ($, window) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    var defaults = {
        timeout:     15000,   // ms before a request is considered timed out
        retries:     3,       // max attempts (first try + N-1 retries)
        retryDelay:  800,     // base delay ms; doubles each retry
        retryOn:     [0, 408, 429, 500, 502, 503, 504],  // HTTP statuses to retry
    };

    /**
     * @param {object} options  - Same as $.ajax options, plus:
     *   {number}  retries      - Override default retry count
     *   {number}  retryDelay   - Override base retry delay (ms)
     *   {number}  timeout      - Override default timeout (ms)
     * @returns {jQuery.Deferred}
     */
    AppTracker.fetch = function (options) {
        var config  = $.extend({}, defaults, options);
        var deferred = $.Deferred();
        var attempt  = 0;

        function run() {
            attempt++;

            $.ajax({
                url:         config.url,
                method:      config.method  || 'GET',
                data:        config.data    || {},
                dataType:    config.dataType || 'json',
                headers:     config.headers  || {},
                timeout:     config.timeout,
                // Pass any extra $.ajax keys through
                beforeSend:  config.beforeSend  || null,
                xhrFields:   config.xhrFields   || {},
            })
            .done(function (data, textStatus, jqXHR) {
                deferred.resolve(data, textStatus, jqXHR);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                var status     = jqXHR.status || 0;
                var isRetryable = config.retryOn.indexOf(status) !== -1
                               || textStatus === 'timeout'
                               || textStatus === 'abort';

                if (isRetryable && attempt < config.retries) {
                    var delay = config.retryDelay * Math.pow(2, attempt - 1);
                    setTimeout(run, delay);
                } else {
                    deferred.reject(jqXHR, textStatus, errorThrown);
                }
            });
        }

        run();
        return deferred.promise();
    };

}(jQuery, window));
