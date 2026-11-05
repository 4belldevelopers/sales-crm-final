# Super Admin Meeting Tracker & Schedule Hub — Forensic Architecture Audit, Outbox Idempotency & 10K Scalability Plan

> **Document Identifier:** `docs/superadmin/SUPER_ADMIN_MEETING_SCHEDULE_BACKEND_ARCHITECTURE_AND_OPTIMIZATION_PLAN.md`  
> **Platform:** JERO B2B Talent Marketplace & GOD-Level ATS (`testing.jero.works`)  
> **Target Route:** `http://localhost/git/JWMaincode28June2026/super-admin/meetingstoreScheduleList` (`superadmin.company.meeting-schedule-list`)  
> **Secondary Routes:**  
> - Meeting Comment Feed: `superadmin.company.meeting-comment` (`GET /meeting-comment/{id}`)  
> - Meeting Comment Mutation: `superadmin.company.meeting-comment-new` (`POST /meeting-comment-new/{id?}`)  
> - Meeting Reschedule Mutation: `superadmin.company.store-schedule` (`POST /meetingstoreSchedule`)  
> - Meeting Delete Mutation: `superadmin.company.meeting-schedule-delete` (`POST /meeting-schedule-delete/{id}`)  
> **Controllers & Services:**  
> - Controller: `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`  
> - Mutation Service: `app/Services/SuperAdmin/SuperAdminMeetingMutationService.php`  
> - Unified Cache Manager: `app/Services/SuperAdminMeetingScheduleCacheManager.php`  
> - Distributed Circuit Breaker: `app/Services/ElasticsearchCircuitBreaker.php`  
> - Transactional Outbox Worker: `app/Jobs/ProcessMeetingOutboxEventJob.php`  
> - Broadcast Event: `app/Events/SuperAdminMeetingUpdatedEvent.php` (Private `private-superadmin.meetings` with monotonic stream sequence ordering)  
> **CLI Diagnostic Commands:**  
> - Health & Telemetry: `php artisan superadmin:meetings:health {--json}`  
> - Drift Detection & Reconciler: `php artisan superadmin:meetings:drift {--dry-run} {--heal} {--limit=100}`  
> - DLQ Inspector & Replay: `php artisan superadmin:meetings:dlq {--retry} {--purge} {--id=}`  
> - Outbox & Lease Pruning: `php artisan superadmin:meetings:outbox-prune {--days=14} {--dry-run}`  
> **Database Schema Truth:** `meeting_schedules`, `meeting_comments`, `interview_mode`, `interview_mode_lavel`, `companies`, `users`, `sys_idempotency_keys`, `sys_outbox_events`, `sys_outbox_deliveries` (`database/schema/mysql-schema.sql`)  
> **Design Mandate:** Strict Zero UI Modification Policy — UI layout, typography, colors, toolbars, tables, drawers, modals, buttons, icons, and styling are 100% frozen, visually and functionally identical.  
> **Audit Status:** Stage 2 Independent Forensic Audit Completed — Gaps Documented with Executable Source Evidence. Remediation Plan Defined.  

---

## 🏛️ Executive Summary & Master Architecture

