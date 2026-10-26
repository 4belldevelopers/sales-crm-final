# JERO Distributed Client Runtime (JDCR): The God-Level Blueprint for 0% Server Rendering, Zero-Strain ATS Drawers, and Real-Time Local Execution

> **Platform:** JERO.WORKS — Enterprise B2B Multi-Tenant ATS & Talent Trading Marketplace  
> **System Paradigm:** **JERO Distributed Client Runtime (JDCR)**  
> **Target Scale:** 10,000 Concurrent Users per page with **0% Avoidable Database & Server Strain on Rendering & Interaction**  
> **Governing Philosophy:** *The browser owns interaction state. MySQL owns transactional truth. Outbox owns durable change propagation. Elasticsearch owns the canonical query projection. Reverb owns notification. Delta Sync owns reconciliation. IndexedDB owns bounded local persistence. RAM owns active working state.*  
> **Core Axiom:** $\mathbf{Correctness} \succ \mathbf{Consistency} \succ \mathbf{Freshness} \succ \mathbf{Latency}$ *(JDCR must prefer authoritative correctness over local-cache freshness whenever the two conflict).*  
> **Hardware Profile:** Dedicated 32 GB RAM nodes each for Web/App Server, MySQL DB, and Elasticsearch  
> **Document Status:** Authoritative Architectural Standard & Living Engineering Blueprint (V5.2 Enterprise Master Standard)  
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
         │     L1 WORKING RAM      │       │     L2 PERSISTENCE      │       │   CLIENT QUERY COMPUTE  │
         │   (ClientMemoryDB)      │       │    (ClientEntityDB)     │       │   (cl-data-worker.js)   │
         │ • Max 25 Active Graphs  │       │ • Bounded IndexedDB v2  │       │ • Stateless Web Worker  │
         │ • P95 < 5ms State Lookups│      │ • Adaptive LRU Eviction │       │ • Max 50MB Worker Heap  │
         │ • Ephemeral WASM Blobs  │       │ • Block-Before-Delete   │       │ • Off-Thread Filtering  │
         │ • Data Sensitivity Tier │       │ • Cache Poisoning Guard │       │ • Structured Clones     │
         └────────────┬────────────┘       └────────────┬────────────┘       └────────────┬────────────┘
                      │                                 │                                 │
                      └─────────────────────────────────┼─────────────────────────────────┘
                                                        │
                                                        ▼
                                       ┌──────────────────────────────────┐
                                       │   SINGLE SYNCHRONIZATION AUTH    │
                                       │       (SyncSupervisor)           │
                                       │   • Sole Authority for Reconcile │
                                       │   • Stream Cursor Tracker        │
                                       │   • Single-Flight Deduplication  │
                                       │   • Adaptive Event Coalescing    │
                                       └────────────────┬─────────────────┘
                                                        │
                                  ┌─────────────────────┴─────────────────────┐
                                  │ (Lossy Invalidation Hint)                 │ (Authoritative Reconcile)
                                  ▼                                           ▼
                   ┌──────────────────────────────┐            ┌──────────────────────────────┐
                   │   LARAVEL REVERB WEBSOCKET   │            │   DELTA-SYNC & ETAG API      │
                   │   • Notification Hint Only   │            │   • Chunked Cursors (limit)  │
                   │   • Jittered Reconnect Storm │            │   • Idempotent Delta Events  │
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
                 │ • 100% JDCR Query Reads     │                 │ • Strict CUD Authority      │
                 │ • Atomic CAS Version Guard  │                 │ • Transactional Outbox + DLQ│
                 │ • Zero-Downtime Alias Swap  │                 │ • DB::transaction() Scope   │
                 └─────────────────────────────┘                 └─────────────────────────────┘
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
```

---

## 2. The Strict Ontological Contract: Truth vs. Projection vs. Cache

To eliminate architectural ambiguity across development teams, JDCR strictly defines the authoritative role of every tier in the system:

| Layer | System Component | Authoritative Role | Invariant Rule |
|---|---|---|---|
| **L4** | **MySQL Database** | **Transactional Truth (System of Record)** | Absolute authority for business writes. Enforces ACID constraints and tenant boundaries. Zero reads on JDCR interactive read paths after read model is initialized. |
| **Log**| **Transactional Outbox** | **Durable Change Log** | Written in the exact same transaction as business data. Guarantees at-least-once ordered propagation to projections. Produces all required events atomically. |
| **L3** | **Elasticsearch Cluster** | **Canonical Query Projection** | Denormalized read projection serving 100% of JDCR interactive query reads. Never a write authority. Enforces atomic version CAS guards. Zero-downtime alias rebuilds. |
| **Sync**| **Thin Delta API** | **Authoritative Reconciliation Mechanism** | The sole mechanism for reconciling client state with truth. Supports chunked cursors (`?after=X&limit=500`) and incremental HTTP 410 resync. |
| **Hint**| **Laravel Reverb** | **Real-Time Notification Hint (Lossy)** | Emits wake-up invalidation hints to `SyncSupervisor`. Never assumed to be authoritative; frames may be dropped without consequence. |
| **L2** | **IndexedDB v2** | **Bounded Local Persistent Replica** | Local client cache bounded by adaptive LRU quotas and PII sensitivity tiers. Untrusted until validated. |
| **L1** | **Browser Working RAM** | **Active Working State** | High-performance pointer cache holding active drawer shell and open sub-tabs (max 25 active graphs). Cleared on session teardown. |

---

## 3. Distributed Consistency: Transactional Outbox, DLQ & Monotonic CAS

### 3.1 Outbox Ordering Semantics & Tenant Sequences
* **Tenant-Scoped Monotonic Ordering:** `mutation_version` is ordered sequentially per tenant (`tenant_mutation_sequence`).
* **Critical Ordering Rule:** `mutation_version` represents **committed event ordering, not guaranteed worker processing completion ordering**.
* Because background Horizon workers process concurrently, Event `102` may reach a worker slightly before Event `101`. The architecture handles this through monotonic projection guards.

### 3.2 Atomic Version Compare-and-Update (CAS) in Elasticsearch
Application-level `incoming > stored` checks are susceptible to concurrency race conditions between workers. Monotonicity must be enforced **atomically at the projection store (Elasticsearch)** using **External Versioning (`version_type=external_gte`)** or conditional Lucene CAS scripts:
```json
POST /ats_client_list/_update/1089?if_primary_term=X&if_seq_no=Y
{
  "script": {
    "source": "if (ctx._source.entity_version >= params.incoming_version) { ctx.op = 'noop'; } else { ctx._source.putAll(params.data); ctx._source.entity_version = params.incoming_version; }",
    "params": {
      "incoming_version": 743,
      "data": { "company_name": "Acme Global", "status": 1 }
    }
  }
}
```
* **Invariant:** *The monotonic version check MUST be enforced atomically at the projection store, not merely in application code. Older entity versions can never overwrite newer versions.*

### 3.3 Formal Versioned Tombstone Protocol
Entity deletions must never disappear silently or re-materialize via out-of-order events:
```json
{
  "event_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d",
  "tenant_id": 42,
  "entity_type": "client",
  "entity_id": 1089,
  "event_type": "ClientDeleted",
  "operation": "delete",
  "entity_version": 744,
  "mutation_version": 918300,
  "schema_version": 2,
  "occurred_at": "2026-10-26T15:20:00Z",
  "actor_id": 991,
  "correlation_id": "JERO-TRACE-98124",
  "tombstone": true,
  "payload": {
    "deleted_at": "2026-10-26T15:20:00Z"
  }
}
```
* **Tombstone Invariant:** *Tombstones MUST carry an authoritative `entity_version`. Any incoming event with $\text{entity\_version} \le \text{tombstone.entity\_version}$ MUST be discarded across all layers (ES, Outbox, Delta Sync, IDB, RAM).*

---

## 4. The 5-Part Tracing/Version Ontology & Chunked Delta Sync

### 4.1 The 5-Part Version & Tracing Ontology Defined
1. **`correlation_id` (String / UUID):** Distributed trace ID passed from Browser Request $\to$ Laravel Gateway $\to$ MySQL Outbox $\to$ Horizon $\to$ Elasticsearch $\to$ Reverb $\to$ Browser. Allows end-to-end auditability.
2. **`event_id` (UUID):** Unique immutable identifier of a discrete outbox log entry.
3. **`mutation_version` / `stream_cursor` (Monotonic BigInt):** Scoped to the entire tenant event stream (e.g. Tenant #42 event log sequence `918273`). Used for **Delta Sync Reconnection (`/delta-sync?after=918273`)**.
4. **`entity_version` (Integer):** Scoped to an individual entity (e.g. Client #1089 revision `742`). Monotonically increments on every row update in MySQL. Used for **Optimistic Concurrency Control (`HTTP 409 VERSION_CONFLICT`)**.
5. **`projection_version` (Integer):** Reflects the latest `entity_version` successfully indexed into Elasticsearch.

### 4.2 Chunked Delta Sync Protocol & Idempotency Guarantee
When a reconnecting browser requests missing deltas:
```http
GET /service_company/clients/api/v2/delta-sync?after=918273&limit=500
```
Server Response:
```json
{
  "from_cursor": 918273,
  "to_cursor": 918772,
  "has_more": true,
  "next_cursor": 918773,
  "events": [ /* max 500 events */ ]
}
```
* **Idempotency Guarantee:** Receiving duplicate delta packets produces zero state corruption.
* **Monotonic Protection:** Incoming deltas with $\text{entity\_version} \le \text{local.entity\_version}$ are safely discarded.
* **Cursor Expiry (`HTTP 410 Gone`):** If the client requested cursor has expired, the runtime invalidates only the affected entity partition, requests an authoritative paginated snapshot, checkpoints the new cursor, and resumes streaming.

---

## 5. Single Synchronization Authority (`SyncSupervisor`) & Idle Recovery

### 5.1 The Single Synchronization Authority Invariant
* **The Invariant:** *No component, view, tab, or event handler except `SyncSupervisor` may directly initiate Delta Sync or network reconciliation.*
* **Topology:**
  $$\text{Reverb} \mid \text{BroadcastChannel} \mid \text{Visibility} \mid \text{Network Resume} \xrightarrow{\text{Hints Only}} \mathbf{SyncSupervisor} \xrightarrow{\text{Decision}} \text{Delta Sync}$$

```
                   SyncSupervisor (Single Authority)
                                 │
                   ┌─────────────┼──────────────┐
                   ▼             ▼              ▼
                Reverb    BroadcastChannel   Lifecycle Events
                   │             │              │
                   └─────────────┼──────────────┘
                                 ▼
                           SyncSupervisor
                                 │
                                 ▼ (Deduplicated & Coalesced)
                             Delta Sync
                                 │
                                 ▼
                            State Store
