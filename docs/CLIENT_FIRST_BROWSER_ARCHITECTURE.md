# JERO Distributed Client Runtime (JDCR): The God-Level Blueprint for 0% Server Rendering, Zero-Strain ATS Drawers, and Real-Time Local Execution

> **Platform:** JERO.WORKS — Enterprise B2B Multi-Tenant ATS & Talent Trading Marketplace  
> **System Paradigm:** **JERO Distributed Client Runtime (JDCR)**  
> **Target Scale:** 10,000 Concurrent Users per page with **0% Server / MySQL / Elasticsearch Strain on Rendering & Interaction**  
> **Core Architecture:** Transactional Outbox CQRS (MySQL Write Authority -> Outbox Log + DLQ -> Idempotent ES Read Projection -> Reverb Invalidation Hint -> Browser Distributed Runtime)  
> **Hardware Profile:** Dedicated 32 GB RAM nodes for Web/App Server, MySQL DB, and Elasticsearch  
> **Document Status:** Authoritative Architectural Standard & Living Engineering Blueprint (V4.4 Enterprise Distributed Standard)  
> **Scope:** Service Company Module (`service_company/clients`) & Universal ATS Platform Standard  
> **Updated:** October 2026  

---

## 1. Executive Summary & The JDCR Paradigm

### 1.1 The Paradigm Shift: From "Web Page with Caching" to "Distributed Client Runtime"

In high-concurrency recruitment software, treating the browser as a traditional web client that requests server-rendered HTML or monolithic JSON graphs on every user interaction is an architectural failure. 

JERO.WORKS rejects the legacy Web 2.0 client-server model in favor of the **JERO Distributed Client Runtime (JDCR)**:
> **The user's browser is engineered as an autonomous, stateful edge node operating in a distributed recruitment runtime.**

```
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
                                    JERO DISTRIBUTED CLIENT RUNTIME (JDCR)
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════

                               ┌──────────────────────────────────────────────────┐
                               │                    UI RUNTIME                    │
                               │   • Incremental Template Renderer                │
                               │   • Information Hierarchy (Tiers 1, 2, 3, 4)     │
                               │   • Zero Inline Onclick (Event Delegation)       │
                               │   • Calibrated for 15.6" Screens (1366 & 1920)   │
                               └────────────────────────┬─────────────────────────┘
                                                        │
                      ┌─────────────────────────────────┼─────────────────────────────────┐
                      ▼                                 ▼                                 ▼
         ┌─────────────────────────┐       ┌─────────────────────────┐       ┌─────────────────────────┐
         │     L1 WORKING RAM      │       │     L2 PERSISTENCE      │       │   COMPUTE ENGINE (OLAP) │
         │   (ClientMemoryDB)      │       │    (ClientEntityDB)     │       │   (cl-data-worker.js)   │
         │ • Active Working Set    │       │ • Bounded IndexedDB v2  │       │ • Stateless Web Worker  │
         │ • 0ms Pointer Lookups   │       │ • Adaptive LRU Eviction │       │ • Off-Thread Filtering  │
         │ • Ephemeral WASM Blobs  │       │ • PII & Session Purge   │       │ • Facet & Sort Compute  │
         │ • Untrusted Data Guard  │       │ • Poisoning Protection  │       │ • Structured Clones     │
         └────────────┬────────────┘       └────────────┬────────────┘       └────────────┬────────────┘
                      │                                 │                                 │
                      └─────────────────────────────────┼─────────────────────────────────┘
                                                        │
                                                        ▼
                                       ┌──────────────────────────────────┐
                                       │     SYNCHRONIZATION SUBSYSTEM    │
                                       │   • Stream Cursor Tracker        │
                                       │   • Optimistic Concurrency Guard │
                                       │   • AbortController Supervisor   │
                                       └────────────────┬─────────────────┘
                                                        │
                                  ┌─────────────────────┴─────────────────────┐
                                  │ (Lossy Invalidation Hint)                 │ (Authoritative Reconcile)
                                  ▼                                           ▼
                   ┌──────────────────────────────┐            ┌──────────────────────────────┐
                   │   LARAVEL REVERB WEBSOCKET   │            │  DELTA-SYNC & ETAG API       │
                   │   • Lossy-Tolerant Broadcast │            │   • Stream Cursor (?after=X) │
                   │   • Entity Invalidation Hint │            │   • HTTP 304 ETag Validation │
                   └──────────────┬───────────────┘            └──────────────┬───────────────┘
                                  │                                           │
                                  └─────────────────────┬─────────────────────┘
                                                        │
════════════════════════════════════════════════════════╪══════════════════════════════════════════════════════════
                                                 NETWORK BOUNDARY (REST / WS)
════════════════════════════════════════════════════════╪══════════════════════════════════════════════════════════
                                                        │
                                       ┌────────────────▼─────────────────┐
                                       │ JERO UNIVERSAL SECURITY PIPELINE │
                                       │   • Session & Tenant Auth Guard  │
                                       │   • RBAC & Field Masking         │
                                       │   • Zero HTML Blade Rendering    │
                                       └────────────────┬─────────────────┘
                                                        │
                                ┌───────────────────────┴───────────────────────┐
                                │                                               │
                                ▼                                               ▼
                 ┌─────────────────────────────┐                 ┌─────────────────────────────┐
                 │     ELASTICSEARCH CLUSTER   │                 │     MYSQL TRANSACTIONAL     │
                 │          (READ MODEL)       │                 │        (WRITE MODEL)        │
                 │ • 100% Query Reads (60+ idx)│                 │ • Strict CUD Authority      │
                 │ • Idempotent Outbox Sink    │                 │ • Transactional Outbox + DLQ│
                 │ • 0% MySQL Reads on Web/API │                 │ • DB::transaction() Scope   │
                 └─────────────────────────────┘                 └─────────────────────────────┘
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
```