The **Super Admin Meeting Tracker & Schedule Hub** (`/super-admin/meetingstoreScheduleList`) provides high-level governance and operational oversight across all client-agency-candidate interview meetings scheduled across the platform ecosystem. It tracks sessions conducted across diverse communication channels: **Google Meet**, **Zoom**, **Microsoft Teams**, **Telephonic Interviews**, and **On-Premise (Face-to-Face)** meetings.

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────+
|               JERO SUPER ADMIN MEETINGS HIGH-CONCURRENCY CQRS ARCHITECTURE                                |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────+
|                                                                                                           |
|   [ BROWSER RUNTIME: Client-First In-RAM State & IndexedDB Engine (jero_superadmin_meetings_db v2) ]      |
|   • Instant Cold Hydration from IndexedDB 'meetings_cache' Object Store                                   |
|   • Multi-Tenant User Isolation: verifies 'rec.user_id === CURRENT_USER_ID' to prevent cross-account leak |
|   • Cross-Tab Cache Synchronization via BroadcastChannel ('jero_superadmin_meetings_sync')                |
|   • RFC-4122 UUID v4 Idempotency Key Injection on Mutations ('X-Idempotency-Key')                         |
|   • In-Flight Mutation Lock Guard (_isSubmittingMeetingComment blocks duplicate clicks in user RAM)        |
|   • Real-Time Authorized WebSockets on Private Channel ('private-superadmin.meetings')                    |
|   • Monotonic Sequence Ordering Check & Sequence Gap Detection for Authoritative Re-sync                  |
|                                           │                                                               |
|                                           ▼                                                               |
|   [ CQRS READ PATH: 100% Isolated Reads (0% MySQL Database Read Strain on Warm Hits) ]                    |
|   • Unified Cache Manager: SuperAdminMeetingScheduleCacheManager (sa:meetings:v{version}:*)               |
|   • Distributed Stampede Protection: Single-Flight Locking (lock:key) with double-checked caching          |
|   • Read-After-Write Consistency Guarantee: Authoritative mutation delta overlay (sa:meetings:delta:)     |
|   • Distributed Circuit Breaker: ElasticsearchCircuitBreaker (Closed -> Open -> Half-Open)               |
|   • Degraded Fallback: Serves snapshot cache (sa:meetings:snapshot:latest) if circuit trips               |
|   • Redis Outage Resilient: Transparent try/catch MySQL execution if Redis connection fails               |
|   • Zero avoidable MySQL reads on warm cache-hit listing paths, comment feeds, and counts                 |
|                                           │                                                               |
|                                           ▼                                                               |
|   [ MUTATION PATH: Outbox Architecture & Distributed Idempotency (SuperAdminMeetingMutationService) ]     |
|   • Two-Tier Idempotency across ALL mutation endpoints (addComment, storeSchedule, deleteMeeting)         |
|   • Atomic Lease Claim via MySQL 'sys_idempotency_keys' with bounded spin-wait resolution (up to 1.5s)   |
|   • Rejection of key reuse with mismatched payloads (HTTP 422 Unprocessable Entity)                       |
|   • Replay of completed responses with is_replayed: true                                                  |
|   • MySQL Row-Locked Atomic Transaction Boundary (lockForUpdate() on meeting_schedules)                  |
|   • Transactional Outbox Event Persistence ('sys_outbox_events') with PENDING state                      |
|   • Dedicated Outbox Delivery Ledgers ('sys_outbox_deliveries') preventing destination lost updates       |
|   • Authoritative Mutation Delta Publication: SuperAdminMeetingScheduleCacheManager::setMutationDelta()  |
|   • Atomic Generation Cache Invalidation: SuperAdminMeetingScheduleCacheManager::invalidateAll()         |
|   • Persistent Worker: ProcessMeetingOutboxEventJob with Dead-Letter Queue (DLQ) tracking                |
|   • Real-Time Reverb WebSocket Broadcasting: SuperAdminMeetingUpdatedEvent with monotonic stream sequence |
|                                                                                                           |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 1. Forensic Architecture Assessment & Critical Findings (P0 / P1 / P2)

The second, independent forensic audit examined runtime source code, database execution plans, Redis failure behaviors, worker lifecycles, and client scripts. The audit identified the following categorized findings:

