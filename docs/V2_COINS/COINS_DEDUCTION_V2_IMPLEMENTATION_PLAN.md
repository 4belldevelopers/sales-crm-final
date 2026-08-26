# COINS DEDUCTION V2 SCALABLE ARCHITECTURE IMPLEMENTATION PLAN

> **Document Type:** Step-by-Step Technical Implementation Roadmap & Architecture Blueprint  
> **Target Application:** JERO B2B Multi-Tenant ATS & Talent Marketplace (`testing.jero.works`)  
> **Target Capacity:** 10,000+ Concurrent Recruiters (2,500+ RPS)  
> **Status:** PROPOSAL / PLAN ONLY — NO CODE MODIFICATIONS PERFORMED  
> **Date:** August 26, 2026  

---

## 1. V2 Architecture Goals

1. **100% Financial & Transactional Integrity:** Eliminate race conditions, double deductions, lost debits, and negative balances.
2. **Atomic Balance Updates:** Database row-locking (`FOR UPDATE`) or atomic SQL expressions (`UPDATE company_coins SET coins = coins - ? WHERE company_id = ? AND coins >= ?`) within explicit `DB::transaction()`.
3. **Idempotent Operations:** Guarantee idempotency across all coin debits via unique idempotency keys stored in Redis.
4. **Zero N+1 Queries:** Refactor batch daily background deductions into bulk multi-row operations.
5. **Decoupled Asynchronous Side-Effects:** Offload Elasticsearch document status updates and Reverb broadcasts to queued post-commit listeners.
6. **Sub-10ms API Latency:** High-speed coin validations using Redis atomic caches.

---

## 2. Phase-Wise Implementation Plan

---

### PHASE 0 — Baseline & Instrumentation

* **Objective:** Establish real-time APM tracing, query logging, and performance benchmarks for coin operations before structural refactoring.
* **Current Problem:** No isolated metrics exist measuring coin transaction duration, deadlock rates, or query counts per tenant under load.
* **Proposed Solution:** Introduce OpenTelemetry / Laravel Telescope telemetry tags and create benchmark suite scripts (`tests/Performance/CoinsLoadBenchmarkTest.php`).
* **Files/Modules Affected:** `app/Providers/AppServiceProvider.php`, `config/logging.php`, `tests/Performance/CoinsLoadBenchmarkTest.php` [NEW]
* **DB Impact:** None (Read-only query profiling).
* **Redis Impact:** Minimal (Telemetry metric aggregation).
* **Elasticsearch Impact:** None.
* **Queue Impact:** None.
* **Performance Impact:** < 1% overhead during benchmarking.
* **Concurrency Impact:** None.
* **Business Risk:** Zero risk (Non-intrusive logging).
* **Testing Requirements:** Run baseline load test on staging environment to establish RPS and P95 latency.
* **Rollback Strategy:** Disable telemetry logging flag in configuration.
* **Dependencies:** None.

---

### PHASE 1 — Critical Coin Transaction Safety

* **Objective:** Secure core coin deduction and credit paths against race conditions, lost updates, and missing audit records.
* **Current Problem:** `GeneralMethod::coinsDeductions()` performs a non-atomic `SELECT` followed by `UPDATE` without DB transactions or row locks. Concurrent requests cause double deductions or negative balances.
* **Proposed Solution:** 
  1. Wrap all balance validations, debits, and log inserts inside `DB::transaction()`.
  2. Use pessimistic row locking `DB::table('company_coins')->where(...)->lockForUpdate()->first()` or atomic update statements:
     ```php
     $affected = DB::table('company_coins')
         ->where('company_id', $companyId)
         ->where('event_type', $eventType)
         ->where('coins', '>=', $deductionAmount)
         ->decrement('coins', $deductionAmount);
     ```
  3. Throw `InsufficientCoinsException` when `$affected === 0`.
* **Files/Modules Affected:**
  * [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php) (`coinsDeductions()`, `coinsValidations()`)
  * `app/Services/CoinService.php` [NEW] (Extract god-method out of `GeneralMethod.php` into clean domain service)
* **DB Impact:** Guarantees strict transactional isolation (`REPEATABLE READ` / `READ COMMITTED` with `FOR UPDATE`).
* **Redis Impact:** None.
* **Elasticsearch Impact:** None.
* **Queue Impact:** None.
* **Performance Impact:** Microsecond row-lock wait time under extreme concurrency, eliminating financial data corruption.
* **Concurrency Impact:** Completely prevents concurrent double-spending per tenant.
* **Business Risk:** Medium (Core financial logic modification; requires comprehensive unit test coverage).
* **Testing Requirements:** Concurrent multi-threaded Pest/PHPUnit tests simulating simultaneous API requests for the same tenant.
* **Rollback Strategy:** Feature flag `config('features.v2_coins_atomic_lock')` fallback to legacy helper method.
* **Dependencies:** Phase 0.

