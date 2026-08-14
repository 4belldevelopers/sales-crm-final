# Jobs Module V2 Architecture Implementation Plan

> **Document Status**: COMPLETED & VERIFIED (Phases 0–6 Implemented & Passing Test Suite)  
> **Target Module**: Jobs Management (`service_company/jobs`)  
> **Target Scale**: 10,000+ Concurrent Recruiters  
> **Date**: August 14, 2026  

---

## 1. Executive Overview & Plan Strategy

This implementation plan defines a 14-phase roadmap to transform the **Jobs Module** (`service_company/jobs`) into a high-performance, robust, tenant-isolated, and scalable V2 engine.

The core design objectives are:
1. Extract business logic from the God Controller (`ServiceCompanyJobsController.php`) into isolated Service classes (`JobService`, `JobValidationService`, `JobElasticService`).
2. Move heavy external network calls (Elasticsearch indexing, notification emails) out of MySQL transactions into asynchronous Laravel Horizon queues.
3. Eliminate all N+1 database queries through batch queries and eager loading.
4. Implement Redis caching across all page load flows (`create`, `curstomcreate`, `edit`).
5. Enforce strict multi-tenancy (`company_id`) and comprehensive validation via `StoreJob` FormRequest.
6. Provide network resilience, idempotency keys, and sub-100ms response targets under peak load.

---

## 2. Phase-by-Phase V2 Implementation Plan

### Phase 0 — Baseline & Safety

* **Problem**: Absence of automated baseline test coverage for Add Job, Edit Job, and Submit workflows makes refactoring risky and prone to regressions.
* **Solution**: 
  - Write comprehensive Pest/PHPUnit integration tests covering:
    1. `create()` route access, gate checks, and data array structure.
    2. `curstomcreate()` tenant isolation and payload validation.
    3. `store()` job creation, coin deduction, relationship creation, and event dispatch.
    4. `edit()` job prefill data for existing records.
    5. `update()` job modification, pivot table updates, and cache invalidation.
  - Create a database query counter test helper to record exact baseline query counts.
* **Risk**: High initial test setup time; potential mock configuration mismatch for Elasticsearch or Mail services.
* **Testing**: Run `./vendor/bin/phpunit --filter=JobManagementTest` to ensure 100% green status on V1 before touching any code.
* **Expected Result**: Safety net established; exact query baseline documented.

---

### Phase 1 — Add Job Optimization

* **Problem**: 
  - `curstomcreate($id)` leaks tenant data by omitting `company_id` on active job count (`Job::where('status', 'active')->count()`).
  - Calls `JobCategory::all()`, `Question::all()`, `Company::all()`, `JobLocation::all()`, `Skill::all()`, and `city_list` without tenant scoping or caching, risking memory crashes.
  - Inconsistent permission check (`view_jobs` gate instead of `add_jobs`).
* **Solution**: 
  - Standardize `curstomcreate()` to inherit the cached, tenant-scoped pipeline used in `create()`.
  - Fix permission gate to `Gate::authorize('add_jobs')`.
  - Scope all active job counts and categories strictly by `auth()->user()->company_id`.
  - Cache reference datasets in Redis with tenant keys (`tenant:{company_id}:categories`).
* **Risk**: Misconfigured cache key tags could return stale category data if observers fail.
* **Testing**: Automated tenancy test: verify User A from Company 1 cannot see Company 2 categories or active job counts in `curstomcreate()`.
* **Expected Result**: 100% tenant isolation enforcement; memory footprint reduced by over 90% on `curstomcreate()`.

---

### Phase 2 — Edit Job Optimization

* **Problem**: `edit($id)` executes 22 to 28 uncached SQL queries per request, including duplicate identical queries for `qualification_master` and `job_additional_detail`.
* **Solution**: 
  - Implement Redis caching for `edit($id)` view data using `Cache::remember('tenant:' . $company_id . ':job_edit:' . $id, 300, ...)`.
  - Consolidate redundant queries: query `qualification_master` once and derive tags in memory using Eloquent collection `pluck()`.
  - Replace separate `count()` and `get()` queries on `job_additional_detail` with a single `get()` query.
