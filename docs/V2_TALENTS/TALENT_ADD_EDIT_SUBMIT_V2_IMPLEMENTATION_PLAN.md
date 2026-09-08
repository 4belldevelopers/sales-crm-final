# V2 Talent (Bench Candidates) Module Implementation Plan

**Target Module:** Service Company Bench Candidates / Talent (`/service_company/bench-candidates`)  
**Objective:** Architect and deploy high-performance, resilient, and scalable V2 flows for Add Talent, Edit Talent, and Submit/Save operations capable of supporting 10,000+ concurrent recruiters.

---

## Architectural Principles & V2 Strategy

1. **Decouple External Service Synchronous Network I/O from MySQL Transactions:**
   Move all Elasticsearch indexing operations (`ELASTIC_BENCH_ASSIGN`, `ELASTIC_BENCH_ADDITIONAL_DETAIL`, `ELASTIC_CANDIDATES_BENCH`) and remote file operations into queued background jobs via Laravel Horizon.
2. **Eliminate Database N+1 Query Loops:**
   Replace single-row insert/update loops with bulk relational insertions and indexed key lookups.
3. **Optimized Frontend Hydration:**
   Eliminate heavy full-table reads (`GeneralMethod::allCities()`) on page load in favor of AJAX auto-complete / select2 endpoints and cached reference lookups.
4. **Enforce Backend Idempotency & Rate Protection:**
   Utilize Redis distributed locks and idempotency request keys to protect against duplicate candidate creation and race conditions under peak concurrency.

---

## Phase-by-Phase V2 Implementation Plan

### Phase 0 — Baseline & Safety
- **Problem:** Absence of automated baseline benchmarks and risk of regressions during V2 deployment without rollback flags.
- **Solution:**
  - Establish APM logging and telemetry for `create()`, `edit()`, and `store()` execution times.
  - Implement a feature flag (`config('features.v2_talent_module')`) to allow instant rollback to V1 logic if anomalies occur.
  - Create integration test suites targeting store, update, validation, and authorization flows.
- **Risk:** Low risk; purely additive observation and feature toggling.
- **Testing:** Execute `php artisan test` covering `ServiceCompanyBenchCandidatesTest` suite.
- **Expected Result:** Verified zero-impact baseline telemetry and functional feature flag switcher.

---

### Phase 1 — Add Client Optimization (Add Talent Page Load)
- **Problem:** `create()` method loads `GeneralMethod::allCities()` and redundant master lookup datasets, inflating memory usage per request.
- **Solution:**
  - Replace synchronous loading of all global cities with dynamic AJAX search endpoints.
  - Cache static master datasets (`qualification_master`, `countries`) in Redis with 24-hour TTL (`Cache::remember('qualification_master', ...)`).
- **Risk:** Missing cached datasets if Redis drops; mitigated by graceful DB fallback.
- **Testing:** Verify Add Talent page load duration and check PHP memory footprint via Laravel Telescope/Debugbar.
- **Expected Result:** Page load response time under 150ms; PHP memory consumption reduced by >60%.

---

### Phase 2 — Edit Client Optimization (Edit Talent Page Load)
- **Problem:** `edit()` action fetches all company jobs without limits and executes duplicate DB calls for `application_additional_detail` (3x queries) and master lookups.
- **Solution:**
  - Scope job selector to active/relevant jobs with pagination or auto-suggest API.
  - Consolidate duplicate table queries into a single structured query call and pass shared datasets to the view.
  - Eager load candidate relations (`with(['job', 'documents'])`) to eliminate lazy-loading query overhead.
- **Risk:** Outdated job list in select dropdown; resolved by active state filtering.
- **Testing:** Measure DB query count during candidate edit page rendering across existing test profiles.
- **Expected Result:** Total SQL queries on Edit Candidate page load reduced from ~18 queries to ≤5 queries.

---