### Summary Matrix:
| Finding ID | Severity | File & Method | Core Vulnerability | Production Consequence | Status |
| :--- | :---: | :--- | :--- | :--- | :---: |
| **P0.1** | **CRITICAL** | `SuperAdminMeetingMutationService.php`<br>`storeSchedule()` (L330–354)<br>`deleteMeeting()` (L490–505) | **Distributed Idempotency Bypass:** When a concurrent duplicate mutation arrives in `PROCESSING` state (`$claimed === 0`), both methods lack the spin-wait loop and 409 rejection. Execution falls through to Step 2, creating duplicate meetings or running duplicate deletes. | Duplicate records created in `meeting_schedules` under high concurrency; delete race conditions. | Identified & Documented |
| **P0.2** | **CRITICAL** | `SuperAdminMeetingScheduleCacheManager.php`<br>Line 315<br>`SuperAdminMeetingUpdatedEvent.php`<br>Line 45 | **Redis Outage Unhandled 500 Crash:** `Cache::increment(self::TELEMETRY_MISSES_KEY)` and `Cache::increment('stream:sa:meetings:seq')` are invoked without `try/catch`. If Redis drops connection, requests crash with HTTP 500 instead of executing MySQL fallback. | Complete page failure on Redis restart, network partition, or connection pool exhaustion. | Identified & Documented |
| **P0.3** | **CRITICAL** | Database Schema (`database/schema/mysql-schema.sql`)<br>`meeting_comments`<br>`meeting_schedules` | **Missing Secondary Indexes & Full Table Scans:** `meeting_comments` has NO index on `meeting_schedule_id` (PRIMARY key on `id` only). EXPLAIN confirms full index scan (`type: index`) on every comment lookup and count query during transaction row-lock hold. | Severe database lock contention, slow queries (>2,000ms), and worker thread starvation under 10K concurrency. | Identified & Documented |
| **P1.1** | **HIGH** | `SuperAdminMeetingScheduleCacheManager.php`<br>Lines 316–324 | **Stale Generation Follower Race:** `$key` with `v1` is generated before entering `rememberWithStampedeProtection`. If version bumps to `v2` during follower spin-wait, the fallback computation writes fresh data under the stale `v1` key, leaving `v2` unpopulated. | Cache fragmentation and cache-miss storms on the new generation. | Identified & Documented |
| **P1.2** | **HIGH** | `ProcessMeetingOutboxEventJob.php`<br>Line 227 | **DLQ State Machine Typo:** `$finalStatus = ($attempts >= $this->tries) ? 'FAILED' : 'FAILED';`. Even when max attempts (5) are reached, parent event is set to `FAILED` instead of `DEAD_LETTER`. | Poisoned outbox events are ignored by worker (`attempts < 5`) but never surfaced to DLQ commands for triage. | Identified & Documented |
| **P1.3** | **HIGH** | `meeting-list.blade.php`<br>Lines 335–349 | **Unused Client Cache & Missing Account-Switch Purge:** `cacheMeetingData` is defined but never invoked on data receipt. No account switch check cleans up old user data when a new admin logs in on the same browser. | Potential cross-account data lingering in browser RAM/IndexedDB; zero client cold hydration advantage. | Identified & Documented |
| **P2.1** | **MEDIUM** | `meeting-list.blade.php`<br>Lines 418–440 | **Missing WebSocket Sequence Gap Detection & Reconnect Sync:** Client drops stale events (`<= lastSeq`), but if `stream_sequence > lastSeq + 1` (dropped packets or sleep/wake reconnect), no catch-up sync is triggered. | Stale modal state if network blips during a comment update. | Identified & Documented |
| **P2.2** | **MEDIUM** | `SuperAdminCompanyController.php`<br>Lines 4195–4198 | **Cache Key Omits Query String Filter Permutations:** Only `page` and `per_page` are hashed into `listKey`. Any URL filter parameters (e.g. `mode`, `company_id`, `search`) are ignored in cache key hashing. | All users share the unfiltered cache even when passing specific query parameters. | Identified & Documented |
| **P2.3** | **MEDIUM** | `SuperAdminMeetingMutationService.php`<br>Outbox & HTTP Headers | **Missing Distributed Tracing (Correlation ID):** Mutations and outbox deliveries lack a consistent `X-Correlation-ID` header linking the web request, MySQL outbox event, and WebSocket broadcast. | Inability to trace an end-to-end event across distributed logs under high concurrency. | Identified & Documented |

---

