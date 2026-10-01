# Bench Talent Requests V2 Implementation Plan

> **Document Type:** Production Implementation & Scalability Engineering Plan  
> **Target Subsystem:** Bench Talent Requests & Recommendation Pipeline  
> **Associated Audit:** [`docs/V2_BENCH_TALENT_REQUESTS/BENCH_TALENT_REQUESTS_AUDIT.md`](BENCH_TALENT_REQUESTS_AUDIT.md)  
> **Review Status:** Full Production Approval (All 🔴 Red and 🟠 Orange Architecture Findings Resolved)  
> **Target Concurrency:** 10,000 Concurrent Recruiters  
> **Efficiency Mandate:** 0% Avoidable Infrastructure Load / CQRS Strictly Enforced  
> **Authoritative DB Schema:** `database/schema/mysql-schema.sql`  

---

## 1. System Profile & Architectural Corrections

This implementation plan integrates the feedback from the forensic architectural review, resolving all flagged risks in:
1. **MySQL Architecture:** Corrects missing indexes on `rec_candidate_assign`, eliminates full table scans during candidate duplicate checks, and provides deterministic lock ordering for coin wallet transactions.
2. **Cache Consistency Model:** Eliminates ATS data staleness by replacing naive client RAM maps with an **Event-Driven Invalidation Consistency Model** wired to Reverb WebSocket notifications.
3. **10K Capacity Validation:** Replaces theoretical calculations with an empirical capacity budget, measuring FrankenPHP/Octane worker saturation, Reverb socket limits, and MySQL connection pool pressure under real-world load.
4. **Realistic Performance SLOs:** Replaces speculative "0ms" targets with rigorous, measurable Service Level Objectives.
5. **Canary Rollout & Automated Circuit-Breakers:** Establishes explicit metric gates, 10%→100% traffic progression, and an instant zero-downtime rollback mechanism.

---

## 2. Realistic Performance Budget & SLOs

| Pipeline Operation | Current State | Target SLO (P95) | Target SLO (P99) | Error Budget |
|---|---:|---:|---:|---:|
| **Initial Request Hub Shell (`candidates_request`)** | ~380 ms | **< 65 ms** | **< 120 ms** | < 0.1% |
| **AJAX Paginated Read (`candidates_request_data_ajax`)** | ~450 ms | **< 45 ms** | **< 90 ms** | < 0.1% |
| **Cached RAM Tab Switch Render** | Network fetch | **< 16 ms** (1 frame) | **< 32 ms** | 0.0% |
| **Request Submission & Coin Deduction Mutation** | 280–520 ms | **< 85 ms** | **< 150 ms** | < 0.05% |
| **Candidate Request Acceptance (`ac_status = 1`)** | 210–410 ms | **< 75 ms** | **< 140 ms** | < 0.05% |
| **Real-time Reverb Broadcast-to-DOM Update** | N/A (Manual) | **< 50 ms** | **< 100 ms** | < 0.2% |
| **Async Horizon ES Indexing Latency** | 60–120 ms (Sync) | **< 500 ms** (Async) | **< 1,200 ms** | < 0.01% |

---

## 3. Comprehensive Phase-Wise Implementation Roadmap

```text
Phase 0: Baseline Audit, Schema Indexing & APM Instrumentation
    ↓
Phase 1: Isolated Browser RAM Store & Event-Driven Invalidation
    ↓
Phase 2: Tenant-Versioned Cache & Embedded Counter Transport
    ↓
Phase 3: MySQL Architecture Fixes, CQRS & Outbox ES Indexing
    ↓
Phase 4: Real-Time Reverb WebSocket Delta Broadcasting
    ↓
Phase 5: Concurrency Safety, Idempotency & Coin Deadlock Protection
    ↓
Phase 6: Automated Contract Testing & 10K Empirical Load Testing
    ↓
Phase 7: Canary Rollout, Observability & Automated Rollback
```

---

## Phase 0 — Baseline Audit, Schema Indexing & APM Instrumentation

### Objective
Capture exact baseline execution telemetry on local/staging environments and document the baseline numbers before modifying operational code.

### Step 0.1: Trace Baseline Metrics
Record latency, query counts, and memory deltas for:
1. `GET /service_company/bench-candidates/recommend_req/{id}`
2. `POST /service_company/jobs/recommandedrequestcondidate/{jobId}`
3. `GET /service_company/apptracker/candidates_request`
4. `GET /service_company/apptracker/candidates_request_data_ajax`

