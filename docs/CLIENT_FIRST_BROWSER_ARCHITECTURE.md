# JERO Distributed Client Runtime (JDCR): The God-Level Blueprint for 0% Server Rendering, Zero-Strain ATS Drawers, and Real-Time Local Execution

> **Platform:** JERO.WORKS — Enterprise B2B Multi-Tenant ATS & Talent Trading Marketplace  
> **System Paradigm:** **JERO Distributed Client Runtime (JDCR)**  
> **Target Scale:** 10,000 Concurrent Users per page with **0% Server / MySQL / Elasticsearch Strain on Rendering & Interaction**  
> **Core Architecture:** Transactional Outbox CQRS (MySQL Write Authority -> Outbox Log -> Idempotent ES Read Projection -> Reverb Invalidation Hint -> Browser Distributed Runtime)  
> **Hardware Profile:** Dedicated 32 GB RAM nodes for Web/App Server, MySQL DB, and Elasticsearch  
> **Document Status:** Authoritative Architectural Standard & Living Engineering Blueprint (V4.3 Enterprise Standard)  
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
                               │   • Calibrated for 15.6" Screens (1366 & 1920)   │
                               └────────────────────────┬─────────────────────────┘
                                                        │
                      ┌─────────────────────────────────┼─────────────────────────────────┐
                      ▼                                 ▼                                 ▼
         ┌─────────────────────────┐       ┌─────────────────────────┐       ┌─────────────────────────┐
         │     L1 WORKING RAM      │       │     L2 PERSISTENCE      │       │   COMPUTE ENGINE (OLAP) │
         │   (ClientMemoryDB)      │       │    (ClientEntityDB)     │       │   (cl-data-worker.js)   │
         │ • Active Working Set    │       │ • Bounded IndexedDB     │       │ • Stateless Web Worker  │
         │ • 0ms Pointer Lookups   │       │ • LRU Eviction (100 cap)│       │ • Off-Thread Filtering  │
         │ • Ephemeral WASM Blobs  │       │ • PII & Session Purge   │       │ • Facet & Sort Compute  │
         └────────────┬────────────┘       └────────────┬────────────┘       └────────────┬────────────┘
                      │                                 │                                 │
                      └─────────────────────────────────┼─────────────────────────────────┘
                                                        │
                                                        ▼
                                       ┌──────────────────────────────────┐
                                       │     SYNCHRONIZATION SUBSYSTEM    │
                                       │   • Monotonic Version Tracker    │
                                       │   • Optimistic Concurrency Guard │
                                       │   • AbortController Supervisor   │
                                       └────────────────┬─────────────────┘
                                                        │
                                  ┌─────────────────────┴─────────────────────┐
                                  │ (Notification Hint)                       │ (Authoritative Reconcile)
                                  ▼                                           ▼
                   ┌──────────────────────────────┐            ┌──────────────────────────────┐
                   │   LARAVEL REVERB WEBSOCKET   │            │     THIN DELTA-SYNC API      │
                   │   • Lossy-Tolerant Broadcast │            │   • Monotonic Version Check  │
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
                                       │      LARAVEL OCTANE GATEWAY      │
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
                 │ • Idempotent Outbox Sink    │                 │ • Transactional Outbox Log  │
                 │ • 0% MySQL Reads on Web/API │                 │ • DB::transaction() Scope   │
                 └─────────────────────────────┘                 └─────────────────────────────┘