---

## 2. Distributed Consistency: Transactional Outbox, DLQ & Authority Hierarchy

### 2.1 The Distributed Flaw of Direct Synchronous Sync
Synchronous chaining (`MySQL -> Elasticsearch -> Reverb`) is fundamentally flawed in distributed systems:
```
  MySQL UPDATE SUCCESS  ──►  PHP Worker Times Out / OOM  ──►  ES Update FAILS  ──►  Reverb FAILS
  (Committed to DB)                                          (Index Stale)         (Client Blind)
```
In this scenario, Elasticsearch is out of sync with MySQL, and connected browsers never receive invalidation notices. The system's "zero stale ATS data" promise is broken.

### 2.2 The God-Level Solution: Transactional Outbox + DLQ & Replay Engine
To guarantee 100% consistency under all failure modes, JERO implements the **Transactional Outbox Pattern with Dead Letter Queue (DLQ)**:

```
                          OUTBOX EVENT DISPATCHER WITH DLQ
                          
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 1. LARAVEL WRITE API (ATOMIC DB TRANSACTION)                                           │
  │    DB::transaction(function() {                                                        │
  │        $client->update($validatedData);                                                │
  │        $client->increment('entity_version'); // Entity-Scoped Monotonic Version        │
  │        $mutationId = DB::table('tenant_mutation_sequence')->insertGetId([...]);        │
  │        OutboxEvent::create([                                                           │
  │            'tenant_id'         => $companyId,                                          │
  │            'entity_type'       => 'client',                                            │
  │            'entity_id'         => $client->id,                                         │
  │            'event_type'        => 'ClientEntityMutated',                               │
  │            'entity_version'    => $client->entity_version,                             │
  │            'mutation_version'  => $mutationId,                                         │
  │            'payload'           => json_encode($delta),                                 │
  │            'status'            => 'PENDING',                                           │
  │            'retry_count'       => 0                                                    │
  │        ]);                                                                             │
  │    });                                                                                 │
  └──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                             │ COMMIT (Guaranteed MySQL Atomicity)
                                             ▼
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 2. ASYNCHRONOUS OUTBOX DISPATCHER (HORIZON / REDIS QUEUE)                              │
  │    • Status: PENDING ➔ PROCESSING ➔ DISPATCHED                                         │
  │    • Idempotent Projection to Elasticsearch Read Cluster                               │
  │    • Broadcasts Invalidation Hint to Laravel Reverb WebSocket                          │
  └───────────────────────────┬────────────────────────────────────────┬───────────────────┘
                              │ Success                                │ Failure
                              ▼                                        ▼
             ┌─────────────────────────────────┐      ┌─────────────────────────────────┐
             │ Status: DISPATCHED              │      │ Status: RETRYING (Exp Backoff)  │
             │ ES projection_version = version │      │ Max Retries Exceeded (5)        │
             └─────────────────────────────────┘      └────────────────┬────────────────┘
                                                                       │
                                                                       ▼
                                                      ┌─────────────────────────────────┐
                                                      │ Status: DEAD_LETTER             │
                                                      │ • High-Priority Sentry Alert    │
                                                      │ • Operational Replay Interface  │
                                                      └─────────────────────────────────┘
```