---

### PHASE 2 — Query & N+1 Optimization

* **Objective:** Eliminate N+1 query loops in daily background coin deduction jobs and DataTables log views.
* **Current Problem:** `ProcessOperationalCoinDeductionsJob` executes $3N+2$ database queries per tenant company in a loop over active users and active jobs.
* **Proposed Solution:** 
  1. Bulk fetch user and job dates per company.
  2. Compute total daily deduction sums in PHP memory.
  3. Execute single bulk `UPDATE` for `company_coins` balance and bulk `INSERT` for `coin_transection_logs`.
  4. Perform bulk updates for user/job active dates:
     ```php
     DB::table('users')->whereIn('id', $activeUserIds)->update(['working_status' => 1, 'auto_date_team' => $today]);
     ```
* **Files/Modules Affected:**
  * [ProcessOperationalCoinDeductionsJob.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php)
  * `ManageSubscriptionController.php`
* **DB Impact:** Reduces query volume from ~300,000 queries down to ~20 queries per scheduled run across 1,000 tenants.
* **Redis Impact:** None.
* **Elasticsearch Impact:** None.
* **Queue Impact:** Background job execution time drops from 4 minutes down to under 5 seconds.
* **Performance Impact:** > 95% reduction in database IOPS during background processing runs.
* **Concurrency Impact:** Eliminates long database table lock holding periods.
* **Business Risk:** Low.
* **Testing Requirements:** Unit test comparing result of bulk deduction vs legacy loop deduction for identical datasets.
* **Rollback Strategy:** Revert job file to legacy single-user loop implementation.
* **Dependencies:** Phase 1.

---

### PHASE 3 — Database Index Optimization

* **Objective:** Create required foreign key and query filter indexes on coin ledger tables.
* **Current Problem:** `coin_transection_logs` lacks secondary indexes, causing full table scans during historical log lookup and reporting.
* **Proposed Solution:** Define composite database indexes in a new migration:
  ```sql
  ALTER TABLE `coin_transection_logs` 
    ADD INDEX `idx_company_created` (`company_id`, `created_at`),
    ADD INDEX `idx_event_type` (`event_type`),
    ADD INDEX `idx_coin_order` (`coin_order_id`);
    
  ALTER TABLE `coin_order_events`
    ADD INDEX `idx_order_event_type` (`company_id`, `coin_order_id`, `coin_event_id`);
  ```
* **Files/Modules Affected:**
  * `database/migrations/2026_08_27_000001_add_indexes_to_coin_tables.php` [NEW]
* **DB Impact:** Fast $O(\log N)$ index range scans for all coin log reports and deduction event lookups.
* **Redis Impact:** None.
* **Elasticsearch Impact:** None.
* **Queue Impact:** None.
* **Performance Impact:** DataTables log queries speed up from > 1.2s to < 8ms.
* **Concurrency Impact:** Reduces DB read lock durations.
* **Business Risk:** Low (Schema addition only).
* **Testing Requirements:** `EXPLAIN` query plan analysis verifying index usage (`USING INDEX`).
* **Rollback Strategy:** Run `down()` migration to drop created indexes.
* **Dependencies:** Phase 2.

---

### PHASE 4 — Redis Caching & Distributed Locking

* **Objective:** Implement sub-millisecond coin balance caching and Redis distributed locking for API endpoints.
* **Current Problem:** High API throughput continuously queries MySQL for tenant coin balances.
* **Proposed Solution:**
  1. Cache tenant balance in Redis: `Key: tenant:{company_id}:coins:{event_type}`, TTL: 300s.
  2. Implement Redis distributed lock around critical deduction endpoints:
     ```php
     Cache::lock("coin_lock:{$companyId}", 5)->block(3, function () use ($companyId, $amount) {
         // Perform DB atomic deduction & invalidate Redis cache
     });
     ```
  3. Invalidate/update Redis cache atomically upon every completed database write.
* **Files/Modules Affected:**
  * `app/Services/CoinService.php`
  * `config/cache.php`