## 2. Detailed Forensic Analysis & Source Code Evidence

### 2.1 P0.1: Distributed Idempotency Bypass in `storeSchedule` and `deleteMeeting`
- **Location:** `app/Services/SuperAdmin/SuperAdminMeetingMutationService.php` lines 330–354 and lines 490–505.
- **Source Code Evidence:**
  In `addComment()` (lines 126–155), concurrent requests execute a bounded spin-wait (up to 1.5s in 50ms intervals) polling `sys_idempotency_keys` for `COMPLETED` status, and return HTTP 409 Conflict if still processing.
  However, in `storeSchedule()`:
  ```php
  // Lines 330-354 in storeSchedule():
  if ($claimed === 0) {
      $existing = DB::table('sys_idempotency_keys')
          ->where('tenant_id', $tenantId)
          ->where('idempotency_key', $idempotencyKey)
          ->first();

      if ($existing && $existing->request_hash !== $canonicalHash) {
          return [ /* 422 mismatch */ ];
      }

      if ($existing && $existing->status === 'COMPLETED' && $existing->response_body) {
          $payload = json_decode((string) $existing->response_body, true);
          if (is_array($payload)) {
              $payload['is_replayed'] = true;
              return $payload;
          }
      }
  }

  // Line 358: FALLS STRAIGHT THROUGH TO WRITE!
  DB::beginTransaction();
  $meetingId = DB::table('meeting_schedules')->insertGetId([...]);
  ```
  If Request A is currently processing (`status = 'PROCESSING'`), and Request B arrives with the identical idempotency key:
  1. Request B checks `claimed === 0` (true).
  2. Request B checks `status === 'COMPLETED'` (false).
  3. Request B proceeds directly to line 358 and inserts a **duplicate meeting schedule record** into `meeting_schedules`!
  4. The same vulnerability exists in `deleteMeeting()` (lines 490–505).
- **Remediation:** Align `storeSchedule()` and `deleteMeeting()` with the identical two-tier lease claim, expired lease recovery, and bounded spin-wait / 409 conflict guard used in `addComment()`.

### 2.2 P0.2: Redis Outage / Connection Exhaustion 500 Crash
- **Location:** `app/Services/SuperAdminMeetingScheduleCacheManager.php` line 315 and `app/Events/SuperAdminMeetingUpdatedEvent.php` line 45.
- **Source Code Evidence:**
  ```php
  // SuperAdminMeetingScheduleCacheManager.php Line 315:
  Cache::increment(self::TELEMETRY_MISSES_KEY); // <--- UNPROTECTED CALL
  $fbStartVersion = self::getVersion();
  $fallbackComputed = $computation();
  ```
  If Redis is down or times out:
  - Fast-path `Cache::get()` catches the exception.
  - `Cache::lock()` catches the exception and sets `$isLeader = false`.
  - Follower spin-wait exits.
  - Step 4 is reached: line 315 calls `Cache::increment(...)` without `try/catch`. This throws `Predis\ConnectionException` or `RedisException`, terminating the HTTP request with a 500 Internal Server Error instead of executing the `$computation()` closure and serving fresh data from MySQL!
  - Similarly, in `SuperAdminMeetingUpdatedEvent.php`:
    ```php
    // Line 45:
    $this->streamSequence = $streamSequence ?? (int) Cache::increment('stream:sa:meetings:seq');
    ```
    Throws an unhandled exception if Redis is unreachable during event instantiation.
- **Remediation:** Wrap all telemetry incrementers and sequence counters in defensive `try/catch` blocks. In `rememberWithStampedeProtection()`, provide a fail-safe fallback that executes `$computation()` immediately if Redis is unreachable, logging a degraded warning with zero user-facing error.

