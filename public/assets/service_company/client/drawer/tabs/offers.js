/**
 * JERO Service Company Drawer - Offers & Joining Tab Adapter
 * Module: drawer/tabs/offers.js
 * 
 * Target Container: #candidatetListJoin / #candidatetListjoins
 * Function: fetchCandidatesJoin / directfetchCandidatesJoin
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var OffersTabAdapter = {
        name: 'offers',
        aliases: ['candidate_joining', 'joining'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#candidatetListJoin');
            if (!$target.length) {
                $target = $root.find('#candidatetListjoins');
            }
            if (!$target.length) {
                $target = $root.find('#candidate_joining');
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

            var isDirect = instance.context.clientType === 'direct';
            var fetchFn = isDirect ? window.directfetchCandidatesJoin : window.fetchCandidatesJoin;

            if (typeof fetchFn === 'function') {
                try {
                    fetchFn(1, options.search || '', filter);
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[OffersTabAdapter] Error calling fetchFn', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Offers & Joins', 'Failed to fetch offers and joins.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                setTimeout(function() {
                    if (instance.isDestroyed()) return;
                    var retryFetch = isDirect ? window.directfetchCandidatesJoin : window.fetchCandidatesJoin;
                    if (typeof retryFetch === 'function') {
                        retryFetch(1, options.search || '', filter);
                        coordinator.transition(tabName, 'READY', { filter: filter });
                    } else {
                        var cid = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', { error: 'Offers service unavailable', correlationId: cid });
                        renderer.renderError($container, 'Offers & Joins', 'Offers service initializing.', cid, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                }, 150);
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['offers'] = OffersTabAdapter;
    window.ClientDrawerTabs['candidate_joining'] = OffersTabAdapter;
    window.ClientDrawerTabs['joining'] = OffersTabAdapter;

})(window);
