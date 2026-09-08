# Talent Placed Listing V2 — Implementation Plan

> **Document Type:** Architectural Optimization & V2 Implementation Plan  
> **Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/placedListing`  
> **Status:** Proposal & Plan Only. Pending User Approval before implementation.  

---

## 1. V2 Objective & Goals

The primary goal of **Talent Placed Listing V2** is to transform the existing page into an enterprise-grade, zero-N+1 listing engine capable of serving **10,000+ concurrent recruiters** with sub-100ms response times.

### Key Objectives
1. **100% N+1 Query Removal:** Eliminate all inline SQL queries and synchronous Elasticsearch HTTP requests (`GeneralMethod::jobDetails()`) from the rendering loop.
2. **Denormalized Elasticsearch Indexing:** Expand `ELASTIC_CANDIDATES` mappings to include job metadata, recruiter names, and placement timeline dates directly within the document `_source`.
3. **Pure JSON API Architecture:** Replace HTML string response caching with lightweight JSON API responses and client-side rendering.
4. **Native Index-Based Sorting:** Replace expensive ES Painless scripts with pre-computed indexed fields (`notice_period_days`, `notice_period_epoch`).
5. **Dual Persistence & Event-Driven Sync:** Ensure drawer updates (`savePlacedAssignmentDetails` and `savePlacedCommercialConfig`) reliably update MySQL primary tables and sync to Elasticsearch asynchronously via Horizon queues.

---

## 2. Business Logic & Data Preservation Rules

During the implementation of V2, the following rules MUST remain 100% unchanged:

1. **Multi-Tenant Authorization & Boundaries:**
   - logged-in user's `company_id` isolation MUST be strictly enforced across all DB and ES queries.
   - Preserved `Gate::denies('view_job_applications')` and `in_array('view_bench', $userPermissions)` authorization gates.
2. **Elasticsearch Placement Visibility Scoping:**
   - Must preserve all 4 original `should` clauses matching placed bench candidates, direct client candidates, and third-party candidates (`job_master_status_id = 6`).
3. **Salary Confidentiality Logic:**
   - `GeneralMethod::confidentialAmontShow($jobId)` checks MUST be preserved prior to exposing offer amounts.
4. **Placement Journey Nodes:**
   - Timeline dates (Bench / Sourcing → Evaluation → Offer → Joined → Placed) must be preserved.

---

## 3. Comprehensive Recommendations (Problem → Solution → Risk → Testing)

### 1. Job Metadata & Recruiter Denormalization into ES
- **Problem:** Blade template executes `GeneralMethod::jobDetails($row->jobId)` per candidate row, firing up to 50 synchronous HTTP GET calls to Elasticsearch on a single request.
- **Solution:** Denormalize job title, job location (city, state), client name, spokesperson name, and confidential status directly into the `ELASTIC_CANDIDATES` index during candidate ingestion/update events.
- **Risk:** Slight increase in Elasticsearch index disk footprint.
- **Testing:** Verify job details appear accurately on the candidate listing without triggering external HTTP requests.

### 2. Elimination of Blade Inline SQL Queries
- **Problem:** Blade partial `bench-place-candidate-ajax.blade.php` executes 5 separate SQL queries per row (`job_applications`, `offer_job_applications`, `offer_create_main_tds`, `updatetimeline`, `job_assign`).
- **Solution:** Eagerly load all required relationship data in the controller using batch queries (`whereIn('id', $candidateIds)`), or read pre-indexed timeline fields from Elasticsearch.
- **Risk:** Misconfiguration of batch queries could omit dates for edge-case candidate records.
- **Testing:** Audit query logs using Laravel Telescope/Debugbar to verify 0 SQL queries execute during Blade view rendering.

### 3. Removal of Global Unindexed MySQL Count
- **Problem:** `placedListing()` executes `BenchApplication::count()` on initial view load, running an unindexed full table count across all tenants.
- **Solution:** Replace `BenchApplication::count()` with a tenant-scoped count or read total placed candidate count from the Elasticsearch total hit result.
- **Risk:** Minor discrepancy if MySQL count is shown before AJAX load completes.
- **Testing:** Verify total placed candidate counter matches ES total count and executes in < 5ms.

### 4. Native Index Sorting for Notice Periods (Replacing Painless Scripts)
- **Problem:** Sorting by Notice Period (cases 5, 6, 21, 22) executes CPU-intensive inline Painless scripts in Elasticsearch for every request.
- **Solution:** Add integer fields `notice_period_days` (e.g., 0 for Immediate, 30, 60, 90) and `notice_period_epoch` to the ES index schema, and sort directly on these numeric fields.
- **Risk:** Requires re-indexing candidates to populate new numeric sort fields.
- **Testing:** Execute benchmark tests comparing Painless sort query vs native field sort query.

### 5. Transition to Lightweight JSON API & Client-Side JS Template Rendering
- **Problem:** Server renders full HTML strings for table rows and stores large HTML fragments in DragonflyDB cache, consuming excess memory.
- **Solution:** Return raw JSON candidate objects and render rows on the client-side using JavaScript templates (or Alpine.js/Vue.js). Store raw JSON arrays in DragonflyDB cache.
- **Risk:** Frontend JS refactoring required for datatable row rendering.
- **Testing:** Verify API response size drops by >80% and table renders seamlessly across browsers.

### 6. Real Elasticsearch Overview Stats Aggregation API
- **Problem:** Overview stat cards ("Currently Working", "Projects Completed", "Replacement Active") are hardcoded static numbers.
- **Solution:** Create an ES Terms & Range Aggregation endpoint to return dynamic live counts for active assignments and upcoming joiners.
- **Risk:** Complex aggregation queries could slow down top bar rendering if uncached.
- **Testing:** Cache stat aggregation responses in Redis for 5 minutes and verify values update accurately.

### 7. Dual Persistence Architecture for Drawers
- **Problem:** `savePlacedAssignmentDetails` and `savePlacedCommercialConfig` perform direct ES updates without saving data into primary MySQL source tables (`add_job_wise_condidates` / `bench_place`).
- **Solution:** Update MySQL source tables first inside a DB transaction, then dispatch an asynchronous job (`SyncPlacedCandidateToElasticJob`) to update Elasticsearch.
- **Risk:** Async job delay could cause slight lag before ES index reflects changes.
- **Testing:** Verify data persists in MySQL database and updates ES index cleanly.

---

## 4. Phase-Wise Implementation List

| Phase | Phase Name | Focus Area | Deliverables & Tasks | Expected Bottleneck Impact |
| :---: | :--- | :--- | :--- | :--- |
| **Phase 1** | **ES Schema & Denormalization** | Elasticsearch | - Add job metadata, recruiter names, and numeric `notice_period_days` to `ELASTIC_CANDIDATES` mapping.<br>- Create re-indexing script/job to populate denormalized fields. | Eliminates 100% of synchronous `GeneralMethod::jobDetails()` HTTP GET requests. |
| **Phase 2** | **Controller & Query Refactoring** | Backend & Database | - Extract placed listing logic into `TalentPlacedService`.<br>- Eliminate `BenchApplication::count()` on initial load.<br>- Implement batch pre-fetching in controller for timeline dates. | Eliminates all 5 Blade N+1 SQL queries per row. Drops SQL query count per request to ≤ 2. |
| **Phase 3** | **JSON API & Frontend Modernization** | API & Frontend | - Refactor `placedListingData_ajax` to return structured JSON.<br>- Update `placedListing.blade.php` to render rows via JS template.<br>- Update DragonflyDB caching to store JSON payloads. | Payload size reduced by 85%. Redis memory footprint reduced by 90%. |
| **Phase 4** | **Overview Aggregations & Dual Persistence** | Analytics & Sync | - Create ES aggregation endpoint for live stat cards.<br>- Refactor Commercial & Assignment drawers to write to MySQL + queue ES sync. | Live real-time stat cards; 100% MySQL source-of-truth data integrity. |
| **Phase 5** | **10K Concurrency & Load Testing** | Optimization | - Run Apache JMeter / k6 load testing for 10,000 concurrent virtual users.<br>- Tune Horizon queues and Redis cache TTLs. | Verifies sub-100ms P95 response times under 10,000 concurrent recruiters. |

---

## 5. 10K+ Concurrency Strategy & Architecture

To support 10,000 concurrent recruiters on the existing 8-core, 31.34 GB RAM server:

```
[ 10,000 Concurrent Recruiters ]
               │
               ▼
   [ Nginx / FrankenPHP / Octane ] (Keep-Alive Connections)
               │
               ▼
   [ DragonflyDB / Redis Cache ] ──(Cache Hit: < 5ms)──► [ JSON Response ]
               │ (Cache Miss)
               ▼
   [ Controller / Service Layer ]
               │
               ▼
   [ Elasticsearch Cluster ] ──(Single Denormalized ES Search)──► [ Native Indexed Sort ]
               │
               ▼
   [ Pure JSON Response ] (Size: ~15 KB)
