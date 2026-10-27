/**
 * JERO Service Company Drawer - Overview Tab Adapter
 * Module: drawer/tabs/overview.js
 * 
 * Target Container: #overview / #kt_client_overview_content
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var OverviewTabAdapter = {
        name: 'overview',
        aliases: ['client_overview'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#kt_client_overview_content');
            if (!$target.length) {
                $target = $root.find('#overview');
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

            var isDirect = instance.context.clientType === 'direct';
            var analyticsFn = isDirect ? window.fetchDirectClientDashboardAnalytics : window.fetchInternalClientDashboardAnalytics;

            if (typeof analyticsFn === 'function' && options.force) {
                try {
                    analyticsFn();
                } catch (e) {
                    console.warn('[OverviewTabAdapter] Analytics refresh warning', e);
                }
            }

            // Overview is pre-rendered in Blade on initial open, so mark READY
            coordinator.transition(tabName, 'READY', { filter: 'default' });
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['overview'] = OverviewTabAdapter;
    window.ClientDrawerTabs['client_overview'] = OverviewTabAdapter;

})(window);
