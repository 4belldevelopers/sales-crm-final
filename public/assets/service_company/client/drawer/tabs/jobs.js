/**
 * JERO Service Company Drawer - Jobs Tab Adapter
 * Module: drawer/tabs/jobs.js
 * 
 * Target Container: #clientJobsListajax / #directclientJobsListajax / #allJobList
 * Coordinates: fetchJobsClient / fetchJobsClientdirect
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var JobsTabAdapter = {
        name: 'jobs',
        aliases: ['requisitions'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var isDirect = instance.context.clientType === 'direct';
            var $target = isDirect ? $root.find('#directclientJobsListajax') : $root.find('#clientJobsListajax');
            if (!$target.length) {
                $target = $root.find('#allJobList');
            }
            if (!$target.length) {
                $target = $root.find('#requisitions .cd-jobs-stream, #requisitions .card-body, #requisitions');
            }
            return $target;
        },

        load: function(instance, options) {
            options = options || {};
            var self = this;
            var tabName = self.name;
            var coordinator = instance.coordinator;
            var renderer = instance.renderer;
            var rm = instance.requestManager;
            var $container = self.getContainer(instance);

            var filter = options.filter || 'all';
            var search = options.search || '';
            var isDirect = instance.context.clientType === 'direct';

            // Transition to LOADING or REFRESHING
            var isInitial = coordinator.getState(tabName).status === 'UNINITIALIZED';
            coordinator.transition(tabName, isInitial ? 'LOADING' : 'REFRESHING', { filter: filter });

            var fetchFn = isDirect ? window.fetchJobsClientdirect : window.fetchJobsClient;

            if (typeof fetchFn === 'function') {
                try {
                    // Update active button state in DOM
                    var $group = isDirect ? $('#kt_activitiesDirectClient #requisitions') : $('#kt_activitiesClient #requisitions');
                    $group.find('.js-job-filter-btn').removeClass('active');
                    $group.find('.js-job-filter-btn[data-status="' + filter + '"]').addClass('active');

                    fetchFn(1, search, filter);
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[JobsTabAdapter] Error calling fetchFn', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Jobs', 'Unable to fetch open jobs.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                // Render skeleton immediately
                renderer.renderSkeleton($container, 'Jobs');

                var clientId = instance.context.canonicalClientId;
                var url = '/service_company/jobs_side_bar_job_ajax/data';

                rm.dispatch(tabName, {
                    url: url,
                    type: 'GET',
                    data: {
                        page: 1,
                        search: search,
                        status: filter,
                        client_id: clientId,
                        custom: !isDirect
                    },
                    filter: filter,
                    success: function(data, textStatus, xhr, reqContext) {
                        var html = '';
                        if (typeof data === 'string') {
                            html = data;
                        } else if (data && data.html) {
                            html = data.html;
                        }

                        var trimmed = $.trim(html);
                        if (!trimmed || trimmed.includes('No jobs found')) {
                            coordinator.transition(tabName, 'EMPTY', { filter: filter, requestId: reqContext.requestId });
                            renderer.renderEmpty($container, 'Jobs', 'No Job Requisitions Found', 'There are no active or matching jobs listed for this client.');
                        } else {
                            coordinator.transition(tabName, 'READY', { filter: filter, requestId: reqContext.requestId });
                            renderer.renderReady($container, html);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown, reqContext, failureReason) {
                        var correlationId = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', {
                            filter: filter,
                            requestId: reqContext.requestId,
                            correlationId: correlationId,
                            error: errorThrown || textStatus
                        });
                        renderer.renderError($container, 'Jobs', 'Unable to fetch job requisitions. Please retry.', correlationId, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                });
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['jobs'] = JobsTabAdapter;
    window.ClientDrawerTabs['requisitions'] = JobsTabAdapter;

})(window);
