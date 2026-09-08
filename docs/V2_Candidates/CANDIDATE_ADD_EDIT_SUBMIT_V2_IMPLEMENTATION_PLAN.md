# Candidate Module V2 Implementation Plan (Add, Edit, Submit Flows)

> **Document Status**: COMPLETED & VERIFIED IMPLEMENTATION (Phases 0 - 13 Complete)  
> **Target Module**: Candidate Management & AppTracker (`service_company/apptracker`, `service_company/job-applications`)  
> **Target Benchmark**: 10,000+ Concurrent Recruiters, Sub-200ms Page Loads, Sub-300ms Submissions  
> **Date**: August 14, 2026  

---

## 1. Overview & Strategy

This document outlines the phased V2 optimization plan for the Candidate Module across **Add Candidate**, **Edit Candidate**, and **Save/Submit** flows. The primary objectives are:

1. Offload synchronous Elasticsearch network operations out of MySQL transactions in `update()`.
2. Eliminate uncached master data queries and duplicate database calls during form load (`create()`, `clientcreate()`, `edit()`).
3. Optimize Tagify location lookups and candidate duplicate detection.
4. Modularize frontend Blade templates to reduce DOM footprint and improve render speeds.
5. Guarantee 100% data consistency between MySQL InnoDB, Elasticsearch, Redis, and Cloudflare R2 under high concurrent recruiter load.

---

## 2. Phase-Wise Implementation Plan

---

### Phase 0 — Baseline & Safety

* **Problem**: Lack of standardized automated performance benchmarks and automated regression tests for candidate add/edit/submit workflows before introducing V2 optimizations.
* **Solution**:
  - Establish automated PHPUnit integration tests covering candidate creation (`store()`), client portal submission (`clientappstore()`), and candidate update (`update()`).
  - Record baseline performance metrics (query counts, memory footprint, HTTP response time, Redis cache hits/misses).
* **Risk**: Low. Test scripts are non-disruptive.
* **Testing**: Run `./vendor/bin/pint --test` and `php artisan test --filter=CandidateSubmissionTest`.
* **Expected Result**: Clean test harness and verified baseline latency metrics prior to V2 code changes.

---

### Phase 1 — Add Client Optimization (Recruiter & Client Add Form Load)

* **Problem**: Opening the "Add Candidate" modal (`job-applications/create` and `clientcreate`) executes 15+ uncached queries fetching static reference data (`qualification_master`, `interview_mode`, `notice_periods`).
* **Solution**:
  - Cache static master data via `ReferenceDataCache` with a 24-hour TTL.
  - Optimize job selection query (`Job::activeJobs()`) with eager-loaded relations and lean select lists (`id`, `title`, `client_id`).
* **Risk**: Low. Static reference data updates infrequently.
* **Testing**: Verify Add Candidate modal opens cleanly; check Query Monitor / Debugbar to confirm query count drops from 15+ to 2 queries.
* **Expected Result**: Page load query count reduced by ~85%; modal render latency under 150ms.

---

### Phase 2 — Edit Client Optimization (Candidate Edit Form Load)

* **Problem**: Candidate edit form load (`job-applications/{id}/edit`) executes 20-25 SQL queries per request, including 3 duplicate reads of `application_additional_detail` and uncached reads of static master tables.
* **Solution**:
  - Remove duplicate database calls for `application_additional_detail` and consolidate into a single eager-loaded dataset.
  - Wrap master reference lookups in `ReferenceDataCache`.
  - Enforce tenant-scoped queries (`company_id`) across all raw DB queries.
* **Risk**: Low. Read-only optimization with strict tenant safety checks.
* **Testing**: Load edit modal for candidates with diverse skills/answers; confirm data accuracy and count DB queries.
* **Expected Result**: DB query count reduced from 22-25 to 4-5 queries per edit form request.

---

### Phase 3 — Submit/Create Optimization (`store` & `clientappstore`)