* **Risk**: Stale edit form data if job is updated by another user and cache invalidation fails.
* **Testing**: Run query profiler on `edit()` route; verify query count drops from ~25 to 0 on cached hits (and max 5 on cache misses).
* **Expected Result**: Edit page load time reduced from ~350ms to <30ms on cache hit; DB queries reduced by >80%.

---

### Phase 3 — Submit/Create Optimization

* **Problem**: `store()` contains severe N+1 query loops (qualifications, cities, skills, recruiters, hiring managers) and synchronous Elasticsearch HTTP calls inside `DB::beginTransaction()`.
* **Solution**: 
  - Refactor `store()` logic into `JobService::createJob(array $data, User $user)`.
  - Batch fetch all reference entities before inserting (e.g. `QualificationMaster::whereIn('name', $names)->pluck('id', 'name')`).
  - Use `DB::table(...)->insert($batchArray)` for bulk pivot table inserts.
  - Move Elasticsearch updates (`GeneralMethod::elasticJobsAddEditUpdateIndex`) out of the DB transaction into an asynchronous queued job `IndexJobToElasticsearch::dispatch($job->id)->onQueue('elasticsearch')`.
* **Risk**: Eventual consistency gap between MySQL write and Elasticsearch index availability (~500ms delay).
* **Testing**: Verify job creation speed under debugbar; confirm zero synchronous HTTP calls to ES inside MySQL transaction.
* **Expected Result**: `store()` transaction duration reduced from >400ms to <40ms; query count reduced from ~45 to <8.

---

### Phase 4 — Submit/Update Optimization

* **Problem**: `update()` performs full table scans via `LIKE '%cityName%'` inside loops, executes N+1 updates, and runs synchronous ES updates inside `DB::beginTransaction()`.
* **Solution**: 
  - Refactor `update()` logic into `JobService::updateJob(Job $job, array $data, User $user)`.
  - Replace `LIKE '%cityName%'` loop queries with exact ID-based batch lookups (`City::whereIn('id', $cityIds)`).
  - Perform bulk syncs for pivot tables (`job_qualifications`, `client_city_jobs`, `job_countries`, `job_assign`) using batch insert/delete operations.
  - Offload Elasticsearch index updates to background queue `IndexJobToElasticsearch::dispatch($job->id)->onQueue('elasticsearch')`.
* **Risk**: Temporary mismatch in Elasticsearch during bulk job updates.
* **Testing**: Test full job update workflow via Pest; confirm exact match on all updated fields without leading-wildcard SQL queries.
* **Expected Result**: Eliminates full table scans on `city_list`; update execution latency reduced by >85%.

---

### Phase 5 — Database Optimization

* **Problem**: Missing compound composite indexes on pivot tables (`client_city_jobs`, `job_qualifications`, `client_contact_info_jobs`, `job_assign`, `job_metronics_skills`, `job_countries`) leading to sub-optimal query execution.
* **Solution**: 
  - Add composite indexes:
    - `client_city_jobs`: `INDEX (job_id, city_id)`
    - `job_qualifications`: `INDEX (job_id, qualification_id)`
    - `client_contact_info_jobs`: `INDEX (job_id, client_contact_info_id)`
    - `job_assign`: `INDEX (job_id, user_id, company_id)`
    - `job_metronics_skills`: `INDEX (job_id, skill_name(50), company_id)`
    - `job_countries`: `INDEX (job_id, country_id)`
  - Optimize `jobs` table indexes: `INDEX (company_id, status, created_at)`.
* **Risk**: Index creation on large multi-million row production tables requires online DDL (`ALGORITHM=INPLACE, LOCK=NONE`) to prevent table locking.
* **Testing**: Execute `EXPLAIN` on all pivot queries to verify index usage (`USING INDEX`).
* **Expected Result**: Pivot query execution times reduced to sub-millisecond level.

---

### Phase 6 — AJAX/API Optimization

* **Problem**: Large payload size during form submission due to uncompressed serialization of HTML descriptions and redundant fields.
* **Solution**: 
  - Implement a clean JSON API endpoint structure for form submission (`POST /api/v2/service_company/jobs`).
  - Sanitize and trim HTML content on the client side before sending.
  - Return standardized JSON response payloads using `JobResource` with minimal required fields.
