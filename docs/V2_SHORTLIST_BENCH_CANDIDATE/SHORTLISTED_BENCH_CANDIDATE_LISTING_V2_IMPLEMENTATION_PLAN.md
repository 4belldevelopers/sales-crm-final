# Shortlisted Bench Candidate Listing V2 — Implementation Plan

> **Location:** `docs/V2_SHORTLIST_BENCH_CANDIDATE/SHORTLISTED_BENCH_CANDIDATE_LISTING_V2_IMPLEMENTATION_PLAN.md`  
> **Target Route:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/offerListing`  
> **Status:** PLANNING ONLY (Do not implement until explicitly approved).

---

## 1. V2 Objective

Transform the **Shortlisted Bench Candidates Listing** into a zero-N+1, highly scalable, 10,000+ concurrent recruiter ready ATS module. V2 eliminates all in-loop MySQL and Elasticsearch queries, refactors Redis cache keying to company scope, denormalizes sorting fields in Elasticsearch, optimizes API payloads, and guarantees strict preservation of tenant isolation and business rules.

---

## 2. Proposed Architecture & Refactored Data Flow

```
[ Recruiter Browser / Mobile API ]
              │
              ▼
[ Route: service.php ]
  ├── GET  /service_company/bench-candidates/offerListing
  └── GET  /service_company/bench-candidates_ajax/offerListingData
              │
              ▼
[ ShortlistBenchCandidateListingService ]
  │
  ├── 1. Redis Cache Lookup (Company-Scoped Key)
  │      Key: ats:benchofferListing:{company_id}:{perm_hash}:{version}:{payload_hash}
  │      ├── HIT  ──> Return Cached JSON API Payload immediately
  │      └── MISS ──> Continue to Step 2
  │
  ├── 2. Query Elasticsearch (ELASTIC_CANDIDATES)
  │      - Filter: other_company, condidate_type, job_master_status_id, tab, location, search
  │      - Direct Field Sort: np_days (indexed), ctc, created_at (NO Painless scripts)
  │      - Returns 10 Candidate Documents
  │
  ├── 3. Single-Pass Batch Hydration Layer (Zero N+1)
  │      - Batch fetch status names from cached master array
  │      - Batch fetch job & client metadata using single ES mget or pre-indexed fields
  │      - Batch compute duplicate candidate flags in 1 single MySQL query
  │
  ├── 4. Transform & Cache JSON Response
  │      - Build clean JSON response payload
  │      - Store in Redis with company-level cache tag
  │
  ▼
