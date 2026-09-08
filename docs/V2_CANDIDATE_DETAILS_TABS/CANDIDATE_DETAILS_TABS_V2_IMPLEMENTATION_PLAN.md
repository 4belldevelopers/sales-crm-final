# Candidate Details Tabs V2 Implementation Plan
**Target System:** JERO ATS / Multi-Tenant B2B Hiring SaaS (`JWMaincode28June2026`)  
**Target File Scope:** Candidate Details Screen (`resources/views/service_company/job-applications/showdetail.blade.php`), Controllers, APIs, Services, and JavaScript Engine  
**Target Concurrency:** 10,000+ Concurrently Active Recruiters  
**Author:** AI Engineering & Architecture Team  
**Date:** September 2, 2026  

---

## Executive Summary & Plan Overview

This implementation plan provides a structured, 14-phase roadmap (Phases 0 through 13) to modernize the Candidate Details drawer/screen and elevate its performance to handle **10,000+ concurrent recruiters**.

The architecture transitions from an **unpaginated monolithic pre-render pattern** to a **lightweight shell + lazy-loaded paginated API architecture**.

```
[Candidate Details Drawer Open]
  │
  ├─► Phase 1: Fast Shell Load (< 30 KB HTML, < 200ms DB fetch)
  │     └─ Renders Header, Status Badges & Tab Navigation Shell
  │
  ├─► User Clicks "Notes" Tab (Phase 2)
  │     └─► AJAX GET /api/v2/candidate/{id}/notes?page=1
  │
  ├─► User Clicks "Timeline" Tab (Phase 3)
  │     └─► AJAX GET /api/v2/candidate/{id}/timeline?page=1 (15 items/page)
  │
  └─► User Clicks "Resume / CV" Tab (Phase 4 & 5)
        └─► AJAX GET /api/v2/candidate/{id}/resume-meta
              └─► Lazy-initializes PDF.js / Mammoth renderer in viewport
```

---

## Phase Breakdown

### PHASE 0 — Baseline & Instrumentation

- **Objective:** Establish measurable performance baselines (latency, payload size, DB queries, memory) before any code refactoring.
- **Current Problem:** No APM telemetry on Candidate Details AJAX endpoint. Benchmark metrics under 10K concurrency are unknown.
- **Files/Modules Involved:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - Laravel OpenTelemetry / Telescope / APM logging middleware
- **Expected Code Changes:** Add execution duration metrics, payload size loggers, and database query count instrumentation to `ServiceCompanyJobApplicationController@show`.
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** Log `window.performance.timing` for candidate drawer open events.
- **Performance Benefit:** Precise data-driven target measurement.
- **Risk:** Low.
- **Dependencies:** None.
- **Testing:** Verify APM log metrics fire accurately on local/staging environments.
- **Rollback Strategy:** Revert logging middleware additions.
- **Acceptance Criteria:** APM records baseline latency (p50, p95, p99), query counts, and response payload sizes for 1,000 drawer opens.

---

### PHASE 1 — Request & Query Optimization

- **Objective:** Slim down initial Candidate Details drawer shell payload from ~450 KB to under 40 KB.
- **Current Problem:** Controller pre-queries 200 timeline items, 100 notes, and 7 multi-table eager map datasets on initial open.
- **Files/Modules Involved:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `resources/views/service_company/job-applications/showdetail.blade.php`
- **Expected Code Changes:** Remove timeline, notes, and offer pre-queries from `show()`. Return lightweight drawer HTML shell containing candidate summary header and empty tab containers (`#notes`, `#timeline`, `#resume_cv`).
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** Update drawer container HTML structure to hold empty placeholder skeletons for deferred tab content.
- **Performance Benefit:** Reduces initial drawer open latency by **75%** and response payload by **90%**.
- **Risk:** Medium (requires verifying header and profile summary fields remain intact).
- **Dependencies:** Phase 0.
- **Testing:** Verify initial drawer opens instantly and header displays correct candidate PII and status.
- **Rollback Strategy:** Revert `show()` controller method changes.
- **Acceptance Criteria:** Initial drawer response size < 40 KB; DB query count <= 3 queries.

---

