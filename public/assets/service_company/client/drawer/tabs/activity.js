/**
 * JERO Service Company Drawer - Activity Tab Adapter
 * Module: drawer/tabs/activity.js
 * 
 * Target Container: #recent_activity
 * Coordinates: ClientActivityManager (CQRS activity stream)
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var ActivityTabAdapter = {
        name: 'recent_activity',
        aliases: ['activity'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#recent_activity');
            if (!$target.length) {
                $target = $root.find('#tab_activity');
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

            if (!$container.length) {
                console.warn('[ActivityTabAdapter] Container #recent_activity not found');
                coordinator.transition(tabName, 'ERROR', { error: 'DOM container missing' });
                return;
            }

            var filter = options.filter || 'all';
            var isInitial = coordinator.getState(tabName).status === 'UNINITIALIZED';
            coordinator.transition(tabName, isInitial ? 'LOADING' : 'REFRESHING', { filter: filter });

            var clientId = instance.context.canonicalClientId;
            var isInternal = instance.context.clientType === 'internal';
            var partnerCompanyId = isInternal ? null : clientId;

            if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
                try {
                    // Initialize Activity Manager with canonical client ID
                    window.ClientActivityManager.initClient(clientId, isInternal, partnerCompanyId);
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[ActivityTabAdapter] Error initializing ClientActivityManager', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Recent Activity', 'Failed to load activity stream.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                // ClientActivityManager not yet ready, retry shortly or mark error
                setTimeout(function() {
                    if (instance.isDestroyed()) return;
                    if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
                        window.ClientActivityManager.initClient(clientId, isInternal, partnerCompanyId);
                        coordinator.transition(tabName, 'READY', { filter: filter });
                    } else {
                        var cid = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', { error: 'Activity Manager Unavailable', correlationId: cid });
                        renderer.renderError($container, 'Recent Activity', 'Activity service is temporarily initializing.', cid, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                }, 150);
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['recent_activity'] = ActivityTabAdapter;
    window.ClientDrawerTabs['activity'] = ActivityTabAdapter;

})(window);