* **Risk**: Breaking legacy frontend code expecting legacy `Reply::redirect` payload format.
* **Testing**: API contract tests verifying exact JSON key schema and HTTP 201/200 status codes.
* **Expected Result**: Request payload size reduced by 40%; standardized error and success API responses.

---

### Phase 7 — Redis Optimization

* **Problem**: Inconsistent cache strategies between `create()`, `curstomcreate()`, and `edit()`; manual cache clearing strings (`alljobslist_version_user_{id}`) prone to missing keys.
* **Solution**: 
  - Adopt a unified Redis caching policy managed by `JobCacheKeys` builder.
  - Implement Redis Cache Tags (or structured key patterns `tenant:{company_id}:jobs:*`) for atomic invalidation.
  - Cache static master datasets (`qualification_master`, `countries`) with long TTLs (24h+), invalidated only via admin observers.
* **Risk**: Higher Redis memory consumption if TTLs are set too long without eviction policies.
* **Testing**: Cache hit/miss ratio monitoring via Redis `INFO stats`.
* **Expected Result**: Cache hit ratio >95% for job form static data; instant cache invalidation on edits.

---

### Phase 8 — Frontend Optimization

* **Problem**: `create.blade.php` is a single 3,454-line file with ~600 lines of embedded CSS, ~1,200 lines of inline JS, heavy DOM updates, and synchronous library loading.
* **Solution**: 
  - Modularize `create.blade.php` into clean partials:
    - `resources/views/service_company/jobs/partials/_form_basic.blade.php`
    - `resources/views/service_company/jobs/partials/_form_location.blade.php`
    - `resources/views/service_company/jobs/partials/_form_team.blade.php`
    - `resources/views/service_company/jobs/partials/_form_questions.blade.php`
  - Extract embedded CSS into dedicated stylesheet `public/css/jobs_v2.css`.
  - Extract inline JS into modular ES6 JS module `public/js/jobs_v2.js`.
* **Risk**: Asset caching issues if versioning query parameters are omitted.
* **Testing**: Cross-browser visual regression testing (Chrome, Firefox, Safari, Edge at 1366x768 -> 4K).
* **Expected Result**: Blade view file reduced from 3,454 lines to <300 lines; improved frontend render performance and maintainability.

---

### Phase 9 — Network & Retry Safety

* **Problem**: Submitting the form during intermittent network loss leads to failed requests with simple error toasts; user retries can cause duplicate job creation.
* **Solution**: 
  - Implement client-side UUID idempotency key generation (`X-Idempotency-Key` header) generated when form opens.
  - Add backend idempotency middleware `EnsureIdempotentSubmission` that caches request responses in Redis for 10 minutes by key.
  - Add automatic AJAX exponential backoff retry mechanism (up to 3 retries on status `0` or `503`).
* **Risk**: Idempotency key cached too briefly if user stays on page for hours before clicking publish.
* **Testing**: Simulate network failure via Chrome DevTools throttling / offline mode during form submit; verify retry behavior and single DB record creation.
* **Expected Result**: 100% protection against duplicate job creation and double coin deductions on retries.

---

### Phase 10 — Concurrency Optimization

* **Problem**: High concurrent job creation by 10,000+ recruiters can cause race conditions during coin deductions (`GeneralMethod::coinsDeductions`) and package job limit checks.
* **Solution**: 
  - Use Redis atomic locks (`Cache::lock('company_coins:' . $company_id, 5)`) around coin validation and deduction routines.
  - Use atomic SQL updates for coin balance updates (`UPDATE company_coins SET balance = balance - :coins WHERE company_id = :id AND balance >= :coins`).
  - Optimize Horizon queue worker concurrency for `emails` and `elasticsearch` queues.
* **Risk**: Lock acquisition timeouts under extreme concurrency if lock TTL is too short.
* **Testing**: Concurrent stress test simulating 100 simultaneous job creations for a single company using parallel curl processes.
* **Expected Result**: Zero race conditions, zero coin over-spending, clean transaction isolation under high load.

---

### Phase 11 — Load Testing

