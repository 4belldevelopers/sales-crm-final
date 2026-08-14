# Job Apply Module V2 Architecture Implementation Plan

> **Document Status**: PROPOSED IMPLEMENTATION PLAN  
> **Target Module**: Job Apply (`/job-apply/{jobId}/{companyId}` and `/careerStore`)  
> **Target Scale**: 10,000+ Concurrent Recruiters / Candidates  
> **Date**: August 14, 2026  

---

## 1. Executive Overview & Plan Strategy

This implementation plan defines a comprehensive 14-phase engineering roadmap (Phases 0–13) to transform the **Job Apply Module** (`/job-apply/{jobId}/{companyId}` and `/careerStore`) into a high-performance, fault-tolerant, tenant-isolated, and scalable V2 engine capable of handling 10,000+ concurrent recruiters and candidates.

### Core Architectural Principles:
1. **Separation of Concerns**: Extract candidate application processing out of monolithic controllers (`HomeController.php`, `ServiceCompanyAppTrackerController.php`) into isolated services (`JobApplyService`, `JobApplyValidationService`, `JobApplyElasticService`).
2. **Asynchronous Out-of-Band Operations**: Remove all external network calls (Elasticsearch indexing, S3 photo uploads, R2 storage claims) from MySQL transactions into background Horizon queues.
3. **Database Bulk Operations**: Eliminate N+1 loops through single-query batch `INSERT` operations for skill ratings, answers, and additional application details.
4. **Multi-Tenant Isolation**: Enforce strict `$company_id` scoping across all queries, removing hardcoded company ID overrides.
5. **Zero-Trust Idempotency & Concurrency**: Implement database-level composite unique indexes and Redis idempotency locks to guarantee double-submit protection under high concurrency.

---

## 2. Phase-by-Phase V2 Implementation Plan

### Phase 0 — Baseline & Safety

* **Problem**: Absence of automated test coverage for candidate job application rendering, pre-flight validation, and submission makes refactoring risky and prone to regressions.
* **Solution**:
  - Write Pest/PHPUnit integration tests covering:
    1. `jobApply()` route access, coin package check, master data view variables, and company scoping.
    2. `careerStore()` submission flow with valid parameters, duplicate email handling, and error states.
    3. `ResumeUpload` session binding and R2 link creation.
  - Create a database query counter test helper to record exact baseline query counts.
* **Risk**: Initial test setup time; potential mock configuration requirements for Elasticsearch and R2 services.
* **Testing**: Run `./vendor/bin/phpunit --filter=JobApplyModuleTest` to verify 100% green status on V1 before touching any application logic.
* **Expected Result**: Automated test suite established; query counts and response latency baselines documented.

---

### Phase 1 — Add Client Optimization

* **Problem**: 
  - `HomeController@jobApply` hardcodes `$companyId = 2` for coin package validation, ignoring route parameter `$company_id`.
  - Executes `Job::activeJobs()` without tenant scoping, fetching all active jobs across the platform.
  - Loads entire `city_list` table (~100,000+ rows) into memory on every page load via `GeneralMethod::allCities()`.
* **Solution**:
  - Replace hardcoded `$companyId = 2` with route `$company_id` parameter.
  - Replace `Job::activeJobs()` with tenant-scoped query `Job::where('company_id', $company_id)->where('status', 'active')->select(...)`.
  - Cache reference master data (`qualification_master`, `interview_mode`, `city_list`) in Redis with tenant/versioned keys.
* **Risk**: Cache invalidation mismatch if new cities or qualifications are added.
* **Testing**: Run tenancy test: verify Company A candidate apply page cannot trigger coin checks or load jobs belonging to Company B.
* **Expected Result**: 100% multi-tenant isolation enforced; memory footprint per page load reduced by >85%.

---

### Phase 2 — Edit Client Optimization