══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
```

---

### 1.2 The Forensic Answers to Core Questions

#### 1. "Is everything browser-based right now? Is local DB working?"
* **Current Reality:** **No, not everything is browser-based yet.**
  * **What IS browser-based today:** The Client Listing table search, sort, and pagination filters utilize an off-main-thread Web Worker (`cl-data-worker.js`) combined with `ClientLocationDB` (IndexedDB) for caching static country/state reference facets. In addition, `ClientStore` holds listing data in memory with ETag-based HTTP 304 caching.
  * **What IS NOT browser-based today (The Culprit):** The **Client Drawer** and its **Sub-Tabs** (Overview, Jobs, Candidates, Interviews, Offers, Joining). When a recruiter clicks a client card, the browser executes an AJAX call (`/clients/viewmore/{id}` or `/clients/dviewmore/{id}`) which forces Laravel Octane/FrankenPHP to compile and render a massive server-side Blade template (`show.blade.php` or `dviewMore.blade.php`, typically **440 KB to 800 KB of raw HTML**), streaming it over TCP to be dumped into the DOM via `$(drawerBody).html(response.view)`.
  * **Local DB Status:** IndexedDB is currently limited to reference lookup data (`ClientLocationDB`). The rich client entity, jobs pipeline, candidate stages, and interview trackers are **not yet persisted into bounded browser IndexedDB or reactive client stores**. Every drawer opening still makes redundant server requests.

#### 2. "What is the use case of 'FrankenPHP/Octane drops idle TCP connections after 60–75s'? Why load unnecessary on the server if everything can be loaded locally?"
* **Why the TCP drop happened:** FrankenPHP and Octane maintain persistent worker processes and HTTP/2 or HTTP/1.1 keep-alive TCP connections. By default, high-concurrency web servers configure an idle keep-alive timeout between 60s and 75s to release inactive sockets and prevent socket exhaustion when handling thousands of clients.
* **Why the Heartbeat was built:** The idle session heartbeat (`/clients/session-heartbeat`) was added as a defensive bandage because after a recruiter left the browser idle for 10 minutes, the underlying TCP connection was dropped, PHP session garbage collection or CSRF expiration occurred, and the next click on a drawer produced a stalled request or HTTP 419/500 error.
* **The JDCR Solution:** In JDCR, the heartbeat **becomes completely unnecessary**. Because entity data and UI templates execute inside the browser runtime (RAM + bounded IndexedDB), **the recruiter never needs to talk to FrankenPHP/Octane just to view a client or switch drawer tabs**. The entire UI renders at 0ms latency with 0% server network traffic, completely eliminating idle session crashes.

#### 3. "Can we remove dependency on FrankenPHP/Octane? What's your thoughts?"
* **Can Octane be removed from Drawer Rendering & Page Interaction? YES, 100%.**
  * The server should NEVER assemble or render HTML Blade views for drawers, modals, tab switches, filters, or entity cards.
  * 100% of the UI assembly, HTML templating, and tab state management must execute in the browser using the user's RAM and CPU.
* **Can Octane/FrankenPHP be removed from the Entire System? NO, and here is why (Security & Data Integrity Boundary):**
  1. **Elasticsearch Cannot Be Publicly Exposed:** Browsers cannot speak directly to an Elasticsearch cluster. Exposing Elasticsearch ports (`9200`) to the public internet or client-side JavaScript allows any user to inspect network requests, extract cluster credentials, execute arbitrary cluster delete commands (`DELETE /_all`), or bypass tenant data boundaries (`company_id`).
  2. **MySQL Write Authority & ACID Transactions:** Any write operation (creating a client, hiring a candidate, moving an interview stage, deducting coins) requires strict transactional integrity (`DB::transaction()`), multi-tenant row-locking, and server-side authorization checks.
  3. **The True Role of FrankenPHP/Octane:** Octane must be demoted to an **Ultra-Thin, Zero-Rendering CQRS Gateway**:
     * **For Reads:** It acts strictly as an authenticated, ultra-fast JSON proxy that queries Elasticsearch and streams raw JSON (< 3ms response, < 4KB payload vs 440KB HTML).
     * **For Writes:** It executes atomic MySQL writes, logs outbox events, and broadcasts real-time mutations.
     * **Zero Server-Side HTML Rendering:** Octane never touches Blade for interactive ATS workflows.

---

## 2. Distributed Consistency: Transactional Outbox & Authority Hierarchy

### 2.1 The Distributed Flaw of Direct Synchronous Sync
Synchronous chaining (`MySQL -> Elasticsearch -> Reverb`) is fundamentally flawed in distributed systems:
```
  MySQL UPDATE SUCCESS  ──►  PHP Worker Times Out / OOM  ──►  ES Update FAILS  ──►  Reverb FAILS
  (Committed to DB)                                          (Index Stale)         (Client Blind)
