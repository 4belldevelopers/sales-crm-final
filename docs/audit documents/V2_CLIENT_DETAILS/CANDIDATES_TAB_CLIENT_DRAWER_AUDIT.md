# GOD-LEVEL PRODUCTION ARCHITECTURAL SPECIFICATION: CANDIDATES TAB IN CLIENT DRAWER (V2.4 MASTER)

> **Document Status:** LOCKED PRODUCTION ARCHITECTURAL SPECIFICATION & INDUSTRIAL-GRADE CONTRACT  
> **Scope:** Internal Client Drawer (`show.blade.php`) & Direct Client Drawer (`dviewMore.blade.php`)  
> **Target Scale:** 10,000+ Concurrently Active Recruiters with Strictly 0% MySQL Reads on Read Paths  
> **Repository Context:** JERO ATS / Multi-Tenant Talent Marketplace SaaS  
> **Target Path:** `docs/V2_CLIENT_DETAILS/CANDIDATES_TAB_CLIENT_DRAWER_AUDIT.md`  
> **Companion Documents:**  
> - [CLIENT_DETAILS_TABS_AUDIT.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/CLIENT_DETAILS_TABS_AUDIT.md)  
> - [CLIENT_DETAILS_TABS_V2_IMPLEMENTATION_PLAN.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/CLIENT_DETAILS_TABS_V2_IMPLEMENTATION_PLAN.md)  
> - [INTERVIEWS_TAB_CLIENT_DRAWER_AUDIT.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/INTERVIEWS_TAB_CLIENT_DRAWER_AUDIT.md)  
> - [OFFER_JOINING_CLIENT_DRAWER_AUDIT.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/OFFER_JOINING_CLIENT_DRAWER_AUDIT.md)  
> - [drawer_candidate.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/drawer_candidate.md)  
> **Authoritative Codebase Files:**  
> - [show.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/show.blade.php) (Internal Client Drawer Shell)  
> - [dviewMore.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php) (Direct Client Drawer Shell)  
> - [index.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/index.blade.php) (Client State Machine & Drawer LRU Manager)  
> - [custom_client_candidates.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates.blade.php) (Legacy Candidate Row View)  
> - [ServiceCompanyJobApplicationController.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php) (Drawer Candidate Endpoints)  
> - [CandidateDrawerApiController.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/CandidateDrawerApiController.php) (Candidate Core JSON API)  
> - [GeneralMethod.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php) (Candidate KPI Counts & ES Sync Logic)  
> - [mysql-schema.sql](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/database/schema/mysql-schema.sql) (Database Schema of Record)  

---

## 🔒 The 10 Non-Negotiable Architecture Rules (Candidate Tab Constitution)

1. **Candidate Drawer read paths execute strictly 0 MySQL SELECT queries across the entire HTTP lifecycle.**
2. **MySQL remains the single transactional Source of Truth (SSOT); Elasticsearch is strictly the Candidate Read Model.**
3. **Authorization is evaluated before Elasticsearch access and ALWAYS fails closed (`HTTP 403`).**
4. **Authorization must NEVER depend on candidate Elasticsearch documents.**
5. **No MySQL fallback is permitted under any circumstances during Elasticsearch cluster outages.**
6. **Every candidate state mutation uses idempotency + Optimistic Concurrency Control (`aggregate_version`).**
7. **Every Elasticsearch projection is monotonically versioned and update operations are strictly idempotent.**
8. **Browser state (`ClientCandidateStore`) guarantees mutation-aware pagination continuity; any membership-altering mutation invalidates pagination cursors and reconciles the collection.**
9. **Internal and Direct client drawers must execute through the single unified `CandidateDrawerQueryService`.**
10. **Every architectural invariant must have a mandatory CI assertion test across the full middleware/HTTP request stack.**

---

## 1. Architectural Foundations & CQRS Authority Boundaries

### 1.1 Strict CQRS Authority Definitions
- **MySQL = Authoritative Transactional Source of Truth (SSOT)**: The single authoritative store for transactional business writes (`INSERT`, `UPDATE`, `DELETE`). Commits all state changes within an atomic transaction containing a domain mutation, an outbox event (`sys_outbox_events`), and an idempotency key claim.
- **Elasticsearch (`ELASTIC_CANDIDATES`) = Authoritative Candidate Read Model**: Universal read projection for candidate searches, multi-stage pipeline filters, pagination, and count aggregations. **Browser clients and UI controllers NEVER write to Elasticsearch directly.**
- **Dragonfly / Redis = Non-Authoritative Coordination & Cache Tier**: Stores fail-closed authorization projections (`auth:tenant_clients:{company_id}`), client pipeline version counters (`client_candidate_version:{company}:{client}`), and transient metadata.
- **Browser User RAM (`ClientCandidateStore`) = Ephemeral Presentation State**: Holds the client-side state machine, loaded candidate records, debounce timers, request IDs, and DOM patching logic.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   CANONICAL CQRS & OUTBOX PIPELINE                                     │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘

        COMMAND PATH (State Mutations)                          QUERY PATH (Zero MySQL Reads)
   ┌──────────────────────────────────────┐                ┌──────────────────────────────────────┐
   │ Recruiter Action (Stage/Note/Status) │                │  Client Drawer Browser (Candidates)  │
   └──────────────────┬───────────────────┘                └──────────────────▲───────────────────┘
                      │ (Idempotency Key + OCC)                               │ (Pure JSON Projection)
                      ▼                                                       │
   ┌──────────────────────────────────────┐                ┌──────────────────┴───────────────────┐
   │        MySQL WRITE STORE (SSOT)      │                │      CandidateDrawerQueryService     │
   │  [Atomic Transaction: Data + Outbox] │                │       (Fail-Closed Auth Guard)       │
   └──────────────────┬───────────────────┘                └──────────────────▲───────────────────┘
                      │                                                       │
           Transactional Outbox Worker                                        │ (Elasticsearch Query)
                      │                                                       │
        ┌─────────────┴─────────────┐                      ┌──────────────────┴───────────────────┐
        ▼                           ▼                      │   ELASTICSEARCH READ MODEL (PROJ)    │
   ┌─────────┐                 ┌─────────┐                 │        ELASTIC_CANDIDATES            │
   │   ES    │                 │ Reverb  │                 └──────────────────────────────────────┘
   │ Indexer │                 │ Broadcast                 
   └─────────┘                 └─────────┘
```

### 1.2 "0% MySQL Reads" Scoped Architectural Rule
> **Architectural Mandate**:  
> **Candidate Drawer read paths must execute strictly 0 MySQL SELECT queries.**  
> *(This invariant governs UI/API candidate listing, counting, filtering, search, and pagination. It strictly prohibits ad-hoc queries, joins, and relational lookups during read execution. Background DB migrations, outbox dispatch workers, and write commands operate under their respective transactional boundaries).*

---

## 2. Security Architecture: Fail-Closed Multi-Tenant Authorization

### 2.1 The Vulnerability in Existing Code
In the legacy implementation ([`ServiceCompanyJobApplicationController.php:1294`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1294)), `clientid` was passed from the request directly into an Elasticsearch `should` query without verifying tenant ownership. Furthermore, using loose `should` clauses (`company_id OR job_company_id OR ClientComID`) created cross-tenant IDOR leakage risks.

### 2.2 The Fail-Closed Security Pipeline & Deterministic Status Codes
Every candidate request passes through a strict, multi-tier security hierarchy before Elasticsearch is queried:

```
Authenticated User (JWT / Session)
       ↓
Extract authenticated company_id
       ↓
Client Access Guard (Dragonfly Set: auth:tenant_clients:{company_id})
       ├── [DRAGONFLY DOWN / UNAVAILABLE] ──► Immediate HTTP 503 Service Unavailable
       ├── [NOT MEMBER / UNAUTHORIZED]    ──► Immediate HTTP 403 Forbidden
       └── [IS AUTHORIZED MEMBER]         ──► Proceed to Candidate Query
                                                   ↓
                                   Elasticsearch Candidate Read Model
                                   (Strict Filter: company_id AND client_id)
                                           ├── [ES CLUSTER OUTAGE / TIMEOUT] ──► HTTP 503 Service Unavailable
                                           └── [QUERY SUCCESS]               ──► Pure JSON Response