* **Problem**: Unknown performance bottlenecks under actual target scale (10,000+ concurrent recruiters).
* **Solution**: 
  - Create a Locust / k6 load testing script simulating:
    1. 7,000 recruiters loading Add/Edit Job pages (`GET`).
    2. 3,000 recruiters submitting/updating jobs (`POST`).
  - Benchmark metrics: p50, p95, p99 latency, CPU usage, Memory usage, MySQL pool saturation, Redis memory, Horizon queue lag.
* **Risk**: High CPU/network load on test infrastructure during benchmark execution.
* **Testing**: Run load test scenario for 30 minutes continuous duration.
* **Expected Result**: 
  - `GET` endpoints: p95 latency < 50ms.
  - `POST` endpoints: p95 latency < 150ms.
  - Zero 5xx HTTP errors; zero unhandled DB deadlocks.

---

### Phase 12 — V1 vs V2 Validation

* **Problem**: Potential functional regressions or subtle data mismatches introduced during V2 optimization.
* **Solution**: 
  - Perform side-by-side data output validation between V1 and V2:
    1. Compare MySQL database row state for identical test submissions.
    2. Compare Elasticsearch document structure generated by V1 vs V2.
    3. Compare outgoing email notification structure and payload.
  - Validate all Metronic UI UX standards (Poppins font, max 14px font, clean icon-based design, no 3D elements).
* **Risk**: Visual discrepancies between old and new asset structures.
* **Testing**: Automated end-to-end regression test suite running on staging environment.
* **Expected Result**: 100% functional, visual, and data parity between V1 and V2.

---

### Phase 13 — Production Rollout & Rollback

* **Problem**: Zero-downtime deployment requirements and instant rollback capability in case of unexpected production issues.
* **Solution**: 
  - Implement a feature flag `config('features.jobs_v2_enabled')` controlled via Redis/Environment.
  - Execution Plan:
    1. Deploy V2 code, database indexes, and background queue workers while feature flag is `false`.
    2. Enable feature flag for 5% of companies (canary deployment).
    3. Monitor error rates, Horizon queue lag, and APM metrics for 2 hours.
    4. Gradually ramp up feature flag to 25%, 50%, and 100%.
  - Rollback Plan: Immediate toggle of feature flag to `false` reverts all traffic back to V1 paths without deployment or DB rollback required.
* **Risk**: Stale cache entries if switching back and forth between V1 and V2.
* **Testing**: Perform live canary toggle test in staging environment before production release.
* **Expected Result**: Zero-downtime, risk-free production rollout with instant emergency rollback capability.

---

## 3. Summary Matrix & Milestone Roadmap

| Phase | Description | Focus Area | Target Latency / Metric | Risk Level |
|---|---|---|---|---|
| **Phase 0** | Baseline & Safety | Test Coverage | 100% Test Pass | Low |
| **Phase 1** | Add Job Optimization | Tenancy & Safety | Sub-50ms GET | Low |
| **Phase 2** | Edit Job Optimization | Caching & Query Reduction | Sub-30ms GET | Medium |
| **Phase 3** | Submit/Create Optimization | Async Jobs & Batching | Sub-100ms POST | High |
| **Phase 4** | Submit/Update Optimization | Index Scans & Offloading | Sub-120ms POST | High |
| **Phase 5** | Database Optimization | Composite Indexes | Sub-5ms Query | Medium |
| **Phase 6** | AJAX/API Optimization | Payload & API Contract | 40% Payload Reduction | Low |
| **Phase 7** | Redis Optimization | Unified Cache Architecture | >95% Hit Ratio | Medium |
| **Phase 8** | Frontend Optimization | Blade Partials & JS | <300 Lines / File | Low |
| **Phase 9** | Network & Retry Safety | Idempotency & Backoff | 0 Duplicate Records | Low |
| **Phase 10**| Concurrency Optimization | Atomic Locks | 0 Coin Over-spending | High |
| **Phase 11**| Load Testing | Locust / k6 Benchmarks | 10,000 Concurrent Users | Medium |
| **Phase 12**| V1 vs V2 Validation | Parity & UX Verification | 100% Feature Parity | Low |
| **Phase 13**| Production Rollout | Canary Feature Flag | Zero Downtime Rollout | Low |

---