### PHASE 2 — Notes Lazy Loading & Pagination

- **Objective:** Convert Notes tab from monolith render to lazy-loaded, paginated AJAX API.
- **Current Problem:** Pre-loads up to 100 notes from ES on initial drawer render; re-renders all notes on add/delete.
- **Files/Modules Involved:**
  - `app/Http/Controllers/ServiceCompany/ApplicantNoteController.php`
  - `resources/views/service_company/job-applications/showdetail.blade.php`
  - `routes/web.php`
- **Expected Code Changes:**
  - Create GET API `/service_company/applicant-note?application_id={id}&page=1`.
  - Fix `ApplicantNoteController@update` and `destroy` by removing `withoutGlobalScopes()` and adding strict `company_id` tenant scoping.
  - Return JSON array of notes with pagination metadata (`current_page`, `last_page`).
- **Database Changes:** Verify `INDEX idx_notes_app_user (job_application_id, user_id)` on `applicant_notes`.
- **Redis Changes:** Cache paginated note responses under `tenant:{companyId}:app:{appId}:notes:page:{page}`.
- **Elasticsearch Changes:** Query ES `ELASTIC_APPLICANT_NOTES` with `from` and `size` parameters (15 items/page).
- **Queue Changes:** None.
- **Frontend Changes:**
  - Add tab click listener on `#notes` tab to trigger AJAX fetch on first open.
  - Add "Load More Notes" or pagination scroll UI.
- **Performance Benefit:** Reduces initial render load for candidates with hundreds of notes to zero until tab click.
- **Risk:** Low.
- **Dependencies:** Phase 1.
- **Testing:** Verify note addition, editing, deletion, and pagination under single-tenant and multi-tenant test accounts.
- **Rollback Strategy:** Revert controller routes to inline partial rendering.
- **Acceptance Criteria:** Notes fetch latency < 300ms; zero cross-tenant note access permitted.

---

### PHASE 3 — Timeline Pagination & Query Optimization

- **Objective:** Replace monolithic 200-event timeline render with paginated timeline API.
- **Current Problem:** 200 ES timeline documents and 7 database eager maps are generated on drawer load.
- **Files/Modules Involved:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `resources/views/service_company/job-applications/showdetail.blade.php`
- **Expected Code Changes:**
  - Create GET API `/service_company/job-applications/{id}/timeline?page=1`.
  - Move bulk eager map queries (`companyOrClientMap`, `interviewFeedbackMap`, etc.) inside the timeline controller API method.
  - Return 15 items per page with cursor/page pagination.
- **Database Changes:** Verify indexes on `interview_feedback` and `job_assign`.
- **Redis Changes:** Cache paginated timeline results.
- **Elasticsearch Changes:** Update `ELASTIC_CANDIDATE_TIMELINE` search query to use `from: 0, size: 15`.
- **Queue Changes:** None.
- **Frontend Changes:** Add tab click listener on `#timeline` tab; append paginated timeline items dynamically on scroll.
- **Performance Benefit:** Eliminates 7 unnecessary DB map queries on drawer open; cuts timeline payload size by **92%**.
- **Risk:** Medium (requires ensuring all 12 timeline event types render identically to legacy layout).
- **Dependencies:** Phase 1.
- **Testing:** Verify all timeline event types (Notes, Email, Status changes, Interviews, Offers) render correctly.
- **Rollback Strategy:** Restore legacy timeline eager loading in `show()`.
- **Acceptance Criteria:** Timeline API response latency < 350ms; 15 items loaded per page.

---

### PHASE 4 — Resume / CV Lazy Loading

- **Objective:** Defer Resume storage validation, signed URL generation, and PDF.js setup until user clicks Resume tab.
- **Current Problem:** Resume URLs, DOCX conversion links, and storage file checks are evaluated synchronously on drawer load.
- **Files/Modules Involved:**
  - `ServiceCompanyJobApplicationController.php`
  - `showdetail.blade.php`
- **Expected Code Changes:**
  - Create GET API `/service_company/job-applications/{id}/resume-meta`.
  - Move R2/S3 storage existence check and signed URL generation into `resume-meta` endpoint.
