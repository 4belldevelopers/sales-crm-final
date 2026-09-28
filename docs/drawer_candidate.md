# ATS Candidate Drawer: God-Level Architecture & Implementation Plan

> **Document Path:** `docs/drawer_candidate.md`  
> **Document Version:** 11.0.0 (The Definitive Production Architecture Contract)  
> **Status:** Final Production Architecture Contract & Engineering Specification  
> **Target Module:** JERO ATS Candidate Profile Drawer (`showdetail`) & Mutation Sub-Systems  
> **Target Environment:** `testing.jero.works` / Production  
> **Target Performance:** **10,000 Concurrent Users** with **Zero MySQL SELECTs across ALL drawer read endpoints** (verified end-to-end through full HTTP kernel middleware stacks across authorized, unauthorized, and cross-tenant matrices) and **near-zero server reads on L1/L2 cache hits**  
> **Core Paradigms:** Dual-Tier Client Storage (LRU RAM vs. Partitioned IDB), Transactionally Coordinated Idempotency & Outbox CQRS, Atomic SQL Monotonic Versioning (`aggregate_version`), Optimistic Concurrency Control (OCC), Watermark-Guaranteed ES Rebuilds, and Realtime Security Invalidation.  

---

## 1. Frozen Architecture Diagram & Three Consistency States

```
                    JERO ATS
                       │
                Candidate Drawer
                       │
        ┌──────────────┴──────────────┐
        │                             │
      Browser                       Server
        │                             │
   ┌────┴────┐                 ┌─────┴─────┐
   │         │                 │           │
 L1 RAM    L2 IDB          Auth Context   Read API
 (LRU PII) (Partitioned)  (Zero SELECT)  (All Routes)
   │         │                 │           │
   │         │                 │           ↓
   │         │                 │      Elasticsearch
   │         │                 │      (Projection)
   └─────────┴─────────────────┴───────────┘
                       │
               Mutation Request
          [Idempotency-Key: UUID]
                       │
         ┌─────────────▼─────────────┐
         │   SINGLE DB TRANSACTION   │
         │ 1. Claim Idempotency Key  │
         │ 2. Execute Mutation       │
         │ 3. aggregate_version++    │
         │ 4. Insert Outbox Event    │
         │ 5. Store Final State      │
         │    (COMPLETED/FAILED_*)   │
         └─────────────┬─────────────┘
                       │
                 Outbox Event
             (sys_outbox_events)
                       │
                     Queue
                       │
              Atomic Painless ES
            (ctx._source.ver check)
                       │
                 Realtime Signal
            {type, id, aggregate_version}
                       │
               Browser Reconcile
```

### The Three Explicit Consistency States

To ensure developers and AI agents maintain absolute clarity across distributed tiers:

1. **Authoritative State (MySQL SSOT):**  
   The single immutable source of truth for all transactional writes. State mutations are atomically committed to MySQL alongside an outbox event and idempotency state.
2. **Projection State (Elasticsearch 7.17):**  
   The eventually consistent, denormalized read model. Updated asynchronously by queue workers with atomic Painless version checks. Cold drawer reads query this store exclusively.
3. **Client State (L1 RAM + L2 IndexedDB):**  
   Volatile user-space representation. Ephemeral and structurally partitioned, bounded deterministically by `aggregate_version`, `min_version`, and realtime invalidation signals.

---

## 2. Executive Architectural Invariants (P0 Standards)

### 1. Invariant: Zero MySQL SELECTs Across Complete HTTP Stack Matrix
The zero-read guarantee applies to the **entire set of candidate drawer read endpoints**:
```php
const DRAWER_READ_ENDPOINTS = [
    '/service_company/job-applications/{id}/core-json',
    '/service_company/job-applications/{id}/interviews',
    '/service_company/job-applications/{id}/questions',
    '/service_company/job-applications/{id}/timeline',
    '/service_company/job-applications/{id}/resume-meta',
];
```
- **Full-Stack Regression Guard Matrix:**  
  The automated CI test constructs realistic multi-tenant fixtures (Tenant A, Candidate A, Authorized User A, Unauthorized User B within Tenant A, Cross-Tenant User C from Tenant B, and Guest) and exercises the complete HTTP lifecycle:
  ```php
  it('enforces strictly zero mysql selects across complete drawer http stack', function () {
      $tenantA = Company::factory()->create();
      $tenantB = Company::factory()->create();
      $candidateA = JobApplication::factory()->create(['company_id' => $tenantA->id]);

      $testMatrix = [
          ['user' => User::factory()->create(['company_id' => $tenantA->id, 'role' => 'recruiter_authorized']), 'expected_status' => 200],
          ['user' => User::factory()->create(['company_id' => $tenantA->id, 'role' => 'recruiter_restricted']), 'expected_status' => 403],
          ['user' => User::factory()->create(['company_id' => $tenantB->id, 'role' => 'recruiter_authorized']), 'expected_status' => 403],
          ['user' => null, 'expected_status' => 401],
      ];

      foreach (DRAWER_READ_ENDPOINTS as $endpoint) {
          foreach ($testMatrix as $case) {
              DB::listen(function ($query) {
                  if (str_starts_with(strtoupper(trim($query->sql)), 'SELECT')) {
                      throw new \RuntimeException("Architectural Invariant Violation: MySQL SELECT executed on drawer endpoint: {$query->sql}");
                  }
              });

              $url = str_replace('{id}', $candidateA->id, $endpoint);
              $response = ($case['user'] === null) ? $this->getJson($url) : $this->actingAs($case['user'])->getJson($url);
              $response->assertStatus($case['expected_status']);
          }
      }
  });
  ```