* **Problem**: Recruiter candidate apply drawer in AppTracker (`career_apply_data_ajax`) executes 15 to 25 uncached queries per tab switch and recalculates salary mode conversions repeatedly.
* **Solution**:
  - Implement Redis caching for job apply filter data using `Cache::remember("tenant:{$company_id}:job_apply_defaults:{$jobId}", 300, ...)`.
  - Pre-calculate and cache currency and salary mode conversion mappings in memory.
  - Consolidate job skill collection queries using single collection `pluck()` operations.
* **Risk**: Stale job requirement defaults if job parameters are modified during active recruiter session.
* **Testing**: Run query profiler on `career_apply_data_ajax`; confirm query count drops from ~20 to <3 on cache hits.
* **Expected Result**: Recruiter drawer response latency reduced from ~300ms to <40ms; DB load reduced by >80%.

---

### Phase 3 — Submit/Create Optimization

* **Problem**: `careerStore()` contains N+1 query loops for skill experience inserts, application skills checks, answers, and additional details, generating 35–60 queries per submit.
* **Solution**:
  - Refactor submission logic into `JobApplyService::createApplication(array $data, int $companyId)`.
  - Replace N+1 loops with single-query batch inserts (`candidate_job_skill_exp`, `job_application_skills`, `job_application_answers`, `application_additional_detail`).
  - Move Elasticsearch skill experience indexing (`ELASTIC_CANDIDATE_SKILLS_EXP`) out of MySQL transaction into a queued job `IndexCandidateSkillsToElasticsearch::dispatch($expIds)`.
* **Risk**: Temporary index availability gap (~300ms) between MySQL insert and Elasticsearch search availability.
* **Testing**: Measure DB query count during application submission; verify query count drops from ~45 to <6.
* **Expected Result**: `careerStore()` transaction duration reduced from >600ms to <50ms; SQL query volume reduced by >85%.

---

### Phase 4 — Submit/Update Optimization

* **Problem**: Candidate application updates (`GeneralMethod::careerStore`) re-fetch 15-table JOIN queries, execute wildcard `LIKE` matching inside loops, and update Elasticsearch synchronously inside the DB transaction.
* **Solution**:
  - Extract `GeneralMethod::careerStore` update logic into `JobApplyElasticService::syncCandidateToElasticsearch($candidateId)`.
  - Offload Elasticsearch candidate document sync (`ELASTIC_CAREER_APPLY`) to a background queue `SyncCandidateToElasticsearch::dispatch($candidateId)->onQueue('elasticsearch')`.
  - Remove redundant 15-table JOIN query from the synchronous web request thread.
* **Risk**: Background queue lag under high load could delay candidate appearance in search by 1–2 seconds.
* **Testing**: Submit application; verify HTTP response completes immediately without waiting for ES HTTP endpoint.
* **Expected Result**: Web response time for application submit reduced by >70%; zero synchronous network calls inside MySQL transaction.

---

### Phase 5 — Database Optimization

* **Problem**:
  - `job_applications` lacks a unique index on `(company_id, job_id, email)`, allowing duplicate rows under race conditions.
  - SQL queries use wildcard `LIKE '%term%'` for skill matching, forcing full table scans on `job_application_skills`.
* **Solution**:
  - Add composite unique index `idx_ja_company_job_email` on `job_applications (company_id, job_id, email)`.
  - Add composite index `idx_ja_company_job_created` on `job_applications (company_id, job_id, created_at)`.
  - Replace `LIKE '%term%'` queries with exact normalized skill ID lookups.
* **Risk**: Duplicate historical rows must be cleaned or handled prior to unique index application.
* **Testing**: Attempt concurrent duplicate inserts via parallel HTTP requests; verify MySQL rejects second insert with a unique key exception handled gracefully.
* **Expected Result**: Zero duplicate candidate records; SQL index hit rate improved to 100%.

---

### Phase 6 — AJAX/API Optimization