- **Database Changes:** None.
- **Redis Changes:** Cache signed URLs for 15 minutes under `tenant:{companyId}:resume_meta:{appId}`.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** Defer PDF.js / Mammoth initialization script until `a[href="#resume_cv"]` is clicked.
- **Performance Benefit:** Prevents storage network checks (R2 HEAD requests) on initial drawer load.
- **Risk:** Low.
- **Dependencies:** Phase 1.
- **Testing:** Verify PDF and DOCX files preview smoothly when clicking Resume tab.
- **Rollback Strategy:** Re-enable inline resume variables in Blade template.
- **Acceptance Criteria:** Zero S3/R2 storage checks executed on initial drawer open.

---

### PHASE 5 — Resume Processing & Conversion Optimization

- **Objective:** Optimize DOCX-to-PDF conversion pipeline and download operations.
- **Current Problem:** Synchronous PDF conversion blocks PHP worker thread during document transformation.
- **Files/Modules Involved:**
  - `ServiceCompanyJobApplicationController@convertResumeToPdf`
  - LibreOffice / unoconv worker queue
- **Expected Code Changes:** Queue DOCX-to-PDF conversion jobs asynchronously. Cache converted PDF outputs on R2 storage (`Companies/{company}/Resumes/{year}/{month}/converted_{safeFilename}.pdf`).
- **Database Changes:** Track conversion status in `candidate_resume_uploads`.
- **Redis Changes:** Store conversion job status in Redis cache.
- **Elasticsearch Changes:** None.
- **Queue Changes:** Dispatch conversion to `conversions` queue worker pool.
- **Frontend Changes:** Display loading indicator ("Converting document to PDF...") while polling conversion status API.
- **Performance Benefit:** Offloads CPU-heavy PDF conversion from web server HTTP worker pool.
- **Risk:** Medium.
- **Dependencies:** Phase 4.
- **Testing:** Verify DOCX files convert asynchronously and cache output on R2.
- **Rollback Strategy:** Fall back to inline synchronous conversion.
- **Acceptance Criteria:** HTTP worker threads freed immediately; conversion handled by background worker.

---

### PHASE 6 — Frontend & JavaScript Optimization

- **Objective:** Eliminate global JS window variable collisions (`window.__rcvSeq`) and memory leaks.
- **Current Problem:** Document-level event listeners and global window tokens leak state across candidate drawers.
- **Files/Modules Involved:**
  - `resources/views/service_company/job-applications/showdetail.blade.php`
- **Expected Code Changes:**
  - Refactor inline JS IIFE into modular, scoped class (`CandidateDrawerManager`).
  - Bind event listeners directly to container element (`$drawer`) and unbind on drawer close.
  - Implement canvas viewport virtualization for PDF.js to release off-screen page canvases.
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** Replace document-level click delegation with container-scoped event binding.
- **Performance Benefit:** Prevents memory leaks and completely eliminates cross-candidate resume display collisions.
- **Risk:** Low.
- **Dependencies:** Phase 2, 3, 4.
- **Testing:** Open 50 candidate drawers in rapid sequence; verify RAM usage remains stable (< 300 MB) and no event listener bloat occurs.
- **Rollback Strategy:** Revert inline script refactoring.
- **Acceptance Criteria:** Zero global window variable collisions; RAM stays under 300 MB across 50 drawer views.

---

### PHASE 7 — Redis & Cache Optimization

- **Objective:** Implement tenant-isolated, deterministic Redis cache key schema with stampede protection.
- **Current Problem:** Legacy cache keys format (`candidateDetails_version_user_{userId}`) lack companyId and applicationId, causing cache poisoning risk.
- **Files/Modules Involved:**
  - `ServiceCompanyJobApplicationController.php`
  - `ApplicantNoteController.php`
- **Expected Code Changes:** Update all cache operations to use strict format: `tenant:{companyId}:user:{userId}:app:{applicationId}:{resource}`. Add atomic Redis lock (`Cache::lock()`) during cache misses to prevent cache stampedes under 10K concurrency.
- **Database Changes:** None.
- **Redis Changes:** Enforce strict key namespacing and TTL limits (15 minutes).
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** None.
- **Performance Benefit:** 100% cache hit safety with zero cross-tenant or cross-candidate cache leakage.
- **Risk:** Low.
- **Dependencies:** Phase 1, 2, 3.
- **Testing:** Verify cache keys in Redis CLI during concurrent requests for multiple candidates.
- **Rollback Strategy:** Flush Redis cache keys.
- **Acceptance Criteria:** Cache keys include `companyId` and `applicationId`; zero cross-tenant cache reads.