```

### 5.2 The Formal Idle-Tab State Machine
To permanently eliminate the defect where switching tabs or waking from 10-minute idle produces blank drawers:

```
                      FORMAL IDLE-TAB LIFECYCLE STATE MACHINE
                      
                                  ┌──────────────┐
                                  │    ACTIVE    │ (Recruiter interacting normally)
                                  └──────┬───────┘
                                         │ Inactivity > 5 min
                                         ▼
                                  ┌──────────────┐
                                  │     IDLE     │ (Timers throttled, Reverb listening)
                                  └──────┬───────┘
                                         │ Inactivity > 10 min / OS Sleep
                                         ▼
                                  ┌──────────────┐
                                  │  SUSPENDED   │ (Sockets idle, timers frozen)
                                  └──────┬───────┘
                                         │ Unseen outbox events occur
                                         ▼
                                  ┌──────────────┐
                                  │    STALE     │ (Local cache marked pending verify)
                                  └──────┬───────┘
                                         │ User clicks tab (visibilitychange / focus)
                                         ▼
                                  ┌──────────────┐
                                  │ FOCUS_RETURN │ (Check auth, tenant, cursor)
                                  └──────┬───────┘
                                         │ SyncSupervisor fires single-flight sync
                                         ▼
                                  ┌──────────────┐
                                  │ RECONCILING  │ (Delta sync executes with limit=500)
                                  └──────┬───────┘
                                         │ Delta merged / Verified
                                         ▼
                                  ┌──────────────┐
                                  │    FRESH     │ (Drawer interactive, verified state)
                                  └──────────────┘

  ┌────────────────────────────────────────────────────────────────────────┐
  │ HARD INVARIANT:                                                        │
  │ A drawer must NEVER remain permanently mounted against an unknown or   │
  │ unverified authorization/synchronization state.                        │
  └────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Multi-Tab Synchronization & Reference Workload Model