* **DB Impact:** Offloads 80%+ of balance check read queries from MySQL to Redis memory.
* **Redis Impact:** Memory increase ~5MB per 10,000 active tenants; latency < 1ms.
* **Elasticsearch Impact:** None.
* **Queue Impact:** None.
* **Performance Impact:** API response latency decreases by 40-60%.
* **Concurrency Impact:** Redis locks prevent DB row contention across multiple application servers.
* **Business Risk:** Low.
* **Testing Requirements:** Redis connection failure fallback test (ensure automatic graceful fallback to MySQL DB if Redis is unreachable).
* **Rollback Strategy:** Disable Redis coin cache via config flag `COIN_CACHE_ENABLED=false`.
* **Dependencies:** Phase 1, Phase 3.

---

### PHASE 5 — Queue & Async Processing Scalability

* **Objective:** Parallelize daily tenant coin deductions across Horizon queue workers using tenant-chunked jobs.
* **Current Problem:** `ProcessOperationalCoinDeductionsJob` processes all system tenants sequentially in a single process.
* **Proposed Solution:**
  1. Refactor schedule to dispatch a parent dispatcher job `DispatchTenantCoinDeductionsJob`.
  2. Parent job chunks tenant IDs in batches of 50 and dispatches child jobs `ProcessCompanyCoinDeductionJob` to the `coins` queue:
     ```php
     Company::whereHas('coins')->chunk(50, function ($companies) {
         foreach ($companies as $company) {
             ProcessCompanyCoinDeductionJob::dispatch($company->id)->onQueue('coins');
         }
     });
     ```
* **Files/Modules Affected:**
  * `app/Jobs/DispatchTenantCoinDeductionsJob.php` [NEW]
  * `app/Jobs/ProcessCompanyCoinDeductionJob.php` [NEW]
  * `app/Console/Kernel.php`
* **DB Impact:** Distributed query load across multiple DB connections.
* **Redis Impact:** Queue payload storage in Redis.
* **Elasticsearch Impact:** None.
* **Queue Impact:** 10 Horizon workers process 1,000 companies concurrently in parallel.
* **Performance Impact:** Total job completion time drops from 5 minutes to under 15 seconds.
* **Concurrency Impact:** Isolated per-company processing ensures one failing company does not block others.
* **Business Risk:** Low.
* **Testing Requirements:** Queue failure, retry, and dead-letter queue (DLQ) processing tests.
* **Rollback Strategy:** Revert schedule in `Kernel.php` to original monolithic job.
* **Dependencies:** Phase 2, Phase 4.

---

### PHASE 6 — API & Frontend Idempotency Safeguards

* **Objective:** Enforce idempotency keys on API endpoints and add frontend debouncing to prevent double clicks.
* **Current Problem:** Rapid recruiter button clicks dispatch multiple HTTP requests, triggering duplicate coin deductions.
* **Proposed Solution:**
  1. Add `EnsureIdempotency` middleware to coin-deducting routes:
     ```php
     $idempotencyKey = $request->header('X-Idempotency-Key');
     if (Cache::has("idempotency:{$idempotencyKey}")) {
         return Cache::get("idempotency:{$idempotencyKey}");
     }
     ```
  2. JS Frontend: Add global Axios/AJAX interceptor and button disable handler on submit actions.
* **Files/Modules Affected:**
  * `app/Http/Middleware/EnsureIdempotency.php` [NEW]
  * `resources/assets/js/cv_tracker.js`
  * `resources/assets/js/bench_candidates.js`
  * `routes/web.php` & `routes/api.php`
* **DB Impact:** Prevents duplicate INSERT/UPDATE queries caused by client retries.
* **Redis Impact:** Temporary 24-hour key storage for API response caching.
* **Elasticsearch Impact:** None.
* **Queue Impact:** None.
* **Performance Impact:** Immediate HTTP 200 response returned from Redis for duplicate incoming requests without touch DB.
* **Concurrency Impact:** 100% protection against client-side request duplication.
* **Business Risk:** Low.
* **Testing Requirements:** Automated Pest test submitting 10 identical requests with the same `X-Idempotency-Key` header.
* **Rollback Strategy:** Disable middleware in route groups.
* **Dependencies:** Phase 4.

---

### PHASE 7 — Elasticsearch Async Event Decoupling

* **Objective:** Remove synchronous Elasticsearch calls from background coin jobs and controller actions.
* **Current Problem:** ES node updates execute synchronously during coin deductions, blocking worker threads if ES slows down.
* **Proposed Solution:**
  1. Fire domain event `TenantUserDeactivatedDueToCoins` when status changes.
  2. Attach asynchronous queued listener `SyncElasticUserStatusListener` running `afterCommit()`:
     ```php
     class SyncElasticUserStatusListener implements ShouldQueue {
         public $afterCommit = true;
         // Handle ES document update
     }
     ```