---

### PHASE 8 — Elasticsearch Optimization

- **Objective:** Optimize ES index queries and reduce source projection overhead.
- **Current Problem:** ES queries fetch full `_source` documents including large text blobs.
- **Files/Modules Involved:**
  - `ServiceCompanyJobApplicationController.php`
  - `app/Services/ElasticService.php`
- **Expected Code Changes:** Add `_source` projections to `ELASTIC_APPLICANT_NOTES` and `ELASTIC_CANDIDATE_TIMELINE` queries to return only required fields.
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** Add index mapping optimization for `application_id` and `job_id` keywords.
- **Queue Changes:** None.
- **Frontend Changes:** None.
- **Performance Benefit:** Reduces ES network payload and JSON parsing overhead by **50%**.
- **Risk:** Low.
- **Dependencies:** Phase 2, 3.
- **Testing:** Verify ES response payloads in debug logs.
- **Rollback Strategy:** Revert `_source` array filtering in ES parameters.
- **Acceptance Criteria:** ES query response duration < 50ms.

---

### PHASE 9 — N+1 Elimination & DB Index Optimization

- **Objective:** Ensure 100% index coverage for all SQL queries executed by Candidate Details.
- **Current Problem:** Unindexed lookup queries scan millions of rows under high database concurrency.
- **Files/Modules Involved:**
  - `database/schema/mysql-schema.sql`
- **Expected Code Changes:** Add missing composite indexes to database schema.
- **Database Changes:**
  ```sql
  ALTER TABLE `applicant_notes` ADD INDEX `idx_notes_app_user` (`job_application_id`, `user_id`);
  ALTER TABLE `job_assign` ADD INDEX `idx_job_assign_job_comp_user` (`job_id`, `company_id`, `user_id`, `spok_user_id`);
  ALTER TABLE `interview_feedback` ADD INDEX `idx_int_fb_interview_user` (`interview_id`, `update_by`);
  ALTER TABLE `interview_skills_feedback` ADD INDEX `idx_int_skills_fb_interview` (`interview_id`);
  ```
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** None.
- **Performance Benefit:** Guarantees O(1) or O(log N) index lookups for all queries.
- **Risk:** Low (online DDL index creation).
- **Dependencies:** Phase 1.
- **Testing:** Execute `EXPLAIN` on all SQL queries; verify `type = ref` or `eq_ref` with no full table scans (`type = ALL`).
- **Rollback Strategy:** Drop newly created indexes if needed.
- **Acceptance Criteria:** `EXPLAIN` analysis confirms 100% index coverage across all candidate queries.

---

### PHASE 10 — 10K+ Concurrency Optimization

- **Objective:** Harden system configuration for 10,000 active recruiters.
- **Current Problem:** Web server and PHP worker pool default limits will cause HTTP 504 Gateway Timeouts under burst load.
- **Files/Modules Involved:**
  - FrankenPHP / Octane configuration
  - Redis connection pool & MySQL max_connections
- **Expected Code Changes:** Tune Octane worker counts, HTTP response compression (Gzip/Brotli), and persistent DB connection pooling.
- **Database Changes:** Set `max_connections = 2000` with PgBouncer / ProxySQL if needed.
- **Redis Changes:** Configure maxmemory-policy `volatile-lru`.
- **Elasticsearch Changes:** Increase HTTP connection pool size.
- **Queue Changes:** Scale Horizon worker pool for conversions and notifications.
- **Frontend Changes:** Enable browser HTTP caching headers for static assets.
- **Performance Benefit:** System sustains 500+ req/sec throughput without worker saturation.
- **Risk:** Medium.
- **Dependencies:** Phases 1 through 9.
- **Testing:** Benchmark with k6 load generator.
- **Rollback Strategy:** Revert configuration parameters to default limits.
- **Acceptance Criteria:** Sustained 500 req/sec at p95 latency < 400ms.