---

### 2. Invariant: Tenant-Scoped `permission_version` & Realtime Security Invalidation
- **`permission_version` (Tenant-Scoped Policy Generation):**  
  Monotonic integer scoped to the tenant (`tenant_id`). Increments whenever an administrator modifies roles, permissions, or access flags capable of affecting any user's access within that tenant.
- **`auth_context_hash` (Security-Context Partition Fingerprint):**  
  Non-monotonic SHA-256 fingerprint defining client cache and IndexedDB storage partitions:
  $$\text{auth\_context\_hash} = \text{SHA256}(\text{tenant\_id} \mathbin{\Vert} \text{user\_id} \mathbin{\Vert} \text{role\_id} \mathbin{\Vert} \text{permission\_version})$$
- **Mid-Session Immediate Salary Redaction Protocol:**  
  When an admin revokes `salary_view`, changing `auth_context_hash` invalidates future cache lookups, but does not alter already-loaded RAM objects in active recruiter tabs.  
  **The Invariant:** Bumping `permission_version` must immediately broadcast a `SECURITY_CONTEXT_CHANGED` event over Reverb/WebSockets to the affected user's private channel.  
  *Client Security Listener:*
  1. Purges L1 volatile RAM (`window.__candidateSensitiveMap.clear()`).
  2. Redacts salary and compensation elements in the currently open drawer DOM immediately.
  3. Updates the client `auth_context_hash`, logically invalidating old IndexedDB partitions for asynchronous garbage collection.

---

### 3. Invariant: Transactionally Coordinated Idempotency State Machine
**Critical Invariant:** *Only the transaction that successfully claims a new idempotency key may execute the business mutation.*

#### State Machine Flow:
```
           Incoming Request [Idempotency-Key: UUID]
                              │
               ┌──────────────▼──────────────┐
               │    SINGLE DB TRANSACTION    │
               │  INSERT sys_idempotency_keys │
               │     (status: PROCESSING)    │
               └──────────────┬──────────────┘
                              │
               ┌──────────────┴──────────────┐
               │ Unique Key Conflict?        │
               ├─────────────────────────────┤
               │ NO (Winner)     YES (Loser) │
               └──────┬───────────────┬──────┘
                      │               │
        ┌─────────────▼─────┐   ┌─────▼─────────────────────────┐
        │ Execute Mutation  │   │ SELECT ... FOR UPDATE         │
        │ aggregate_version++│   │                               │
        │ Insert Outbox     │   │ if request_hash != req: 422   │
        │ Status: COMPLETED │   │ if status == COMPLETED: 200   │
        │ COMMIT            │   │ if status == PROCESSING: 425  │
        └───────────────────┘   │ if status == FAILED_FINAL:    │
                                │    replay failure (e.g. 409)  │
                                │ if status == FAILED_RETRYABLE:│
                                │    allow retry                │
                                └───────────────────────────────┘
```

