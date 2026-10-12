# PHASE-WISE IMPLEMENTATION PLAN — RECOMMEND INTERNAL REQUISITIONS
## Service Company → Recommend Internal Request (Bench Candidates)

> **Target Route:** `service_company.bench-candidates.recommend_internal_req`  
> **Target Capacity:** 10,000 Active Concurrent Users  
> **Environment Context:** JERO Multi-tenant B2B Hiring SaaS (Laravel 11, MySQL 8, Elasticsearch 8, DragonflyDB/Redis, Reverb)  
> **Document Status:** AUTHORITATIVE IMPLEMENTATION SPECIFICATION  
> **Date:** October 12, 2026  

---

## 1. Executive Implementation Roadmap

This plan establishes an eight-phase engineering roadmap (Phases 0 through 7) to optimize the Recommend Internal Requisitions module for 10,000 active concurrent users with <15ms response latency, zero duplicate mutations, and 100% business logic preservation.

---

## 2. Phase-by-Phase Technical Specifications

---

### Phase 1: Browser and Local-State Optimization

#### 1. Exact Files and Components Affected
- `resources/views/service_company/bench-candidates/recommend_internal_req.blade.php`
- Local assets: SweetAlert & FontAwesome

#### 2. Current Behavior
- Loads external CDN (`cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js`).
- Mutating candidate (`.request-send`) executes 3 redundant tasks:
  1. Modifies DOM button in-place to `<a class="btn-cta-sent">Request Sent</a>`.
  2. Immediately makes a second synchronous AJAX GET to `mailrecommandedrequestcondidate`.
  3. Immediately calls `fetchBenchRecommendedReqCandidateList()`, causing a full-page AJAX download (~37 KB) and re-rendering of all 10 cards.
- Free-text search and Tagify filters fire unthrottled requests.
- No structured in-memory state store.

#### 3. Proposed Change
- Localize CDN scripts to local assets.
- Eliminate the redundant full-list AJAX re-fetch (`fetchBenchRecommendedReqCandidateList()`) on successful mutation; retain the authoritative in-place button update.
- Eliminate the separate frontend AJAX GET to `mailrecommandedrequestcondidate`; offload email dispatch entirely to backend outbox worker.
- Introduce an isolated page store (`window.RecommendInternalReqStore`) in browser RAM for pagination, filters, and pending mutations.
- Add 300ms debounce to search and filter inputs.

#### 4. Technical Justification
- Eliminates 37 KB network transfer on every candidate submission.
- Eliminates 1 unnecessary HTTP request per submission.
- Keeps browser responsive without DOM thrashing.

#### 5. Dependencies and Prerequisites
- None. Fully client-side optimizations.

#### 6. Migration or Deployment Requirements
- Deploy updated Blade template. Zero database migrations.

#### 7. Risks and Mitigations
- *Risk:* Recruiter submits candidate and other job cards don't reflect updated coin count.
- *Mitigation:* Update coin badge in DOM from the mutation response JSON payload.

#### 8. Regression and Security Tests
- Test button transitions: "Submit Candidate" -> "Submitting..." -> "Request Sent".
- Verify double-clicks do not fire duplicate requests.
- Verify drawer triggers (`Details`, `Terms`, `Hiring Manager`, `Team`) continue to open seamlessly.

#### 9. Measurable Acceptance Criteria
- 0 full-list AJAX re-fetches upon clicking "Submit Candidate".
- 1 single HTTP request fired on submission instead of 2.
- 0 external CDN network requests.

#### 10. Rollback Strategy
- Revert `recommend_internal_req.blade.php` via `git checkout`.

---

### Phase 2: IndexedDB and Cache Strategy

#### 2.1 Exact Files and Components Affected
- `resources/views/service_company/bench-candidates/recommend_internal_req.blade.php`

#### 2.2 Current Behavior
- Every page load and navigation re-requests all lookup reference data and candidate summary counters from scratch. Zero client-side persistent storage.

#### 2.3 Proposed Change
- Implement a lightweight, tenant-isolated in-memory and `sessionStorage` cache (`RecommendInternalReqCache`) for static lookup data (salaries, currency modes, qualifications).
- Store recent page query results keyed by `hash(candidate_id, company_id, page, filters)` with 120s TTL.
- Auto-evict cache upon user logout or switching candidates.

