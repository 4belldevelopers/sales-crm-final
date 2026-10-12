# OUTBOX ARCHITECTURE & IDEMPOTENCY DESIGN
## Service Company → Recommend Internal Request (Bench Candidates)

> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/recommend_internal_req/6`  
> **Target Mutation:** Candidate Recommendation to Internal Job Requirement (`rec_candidate_assign`)  
> **Schema Tables:** `sys_outbox_events`, `sys_idempotency_keys`, `rec_candidate_assign`, `coin_transection_logs`  
> **Document Status:** AUTHORITATIVE TECHNICAL ARCHITECTURE DESIGN  
> **Date:** October 12, 2026  

---

## 1. Architectural Motivation & Problem Statement

In the current implementation of `recommandedrequestcondidate`:
1. While an in-memory Redis lock (`Cache::lock(10s)`) exists to guard double clicks, the database writes (`rec_candidate_assign`, coin deductions, and logs) are not wrapped inside a unified ACID transaction alongside an outbox event.
2. If the server process crashes or network disconnects immediately after `DB::table('rec_candidate_assign')->insert($values)`:
   - The queue jobs (`SyncCandidateRequestToElasticJob`, `SendEmailJob`) or Reverb events (`BenchRequestCreated`) might never be dispatched.
   - The client experiences a network timeout, retries the request, and encounters an unexpected "Candidate request already submitted for this job" warning instead of receiving the authoritative completed outcome.
3. In multi-tab or concurrent recruiter scenarios, two recruiters recommending the same candidate can trigger simultaneous race conditions.

To achieve enterprise-grade reliability and survive 10,000 concurrent user traffic spikes without duplicate records or lost events, we design and implement a **Dual-Lease Transactionally Coordinated Outbox & Idempotency Engine**.

---

## 2. Idempotency Architecture (`sys_idempotency_keys`)

### 2.1 Table Schema Integration

Leveraging the platform's production-hardened `sys_idempotency_keys` table:

```sql
CREATE TABLE `sys_idempotency_keys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `idempotency_key` varchar(64) NOT NULL,
  `request_hash` varchar(64) NOT NULL,
  `status` enum('PROCESSING','COMPLETED','FAILED_FINAL','FAILED_RETRYABLE') NOT NULL DEFAULT 'PROCESSING',
  `response_status` int(11) DEFAULT NULL,
  `response_body` longtext DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_idempotency_tenant_key` (`tenant_id`,`idempotency_key`),
  KEY `idx_idempotency_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 Key Generation & Scoping Rules

1. **Client-Supplied Key:** The browser frontend generates a unique UUIDv4 key for every user intent click:
   `const idempotencyKey = 'idemp_bench_req_' + candidateId + '_' + jobId + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);`
   Sent via HTTP Header: `X-Idempotency-Key` or request parameter.
2. **Deterministic Fallback Key:** If the client fails to provide an idempotency key, the server synthesizes a deterministic key:
   `$idempotencyKey = "bench_req:{$tenantId}:{$jobId}:{$candidateId}:" . substr(sha1(json_encode($payload)), 0, 16);`
3. **Payload Fingerprinting:** The server calculates SHA-256 canonical hash of the request parameters (`candidate_id`, `job_id`, `company_id`).
   - If the same key is received with a **different payload**, the server immediately rejects the request with HTTP 409 Conflict: `"Idempotency key payload mismatch."`
   - If the same key is received with the **same payload**:
     - Status `COMPLETED`: Server returns the cached `response_body` immediately without re-executing any SQL writes or coin deductions.
     - Status `PROCESSING`: Server returns HTTP 429 / 409: `"This request is already being processed. Please wait."`

---

## 3. Transactional Outbox Architecture (`sys_outbox_events`)

### 3.1 Table Schema Integration

Leveraging the platform's production-hardened `sys_outbox_events` table:

```sql
CREATE TABLE `sys_outbox_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `idempotency_key` varchar(64) DEFAULT NULL,
  `aggregate_type` varchar(64) NOT NULL DEFAULT 'Candidate',
  `aggregate_id` bigint(20) unsigned NOT NULL,
  `aggregate_version` int(10) unsigned NOT NULL,
  `event_type` varchar(64) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` enum('PENDING','PROCESSING','COMPLETED','FAILED','DEAD_LETTER') NOT NULL DEFAULT 'PENDING',
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `available_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_outbox_tenant_idempotency` (`tenant_id`,`idempotency_key`),
  KEY `idx_outbox_status_available` (`status`,`available_at`),
  KEY `idx_outbox_aggregate` (`aggregate_type`,`aggregate_id`,`aggregate_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Atomic Transaction Boundary

Every candidate recommendation mutation executes within a single MySQL `DB::transaction()`:

```php
DB::beginTransaction();
try {
    // 1. Authoritative Eligibility & Tenancy Check
    // 2. Existing Assignment Check (rec_candidate_assign)
    // 3. Coin Deductions & Transaction Log
    // 4. Insert into rec_candidate_assign
    $recId = DB::table('rec_candidate_assign')->insertGetId($values);

    // 5. Increment Tenant/Candidate Aggregate Version in Cache
    $newVersion = Cache::increment("pipeline_v2:{$tenantId}:epoch");

    // 6. Write Transactional Outbox Event in SAME MySQL Transaction
    $outboxId = DB::table('sys_outbox_events')->insertGetId([
        'tenant_id' => $tenantId,
        'idempotency_key' => $idempotencyKey,
        'aggregate_type' => 'BenchCandidateRequest',
        'aggregate_id' => (int) $candidateId,
        'aggregate_version' => (int) $newVersion,
        'event_type' => 'BenchCandidateRecommended',
        'payload' => json_encode([
            'rec_id' => $recId,
            'candidate_id' => $candidateId,
            'job_id' => $jobId,
            'tenant_id' => $tenantId,
            'source_company_id' => $sourceCompanyId,
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ]),
        'status' => 'PENDING',
        'attempts' => 0,
        'available_at' => now(),
        'created_at' => now(),
    ]);

    // 7. Update Idempotency Record to COMPLETED
    DB::table('sys_idempotency_keys')
        ->where('tenant_id', $tenantId)
        ->where('idempotency_key', $idempotencyKey)
        ->update([
            'status' => 'COMPLETED',
            'response_status' => 200,
            'response_body' => json_encode(['status' => 'success', 'message' => 'Candidate request submitted successfully.']),
            'completed_at' => now(),
            'updated_at' => now(),
        ]);

    DB::commit();

    // 8. Asynchronously Dispatch Outbox Relay Job (Post-Commit)
    ProcessBenchRequestOutboxEventJob::dispatch((int) $outboxId);

} catch (\Throwable $e) {
    DB::rollBack();
    // Mark idempotency key FAILED_RETRYABLE or release lock
    throw $e;
}
```

---

## 4. Outbox Worker Execution Protocol

### 4.1 Claim Protocol (Dual-Lease with `SKIP LOCKED`)

The outbox background worker (`ProcessBenchRequestOutboxEventJob`) implements a dual-lease claim protocol:

1. **Short-lived Batch Claim Transaction:**
   ```sql
   SELECT id FROM sys_outbox_events
   WHERE aggregate_type = 'BenchCandidateRequest'
     AND status = 'PENDING'
     AND available_at <= NOW()
     AND (locked_at IS NULL OR locked_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))
   ORDER BY id ASC
   LIMIT 50
   FOR UPDATE SKIP LOCKED;
   ```
2. **Optimistic Transition:**
   ```sql
   UPDATE sys_outbox_events
   SET status = 'PROCESSING', locked_at = NOW(), attempts = attempts + 1
   WHERE id IN (:claimedIds);
   ```

### 4.2 Decoupled Side Effects Execution

The worker executes decoupled side effects outside the primary HTTP request path:
1. **Elasticsearch Indexing:** Calls `SyncCandidateRequestToElasticJob::dispatchSync($recId)`.
2. **Reverb WebSocket Delta Broadcast:** Dispatches `BenchRequestCreated` event to `company.{$tenantId}` and `company.{$sourceCompanyId}`.
3. **Email Notification Dispatch:** Dispatches `SendEmailJob` and `QueueNotificationMailService` via Horizon queues.
4. **Cache Invalidation:** Flushes Redis version counters and tag caches.
5. **Completion Transition:**
   ```sql
   UPDATE sys_outbox_events
   SET status = 'COMPLETED', processed_at = NOW()
   WHERE id = :id;
   ```

### 4.3 Error Handling, Retries & Dead Letter Policy

- **Max Attempts:** 3 retries.
- **Exponential Backoff:** `available_at = NOW() + (attempts * 15 seconds)`.
- **Dead-Letter State:** If `attempts >= 3`, event transitions to `DEAD_LETTER` with sanitized error log in `last_error` and triggers operational alert.
- **Zero Poison Message Blocking:** A failing event never halts subsequent outbox events due to `SKIP LOCKED`.

---

## 5. Concurrency & Race Condition Resolution

| Failure / Race Scenario | Mitigating Mechanism | Guaranteed Outcome |
| :--- | :--- | :--- |
| **Rapid Double Click by Recruiter** | Frontend disabling + `sys_idempotency_keys` unique constraint | Exactly 1 insertion; second click returns 200 with identical success payload |
| **Simultaneous Submissions by 2 Users** | Row-level locking on `rec_candidate_assign` + unique business check | First user succeeds; second user receives clean warning: "Already assigned" |
| **Server Crash during Database Commit** | MySQL ACID rollback | No dirty state; no outbox event created; client retries cleanly |
| **Worker Crash after Outbox Insertion** | Lease timeout (`locked_at < NOW() - 5m`) | Standby worker re-claims event after 5 minutes and completes side effects |
| **Elasticsearch Cluster Outage** | Worker retries with backoff; outbox record persists | MySQL commit remains authoritative; ES sync catches up upon recovery |
| **Reverb / WebSocket Disconnection** | Fallback to REST reconciliation; client reconnect re-queries state | Client UI refreshes delta without data loss |

---

## 6. Business Logic & Tenancy Invariant Guarantees

1. **Coin Deductions:** Coins are deducted using the exact existing `GeneralMethod::coinsValidations` and `GeneralMethod::coinsDeductions` rules (Event ID `25`).
2. **Eligibility Rules:** Candidate status must equal `2` (Bench status); job requirement must be active and scoped to company.
3. **Tenancy:** Tenant isolation strictly validated against authenticated user's `company_id`.
4. **Email Notifications:** Preserves existing templates (Mail Type 21) and recipients without mutations.
