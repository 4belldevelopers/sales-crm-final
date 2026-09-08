# Agency Listing V2 Implementation Plan

> **STATUS:** PROPOSAL ONLY — DO NOT IMPLEMENT V2. AWAIT USER APPROVAL BEFORE PROCEEDING.

---

## 1. V2 Objective & Architectural Goal

### Objective
Transform the Agency Listing (`service_company/consultancy`) from a heavy, N+1 query-laden legacy flow into a high-performance, enterprise-grade architecture capable of serving **10,000+ concurrent recruiters** with sub-100ms response times, zero N+1 database queries, and optimal resource usage on the existing server infrastructure (8 Cores, 32 GB RAM).

### High-Level Proposed Architecture
1. **Decoupled Page & Data Fetching:**
   - Initial `index()` page load renders only lightweight UI layout shell without executing synchronous heavy database or Elasticsearch queries.
   - Summary stat cards (`spartnerDataCnt`) and agency data grid are loaded via asynchronous API endpoints cached in DragonflyDB/Redis.
2. **Zero N+1 Query Engine:**
   - Eliminate all raw database queries (`DB::table(...)`) and Elasticsearch sub-queries (`GeneralMethod::agencyJobsCountAllstatus`, `GeneralMethod::agencyCandidatesCountList`) from inside the Blade template.
   - Pre-aggregate job counts (Active, Closed, Hold) and candidate pipeline counts (Sourced, Evaluated, Interviews, Offers, Joined) directly into a batch DTO (Data Transfer Object) in the Controller/Service layer, or store directly in the `ELASTIC_CLIENT_LIST` index.
3. **Structured API & JSON Response:**
   - Transition from server-side HTML string rendering (`responseData.html`) to clean, compact JSON payloads returned to client JS.

---

## 2. Phase-Wise Implementation Roadmap

| Phase | Target Area | Key Tasks & Technical Scope | Deliverables & Verification |
|---|---|---|---|
| **Phase 1** | **Database Indexing Strategy** | Add missing compound indexes on `staffing_company_relation`, `staffing_company_invitation`, `client_location`, and `recommendation_relation`. | `EXPLAIN` analysis showing zero full table scans on fallback SQL queries. |
| **Phase 2** | **Service Layer & Batch Stat Loader** | Create `AgencyListingService` with `getBatchAgencyStats()` method to fetch job & candidate metrics in 2 batch SQL queries for all page items instead of 70+ queries. | Unit tests comparing batch metrics output with legacy `GeneralMethod` calculations. |
| **Phase 3** | **Elasticsearch Document Enhancement** | Update `ELASTIC_CLIENT_LIST` index mapping to embed relation stats and nested location fields cleanly; remove `size: 10000` unpaginated relation query. | ES query benchmark showing response latency < 20ms. |
| **Phase 4** | **Controller & API Refactoring** | Refactor `ServiceCompanyConsultancyController@data_ajax` and `spartnerDataCnt` to use `AgencyListingService` and return structured JSON. | API response payload size reduced by 60%+; response time < 80ms. |
| **Phase 5** | **Redis Caching & Queue Worker Offloading** | Implement Redis key-value caching for stat card counts with 10-minute TTL; trigger async queue jobs (`UpdateAgencyStatsJob`) on relation/application updates. | Zero DB load on repetitive stat card refreshes. |
| **Phase 6** | **Frontend UI & Data Grid Optimization** | Refactor `index.blade.php` to render rows via lightweight client JS template engine or clean Blade partial using pre-hydrated DTOs. | Page load time under 150ms; smooth client-side pagination. |
| **Phase 7** | **Concurrency & Load Verification** | Execute Apache Bench / k6 load testing simulating 500 to 10,000 concurrent requests; verify server RAM, CPU, and DB connection pool stability. | Verified 0% error rate under load. |

---

## 3. Query & N+1 Removal Strategy

### Problem vs. Solution

#### Recommendation 1: Blade N+1 Query Elimination
- **Problem:** Every row rendered in `staffing-partner-ajax.blade.php` executes 5 SQL queries and 2 Elasticsearch HTTP API calls inside `@foreach`. For 10 rows, this fires 70+ queries.
- **Solution:** 
  Create an `AgencyListingService` that accepts the list of page `company_id`s and executes **2 bulk batch queries**:
  1. `getBatchJobCounts(array $agencyCompanyIds, int $fromCompanyId)`: A single MySQL `GROUP BY` query returning active/closed/hold job counts for all 10 agencies in one roundtrip.
  2. `getBatchCandidateCounts(array $agencyCompanyIds, int $fromCompanyId)`: A single MySQL `GROUP BY` query returning candidate pipeline counts (Sourced, Evaluated, Interviews, Offers, Joined) for all 10 agencies.
  Map these pre-computed metrics into an array keyed by `agency_company_id` before passing to the view.