### 2.3 P0.3: Missing MySQL Secondary Indexes & Full Table Scans
- **Location:** Live MySQL Database (`newdata`) and `database/schema/mysql-schema.sql`.
- **Database Schema Evidence:**
  Running `SHOW INDEX FROM meeting_comments` and `SHOW INDEX FROM meeting_schedules` reveals:
  ```
  === Indexes for meeting_schedules ===
  Key: PRIMARY | Col: id | Seq: 1 | Unique: YES

  === Indexes for meeting_comments ===
  Key: PRIMARY | Col: id | Seq: 1 | Unique: YES
  ```
  Running `EXPLAIN` on the comment loading query (`SuperAdminCompanyController.php:4252`):
  ```sql
  EXPLAIN SELECT meeting_comments.*, users.name, users.image
  FROM meeting_comments
  JOIN users ON users.id = meeting_comments.comment_by
  WHERE meeting_comments.meeting_schedule_id = 1
  ORDER BY meeting_comments.id DESC;
  ```
  Yields:
  ```json
  {"id":1,"select_type":"SIMPLE","table":"meeting_comments","type":"index","possible_keys":null,"key":"PRIMARY","key_len":"4","ref":null,"rows":"9","Extra":"Using where"}
  {"id":1,"select_type":"SIMPLE","table":"users","type":"eq_ref","possible_keys":"PRIMARY","key":"PRIMARY","key_len":"4","ref":"newdata.meeting_comments.comment_by","rows":"1","Extra":"Using where"}
  ```
  Notice: `type: index`, `possible_keys: null`. MySQL scans the entire primary key tree of `meeting_comments` to evaluate `WHERE meeting_schedule_id = 1`.
  Furthermore, during `addComment()` (`SuperAdminMeetingMutationService.php:183`), inside the atomic transaction holding `lockForUpdate` on `meeting_schedules`:
  ```php
  $totalComments = DB::table('meeting_comments')->where('meeting_schedule_id', $meetingId)->count();
  ```
  This query performs a full table scan while holding an exclusive row lock on `meeting_schedules`, creating severe database lock contention under 10K concurrent users!
- **Remediation:** Add compound index `INDEX idx_comments_schedule_id (meeting_schedule_id, id)` on `meeting_comments`, and `INDEX idx_schedule_date (schedule_date, id)` on `meeting_schedules`.

### 2.4 P1.1: Stale Generation Key Race in Follower Fallback
- **Location:** `app/Services/SuperAdminMeetingScheduleCacheManager.php` lines 316–324.
- **Race Condition Analysis:**
  1. Request 1 and Request 2 arrive for page 1 at `version = 1`. Both construct `$key = "sa:meetings:v1:list:hash"`.
  2. Request 1 acquires single-flight lock and begins computing.
  3. Request 2 enters follower spin-wait.
  4. While Request 2 is waiting, an admin posts a comment via another thread, triggering `invalidateAll()`, which increments global version from 1 to 2.
  5. Request 1 finishes computation. Generation guard notices `1 !== 2` and discards the write.
  6. Request 2 times out after 500ms and executes Step 4 fallback:
     ```php
     $fbStartVersion = self::getVersion(); // returns 2
     $fallbackComputed = $computation();
     if ($fbStartVersion === self::getVersion()) { // 2 === 2 (true)
         Cache::put($key, $fallbackComputed, $ttlSeconds); // Writes to "sa:meetings:v1:..."!
     }
     ```
  7. Request 2 writes the fresh computation into the OLD `v1` key!
  8. Future requests query with `v2` and miss the cache, creating unnecessary database load.
- **Remediation:** Pass a key builder callback `Closure(int $version): string` or validate the key version against current generation before writing.

### 2.5 P1.2: Typo in DLQ Marking for Parent Outbox Event
- **Location:** `app/Jobs/ProcessMeetingOutboxEventJob.php` line 227.
- **Source Code Evidence:**
  ```php
  // ProcessMeetingOutboxEventJob.php line 227:
  $attempts = (int) ($event->attempts ?? 1);
  $finalStatus = ($attempts >= $this->tries) ? 'FAILED' : 'FAILED'; // <--- TYPO
  ```
  When an event exceeds max retry attempts (5), it remains set to `'FAILED'` rather than `'DEAD_LETTER'`.
  In `ProcessMeetingOutboxEventJob.php:67`, the batch query explicitly filters:
  `->where('attempts', '<', $this->tries)`
  Once attempts reach 5, the event is permanently ignored by the worker, but since its status is `FAILED` instead of `DEAD_LETTER`, it never surfaces in DLQ monitoring queries (`SuperAdminMeetingsDlqCommand` checks `status = 'DEAD_LETTER'`).