### 6.1 Multi-Tab Coordination via `BroadcastChannel`
* `BroadcastChannel('JERO_TAB_SYNC')` is strictly a **Local Invalidation Hint**, never an authoritative consistency mechanism.
* When Tab A mutates Client #1089, it emits a local hint. Tab B invalidates its in-memory working state and yields control to `SyncSupervisor`. If Tab B was suspended or throttled, Delta Sync provides the authoritative reconciliation upon tab focus.

### 6.2 The JDCR Reference Workload Model (10,000 Concurrent Users)
Grounded in empirical capacity engineering:
* **Session Distribution:** 10,000 concurrent recruiter sessions: $70\%$ idle/monitoring, $20\%$ active browsing, $8\%$ filtering/searching, $2\%$ active mutations.

| Metric | Steady State | Burst State (Campaigns / Blasts) | Recovery Storm (Post-Outage) |
|---|---|---|---|
| **Gateway API Requests** | $85\text{ req/sec}$ | $500\text{ req/sec}$ | Throttled via Jittered Backoff |
| **Reverb Messages** | $150\text{ msg/sec}$ | $2,000\text{ msg/sec}$ | Broadcast Fanout |
| **MySQL Writes** | $12\text{ writes/sec}$ | $100\text{ writes/sec}$ | Queued via Horizon Outbox |
| **Client Drawer Opens** | $35\text{ opens/sec}$ ($>95\%$ local) | $120\text{ opens/sec}$ ($>95\%$ local) | 0% MySQL Load |
| **Reconnection Storm** | N/A | N/A | 10k clients reconnecting (Jitter $1-30\text{s}$) |