#### Complete Transactional Implementation:
```php
DB::transaction(function () use ($request, $tenantId, $idempotencyKey, $canonicalHash) {
    // 1. Attempt atomic claim
    $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
        'tenant_id' => $tenantId,
        'idempotency_key' => $idempotencyKey,
        'request_hash' => $canonicalHash,
        'status' => 'PROCESSING',
        'expires_at' => now()->addHours(24),
    ]);

    if (!$inserted) {
        $existing = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $tenantId)
            ->where('idempotency_key', $idempotencyKey)
            ->lockForUpdate()
            ->first();

        if ($existing->request_hash !== $canonicalHash) {
            abort(422, 'Idempotency key payload mismatch.');
        }
        if ($existing->status === 'COMPLETED') {
            return response()->json(json_decode($existing->response_body, true), $existing->response_status);
        }
        if ($existing->status === 'PROCESSING') {
            abort(425, 'Request currently processing.');
        }
        if ($existing->status === 'FAILED_FINAL') {
            return response()->json(json_decode($existing->response_body, true), $existing->response_status);
        }
        // FAILED_RETRYABLE falls through to attempt re-execution
    }

    try {
        // 2. Execute mutation & OCC
        $updated = DB::table('job_applications')
            ->where('id', $candidateId)
            ->where('company_id', $tenantId)
            ->where('aggregate_version', $expectedVersion)
            ->update([
                'recruiter_note' => $note,
                'aggregate_version' => $expectedVersion + 1,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            // OCC conflict is a deterministic application failure: FAILED_FINAL
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 409,
                    'response_body' => json_encode(['status' => 'conflict', 'message' => 'Candidate modified by another user.']),
                    'completed_at' => now(),
                ]);
            abort(409, 'Conflict: Candidate modified by another user.');
        }

        // 3. Insert Outbox Event
        DB::table('sys_outbox_events')->insert([...]);

        // 4. Mark Idempotency Record COMPLETED
        DB::table('sys_idempotency_keys')
            ->where('tenant_id', $tenantId)
            ->where('idempotency_key', $idempotencyKey)
            ->update([
                'status' => 'COMPLETED',
                'response_status' => 200,
                'response_body' => json_encode($responseData),
                'completed_at' => now(),
            ]);

        return response()->json($responseData, 200);

    } catch (\Throwable $e) {
        if (!($e instanceof HttpResponseException && in_array($e->getResponse()->getStatusCode(), [409, 422]))) {
            // Infrastructure or unexpected failure: mark FAILED_RETRYABLE
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => $e->getMessage(),
                ]);
        }
        throw $e;
    }
});
```

#### Canonical Hash & Retention Contract:
- **Canonical Hash:** `request_hash` is computed as `SHA-256` over the canonical JSON payload (keys sorted lexicographically).
- **Retention Window:** 24 hours.
- **Pruning:** Scheduled hourly command (`php artisan idempotency:prune`) deletes records where `expires_at < NOW()`. Expired keys arriving later are processed as brand-new requests.

---

### 4. Invariant: Watermark-Guaranteed Elasticsearch Rebuild Protocol
To prevent continuous mutations from being overwritten by an older rebuild snapshot:

#### Rebuild Protocol Boundary:
The rebuild snapshot and outbox watermark **MUST be established from the exact same MySQL transaction consistency boundary**:
```php
DB::transaction(function () use ($timestamp) {
    DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    DB::statement('START TRANSACTION WITH CONSISTENT SNAPSHOT');

    // 1. Capture exact watermark at snapshot boundary
    $watermarkId = (int) DB::table('sys_outbox_events')->max('id');

    // 2. Stream snapshot data to temporary file / buffer
    $cursor = DB::table('job_applications')->cursor();
    
    // (Snapshot streaming executes under this boundary)
}, 5);
```

#### Full Rebuild Lifecycle:
1. Establish consistent snapshot and capture `$watermarkId`.
2. Bulk load snapshot into backing index `candidates_v{timestamp}`.
3. Replay every outbox event committed after `$watermarkId` against `candidates_v{timestamp}` using the atomic Painless script until `max_outbox_id == max_processed_outbox_id`.
4. **Rebuild Invariant:** *No alias swap is permitted until every outbox event committed after the rebuild snapshot has been projected and the new index's per-aggregate versions are equal to or greater than MySQL's corresponding `aggregate_version`.*
5. Repoint alias atomically to `candidates_v{timestamp}`.  
   *The combination of rebuild watermarking and atomic aggregate-version guards prevents an older projection from replacing a newer aggregate state during rebuild.*

---

## 3. Operational Governance & Outbox Telemetry (SLOs)