* **Problem**: Front-end candidate form injects full city JSON arrays into HTML inline scripts, causing bloated DOM payload (>400KB).
* **Solution**:
  - Replace inline city JSON data with an asynchronous lightweight Select2/Tagify AJAX endpoint `/api/v2/cities/search?q={term}`.
  - Enable HTTP Gzip/Brotli compression and response caching for static drop-down master data endpoints.
* **Risk**: Additional AJAX request on search input focus.
* **Testing**: Inspect network tab on `/job-apply/{jobId}/{companyId}`; verify HTML page size drops from ~400KB to <45KB.
* **Expected Result**: Initial DOM load payload reduced by >85%; faster time-to-interactive (TTI) on mobile devices.

---

### Phase 7 — Redis Optimization

* **Problem**: Candidate submission duplicate check executes a blocking HTTP search request to Elasticsearch prior to transaction start.
* **Solution**:
  - Implement Redis atomic bloom filter or key check `redis->set("apply:dedup:{$companyId}:{$jobId}:{$emailHash}", 1, 'NX', 'EX', 300)`.
  - Store active job details and company brand settings in Redis hash tables (`tenant:{$companyId}:job:{$jobId}`).
* **Risk**: Redis memory usage increase under extreme traffic (mitigated by 300s TTL).
* **Testing**: Measure latency of duplicate application check; verify latency drops from ~80ms (ES HTTP call) to <1ms (Redis check).
* **Expected Result**: Sub-millisecond duplicate checks; zero Elasticsearch query load for duplicate submissions.

---

### Phase 8 — Frontend Optimization

* **Problem**: Monolithic `career-apply.blade.php` (~3,324 lines) combines custom CSS, inline script logic, third-party libraries (Mammoth.js, PDF.js, Tagify), and inline submit handlers without modularization.
* **Solution**:
  - Extract inline JS into modular JS files (`job-apply-form.js`, `resume-uploader.js`, `skill-rating.js`).
  - Implement visual loading spinners and disable submit button immediately upon form click to prevent double clicks.
* **Risk**: Require testing JS bundle loading order across browsers.
* **Testing**: Test form submission in Chrome/Firefox/Safari; verify double-click on submit button triggers only 1 POST request.
* **Expected Result**: Zero client-side double submits; clean, maintainable frontend architecture.

---

### Phase 9 — Network & Retry Safety

* **Problem**: Public `POST /careerStore` endpoint has no rate limiting or idempotency key validation.
* **Solution**:
  - Attach `throttle:30,1` middleware to `/careerStore` route.
  - Implement client-generated Idempotency-Key header processing in `JobApplyService`.
  - Implement exponential backoff retry logic for resume file uploads.
* **Risk**: Legitimate high-frequency users sharing public NAT IPs could hit throttle limits (mitigated by 30 req/min setting).
* **Testing**: Send 35 rapid requests to `/careerStore`; verify 31st request receives HTTP 429 Too Many Requests.
* **Expected Result**: Complete protection against automated bot spam and denial-of-service attempts.

---

### Phase 10 — Concurrency Optimization

* **Problem**: Under 10,000+ concurrent recruiters/candidates, database transaction lock contention and connection pool exhaustion occur due to long-running synchronous work inside `DB::beginTransaction()`.
* **Solution**:
  - Minimize transaction window duration: perform validation, geocoding, and data preparation *before* calling `DB::beginTransaction()`.
  - Limit transaction strictly to MySQL `INSERT` statements (`job_applications` + pivot batch inserts).
  - Offload S3 photo uploads and R2 resume session claims to post-commit queue listeners.
* **Risk**: Post-commit job failures must be monitored via Laravel Horizon retry mechanisms.
* **Testing**: Run concurrent thread simulator; verify MySQL transaction lock hold time remains under 10ms.
* **Expected Result**: Transaction duration < 10ms; zero DB connection pool starvation under peak load.

---

### Phase 11 — Load Testing