```
In this scenario, Elasticsearch is out of sync with MySQL, and connected browsers never receive invalidation notices. The system's "zero stale ATS data" promise is broken.

### 2.2 The God-Level Solution: Transactional Outbox + Idempotent Projections
To guarantee 100% consistency under all failure modes, JERO implements the **Transactional Outbox Pattern**:

```
                       TRANSACTIONAL OUTBOX PIPELINE
                       
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 1. LARAVEL WRITE API (ATOMIC DB TRANSACTION)                                           │
  │    DB::transaction(function() {                                                        │
  │        $client->update($validatedData);                                                │
  │        $newVersion = $client->incrementVersion(); // Monotonic Version                 │
  │        OutboxEvent::create([                                                           │
  │            'tenant_id'         => $companyId,                                          │
  │            'entity_type'       => 'client',                                            │
  │            'entity_id'         => $client->id,                                         │
  │            'event_type'        => 'ClientEntityMutated',                               │
  │            'mutation_version'  => $newVersion,                                         │
  │            'payload'           => json_encode($delta),                                 │
  │            'status'            => 'pending'                                            │
  │        ]);                                                                             │
  │    });                                                                                 │
  └──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                             │ COMMIT (Guaranteed MySQL Atomicity)
                                             ▼
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 2. ASYNCHRONOUS OUTBOX DISPATCHER (HORIZON / REDIS QUEUE)                              │
  │    • Idempotent Projection to Elasticsearch Read Cluster                               │
  │    • Broadcasts Invalidation Hint to Laravel Reverb WebSocket                          │
  │    • Marks Outbox Event as 'dispatched'                                                │
  └───────────────────────┬──────────────────────────────────────────┬─────────────────────┘
                          │                                          │
                          ▼                                          ▼
  ┌──────────────────────────────────────────────┐ ┌───────────────────────────────────────┐
  │ 3. ELASTICSEARCH READ PROJECTION             │ │ 4. LARAVEL REVERB WEBSOCKET           │
  │    • Denormalized Index (ats_client_list)    │ │    • Broadcasts Invalidation Hint     │
  │    • projection_version == mutation_version  │ │    • Lossy-Tolerant Delivery          │
  └──────────────────────────────────────────────┘ └───────────────────┬───────────────────┘
                                                                       │
                                                                       ▼
                                                   ┌───────────────────────────────────────┐
                                                   │ 5. REVERB RECEIVER (NOTIFICATION ONLY)│
                                                   │    • Flags local entity as STALE      │
                                                   │    • Triggers Delta-Sync Reconcile    │
                                                   └───────────────────────────────────────┘