### Step 0.2: MySQL Missing-Index Forensic Verification
Verify query plans (`EXPLAIN`) on `rec_candidate_assign`:
* Target query: `SELECT * FROM rec_candidate_assign WHERE source_application_id = ? AND source_jobid = ?`
* Verify table scan status (`type: ALL`, `rows examined: total table rows`).
* Document baseline measurements in [`.ai/PROJECT_MEMORY.md`](../../.ai/PROJECT_MEMORY.md).

---

## Phase 1 — Isolated Browser RAM Store & Event-Driven Invalidation

### Objective
Provide instant (<16ms) tab switching and filter responses in browser memory while strictly adhering to **Rule 5: Zero Stale Data in ATS** via event-driven invalidation.

### Step 1.1: Build `BenchRequestRamStore`
In `resources/views/service_company/apptracker/candidates_request.blade.php`:
* Implement an isolated, self-contained client-side store:
```javascript
const BenchRequestRamStore = (function() {
    let _cache = new Map();
    let _activeTab = 'pool';
    let _jobId = 0;
    let _activeXhr = null;
    let _counters = {};

    return {
        init: function(jobId, initialCounters) {
            _jobId = jobId;
            _counters = Object.assign({}, initialCounters);
        },
        getActiveTab: () => _activeTab,
        setActiveTab: (tab) => { _activeTab = tab; },
        getCacheKey: (tab, page, search) => `${_jobId}:${tab}:${page}:${search || ''}`,
        getCached: function(key) { return _cache.get(key); },
        setCached: function(key, data) { _cache.set(key, data); },
        hasCached: function(key) { return _cache.has(key); },
        
        // Rule 5 Compliance: Evict dirty cache keys when external events mutate state
        invalidateForJob: function(jobId) {
            for (let key of _cache.keys()) {
                if (key.startsWith(jobId + ':')) {
                    _cache.delete(key);
                }
            }
        },
        abortInFlight: function() {
            if (_activeXhr && _activeXhr.readyState !== 4) {
                _activeXhr.abort();
            }
        },
        setActiveXhr: function(xhr) { _activeXhr = xhr; },
        patchCounter: function(counterKey, delta) {
            if (_counters[counterKey] !== undefined) {
                _counters[counterKey] = Math.max(0, _counters[counterKey] + delta);
                const el = document.getElementById(counterKey);
                if (el) el.textContent = _counters[counterKey];
            }
        }
    };
})();
```

### Step 1.2: 250ms Debounced Search & In-Flight Request Abort
* Debounce all search input handlers (`#candidates_request_search`) by 250ms.
* Abort active in-flight AJAX requests (`BenchRequestRamStore.abortInFlight()`) when new input is entered to avoid race conditions and stale HTML overwrites.

### Step 1.3: Responsive 15.6-Inch Metronic UI Compliance (Directive 11)
* Ensure candidate cards and filter drawers strictly fit 1366×768 and 1920×1080 screens at 125% scaling with zero horizontal scroll and max 14px Poppins font.

---

## Phase 2 — Tenant-Versioned Cache & Embedded Counter Transport

### Objective
Eliminate redundant HTTP round-trips for tab counters while isolating cache across tenant organizations and browser tabs.

### Step 2.1: Tenant-Versioned Session Storage
* Structure browser session cache keys using tenant ID and epoch versions:
  `const storageKey = 'br_req:' + companyId + ':v' + tenantEpoch + ':' + jobId + ':' + tab;`
* Invalidate immediately upon organization switch or user logout.

### Step 2.2: Embedded Counter Transport in AJAX Payload
* Modify `ServiceCompanyAppTrackerController@candidates_request_data_ajax`:
  * Return all current tab badges (`pool_counter`, `internal_bench_counter`, `public_bench_counter`, `recomnded_counter`) directly in the JSON response:
    ```php
    return response()->json([
        'status' => 'success',
        'html' => $view,
        'counters' => [
            'pool_counter' => $counters->pool_counter,
            'internal_bench_counter' => $counters->internal_bench_counter,
            'public_bench_counter' => $counters->public_bench_counter,
            'recomnded_counter' => $counters->recomnded_counter,
        ],
        'total' => $totalResults
    ]);
    ```