[ Response: Lightweight JSON API ]
```

---

## 3. Detailed Recommendations (Problem → Solution → Risk → Testing)

### Recommendation 1: Eliminate Blade-Level Elasticsearch N+1 Calls (`jobDetails`)
- **Problem:** `GeneralMethod::jobDetails($row->jobId)` fires individual ES HTTP GET requests inside Blade `@foreach` for every row, generating 10 ES round-trips per page.
- **Solution:** Denormalize job metadata (`job_title`, `client_name`, `job_status`, `client_company_id`) directly into candidate ES documents during job/candidate sync events, OR batch-fetch all job IDs using `mget` in `ShortlistBenchCandidateListingService` prior to rendering.
- **Risk:** Minor synchronization delay if a job title changes before the candidate index is updated.
- **Testing:** Verify job title and client name match MySQL database records across all candidate rows in all tabs.

### Recommendation 2: Eliminate Blade-Level MySQL N+1 Calls (`companyDetailsByClient`, `bench_master_status`, `job_applications`)
- **Problem:** Blade template executes 3 separate MySQL queries per row to fetch status names, client details, and duplicate application counts.
- **Solution:** 
  1. Cache `bench_master_status` table in application memory (static array of 5 rows).
  2. Batch-fetch job/client details in 1 query for all job IDs on the page.
  3. Batch-check duplicate applications in 1 query: `WHERE company_id = ? AND (email IN (...) OR phone IN (...))` and map results in memory.
- **Risk:** Complex batch query must strictly adhere to `company.app_duplicate` (1 vs 2) and `company.app_duplicate_days` rules.
- **Testing:** Execute unit tests with varying combinations of `app_duplicate` (1 and 2) and day intervals to verify 100% duplicate flag accuracy.

### Recommendation 3: Replace Painless Script ES Sorting with Indexed Numeric Fields
- **Problem:** Notice period sorting (cases 5, 6, 21, 22) uses runtime Elasticsearch Painless scripts parsing string fields (`doc['np_id.keyword']`), consuming severe ES CPU.
- **Solution:** Add normalized numeric integer field `np_days` and date field `np_date` to `candidates` Elasticsearch index schema during write/indexing time. Execute sorting directly on indexed numeric/date fields.
- **Risk:** Requires updating candidate indexing listener (`BenchCandidateIndexJob`). Existing documents require an index mapping update.
- **Testing:** Compare sort results of `np_days` numeric sorting against Painless script sorting across all edge cases (Immediate joiners, NULL values, multi-values).

### Recommendation 4: Scope Redis Listing Cache to Company ID & Role
- **Problem:** Listing cache key includes `$user->id`, forcing every recruiter in the same company to store separate identical HTML/JSON cache fragments, wasting Redis RAM.
- **Solution:** Scope cache key by `$company_id` and permission hash: `ats:benchofferListing:{company_id}:{perm_hash}:{version}:{payload_hash}`.
- **Risk:** User-specific UI states (if any) must be passed as client parameters rather than baked into cached server responses.
- **Testing:** Log in as two separate recruiters from the same company; verify second recruiter hits Redis cache and receives identical correct data.

### Recommendation 5: Remove Unused Page-Load Database Query
- **Problem:** `offerListing()` method executes `BenchApplication::count()` on initial page load which is never rendered or used.
- **Solution:** Delete `BenchApplication::count()` line from `offerListing()`.
- **Risk:** None.
- **Testing:** Verify initial page load operates cleanly without errors and hero KPI cards populate correctly from ES aggregations.

### Recommendation 6: Transition to Clean JSON API Payload & Client Component Rendering
- **Problem:** AJAX endpoint returns full server-rendered HTML strings inside JSON (`responseData.html`), inflating payload size (25-45 KB) and increasing PHP CPU load.
- **Solution:** Refactor AJAX response to return clean JSON payload containing candidate array and pagination metadata (~3-5 KB), and let frontend JS render component rows.
- **Risk:** Requires updating frontend JavaScript render logic.
- **Testing:** Verify all table columns (Avatar, Candidate Name, Job, Job Status, Compensation, Offer Status, Joining Date, Master Status) render identically in the browser.

### Recommendation 7: Add Composite Index for Duplicate Application Lookups
- **Problem:** `job_applications` table lacks a targeted composite index for duplicate candidate checking queries.
- **Solution:** Document recommended composite index in database schema strategy:
  ```sql
  ALTER TABLE `job_applications` ADD INDEX `idx_job_app_dup` (`company_id`, `email`, `created_at`);
  ```
- **Risk:** Temporary write lock during index creation on large tables.
- **Testing:** Run `EXPLAIN` on duplicate count query to verify `idx_job_app_dup` index usage (`ref` / `range` access type).

---

## 4. Server Optimization Strategy (Ubuntu 24 / aaPanel)

1. **PHP 8.2 OPcache Configuration:**
   - `opcache.memory_consumption=512`
   - `opcache.max_accelerated_files=20000`
   - `opcache.validate_timestamps=0` (in production)
2. **PHP-FPM Pool Tuning (32 GB RAM Server):**
   - `pm = dynamic`
   - `pm.max_children = 100`
   - `pm.start_servers = 20`
   - `pm.min_spare_servers = 10`
   - `pm.max_spare_servers = 30`
3. **Elasticsearch JVM Heap Allocation:**
   - Allocate 12 GB RAM to ES JVM heap (`-Xms12g -Xmx12g`).
   - Enable request cache and node query cache (`indices.queries.cache.size: 10%`).
4. **Redis / DragonflyDB Memory Management:**
   - Configure `maxmemory 4gb` with `volatile-lru` eviction policy.

---

## 5. 10,000+ Concurrent Recruiters Scalability Strategy

- **Caching Layer (Redis):** Company-scoped cache keying guarantees a 95%+ cache hit ratio for recruiters within the same organization.
- **Zero In-Loop Database Queries:** 0 MySQL queries and 0 extra ES GET requests on cache misses during rendering.
- **Pure Index Sorting (ES):** Pure indexed term/range sorting without Painless script evaluation preserves ES CPU.
- **Lightweight Payload:** Response payload reduced by ~85% (from ~35 KB HTML to ~4 KB JSON), reducing network bandwidth and server serialization overhead.

---

## 6. Testing, Rollout & Rollback Plan

### Testing Strategy
- **Unit Testing:** Unit tests for `ShortlistBenchCandidateListingService` covering all tab filters and sorting rules.
- **Integration Testing:** Verify candidate status transition modal and job assignment drawer integration.
- **Load Testing:** Benchmark with k6 / JMeter simulating 1,000 to 10,000 concurrent recruiters requesting candidate listings.

### Rollout Strategy
- Canary deployment using feature flag `v2_shortlisted_bench_listing_enabled`.
- Enable for 5% of service companies, monitor error rates and latency, then expand to 100%.

### Rollback Strategy
- If issues arise, toggle feature flag `v2_shortlisted_bench_listing_enabled` to `false` to instantly route traffic back to V1 controller logic without downtime.

---

## 7. Business Logic Preservation Matrix

| Feature / Rule | V1 Logic Location | V2 Preservation Strategy |
|---|---|---|
| **Tenant Isolation** | `other_company = company_id` in ES query | Strict `term` filter in `ShortlistBenchCandidateListingService` |
| **Shortlist & Offer Status Filter** | `job_master_status_id IN [5, 6]`, `status != 3` | Mandatory ES query conditions |
| **Tab Status Filtering** | `pending`, `released`, `accepted`, `joining_soon`, `expired` | Identical ES `bool` query sub-clauses |
| **Confidential Compensation** | `GeneralMethod::confidentialAmontShow($row->jobId)` | Preserved in JSON transformer before payload generation |
| **Duplicate Candidate Check** | `app_duplicate` (1 vs 2) and `app_duplicate_days` | Preserved in single-pass batch MySQL duplicate query |
| **Hero KPI Summary Aggregation** | `benchOfferStats()` single ES aggregation call | Kept intact with company-level Redis caching |

---

## 8. Phase-Wise Implementation List (Table Format)

| Phase | Component | Tasks | Deliverables | Risk Level |
|---|---|---|---|---|
| **Phase 1** | Service Layer & Query Extraction | Create `ShortlistBenchCandidateListingService`, extract ES query construction & batch hydration out of God controller | Clean service class, isolated unit test suite | Low |
| **Phase 2** | N+1 Removal & Batch Hydration | Eliminate ES `jobDetails` & MySQL queries from Blade; batch pre-fetch status names, job details, and duplicate flags | Zero-query Blade/JSON template, 100% query reduction | Medium |
| **Phase 3** | ES Denormalization & Painless Removal | Index `np_days` and `np_date` in Elasticsearch `candidates` index; replace Painless sort scripts with direct field sorting | Fast ES sorting, reduced ES cluster CPU utilization | Medium |
| **Phase 4** | Redis & Cache Architecture | Refactor cache keys from `user_id` to `company_id` + permission hash; implement tag-based invalidation | 95%+ Redis cache hit ratio, reduced Redis memory footprint | Low |
| **Phase 5** | Frontend & API Payload | Optimize AJAX JSON response structure, refine frontend JS event listeners & drawer integrations | 85% smaller payload size, smoother UI response | Low |
| **Phase 6** | Verification & Load Testing | Run automated feature tests, benchmark with k6 for 10,000 concurrent recruiter simulation, document walkthrough | Verified load test report, rollout readiness approval | Low |