### 2.3 Outbox State Machine
The life cycle of every mutation event is governed by a strict state machine:
`PENDING` ➔ `PROCESSING` ➔ `DISPATCHED`  
↳ `FAILED` ➔ `RETRYING` (exponential backoff up to 5 attempts) ➔ `DEAD_LETTER` ➔ `ALERT_TRIGGERED` ➔ `REPLAYED`.

### 2.4 The Formalized Authority Hierarchy
1. **MySQL (L4):** **Transactional Authority (System of Record)**. The sole source of truth for business writes.
2. **Transactional Outbox:** **Monotonic Event Log**. Guarantees at-least-once delivery to read stores.
3. **Elasticsearch (L3):** **Authoritative Read Projection**. Serves 100% of read queries.
4. **Version API (`/delta-sync`):** **Consistency & Reconciliation Engine**. Guarantees state alignment.
5. **Laravel Reverb:** **Real-Time Notification Hint**. Informs the browser that a mutation occurred; NEVER treated as the data authority.
6. **IndexedDB (L2):** **Bounded Local Read Replica**. Persists active entities with adaptive LRU eviction.
7. **Browser RAM (L1):** **Active Working Set**. Sub-millisecond UI rendering for active drawer tabs.

---

## 3. Disambiguating Versioning Sequences & Stream Cursors

To prevent distributed concurrency bugs, the architecture explicitly distinguishes between entity-level versioning and stream-level cursors:

