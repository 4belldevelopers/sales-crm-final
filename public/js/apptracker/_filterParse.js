/**
 * _filterParse.js — Client-side Tagify filter parsing for Apptracker.
 *
 * Parses Tagify JSON chips locally in the browser instead of sending the raw
 * JSON to the server for parsing. This eliminates server-side JSON decode +
 * array_column() calls on hot AJAX paths and reduces request query-string size
 * by transmitting structured params rather than the full Tagify JSON blob.
 *
 * API:
 *   AppTracker.filterParse.skills(tagifyValue)
 *       → { all: ['php','laravel'], required: ['laravel'] }
 *       all      = all skills entered (checked + unchecked)
 *       required = only the starred/checked "mandatory" skills
 *
 *   AppTracker.filterParse.locations(tagifyValue)
 *       → ['mumbai', 'pune']   — flat array of location strings
 *
 *   AppTracker.filterParse.education(tagifyValue)
 *       → ['btech', 'mtech']  — flat array of qualification strings
 *
 *   AppTracker.filterParse.buildParams(opts)
 *       → plain object suitable for jQuery $.ajax `data:` — merges all parsed
 *         filter values together with the standard jobid / mstatus_id fields.
 *
 * Integration (replace inline cus_search pass-through in AJAX calls):
 *   var filters = AppTracker.filterParse.buildParams({
 *     jobid      : $('#jobid').val(),
 *     mstatus_id : $('#mstatus_id').val(),
 *     skillsSel  : '#kt_tagify_2',
 *     locSel     : '#kt_tagify_location',
 *     eduSel     : '#kt_tagify_education',
 *   });
 *   $.ajax({ url: url, data: filters });
 */
(function ($, window) {
    'use strict';

    window.AppTracker = window.AppTracker || {};

    /**
     * Safely parses a Tagify JSON string. Returns [] on parse failure.
     */
    function safeParseTagify(str) {
        if (!str || str === '[]' || str === '') { return []; }
        try {
            var parsed = JSON.parse(str);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    AppTracker.filterParse = {
        /**
         * Parse a Tagify skills input value.
         * Returns { all: string[], required: string[] }
         */
        skills: function (tagifyValue) {
            var tags = safeParseTagify(tagifyValue);
            var all      = [];
            var required = [];
            for (var i = 0; i < tags.length; i++) {
                var val = tags[i].value;
                if (val) { all.push(val); }
                if (tags[i].checked === true) { required.push(val); }
            }
            return { all: all, required: required };
        },

        /**
         * Parse a Tagify location/education input value.
         * Returns a flat array of string values.
         */
        values: function (tagifyValue) {
            var tags = safeParseTagify(tagifyValue);
            return tags.map(function (t) { return t.value; }).filter(Boolean);
        },

        /**
         * Build a plain param object for AJAX data.
         * Parses all three Tagify inputs and merges with standard fields.
         *
         * @param {Object} opts
         * @param {string|number} opts.jobid
         * @param {string|number} opts.mstatus_id
         * @param {string}        [opts.skillsSel]  CSS selector for the skills Tagify input
         * @param {string}        [opts.locSel]     CSS selector for the location Tagify input
         * @param {string}        [opts.eduSel]     CSS selector for the education Tagify input
         * @param {Object}        [opts.extra]      Additional key-value pairs to merge
         */
        buildParams: function (opts) {
            var skillsParsed = this.skills(opts.skillsSel ? $(opts.skillsSel).val() : '');
            var locations    = this.values(opts.locSel    ? $(opts.locSel).val()    : '');
            var education    = this.values(opts.eduSel    ? $(opts.eduSel).val()    : '');

            var params = {
                jobid          : opts.jobid,
                mstatus_id     : opts.mstatus_id,
                // Send structured arrays; server reads these instead of re-parsing Tagify JSON.
                skills         : skillsParsed.all.join(','),
                required_skills: skillsParsed.required.join(','),
                locations      : locations.join(','),
                education      : education.join(','),
                // Keep raw Tagify JSON for backward compat with methods not yet updated.
                cus_search             : opts.skillsSel    ? $(opts.skillsSel).val() : '',
                kt_tagify_location     : opts.locSel       ? $(opts.locSel).val()    : '',
                kt_tagify_education    : opts.eduSel       ? $(opts.eduSel).val()    : '',
            };

            if (opts.extra) {
                $.extend(params, opts.extra);
            }
            return params;
        },
    };

}(jQuery, window));