- **Remediation:** Correct line 227 to `$finalStatus = ($attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';`. In `SuperAdminMeetingsDlqCommand`, reset both the delivery ledger and parent outbox event status when `--retry` is executed.

---

## 3. Genuine 10,000 Concurrent User Load-Testing Strategy

To provide incontrovertible, empirical proof of 10,000 concurrent user scalability:

### 3.1 Load-Testing Protocol & Traffic Distribution
```
+───────────────────────────────────────────────────────────────────────────────────+
|               10K CONCURRENCY DISTRIBUTED LOAD-TEST ARCHITECTURE                  |
+───────────────────────────────────────────────────────────────────────────────────+
|                                                                                   |
|   [ 10,000 Virtual Users (VUs) Distributed Across 4 Load Generator Workers ]      |
|                                                                                   |
|   Traffic Mix:                                                                    |
|   • 75% Warm Cached Listing: GET /super-admin/meetingstoreScheduleList            |
|   • 15% Comment Modal Inspection: GET /meeting-comment/{id}                       |
|   • 5% Meeting Comment Creation: POST /meeting-comment-new                        |
|   • 3% Meeting Rescheduling: POST /meetingstoreSchedule                           |
|   • 2% Cold Cache Filter Exploration: Pagination, Per-Page, and Mode Filters      |
|                                                                                   |
|   Cadence:                                                                        |
|   • Truncated Gaussian Think Time: Mean 5.0s, StdDev 1.5s, Min 1.0s, Max 15.0s    |
|   • Peak Sustained Request Rate: 2,000 HTTP RPS                                   |
|                                                                                   |
+───────────────────────────────────────────────────────────────────────────────────+
```

### 3.2 Concurrency Stages & Pass/Fail Criteria
| Concurrency Tier | Target RPS | SLA p50 Latency | SLA p95 Latency | SLA p99 Latency | Max Error Rate | MySQL Read Queries (Warm) |
| :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **1,000 VUs** | 200 RPS | < 5 ms | < 25 ms | < 50 ms | 0.00% | 0 domain reads |
| **3,000 VUs** | 600 RPS | < 10 ms | < 35 ms | < 75 ms | 0.00% | 0 domain reads |
| **5,000 VUs** | 1,000 RPS | < 15 ms | < 50 ms | < 100 ms | < 0.01% | 0 domain reads |
| **10,000 VUs** | 2,000 RPS | < 20 ms | < 60 ms | < 120 ms | < 0.05% | 0 domain reads |

### 3.3 Fault Injection & Resilience Testing Scenarios
1. **Redis Network Partition & Reconnection:**
   - Inject 15-second Redis socket timeout.
   - Assert zero HTTP 500 errors; requests gracefully execute MySQL fallback.
   - Assert automatic recovery to Redis L2 cache once partition resolves.
2. **Transactional Outbox Worker Crash & Recovery:**
   - Simulate worker process termination (`SIGKILL`) while processing an outbox event.
   - Verify that stale lease reclamation (`processing_until < now()`) claims the event on the next worker cycle.
   - Verify zero duplicate WebSocket notifications delivered via client ring buffer deduplication.
3. **WebSocket Connection & Reconnection Storm:**
   - Force 5,000 concurrent client WebSocket disconnections and simultaneous reconnects.
   - Verify Reverb / Pusher connection pool handles the reconnect burst without memory exhaustion.
   - Verify client sequence gap detection triggers state reconciliation for any dropped updates.