#### 2.4 Technical Justification
- Eliminates repeated server round-trips when recruiters toggle between pagination pages or back and forth from drawer modals.

#### 2.5 Dependencies and Prerequisites
- Phase 1 completion.

#### 2.6 Migration or Deployment Requirements
- Purely browser JavaScript. Zero schema changes.

#### 2.7 Risks and Mitigations
- *Risk:* Stale cache after candidate status changes.
- *Mitigation:* Invalidate cache key upon receiving real-time Reverb WebSocket events or mutation completion.

#### 2.8 Regression and Security Tests
- Verify cache key is partitioned by `company_id` and `candidate_id`.
- Verify confidential data is not retained in persistent browser storage.

#### 2.9 Measurable Acceptance Criteria
- Back-and-forth pagination between page 1 and page 2 renders in <5ms from client memory.

#### 2.10 Rollback Strategy
- Disable cache initialization flag in `recommend_internal_req.blade.php`.

---

### Phase 3: Laravel API, MySQL, and Elasticsearch Optimization

#### 3.1 Exact Files and Components Affected
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- `resources/views/service_company/ajax_pages/bench-recommend-internal-req-ajax.blade.php`
- `app/Helper/GeneralMethod.php`

#### 3.2 Current Behavior
- `recommend_internal_req()` executes 9 SQL queries on cold cache and 3 on warm cache. `JobApplication::find(6)` is executed 3 times redundantly.
- `all_internal_data_ajax()` executes `JobApplication::withoutGlobalScopes()->find($request->condidate_id)` *before* checking the DragonflyDB/Redis cache, resulting in guaranteed SQL execution even on 100% cache hits.
- Blade partial `bench-recommend-internal-req-ajax.blade.php` executes 7 unbatched SQL queries:
  - `rec_candidate_assign`
  - `job_metronics_skills` (redundant: already inside ES hits)
  - `job_categories`
  - `companies`
  - `jobs/clients/companies` multi-join
- Line 7066 contains an unhandled `Undefined array key ""` error when `min_salary_currency` is empty.

#### 3.3 Proposed Change
- Pass pre-fetched `$this->bench_detail` into `canGgdFilterData()` and `benchjobAndRecomCount()`, eliminating 2 redundant SQL queries.
- Cache `canGgdFilterData($id)` in Redis with key `ats:bench_filter_data:{$id}` (TTL: 300s).
- Fix line 7066 with safe null-coalescing: `$ecurrency = $currency[$min_salary_currency ?? 0] ?? 'INR'`.
- In `all_internal_data_ajax()`, move DragonflyDB/Redis cache check to the **very top** before executing any database lookups.
- In `bench-recommend-internal-req-ajax.blade.php`:
  - Read `job_metronics_skills` directly from the Elasticsearch document `_source['job_metronics_skills']` instead of querying MySQL `job_metronics_skills`.
  - Cache company names and category lookups in Redis.

#### 3.4 Technical Justification
- Cuts SQL queries on cold page load from 9 to 2.
- Cuts SQL queries on warm page load from 3 to 0 (100% offload).
- Cuts SQL queries during AJAX rendering from 11 to 2.
- Prevents cache-hit SQL leakage.

#### 3.5 Dependencies and Prerequisites
- Redis / DragonflyDB service active.

#### 3.6 Migration or Deployment Requirements
- Deploy controller and Blade updates. Zero schema changes.

#### 3.7 Risks and Mitigations
- *Risk:* Cache stampede when candidate detail cache expires simultaneously across 10K users.
- *Mitigation:* Wrap cache misses in atomic lock: `Cache::lock("lock:bench_recom:{$candidateId}", 5)->get(...)`.

#### 3.8 Regression and Security Tests
- Verify candidate details, skills, and KPI numbers match exact database truth.
- Verify summary card counters (`bench_explore`, `recommend_req`, `recommend_internal_req`) are identical before and after.

#### 3.9 Measurable Acceptance Criteria
- Warm initial page load SQL queries = 0.
- Warm AJAX list fetch SQL queries = 0.
- AJAX cold fetch duration drops from 413ms to <120ms.

