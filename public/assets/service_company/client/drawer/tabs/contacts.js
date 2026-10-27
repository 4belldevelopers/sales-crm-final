/**
 * JERO Service Company Drawer - Contacts Tab Adapter
 * Module: drawer/tabs/contacts.js
 * 
 * Target Container: #cdContactCardsStreamInternal / #cdContactCardsStreamDirect / #contactListInternal
 * Coordinates: InternalClientContactsManager / DirectClientContactsManager
 * 
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    var ContactsTabAdapter = {
        name: 'contacts',
        aliases: ['client_contacts'],

        getContainer: function(instance) {
            var $root = instance.getRoot();
            var $target = $root.find('#cdContactCardsStreamInternal');
            if (!$target.length) {
                $target = $root.find('#cdContactCardsStreamDirect');
            }
            if (!$target.length) {
                $target = $root.find('#contactListInternal, #contacts');
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
            var isDirect = instance.context.clientType === 'direct';
            var mgr = isDirect ? window.DirectClientContactsManager : window.InternalClientContactsManager;

            if (mgr && typeof mgr.init === 'function') {
                try {
                    mgr.init(clientId);
                    if (Array.isArray(mgr.allContacts) && mgr.allContacts.length > 0) {
                        if (typeof mgr.filterAndRender === 'function') {
                            mgr.filterAndRender();
                        }
                    } else if (typeof mgr.fetch === 'function') {
                        mgr.fetch();
                    }
                    coordinator.transition(tabName, 'READY', { filter: filter });
                } catch (e) {
                    console.error('[ContactsTabAdapter] Error in contacts manager init', e);
                    var cid = renderer.generateCorrelationId();
                    coordinator.transition(tabName, 'ERROR', { error: e.message, correlationId: cid });
                    renderer.renderError($container, 'Contacts', 'Failed to load contacts directory.', cid, function() {
                        coordinator.retryTab(tabName);
                    });
                }
            } else {
                setTimeout(function() {
                    if (instance.isDestroyed()) return;
                    var retryMgr = isDirect ? window.DirectClientContactsManager : window.InternalClientContactsManager;
                    if (retryMgr && typeof retryMgr.init === 'function') {
                        retryMgr.init(clientId);
                        if (typeof retryMgr.filterAndRender === 'function') retryMgr.filterAndRender();
                        coordinator.transition(tabName, 'READY', { filter: filter });
                    } else {
                        var cid = renderer.generateCorrelationId();
                        coordinator.transition(tabName, 'ERROR', { error: 'Contacts manager unavailable', correlationId: cid });
                        renderer.renderError($container, 'Contacts', 'Contacts service initializing.', cid, function() {
                            coordinator.retryTab(tabName);
                        });
                    }
                }, 150);
            }
        }
    };

    window.ClientDrawerTabs = window.ClientDrawerTabs || {};
    window.ClientDrawerTabs['contacts'] = ContactsTabAdapter;

})(window);