* **Problem**: While `store()` already dispatches `ProcessCandidateSubmissionJob` post-commit, initial candidate creation still performs synchronous primary ES indexing (`GeneralMethod::JobApplicationsAddEditUpload()`) before responding.
* **Solution**:
  - Keep primary candidate ES indexing light and fast.
  - Verify all post-insert tasks (skills indexing, team rank ES updates, skill match calculations, answer recording, candidate notifications) remain fully decoupled in async queued jobs (`ProcessCandidateSubmissionJob`, `QueueCandidateNotificationJob`).
* **Risk**: Low. Preserves instantaneous grid visibility while maintaining low HTTP response times.
* **Testing**: Submit new candidates via recruiter ATS and client portal; verify async job execution on `emails` queue worker.
* **Expected Result**: Submit/Create HTTP response latency under 250ms under standard load.

---

### Phase 4 — Submit/Update Optimization (`update`)

* **Problem**: `ServiceCompanyJobApplicationController@update()` performs synchronous `deleteByQuery` and doc-by-doc HTTP calls to Elasticsearch for `ELASTIC_CANDIDATE_SKILLS_EXP` and `ELASTIC_CANDIDATE_JOB_QUESTION` inside the `DB::beginTransaction()` block.
* **Solution**:
  - Extract all Elasticsearch deletions and updates from `update()` into a dedicated asynchronous job (`ProcessCandidateUpdateJob`).
  - Execute MySQL updates cleanly inside the transaction and commit immediately.
  - Dispatch `ProcessCandidateUpdateJob` post-commit on the `emails` queue.
* **Risk**: Medium. Requires ensuring async job handles potential ES retries idempotently.
* **Testing**: Edit existing candidate details, skills, and additional questions; verify immediate MySQL update and eventual consistency in ES within 500ms.
* **Expected Result**: Transaction hold time reduced from 500ms-2000ms down to <30ms; HTTP response time under 200ms.

---

### Phase 5 — Database Optimization

* **Problem**: Tagify work location lookup (`kt_tagify_work_location`) executes N+1 3-table joins (`city_list` + `state_list` + `countries`) inside loops. Candidate duplicate checks rely on unindexed `orWhere('phone', ...)` queries. Pivot tables on updates use delete-and-reinsert patterns.
* **Solution**:
  - Create composite database index on `job_applications (company_id, email, phone)` for instantaneous duplicate candidate checks.
  - Add composite index on `city_list (city, state_id)` to speed up location parsing.
  - Replace delete-and-reinsert logic on `candidate_job_skill_exp` with upsert (`upsert()` / `updateOrCreate()`) to avoid primary key gaps.
* **Risk**: Low. Indexes improve read performance without altering business logic.
* **Testing**: Run `EXPLAIN` on duplicate candidate queries and Tagify city lookup queries to verify index usage (`USING INDEX`).
* **Expected Result**: Location parsing query latency reduced by 90%; duplicate candidate check latency <5ms.

---

### Phase 6 — AJAX/API Optimization

* **Problem**: Candidate form submissions submit large uncompressed FormData payloads containing full JSON tag arrays, raw HTML strings, and unvalidated structure.
* **Solution**:
  - Create dedicated Laravel FormRequest classes (`StoreCandidateRequest`, `UpdateCandidateRequest`) with explicit field validation rules.
  - Standardize JSON response structure using `Reply::success()` and `Reply::dataOnly()`.
* **Risk**: Low. Enhances request validation and type safety.
* **Testing**: Send malformed or invalid inputs via Postman/AJAX; verify clean 422 JSON validation responses.
* **Expected Result**: 100% type-safe candidate input processing and structured API responses.

---

### Phase 7 — Elasticsearch & Redis Optimization

* **Problem**: Potential ES query conflict errors (`conflicts => proceed`) during high-frequency candidate updates and missing Redis cache invalidation strategies for tenant candidate lists.
* **Solution**:
  - Implement Redis tag-based invalidation (`Cache::tags(['tenant:' . $companyId, 'candidates'])`).
  - Standardize ES indexing operations with exponential backoff retries in queue workers.
* **Risk**: Medium. Requires testing Redis tag support across environments.
* **Testing**: Bulk update candidates simultaneously; verify Redis cache invalidation and ES index consistency.
* **Expected Result**: Zero search index drift and 99.99% Redis cache hit ratio for pipeline views.