### Phase 3 — Submit/Create Optimization
- **Problem:** Creating a talent profile executes synchronous Elasticsearch indexing, R2 storage checks, and N+1 SQL insert loops inside `DB::beginTransaction()`.
- **Solution:**
  - Restructure `store()` to perform fast local MySQL database commits.
  - Dispatch asynchronous queued jobs (`SyncBenchCandidateToElastic`, `SyncBenchAssignToElastic`) to handle Elasticsearch operations out-of-band via Redis Horizon queues.
  - Replace loop-based skill inserts with bulk `insert()` array operations.
- **Risk:** Temporary eventual consistency gap (100-300ms) between MySQL commit and Elasticsearch index appearance.
- **Testing:** Submit new candidates and verify immediate DB commit followed by asynchronous background queue processing.
- **Expected Result:** HTTP response time for candidate creation drops from ~1200ms to <200ms; DB transaction hold time reduced by >85%.

---

### Phase 4 — Submit/Update Optimization
- **Problem:** Candidate updates execute heavy 7-table SQL joins inside controller methods and force immediate Elasticsearch Lucene commits (`'refresh' => true`).
- **Solution:**
  - Remove `'refresh' => true` from Elasticsearch update calls and transition updates to background event listeners (`BenchCandidateUpdated` event).
  - Perform differential updates on `job_application_skills` and `bench_candidate_skill_experience` using batch SQL updates.
- **Risk:** High-frequency updates on the same profile could arrive out-of-order in background queues; mitigated by queueing jobs by candidate ID (`onQueue('candidate-updates')->selectId()`).
- **Testing:** Perform rapid sequential updates on a single profile and verify final database and Elasticsearch state consistency.
- **Expected Result:** Candidate update latency under 180ms; zero Elasticsearch refresh locks on primary HTTP threads.

---

### Phase 5 — Database Optimization
- **Problem:** Unindexed or poorly indexed query filters on `job_applications`, `bench_assign`, and `bench_additional_detail` lead to full table scans during lookup.
- **Solution:**
  - Validate and ensure optimal compound indexes exist in MySQL schema (`database/schema/mysql-schema.sql`):
    - `job_applications(company_id, condidate_type, deleted_at)`
    - `bench_assign(bench_applications_id, company_id)`
    - `bench_additional_detail(bench_id)`
    - `bench_candidate_skill_experience(candidate_id, skill_key)`
- **Risk:** Negligible risk; indexes align with read access patterns.
- **Testing:** Run `EXPLAIN` on candidate store and edit lookup queries to verify index coverage.
- **Expected Result:** Zero unindexed queries during Add/Edit/Submit actions.

---

### Phase 6 — AJAX/API Optimization
- **Problem:** Form submissions send uncompressed multi-part payloads and receive redundant HTML/data responses.
- **Solution:**
  - Standardize JSON API payload contracts (`{ status: 'success', data: { candidate_id: 123 }, redirect_url: '...' }`).
  - Implement form field payload sanitization and client-side pre-validation before trigger.
- **Risk:** API contract mismatches on legacy JS handlers; addressed by automated contract testing.
- **Testing:** Verify JSON payload structure and size across Add and Edit submissions.
- **Expected Result:** Payload response size reduced to <2KB; predictable JSON schema response.

---

### Phase 7 — Redis Optimization
- **Problem:** Redis cache eviction in `store()` flushes keys per individual user but lacks structured tag-based cache management for company listings.
- **Solution:**
  - Implement Redis Cache Tags (`Cache::tags(['company_'.$company_id, 'bench_candidates'])`).
  - Cache company candidate counts and filter states with versioned Redis keys.
- **Risk:** Cache tag invalidation failure; handled by fallback key eviction rules.
- **Testing:** Measure cache hit/miss ratio during high-volume candidate additions.
- **Expected Result:** >95% cache hit rate for bench count lookups and master option data.

---

### Phase 8 — Frontend Optimization
- **Problem:** `create.blade.php` is a monolithic 140KB file causing slow DOM rendering and high browser CPU load.
- **Solution:**
  - Componentize `create.blade.php` into modular sub-views (`_basic_info.blade.php`, `_skills_experience.blade.php`, `_custom_details.blade.php`).
  - Extract inline JS scripts into dedicated, compiled asset modules (`talent-form-handler.js`).