```

1. **FrankenPHP / Laravel Octane:** Keep application state in memory to avoid boot overhead on each HTTP request.
2. **DragonflyDB Memory Efficiency:** Caching compact JSON arrays instead of heavy HTML strings allows caching up to 500,000 search permutations within 2 GB of RAM.
3. **Single ES Search per Request:** Every request executes exactly **1 Elasticsearch query** and **0 MySQL queries** on cache hit (or **1 batch query** on cache miss).

---

## 6. Testing, Rollout & Rollback Strategy

### Automated & Integration Testing Plan
- **Unit Tests:** Test `TalentPlacedService` multi-tenant visibility filtering against company boundaries.
- **Query Count Tests:** Automated test asserting zero N+1 SQL queries or external ES HTTP GETs execute during request handling.
- **Load Testing:** Benchmark API endpoint using `k6` to verify P95 latency stays under 100ms at 10,000 RPM.

### Rollout Plan
1. Deploy ES mapping updates and execute background re-indexing job.
2. Deploy V2 Service and Controller changes behind a feature flag (`config('features.talent_placed_v2')`).
3. Enable V2 feature flag for internal staging tenants, then roll out incrementally (10% -> 50% -> 100%).

### Rollback Plan
1. If unexpected errors occur, instantly flip `config('features.talent_placed_v2') => false` to fallback to V1 routing without downtime.
2. MySQL schema changes are strictly additive, allowing immediate rollback without data loss.