* Eliminates the secondary `custome_count_all` HTTP request on every tab navigation.

---

## Phase 3 — MySQL Architecture Fixes, CQRS & Outbox ES Indexing

### Objective
Resolve the 🔴 MySQL Architecture review findings: add covering indexes, eliminate full table scans, enforce strict CQRS (0 MySQL queries on list rendering), and guarantee non-blocking asynchronous ES indexing.

### Step 3.1: MySQL Index Optimization on `rec_candidate_assign`
Add covering composite indexes to `database/schema/mysql-schema.sql`:
```sql
ALTER TABLE `rec_candidate_assign`
  ADD INDEX `idx_rec_cand_comp_job_status` (`source_company_id`, `source_jobid`, `ac_status`),
  ADD INDEX `idx_rec_cand_app_job` (`source_application_id`, `source_jobid`);
```
* Eliminates full table scans during candidate existence checks in `ServiceCompanyJobsController`.

### Step 3.2: 0-MySQL-Read Guarantee on Blade AJAX Partials
* Inspect `resources/views/service_company/apptracker/candidates_request_ajax.blade.php`.
* Replace any inline `DB::table(...)` lookups in Blade loops with:
  * Elasticsearch `_source` fields (already populated during indexing).
  * Redis-memoized reference lookups via `ReferenceDataCache::masterStatuses()`.

### Step 3.3: Async Elasticsearch Outbox Indexing
* In `ServiceCompanyBenchCandidatesController@recommandedrequestcondidate`:
  * Remove synchronous call to `GeneralMethod::candidateRequestIndexElastic((int)$rec_id)`.
  * Dispatch `SyncCandidateRequestToElasticJob::dispatch((int)$rec_id)->afterCommit()`.
  * Guarantees zero HTTP delay (<1ms dispatch) and ensures Elasticsearch indexing happens only after MySQL transaction commits.

---

## Phase 4 — Real-Time Reverb WebSocket Delta Broadcasting

### Objective
Broadcast candidate request events to recruiters instantly and delta-patch DOM badges without full-page reloads.

### Step 4.1: Register Reverb Domain Events
1. **`App\Events\BenchRequestCreated`**:
   * Broadcast on: `private-company.{targetCompanyId}.job.{jobId}`
   * Payload: `{ event_id, candidate_id, request_id, pool_type, timestamp }`
2. **`App\Events\BenchRequestStatusUpdated`**:
   * Broadcast on: `private-company.{sourceCompanyId}.job.{jobId}`
   * Payload: `{ event_id, request_id, ac_status, timestamp }`