- **Risk:** Script dependency ordering issues; managed via Vite asset pipeline bundling.
- **Testing:** Audit DOM load time and script execution timeline via Chrome DevTools.
- **Expected Result:** DOM Interactive time reduced by >50%; clean separation of Blade components.

---

### Phase 9 — Network & Retry Safety
- **Problem:** Transient network drops during submit cause ambiguous error popups or potential double submissions.
- **Solution:**
  - Implement client-side submit token generation (`X-Idempotency-Key`).
  - Add backend Redis idempotency middleware (`IdempotentSubmissionMiddleware`) locking submission tokens for 10 seconds.
  - Implement exponential backoff for background queue retries on external API calls (R2 / Elasticsearch).
- **Risk:** User attempting legitimate submit blocked if token collision occurs; mitigated by UUIDv4 token generation per form load.
- **Testing:** Simulate network delays and duplicate submit clicks in browser; verify single database record creation.
- **Expected Result:** Guaranteed single-record creation on duplicate button clicks; resilient retry background pipeline.

---

### Phase 10 — Concurrency Optimization
- **Problem:** Under 10,000+ concurrent recruiter scenario, synchronous connection limits on MySQL and Elasticsearch cause thread exhaustion.
- **Solution:**
  - Leverage FrankenPHP / Octane worker persistence for non-blocking I/O.
  - Decouple DB writes from HTTP worker execution threads using queue workers scaled via Laravel Horizon.
- **Risk:** Horizon queue backlog if worker count insufficient; mitigated by auto-scaling queue rules.
- **Testing:** Simulate concurrent connection pools and monitor PHP-FPM / Horizon worker queue depth.
- **Expected Result:** System maintains stable response times without HTTP 500 / 504 gateway timeouts under peak concurrency.

---

### Phase 11 — Load Testing
- **Problem:** Real-world performance under 10K+ concurrent load is unknown without empirical stress testing.
- **Solution:**
  - Run k6 / Apache JMeter scripts targeting candidate creation and editing endpoints.
  - Measure P95 latency, throughput (RPS), error rates, and resource utilization.
- **Risk:** Load testing impacting shared environment; run strictly on isolated test environment (`testing.jero.works`).
- **Testing:** Execute 10-minute sustained concurrency load test script with 5,000-10,000 virtual users.
- **Expected Result:** P95 response time <250ms; 0% request drop rate.

---

### Phase 12 — V1 vs V2 Validation
- **Problem:** Verification of feature parity and performance improvement required before production deployment.
- **Solution:**
  - Compare V1 baseline telemetry against V2 test metrics across latency, query count, memory, and success rate.
  - Perform visual regression testing on Add/Edit Blade components.
- **Risk:** Undetected functional divergence; avoided by comprehensive field-level diff validation.
- **Testing:** Run comparative automated test suites validating full output matching between V1 and V2.
- **Expected Result:** 100% functional parity achieved with confirmed >4x throughput performance gain.

---

### Phase 13 — Production Rollout & Rollback
- **Problem:** Deploying V2 to live environments carries operational risks if unexpected traffic patterns emerge.
- **Solution:**
  - Deploy via canary strategy using `config('features.v2_talent_module')` enabled for a subset of companies first.
  - Monitor APM dashboards, error logs, and Horizon queue status.
  - Maintain instant zero-downtime rollback mechanism to revert feature flag to V1.
- **Risk:** Data incompatibility between V1 and V2 formats; prevented by schema symmetry (zero structural DB changes).
- **Testing:** Monitor real-time error rates and latency metrics post-canary release.
- **Expected Result:** Seamless production rollout with zero downtime and instant rollback capability.

---

## Final Verification Checklist & Next Steps

- [x] V1 Module Architecture & Data Flow Audited
- [x] Confirmed Problems & Performance Vulnerabilities Documented
- [x] Phase 0 - Phase 13 V2 Implementation Roadmap Defined
- [ ] **AWAIT USER APPROVAL BEFORE CODE IMPLEMENTATION**