---

## 7. Browser Runtime Memory Budgets & Data Sensitivity

### 7.1 Browser Runtime Memory Governance
To prevent long-lived ATS tabs from suffering memory exhaustion:
* **L1 RAM Active Graphs:** Bounded to **$\le 25$ active client entities**.
* **Client Query Compute Worker:** Heap bounded to **$\le 50\text{MB}$**.
* **Active DOM Elements in Drawer:** Bounded to **$\le 1,500$ DOM nodes**.
* **WASM Memory:** Ephemeral, bounded to **$\le 64\text{MB}$**, with mandatory `.revokeObjectURL()` and buffer release.
* **Memory Pressure Controller:** Uses `navigator.deviceMemory` and performance heuristics to trigger staged eviction ($\text{L1 Evict} \to \text{L2 IDB Evict} \to \text{Worker GC}$).

### 7.2 The 4-Tier Data Classification Matrix

| Tier | Sensitivity Level | Entity Domains | Permitted Storage | Retention & Protection Policy |
|---|---|---|---|---|
| **Tier A** | **Highly Sensitive** | Resume Binaries, Compensation/Salary, Billing Terms, PII (Phone/Email) | **L1 Working RAM Only** (Never written to disk/IDB) | Ephemeral Blob URLs (`URL.revokeObjectURL()`); wiped on teardown |
| **Tier B** | **Sensitive** | Candidate Full Profile, Interview Feedback, Offer Terms | **L1 RAM + Bounded IDB** | Tenant-scoped store; 5-min TTL; optional Web Crypto encryption |
| **Tier C** | **Operational** | Client Name, Job Titles, Requisition Status, Funnel Counts | **L1 RAM + Bounded IDB** | Adaptive LRU eviction; 24-hr TTL; event-driven invalidation |
| **Tier D** | **Reference** | Country Lists, State/City Facets, Standard Skill Taxonomies | **L2 Persistent IDB** | 7-day TTL; background ETag refresh |

---

## 8. Elasticsearch Disaster Recovery: Zero-Downtime Atomic Alias Rebuild

In enterprise recruitment, an Elasticsearch cluster failure or index mapping corruption must not bring down the ATS.

