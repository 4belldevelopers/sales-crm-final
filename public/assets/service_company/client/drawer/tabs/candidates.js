/**
 * JERO Service Company Drawer - Candidates Tab Adapter
 * Module: drawer/tabs/candidates.js
 * 
 * Target Container: #candidatetList
 * Coordinates: ClientCandidateStore / ClientCandidateStoreDirect
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var CandidatesTabAdapter = {
        name: 'candidates',
        aliases: ['candidate_list'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#candidatetList');
            if (!$target.length) {
                $target = $root.find('#candidate_list .cd-cand-stream, #candidate_list');
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

            var clientId = instance.context.canonicalClientId;
            var companyId = instance.context.companyId || (window.CURRENT_COMPANY_ID || 0);
            var isDirect = instance.context.clientType === 'direct';

            var store = isDirect ? window.ClientCandidateStoreDirect : window.ClientCandidateStore;

            if (store && typeof store.init === 'function') {
                try {
                    store.init(companyId, clientId, isDirect ? 'direct' : 'internal');
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[CandidatesTabAdapter] Error in store.init', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Candidates', 'Unable to initialize candidate pipeline.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                setTimeout(function() {
                    if (instance.isDestroyed()) return;
                    var retryStore = isDirect ? window.ClientCandidateStoreDirect : window.ClientCandidateStore;
                    if (retryStore && typeof retryStore.init === 'function') {
                        retryStore.init(companyId, clientId, isDirect ? 'direct' : 'internal');
                        coordinator.transition(tabName, 'READY', { filter: filter });
                    } else {
                        var cid = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', { error: 'Candidate store unavailable', correlationId: cid });
                        renderer.renderError($container, 'Candidates', 'Candidate store is initializing.', cid, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                }, 150);
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['candidates'] = CandidatesTabAdapter;
    window.ClientDrawerTabs['candidate_list'] = CandidatesTabAdapter;

})(window);
