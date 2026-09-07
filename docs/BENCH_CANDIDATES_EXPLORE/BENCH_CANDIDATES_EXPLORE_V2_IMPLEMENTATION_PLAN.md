# BENCH CANDIDATES EXPLORE PAGE — V2 PHASE-WISE IMPLEMENTATION PLAN
## 10,000 CONCURRENT ACTIVE RECRUITERS OPTIMIZATION ROADMAP

> **Project:** JERO B2B Talent Marketplace Platform (`C:\xampp\htdocs\git\JWMaincode28June2026`)  
> **Target Page:** `/service_company/bench-candidates/bench_explore/{id}/{stage}`  
> **Plan Status:** DRAFT PLAN — PENDING USER APPROVAL (`APPROVED — IMPLEMENT`).  
> **Constraint:** NO CODE IMPLEMENTATION HAS BEEN OR WILL BE PERFORMED UNTIL EXPLICIT APPROVAL IS GIVEN.

---

## 1. Executive Implementation Strategy

The V2 Architecture for the Bench Candidates Explore page transitions the system from an unthrottled, multi-waterfall PHP-rendered pipeline to a **high-concurrency, single-payload API architecture** capable of handling **10,000 concurrent active recruiters** without database lockups, PHP worker pool starvation, or Elasticsearch thread rejection.

### Key Objectives
1. Eliminate the 3-step AJAX waterfall into a **single consolidated JSON endpoint**.
2. Resolve **P0 IDOR security risks** by validating candidate tenant ownership before rendering.
3. Fix all **MySQL N+1 query patterns** and add missing composite database indexes.
4. Enable **Redis response caching** with atomic stampede protection (lock/re-validate).
5. Implement client-side **`BenchExploreTabManager`** with 300ms search debounce, active request abort controls, and latest-request-wins guarantee.
6. Preserve **100% of existing candidate visibility, permissions, ranking, and business logic**.

---

## 2. Phase-Wise Implementation Roadmap (Phases 0 — 27)

### Phase 0 — Baseline & Environment Verification
- Capture baseline response times, memory consumption, and QPS using k6 / Locust load testing against `/service_company/bench-candidates/bench_explore/22/all`.
- Record baseline MySQL slow-query logs and Elasticsearch thread pool metrics.

### Phase 1 — Request Flow Consolidation
- Deprecate sequential dual-AJAX flow (`bench_explore_listing_job_ajax` followed by `bench_explore_listing_counter`).
- Design consolidated route `bench_explore_v2_data` returning candidate listing array + pipeline status counters in a single unified JSON payload.

### Phase 2 — Page Shell Optimization
- Strip heavy DB lookups (`benchHeroKpis`, `benchjobAndRecomCount`) out of the initial HTTP GET page request.
- Render lightweight Page Shell (< 15 KB) containing hero layout skeleton, filter controls, table header, and JS asset bindings.

### Phase 3 — True Lazy Loading Architecture (`BenchExploreTabManager`)
- Implement `BenchExploreTabManager` JS class in `resources/js/` or Blade footer script.
- Manage tab lifecycle: render shell -> trigger consolidated API call -> populate UI components asynchronously.
- Prevent loading inactive tabs or off-screen drawer components until user explicitly clicks them.

### Phase 4 — Candidate Query Refactoring & Selective Projection
- Refactor Elasticsearch `ELASTIC_CANDIDATES` search query to specify explicit `_source` projections:
  `['id', 'job_id', 'candidate_id', 'client_name', 'title', 'max_salary', 'min_salary', 'job_status', 'updated_at']`.
- Reduce payload size by 65%.

### Phase 5 — N+1 Query Elimination
- Eliminate in-loop `job_metronics_skills` SQL queries in legacy DataTables endpoint (`ServiceCompanyBenchCandidatesController:5436`).
- Pre-fetch or index job skills inside Elasticsearch nested documents (`job_metronics_skills.skill_name`).

### Phase 6 — Keyset & Deep Pagination Optimization
- Implement bounded page sizes (`per_page` capped between 10 and 50).
- Integrate Elasticsearch `search_after` / pit cursor pattern for deep page navigation beyond page 20 to eliminate `from` + `size` heap memory bloat.

### Phase 7 — Search Debounce & Request Cancellation
- Add 300ms debounce handler on input field `#kt_tagify_2`.
- Wrap AJAX requests in `AbortController` or `jqXHR.abort()` registry.
- Enforce "latest-request-wins" strategy using incremental request sequence IDs (`req_seq_id`).

### Phase 8 — Advanced Server-Side Filter Execution
- Verify status, location, salary range, company tags, and activity filters execute strictly at ES/MySQL layer.
- Ensure no post-fetch PHP array filtering occurs on large result sets.

### Phase 9 — Safe Multi-Tenant Redis Caching & Stampede Guard
- Re-enable Redis cache key checking in `bench_explore_listing_job_ajax` (`Controller:5025`).
- Scope key by tenant, user, stage, and parameters: `ats:bench_v2:{company_id}:{user_id}:{stage}:{param_hash}`.
- Implement Redis `atomic lock` with 5-second TTL to prevent Cache Stampedes when hot cache entries expire under 10K concurrency.

### Phase 10 — Elasticsearch Resilience & Fallback Engine
- Wrap Elasticsearch searches in try-catch blocks with 2.5s connection timeout.
- Implement automated fallback to optimized MySQL index query if ES cluster is unreachable.