- **Risk:** Slight divergence in count numbers if legacy status IDs are mismatched.
- **Testing:** Assert that `$agencyStats[123]->active_jobs === GeneralMethod::agencyJobsCountAllstatus(123, $fromCompanyId)->job_active` across 50 test agencies.

---

## 4. Indexing Strategy

### Proposed MySQL Indexes

Add the following targeted compound indexes to `database/schema/mysql-schema.sql` (to be created via proper Artisan migration in V2):

```sql
-- 1. Index for staffing company relations filtered by from_company_id & final_status
ALTER TABLE `staffing_company_relation` 
ADD INDEX `idx_scr_from_final_created` (`from_company_id`, `final_status`, `created_at`);

-- 2. Index for pending sent invitations
ALTER TABLE `staffing_company_invitation` 
ADD INDEX `idx_sci_from_inv_status` (`from_company_id`, `to_invitation_status`);

-- 3. Index for client location lookups by client_id
ALTER TABLE `client_location` 
ADD INDEX `idx_cl_client_id` (`client_id`);

-- 4. Index for recommendation relations filtered by from_company_id and to_company_id
ALTER TABLE `recommendation_relation` 
ADD INDEX `idx_rr_from_to_final` (`from_company_id`, `to_company_id`, `final_status`);
```

- **Problem:** Full table scans on `client_location`, `staffing_company_invitation`, and left-prefix index mismatch on `staffing_company_relation`.
- **Solution:** Execute the migration to add the 4 targeted indexes above.
- **Risk:** Minor storage overhead on write operations (negligible for read-heavy listing page).
- **Testing:** Run `EXPLAIN SELECT ...` on all 4 queries and verify `type` improves from `ALL` (full table scan) to `ref` or `range`.

---

## 5. API & Cache Optimization Strategy

### Redis / DragonflyDB Cache Refactoring
- **Problem:** Currently, the server caches raw rendered HTML strings (`responseData.html`) in Redis. This uses excessive memory and prevents API reuse.
- **Solution:** 
  1. Cache raw JSON data objects instead of rendered HTML.
  2. Cache summary card stats (`spartnerDataCnt`) under key `ats:spartner_cnt:{company_id}` for 10 minutes.
  3. When an agency relation or invitation changes, fire a Redis tag/key cache invalidation event for that `company_id`.

---

## 6. Server & 10,000+ Concurrency Scaling Plan

### Hardware Utilization (8 Cores, 32 GB RAM, Ubuntu 24.04, aaPanel)

1. **PHP-FPM Optimization:**
   - Set `pm = dynamic`
   - `pm.max_children = 120`
   - `pm.start_servers = 20`
   - `pm.min_spare_servers = 15`
   - `pm.max_spare_servers = 40`
   - `pm.max_requests = 1000` (prevents memory leaks over time)

2. **OPcache Optimization:**
   - `opcache.enable = 1`
   - `opcache.memory_consumption = 256`
   - `opcache.max_accelerated_files = 20000`
   - `opcache.validate_timestamps = 0` (in production)

3. **MySQL Connection Pooling & Buffer Pool:**
   - Set `innodb_buffer_pool_size = 20G` (65% of available 32 GB RAM).
   - Set `max_connections = 1000`.

---

## 7. Business Logic Preservation Checklist

During V2 implementation, the following data invariants MUST be maintained:

- [ ] **Tenancy Isolation:** All queries must explicitly filter by `company_id = auth()->user()->company_id`.
- [ ] **Relation Filter:** Only show agencies where `final_status = 1`.
- [ ] **Pending Invitations:** Count only `to_invitation_status != 1`.
- [ ] **Job Counts Invariant:** `Active` (status = active), `Closed` (status = inactive), `Hold` (status = hold).
- [ ] **Candidate Pipeline Invariant:**
  - `Sourced` = candidate in stage 1 (`master_status_id = 1`)
  - `Evaluated` = candidates in evaluation stages
  - `Interviews` = candidates scheduled/completed interview
  - `Offers` = candidates with active offer
  - `Joined` = candidate joined (`master_status_id = 6`)
  - Exclude deleted/rejected candidates (`master_status_id IN (8, 9)`).

---

## 8. Rollout, Testing, & Rollback Plan

### Testing Strategy
1. **Automated Feature Tests:** Create `tests/Feature/AgencyListingV2Test.php` to compare V1 and V2 JSON output line-by-line across 100 random tenant companies.
2. **Performance Benchmark:** Run `ab -n 1000 -c 50 http://localhost/service_company/consultancy_ajax/data` before and after V2. Target latency: < 80ms p95.

### Rollout Strategy
1. Deploy DB index migration during low-traffic window.
2. Enable feature flag `config('features.agency_listing_v2')`.
3. Canary release to 10% of service companies, monitor error logs for 24 hours, then rollout to 100%.

### Rollback Plan
If any critical discrepancy or unexpected server strain occurs:
1. Flip feature flag `config('features.agency_listing_v2') => false`. The controller instantly reverts to V1 execution path.
2. No database structure changes or column removals are involved, ensuring 100% safe, instant rollback.