---

### Phase 8 — Frontend Optimization

* **Problem**: Large Blade views (`create.blade.php` - 183KB, `clientcreate.blade.php` - 151KB, `showdetail.blade.php` - 432KB) overload DOM rendering and increase PHP memory footprint per request.
* **Solution**:
  - Refactor monolithic Blade files into modular Blade components (`x-candidate.form-basic`, `x-candidate.form-skills`, `x-candidate.form-questions`).
  - Defer rendering of non-active modal tabs until clicked by the user.
* **Risk**: Low. Purely structural UI refactoring matching Metronic standards.
* **Testing**: Inspect frontend DOM tree size and measure initial modal render time in browser DevTools.
* **Expected Result**: Blade file size reduced by 60%; DOM node count reduced by 50%; frontend modal open speed <100ms.

---

### Phase 9 — Network & Retry Safety

* **Problem**: Cloudflare R2 / AWS S3 storage pre-flight checks (`Storage::disk('r2')->exists()`) can throw unhandled network exceptions or hang during cloud provider outages.
* **Solution**:
  - Wrap external storage checks in try-catch blocks with short socket timeouts (2 seconds).
  - Provide graceful user feedback ("Storage service temporarily busy, please retry") without throwing 500 errors.
* **Risk**: Low. Improves application resilience against third-party network failures.
* **Testing**: Simulate network timeout on S3/R2 disk; confirm application catches error cleanly and logs warning.
* **Expected Result**: Zero uncaught 500 internal server errors caused by third-party storage timeouts.

---

### Phase 10 — Concurrency Optimization (10,000+ Concurrent Recruiters)

* **Problem**: High concurrent submissions can cause MySQL lock contention and thundering herd effects on pipeline counters.
* **Solution**:
  - Maintain `Cache::flexible` stale-while-revalidate pattern with single-flight `Cache::lock` on `loadcounter()`.
  - Use short, atomic DB transactions strictly wrapping MySQL writes.
* **Risk**: Medium. Requires validation under high concurrent load simulation.
* **Testing**: Run concurrent API requests using Apache Bench / k6.
* **Expected Result**: System maintains stable memory and sub-300ms response times at 10,000 concurrent active recruiter sessions.

---

### Phase 11 — Load Testing

* **Problem**: Lack of empirical throughput validation under peak enterprise load scenarios.
* **Solution**:
  - Execute automated k6 load testing scripts simulating 1,000 to 10,000 concurrent recruiters adding, editing, and viewing candidates.
  - Monitor MySQL CPU, Redis memory, Horizon queue lag, and FrankenPHP request rates.
* **Risk**: Low. Conducted on dedicated staging environment.
* **Testing**: `k6 run loadtest_candidates.js --vus 10000 --duration 5m`.
* **Expected Result**: 99th percentile response time <300ms; zero transaction deadlocks; zero failed HTTP requests.

---

### Phase 12 — V1 vs V2 Validation

* **Problem**: Ensuring V2 candidate optimizations cause zero functional regressions compared to V1.
* **Solution**:
  - Run side-by-side verification comparing V1 and V2 candidate outputs across database records, Elasticsearch documents, Cloudflare R2 file attachments, and email notifications.
* **Risk**: Low. Read-only verification phase.
* **Testing**: Verify end-to-end candidate lifecycle (Add → Edit → Move Stage → Hire) matches V1 behavior exactly.
* **Expected Result**: 100% functional parity with V1 business logic and zero missing candidate data.

---

### Phase 13 — Production Rollout & Rollback

* **Problem**: Deployment risks during live production migration.
* **Solution**:
  - Implement feature flag `config('app.candidates_v2_enabled')`.
  - Deploy V2 code with feature flag disabled by default. Enable progressively per company tenant.
  - Automated instant rollback mechanism (flip feature flag to `false` if error rate exceeds 0.1%).
* **Risk**: Low. Gated canary rollout.
* **Testing**: Canary deployment on internal test company account prior to full platform enable.
* **Expected Result**: Zero downtime deployment with instant rollback capability.