### Phase 11 — Consolidated API Endpoint Standard
- Standardize response payload format:
  ```json
  {
    "status": "success",
    "meta": { "total": 142, "page": 1, "per_page": 10, "last_page": 15 },
    "counters": { "all": 142, "sourcing": 45, "screening": 30, "interview": 20, "offered": 10, "joined": 5, "rejected": 32 },
    "data": [ /* candidate records */ ]
  }
  ```

### Phase 12 — Blade & Client-Side DOM Optimization
- Replace server-side Blade HTML string rendering (`'html' => view(...)->render()`) with client-side JS DOM construction or Handlebars/Alpine lightweight template rendering.
- Reduce server CPU load by eliminating per-request Blade engine compilation.

### Phase 13 — Security & Data Isolation Enforcement (P0)
- Enforce strict tenant ownership check on main shell endpoint:
  ```php
  $this->bench_detail = JobApplication::where('company_id', $company_id)->findOrFail($this->bench_id);
  ```
- Reject unauthorized cross-tenant parameter manipulation with HTTP 403 Forbidden.

### Phase 14 — Database Composite Index Creation
- Prepare migration for missing indexes (to be executed ONLY after phase approval):
  - `ALTER TABLE rec_candidate_assign ADD INDEX idx_rca_source_from (source_from_id, source_application_id);`
  - `ALTER TABLE rec_candidate_assign ADD INDEX idx_rca_source_company (source_company_id);`
  - `ALTER TABLE job_metronics_skills ADD INDEX idx_jms_job_id (job_id, skill_name);`
  - `ALTER TABLE bench_count_view_download ADD INDEX idx_bcvd_cand_status (candidate_id, status, type);`

### Phase 15 — PHP Runtime & Octane Memory Optimization
- Audit garbage collection and memory detachment for long-running Octane/FrankenPHP workers.
- Avoid persistent static array accumulation across HTTP request cycles.

### Phase 16 — 1,000 Concurrent User Load Test Gate
- Execute k6 load test simulating 1,000 active recruiters.
- Threshold Target: p95 latency < 100ms, 0% errors.

### Phase 17 — 5,000 Concurrent User Load Test Gate
- Execute k6 load test simulating 5,000 active recruiters with mixed search/filter workloads.
- Threshold Target: p95 latency < 180ms, error rate < 0.01%.

### Phase 18 — 10,000 Concurrent User Load Test Gate
- Execute sustained 10,000 active recruiter load test (30-minute duration).
- Target: p50 < 45ms, p95 < 150ms, p99 < 300ms, Error Rate < 0.001%.

### Phase 19 — Hotspot & Cache Stampede Failure Testing
- Simulate sudden cache invalidation during 10K peak load.
- Verify Redis lock prevents database connection spike.

### Phase 20 — Disaster Recovery & Failover Testing
- Simulate total Elasticsearch node outage during load test.
- Verify MySQL fallback path maintains system availability without server crashes.

### Phase 21 — Observability & Telemetry Integration
- Add Prometheus / OpenTelemetry metrics for candidate explore latency, ES query durations, and cache hit ratios.

### Phase 22 — Full Functional Regression Testing
- Execute end-to-end testing suite verifying candidate stage progression, pipeline counters, filter accuracy, and drawer details.

### Phase 23 — Automated Performance Regression Gate
- Integrate automated load check into CI pipeline to prevent future code changes from introducing N+1 queries.

### Phase 24 — Staged Canary Rollout
- Deploy V2 architecture to 10% of recruiter traffic, monitoring error rates and response times.
- Incrementally increase rollout to 25%, 50%, and 100%.

### Phase 25 — Rollback & Emergency Bypass Safeguards
- Maintain feature flag `config('features.bench_explore_v2')`.
- Ensure instant rollback capability to legacy V1 path via config toggle.

### Phase 26 — Final 10K Concurrency Acceptance Sign-Off
- Formal sign-off upon meeting all latency, memory, security, and scalability metrics under 10K load.

### Phase 27 — Documentation & Developer Handover
- Complete final API specifications, architectural diagrams, and maintenance runbooks in `docs/BENCH_CANDIDATES_EXPLORE/`.

---

## 3. Comprehensive Testing Requirements

| Test Category | Methodology / Tools | Pass Criteria |
| :--- | :--- | :--- |
| **Functional Verification** | PHPUnit + Laravel Dusk | 100% test pass on candidate visibility, permissions, stage counts |
| **Security / IDOR Test** | Automated Security Suite | 403 Forbidden returned when accessing unauthorized tenant `bench_id` |
| **N+1 Regression Test** | Laravel Query Detector | Max 2 SQL queries executed per request path |
| **Search Debounce & Race Test** | Cypress / Playwright | Fast input typing fires max 1 request; stale responses ignored |
| **10K Concurrency Load** | k6 / Locust Distributed | 10,000 active virtual users, p95 < 150ms, 0% connection errors |
| **ES Failover Test** | Chaos Engineering (Stop ES) | Graceful MySQL fallback, notification logged, UI displays notification |
| **Redis Stampede Test** | Cache Purge during Load | DB connections remain bounded (< 150), zero connection pool drops |

---

## 4. Verification & Approval Gate

- **Implementation Plan Created:** September 7, 2026
- **Current Status:** AUDIT AND PLAN COMPLETE — WAITING FOR APPROVAL
- **Next Step:** NO CODE IS MODIFIED UNTIL THE USER PROVIDES THE EXPLICIT COMMAND:

```text
APPROVED — IMPLEMENT
```