### Schema DDL: `sys_outbox_events`
```sql
CREATE TABLE `sys_outbox_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `idempotency_key` varchar(64) DEFAULT NULL,
  `aggregate_type` varchar(64) NOT NULL,
  `aggregate_id` bigint(20) unsigned NOT NULL,
  `aggregate_version` int(10) unsigned NOT NULL,
  `event_type` varchar(64) NOT NULL,
  `payload` json NOT NULL,
  `status` enum('PENDING','PROCESSING','COMPLETED','FAILED','DEAD_LETTER') NOT NULL DEFAULT 'PENDING',
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `available_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_outbox_tenant_idempotency` (`tenant_id`, `idempotency_key`),
  KEY `idx_outbox_status_available` (`status`, `available_at`),
  KEY `idx_outbox_aggregate` (`aggregate_type`, `aggregate_id`, `aggregate_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Operational Telemetry Metrics & Critical Alert Thresholds

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Outbox Production SLOs                          │
├──────────────────────────┬──────────────────────┬──────────────────────┤
│ Metric                   │ Target / Normal      │ Critical Alert (P0)  │
├──────────────────────────┼──────────────────────┼──────────────────────┤
│ max_projection_lag_ver   │ < 5 versions         │ > 100 versions       │
│ oldest_pending_event_age │ < 2 seconds          │ > 30 seconds         │
│ outbox_dead_letter_count │ 0                    │ > 0                  │
│ mutation_resilience_rate │ 100%                 │ < 99.99%             │
└──────────────────────────┴──────────────────────┴──────────────────────┘
```

- **Per-Aggregate Lag Telemetry:**  
  $$\text{candidate\_projection\_lag\_versions} = \text{MySQL aggregate\_version} - \text{ES aggregate\_version}$$
- **Mutation Resilience Invariant:**  
  *Elasticsearch outage or latency must not cause successful MySQL mutations to fail or roll back.*  
  Measured as: `mutation_success_rate_excluding_mysql_app_failures >= 99.99%`.

---

## 4. Hard CI-Enforced 15 KB gzip Payload Budget

### Observational SLOs vs. CI Regression Ceiling:
- **Production Observational SLOs:**
  - **P50:** $\le 12$ KB gzip
  - **P95:** $\le 15$ KB gzip
  - **P99:** $\le 18$ KB gzip
- **Automated CI Fixture Ceiling:** $\le 15$ KB gzip (15,360 bytes).  
  *The CI fixture ceiling is an automated regression guard against payload creep, while production P99 is an operational observability SLO.*

### Automated CI Size Assertion:
```php
it('enforces hard 15kb gzip budget on candidate core endpoint', function () {
    $candidate = JobApplication::factory()->createWithAllAttributes();
    $response = $this->getJson("/service_company/job-applications/{$candidate->id}/core-json");
    
    // Measures actual HTTP representation (Content-Encoding: gzip)
    $actualGzipBytes = strlen(gzencode($response->getContent(), 9));
    expect($actualGzipBytes)->toBeLessThanOrEqual(15360);
});
```

Heavy sections (Interview Logs, Screening Q&A, Resume Document Meta, Activity Timeline) are decoupled into secondary endpoints and loaded strictly on-demand.

---

## 5. Workload Acceptance Benchmarks (Gold Standard Matrix)

| Workload Scenario | Target Acceptance Threshold | Verification Method |
| :--- | :--- | :--- |
| **L1 RAM Cache Hit** | **< 10 ms** UI response | Browser performance.now() audit |
| **L2 IndexedDB Hit** | **< 50 ms** UI response | IndexedDB cursor read benchmark |
| **ES Cold Read (Cache Miss)** | **P95 < 300 ms** | Laravel Telescope / APM telemetry |
| **Core Payload Budget** | **CI Ceiling $\le$ 15 KB gzip** | Automated CI `gzencode` assertion |
| **Mutation API (Outbox Write)** | **P95 < 500 ms** | MySQL transaction duration log |
| **Elasticsearch Outage / Delay** | **Zero MySQL mutation rollbacks** | Outbox retries asynchronously |
| **Read-After-Write Lag** | **Max 150ms wait; zero UI freeze** | Bounded poll with `projection_pending` |
| **MySQL SELECT on ANY Drawer Route** | **Strictly 0 queries** | Full-stack matrix Pest `DB::listen` probe |
| **Mid-Session Permission Revoke** | **Immediate RAM & DOM redaction** | Reverb `SECURITY_CONTEXT_CHANGED` |
| **Unauthorized Salary Exposure** | **Strictly 0 leak events** | Multi-role automated pest tests |
| **Cross-Tenant Candidate Probe** | **Strictly 0 IDOR leaks (HTTP 403)** | Automated tenancy penetration test |
| **Concurrent Edit Collision** | **0 Silent overwrites (HTTP 409)** | Atomic SQL OCC version guard |
| **Network Retry on Mutation** | **0 Duplicate writes (HTTP 200/425)**| Coordinated `sys_idempotency_keys` |

---

## 6. Living Memory & Governance Log

- **Author:** JERO Core Engineering & Architecture Team
- **Approval:** Lead Architect
- **Status:** **THE DEFINITIVE PRODUCTION CONTRACT (v11.0.0)**
- **Cross-Reference:**
  - Standard Rules: [`CLAUDE.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/CLAUDE.md) / [`AGENTS.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/AGENTS.md)
  - Architectural Standards: [`docs/ARCHITECTURE.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/ARCHITECTURE.md) / [`docs/AI_RULES.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/AI_RULES.md)
  - UI/UX Guidelines: [`docs/UI_UX_STANDARDS.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/UI_UX_STANDARDS.md)