#### 3.10 Rollback Strategy
- Revert controller and blade file changes via Git.

---

### Phase 4: Real-Time and Delta Synchronization

#### 4.1 Exact Files and Components Affected
- `app/Events/BenchRequestCreated.php`
- `resources/views/service_company/bench-candidates/recommend_internal_req.blade.php`

#### 4.2 Current Behavior
- `BenchRequestCreated` event broadcasts strictly to `private-company.{$companyId}.job.{$jobId}`.
- `recommend_internal_req.blade.php` has zero Reverb / Echo listeners.
- Recruiters on this page never receive real-time delta updates when a teammate submits or updates a candidate.

#### 4.3 Proposed Change
- Extend `BenchRequestCreated` to also broadcast on `private-company.{$companyId}` (or channel `company.{$companyId}.candidate.{$candidateId}`).
- In `recommend_internal_req.blade.php`, initialize Echo listener:
  ```javascript
  window.Echo.private('company.' + companyId)
      .listen('.bench.request.created', function(e) {
          if (e.candidateId == currentCandidateId) {
              handleBenchRequestCreatedDelta(e);
          }
      });
  ```
- When event arrives:
  1. Find target job row in DOM via `data-jobId`.
  2. Dynamically replace "Submit Candidate" button with `<span class="badge badge-light-warning"><i class="ti ti-clock me-1"></i> Request Sent</span>`.
  3. Smooth green fade animation (`.row-reverb-updated`).
  4. Invalidate local RAM cache.
- Implement WebSocket reconnection recovery to quietly reconcile state on re-establishment.

#### 4.4 Technical Justification
- Zero page reloads required across multi-tab recruiter sessions.
- Multi-user concurrency: Recruiter B instantly sees that Recruiter A already recommended the candidate for Job X.

#### 4.5 Dependencies and Prerequisites
- Laravel Reverb service running.

#### 4.6 Migration or Deployment Requirements
- Additive event channel broadcast. Zero schema changes.

#### 4.7 Risks and Mitigations
- *Risk:* High WebSocket subscription churn under 10,000 connections.
- *Mitigation:* Single tenant-scoped channel per company session (`company.{companyId}`) with minimal JSON payloads (<200 bytes).

#### 4.8 Regression and Security Tests
- Verify Echo channel authentication verifies user belongs to `company_id`.
- Open two browser tabs; submit candidate in Tab 1; verify Tab 2 updates button in-place within 50ms without reloading.

#### 4.9 Measurable Acceptance Criteria
- Delta UI update latency <50ms after commit.
- 0 page reloads triggered by WebSocket events.

#### 4.10 Rollback Strategy
- Wrap frontend Echo listener in a feature guard: if Echo is unavailable, UI remains gracefully functional.

---

### Phase 5: Outbox, Idempotency, and Failure Handling