* **Files/Modules Affected:**
  * `app/Events/TenantUserDeactivatedDueToCoins.php` [NEW]
  * `app/Listeners/SyncElasticUserStatusListener.php` [NEW]
  * `app/Jobs/ProcessCompanyCoinDeductionJob.php`
* **DB Impact:** DB transaction commits without waiting for network IO to Elasticsearch.
* **Redis Impact:** Queue job buffering.
* **Elasticsearch Impact:** Smoother, batch-friendly indexing load without web-thread blocking.
* **Queue Impact:** Dedicated `elasticsearch` queue isolates ES tasks.
* **Performance Impact:** Eliminates network I/O block time during DB transactions.
* **Concurrency Impact:** Prevents database lock contention caused by waiting for ES network responses.
* **Business Risk:** Low.
* **Testing Requirements:** Test ES sync failure resilience (ensure queue retries work independently without rolling back DB state).
* **Rollback Strategy:** Synchronous fallback event listener configuration.
* **Dependencies:** Phase 5.

---

### PHASE 8 — Server & Infrastructure Scaling (10K Recruiter Target)

* **Objective:** Tune PHP-FPM / Octane, MySQL 8, Redis, and Linux kernel parameters for 10,000 concurrent recruiters.
* **Current Problem:** Default OS/MySQL connection limits will exhaust file descriptors and socket pools under 2,500 RPS.
* **Proposed Solution (Recommended Configuration Blueprint):**
  * **FrankenPHP / Octane:** Set max workers to 128; worker max requests = 10,000 (prevent memory leaks).
  * **MySQL 8 (`my.cnf`):**
    ```ini
    max_connections = 1000
    innodb_buffer_pool_size = 20G
    innodb_log_file_size = 2G
    innodb_flush_log_at_trx_commit = 2
    innodb_lock_wait_timeout = 5
    ```
  * **Linux OS Kernel (`/etc/sysctl.conf`):**
    ```ini
    net.core.somaxconn = 65535
    fs.file-max = 2097152
    ```
* **Files/Modules Affected:** Server environment configuration (`my.cnf`, `sysctl.conf`, `octane.php`).
* **DB Impact:** Handles 1,000 concurrent connections smoothly with 20GB buffer pool.
* **Redis Impact:** Enables Redis memory persistence and maxmemory policy (`allkeys-lru`).
* **Elasticsearch Impact:** ES heap size tuned to 8GB with bulk indexing buffer.
* **Queue Impact:** Horizon worker count configured to 32 worker processes across 4 queues (`default`, `coins`, `elasticsearch`, `broadcast`).
* **Performance Impact:** System capacity scaled to handle 2,500+ RPS.
* **Concurrency Impact:** Eliminates connection starvation and socket exhaustion.
* **Business Risk:** High (System configuration; requires execution during maintenance window).
* **Testing Requirements:** Infrastructure monitoring stress test verifying CPU/RAM/Disk IO limits.
* **Rollback Strategy:** Backup original configuration files for instant restoration.
* **Dependencies:** Phase 0 - Phase 7.

---

### PHASE 9 — 10K+ Load & Stress Testing Validation

* **Objective:** Validate end-to-end performance, concurrency safety, and stability under simulated 10,000 recruiter load.
* **Current Problem:** Unvalidated system capacity under heavy load.
* **Proposed Solution:** Execute Grafana k6 / Locust load testing scripts targeting:
  1. 10,000 concurrent active sessions.
  2. 500 concurrent recruiters belonging to the same tenant company performing coin-deducting candidate unlocks.
  3. Background daily coin deduction job running simultaneously during peak load.
* **Files/Modules Affected:** `tests/K6/coins_scalability_test.js` [NEW]
* **DB Impact:** Verified zero deadlocks, zero negative balances, zero lost updates.
* **Redis Impact:** Verified sub-millisecond latency.
* **Elasticsearch Impact:** Verified async indexing stability.
* **Queue Impact:** Verified queue backlog resolves within < 30 seconds.
* **Performance Impact:** Validated P95 response latency < 50ms at 2,500 RPS.
* **Concurrency Impact:** 100% transactional safety confirmed.
* **Business Risk:** Zero (Executed on isolated staging environment).
* **Testing Requirements:** Full suite of load, stress, spike, and endurance tests.
* **Rollback Strategy:** N/A (Testing phase).
* **Dependencies:** Phase 8.