### 3.1 The Three Version Sequences Defined
1. **`entity_version` (Integer):**
   * **Scope:** Individual Entity (e.g. Client #1089 revision `742`).
   * **Behavior:** Monotonically increments on every row update in MySQL.
   * **Purpose:** **Optimistic Concurrency Control**. Used by clients in `If-Match-Version` headers to prevent concurrent edit collisions (`HTTP 409 VERSION_CONFLICT`).
2. **`mutation_version` / `stream_cursor` (Monotonic BigInt):**
   * **Scope:** Entire Tenant Event Stream (e.g. Tenant #42 event log sequence `918273`).
   * **Behavior:** Monotonically ordered sequence across all entities within a tenant.
   * **Purpose:** **Delta Synchronization & Reconnection Cursor**. Allows a reconnecting browser to request `GET /delta-sync?after=918273` to receive missing deltas.
3. **`projection_version` (Integer):**
   * **Scope:** Elasticsearch Read Index (`ats_client_list`).
   * **Behavior:** Reflects the latest `entity_version` successfully indexed into Elasticsearch.
   * **Purpose:** **Projection Freshness Validation**. If `projection_version < entity_version`, the read projection is lagging.

### 3.2 Complementary Freshness: ETag vs. Mutation Cursor
* **HTTP ETag (Resource Freshness):** Evaluates *"Has this entire representation changed?"* Used on full resource endpoints (`/clients/api/v2/{id}/graph`) returning `HTTP 304 Not Modified` with 0 bytes.
* **Mutation Cursor (Delta Stream):** Evaluates *"What discrete events did I miss since timestamp/cursor X?"* Used on stream reconciliation endpoints (`/clients/api/v2/delta-sync?after={cursor}`).

---

## 4. JERO Universal API Security Pipeline

The browser is an untrusted environment. Tenant namespacing alone does not constitute a security boundary. The runtime enforces tenant-scoped cache access and purge; **server-side authorization remains the ultimate immutable security boundary**.

Every JERO API endpoint must pass through the **JERO Universal API Security Pipeline**:

```
  Incoming Request: GET /api/v2/clients/1089/graph
         │
         ▼
  1. Authenticated User Check (Session / JWT Token Validation)
         │
         ▼
  2. Tenant Boundary Isolation ($user->company_id === $request_tenant_id)
         │
         ▼
  3. RBAC Role & Permission Verification ($user->can('view_clients'))
         │
         ▼
  4. Entity Ownership & Exclusion Rules (Verify client belongs to tenant)
         │
         ▼
  5. Field Masking & Projections (Strip confidential billing, margins)
         │
         ▼
  6. Return Normalized Schema 2.0 JSON Payload
```

---

## 5. Storage Security, Bounded Quotas & Cache Poisoning Protection

### 5.1 Cache Poisoning & Untrusted Local Storage Protection
Because IndexedDB persists on user disks, corrupted or tampered local data must never be trusted implicitly.
```
  IndexedDB Read
        │
        ▼
  1. JSON Schema Validation (Verify data shape matches Schema 2.0)
        │
        ▼
  2. Tenant Scope Validation (Confirm tenant_id matches active session)
        │
        ▼
  3. Version Validation (Confirm entity_version is valid monotonic number)
        │
        ▼
  4. Permission Verification (Confirm active user can view this entity)
        │
        ▼
  Mount to UI via ClientIncrementalRenderer
```

### 5.2 Zero Persistent Resume Binaries & Session Purge
* **Zero PDF Disk Writes:** Resume binaries are streamed into **in-memory ephemeral Blob URLs** (`URL.createObjectURL(blob)`), processed via WASM in user RAM, and immediately garbage-collected via `URL.revokeObjectURL()`. Zero bytes of resumes are ever written to IndexedDB.
* **Atomic Session Purge:** On user logout, organization switch, or session expiration (HTTP 401), `ClientSecurityManager.purgeLocalData()` evacuates RAM, purges IndexedDB object stores, deletes dynamic Service Worker caches, and terminates WebSocket connections.

### 5.3 Adaptive Storage Quotas & Migration Safety
Rather than hardcoded constants, local storage quotas adapt dynamically based on the client hardware profile:
* **Default Baselines:** `DEFAULT_MAX_CLIENTS = 100`, `DEFAULT_MAX_JOBS = 50`, `DEFAULT_MAX_CANDIDATES = 50`, `DEFAULT_TTL = 7 days`.
* **Adaptive Scaling:**
  ```javascript
  const deviceRam = navigator.deviceMemory || 4; // Device RAM in GB
  const MAX_CACHED_CLIENTS = deviceRam >= 16 ? 200 : (deviceRam <= 4 ? 25 : 100);
  ```
* **Migration Safety:** The local database uses versioned stores (`Jero_ClientEntityDB_v2`) with explicit `onupgradeneeded` migration handlers to avoid destructive database drops during application updates.

---

## 6. UI Runtime: Information Hierarchy & Zero Inline Handlers

### 6.1 The Information Hierarchy Standard
The UI strictly enforces semantic information hierarchy across all 15.6" screens (`1366×768` and `1920×1080`):
* **Tier 1: Toolbar Card:** `#ffffff`, border `1px solid #e2e8f0`, radius `10px`, interaction-specific search and primary action.
* **Tier 2: Navigation / Sub-Tabs Card:** Single active blue highlight (`#009ef7`).
* **Tier 3: Primary Information Surface:** Context-aware selection (Card Stream, High-Density Table, Kanban, Timeline, or Vector Chart).
* **Tier 4: Activity & Audit Strip:** Recruiter attribution, timestamps, and status badges.

### 6.2 Frontend Security & Zero Inline Onclick Standard
* **Zero Inline Handlers:** All `onclick="..."` attributes are strictly banned. The UI Runtime employs **Centralized Event Delegation** (`document.addEventListener('click', ...)` with `data-action` and `data-tab` attributes) ensuring strict Content Security Policy (CSP) compliance.
* **XSS Sanitization:** All dynamic text must be assigned via `.textContent` or escaped. All dynamic asset URLs must pass `sanitizeAssetUrl()` before assignment to DOM attributes.

### 6.3 Interaction-Specific Timing Matrix
* **Tab Switch / Navigation:** **0ms (Immediate)**.
* **Filter Toggle / Pill Click:** **0ms (Immediate)**.
* **Pagination / Page Size:** **0ms (Immediate)**.
* **Dropdown Selection:** **0ms (Immediate)**.
* **Local In-Memory Text Search:** **150ms** (Maintains constant 60 FPS).
* **Remote Gateway Query Search:** **300ms** (Prevents server request flooding).

---

## 7. Performance SLOs & Systemic Failure Mode Matrix

### 7.1 Authoritative Performance SLOs
* **Target P95 Gateway Processing Time:** `< 10ms` (PHP Octane worker execution).
* **Target P95 End-to-End API Latency:** `< 200ms` (Over public WAN).
* **L1 RAM / L2 IndexedDB Latency:** `< 1ms` (Instant local rendering).
* **UI Frame Rate Budget:** Locked **60 FPS** (16.6ms frame budget).

### 7.2 Systemic Failure Mode Matrix

| Failure Event | Systemic Impact | Expected Runtime Behavior | Recovery Mechanism |
|---|---|---|---|
| **Reverb Disconnected** | Real-time hints stopped | Runtime continues rendering from L1/L2 local cache | Exponential reconnect + Delta-Sync on reconnect |
| **Reverb Event Missed** | Invalidation hint lost | Runtime serves cached data with `SYNCING` indicator | Background Delta-Sync validates cursor on tab focus |
| **Elasticsearch Outage** | Read queries failing | Writes continue to MySQL; Outbox events queue | Circuit breaker falls back to read-only DB replica; Outbox retries |
| **Outbox Worker Down** | ES projections delayed | MySQL transactions succeed; Outbox queue builds | Events remain `PENDING`; workers resume without data loss |
| **MySQL Outage** | Writes unavailable | Reads continue served 100% from Elasticsearch & Local DB | Writes fail safely with `HTTP 503 Service Unavailable` |
| **IndexedDB Blocked** | L2 persistence disabled | Runtime degrades to L1 RAM + Direct Gateway fetch | Application functions normally in-memory; logs warning |
| **Browser Offline** | Network unreachable | **Offline READ enabled**: Cached clients/jobs viewable | Read-only banner displayed; **Offline WRITE disabled** |
| **Session Expired (401)**| Auth token invalid | Immediate atomic purge of RAM, IndexedDB, and tokens | Redirect to login; zero sensitive ATS data left on disk |
| **Version Conflict (409)**| Stale write collision | Server rejects mutation; returns latest entity state | UI presents Conflict Reconciliation Modal for merge |
| **Tenant Organization Switch** | Cross-tenant switch | Runtime purges all tenant-scoped memory and IDB stores | Re-hydrates exclusively from new tenant gateway |
| **API Schema Mismatch** | Deploy incompatibility| Runtime detects Schema version bump (e.g. 2.0 -> 2.1) | Automatically invalidates outdated cache & refetches |

---

## 8. Concrete Implementation Roadmap for Service Company Clients

### Phase 0: Baseline & Contract Freeze (Pre-Implementation Verification)
1. **Benchmark Current Baseline:** Measure current drawer HTML payload size (KB), latency (TTI), MySQL queries, ES queries, and server CPU/RAM.
2. **Contract Freeze:** Formalize Schema 2.0 JSON specifications and versioning rules.
3. **Rollback Strategy:** Establish feature-flag toggles allowing instant rollback to Blade drawer rendering if required.

### Phase 1: Transactional Outbox & Thin JSON API (Backend)
1. Create migration for `tenant_outbox_events` and `tenant_outbox_dead_letter` tables.
2. Implement `OutboxEventDispatcherJob` in Laravel Horizon with exponential retry and DLQ routing.
3. Implement `GET /service_company/clients/api/v2/{id}/graph` enforcing the **JERO Universal API Security Pipeline**.
4. Implement `GET /service_company/clients/api/v2/delta-sync?after={cursor}`.

### Phase 2: Client Local Storage Engine (`cl-entity-db.js`)
1. Create `public/assets/service_company/client/cl-entity-db.js`:
   * Adaptive IndexedDB v2 repository with LRU eviction and migration handlers.
   * Ephemeral memory handling for WASM PDF resumes (zero disk writes).
   * Cache poisoning protection and atomic session teardown (`purgeLocalData()`).

### Phase 3: Incremental Template Renderer (`cl-drawer-view.js`)
1. Build `public/assets/service_company/client/cl-drawer-view.js`:
   * Client-side incremental renderers for Shell, Overview, Jobs, Candidates, Interviews, Offers, Joining.
   * Centralized Event Delegation (zero inline `onclick`).
   * Information Hierarchy compliance calibrated for 15.6" screens (`1366×768` and `1920×1080`).

### Phase 4: Reactive State Machine & Reverb Wiring
1. Upgrade `ClientStateManager` in `index.blade.php`:
   * Lazy sub-tab hydration engine with stream cursor tracking.
   * Monotonic request tokens and `AbortController` cancellation.
   * Optimistic concurrency handling with HTTP 409 conflict notifications.
2. Wire Laravel Reverb channel:
   * Listen for `ClientEntityMutated` events.
   * Invalidate local cache and trigger background delta-sync.

### Phase 5: Verification & Zero-Strain Benchmark
1. Automated network throttling test (simulated 10-minute idle sleep, offline-to-online recovery).
2. Concurrency load test targeting 10,000 simulated users with 0% MySQL reads and flat server CPU (< 5%).
3. Compare results against Phase 0 baselines to quantitatively prove 0% server strain.

---

*This document is the authoritative standard for the JERO Distributed Client Runtime (JDCR). All subsequent development on the Service Company Client module must adhere strictly to these specifications.*