```

### 2.3 Clarifying the Authority Hierarchy
WebSockets are inherently lossy from a client's perspective (WiFi sleep, tab suspension, network blips). Therefore, the system hierarchy is strictly formalized as:

1. **MySQL (L4):** **Transactional Authority (System of Record)**. The sole source of truth for business writes.
2. **Transactional Outbox:** **Monotonic Event Log**. Guarantees at-least-once delivery to read stores.
3. **Elasticsearch (L3):** **Authoritative Read Projection**. Serves 100% of read queries.
4. **Version API (`/delta-sync`):** **Consistency & Reconciliation Engine**. Guarantees state alignment.
5. **Laravel Reverb:** **Real-Time Notification Hint**. Informs the browser that a mutation occurred; NEVER treated as the data authority.
6. **IndexedDB (L2):** **Bounded Local Read Replica**. Persists active entities with strict LRU eviction.
7. **Browser RAM (L1):** **Active Working Set**. Sub-millisecond UI rendering for active drawer tabs.

---

## 3. Global Multi-Dimensional Versioning & Conflict Handling

### 3.1 Versioned Envelope Specification
Every entity exchanged between Server, Elasticsearch, and Client carries strict monotonic metadata:
```json
{
  "schema_version": "2.0",
  "tenant_id": 42,
  "entity_type": "client",
  "entity_id": 1089,
  "entity_version": 742,
  "projection_version": 742,
  "mutation_version": 918273,
  "updated_at": "2026-10-26T14:42:21Z",
  "data": {
    "company_name": "Acme Global",
    "status": 1,
    "active_jobs_count": 12
  }
}
```

### 3.2 Optimistic Concurrency & Mutation Conflict Resolution
To prevent the "Lost Update Problem" when two recruiters edit a client or move candidate stages concurrently:
1. When Recruiter A submits an edit:
   ```http
   POST /service_company/clients/1089/update
   Headers: If-Match-Version: 742
   ```
2. The Server Write Gateway validates:
   ```php
   if ($currentEntity->entity_version != $request->header('If-Match-Version')) {
       return response()->json([
           'error'           => 'VERSION_CONFLICT',
           'current_version' => $currentEntity->entity_version,
           'message'         => 'This entity was modified by another recruiter. Please review changes.'
       ], 409);
   }
   ```
3. The Browser UI displays a **Conflict Reconciliation Modal** allowing the recruiter to inspect the updated fields rather than silently clobbering another user's work.

### 3.3 Visual "Stale Data / Syncing" States
The UI must never silently present unconfirmed local data as authoritative. Every view surface supports three explicit sync states:
* `SYNCING`: Subtle pulse indicator ("Updating latest data...").
* `UP_TO_DATE`: Clean display with verified timestamp.
* `VERSION_CONFLICT`: Amber badge prompting one-click merge or rehydration.

---

## 4. Lazy Sub-Tab Hydration vs. Monolithic Graph Preloading

Preloading an entire client graph (Overview + 50 Jobs + 1,000 Candidates + 200 Interviews + 50 Offers + Contacts) on first drawer open is an anti-pattern that wastes megabytes of bandwidth and browser RAM.

### 4.1 Staged Progressive Hydration
```
  Recruiter Clicks Client Card
               │
               ▼
  ┌──────────────────────────────────────────────────────────────┐
  │ STAGE 1: IMMEDIATE SHELL & OVERVIEW (< 2KB JSON, 0ms if RAM) │
  │ • Renders Tier 1 Toolbar, Tier 2 Sub-Tabs, Overview Card     │
  │ • Recruiter immediately sees Company Info, Contacts, Status  │
  └──────────────────────────────┬───────────────────────────────┘
                                 │
             ┌───────────────────┴───────────────────┐
             │ Recruiter Clicks 'Jobs' Sub-Tab       │
             ▼                                       ▼
  ┌──────────────────────────────────────┐ ┌───────────────────────────────────────┐
  │ STAGE 2: LAZY SUB-TAB HYDRATION      │ │ STAGE 3: LOCAL PERSISTENCE            │
  │ • Checks L1 RAM (Found? 0ms Render)  │ │ • Once loaded, tab dataset persists   │
  │ • Checks L2 IndexedDB (1ms Render)   │ │   in RAM & bounded IndexedDB          │
  │ • Not found? Fetches thin JSON delta │ │ • Subsequent switches to 'Jobs' are   │
  │   from Thin Gateway (< 3ms)          │ │   INSTANT (0ms)                       │
  └──────────────────────────────────────┘ └───────────────────────────────────────┘
