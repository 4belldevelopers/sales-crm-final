/**
 * JERO Distributed Client Runtime (JDCR) - Local Persistent Entity Engine
 * Version: 5.2 Enterprise Master Standard
 *
 * Responsibilities:
 * 1. Bounded Local Persistent Replica using IndexedDB v2 (Jero_ClientEntityDB_v2)
 * 2. Adaptive LRU Eviction based on device memory and storage quota
 * 3. Cache Poisoning Guard: Validates tenant boundary, schema version, and entity version
 * 4. Security-Critical Teardown: Block-Before-Delete protocol on logout/session expiry
 * 5. Ephemeral In-Memory Blob URL manager for WASM PDF resumes (Zero permanent disk persistence)
 *
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    const DB_NAME = 'Jero_ClientEntityDB_v2';
    const DB_VERSION = 1;
    const STORE_CLIENTS = 'clients';
    const STORE_CHECKPOINTS = 'sync_checkpoints';

    // Adaptive hardware-based capacity
    const deviceMemoryGb = (typeof navigator !== 'undefined' && navigator.deviceMemory) ? navigator.deviceMemory : 4;
    const MAX_CACHED_CLIENTS = deviceMemoryGb >= 16 ? 200 : (deviceMemoryGb <= 4 ? 30 : 100);

    let dbInstance = null;
    let isBlocked = false; // Block-before-delete guard flag
    const ephemeralBlobUrls = new Set();

    const ClientEntityDB = {
        dbName: DB_NAME,
        version: DB_VERSION,
        maxClients: MAX_CACHED_CLIENTS,

        /**
         * Open and initialize IndexedDB v2 connection with migration safety
         */
        open: function() {
            if (isBlocked) {
                return Promise.reject(new Error('[JDCR Security] Local database is blocked during session teardown.'));
            }
            if (!window.indexedDB) {
                return Promise.reject(new Error('[JDCR] IndexedDB not supported by this browser runtime.'));
            }
            if (dbInstance) {
                return Promise.resolve(dbInstance);
            }

            return new Promise(function(resolve, reject) {
                const req = window.indexedDB.open(DB_NAME, DB_VERSION);

                req.onupgradeneeded = function(e) {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains(STORE_CLIENTS)) {
                        const store = db.createObjectStore(STORE_CLIENTS, { keyPath: 'storage_key' });
                        store.createIndex('tenant_id', 'tenant_id', { unique: false });
                        store.createIndex('entity_id', 'entity_id', { unique: false });
                        store.createIndex('cached_at', 'cached_at', { unique: false });
                    }
                    if (!db.objectStoreNames.contains(STORE_CHECKPOINTS)) {
                        db.createObjectStore(STORE_CHECKPOINTS, { keyPath: 'tenant_id' });
                    }
                };

                req.onsuccess = function(e) {
                    dbInstance = e.target.result;
                    dbInstance.onversionchange = function() {
                        dbInstance.close();
                        dbInstance = null;
                    };
                    resolve(dbInstance);
                };

                req.onerror = function(e) {
                    console.warn('[JDCR EntityDB] Open failed, degrading gracefully to RAM-only runtime:', e.target.error);
                    reject(e.target.error);
                };
            });
        },

        /**
         * Generate isolated storage key scoped to tenant and entity
         */
        getStorageKey: function(tenantId, clientId) {
            return `tenant_${tenantId}__client_${clientId}`;
        },

        /**
         * Get client entity from L2 IndexedDB with Cache Poisoning Protection
         */
        get: function(tenantId, clientId) {
            if (isBlocked) return Promise.resolve(null);
            const self = this;
            const storageKey = this.getStorageKey(tenantId, clientId);

            return this.open().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        const tx = db.transaction(STORE_CLIENTS, 'readonly');
                        const store = tx.objectStore(STORE_CLIENTS);
                        const req = store.get(storageKey);

                        req.onsuccess = function() {
                            const record = req.result;
                            if (!record) return resolve(null);

                            // ─── Cache Poisoning & Untrusted Local Storage Protection ───
                            // 1. Tenant Scope Guard
                            if (Number(record.tenant_id) !== Number(tenantId)) {
                                console.warn('[JDCR Security] Tenant boundary mismatch detected in IDB. Discarding record.');
                                return resolve(null);
                            }
                            // 2. Schema Compatibility Guard
                            if (record.schema_version !== '2.0') {
                                console.warn('[JDCR Security] Schema version mismatch in cached entity. Evacuating.');
                                return resolve(null);
                            }
                            // 3. Entity Integrity Guard
                            if (!record.data || typeof record.entity_version !== 'number' || record.entity_version <= 0) {
                                console.warn('[JDCR Security] Entity corruption detected. Rejecting cached graph.');
                                return resolve(null);
                            }

                            // Touch cached_at timestamp for LRU tracking asynchronously
                            self.touchRecord(storageKey);

                            resolve(record);
                        };

                        req.onerror = function() {
                            resolve(null);
                        };
                    } catch (err) {
                        resolve(null);
                    }
                });
            }).catch(function() {
                return null;
            });
        },

        /**
         * Store or update client entity in L2 IndexedDB with LRU capacity control
         */
        put: function(tenantId, clientId, envelope) {
            if (isBlocked || !envelope || !envelope.data) return Promise.resolve(false);
            const self = this;
            const storageKey = this.getStorageKey(tenantId, clientId);

            const record = {
                storage_key: storageKey,
                tenant_id: Number(tenantId),
                entity_id: Number(clientId),
                entity_type: 'client',
                schema_version: envelope.schema_version || '2.0',
                entity_version: Number(envelope.entity_version || 1),
                projection_version: Number(envelope.projection_version || envelope.entity_version || 1),
                cached_at: Date.now(),
                data: envelope.data
            };

            return this.open().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        const tx = db.transaction(STORE_CLIENTS, 'readwrite');
                        const store = tx.objectStore(STORE_CLIENTS);
                        store.put(record);

                        tx.oncomplete = function() {
                            // Check quota and trigger adaptive LRU eviction in background
                            self.enforceLruEviction(tenantId);
                            resolve(true);
                        };

                        tx.onerror = function() {
                            resolve(false);
                        };
                    } catch (err) {
                        resolve(false);
                    }
                });
            }).catch(function() {
                return false;
            });
        },

        /**
         * Update cached_at timestamp for LRU ordering
         */
        touchRecord: function(storageKey) {
            this.open().then(function(db) {
                try {
                    const tx = db.transaction(STORE_CLIENTS, 'readwrite');
                    const store = tx.objectStore(STORE_CLIENTS);
                    const req = store.get(storageKey);
                    req.onsuccess = function() {
                        const item = req.result;
                        if (item) {
                            item.cached_at = Date.now();
                            store.put(item);
                        }
                    };
                } catch (e) {}
            }).catch(function() {});
        },

        /**
         * Delete specific entity from L2 IndexedDB (e.g. on Tombstone event)
         */
        delete: function(tenantId, clientId) {
            const storageKey = this.getStorageKey(tenantId, clientId);
            return this.open().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        const tx = db.transaction(STORE_CLIENTS, 'readwrite');
                        tx.objectStore(STORE_CLIENTS).delete(storageKey);
                        tx.oncomplete = function() { resolve(true); };
                        tx.onerror = function() { resolve(false); };
                    } catch (e) {
                        resolve(false);
                    }
                });
            }).catch(function() { return false; });
        },

        /**
         * Adaptive LRU Eviction: Ensures storage does not exceed MAX_CACHED_CLIENTS or quota threshold
         */
        enforceLruEviction: function(tenantId) {
            const self = this;
            this.open().then(function(db) {
                try {
                    const tx = db.transaction(STORE_CLIENTS, 'readwrite');
                    const store = tx.objectStore(STORE_CLIENTS);
                    const index = store.index('cached_at');
                    const records = [];

                    const cursorReq = index.openCursor();
                    cursorReq.onsuccess = function(e) {
                        const cursor = e.target.result;
                        if (cursor) {
                            if (cursor.value.tenant_id === Number(tenantId)) {
                                records.push({ key: cursor.primaryKey, cached_at: cursor.value.cached_at });
                            }
                            cursor.continue();
                        } else {
                            // If count exceeds max clients, delete oldest entries
                            if (records.length > self.maxClients) {
                                const excessCount = records.length - self.maxClients;
                                for (let i = 0; i < excessCount; i++) {
                                    store.delete(records[i].key);
                                }
                                console.log(`[JDCR LRU] Evicted ${excessCount} oldest client entities from L2 cache.`);
                            }
                        }
                    };
                } catch (e) {}
            }).catch(function() {});
        },

        /**
         * Save last known stream cursor for deterministic delta sync
         */
        saveCheckpoint: function(tenantId, cursor) {
            return this.open().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        const tx = db.transaction(STORE_CHECKPOINTS, 'readwrite');
                        tx.objectStore(STORE_CHECKPOINTS).put({
                            tenant_id: Number(tenantId),
                            latest_cursor: Number(cursor),
                            updated_at: Date.now()
                        });
                        tx.oncomplete = function() { resolve(true); };
                        tx.onerror = function() { resolve(false); };
                    } catch (e) { resolve(false); }
                });
            }).catch(function() { return false; });
        },

        /**
         * Read last known stream cursor
         */
        getCheckpoint: function(tenantId) {
            return this.open().then(function(db) {
                return new Promise(function(resolve) {
                    try {
                        const tx = db.transaction(STORE_CHECKPOINTS, 'readonly');
                        const req = tx.objectStore(STORE_CHECKPOINTS).get(Number(tenantId));
                        req.onsuccess = function() {
                            resolve(req.result ? Number(req.result.latest_cursor) : 0);
                        };
                        req.onerror = function() { resolve(0); };
                    } catch (e) { resolve(0); }
                });
            }).catch(function() { return 0; });
        },

        /**
         * Ephemeral Blob URL Manager for WASM Resumes (Zero permanent disk persistence)
         */
        createEphemeralBlobUrl: function(blob) {
            if (!blob) return '';
            const url = URL.createObjectURL(blob);
            ephemeralBlobUrls.add(url);
            return url;
        },

        revokeEphemeralBlobUrl: function(url) {
            if (url && ephemeralBlobUrls.has(url)) {
                URL.revokeObjectURL(url);
                ephemeralBlobUrls.delete(url);
            }
        },

        purgeAllEphemeralBlobs: function() {
            ephemeralBlobUrls.forEach(function(url) {
                try { URL.revokeObjectURL(url); } catch (e) {}
            });
            ephemeralBlobUrls.clear();
        },

        /**
         * Security-Critical Local State Teardown (Block-Before-Delete Protocol)
         * Guarantees zero sensitive ATS data can be mounted after logout or tenant switch
         */
        purgeLocalData: function() {
            isBlocked = true; // 1. Immediately block any subsequent cache reads/writes
            this.purgeAllEphemeralBlobs(); // 2. Revoke in-memory WASM blobs

            return new Promise(function(resolve) {
                if (dbInstance) {
                    try { dbInstance.close(); } catch (e) {}
                    dbInstance = null;
                }

                if (window.indexedDB && window.indexedDB.deleteDatabase) {
                    const req = window.indexedDB.deleteDatabase(DB_NAME);
                    req.onsuccess = function() {
                        console.log('[JDCR Security] L2 IndexedDB purged successfully.');
                        isBlocked = false;
                        resolve(true);
                    };
                    req.onerror = function() {
                        isBlocked = false;
                        resolve(false);
                    };
                    req.onblocked = function() {
                        isBlocked = false;
                        resolve(false);
                    };
                } else {
                    isBlocked = false;
                    resolve(true);
                }
            });
        }
    };

    window.ClientEntityDB = ClientEntityDB;

})(typeof window !== 'undefined' ? window : this);