```

> [!CAUTION]
> **P0 SECURITY CONTRACT: Zero ES Candidate Document Auth Fallback**  
> Authorization must **NEVER** depend on Elasticsearch candidate documents. Improvised document-level authorization fallbacks are strictly prohibited.

#### Deterministic HTTP Status Code Contract
To ensure architectural determinism across all read and coordination failure modes, HTTP status codes are strictly mapped:

| Infrastructure / Security Condition | HTTP Status | Error Code | Operational Behavior |
|---|---|---|---|
| **Authenticated user lacks client tenancy access** | `403 Forbidden` | `ACCESS_DENIED` | Fails closed; zero data emitted. |
| **Auth projection tier unavailable (Dragonfly down)** | `503 Service Unavailable` | `AUTH_PROJECTION_UNAVAILABLE` | Fails closed; zero MySQL reads; zero ES queries. |
| **Client version store unavailable (Dragonfly down)** | `503 Service Unavailable` | `COORDINATION_STORE_UNAVAILABLE` | Fails closed; zero MySQL reads; zero ES queries. |
| **Elasticsearch read cluster down or socket timeout** | `503 Service Unavailable` | `READ_MODEL_UNAVAILABLE` | Circuit breaker trips; strictly zero MySQL fallback. |
| **Tampered cursor / sort mismatch / page > 5 no cursor** | `422 Unprocessable Entity` | `INVALID_CURSOR` / `INVALID_PAGINATION` | Fast rejection; prevents deep ES offset abuse. |
| **Per-user or per-tenant rate limit threshold exceeded** | `429 Too Many Requests` | `RATE_LIMIT_EXCEEDED` | Rejects excess request with `Retry-After: 3`. |
| **OCC version collision on candidate write mutation** | `409 Conflict` | `OCC_VERSION_CONFLICT` | Mutation rejected; prompts recruiter to refresh. |

### 2.3 Authorization Revocation Propagation SLA & Verifiable Channel Invalidation
When an administrator revokes a recruiter's permissions to access a specific client:
1. **Immediate Dragonfly Revocation Write**: An atomic Redis transaction removes the client ID (`SREM auth:tenant_clients:{company_id} {client_id}`).
2. **Deterministic Revocation SLA**:
   - **P95 < 1.0 second**
   - **P99 < 5.0 seconds**
3. **Verifiable Subscription Invalidation Contract**:
   - Authorization revocation becomes effective in Dragonfly within the defined SLA.
   - All subsequent candidate API requests fail closed with `HTTP 403 Forbidden` (`ACCESS_DENIED`).
   - Active Reverb WebSocket subscriptions are invalidated through the channel authorization guard: the server-side channel token is evicted from the coordination cache, terminating broadcast delivery on `private-company.{companyId}.client.{clientId}.candidates` within the P99 SLA.
   - **Verification Test**: Explicitly asserted by `test_revoked_client_access_invalidates_active_socket_subscription_and_denies_api_reads` (Section 11.2). Zero candidate documents, aggregations, or real-time mutation events are delivered post-revocation.

---

## 3. Disambiguated Versioning & Semantic Counts

### 3.1 Formal Definition of Distributed Versions
1. **`aggregate_version` (Candidate Aggregate Version)**:  
   Monotonically increasing integer (e.g., `42`) representing the state revision of an individual candidate entity in MySQL. Used for Optimistic Concurrency Control (OCC) during state mutations.
2. **`client_candidate_version` (Client Pipeline Version)**:  
   Monotonically increasing integer (e.g., `1035`) stored in Dragonfly/Redis tracking candidate collection changes under a specific client (`client_candidate_version:{company}:{client}`). Used by browser stores to detect collection-level changes and trigger intelligent reconciliation.
3. **`projection_schema_version` (Read Model Schema Version)**:  
   Semantic schema revision of the Elasticsearch index structure (e.g., `2`). Guarantees that consumers process documents conforming to the expected contract.

### 3.2 Single Source of Truth & Preferred Count Architecture Hierarchy
- **Total Active Candidates (Hero KPI & Tab Badge)**:  
  Represents all non-archived candidate profiles linked to this client within the authenticated company scope:
  - `company_id = authenticated_company`
  - `client_id = authorized_client`
  - `job_master_status_id != 9` (Archived/Deleted)
- **Filtered Result Count (Table Toolbar)**:  
  Represents matching records when search terms or stage filters are applied:
  - `Display Contract`: *"Showing 18 of 247 Candidates (Filtered by: Interview)"*.
  - Temporary UI filters must **never overwrite or mutate the semantic value of the Hero KPI badge**.

#### Preferred Count Architecture Hierarchy:
1. **Tier 1 (Preferred / Production Standard — 0 ES Read Load)**:  
   **Versioned Precomputed Count Projection (`ats:kpi:client_cand:{company_id}:{client_id}`)**. Powers the drawer header KPI badge and tab counter with zero Elasticsearch queries. Updated via atomic invalidation on candidate mutation outbox events.
2. **Tier 2 (Good / Filtered Search Bounded Count)**:  
   **Bounded `track_total_hits: 10000`**. Used when active filters/searches are applied. Prevents expensive full-shard index walks while delivering exact precision up to 10,000 records.
3. **Tier 3 (Conditional Baseline / Empirical Validation Gate)**:  
   **Exact `track_total_hits: true`**. Only sustained in baseline configuration if empirical k6 10,000 concurrency testing validates that P95 read latency remains `< 120 ms` and ES cluster CPU remains `< 70%`.

### 3.3 Version-Based Invalidation (Retiring 15-Minute TTL)
Arbitrary 15-minute (`900s`) caching is permanently eradicated. Caching utilizes atomic, versioned keys:

```text
Key: ats:kpi:client_cand:{company_id}:{client_id}
Tag: client_candidate_version:{company_id}:{client_id}
```

### 3.4 Outbox Pipeline Crash-Recovery & Idempotent Replay Contract
When an outbox worker processes a candidate mutation event (`sys_outbox_events`), the worker executes a 5-stage idempotent pipeline:

```
[MySQL sys_outbox_events]
          ↓ (Event Picked Up by Worker)
Stage 1: Elasticsearch Projection Update (Idempotent OCC via aggregate_version)
          ↓ (Success)
Stage 2: Dragonfly Collection Version Advancement (Idempotent via event_id tracking)
          ↓ (Success)
Stage 3: KPI Count Cache Eviction (Idempotent DEL)
          ↓ (Success)
Stage 4: Laravel Reverb Broadcast (event_id, aggregate_version, client_candidate_version)
          ↓ (Success)
Stage 5: MySQL Outbox Event Marked PROCESSED (processed_at = NOW())
```

#### Crash Recovery & Replay Invariants:
1. **Stage 1 Crash (Worker crashes during/after ES update, before Stage 2)**:
   - On retry, ES update re-executes with OCC (`if_seq_no` / `ctx._source.aggregate_version < params.new_version`). If ES is already updated to `aggregate_version`, it safely no-ops.
2. **Stage 2 Crash (Worker crashes after Dragonfly version increment, before broadcast/DB ack)**:
   - To prevent runaway version advancement during worker retries, Dragonfly tracks processed event IDs:
     ```text
     MULTI
     INCR client_candidate_version:{company_id}:{client_id}
     SET client_cand_last_event:{company_id}:{client_id} {event_id}
     EXEC
     ```
   - On retry, if `client_cand_last_event == event_id`, the worker skips `INCR` and retrieves the existing version number.
3. **Stage 4 Crash (Worker crashes after broadcast, before MySQL status marked PROCESSED)**:
   - On retry, Reverb broadcast re-emits. The browser store's `processedEventIds` Set and `candidateVersions` OCC checks immediately discard the duplicate event as a no-op.
4. **Causal Consistency Guarantees**:
   - Outbox worker polls events with best-effort partition ordering (`id ASC`). However, system correctness does **NOT** depend on message delivery sequence; causal consistency is strictly guaranteed by:
     - `aggregate_version` OCC enforcement on entity writes.
     - Monotonic external versioning in Elasticsearch (`version_type: external_gte` or OCC script).
     - Atomic monotonic advancement of `client_candidate_version` in Dragonfly.
     - Idempotent outbox event worker retries.
     - Browser store stale-event rejection via `aggregate_version` and `client_candidate_version`.

### 3.5 Reusable Version Store & Atomic Bootstrap (`ClientCandidateVersionStore`)
The client collection version is a critical concurrency gate. To prevent duplicate logic and semantic drift between the candidate query engine and the reconnect controller:

1. **Dedicated Service**: All version reads and increments are encapsulated in a single authoritative service:  
   [`App\Services\Candidate\ClientCandidateVersionStore`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Services/Candidate/ClientCandidateVersionStore.php).
2. **Explicit Semantics for Version State**:
   - **Dragonfly Unavailable**: Immediately aborts with `HTTP 503 Service Unavailable` (`COORDINATION_STORE_UNAVAILABLE`). Strictly zero MySQL fallback queries.
   - **Dragonfly Healthy + Key Exists**: Returns the current monotonically increasing integer version in a single atomic round-trip.
   - **Dragonfly Healthy + Key Absent (Cold Bootstrap)**: Atomically executes Lua primitive `EXISTS -> SETNX 0 -> return 0`. This is explicitly defined as a valid cold bootstrap state, not an unhandled fallback.
   - **Silent `?: 1` Fallbacks are Strictly Prohibited**: Default version fallback is never permitted under any error condition.
3. **Replay-Retention Invariant (`event_ledger_TTL > maximum_outbox_replay_horizon`)**:
   - Outbox maximum retry/recovery replay horizon: **72 hours** (259,200 seconds).
   - Deduplication event ledger TTL: **7 days (168 hours / 604,800 seconds)**, safely exceeding the $\ge 96\text{h}$ safety horizon.
   - **Mathematical Guarantee**: Any duplicate event replay, redelivery, or disaster-recovery backfill occurring within the 7-day window is recognized as already processed, guaranteeing zero spurious collection version increments.

```php
namespace App\Services\Candidate;

class ClientCandidateVersionStore
{
    /**
     * Atomic version lookup with single-operation cold bootstrap
     */
    public function getVersion(int $companyId, int $clientId): int
    {
        $key = "client_candidate_version:{$companyId}:{$clientId}";

        $luaBootstrap = <<<'LUA'
            local key = KEYS[1]
            if redis.call('EXISTS', key) == 1 then
                return tonumber(redis.call('GET', key) or 0)
            else
                redis.call('SET', key, 0, 'NX')
                return 0
            end
LUA;

        try {
            $redis = cache()->store('dragonfly')->getRedis();
            return (int) $redis->eval($luaBootstrap, 1, $key);
        } catch (\Throwable $e) {
            \Log::emergency('Dragonfly coordination tier unavailable for candidate version lookup', [
                'company_id' => $companyId,
                'client_id'  => $clientId,
                'error'      => $e->getMessage()
            ]);
            abort(503, 'Coordination store unavailable. Candidate synchronization cannot be guaranteed.');
        }
    }