#### 5.1 Exact Files and Components Affected
- `app/Services/BenchRequestMutationService.php` (New isolated service)
- `app/Jobs/ProcessBenchRequestOutboxEventJob.php` (New outbox worker)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`

#### 5.2 Current Behavior
- `recommandedrequestcondidate` executes ad-hoc database inserts into `rec_candidate_assign`, dispatches queue jobs inline, and fires Reverb broadcast outside an ACID transaction.
- Duplicate prevention relies on a transient 10s Redis lock and checking `rec_candidate_assign` without an idempotency record.

#### 5.3 Proposed Change
- Create `BenchRequestMutationService`:
  - Atomically claims idempotency key in `sys_idempotency_keys`.
  - Executes candidate eligibility, coin deduction, `rec_candidate_assign` insert, and `sys_outbox_events` insert inside a single `DB::transaction()`.
  - Dispatches `ProcessBenchRequestOutboxEventJob` post-commit.
- Create `ProcessBenchRequestOutboxEventJob`:
  - Dual-lease claim protocol using `FOR UPDATE SKIP LOCKED`.
  - Executes side effects: ES indexing (`SyncCandidateRequestToElasticJob`), Reverb broadcasting (`BenchRequestCreated`), and email notification dispatch (`QueueNotificationMailService`).
  - Marks outbox event `COMPLETED`.

#### 5.4 Technical Justification
- Guarantees 100% reliable at-least-once event publication.
- Eliminates phantom records and lost side-effects during server/worker crashes.
- Replaying identical requests returns prior cached success payload safely.

#### 5.5 Dependencies and Prerequisites
- Existing tables `sys_idempotency_keys` and `sys_outbox_events` (already in schema).

#### 5.6 Migration or Deployment Requirements
- Deploy new service, job, and controller integration. Zero schema changes.

#### 5.7 Risks and Mitigations
- *Risk:* Outbox worker lag under massive traffic spikes.
- *Mitigation:* Worker processes batches of 50 via `SKIP LOCKED`; UI already received authoritative commit response so recruiter experience is instantaneous.

#### 5.8 Regression and Security Tests
- Replay test: Send same request with same idempotency key twice; verify exactly 1 record in `rec_candidate_assign`.
- Payload mismatch test: Send same key with different job ID; verify HTTP 409 rejection.
- Rollback test: Force exception before commit; verify 0 outbox events and 0 assignments created.

#### 5.9 Measurable Acceptance Criteria
- 100% of candidate recommendations recorded in `sys_outbox_events`.
- Duplicate mutation rate = 0.00%.

#### 5.10 Rollback Strategy
- Revert controller method to direct insertion path if needed.

---

### Phase 6: Load, Stress, and Reliability Testing

#### 6.1 Exact Files and Components Affected
- Automated test suite: `tests/Feature/BenchCandidateRecommendInternalReqTest.php`
- Load test scripts in scratch directory.

#### 6.2 Current Behavior
- No dedicated concurrency or outbox tests for `recommend_internal_req`.

#### 6.3 Proposed Change
- Implement automated regression and concurrency tests:
  1. Tenancy isolation validation.
  2. Idempotent submission and deduplication.
  3. Replay protection.
  4. Outbox worker processing and retry backoff.
  5. Cold vs warm caching performance.
- Execute simulated concurrent load benchmarks (1,000 to 10,000 sessions).

#### 6.4 Technical Justification
- Validates the 10,000 concurrent user target under simulated load with empirical data.

#### 6.5 Dependencies and Prerequisites
- Phases 1-5 completed.

#### 6.6 Migration or Deployment Requirements
- Test scripts only.

#### 6.7 Risks and Mitigations
- *Risk:* Load test consumes significant local machine CPU.
- *Mitigation:* Run bounded benchmark loops with high concurrency mathematical extrapolation.

#### 6.8 Regression and Security Tests
- Execute `php artisan test --filter=BenchCandidateRecommendInternalReqTest`.

#### 6.9 Measurable Acceptance Criteria
- 100% test pass rate across all feature, security, and idempotency tests.

#### 6.10 Rollback Strategy
- N/A.

---

### Phase 7: Production Rollout and Monitoring

#### 7.1 Exact Files and Components Affected
- Production deployment scripts, Horizon queue configuration, and logging telemetry.

#### 7.2 Current Behavior
- Ad-hoc logging in controller.

#### 7.3 Proposed Change
- Configure Horizon queue supervisor for `bench-outbox`.
- Add structured operational logging with correlation IDs:
  - `[RecommendReq] Mutation initiated | Candidate: {id} | Job: {jobId} | Key: {key}`
  - `[RecommendReq] Outbox published | Event: {id} | Attempts: {n}`
- Provide canary rollout and rollback runbook.

#### 7.4 Technical Justification
- Ensures operational visibility and immediate alerting on outbox lag or worker failures.

#### 7.5 Dependencies and Prerequisites
- Phase 6 testing signoff.

#### 7.6 Migration or Deployment Requirements
- Horizon supervisor restart (`php artisan horizon:terminate`).

#### 7.7 Risks and Mitigations
- *Risk:* Queue worker process crash.
- *Mitigation:* Systemd / Supervisor automatic restart with dead-letter queue monitoring.

#### 7.8 Regression and Security Tests
- Health check endpoints verify outbox backlog age < 30 seconds.

#### 7.9 Measurable Acceptance Criteria
- Horizon queue processing lag < 500ms.
- Error rate < 0.01%.

#### 7.10 Rollback Strategy
- Step-by-step git revert instructions documented in work report.