* **Problem**: Need empirical validation that V2 architecture sustains 10,000+ concurrent recruiters/candidates without degradation.
* **Solution**:
  - Run K6 / Locust load tests simulating:
    - 5,000 concurrent candidate page loads (`GET /job-apply/{jobId}/{companyId}`).
    - 2,000 concurrent application submissions (`POST /careerStore`).
    - 3,000 concurrent recruiter drawer AJAX requests (`career_apply_data_ajax`).
* **Risk**: High CPU/Memory consumption on testing environment during peak load.
* **Testing**: Execute load test scenarios; monitor APM (P99 response time, DB CPU usage, Redis memory, Horizon queue backlog).
* **Expected Result**: P99 response time < 100ms; error rate = 0.00%; CPU utilization < 60%.

---

### Phase 12 — V1 vs V2 Validation

* **Problem**: Must guarantee 100% functional equivalence and zero business logic regression between V1 and V2.
* **Solution**:
  - Run side-by-side verification tests comparing V1 and V2 outputs for identical inputs:
    - Check database row state in `job_applications`, `candidate_job_skill_exp`, `job_application_skills`.
    - Compare Elasticsearch document structure in `ELASTIC_CAREER_APPLY`.
    - Verify R2 resume file linking and audit log records.
* **Risk**: Data field omission due to missing refactored key.
* **Testing**: Execute parity suite `./vendor/bin/phpunit --filter=V1V2JobApplyParityTest`.
* **Expected Result**: 100% data fidelity match between V1 and V2.

---

### Phase 13 — Production Rollout & Rollback

* **Problem**: Production deployment of V2 job apply engine must occur with zero downtime and instant rollback capability.
* **Solution**:
  - Deploy V2 codebase behind a Feature Flag (`config('features.job_apply_v2')`).
  - Perform phased rollout: 5% -> 25% -> 50% -> 100% traffic allocation.
  - Maintain one-click fallback toggle to instantly revert traffic to V1 if error rate exceeds 0.1%.
* **Risk**: Cache inconsistency during partial traffic split.
* **Testing**: Toggle feature flag in staging environment; verify seamless switching between V1 and V2 controllers.
* **Expected Result**: Zero downtime deployment with fail-safe operational control.

---

## 3. Phase Summary & Risk Mitigation Matrix

| Phase | Core Objective | Primary Risk | Expected Latency / Capacity |
| :--- | :--- | :--- | :--- |
| **Phase 0** | Baseline & Safety | Test setup overhead | Safety net established |
| **Phase 1** | Add Client Optimization | Stale master data cache | Page load < 60ms |
| **Phase 2** | Edit Client Optimization | Drawer cache sync | Recruiter drawer < 40ms |
| **Phase 3** | Submit/Create Optimization | Async queue delay | Submit DB Tx < 50ms |
| **Phase 4** | Submit/Update Optimization | ES eventual consistency | Submit response < 100ms |
| **Phase 5** | Database Optimization | Historical duplicate cleanup | 100% Index hit rate |
| **Phase 6** | AJAX/API Optimization | Input focus network call | Page payload < 45KB |
| **Phase 7** | Redis Optimization | Redis memory consumption | Dedup check < 1ms |
| **Phase 8** | Frontend Optimization | Bundle load order | 0 Double submits |
| **Phase 9** | Network & Retry Safety | Rate limit false positives | 100% Bot protection |
| **Phase 10**| Concurrency Optimization | Horizon job retries | DB Tx hold time < 10ms |
| **Phase 11**| Load Testing | Server load during test | 10,000+ Req/sec capacity |
| **Phase 12**| V1 vs V2 Validation | Missing field mapping | 100% Data fidelity |
| **Phase 13**| Rollout & Rollback | Cache split during rollout | Instant 1-click fallback |

---

> **STOP**: Implementation plan completed. Do not execute code modifications. Awaiting user approval before proceeding to V2 implementation.
