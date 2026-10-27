/**
 * JERO Service Company Drawer - Interviews Tab Adapter
 * Module: drawer/tabs/interviews.js
 * 
 * Target Container: #candidatetListinterviewtracker / #directcandidatetListinterviewtracker
 * Coordinates: ClientInterviewStoreInternal / ClientInterviewStoreDirect
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var InterviewsTabAdapter = {
        name: 'interviews',
        aliases: ['interview_tracker'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#candidatetListinterviewtracker');
            if (!$target.length) {
                $target = $root.find('#directcandidatetListinterviewtracker');
            }
            if (!$target.length) {
                $target = $root.find('#interview_tracker_body, #interview_tracker');
            }
            return $target;
        },

        load: function(instance, options) {
            options = options || {};
            var self = this;
            var tabName = self.name;
            var coordinator = instance.coordinator;
            var renderer = instance.renderer;
            var $container = self.getContainer(instance);

            var filter = options.filter || 'all';
            var isInitial = coordinator.getState(tabName).status === 'UNINITIALIZED';
            coordinator.transition(tabName, isInitial ? 'LOADING' : 'REFRESHING', { filter: filter });

            var clientId = parseInt(instance.context.canonicalClientId, 10);
            var isDirect = instance.context.clientType === 'direct';

            var fetchFn = isDirect ? window.directfetchCandidatesInverViewTracker : window.fetchCandidatesInverViewTracker;
            var store = isDirect ? window.ClientInterviewStoreDirect : window.ClientInterviewStoreInternal;

            if (typeof fetchFn === 'function') {
                try {
                    fetchFn(1, options.search || '');
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[InterviewsTabAdapter] Error calling fetchFn', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Interviews', 'Failed to fetch interview schedules.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else if (store && typeof store.fetch === 'function') {
                try {
                    store.fetch(clientId, null, false, options.search || '');
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[InterviewsTabAdapter] Error in interview store.fetch', e);
                    var cid2 = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid2 });
                    renderer.renderError($container, 'Interviews', 'Interview tracker error.', cid2, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                setTimeout(function() {
                    if (instance.isDestroyed()) return;
                    var retryFetch = isDirect ? window.directfetchCandidatesInverViewTracker : window.fetchCandidatesInverViewTracker;
                    if (typeof retryFetch === 'function') {
                        retryFetch(1, options.search || '');
                        coordinator.transition(tabName, 'READY', { filter: filter });
                    } else {
                        var cid = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', { error: 'Interview tracker unavailable', correlationId: cid });
                        renderer.renderError($container, 'Interviews', 'Interview service initializing.', cid, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                }, 150);
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['interviews'] = InterviewsTabAdapter;
    window.ClientDrawerTabs['interview_tracker'] = InterviewsTabAdapter;

})(window);