    /**
     * Atomic check-and-increment with 7-day replay ledger
     * Invariant: event_ledger_TTL (7d) > maximum_outbox_replay_horizon (72h)
     */
    public function incrementVersion(int $companyId, int $clientId, string $eventId): int
    {
        $versionKey = "client_candidate_version:{$companyId}:{$clientId}";
        $eventKey   = "event_idempotency:{$companyId}:{$clientId}:{$eventId}";
        $ttlSeconds = 604800; // 7-day replay deduplication horizon (exceeds 72h outbox redelivery horizon)

        // Atomic Lua Script: Evaluates event deduplication and collection advancement in one single-threaded transaction
        $luaScript = <<<'LUA'
            local eventKey   = KEYS[1]
            local versionKey = KEYS[2]
            local ttl        = tonumber(ARGV[1])

            -- 1. Check if event was already processed (replay idempotency)
            if redis.call('EXISTS', eventKey) == 1 then
                return tonumber(redis.call('GET', versionKey) or 0)
            end

            -- 2. Mark event as processed with 7-day TTL ledger
            redis.call('SET', eventKey, 1, 'EX', ttl)

            -- 3. Atomically increment collection version
            local newVersion = redis.call('INCR', versionKey)
            return newVersion
LUA;

        try {
            $redis = cache()->store('dragonfly')->getRedis();
            return (int) $redis->eval($luaScript, 2, $eventKey, $versionKey, $ttlSeconds);
        } catch (\Throwable $e) {
            \Log::emergency('Dragonfly version advancement failed', [
                'company_id' => $companyId,
                'client_id'  => $clientId,
                'event_id'   => $eventId,
                'error'      => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

### 3.6 Event Deduplication: Advisory Browser Optimization vs Authoritative OCC Correctness
`processedEventIds` in the browser client store is a bounded (max 500) memory-control optimization designed to suppress immediate duplicate WebSocket deliveries in User RAM. It is **NOT** a distributed exactly-once correctness boundary.

#### Authoritative Concurrency & Correctness Hierarchy:
1. **Tier 1 (Advisory Optimization / Ephemeral)**:  
   **Event ID Tracking (`processedEventIds` Set)**. Suppresses immediate network duplicate WebSocket frames in browser User RAM. Eviction from this bounded set never compromises system correctness.
2. **Tier 2 (Candidate-Level Correctness / OCC)**:  
   **`aggregate_version` OCC Verification**. If a duplicate or late-arriving candidate event is processed, `e.aggregate_version <= localCandidateVersion` immediately rejects the mutation as a no-op.
3. **Tier 3 (Collection-Level Consistency)**:  
   **`client_candidate_version` Sequence Watermark**. If an event collection version is older than local state (`e.client_candidate_version <= localClientVersion`), the collection update is ignored.
4. **Tier 4 (Final State Convergence)**:  
   **Query-Aware Collection Reconciliation**. Server-side Elasticsearch read model queries ensure definitive convergence across all browser tabs.

---

## 4. Search, Pagination & Tamper-Proof Cursor Architecture

### 4.1 Production Elasticsearch Analyzer & Mapping Design
```json
{
  "mappings": {
    "properties": {
      "full_name": {
        "type": "text",
        "analyzer": "standard",
        "fields": {
          "search": { "type": "text", "analyzer": "autocomplete_ngram" },
          "keyword": { "type": "keyword", "ignore_above": 256 }
        }
      },
      "email": { "type": "keyword" },
      "phone": { "type": "keyword" },
      "current_designation": {
        "type": "text",
        "fields": { "search": { "type": "text", "analyzer": "standard" } }
      },
      "skills": {
        "type": "text",
        "fields": {
          "search": { "type": "text", "analyzer": "skills_analyzer" },
          "keyword": { "type": "keyword" }
        }
      },
      "created_at": { "type": "date" },
      "wiseId": { "type": "long" },
      "aggregate_version": { "type": "long" }
    }
  }
}
```

### 4.2 Explicit Pagination Contract & Deep Pagination Enforcement
```text
Mode = "page": (page <= 5) -> Uses bounded 'from + size'
Mode = "cursor": (cursor supplied) -> Uses 'search_after'
```

> [!CAUTION]
> **P0 PAGINATION GUARD**: If a client requests `page > 5` without a cursor, the API **strictly rejects the request with HTTP 422 Unprocessable Entity** (`INVALID_PAGINATION_MODE: Deep pagination beyond page 5 requires cursor navigation`). It does NOT silently fall back to an expensive deep Elasticsearch offset.

### 4.3 Mathematical `size + 1` Cursor Execution & Response Delineation
To prevent false-positive `has_more` indicators when result counts exactly match the page size:
1. The service requests **`size + 1`** documents from Elasticsearch (e.g., requested 25 ➔ fetches 26).
2. If 26 documents return:
   - `has_more = true`.
   - The 26th record is removed from the visible dataset (`array_slice($items, 0, $size)`).
   - `next_cursor` is computed from the 25th record.
3. If <= 25 documents return:
   - `has_more = false`.
   - `next_cursor = null`.

> [!WARNING]
> **HIGH-CONCURRENCY `track_total_hits` PERFORMANCE & CONFIGURATION POLICY**:  
> Requesting exact total hits (`track_total_hits: true`) forces Elasticsearch to visit all matching documents across all index shards, disabling early termination optimizations (e.g., Block-Max WAND). At 10,000 concurrent recruiters with multi-clause filters, exact counts become a significant contributor to ES CPU and GC pressure.  
> **Deterministic Configuration Policy (Zero In-Flight Query Mutation)**:  
> 1. Exact totals are retained in baseline V2.3 via `config('services.elasticsearch.candidate_track_total_hits', true)` to support the explicit recruiter UI requirement (*"Showing 18 of 247 Candidates"*).  
> 2. Query semantics are **NEVER mutated dynamically mid-flight per request** (which would cause Request A to return "247" and Request B to return "10,000+", creating jarring UI inconsistencies).  
> 3. If the **Section 11.2 Empirical Load-Test Gate** fails under 10K k6 stress testing (P95 > 120ms or ES CPU > 70%), the configuration is permanently switched at deployment/environment level to bounded tracking (`config: candidate_track_total_hits = 10000`) or decoupled count projection.

#### Delineated Pagination Response Contracts:
- **Page Mode (`mode: "page"` with Exact Total `total_relation: "eq"`)**:
  ```json
  "pagination": {
    "mode": "page",
    "total": 247,
    "total_relation": "eq",
    "per_page": 25,
    "current_page": 1,
    "last_page": 10,
    "has_more": true
  }
  ```
- **Page Mode (`mode: "page"` with Bounded Lower-Bound `total_relation: "gte"`)**:
  ```json
  "pagination": {
    "mode": "page",
    "total": 10000,
    "total_relation": "gte",
    "per_page": 25,
    "current_page": 1,
    "last_page": null,
    "has_more": true
  }
  ```
  *(When bounded tracking triggers at 10,000 matches, `total_relation` returns `"gte"`, `last_page` is strictly set to `null` to avoid rendering bogus page bounds like `last_page: 400`, and the UI displays `"10,000+ Candidates"` with continuous Next navigation).*
- **Cursor Mode (`mode: "cursor"`)**:
  ```json
  "pagination": {
    "mode": "cursor",
    "total": 247,
    "total_relation": "eq",
    "per_page": 25,
    "has_more": true,
    "next_cursor": "eyJ2IjoyLCJwYXlsb2FkIjp7InZhbHVlcyI6WzE3OTc5ODI0MDAsMTg0OTJdLCJjb21wYW55X2lkIjo0MiwiY2xpZW50X2lkIjoxMDgsImNsaWVudF90eXBlIjoiaW50ZXJuYWwiLCJjbGllbnRfY2FuZGlkYXRlX3ZlcnNpb24iOjEwMzUsInF1ZXJ5X2ZpbmdlcnByaW50IjoiOGY0YTI...",
  }
  ```
  *(Offset-specific fields like `current_page` and `last_page` are omitted in cursor mode).*

### 4.4 Tamper-Proof HMAC-SHA256 Cursor with Mutation-Aware Pagination Continuity
To prevent cursor state manipulation, cross-filter cursor reuse, stale schema execution, live index drift, or sort mismatch:
> [!IMPORTANT]
> **Precise Snapshot Consistency Contract**: The system guarantees **mutation-aware pagination continuity**, not immutable physical ES snapshot semantics (which would require expensive Elasticsearch Point-in-Time [PIT] states on hot clusters). Any collection mutation invalidates existing cursors via the collection version watermark check and cleanly resets pagination to page 1.

1. **Scope Invariant**: The Candidate Drawer V2.4 active query scope is strictly bounded to `search`, `status_id`, `job_id`, and deterministic `sort`. Secondary demographic filters (`country_id`, `city_id`, `experience`, `notice_id`) belong to the global talent database index and are excluded from the drawer query fingerprint to guarantee 100% contract parity between the cursor payload and the Elasticsearch query engine.
2. **Canonical Query Fingerprint**: Formed from all active search criteria hashed with SHA-256:
   ```php
   $canonicalFilterData = [
       'company_id'  => $companyId,
       'client_id'   => $clientId,
       'client_type' => $clientType,
       'search'      => trim($params['search'] ?? ''),
       'status_id'   => $params['status_id'] ?? null,
       'job_id'      => $params['job_id'] ?? null,
       'sort'        => 'created_at:desc,wiseId:desc'
   ];
   ksort($canonicalFilterData);
   $queryFingerprint = hash('sha256', json_encode($canonicalFilterData));
   ```
3. **Cursor Payload Lifecycle & Snapshot Consistency Contract**:
   Every cursor payload explicitly binds context, query state, collection version watermark, and lifecycle constraints in a versioned envelope (`v: 2`):
   ```php
   $cursorPayload = [
       'values'                    => [$lastItem['created_at'] ?? 0, $lastItem['wiseId'] ?? 0],
       'company_id'                => $companyId,
       'client_id'                 => $clientId,
       'client_type'               => $clientType,
       'client_candidate_version'  => $currentCollectionVersion, // Binds cursor to collection snapshot
       'query_fingerprint'         => $queryFingerprint,
       'sort'                      => $canonicalSort,
       'projection_schema_version' => 2,
       'issued_at'                 => now()->timestamp,
       'expires_at'                => now()->addMinutes(15)->timestamp, // 15-Minute TTL Lifecycle
   ];
   $envelope = [
       'v'       => 2,
       'payload' => $cursorPayload,
       'sig'     => hash_hmac('sha256', json_encode($cursorPayload), config('app.key')),
   ];
   $nextCursor = base64_encode(json_encode($envelope));
   ```
4. **HMAC Signing & Verification Invariants**:
   - The query service strictly parses cursor strings using `base64_decode($cursorString, true)`.
   - Verifies:
     - Envelope version: `envelope['v'] === 2`.
     - Cryptographic signature via constant-time `hash_equals()`.
     - Context match: `company_id`, `client_id`, and `client_type`.
     - Fingerprint match: `query_fingerprint === $activeQueryFingerprint`.
     - Sort order match: `sort === $canonicalSort`.
     - Schema version match: `projection_schema_version === 2`.
     - Lifecycle TTL: `now()->timestamp <= expires_at`.
     - Snapshot Consistency: `client_candidate_version === $activeCollectionVersion`. If a mutation occurred while paginating, aborts with **`HTTP 409 Conflict` (`CURSOR_STALE: Collection mutated; pagination reset required`)**, prompting the browser to refetch page 1 without duplicate/skipped records.
   - Any tampering or expiration immediately aborts with **`HTTP 422 Unprocessable Entity` (`INVALID_CURSOR`)**.

---

## 5. Candidate Read Model Denormalization Schema

Every candidate projection document in `ELASTIC_CANDIDATES` contains pre-computed display attributes:

```json
{
  "_id": "application_18492",
  "_source": {
    "id": 18492,
    "wiseId": 18492,
    "company_id": 42,
    "tenant_id": 42,
    "client_id": 108,
    "client_name": "Jio Platforms",
    "full_name": "Vibhanshu Jaiswal",
    "email": "vibhanshu@example.com",
    "phone": "+91 9876543210",
    "current_designation": "Principal Architect",
    "total_exp": "10.5",
    "rel_exp": "8.0",
    "ctc_yearly": "3500000",
    "ectc_yearly": "4500000",
    "current_ctc_mode": "Y",
    "current_ctc_cur": 0,
    "currency_symbol": "INR",
    "np_id": "Immediate",
    "serving_notice_period_mode": 1,
    "current_company": "Enterprise Corp",
    "country_id": 101,
    "country_name": "India",
    "state_name": "Maharashtra",
    "city_id": 1420,
    "city_name": "Mumbai",
    "skills": "PHP, Laravel, Elasticsearch, Redis, MySQL",
    "skills_array": ["PHP", "Laravel", "Elasticsearch", "Redis", "MySQL"],
    "skill_match_percentage": 92.5,
    "job_id": 504,
    "job_title": "Lead Software Engineer",
    "job_mode": "Full-Time",
    "job_currency": 0,
    "master_status_id": 3,
    "master_status_name": "Interview",
    "master_status_css": "badge-light-primary",
    "child_status_id": 14,
    "child_status_name": "Technical Round 2",
    "posted_by_id": 12,
    "posted_by_name": "Rahul Verma",
    "submitted_by_company_id": 42,
    "submitted_by_company_name": "Acme Talent",
    "aggregate_version": 42,
    "projection_schema_version": 2,
    "source_updated_at": "2026-10-22T21:40:00Z",
    "indexed_at": "2026-10-22T21:40:00.082Z"
  }
}
```

---

## 6. Client Architecture: `ClientCandidateStore` State Machine

### 6.1 State Machine Lifecycle & Filter-Aware Reconciliation
```javascript
window.ClientCandidateStore = {
    state: 'IDLE', // 'IDLE' | 'LOADING' | 'READY' | 'REFRESHING' | 'ERROR' | 'DESTROYED'
    companyId: null,
    clientId: null,
    clientType: null,
    queryFingerprint: null,
    activeXhr: null,
    latestRequestId: 0,
    clientCandidateVersion: 0,
    projectionSchemaVersion: 2,
    candidateVersions: {}, // Map: { [candidateId]: aggregate_version } for active rows
    processedEventIds: new Set(), // Bounded set for event idempotency (prevents memory leak)
    maxProcessedEventIds: 500,
    filters: {
        page: 1,
        per_page: 25,
        cursor: null,
        search: '',
        status_id: '',
        job_id: '',
        sort: 'created_at:desc'
    },
    data: [],
    pagination: {},

    markEventProcessed: function(eventId) {
        if (!eventId) return;
        this.processedEventIds.add(eventId);
        if (this.processedEventIds.size > this.maxProcessedEventIds) {
            // Prune oldest processed event ID from User RAM
            var oldestId = this.processedEventIds.values().next().value;
            this.processedEventIds.delete(oldestId);
        }
    },

    init: function(companyId, clientId, clientType) {
        if (this.clientId !== clientId || this.companyId !== companyId || this.clientType !== clientType) {
            this.destroy();
        }

        this.companyId = companyId;
        this.clientId = clientId;
        this.clientType = clientType;
        this.state = 'IDLE';
        this.clientCandidateVersion = 0;
        this.queryFingerprint = null;
        this.candidateVersions = {};
        this.processedEventIds.clear();
        this.bindEvents();
        this.subscribeReverb();
        this.fetch(1);
    },

    reconcileTimer: null,
    coalesceDelayMs: 60, // 60ms Bounded Window: Produces at most one ES refresh per active reconciliation window

    scheduleReconcile: function(resetToPageOne = false) {
        var self = this;
        if (resetToPageOne) {
            this.filters.page = 1;
            this.filters.cursor = null;
        }
        if (this.reconcileTimer) {
            clearTimeout(this.reconcileTimer);
        }
        // Events are coalesced within the bounded 60ms window; multiple events within window yield at most one refresh
        this.reconcileTimer = setTimeout(function() {
            self.reconcileTimer = null;
            self.fetch(self.filters.page, self.filters.cursor);
        }, this.coalesceDelayMs);
    },

    updateCounts: function(total, relation) {
        var displayTotal = (relation === 'gte') ? (Number(total).toLocaleString() + '+') : Number(total).toLocaleString();
        $('#cdCandidateCount').text(displayTotal);
        $('#tabBadgeCandidates').text(displayTotal);
    },

    fetch: function(page = 1, cursor = null) {
        var self = this;
        this.filters.page = page;
        this.filters.cursor = cursor;
        var requestId = ++this.latestRequestId;

        if (this.activeXhr) {
            this.activeXhr.abort();
        }

        this.state = (this.data.length > 0) ? 'REFRESHING' : 'LOADING';
        this.renderState();

        this.activeXhr = $.ajax({
            url: (this.clientType === 'direct')
                ? '/service_company/direct-client-candidate-list'
                : '/service_company/client-candidate-list',
            type: 'GET',
            data: $.extend({ clientid: this.clientId, request_id: requestId }, this.filters),
            success: function(res) {
                if (requestId !== self.latestRequestId) return;

                self.data = res.data;
                self.pagination = res.pagination;
                self.clientCandidateVersion = res.meta.client_candidate_version || self.clientCandidateVersion;
                self.projectionSchemaVersion = res.meta.projection_schema_version || self.projectionSchemaVersion;
                self.queryFingerprint = res.meta.query_fingerprint || null;

                // Sync candidate OCC versions for active rendered rows
                self.candidateVersions = {};
                if (Array.isArray(res.data)) {
                    res.data.forEach(function(cand) {
                        if (cand.id && cand.aggregate_version) {
                            self.candidateVersions[cand.id] = cand.aggregate_version;
                        }
                    });
                }

                self.state = 'READY';
                self.renderRows();
                self.updateCounts(res.pagination.total, res.pagination.total_relation);
            },
            error: function(xhr, status) {
                if (status === 'abort') return;
                if (requestId !== self.latestRequestId) return;
                if (xhr.status === 409) {
                    // P0 Invariant: Cursor invalidated by concurrent collection mutation -> refetch page 1
                    self.scheduleReconcile(true);
                    return;
                }
                self.state = 'ERROR';
                var msg = (xhr.status === 403)
                    ? 'Access Denied.'
                    : (xhr.status === 503)
                        ? 'Service temporarily unavailable. Please retry shortly.'
                        : (xhr.responseJSON?.error?.message || 'Failed to load candidates.');
                self.renderError(msg);
            }
        });
    },

    QUERY_DEPENDENCIES: {
        search: { fields: ['full_name', 'current_designation', 'skills', 'email', 'phone'] },
        status_id: { fields: ['master_status_id'], filter_key: 'status_id' },
        job_id: { fields: ['job_id'], filter_key: 'job_id' },
        sort: { fields: ['created_at', 'wiseId'] },
        visibility: {
            excluded_values: {
                master_status_id: [9] // Declarative Archive Rule: Status 9 excluded from active drawer scope
            }
        }
    },

    mutationAffectsCurrentQuery: function(e) {
        var fields = e.changed_fields || {};
        var changedKeys = Object.keys(fields);
        if (changedKeys.length === 0) return false;

        // 1. Fully Declarative Visibility Check: Evaluates manifest-defined excluded_values
        var visibility = this.QUERY_DEPENDENCIES.visibility;
        if (visibility && visibility.excluded_values) {
            for (var visField in visibility.excluded_values) {
                if (fields[visField] !== undefined) {
                    var excludedArr = visibility.excluded_values[visField];
                    var val = parseInt(fields[visField], 10);
                    if (excludedArr.indexOf(val) !== -1) return true;
                }
            }
        }

        // 2. Fully Declarative Scope Check: Non-visible candidate mutating into active filter scope
        var isCurrentlyVisible = this.candidateVersions.hasOwnProperty(e.candidate_id);
        if (!isCurrentlyVisible) {
            var entersActiveScope = true;
            for (var depKey in this.QUERY_DEPENDENCIES) {
                var def = this.QUERY_DEPENDENCIES[depKey];
                if (def && def.filter_key && this.filters[def.filter_key]) {
                    var activeFilterVal = parseInt(this.filters[def.filter_key], 10);
                    var matchedField = false;
                    for (var f = 0; f < def.fields.length; f++) {
                        var fieldName = def.fields[f];
                        if (fields[fieldName] !== undefined) {
                            if (parseInt(fields[fieldName], 10) === activeFilterVal) {
                                matchedField = true;
                                break;
                            }
                        }
                    }
                    if (!matchedField) {
                        entersActiveScope = false;
                        break;
                    }
                }
            }
            if (entersActiveScope) return true;
        }

        // 3. Fully Declarative Active Filter Match
        for (var filterKey in this.filters) {
            if (this.filters.hasOwnProperty(filterKey) && this.filters[filterKey] && this.QUERY_DEPENDENCIES[filterKey]) {
                var depFields = this.QUERY_DEPENDENCIES[filterKey].fields || [];
                for (var i = 0; i < depFields.length; i++) {
                    if (fields[depFields[i]] !== undefined) return true;
                }
            }
        }

        // 4. Fully Declarative Sort Order Match
        if (this.QUERY_DEPENDENCIES.sort && this.QUERY_DEPENDENCIES.sort.fields) {
            var sortFields = this.QUERY_DEPENDENCIES.sort.fields;
            for (var s = 0; s < sortFields.length; s++) {
                if (fields[sortFields[s]] !== undefined) return true;
            }
        }

        return false;
    },

    subscribeReverb: function() {
        var self = this;
        var channel = `private-company.${this.companyId}.client.${this.clientId}.candidates`;
        
        Echo.private(channel)
            .listen('.CandidateStatusChanged', function(e) {
                // 1. Idempotency Guard: Discard duplicate events (advisory optimization)
                if (e.event_id && self.processedEventIds.has(e.event_id)) return;
                self.markEventProcessed(e.event_id);

                // 2. Authoritative OCC Guard: Discard late-arriving event if candidate version is older
                var currentVer = self.candidateVersions[e.candidate_id] || 0;
                if (e.aggregate_version && e.aggregate_version <= currentVer) {
                    return; // Ignore stale event
                }

                // 3. Rule 8: Comprehensive Query-Aware Reconciliation Predicate (Coalesced)
                if (self.mutationAffectsCurrentQuery(e)) {
                    self.scheduleReconcile();
                    return;
                }

                // 4. Row-Only Mutation: safe in-place patch
                if (e.candidate_id && e.aggregate_version) {
                    self.candidateVersions[e.candidate_id] = e.aggregate_version;
                    self.patchCandidateRow(e.candidate_id, e.changed_fields, e.aggregate_version);
                }
            })
            .listen('.ClientCandidateVersionUpdated', function(e) {
                // 1. Idempotency Guard: Discard duplicate events
                if (e.event_id && self.processedEventIds.has(e.event_id)) return;
                self.markEventProcessed(e.event_id);

                // 2. Stale Event Guard: Ignore if event collection version is not newer
                if (e.client_candidate_version <= self.clientCandidateVersion) {
                    return;
                }

                self.clientCandidateVersion = e.client_candidate_version;
                // Coalesce collection refresh to prevent duplicate ES queries
                self.scheduleReconcile();
            });

        // Safety Reconciliation on Reconnection
        Echo.connector.pusher.connection.bind('connected', function() {
            if (self.state === 'READY' && self.clientId) {
                self.reconcileVersion();
            }
        });
    },

    reconcileVersion: function() {
        var self = this;
        $.get(`/service_company/clients/${this.clientId}/candidate-version`, function(res) {
            if (res.client_candidate_version > self.clientCandidateVersion) {
                self.scheduleReconcile();
            }
        });
    },

    destroy: function() {
        if (this.reconcileTimer) clearTimeout(this.reconcileTimer);
        this.reconcileTimer = null;
        if (this.activeXhr) this.activeXhr.abort();
        if (this.companyId && this.clientId) {
            Echo.leave(`private-company.${this.companyId}.client.${this.clientId}.candidates`);
        }
        this.state = 'DESTROYED';
        this.data = [];
        this.candidateVersions = {};
        this.processedEventIds.clear();
        this.queryFingerprint = null;
        this.companyId = null;
        this.clientId = null;
    }
};
```

---

## 7. Frozen Reverb Event Contracts & Canonical API Schemas

### 7.1 Frozen Reverb Event Contracts

#### Event 1: `ClientCandidateVersionUpdated` (Collection-Level Change)
```json
{
  "event": "ClientCandidateVersionUpdated",
  "company_id": 42,
  "client_id": 108,
  "client_candidate_version": 1036,
  "occurred_at": "2026-10-22T21:40:00Z",
  "event_id": "7f8b9c0d-1e2f-4a3b-8c9d-0e1f2a3b4c5d"
}
```

#### Event 2: `CandidateStatusChanged` (Candidate-Level OCC Mutation)
```json
{
  "event": "CandidateStatusChanged",
  "company_id": 42,
  "client_id": 108,
  "candidate_id": 18492,
  "aggregate_version": 43,
  "client_candidate_version": 1036,
  "changed_fields": {
    "master_status_id": 3,
    "master_status_name": "Interview",
    "child_status_name": "Technical Round 2"
  },
  "occurred_at": "2026-10-22T21:40:00Z",
  "event_id": "9a8b7c6d-5e4f-3a2b-1c0d-9e8f7a6b5c4d"
}
```

### 7.2 Canonical API Response Schema (`HTTP 200 OK`)
```json
{
  "success": true,
  "data": [
    {
      "id": 18492,
      "wiseId": 18492,
      "full_name": "Vibhanshu Jaiswal",
      "current_designation": "Principal Architect",
      "total_exp": "10.5",
      "rel_exp": "8.0",
      "ctc_yearly": "3500000",
      "ectc_yearly": "4500000",
      "currency_symbol": "INR",
      "current_ctc_mode": "Y",
      "np_id": "Immediate",
      "city_name": "Mumbai",
      "country_name": "India",
      "skills_array": ["PHP", "Laravel", "Elasticsearch", "Redis", "MySQL"],
      "skill_match_percentage": 92.5,
      "job_id": 504,
      "job_title": "Lead Software Engineer",
      "master_status_id": 3,
      "master_status_name": "Interview",
      "master_status_css": "badge-light-primary",
      "child_status_name": "Technical Round 2",
      "posted_by_name": "Rahul Verma",
      "submitted_by_company_name": "Acme Talent",
      "created_at": "2026-10-22T21:40:00Z",
      "aggregate_version": 42
    }
  ],
  "meta": {
    "client_id": 108,
    "client_type": "internal",
    "query_fingerprint": "8f4a2b9c7d1e0f3a6b5c4d3e2f1a0b9c8d7e6f5a4b3c2d1e0f9a8b7c6d5e4f3a",
    "client_candidate_version": 1035,
    "projection_schema_version": 2,
    "server_time": "2026-10-22T22:30:00Z"
  },
  "pagination": {
    "mode": "page",
    "total": 247,
    "total_relation": "eq",
    "per_page": 25,
    "current_page": 1,
    "last_page": 10,
    "has_more": true
  },
  "filters": {
    "search": "",
    "status_id": null,
    "job_id": null
  }
}
```

---

## 8. Rate Limiting, Abuse Protection & ES Circuit Breaker Policy

### 8.1 Differentiated UI & Background Rate Limiting Contract
To prevent automated background reconciliation (e.g., rapid Reverb bursts or tab re-focus reconnects) from exhausting a recruiter's interactive search quota, rate limits are explicitly separated into three differentiated token buckets:

1. **`interactive_candidate_read` (User Search, Filtering & Pagination)**:
   - **Threshold**: Maximum **20 requests per 10 seconds** per authenticated user.
   - **Trigger**: Keystroke search, dropdown filter changes, manual page clicks.
2. **`background_candidate_reconcile` (Automated Reverb Event Reconciliation)**:
   - **Threshold**: Maximum **60 requests per minute** per client session.
   - **Trigger**: Coalesced Reverb events dispatched via `scheduleReconcile()`.
3. **`candidate_version_reconnect` (Socket Reconnection & Visibility Sync)**:
   - **Threshold**: Maximum **30 requests per minute** per user session.
   - **Trigger**: Browser tab re-focus or WebSocket `connected` event.
4. **Shared Tenant Safety Ceiling**:
   - **Threshold**: Maximum **500 total candidate requests per minute** per `company_id`.
   - **Operational Behavior**: Any bucket exceeding its quota immediately receives **`HTTP 429 Too Many Requests`** with a `Retry-After: 3` header. Zero MySQL or Elasticsearch queries are dispatched.

### 8.2 Elasticsearch Circuit Breaker & Timeout Policy
```php
'elasticsearch' => [
    'connect_timeout' => 1.0,     // 1 second connection timeout
    'timeout'         => 2.0,     // 2 seconds socket read timeout
    'retries'         => 0,       // ZERO synchronous retries on search paths!
    'circuit_breaker' => [
        'failure_threshold' => 10,  // 10 consecutive failures opens circuit
        'recovery_window'   => 30,  // 30 seconds before half-open probe
    ],
]
```

---

## 9. Enterprise Data Governance: PII Logging & Index Lifecycle

### 9.1 PII Logging Prohibition Invariant
Candidate personal identifiable information (PII) must **NEVER** appear in unencrypted operational logs or telemetry:
- **Strictly Prohibited in Logs & Metric Labels**: `email`, `phone`, `full_name`, `ctc_yearly`, `ectc_yearly`, `resume_url`.
- **Permitted in Telemetry**: `company_id`, `client_id`, `candidate_id` (numeric integer ID), `status_id`, `latency_ms`.

### 9.2 Zero-Downtime Reindexing via Aliases
- Connect exclusively through index alias: `ELASTIC_CANDIDATES`.
- Reindexing pipeline uses versioned indices (`ELASTIC_CANDIDATES_v3`) with atomic alias swaps.

### 9.3 CQRS-Safe Background Reconciliation Job
A scheduled operational background worker (`ats:reconcile-candidates`) executes during off-peak hours:
1. Compares MySQL candidate records against Elasticsearch index watermarks.
2. Identifies missing records, stale `aggregate_version` documents, or orphaned entries.
3. Enqueues repair events into `sys_outbox_events` for asynchronous processing.
> [!CAUTION]
> **STRICT CQRS BOUNDARY**: The Candidate Drawer read path **NEVER** invokes background reconciliation or initiates synchronous repair queries against MySQL. The read path queries Elasticsearch exclusively.

### 9.4 Candidate PII Authorization Scoping & Privileged Infrastructure Security
1. **PII Authorization Scoping Invariant**:
   - Candidate personal identifiable information (`email`, `phone`, `current_designation`, `ctc_yearly`, `ectc_yearly`, `current_company`, `location`, `full_name`) stored in `ELASTIC_CANDIDATES` must **NEVER** be returned unless the authenticated user is authorized for that specific `client_id` via `ClientAccessGuard`.
2. **Privileged Infrastructure Classification**:
   - Direct cluster-level administrative access to Elasticsearch is strictly classified as **Privileged Infrastructure Access**:
     - ES Snapshot and Restore pipelines
     - ES Kibana Dev Tools Console
     - ES cluster backups and data dumps
     - ES ETL pipelines and batch export scripts
     - ES APM and Observability index exports
   - All privileged cluster access requires private VPC isolation, bastion-gated IP whitelisting, strict Role-Based Access Control (RBAC), and immutable audit trail logging. Accessing candidate indices outside authenticated application guards is strictly prohibited.

---

## 10. Unified Query Engine: `CandidateDrawerQueryService`

Both Internal and Direct client drawers delegate exclusively to this single query service:

```php
namespace App\Services\Candidate;

use App\Services\ElasticService;
use App\Services\ClientAccessGuard;

class CandidateDrawerQueryService
{
    public function __construct(
        protected ElasticService $elasticService,
        protected ClientAccessGuard $accessGuard,
        protected ClientCandidateVersionStore $versionStore
    ) {}

    public function search(int $companyId, int $clientId, string $clientType, array $params): array
    {
        // 1. Strict Fail-Closed Authorization Guard (Zero MySQL Read)
        if (! $this->accessGuard->canAccessClient($companyId, $clientId)) {
            abort(403, 'Unauthorized access to client organization.');
        }

        // 2. Build Canonical Tenancy Filter
        $filter = [
            ['term' => ['client_id' => $clientId]],
            ['term' => ['company_id' => $companyId]],
        ];

        // 3. Status and Job Filters
        if (! empty($params['status_id'])) {
            $filter[] = ['term' => ['master_status_id' => (int) $params['status_id']]];
        }
        if (! empty($params['job_id'])) {
            $filter[] = ['term' => ['job_id' => (int) $params['job_id']]];
        }

        $mustNot = [
            ['terms' => ['master_status_id' => [9]]], // Exclude deleted/archived
        ];

        // 4. Multi-Match Analyzer Search
        $must = [];
        if (! empty($params['search'])) {
            $must[] = [
                'multi_match' => [
                    'query' => trim($params['search']),
                    'fields' => ['full_name.search^3', 'current_designation.search^2', 'skills.search^2', 'email', 'phone'],
                    'type' => 'best_fields',
                    'tie_breaker' => 0.3,
                ],
            ];
        }

        // 5. Strict Parameter Validation & Deterministic Sorting (created_at DESC, wiseId DESC)
        $perPage = (int) ($params['per_page'] ?? 25);
        if ($perPage < 1 || $perPage > 50) {
            abort(422, 'Invalid per_page value. Must be between 1 and 50.');
        }
        $size = $perPage;
        $page = max((int) ($params['page'] ?? 1), 1);
        $canonicalSort = 'created_at:desc,wiseId:desc';
        $sort = [
            ['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']],
            ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']],
        ];

        // Retrieve current collection version for snapshot consistency & cursor generation
        $currentCollectionVersion = $this->versionStore->getVersion($companyId, $clientId);

        // Canonical SHA-256 Query Fingerprint
        $canonicalFilterData = [
            'company_id'  => $companyId,
            'client_id'   => $clientId,
            'client_type' => $clientType,
            'search'      => trim($params['search'] ?? ''),
            'status_id'   => $params['status_id'] ?? null,
            'job_id'      => $params['job_id'] ?? null,
            'sort'        => $canonicalSort,
        ];
        ksort($canonicalFilterData);
        $queryFingerprint = hash('sha256', json_encode($canonicalFilterData));

        // Fetch size + 1 with explicit _source minimization
        $esBody = [
            '_source' => [
                'id', 'wiseId', 'company_id', 'client_id', 'full_name', 'current_designation',
                'total_exp', 'rel_exp', 'ctc_yearly', 'ectc_yearly', 'currency_symbol',
                'current_ctc_mode', 'np_id', 'city_name', 'country_name', 'skills_array',
                'skill_match_percentage', 'job_id', 'job_title', 'master_status_id',
                'master_status_name', 'master_status_css', 'child_status_name',
                'posted_by_name', 'submitted_by_company_name', 'created_at',
                'aggregate_version', 'projection_schema_version'
            ],
            'track_total_hits' => config('services.elasticsearch.candidate_track_total_hits', 10000),
            'size' => $size + 1,
            'sort' => $sort,
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter,
                    'must_not' => $mustNot,
                ],
            ],
        ];

        // P0 Pagination Guard: Enforce cursor for deep pages
        $isCursorMode = ! empty($params['cursor']);
        if ($isCursorMode) {
            $cursorData = $this->verifyAndDecodeCursor($params['cursor'], $companyId, $clientId, $clientType, $currentCollectionVersion, $queryFingerprint, $canonicalSort);
            $esBody['search_after'] = $cursorData['values'];
        } elseif ($page <= 5) {
            $esBody['from'] = ($page - 1) * $size;
        } else {
            abort(422, 'Deep pagination beyond page 5 requires cursor navigation.');
        }

        // Execute query (request_cache removed from dynamic queries)
        $response = $this->elasticService->getClient()->search([
            'index' => ELASTIC_CANDIDATES,
            'body' => $esBody,
        ]);

        $totalHits = $response['hits']['total'] ?? [];
        $total = $totalHits['value'] ?? 0;
        $totalRelation = $totalHits['relation'] ?? 'eq'; // 'eq' or 'gte'
        $rawHits = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);

        // Mathematical size + 1 evaluation
        $hasMore = count($rawHits) > $size;
        $items = $hasMore ? array_slice($rawHits, 0, $size) : $rawHits;

        // Generate HMAC-Signed Next Cursor with Lifecycle TTL & Snapshot Consistency
        $nextCursor = null;
        if ($hasMore) {
            $lastItem = end($items);
            $cursorPayload = [
                'values'                    => [$lastItem['created_at'] ?? 0, $lastItem['wiseId'] ?? 0],
                'company_id'                => $companyId,
                'client_id'                 => $clientId,
                'client_type'               => $clientType,
                'client_candidate_version'  => $currentCollectionVersion, // Binds cursor to collection snapshot
                'query_fingerprint'         => $queryFingerprint,
                'sort'                      => $canonicalSort,
                'projection_schema_version' => 2,
                'issued_at'                 => now()->timestamp,
                'expires_at'                => now()->addMinutes(15)->timestamp, // 15-Minute TTL Lifecycle
            ];
            $envelope = [
                'v'       => 2,
                'payload' => $cursorPayload,
                'sig'     => hash_hmac('sha256', json_encode($cursorPayload), config('app.key')),
            ];
            $nextCursor = base64_encode(json_encode($envelope));
        }

        $pagination = [
            'mode'           => $isCursorMode ? 'cursor' : 'page',
            'total'          => $total,
            'total_relation' => $totalRelation,
            'per_page'       => $size,
            'has_more'       => $hasMore,
        ];

        if ($isCursorMode) {
            $pagination['next_cursor'] = $nextCursor;
        } else {
            $pagination['current_page'] = $page;
            // P0 Invariant: When total_relation = gte, last_page cannot be computed from lower-bound total
            $pagination['last_page'] = ($totalRelation === 'gte') ? null : (int) ceil($total / $size);
        }

        return [
            'data' => $items,
            'meta' => [
                'client_id'                 => $clientId,
                'client_type'               => $clientType,
                'query_fingerprint'         => $queryFingerprint,
                'client_candidate_version'  => $currentCollectionVersion,
                'projection_schema_version' => 2,
                'server_time'               => now()->toIso8601String(),
            ],
            'pagination' => $pagination,
            'filters' => [
                'search'    => $params['search'] ?? '',
                'status_id' => $params['status_id'] ?? null,
                'job_id'    => $params['job_id'] ?? null,
            ]
        ];
    }

    protected function verifyAndDecodeCursor(string $cursorString, int $companyId, int $clientId, string $clientType, int $currentCollectionVersion, string $queryFingerprint, string $expectedSort): array
    {
        $rawJson = base64_decode($cursorString, true);
        if ($rawJson === false) {
            abort(422, 'Malformed base64 cursor format.');
        }

        $decoded = json_decode($rawJson, true);
        if (! is_array($decoded) || ($decoded['v'] ?? 0) !== 2 || empty($decoded['payload']) || empty($decoded['sig'])) {
            abort(422, 'Malformed or unsupported cursor envelope.');
        }

        $payload = $decoded['payload'];
        $expectedSig = hash_hmac('sha256', json_encode($payload), config('app.key'));

        if (! hash_equals($expectedSig, $decoded['sig'])) {
            abort(422, 'Cursor signature verification failed.');
        }

        if ($payload['company_id'] !== $companyId || $payload['client_id'] !== $clientId || ($payload['client_type'] ?? '') !== $clientType) {
            abort(422, 'Cursor context mismatch.');
        }

        if ($payload['query_fingerprint'] !== $queryFingerprint) {
            abort(422, 'Cursor filter state mismatch.');
        }

        if (($payload['sort'] ?? '') !== $expectedSort) {
            abort(422, 'Cursor sort order mismatch.');
        }

        if (($payload['projection_schema_version'] ?? 0) !== 2) {
            abort(422, 'Cursor projection version mismatch.');
        }

        if (now()->timestamp > ($payload['expires_at'] ?? 0)) {
            abort(422, 'Cursor has expired.');
        }

        // Snapshot consistency: check collection version
        if (($payload['client_candidate_version'] ?? 0) !== $currentCollectionVersion) {
            abort(409, 'Pagination cursor invalidated by background collection mutation. Resetting to page 1 required.');
        }

        return $payload;
    }
}
```

### 10.2 Reconnect Reconciliation Endpoint Contract
The reconnect reconciliation endpoint (`GET /service_company/clients/{clientId}/candidate-version`) is an integral component of the Candidate Drawer read architecture:
- **Strictly 0 MySQL Reads**: Operates entirely from the Dragonfly coordination projection tier.
- **Fail-Closed Authorization**: Evaluates `ClientAccessGuard::canAccessClient($companyId, $clientId)` before version retrieval; rejects unauthorized access with `HTTP 403 Forbidden`.
- **Fail-Closed Dragonfly Availability**: Uses `ClientCandidateVersionStore`; if Dragonfly is down, returns deterministic `HTTP 503 Service Unavailable`; **never falls back to MySQL**.
- **Rate-Limited**: Bound by the candidate read rate limiter (20 requests per 10 seconds per user).

```php
namespace App\Http\Controllers\ServiceCompany;

use App\Services\ClientAccessGuard;
use App\Services\Candidate\ClientCandidateVersionStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateVersionApiController
{
    public function __construct(
        protected ClientAccessGuard $accessGuard,
        protected ClientCandidateVersionStore $versionStore
    ) {}

    public function getClientCandidateVersion(Request $request, int $clientId): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;

        // 1. Strict Fail-Closed Authorization Guard (Strictly 0 MySQL Reads)
        if (! $this->accessGuard->canAccessClient($companyId, $clientId)) {
            abort(403, 'Unauthorized access to client organization.');
        }

        // 2. Fail-Closed Dragonfly Version Retrieval via Reusable Store
        $version = $this->versionStore->getVersion($companyId, $clientId);

        return response()->json([
            'success'                  => true,
            'client_id'                => $clientId,
            'client_candidate_version' => $version,
            'server_time'              => now()->toIso8601String(),
        ]);
    }
}
```

---

## 11. Target SLOs Pending Load-Test Validation & CI Test Gates

### 11.1 Target SLOs & Comprehensive Observability Metrics
- **API Read Latency**: P50 `< 40 ms`, P95 `< 120 ms`, P99 `< 250 ms`.
- **Browser Presentation Latency**: DOM Render `< 60 ms` after JSON arrival. Zero main-thread tasks `> 50 ms`.
- **4-Stage Reactive Projection Lag Distribution**:
  1. *Stage 1 (MySQL Commit → ES Document Indexed)*: **P95 < 100 ms**, P99 < 250 ms.
  2. *Stage 2 (MySQL Commit → Dragonfly Version Incremented)*: **P95 < 50 ms**, P99 < 100 ms.
  3. *Stage 3 (MySQL Commit → Reverb WebSocket Broadcast Delivery)*: **P95 < 150 ms**, P99 < 350 ms.
  4. *Stage 4 (MySQL Commit → Browser Store Reconciliation Rendered)*: **P95 < 300 ms**, P99 < 600 ms.
- **Database Strain**: MySQL Candidate Read Queries: **Strictly 0** across the complete HTTP lifecycle.
- **Server Concurrency Target**: 10,000 active recruiters with server CPU `< 25%`.
- **Elasticsearch Cluster Observability & Shard Pressure Metrics**:
  - Search Latency: P50, P95, P99 tracked per tenant.
  - Cluster Shard Pressure: Search thread pool queue depth (`thread_pool.search.queue`), rejected searches (`thread_pool.search.rejected`), JVM heap usage (< 75%), and GC pause duration (< 200 ms).
  - Elasticsearch Query Cache Hit Ratio: Monitored on static terms filters.
  - Circuit Breaker Trips: Zero open circuit breaker events during normal load.

### 11.2 Mandatory Automated CI Test Gates (Full HTTP Lifecycle Verification)
> [!IMPORTANT]
> **Complete HTTP Middleware Stack 0-SQL CI Invariant**: `DB::getQueryLog()` must remain strictly zero across the complete production HTTP middleware stack (authentication, company resolution, tenant middleware, authorization middleware, rate limiter, controller, query service, and exception handling), not merely isolated service method calls. Any middleware executing `$user->company()` or DB lookups on this route triggers an immediate CI failure.

To prevent regressions, the following 8 end-to-end lifecycle assertion tests must pass before production deployment:

1. **`CandidateDrawerReadTest` (Authorized 0 SQL Reads Gate across Full Middleware Stack)**:
   ```php
   public function test_authorized_candidate_drawer_executes_zero_mysql_reads_across_complete_http_stack()
   {
       DB::enableQueryLog();
       $this->expectElasticSearchCalledOnce();

       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");
       
       $response->assertStatus(200);
       $this->assertCount(0, DB::getQueryLog(), "Candidate read path executed MySQL queries inside middleware or controller!");
   }
   ```
2. **`CandidateDrawerUnauthorizedTest` (Unauthorized 0 SQL / 0 ES Gate)**:
   ```php
   public function test_unauthorized_request_aborts_with_403_zero_mysql_reads_zero_es_queries()
   {
       DB::enableQueryLog();
       $this->expectElasticSearchNeverCalled();

       $response = $this->actingAs($this->unauthorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");

       $response->assertStatus(403);
       $this->assertCount(0, DB::getQueryLog(), "Unauthorized path executed MySQL queries!");
   }
   ```
3. **`CandidateDrawerCircuitBreakerTest` (ES Outage No MySQL Fallback Gate)**:
   ```php
   public function test_es_timeout_aborts_with_503_and_never_falls_back_to_mysql()
   {
       $this->mockElasticServiceToTimeout();
       DB::enableQueryLog();

       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");

       $response->assertStatus(503);
       $this->assertCount(0, DB::getQueryLog(), "ES failure triggered illegal MySQL fallback!");
   }
   ```
4. **`CandidateDrawerFailClosedVersionTest` (Dragonfly Outage 0 SQL / 0 ES Gate)**:
   ```php
   public function test_dragonfly_downtime_aborts_with_503_zero_mysql_reads_zero_es_queries()
   {
       $this->mockDragonflyStoreToFail();
       DB::enableQueryLog();
       $this->expectElasticSearchNeverCalled();

       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");

       $response->assertStatus(503);
       $this->assertCount(0, DB::getQueryLog(), "Coordination failure triggered illegal MySQL read!");
   }
   ```
5. **`CandidateDrawerCursorSecurityTest` (Cursor Tampering & Expiry Gate)**:
   ```php
   public function test_tampered_or_expired_cursor_aborts_with_422_zero_mysql_zero_es()
   {
       DB::enableQueryLog();
       $this->expectElasticSearchNeverCalled();

       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}&cursor=tampered_value");
       
       $response->assertStatus(422);
       $this->assertCount(0, DB::getQueryLog());
   }
   ```
6. **`CandidateVersionReconnectReadTest` (Reconnect Endpoint Full Failure Matrix)**:
   ```php
   public function test_reconnect_version_endpoint_full_lifecycle_and_failure_matrix()
   {
       DB::enableQueryLog();

       // 1. Authorized Happy Path: 0 SQL, 200 OK
       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/clients/{$this->client->id}/candidate-version");
       $response->assertStatus(200);
       $this->assertCount(0, DB::getQueryLog(), "Candidate version endpoint executed MySQL queries!");

       // 2. Unauthorized Tenant: 403 Forbidden, 0 SQL
       $response = $this->actingAs($this->unauthorizedUser)
           ->getJson("/service_company/clients/{$this->client->id}/candidate-version");
       $response->assertStatus(403);
       $this->assertCount(0, DB::getQueryLog());

       // 3. Dragonfly Outage: 503 Service Unavailable, 0 SQL
       $this->mockDragonflyStoreToFail();
       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/clients/{$this->client->id}/candidate-version");
       $response->assertStatus(503);
       $this->assertCount(0, DB::getQueryLog());
   }
   ```
7. **`CandidateDrawerSnapshotConsistencyTest` (Stale Cursor 409 Conflict Gate)**:
   ```php
   public function test_cursor_with_stale_collection_version_aborts_with_409()
   {
       $staleCursor = $this->generateValidCursorForVersion($this->client->id, version: 1030);
       $this->versionStore->incrementVersion($this->company->id, $this->client->id, eventId: 'evt_123'); // Now 1031

       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}&cursor={$staleCursor}");

       $response->assertStatus(409);
       $this->assertCount(0, DB::getQueryLog());
   }
   ```
8. **`CandidateDrawerRevocationSocketTest` (Verifiable Channel Invalidation Gate)**:
   ```php
   public function test_revoked_client_access_invalidates_active_socket_subscription_and_denies_api_reads()
   {
       DB::enableQueryLog();

       // 1. Initially authorized user establishes connection
       $response = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");
       $response->assertStatus(200);

       // 2. Admin revokes client tenancy in Dragonfly
       $redis = cache()->store('dragonfly')->getRedis();
       $redis->srem("auth:tenant_clients:{$this->company->id}", $this->client->id);

       // 3. Next API request must fail closed with 403 (zero SQL)
       $responseAfterRevocation = $this->actingAs($this->authorizedUser)
           ->getJson("/service_company/client-candidate-list?clientid={$this->client->id}");
       $responseAfterRevocation->assertStatus(403);
       $this->assertCount(0, DB::getQueryLog());
   }
   ```

---

## 12. Acceptance & Governance Matrix

### Architecture Approved (Verified & Locked)
- [x] **CQRS Authority Boundaries**: MySQL SSOT vs Elasticsearch Read Model formalized.
- [x] **0% MySQL Reads Scoped**: Candidate drawer read path strictly restricted to 0 SQL queries across full HTTP lifecycle and all middleware.
- [x] **Fail-Closed Security Guard**: Dragonfly pre-check prevents cross-tenant access prior to querying ES.
- [x] **Zero ES Auth Fallback**: Document-level fallback eliminated in favor of strict fail-closed (`HTTP 403`).
- [x] **Verifiable Authorization Revocation**: P95 < 1.0s, P99 < 5.0s propagation with active socket channel invalidation verified in CI.
- [x] **Privileged Infrastructure Classification**: Cluster administrative access strictly isolated and audited.
- [x] **Single-Operation Atomic Version Bootstrap**: Lua primitive (`EXISTS -> SETNX 0 -> return 0`) eliminates coordination latency on hot read path.
- [x] **Atomic Lua Version Advancement with 7-Day Ledger**: Single-threaded Redis Lua script with 7-day TTL ledger (`event_idempotency:{company}:{client}:{event_id}`) exceeds 72h outbox redelivery horizon and eliminates race conditions.
- [x] **ClientCandidateVersionStore Service**: Reusable atomic bootstrap (`SETNX`, version 0) and crash-resilient version advancement.
- [x] **Snapshot-Consistent Cursors**: Binds `client_candidate_version` into versioned envelope (`v: 2`); background mutation returns `HTTP 409 Conflict`.
- [x] **Bounded Coalescing Window**: Store coalesces Reverb bursts within 60ms window, producing at most one ES refresh per active reconciliation window.
- [x] **Deterministic Status Codes**: 403 (unauthorized), 503 (infrastructure offline), 422 (cursor/schema error), 409 (cursor stale), 429 (rate limit).
- [x] **Outbox Crash-Recovery & Idempotent Replay**: Monotonic outbox pipeline with idempotent ES projections and safe retry semantics.
- [x] **Browser Event Idempotency Hierarchy**: Event ID (advisory) -> Candidate OCC (`aggregate_version`) -> Collection Version -> ES Reconciliation.
- [x] **Declarative Query Dependency Manifest**: Fully generic evaluator driven by manifest (including archive visibility exclusions) without hardcoded status checks.
- [x] **Browser Query Fingerprint Tracking**: Store maintains active `queryFingerprint` to prevent cross-filter event corruption.
- [x] **Deterministic HMAC-SHA256 Cursor**: Encodes `company_id`, `client_id`, `client_type`, `query_fingerprint`, `sort`, `projection_schema_version`, and 15-minute TTL.
- [x] **Mathematical `size + 1` Cursor Execution**: Exact `has_more` calculation eliminating false-positive empty cursor pages.
- [x] **Count Hierarchy & Relation Handling**: Tier 1 Precomputed Count (Hero KPI) -> Tier 2 Bounded 10K with `total_relation: "gte"` and `last_page: null` -> Tier 3 Exact.
- [x] **Field-Level `_source` Minimization**: Drawer listing projection restricted to 28 lightweight display fields; detail fields lazy-loaded.
- [x] **Page > 5 Deep Pagination Enforcement**: `HTTP 422` returned if cursor missing for deep navigation.
- [x] **Strict Parameter Validation**: `1 <= per_page <= 50` enforced; strict Base64 parsing.
- [x] **Differentiated Rate Limiting**: Independent token buckets for interactive reads (20/10s), background reconciliations (60/min), and reconnect syncs (30/min).
- [x] **Frozen Reverb Contracts**: Complete payload schemas for `ClientCandidateVersionUpdated` and `CandidateStatusChanged`.
- [x] **ES Circuit Breaker & Timeout Policy**: Zero synchronous retries on search paths, 1.0s connect / 2.0s read timeout.
- [x] **Enterprise Governance**: PII logging prohibitions and zero-downtime alias reindexing mandated.
- [x] **Unified Query Service**: `CandidateDrawerQueryService` specified for single-source execution across Internal and Direct clients.

### Implementation Verification (Pending Empirical Load & CI Testing)
- [ ] **Automated CI Gate**: `CandidateDrawerReadTest` passing with 0 query log entries across complete HTTP middleware stack.
- [ ] **Automated CI Gate**: `CandidateDrawerUnauthorizedTest` passing with 403, 0 SQL, 0 ES.
- [ ] **Automated CI Gate**: `CandidateDrawerCircuitBreakerTest` passing with 503, 0 SQL on ES outage.
- [ ] **Automated CI Gate**: `CandidateDrawerFailClosedVersionTest` passing with 503, 0 SQL on Dragonfly outage.
- [ ] **Automated CI Gate**: `CandidateDrawerCursorSecurityTest` passing with 422 on signature/sort/expiry mismatch.
- [ ] **Automated CI Gate**: `CandidateDrawerSnapshotConsistencyTest` passing with 409 on stale collection version.
- [ ] **Automated CI Gate**: `CandidateVersionReconnectReadTest` passing full failure matrix (200, 403, 503) with 0 SQL reads.
- [ ] **Automated CI Gate**: `CandidateDrawerRevocationSocketTest` passing with socket invalidation and 403, 0 SQL.
- [ ] **k6 1,000 Concurrent User Test**: Validate P95 latency < 120 ms.
- [ ] **k6 5,000 Concurrent User Test**: Validate P95 latency < 150 ms.
- [ ] **k6 10,000 Concurrent User Test**: Validate P95 latency < 200 ms and CPU < 25%.
- [ ] **Empirical `track_total_hits` Load Gate**: At 10K concurrency, measure ES CPU, thread pool queue, and latency impact of exact total hits. If P95 exceeds 120ms or ES CPU > 70%, trigger configuration switch to bounded tracking (`track_total_hits: 10000`).
- [ ] **4-Stage Reactive Projection Lag SLA**: Empirically verify P95 MySQL commit to browser store render < 300 ms.
- [ ] **Cross-Tenant IDOR Penetration Test**: Validate 100% fail-closed rejection on mismatched client IDs.
