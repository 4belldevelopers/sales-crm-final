# JERO Distributed Client Runtime (JDCR): The God-Level Blueprint for 0% Server Rendering, Zero-Strain ATS Drawers, and Real-Time Local Execution

> **Platform:** JERO.WORKS — Enterprise B2B Multi-Tenant ATS & Talent Trading Marketplace  
> **System Paradigm:** **JERO Distributed Client Runtime (JDCR)**  
> **Target Scale:** 10,000 Concurrent Users per page with **0% Avoidable Database & Server Strain on Rendering & Interaction**  
> **Governing Principle:** *The browser owns interaction state. MySQL owns transactional truth. Outbox owns durable change propagation. Elasticsearch owns the canonical query projection. Reverb owns notification. Delta Sync owns reconciliation. IndexedDB owns bounded local persistence. RAM owns active working state.*  
> **Hardware Profile:** Dedicated 32 GB RAM nodes each for Web/App Server, MySQL DB, and Elasticsearch  
> **Document Status:** Authoritative Architectural Standard & Living Engineering Blueprint (V5.0 Definitive Enterprise Standard)  
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
                               │   • Zero Inline Handlers (Event Delegation)      │
                               │   • Multi-Tab BroadcastChannel Coordination      │
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
         │ • Data Sensitivity Tier │       │ • Cache Poisoning Guard │       │ • Structured Clones     │
         └────────────┬────────────┘       └────────────┬────────────┘       └────────────┬────────────┘
                      │                                 │                                 │
                      └─────────────────────────────────┼─────────────────────────────────┘
                                                        │
                                                        ▼
                                       ┌──────────────────────────────────┐
                                       │     SYNCHRONIZATION SUBSYSTEM    │
                                       │   • Stream Cursor Tracker        │
                                       │   • Single-Flight Request De-dup │
                                       │   • Event Coalescing Supervisor  │
                                       │   • AbortController Supervisor   │
                                       └────────────────┬─────────────────┘
                                                        │
                                  ┌─────────────────────┴─────────────────────┐
                                  │ (Lossy Invalidation Hint)                 │ (Authoritative Reconcile)
                                  ▼                                           ▼
                   ┌──────────────────────────────┐            ┌──────────────────────────────┐
                   │   LARAVEL REVERB WEBSOCKET   │            │   DELTA-SYNC & ETAG API      │
                   │   • Notification Hint Only   │            │   • Stream Cursor (?after=X) │
                   │   • Coalesced Broadcast      │            │   • HTTP 304 ETag Validation │
                   │   • Lossy-Tolerant Delivery  │            │   • HTTP 410 Resync Support  │
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
                                       │   • Correlation ID Propagation   │
                                       │   • Zero HTML Blade Rendering    │
                                       └────────────────┬─────────────────┘
                                                        │
                                ┌───────────────────────┴───────────────────────┐
                                │                                               │
                                ▼                                               ▼
                 ┌─────────────────────────────┐                 ┌─────────────────────────────┐
                 │     ELASTICSEARCH CLUSTER   │                 │     MYSQL TRANSACTIONAL     │
                 │  (CANONICAL QUERY PROJECTION)│                │    (TRANSACTIONAL TRUTH)    │
                 │ • 100% Query Reads (60+ idx)│                 │ • Strict CUD Authority      │
                 │ • Monotonic Projection Guard│                 │ • Transactional Outbox + DLQ│
                 │ • 0% MySQL Reads on Web/API │                 │ • DB::transaction() Scope   │
                 └─────────────────────────────┘                 └─────────────────────────────┘
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
```

---

## 2. The Strict Ontological Contract: Truth vs. Projection vs. Cache

To eliminate architectural ambiguity across development teams, JDCR strictly defines the authoritative role of every tier in the system:

| Layer | System Component | Authoritative Role | Invariant Rule |
|---|---|---|---|
| **L4** | **MySQL Database** | **Transactional Truth (System of Record)** | Absolute authority for business writes. Enforces ACID constraints and tenant boundaries. Zero reads on web request paths. |
| **Log**| **Transactional Outbox** | **Durable Change Log** | Written in the exact same transaction as business data. Guarantees at-least-once ordered propagation to projections. |
| **L3** | **Elasticsearch Cluster** | **Canonical Query Projection** | Denormalized read projection serving 100% of read queries. Never a write authority. Must enforce monotonic updates. |
| **Sync**| **Thin Delta API** | **Authoritative Reconciliation Mechanism** | The sole mechanism for reconciling client state with truth. Supports stream cursors (`?after=X`) and HTTP 410 resync. |
| **Hint**| **Laravel Reverb** | **Real-Time Notification Hint (Lossy)** | Emits wake-up invalidation hints. Never assumed to be authoritative; frames may be dropped without consequence. |
| **L2** | **IndexedDB v2** | **Bounded Local Persistent Replica** | Local client cache bounded by adaptive LRU quotas and PII sensitivity tiers. Untrusted until validated. |
| **L1** | **Browser Working RAM** | **Active Working State** | Sub-millisecond pointer cache holding active drawer shell and open sub-tabs. Cleared on session teardown. |

---

## 3. Distributed Consistency: Transactional Outbox, DLQ & Monotonic Projections

### 3.1 Outbox Ordering Semantics & Tenant Sequences
* **Tenant-Scoped Monotonic Ordering:** `mutation_version` is ordered sequentially per tenant (`tenant_mutation_sequence`).
* **Critical Ordering Rule:** `mutation_version` represents **committed event ordering, not guaranteed worker processing completion ordering**.
* Because background Horizon workers process concurrently, Event `102` may reach a worker slightly before Event `101`. The architecture handles this through monotonic projection guards.

### 3.2 Elasticsearch Monotonic Projection Invariant
To prevent out-of-order worker delivery from regressing read state (e.g., Version 10 overwriting Version 11):
$$\text{incoming.entity\_version} > \text{stored.entity\_version}$$
* If $\text{incoming.entity\_version} > \text{stored.entity\_version}$, the Elasticsearch document is updated.
* If $\text{incoming.entity\_version} \le \text{stored.entity\_version}$, the update is **idempotently ignored**.
* **Invariant:** *Elasticsearch projection updates MUST be monotonic per entity. Older entity versions can never overwrite newer versions.*

```
                          OUTBOX EVENT DISPATCHER WITH DLQ
                          
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 1. LARAVEL WRITE API (ATOMIC DB TRANSACTION)                                           │
  │    DB::transaction(function() {                                                        │
  │        $client->update($validatedData);                                                │
  │        $client->increment('entity_version');                                           │
  │        $mutationId = DB::table('tenant_mutation_sequence')->insertGetId([...]);        │
  │        OutboxEvent::create([                                                           │
  │            'event_id'          => (string) Str::uuid(),                                │
  │            'tenant_id'         => $companyId,                                          │
  │            'entity_type'       => 'client',                                            │
  │            'entity_id'         => $client->id,                                         │
  │            'event_type'        => 'ClientEntityMutated',                               │
  │            'operation'         => 'update',                                            │
  │            'entity_version'    => $client->entity_version,                             │
  │            'mutation_version'  => $mutationId,                                         │
  │            'schema_version'    => 2,                                                   │
  │            'occurred_at'       => now()->toISOString(),                                │
  │            'actor_id'          => auth()->id(),                                        │
  │            'correlation_id'    => request()->header('X-Correlation-ID', Str::uuid()),  │
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

### 3.3 Outbox State Machine
The life cycle of every mutation event is governed by an explicit 7-state machine:
$$\text{PENDING} \longrightarrow \text{PROCESSING} \longrightarrow \text{DISPATCHED}$$
$$\text{PROCESSING} \longrightarrow \text{FAILED} \longrightarrow \text{RETRYING} \longrightarrow \text{DEAD\_LETTER} \longrightarrow \text{REPLAYED}$$

---

## 4. Disambiguating Versioning Sequences, Cursors & `HTTP 410`

### 4.1 The Three Version Sequences Defined
1. **`entity_version` (Integer):**
   * **Scope:** Scoped to an individual entity (e.g. Client #1089 revision `742`).
   * **Behavior:** Monotonically increments on every row update in MySQL.
   * **Purpose:** **Optimistic Concurrency Control**. Used by clients in `If-Match-Version` headers to prevent concurrent edit collisions (`HTTP 409 VERSION_CONFLICT`).
2. **`mutation_version` / `stream_cursor` (Monotonic BigInt):**
   * **Scope:** Scoped to the entire tenant event stream (e.g. Tenant #42 event log sequence `918273`).
   * **Behavior:** Monotonically ordered sequence across all entities within a tenant.
   * **Purpose:** **Delta Synchronization & Reconnection Cursor**. Allows a reconnecting browser to request missing event deltas.
3. **`projection_version` (Integer):**
   * **Scope:** Scoped to the Elasticsearch Read Index document (`ats_client_list`).
   * **Behavior:** Reflects the latest `entity_version` successfully materialized into Elasticsearch.
   * **Purpose:** **Projection Freshness Validation & Observability**.

### 4.2 Delta Sync Protocol & Cursor Expiry (`HTTP 410`)
When a reconnecting browser requests missing deltas:
```http
GET /service_company/clients/api/v2/delta-sync?after=918273
```
* **Case 1: Cursor Valid:** Server streams missing delta array `[918274, 918275...]`. Browser patches local state.
* **Case 2: Cursor Expired / Pruned:** If the client has been offline for weeks and requested cursor has been purged from the outbox log:
  ```http
  HTTP/1.1 410 Gone
  Content-Type: application/json
  {
      "error": "SYNC_CURSOR_EXPIRED",
      "message": "Local stream cursor is too old. Full resync required.",
      "server_latest_cursor": 950000
  }
  ```
  The runtime intercepts `HTTP 410`, purges the local entity replica, and initiates an authoritative **Full Entity Resync**.

### 4.3 Complementary Freshness: ETag vs. Mutation Cursor
* **HTTP ETag (Resource Freshness):** Evaluates *"Has this entire representation changed?"* Used on full resource endpoints (`/clients/api/v2/{id}/graph`) returning `HTTP 304 Not Modified` with 0 bytes.
* **Mutation Cursor (Delta Stream):** Evaluates *"What discrete events did I miss since cursor X?"* Used on stream reconciliation endpoints (`/clients/api/v2/delta-sync?after={cursor}`).

---

## 5. Event Coalescing & The Formal Drawer Lifecycle State Machine

### 5.1 Browser Event Coalescing
If a high-velocity client experiences 10 stage updates in 2 seconds, Reverb emits 10 invalidation hints. The runtime prevents 10 duplicate API calls via **Event Coalescing**:
```javascript
// Browser Event Coalescing: Throttles re-sync requests
window.ClientSyncSupervisor = {
    dirtyEntities: new Set(),
    coalesceTimer: null,

    notifyDirty(clientId) {
        this.dirtyEntities.add(clientId);
        if (this.coalesceTimer) return;
        
        this.coalesceTimer = setTimeout(() => {
            this.flushDirtyEntities();
            this.coalesceTimer = null;
        }, 150); // 150ms Coalescing Window
    },

    flushDirtyEntities() {
        this.dirtyEntities.forEach(clientId => {
            ClientStateManager.reconcileEntity(clientId);
        });
        this.dirtyEntities.clear();
    }
};
```

### 5.2 The Formal Drawer Lifecycle State Machine
To permanently eliminate the defect where switching tabs or waking from idle produces blank drawers:

```
                      FORMAL DRAWER LIFECYCLE STATE MACHINE
                      
                                  ┌──────────────┐
                                  │    CLOSED    │
                                  └──────┬───────┘
                                         │ Click Client Card
                                         ▼
                                  ┌──────────────┐
                                  │   OPENING    │ (Monotonic Request Token Allocated)
                                  └──────┬───────┘
                                         │ Check L1 RAM / L2 IDB
                                         ▼
                                  ┌──────────────┐
                                  │ SHELL_READY  │ (Toolbar, Tabs, Shell Mounted in 0ms)
                                  └──────┬───────┘
                                         │ Hydrate Active Tab
                                         ▼
                                  ┌──────────────┐
                     ┌───────────►│  HYDRATING   ├────────────┐
                     │            └──────┬───────┘            │
                     │                   │                    │
                     │ Local/Remote OK   │ Failure            │ Network Offline
                     │                   ▼                    ▼
              ┌──────┴───────┐    ┌──────────────┐     ┌──────────────┐
              │    READY     │    │   DEGRADED   │     │   OFFLINE    │
              └──────┬───────┘    │ (Retry View) │     │  (Read-Only) │
                     │            └──────────────┘     └──────────────┘
                     │ Reverb Hint / Cursor Lag
                     ▼
              ┌──────────────┐
              │    STALE     │ (Subtle visual badge)
              └──────┬───────┘
                     │ Background Reconcile
                     ▼
              ┌──────────────┐
              │   SYNCING    │
              └──────┬───────┘
                     │ Delta Reconciled
                     ▼
              ┌──────────────┐
              │    READY     │
              └──────────────┘

  ┌────────────────────────────────────────────────────────────────────────┐
  │ AUTH EXCEPTION (401 / ORG SWITCH):                                     │
  │ ANY STATE ────────► SESSION_INVALID ────────► PURGED & TEARDOWN        │
  └────────────────────────────────────────────────────────────────────────┘
```

### 5.3 Stale State Budget (UX Freshness Standards)
The UI never silently presents stale operational data. Every view surface evaluates freshness against explicit thresholds:
* **`0 – 2 sec`:** **Fresh** (Standard presentation).
* **`2 – 10 sec`:** **Syncing** (Subtle sync pulse).
* **`10 – 60 sec`:** **Possibly Stale** (Light amber indicator).
* **`> 60 sec`:** **Stale** (Prompt badge: *"Data may be outdated. Reconciling..."*).
* **`> 5 min / Offline`:** **Revalidation Required / Read-Only Mode**.

---

## 6. Multi-Tab Synchronization & Single-Flight Deduplication

### 6.1 Multi-Tab Coordination via `BroadcastChannel`
When a recruiter has multiple browser tabs open on JERO:
```
  [ TAB A ] Recruiter moves Candidate to 'Offered'
      │
      ▼
  MySQL Write ➔ Outbox ➔ Reverb
      │
      ▼
  Tab A emits local event: BroadcastChannel('JERO_TAB_SYNC').postMessage({
      type: 'ENTITY_MUTATED',
      tenant_id: 42,
      entity_type: 'client',
      entity_id: 1089,
      new_version: 743
  });
      │
      ▼
  [ TAB B ] Receives local broadcast in 0ms:
      • Invalidates Client #1089 in L1 RAM
      • Patches UI or triggers background Delta-Sync
      • 0 network roundtrips required for cross-tab awareness!
```

### 6.2 Single-Flight Request Deduplication (Anti-Stampede)
To prevent multiple components, sub-tabs, or multi-tab listeners from triggering concurrent HTTP requests for the same entity:
```javascript
window.ClientFlightSupervisor = {
    inFlightRequests: new Map(),

    fetchClient(clientId) {
        if (this.inFlightRequests.has(clientId)) {
            // Await existing in-flight Promise (Deduplication)
            return this.inFlightRequests.get(clientId);
        }

        const fetchPromise = fetch(`/service_company/clients/api/v2/${clientId}/graph`)
            .then(res => res.json())
            .finally(() => {
                this.inFlightRequests.delete(clientId);
            });

        this.inFlightRequests.set(clientId, fetchPromise);
        return fetchPromise;
    }
};
```

---

## 7. Data Sensitivity Classification & Storage Policies

ATS data is legally sensitive (PII, salaries, interview feedback, billing terms). Not all objects are permitted in persistent storage.

### 7.1 The 4-Tier Data Classification Matrix

| Tier | Sensitivity Level | Entity Domains | Permitted Storage | TTL / Eviction Policy |
|---|---|---|---|---|
| **Tier A** | **Highly Sensitive** | Resume Binaries, Compensation/Salary, Billing Terms, PII (Phone/Email) | **L1 Working RAM Only** (Never written to disk/IDB) | Immediate revocation (`URL.revokeObjectURL()`) / Session Teardown |
| **Tier B** | **Sensitive** | Candidate Full Profile, Interview Feedback, Offer Terms | **L1 RAM + Bounded IDB** | 5-minute soft TTL; encrypted tenant store; strict LRU |
| **Tier C** | **Operational** | Client Name, Job Titles, Requisition Status, Funnel Counts | **L1 RAM + Bounded IDB** | 24-hour soft TTL; revalidated via event invalidation |
| **Tier D** | **Reference** | Country Lists, State/City Facets, Standard Skill Taxonomies | **L2 Persistent IDB** | 7-day TTL; background ETag refresh |

### 7.2 Storage Quota Management & Poisoning Protection
* **Storage Quota Supervision:** Uses `navigator.storage.estimate()`. If storage exceeds **70%**, soft LRU eviction runs. If storage exceeds **85%**, aggressive eviction purges all Tier C data.
* **Cache Poisoning Protection:** Data read from IndexedDB is untrusted until verified:
  $$\text{IndexedDB Read} \longrightarrow \text{Schema Validation} \longrightarrow \text{Tenant Isolation Check} \longrightarrow \text{Version Check} \longrightarrow \text{Mount}$$

---

## 8. UI Runtime Standard: Information Hierarchy & Zero Inline Handlers

### 8.1 Semantic Information Hierarchy
The platform strictly enforces semantic information hierarchy across all 15.6" screens (`1366×768` and `1920×1080`):
* **Tier 1: Pure White Toolbar Card:** `#ffffff`, border `1px solid #e2e8f0`, radius `10px`, interaction-specific search and primary action.
* **Tier 2: Pure White Sub-Tabs Card:** Single active blue highlight (`#009ef7`).
* **Tier 3: Primary Information Surface:** Context-aware selection (Card Stream, High-Density Table, Kanban, Timeline, or Vector Chart).
* **Tier 4: Activity & Audit Strip:** Recruiter attribution, timestamps, and status badges.

### 8.2 Frontend Security & Zero Inline Onclick Standard
* **Zero Inline Handlers:** All `onclick="..."` attributes are strictly banned. The UI Runtime employs **Centralized Event Delegation** (`document.addEventListener('click', ...)` with `data-action` and `data-tab` attributes) ensuring strict Content Security Policy (CSP) compliance.
* **XSS Sanitization:** All dynamic text must be assigned via `.textContent` or escaped. All dynamic asset URLs must pass `sanitizeAssetUrl()` before assignment to DOM attributes.

---

## 9. Observability: JDCR Control Plane & The Ultimate KPI

Rather than unmeasurable slogans, JDCR defines explicit operational metrics and SLOs.

### 9.1 The Ultimate KPI: JDCR Local Hit Ratio
$$\text{JDCR Local Hit Ratio} = \frac{\text{L1 RAM Hits} + \text{L2 IndexedDB Hits}}{\text{Total Read Interactions}} \times 100\%$$
* **Launch SLO Target:** $> 95\%$ of drawer tab interactions execute with 0 network calls.
* **Mature Production Target:** $> 99\%$ for already-hydrated entity sessions.

### 9.2 First-Class Control Plane Metrics

| Metric Name | Measurement Scope | Production SLO / Alert Threshold |
|---|---|---|
| **`projection_lag`** | `entity_version - projection_version` | Critical Alert if $\text{P95 Lag} > 0$ for $> 3\text{ seconds}$ |
| **`outbox_pending_count`** | Horizon queue depth of unprojected events | Alert if pending count $> 500$ |
| **`outbox_oldest_pending_age`**| Timestamp of oldest unprocessed event | Critical Alert if oldest age $> 10\text{ seconds}$ |
| **`dlq_event_count`** | Total events in Dead Letter Queue | Critical Alert if count $> 0$ (Immediate Sentry notification) |
| **`gateway_processing_time`** | Octane PHP worker execution latency | $\text{P95} < 10\text{ms}$ |
| **`end_to_end_api_latency`** | Total network roundtrip over WAN | $\text{P95} < 200\text{ms}$ |
| **`local_render_latency`** | Template engine execution in browser RAM | $\text{P95} < 1.5\text{ms}$ |

---

## 10. Systemic Failure Mode Matrix

| Failure Event | Systemic Impact | Expected Runtime Behavior | Recovery Mechanism |
|---|---|---|---|
| **Reverb Disconnected** | Real-time hints stopped | Runtime continues rendering from L1/L2 local cache | Exponential reconnect + Delta-Sync on reconnect |
| **Reverb Event Missed** | Invalidation hint lost | Runtime serves cached data with `SYNCING` indicator | Background Delta-Sync validates cursor on tab focus |
| **Elasticsearch Outage** | Query reads failing | Writes continue to MySQL; Outbox events queue | Reads degrade to L1/L2 local cache + read-only degraded notice |
| **Outbox Worker Down** | ES projections delayed | MySQL transactions succeed; Outbox queue builds | Events remain `PENDING`; workers resume without data loss |
| **MySQL Outage** | Writes unavailable | Reads continue served 100% from Elasticsearch & Local DB | Writes fail safely with `HTTP 503 Service Unavailable` |
| **IndexedDB Blocked** | L2 persistence disabled | Runtime degrades to L1 RAM + Direct Gateway fetch | Application functions normally in-memory; logs warning |
| **Browser Offline** | Network unreachable | **Offline READ enabled**: Cached clients/jobs viewable | Read-only banner displayed; **Offline WRITE disabled** |
| **Session Expired (401)**| Auth token invalid | Immediate atomic purge of RAM, IndexedDB, and tokens | Redirect to login; zero sensitive ATS data left on disk |
| **Version Conflict (409)**| Stale write collision | Server rejects mutation; returns latest entity state | UI presents Conflict Reconciliation Modal for merge |
| **Tenant Organization Switch** | Cross-tenant switch | Runtime purges all tenant-scoped memory and IDB stores | Re-hydrates exclusively from new tenant gateway |
| **API Schema Mismatch** | Deploy incompatibility| Runtime detects Schema version bump (e.g. 2.0 -> 2.1) | Automatically invalidates outdated cache & refetches |

---

## 11. Concrete Implementation Roadmap for Service Company Clients

### Phase 0: Baseline & Contract Freeze (Pre-Implementation Verification)
1. **Benchmark Current Baseline:** Measure current drawer HTML payload size (KB), latency (TTI), MySQL queries, ES queries, and server CPU/RAM.
2. **Contract Freeze:** Formalize Schema 2.0 JSON specifications and versioning rules.
3. **Rollback Strategy:** Establish feature-flag toggles allowing instant rollback to Blade drawer rendering if required.

### Phase 1: Transactional Outbox & Thin JSON API (Backend)
1. Create migration for `tenant_outbox_events` and `tenant_outbox_dead_letter` tables.
2. Implement `OutboxEventDispatcherJob` in Laravel Horizon with exponential retry, DLQ routing, and monotonic projection guard.
3. Implement `GET /service_company/clients/api/v2/{id}/graph` enforcing the **JERO Universal API Security Pipeline**.
4. Implement `GET /service_company/clients/api/v2/delta-sync?after={cursor}` with `HTTP 410` support.

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

### Phase 4: Reactive State Machine & Multi-Tab Wiring
1. Upgrade `ClientStateManager` in `index.blade.php`:
   * Implement formal Drawer Lifecycle State Machine.
   * Single-Flight request deduplication and Event Coalescing supervisor.
   * Multi-tab `BroadcastChannel('JERO_TAB_SYNC')` coordination.
2. Wire Laravel Reverb channel:
   * Listen for `ClientEntityMutated` events.
   * Invalidate local cache and trigger background delta-sync.

### Phase 5: Verification & Zero-Strain Benchmark
1. Automated network throttling test (simulated 10-minute idle sleep, offline-to-online recovery).
2. Concurrency load test targeting 10,000 simulated users with 0% MySQL reads and flat server CPU (< 5%).
3. Compare results against Phase 0 baselines to quantitatively prove $> 95\%$ JDCR Local Hit Ratio.

---

## 12. The 20 Non-Negotiable Invariants of JDCR

1. **Invariant 1:** MySQL is the sole transactional authority.
2. **Invariant 2:** Elasticsearch is the canonical query projection, never a write authority.
3. **Invariant 3:** Reverb is a notification hint, never the data authority.
4. **Invariant 4:** Browser-local interaction must execute in 0ms without hitting MySQL or Elasticsearch.
5. **Invariant 5:** Every business write produces exactly one atomic outbox event in the same transaction.
6. **Invariant 6:** Every projection operation must be strictly idempotent.
7. **Invariant 7:** Older entity versions can never overwrite newer versions in read projections.
8. **Invariant 8:** Deleted entities must propagate through durable tombstone events.
9. **Invariant 9:** Tenant boundary isolation is enforced server-side on every request.
10. **Invariant 10:** Local client cache validation never grants authorization.
11. **Invariant 11:** Offline writes are strictly disabled in JDCR V1.
12. **Invariant 12:** Every cache tier must enforce explicit TTL, LRU eviction, and data sensitivity policies.
13. **Invariant 13:** Every browser network request must have cancellation (`AbortController`) and monotonic token protection.
14. **Invariant 14:** Every mutation and outbox event must carry a global correlation ID.
15. **Invariant 15:** Every reconnecting browser must deterministically reconcile via Delta Sync or `HTTP 410` full resync.
16. **Invariant 16:** API schema incompatibility triggers controlled client cache invalidation.
17. **Invariant 17:** No dynamic user content may ever be injected into the DOM without sanitization.
18. **Invariant 18:** Zero inline JavaScript event handlers (`onclick`) permitted in the UI runtime.
19. **Invariant 19:** Resume binaries are never intentionally persisted to browser disk storage.
20. **Invariant 20:** No performance claim is considered valid without empirical measurement against Phase 0 baselines.

---

*This document is the authoritative standard for the JERO Distributed Client Runtime (JDCR). All subsequent development on the Service Company Client module must adhere strictly to these 20 invariants.*