---

### PHASE 10 — Production Rollout & Zero-Downtime Migration

* **Objective:** Safely deploy Coins V2 architecture into production with zero downtime and instant rollback capability.
* **Current Problem:** Direct deployment risks service interruption or unexpected edge-case failures.
* **Proposed Solution:**
  1. Deploy migration and code behind feature flag `config('features.v2_coins')`.
  2. Enable Canary rollout: 5% of companies -> 25% -> 50% -> 100%.
  3. Monitor error rates and database lock metrics in real time.
* **Files/Modules Affected:** Deployment scripts & feature flag configurations.
* **DB Impact:** Seamless background migration.
* **Redis Impact:** Real-time feature flag toggle storage.
* **Elasticsearch Impact:** None.
* **Queue Impact:** Zero downtime worker restart via `php artisan horizon:terminate`.
* **Performance Impact:** Immediate speedup for migrated tenants.
* **Concurrency Impact:** Production concurrency protection active.
* **Business Risk:** Low (Gated by feature flags and canary rollout).
* **Testing Requirements:** Live canary telemetry check and real-time log error inspection.
* **Rollback Strategy:** Set `v2_coins` feature flag to `false` to instantly route traffic back to legacy code paths.
* **Dependencies:** Phase 9.

---

## 3. Comprehensive Testing Strategy

```mermaid
graph TD
    A[Unit Tests] --> B[Integration Tests]
    B --> C[Concurrency & Race Condition Tests]
    C --> D[Load & Stress Tests (k6)]
    D --> E[Failure & Chaos Recovery Tests]
```

### 1. Unit & Feature Tests (PHPUnit / Pest)
* Test atomic balance debit with sufficient vs. insufficient balance.
* Test transaction log generation accuracy.
* Test auto-settlement package activation logic.
* **Crucial Rule:** Do **NOT** use `RefreshDatabase` (per project rules). Use clean tenant isolation helper fixtures and database transaction rollbacks in tests.

### 2. Concurrency & Race Condition Tests
* Utilize parallel HTTP requests (Guzzle / ReactPHP / Process) hitting the exact same tenant ID simultaneously.
* Assert that total final balance equals `initial_balance - (concurrent_requests * deduction_rate)`.
* Assert that negative balance is impossible (`coins >= 0` check).

### 3. Load & Stress Tests (k6)
* Simulate 1,000, 5,000, and 10,000 concurrent virtual users (VUs).
* Measure P95, P99 latency, HTTP failure rate, and MySQL deadlock rate.

### 4. Failure & Recovery Chaos Tests
* Simulate Redis server crash during active coin deduction -> verify graceful fallback to MySQL `lockForUpdate()`.
* Simulate Elasticsearch cluster failure -> verify background job completes DB transaction successfully.

---

## 4. Rollout & Monitoring Strategy

### Feature Flag Gating
```php
if (config('features.v2_coins_enabled')) {
    return $this->coinService->deductCoinsAtomic($companyId, $eventId, $userId);
} else {
    return GeneralMethod::coinsDeductions($companyId, $eventId, $userId, $companyId);
}
```

### Key Monitoring Metrics (Grafana / Prometheus Alerting)
1. **Financial Integrity Metric:** `count(company_coins WHERE coins < 0)` -> Trigger Alert if > 0.
2. **Deadlock Metric:** `mysql_performance_schema_lock_deadlocks` -> Alert if > 0 per minute.
3. **P95 API Response Time:** `/api/v2/coins/*` -> Alert if > 200ms.
4. **Queue Lag:** `horizon_queue_delay{queue="coins"}` -> Alert if > 15 seconds.

---

## 5. Final Recommended Implementation Order

To maximize safety and achieve rapid performance gains, execution should strictly follow this sequence:

1. **Phase 0:** Baseline & Instrumentation
2. **Phase 3:** Database Index Optimization (Immediate low-risk speedup for reads)
3. **Phase 1:** Critical Coin Transaction Safety (Core financial protection)
4. **Phase 2:** Query / N+1 Optimization (Background job speedup)
5. **Phase 4:** Redis Caching & Distributed Locking
6. **Phase 5:** Queue & Async Processing Scalability
7. **Phase 6:** API & Frontend Idempotency Safeguards
8. **Phase 7:** Elasticsearch Async Event Decoupling
9. **Phase 8:** Server & Infrastructure Scaling
10. **Phase 9:** 10K+ Load Testing Validation
11. **Phase 10:** Production Rollout

---
*End of Implementation Plan.*