---

### PHASE 11 — Load & Stress Testing

- **Objective:** Validate system performance under simulated 10,000 active recruiter load profile.
- **Current Problem:** Lack of empirical stress test data under burst load conditions.
- **Files/Modules Involved:**
  - k6 / Locust load testing scripts (`tests/load/candidate_drawer_stress.js`)
- **Expected Code Changes:** Add k6 stress test script targeting Candidate Details shell, Notes, Timeline, and Resume APIs.
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** None.
- **Performance Benefit:** Empirical proof of 10K concurrency readiness.
- **Risk:** None (executed in isolated staging environment).
- **Dependencies:** Phase 10.
- **Testing:** Run simulated ramp-up from 100 to 10,000 virtual users over 30 minutes.
- **Rollback Strategy:** N/A.
- **Acceptance Criteria:** Zero HTTP 5xx errors; p95 latency < 500ms; CPU usage < 75%.

---

### PHASE 12 — Production Rollout

- **Objective:** Deploy Candidate Details V2 architecture to production zero-downtime.
- **Current Problem:** Live recruiters actively using candidate drawers during deployment.
- **Files/Modules Involved:**
  - Production deployment pipeline
- **Expected Code Changes:** Deploy feature-flagged V2 Candidate Details drawer.
- **Database Changes:** Apply DB composite indexes during off-peak hours.
- **Redis Changes:** Flush legacy cache keys.
- **Elasticsearch Changes:** Ensure index aliases are aligned.
- **Queue Changes:** Restart Horizon workers.
- **Frontend Changes:** Serve updated JS assets.
- **Performance Benefit:** Smooth production transition with zero downtime.
- **Risk:** Medium.
- **Dependencies:** Phase 11.
- **Testing:** Blue/green canary rollout for 5% of companies before 100% rollout.
- **Rollback Strategy:** Toggle feature flag `config('resumes.features.v2_candidate_drawer') => false`.
- **Acceptance Criteria:** Successful canary rollout with zero error spikes in Sentry/APM.

---

### PHASE 13 — Monitoring & Rollback

- **Objective:** Continuous post-launch monitoring and automated alerts.
- **Current Problem:** Need real-time monitoring of drawer load latency, ES errors, and DB query times post-deployment.
- **Files/Modules Involved:**
  - Prometheus / Grafana / Datadog dashboards
- **Expected Code Changes:** Add custom OpenTelemetry metrics for drawer open duration, tab switch latencies, and conversion queue delays.
- **Database Changes:** None.
- **Redis Changes:** None.
- **Elasticsearch Changes:** None.
- **Queue Changes:** None.
- **Frontend Changes:** Monitor client-side JS error rates.
- **Performance Benefit:** Immediate notification and automatic rollback if latency thresholds are exceeded.
- **Risk:** Low.
- **Dependencies:** Phase 12.
- **Testing:** Trigger test alerts in staging.
- **Rollback Strategy:** Instant feature flag kill-switch.
- **Acceptance Criteria:** Real-time dashboards active; automated alerts configured for p95 > 800ms or HTTP 5xx > 0.1%.

---

## Plan Verification Matrix

| Phase | Core Objective | Key Deliverable | Primary Target Metric |
| :--- | :--- | :--- | :--- |
| **Phase 1** | Shell Optimization | Lightweight drawer shell | Response payload < 40 KB |
| **Phase 2** | Notes Lazy-Load | Paginated Notes API | Latency < 300ms |
| **Phase 3** | Timeline Lazy-Load | Paginated Timeline API | Latency < 350ms (15 items/page) |
| **Phase 4** | Resume Deferral | Defer S3/R2 check to click | Zero S3 checks on shell open |
| **Phase 6** | JS Scoping | Scoped instance manager | Zero cross-candidate CV collisions |
| **Phase 9** | DB Indexing | Composite SQL indexes | `EXPLAIN` 100% index match |
| **Phase 11**| Load Testing | k6 10K concurrency test | Sustained 500 req/sec @ p95 < 500ms |

**Status:** Plan Documented. Awaiting User Approval before initiating Phase 0.