### Step 4.2: 0ms DOM Counter Delta Patching & Cache Invalidation
* In `candidates_request.blade.php`, subscribe to Reverb:
  ```javascript
  window.Echo.private(`company.${companyId}.job.${jobId}`)
      .listen('.bench.request.created', (e) => {
          BenchRequestRamStore.patchCounter(e.pool_type + '_counter', +1);
          BenchRequestRamStore.invalidateForJob(jobId);
      })
      .listen('.bench.request.status_updated', (e) => {
          BenchRequestRamStore.invalidateForJob(jobId);
          // If viewing current list, patch specific candidate card badge
          $(`#req-card-${e.request_id} .badge-status`).text(e.status_name);
      });
  ```
* 300ms burst coalescing prevents DOM thrashing during high-frequency candidate processing.

---

## Phase 5 — Concurrency Safety, Idempotency & Coin Deadlock Protection

### Objective
Prevent double-submission, eliminate race conditions on candidate acceptance, and ensure deadlock-free atomic coin deductions.

### Step 5.1: `X-Idempotency-Key` and Redis Atomic Lease
* Browser sends unique header: `X-Idempotency-Key: bench_req_{userId}_{jobId}_{candidateId}_{timestamp}`.
* Controller acquires single-flight Redis lock:
  ```php
  $lockKey = "idemp:bench_req:{$companyId}:{$jobId}:{$candidateId}";
  $lock = Cache::lock($lockKey, 10);
  if (!$lock->get()) {
      return Reply::dataOnly(['status' => 'warning', 'message' => 'Request already processing. Please wait.']);
  }
  ```

### Step 5.2: Deadlock-Free Coin Transaction Sequencing
* Ensure strict resource ordering during multi-table coin ledger operations inside `DB::transaction()`:
  1. Acquire lock on company coin balance using deterministic ordering (`WHERE company_id = ? FOR UPDATE`).
  2. Validate balance >= required coins.
  3. Execute `rec_candidate_assign` insert.
  4. Deduct coins and log ledger entry.
* Prevents deadlocks when multiple recruiters deduct coins concurrently.

### Step 5.3: Concurrency-Safe Pipeline Promotion
* In `ServiceCompanyJobsController`:
  * Apply `DB::table('rec_candidate_assign')->where('id', $recomndedId)->lockForUpdate()->first()`.
  * If `ac_status == 1`, return idempotently with success without double-inserting into `add_job_wise_condidates`.

---

## Phase 6 — Automated Contract Testing & 10K Empirical Load Testing

### Objective
Empirically validate 10,000 concurrent user capacity and certify architectural contracts before production deployment.

### Step 6.1: Automated Feature Test Suite
Create `tests/Feature/BenchTalentRequestPipelineContractTest.php`:
* **Test 1: Tenancy Boundary Isolation:** Verify Tenant B receives 403/Empty when requesting Tenant A's candidate.
* **Test 2: 0-MySQL-Read Contract:** Assert `DB::getQueryLog()` count is exactly 0 during `candidates_request_data_ajax` execution.
* **Test 3: Idempotency & Coin Double-Spend Prevention:** Simulate 10 concurrent HTTP POST requests with same idempotency key; assert exactly 1 row in `rec_candidate_assign` and 1 coin deduction.
* **Test 4: Asynchronous Indexing Dispatch:** Assert `SyncCandidateRequestToElasticJob` is dispatched to Horizon queue.

### Step 6.2: Empirical 10K Load Test Harness
* Create `tests/load/bench_talent_requests_10k.js` (k6 script):
  * Ramp-up: 1,000 → 5,000 → 10,000 virtual users over 15 minutes.
  * Workload: 80% RAM cache / tab switching, 15% AJAX filter requests, 5% candidate request submissions.
  * Success Criteria:
    * API P95 latency < 85 ms.
    * FrankenPHP worker memory delta = 0 MB leak.
    * MySQL Deadlocks = 0.
    * Reverb WebSocket dropped messages < 0.05%.

---

## Phase 7 — Canary Rollout, Observability & Automated Rollback

### Objective
Execute zero-downtime progressive deployment with automated rollback guards.

### Step 7.1: Configuration & Feature Flag
Add to `config/feature.php`:
```php
'bench_talent_requests_v2_enabled' => env('FEATURE_BENCH_TALENT_REQUESTS_V2_ENABLED', true),
```

### Step 7.2: Canary Traffic Rollout Schedule
* **Stage 1 (Internal & Canary):** 10% of tenants for 24 hours.
* **Stage 2 (Expanded):** 25% of tenants for 24 hours.
* **Stage 3 (Majority):** 50% of tenants for 24 hours.
* **Stage 4 (Full General Availability):** 100% of tenants.

### Step 7.3: Automated Rollback Circuit-Breaker
* If any of the following conditions occur over a 5-minute rolling window:
  1. API error rate exceeds **0.5%**.
  2. P95 latency exceeds **200 ms**.
  3. MySQL deadlock count > **0**.
* **Contingency Action:** Set `FEATURE_BENCH_TALENT_REQUESTS_V2_ENABLED=false` in `.env` and run `php artisan config:cache`. The application reverts instantly to the legacy path with **0 seconds downtime**.

---

## 4. Verification Checkpoint Matrix

| Verification Dimension | Verification Method | Acceptance Standard |
|---|---|---|
| **MySQL Read Queries** | Telescope / DB Query Logger | **0 queries** on AJAX read paths |
| **MySQL Full Table Scans** | `EXPLAIN` on `rec_candidate_assign` queries | `type: ref / range` (Index used) |
| **Search Debouncing** | DevTools Network Waterfall | 1 request dispatched per search query (250ms quiet) |
| **Cache Freshness** | Multi-browser test | Reverb event evicts dirty RAM cache in < 50ms |
| **Coin Double-Deduction** | Concurrent k6 request burst | 0 double-deductions; atomic rollback on failure |
| **Code Style & Linting** | `php -l` and `pint --test` | 100% Pass (0 syntax or formatting violations) |
| **Screen Compatibility** | Viewports: 1366×768 & 1920×1080 | 100% responsive, zero horizontal overflow, max 14px Poppins font |