```

---

## 5. Storage Security, PII Policy & Bounded IndexedDB Eviction

### 5.1 Local Storage Security & PII Protection
ATS data contains sensitive Personally Identifiable Information (candidate phone numbers, emails, salaries, interview ratings, client contracts) subject to GDPR, SOC2, and DPDP regulations.

#### 1. Zero Persistent Resume Binaries
* **Strict Rule:** Raw PDF resume binaries and rendered vector blobs must **NEVER be persisted in IndexedDB or localStorage**.
* **Execution Flow:**
  `Download Stream -> In-Memory Ephemeral Blob URL (URL.createObjectURL) -> WASM Processing in User RAM -> User Download / View -> URL.revokeObjectURL() -> Immediate Garbage Collection`.

#### 2. Atomic Multi-Tenant Logout & Session Teardown
When a user logs out, switches organizations, or experiences a session timeout (HTTP 401):
```javascript
window.ClientSecurityManager = {
    purgeLocalData() {
        // 1. Evacuate In-Memory Working Set
        ClientMemoryDB.clear();
        
        // 2. Destroy IndexedDB Tenant Object Stores
        indexedDB.deleteDatabase('Jero_ClientEntityDB');
        
        // 3. Purge Service Worker Dynamic Caches
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => { if (name.startsWith('jero-dynamic-')) caches.delete(name); });
            });
        }
        
        // 4. Disconnect Real-Time WebSockets
        if (window.Echo) {
            window.Echo.disconnect();
        }
        
        console.log('[Security] Atomic purge executed. 0% sensitive ATS data remains on disk.');
    }
};
```

#### 3. Cryptographic Tenant & User Key Scoping
Local database keys are strictly isolated:
```text
tenant_{tenant_id}__user_{user_id}__{entity_type}__{entity_id}
```
A user switching between multiple organizations inside JERO can never read another organization's cached entities from IndexedDB.

---

### 5.2 Bounded Storage Quota & LRU Eviction Engine
An enterprise recruitment firm may manage 10,000 clients and 500,000 candidates. The browser must never attempt to store the entire ATS locally.

* **L1 Cache (Browser RAM):** Strictly holds the **Active Working Set** (currently open client drawer and its active sub-tabs).
* **L2 Cache (IndexedDB):** Bounded by a strict **Least Recently Used (LRU) Eviction Policy**:
  * **Max Cached Clients:** 100 most recently visited clients.
  * **Max Cached Jobs:** 50 active job requisitions per client.
  * **Max Cached Candidates:** Top 50 pipeline candidates per client.
  * **Time-to-Live (TTL):** Hard expiry of 7 days; soft TTL revalidated via delta-sync.

### 5.3 Authoritative Cache Policy Matrix

| Entity Domain | L1 RAM | L2 IndexedDB | Authoritative Source | Freshness / Invalidation Policy |
|---|---|---|---|---|
| **Client Profile / Header** | Yes | Yes (LRU max 100) | ES Projection | 60s soft TTL / Event-driven Invalidation |
| **Active Jobs Tab** | Yes | Yes (LRU max 50) | ES Projection | Event-driven (Outbox Event) |
| **Candidate Funnel Tab** | Yes | Limited (Top 50) | ES Projection | Event-driven (Outbox Event) |
| **Interviews / Offers Tab** | Yes | Limited (Recent) | ES Projection | Event-driven (Outbox Event) |
| **Analytics & KPIs** | Yes | **No (RAM only)** | ES Aggregations | 5-minute soft TTL |
| **User RBAC & Permissions**| Yes | **NO (Forbidden)** | Server Session Gateway | Strictly Session-Bound / Validated per Write |

---

## 6. Flexible UI Architecture: Information Hierarchy Standard

### 6.1 The Principle of Information Hierarchy
The platform strictly enforces an **Information Hierarchy Standard**, replacing rigid component constraints with semantic UI architecture:

```
  ┌────────────────────────────────────────────────────────────────────────┐
  │ TIER 1: PURE WHITE TOOLBAR CARD                                        │
  │ • #ffffff background, border 1px solid #e2e8f0, border-radius 10px     │
  │ • Interaction-specific search & filter bar with action buttons         │
  └───────────────────────────────────┬────────────────────────────────────┘
                                      │
  ┌───────────────────────────────────▼────────────────────────────────────┐
  │ TIER 2: PURE WHITE NAVIGATION / SUB-TABS CARD                          │
  │ • #ffffff background, border 1px solid #e2e8f0, border-radius 10px     │
  │ • Single active primary blue highlight (#009ef7)                       │
  └───────────────────────────────────┬────────────────────────────────────┘
                                      │
  ┌───────────────────────────────────▼────────────────────────────────────┐
  │ TIER 3: PRIMARY INFORMATION SURFACE (CONTEXT-AWARE SELECTION)          │
  │ • Clients / Requisitions: Card Stream + Discrete Sub-Cards             │
  │ • Candidate Pipeline: High-Density Data Grid OR Kanban Stage Board     │
  │ • Interview Tracker: Interactive Schedule Timeline / Calendar View     │
  │ • Analytics Tab: Vector Metrics Charts & Aggregation Cards             │
  └───────────────────────────────────┬────────────────────────────────────┘
                                      │
  ┌───────────────────────────────────▼────────────────────────────────────┐
  │ TIER 4: ACTIVITY & AUDIT STRIP                                         │
  │ • Last updated timestamp, recruiter attribution, status badges        │
  └────────────────────────────────────────────────────────────────────────┘
```

### 6.2 Interaction-Specific Timing Matrix
Debounce and throttle timings are calibrated per interaction type:
* **Tab Switch / Navigation:** **0ms (Immediate)**.
* **Filter Toggle / Pill Click:** **0ms (Immediate)**.
* **Pagination / Page Size:** **0ms (Immediate)**.
* **Dropdown Selection:** **0ms (Immediate)**.
* **Local In-Memory Text Search:** **150ms** (Maintains constant 60 FPS).
* **Remote Gateway Query Search:** **300ms** (Prevents server request flooding).

---

## 7. Web Worker & Incremental Template Rendering Engine

### 7.1 Web Worker as a Stateless Compute Engine
* **`cl-data-worker.js`** is strictly a **Compute Engine**, not a state authority or secondary database.
* **Responsibilities:** Multi-field filtering, fuzzy keyword matching, statistical aggregations, sorting arrays of 5,000+ candidates off the main thread.
* **Data Transfer:** Employs structured clones and transferable `ArrayBuffer` instances where large numerical datasets exist.

### 7.2 Client-Side Incremental Template Engine
The rendering pipeline is defined as a **Client-Side Incremental Template Engine**. It generates DOM fragments from pre-compiled template functions and patches changed nodes using element ID targeting:

```javascript
// High-performance compiled template renderer
const ClientIncrementalRenderer = {
    renderShell(client) {
        return `
            <div id="client-shell-${client.id}" class="d-flex flex-column h-100 bg-light">
                <!-- Tier 1: Toolbar -->
                <div class="card bg-white border border-slate-200 rounded-xl p-4 mb-3 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <img src="${client.logo_url || '/assets/media/avatars/blank.png'}" class="w-10 h-10 rounded-lg border object-cover" />
                            <div>
                                <h4 class="text-dark font-bold mb-0">${escapeHtml(client.company_name)}</h4>
                                <span class="text-muted text-xs">ID: #${client.id} • ${escapeHtml(client.industry_name || 'General')}</span>
                            </div>
                        </div>
                        <div id="sync-status-${client.id}">
                            <span class="badge badge-light-success text-xs font-medium">Up to Date</span>
                        </div>
                    </div>
                </div>
                <!-- Tier 2: Sub-Tabs -->
                <div class="card bg-white border border-slate-200 rounded-xl px-4 py-2 mb-3 shadow-sm">
                    <ul class="nav nav-tabs nav-line-tabs border-0 fs-6">
                        <li class="nav-item"><a class="nav-link active font-semibold" data-tab="overview" onclick="ClientStateManager.switchTab('overview')">Overview</a></li>
                        <li class="nav-item"><a class="nav-link font-semibold" data-tab="jobs" onclick="ClientStateManager.switchTab('jobs')">Jobs</a></li>
                        <li class="nav-item"><a class="nav-link font-semibold" data-tab="candidates" onclick="ClientStateManager.switchTab('candidates')">Candidates</a></li>
                        <li class="nav-item"><a class="nav-link font-semibold" data-tab="interviews" onclick="ClientStateManager.switchTab('interviews')">Interviews</a></li>
                    </ul>
                </div>
                <!-- Tier 3: Primary Surface Container -->
                <div id="client-tab-content-${client.id}" class="flex-grow-1 overflow-auto pe-2">
                    <!-- Populated dynamically by active tab renderer -->
                </div>
            </div>
        `;
    },

    patchSyncStatus(clientId, status, text) {
        const el = document.getElementById(`sync-status-${clientId}`);
        if (!el) return;
        const badgeClass = status === 'syncing' ? 'badge-light-warning' : (status === 'conflict' ? 'badge-light-danger' : 'badge-light-success');
        el.innerHTML = `<span class="badge ${badgeClass} text-xs font-medium">${text}</span>`;
    }
};
```

---

## 8. Thin Security Gateway & Mandatory Tenant Query Enforcement

### 8.1 The Gateway Security Pipeline
The server-side gateway endpoint (`GET /service_company/clients/api/v2/{id}/graph`) is not a pass-through proxy. It enforces a strict 6-stage security pipeline:

```
  Incoming Request: GET /api/v2/clients/1089/graph
         │
         ▼
  1. Authenticated User Check (Session / JWT)
         │
         ▼
  2. Tenant Boundary Isolation ($user->company_id === $request_tenant_id)
         │
         ▼
  3. RBAC Role & Permission Verification ($user->can('view_clients'))
         │
         ▼
  4. Entity Ownership & Exclusion Rules (ats_client_list scoped by company_id)
         │
         ▼
  5. Field Masking & Projections (Strip internal billing, raw margin tokens)
         │
         ▼
  6. Return Normalized Schema 2.0 JSON Payload (< 3ms)
```

---

## 9. Concrete Implementation Roadmap for Service Company Clients

### Phase 1: Transactional Outbox & Thin JSON API (Backend)
1. Create migration for `tenant_outbox_events` table (strictly tenant-scoped).
2. Implement `OutboxEventDispatcherJob` in Laravel Horizon for idempotent ES projections and Reverb broadcasts.
3. Implement `GET /service_company/clients/api/v2/{id}/graph` returning Schema 2.0 JSON.
4. Implement `GET /service_company/clients/api/v2/{id}/delta-sync` with `min_version` support.

### Phase 2: Client Local Storage Engine (`ClientEntityDB.js`)
1. Create `public/assets/service_company/client/cl-entity-db.js`:
   * Bounded IndexedDB v2 repository with LRU eviction (max 100 clients).
   * Ephemeral memory handling for WASM PDF resumes (zero disk writes).
   * Multi-tenant key scoping (`tenant_id + user_id`).
   * Atomic session purge on logout/401.

### Phase 3: Incremental Template Renderer (`cl-drawer-view.js`)
1. Build `public/assets/service_company/client/cl-drawer-view.js`:
   * Client-side incremental renderers for Shell, Overview, Jobs, Candidates, Interviews, Offers, Joining.
   * Information Hierarchy compliance (Toolbar, Sub-Tabs, Surface, Activity Strip).
   * Native 15.6" screen calibration (`1366×768` and `1920×1080`).

### Phase 4: Reactive State Machine & Reverb Wiring
1. Upgrade `ClientStateManager` in `index.blade.php`:
   * Lazy sub-tab hydration engine.
   * Monotonic request tokens and `AbortController` cancellation.
   * Optimistic concurrency handling with HTTP 409 conflict notifications.
2. Wire Laravel Reverb channel:
   * Listen for `ClientEntityMutated` events.
   * Invalidate local cache and trigger background delta-sync.

### Phase 5: Verification & Zero-Strain Benchmark
1. Automated network throttling test (simulated 10-minute idle sleep, offline-to-online recovery).
2. Concurrency load test targeting 10,000 simulated users with 0% MySQL reads and flat server CPU (< 5%).
3. Formal verification against all 20 non-negotiable rules in `AGENTS.md`.

---

*This document is the authoritative standard for the JERO Distributed Client Runtime (JDCR). All subsequent development on the Service Company Client module must adhere strictly to these specifications.*