### 8.1 The Zero-Downtime Rebuild State Machine
$$\text{NORMAL} \longrightarrow \text{REBUILDING} \longrightarrow \text{CATCHING\_UP} \longrightarrow \text{READY} \longrightarrow \text{ATOMIC\_ALIAS\_SWITCH}$$

```
                               ES DISASTER RECOVERY PROTOCOL
                               
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 1. CREATE NEW INDEX: ats_client_list_v20261026                                         │
  │    • Define optimized schema and mappings without touching the active production alias │
  └──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                             │
                                             ▼
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 2. BULK SEEDING FROM MYSQL TRANSACTIONAL TRUTH                                         │
  │    • Stream entity snapshots from MySQL tables into the new index                      │
  │    • Client runtimes continue operating from L1/L2 cache in read-only degraded mode     │
  └──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                             │
                                             ▼
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 3. REPLAY OUTBOX EVENT LOG (CATCHING UP)                                               │
  │    • Horizon workers replay outbox events created during bulk seed                     │
  │    • Enforce projection_version == entity_version                                      │
  └──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                             │
                                             ▼
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 4. ATOMIC ZERO-DOWNTIME ALIAS SWAP                                                     │
  │    POST /_aliases                                                                      │
  │    {                                                                                   │
  │      "actions": [                                                                      │
  │        { "remove": { "index": "*", "alias": "ats_client_list" } },                     │
  │        { "add":    { "index": "ats_client_list_v20261026", "alias": "ats_client_list" }}│
  │      ]                                                                                 │
  │    }                                                                                   │
  └────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 9. Observability: JDCR Control Plane & The Ultimate KPI

### 9.1 The Ultimate KPI: JDCR Local Hit Ratio
$$\text{JDCR Local Hit Ratio} = \frac{\text{L1 RAM Hits} + \text{L2 IndexedDB Hits}}{\text{Total Read Interactions}} \times 100\%$$
* **Launch SLO Target:** $> 95\%$ of drawer tab interactions execute with 0 network calls.
* **Mature Production Target:** $> 99\%$ for already-hydrated entity sessions.

### 9.2 First-Class Control Plane Metrics

| Metric Name | Measurement Scope | Production SLO / Alert Threshold |
|---|---|---|
| **`projection_lag`** | `entity_version - projection_version` | **Warning:** $\text{P95} > 2\text{s}$ for $> 1\text{m}$; **Critical:** $\text{P95} > 10\text{s}$ |
| **`outbox_pending_count`** | Horizon queue depth of unprojected events | Alert if pending count $> 500$ |
| **`outbox_oldest_pending_age`**| Timestamp of oldest unprocessed event | Critical Alert if oldest age $> 10\text{ seconds}$ |
| **`dlq_event_count`** | Total events in Dead Letter Queue | Critical Alert on **New Production DLQ Event** |
| **`gateway_processing_time`** | Octane PHP worker execution latency | $\text{P95} < 10\text{ms}$ |
| **`end_to_end_api_latency`** | Total network roundtrip over WAN | $\text{P95} < 200\text{ms}$ |
| **`shell_visibility_latency`**| Drawer shell render in browser | $\text{P95} < 50\text{ms}$ |
| **`content_visible_latency`** | Primary tab content visible | $\text{P95} < 100\text{ms}$ |

---

## 10. Concrete Implementation Roadmap for Service Company Clients

### Phase 0: Baseline & Contract Freeze (Pre-Implementation Verification)
1. **Benchmark Current Baseline:** Measure current drawer HTML payload size (KB), latency (TTI), MySQL queries, ES queries, and server CPU/RAM.
2. **Contract Freeze:** Formalize Schema 2.0 JSON specifications and versioning rules.
3. **Rollback Strategy:** Establish feature-flag toggles allowing instant rollback to Blade drawer rendering if required.

### Phase 1: Transactional Outbox & Thin JSON API (Backend)
1. Create migration for `tenant_outbox_events` and `tenant_outbox_dead_letter` tables.
2. Implement `OutboxEventDispatcherJob` in Laravel Horizon with exponential retry, DLQ routing, and monotonic atomic projection CAS guard.
3. Implement `GET /service_company/clients/api/v2/{id}/graph` enforcing the **JERO Universal API Security Pipeline**.
4. Implement `GET /service_company/clients/api/v2/delta-sync?after={cursor}&limit=500` with chunking and `HTTP 410` support.

### Phase 2: Client Local Storage Engine (`cl-entity-db.js`)
1. Create `public/assets/service_company/client/cl-entity-db.js`:
   * Adaptive IndexedDB v2 repository with LRU eviction and migration handlers.
   * Ephemeral memory handling for WASM PDF resumes (zero disk writes).
   * Cache poisoning protection and Block-Before-Delete session teardown.

### Phase 3: Incremental Template Renderer (`cl-drawer-view.js`)
1. Build `public/assets/service_company/client/cl-drawer-view.js`:
   * Client-side incremental renderers for Shell, Overview, Jobs, Candidates, Interviews, Offers, Joining.
   * Centralized Event Delegation (zero inline `onclick`).
   * Information Hierarchy compliance calibrated for 15.6" screens (`1366×768` and `1920×1080`).

### Phase 4: Reactive State Machine & Multi-Tab Wiring
1. Upgrade `ClientStateManager` in `index.blade.php`:
   * Wire `SyncSupervisor` as single synchronization authority.
   * Implement the formal Idle-Tab Lifecycle State Machine.
   * Single-Flight request deduplication and Adaptive Event Coalescing supervisor.
   * Multi-tab `BroadcastChannel('JERO_TAB_SYNC')` coordination.
2. Wire Laravel Reverb channel:
   * Listen for `ClientEntityMutated` events.
   * Deliver invalidation hints exclusively to `SyncSupervisor`.

### Phase 5: Verification & Zero-Strain Benchmark
1. Automated network throttling test (simulated 10-minute idle sleep, offline-to-online recovery).
2. Concurrency load test targeting 10,000 simulated users under steady and burst workloads.
3. Compare results against Phase 0 baselines to quantitatively prove $> 95\%$ JDCR Local Hit Ratio.

---

## 11. The 20 Non-Negotiable Invariants of JDCR (V5.2 Master Standard)

1. **Invariant 1:** MySQL is the sole transactional authority.
2. **Invariant 2:** Elasticsearch is the canonical query projection, never a write authority.
3. **Invariant 3:** Reverb is a notification hint, never the authoritative consistency mechanism.
4. **Invariant 4:** Browser-local interactions MUST NOT perform network/database-server reads and MUST provide visual response within defined interaction SLOs.
5. **Invariant 5:** Every committed business mutation MUST produce all required outbox events atomically within the same transaction, with no event published before transaction commit.
6. **Invariant 6:** Every projection operation must be strictly idempotent.
7. **Invariant 7:** Monotonic version checks MUST be enforced atomically at the projection store; older entity versions can never overwrite newer versions.
8. **Invariant 8:** Deleted entities must propagate through durable versioned tombstone events; older events must be discarded.
9. **Invariant 9:** Tenant boundary isolation is enforced server-side on every request.
10. **Invariant 10:** Local client cache validation never grants authorization.
11. **Invariant 11:** Offline writes are strictly disabled in JDCR V1.
12. **Invariant 12:** Every cache tier must enforce explicit TTL, LRU eviction, and data sensitivity policies.
13. **Invariant 13:** Every browser network request must have cancellation (`AbortController`) and monotonic token protection.
14. **Invariant 14:** Every distributed mutation must carry a global correlation ID for end-to-end tracing.
15. **Invariant 15:** Every reconnecting browser must deterministically reconcile via chunked Delta Sync or `HTTP 410` full resync.
16. **Invariant 16:** API schema incompatibility triggers controlled client cache invalidation.
17. **Invariant 17:** No dynamic user content may ever be injected into the DOM without sanitization.
18. **Invariant 18:** Zero inline JavaScript event handlers (`onclick`) permitted in the UI runtime.
19. **Invariant 19:** Resume binaries are never intentionally persisted in browser storage.
20. **Invariant 20:** No performance claim is considered valid without empirical measurement against Phase 0 baselines.

---

*This document is the authoritative standard for the JERO Distributed Client Runtime (JDCR). All subsequent development on the Service Company Client module must adhere strictly to these 20 invariants.*