4. **4-Hour Soak Test:**
   - Sustained 3,000 VUs over 4 continuous hours.
   - Measure PHP worker memory growth (verifying absence of memory leaks).
   - Verify MySQL active connections remain bounded within pool limits (`max_connections = 150`).
   - Verify Redis memory footprint remains steady with automatic TTL pruning.

---

## 4. Remediation Plan & Execution Sequence

Upon approval, the remediation will be executed according to the following strict sequence:

1. **Step 1 (P0.1 Remediation):**
   - Refactor `storeSchedule()` and `deleteMeeting()` in `SuperAdminMeetingMutationService.php` with two-tier idempotency lease claim, expired lease recovery, and bounded spin-wait (up to 1.5s in 50ms intervals) returning HTTP 409 Conflict if processing.
2. **Step 2 (P0.2 Remediation):**
   - Wrap `Cache::increment` in `SuperAdminMeetingScheduleCacheManager.php` and `SuperAdminMeetingUpdatedEvent.php` in defensive `try/catch` blocks.
   - Implement fail-safe direct MySQL execution in `rememberWithStampedeProtection()` when Redis is unreachable.
3. **Step 3 (P0.3 Remediation):**
   - Apply MySQL indexes on `meeting_comments(meeting_schedule_id, id)` and `meeting_schedules(schedule_date, id)`.
   - Verify with `EXPLAIN` that all comment and schedule queries use index ref lookups with 0 table scans.
4. **Step 4 (P1.1 Remediation):**
   - Enhance `rememberWithStampedeProtection()` with generation drift detection in follower fallback, writing strictly to the current version key.
5. **Step 5 (P1.2 Remediation):**
   - Fix DLQ typo in `ProcessMeetingOutboxEventJob.php:227` (`DEAD_LETTER` on max attempts).
   - Update `SuperAdminMeetingsDlqCommand` `--retry` to reset parent outbox event status and attempts.
6. **Step 6 (P1.3, P2.1, P2.2 Remediation — Strict Zero UI Change):**
   - Preserve 100% of Blade HTML, tables, columns, styles, and classes in `meeting-list.blade.php`.
   - In `@push('footer-script')`, add account-switch purge for IndexedDB, and WebSocket sequence gap detection with Echo reconnect listener.
   - In `SuperAdminCompanyController.php`, include all URL query parameters in cache key generation.
7. **Step 7 (Verification & Testing):**
   - Run full automated test suite `tests/Feature/SuperAdminMeetingsLoadTest.php`.
   - Run distributed load test verifying latency, zero MySQL reads on warm hits, and idempotency guarantees.
   - Run Pint for code formatting compliance.

---

## 5. Production Readiness Sign-Off Criteria

| Dimension | Mandatory Pass Requirement | Verification Method |
| :--- | :--- | :--- |
| **Strict UI Preservation** | 0 HTML/CSS/Blade layout changes. Existing UI visually and functionally 100% identical. | Git diff against HEAD on `meeting-list.blade.php` (lines 1–273 untouched). |
| **0% MySQL Warm Reads** | 0 domain queries executed on cached listing, comment, and count requests. | Automated test assertion with `DB::getQueryLog()`. |
| **Database Execution Plans** | 0 full table scans on `meeting_comments` and `meeting_schedules`. | MySQL `EXPLAIN` query output showing `type: ref` using secondary index. |
| **Two-Tier Idempotency** | 100% rejection of duplicate in-flight requests (409) or canonical replay (`is_replayed: true`). | Automated concurrent mutation test suite. |
| **Redis Outage Resiliency** | Zero HTTP 500 errors when Redis is disconnected; transparent MySQL execution. | Simulated Redis connection exception test. |
| **Outbox & DLQ Lifecycle** | Poisoned events properly marked `DEAD_LETTER` and retried via CLI. | `php artisan superadmin:meetings:dlq` CLI execution. |
| **Code Documentation** | Complete class and method docblocks explaining intent, tenancy, and architecture. | PSR-12 and Pint clean code check. |
