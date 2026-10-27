/**
 * JERO Service Company Drawer - Performance Tab Adapter
 * Module: drawer/tabs/performance.js
 * 
 * Target Container: #candidateDashboard
 * Coordinates: initPerformanceDashboard / fetchInternalClientDashboardAnalytics
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var PerformanceTabAdapter = {
        name: 'performance',
        aliases: ['candidateDashboard', 'candidatedashboard', 'dashboard', 'analytics'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#candidateDashboard');
            if (!$target.length) {
                $target = $root.find('#tab_performance');
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

            try {
                if (typeof window.initPerformanceDashboard === 'function') {
                    window.initPerformanceDashboard();
                } else if (!isDirect && typeof window.fetchInternalClientDashboardAnalytics === 'function') {
                    window.fetchInternalClientDashboardAnalytics();
                } else if (isDirect && typeof window.fetchDirectClientDashboardAnalytics === 'function') {
                    window.fetchDirectClientDashboardAnalytics();
                }

                setTimeout(function() {
                    if (window.perfChartInstances) {
                        Object.values(window.perfChartInstances).forEach(function(c) {
                            try { if (c && typeof c.resize === 'function') c.resize(); } catch(err){}
                        });
                    }
                }, 60);

                coordinator.transition(tabName, 'READY', { filter: filter });
            } catch (e) {
                console.error('[PerformanceTabAdapter] Error loading performance dashboard', e);
                var cid = renderer.generateCorrelationId();
                coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                renderer.renderError($container, 'Performance', 'Failed to load performance analytics.', cid, function() {
                    coordinator.retryTab(tabName);
                });
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['performance'] = PerformanceTabAdapter;
    window.ClientDrawerTabs['candidateDashboard'] = PerformanceTabAdapter;
    window.ClientDrawerTabs['candidatedashboard'] = PerformanceTabAdapter;
    window.ClientDrawerTabs['dashboard'] = PerformanceTabAdapter;
    window.ClientDrawerTabs['analytics'] = PerformanceTabAdapter;

})(window);
