# CV Tracker Listing V2 Implementation Plan

> **NOTICE:** DO NOT IMPLEMENT THIS V2 PLAN AUTOMATICALLY. THIS DOCUMENT IS CREATED FOR PLANNING AND AUDIT PURPOSES ONLY. IMPLEMENTATION WILL BEGIN ONLY AFTER EXPLICIT USER APPROVAL.

---

## 1. V2 Objective
The primary objective of CV Tracker Listing V2 is to re-architect the `/service_company/cvtracker` module to support **10,000+ concurrent recruiters** with sub-100ms API response times. V2 will eliminate **100% of N+1 database queries**, replace server-side HTML rendering with a **pure JSON REST API**, optimize Elasticsearch mapping to eliminate Painless script sorting, introduce tenant-level Redis caching, and enforce strict database indexing while preserving all business logic and tenant isolation rules.

---

## 2. Proposed Architecture

### Architectural Shift: Monolith Blade Partial → High-Throughput REST + Client-Side Rendering
```
[ Frontend: Metronic / JS DataTable Engine ]
                    │
                    ▼ (Pure JSON Request: GET /api/v2/service_company/cvtracker/candidates)
[ Controller: ServiceCompanyCvTrackerV2Controller ]
                    │
                    ▼
[ Service Layer: CvTrackerService ]
                    │
                    ├──► [ Cache Layer: DragonflyDB / Redis (ats:cvtracker:{company_id}:{filters_hash}) ]
                    │      └── Hit: Return JSON directly (< 5ms)
                    │
                    └──► [ Miss: Read Layer - Elasticsearch (local_candidates index) ]
                           ├── Pre-indexed Denormalized Data (No MySQL Queries during Read!)
                           ├── Numeric Sort Fields (No Painless Scripts!)
                           └── Fast Aggregations for KPI Cards & Tab Badges
```

### Architectural Principles
1. **Read-Path Zero MySQL Queries:** The candidate listing endpoint must read 100% of table row data, filters, and aggregations directly from Elasticsearch and Redis. Zero MySQL queries are allowed during AJAX page rendering.
2. **Denormalized Elasticsearch Mapping:** All required row fields (`job_mode`, `job_currency`, `city_name`, `spokperson_name`, `child_status_name`, `is_duplicate`) are pre-indexed into Elasticsearch documents during candidate create/update events.
3. **Pure JSON API Payload:** Endpoint returns lightweight JSON objects instead of HTML strings (`responseData.html`).
4. **Company-Scoped Shared Caching:** Redis cache keys use `company_id` instead of `user_id` so recruiters within the same organisation share cache results.

---

## 3. Query Optimization & N+1 Removal

### Recommendation 1: Eliminate Blade View N+1 Queries via ES Denormalization
* **Problem:** Blade template `all_candidates.blade.php` executes 5 MySQL queries per row (100 DB queries per page of 20 candidates) for job currency/mode, duplicate application check, city lookup, spokesperson user join, and child candidate status.
* **Solution:** Denormalize these 5 attributes directly into the Elasticsearch `local_candidates` index mapping:
  * `job_max_sal_mode`, `job_max_salary_currency`
  * `city_name`
  * `spokperson_name`
  * `child_status_name`
  * `is_duplicate_app` (boolean flag pre-calculated on write)
* **Risk:** Slightly increased document size in Elasticsearch and requirement to re-index when job or spokesperson metadata changes.
* **Testing:** Run `DB::enableQueryLog()` during AJAX listing request and assert `count(DB::getQueryLog()) === 0`.

### Recommendation 2: Eliminate Controller Tagify Loop Queries
* **Problem:** Controller loops over location and education tags executing `DB::table('city_list')->where(...)` and `DB::table('qualification_master')->where(...)` for every tag in request.
* **Solution:** Replace loop queries with single batch `whereIn()` queries or leverage Redis cache for master city/qualification lookups.
* **Risk:** Minimal; cache invalidation required if city or qualification master tables are updated.
* **Testing:** Perform multi-tag location/education searches and verify via query log that query count remains constant regardless of tag count.

### Recommendation 3: Eliminate Un-indexed Skill Subquery
* **Problem:** `cus_search_skill` executes a heavy MySQL left join on `job_application_skills` (470k+ rows) with full table scans.
* **Solution:** Query skills directly via Elasticsearch `nested` or `terms` match on `job_application_skills.skills` field without touching MySQL.
* **Risk:** Elasticsearch mapping must ensure `job_application_skills` array is indexed correctly with `keyword` analyzers.
* **Testing:** Execute skill filter searches and verify 0 MySQL queries are executed and ES response time is < 20ms.

---

## 4. Pagination, Search, and Filter Optimization

### Recommendation 4: Replace Painless Sort Scripts with Indexed Numeric Fields
* **Problem:** Notice period sorting (cases 5, 6, 21, 22) and Best Match sorting (case 30) use inline Painless scripts that dynamically parse date strings and loop through skill arrays per hit, causing ES CPU saturation.
* **Solution:** Pre-calculate and index numeric fields in Elasticsearch:
  * `notice_period_days` (integer: 0 for Immediate, 15, 30, 60, 90, 9999 for invalid)
  * `notice_period_epoch` (long: epoch day for serving notice period last working date)
  * `skills_keyword_array` (array of lowercase skill strings for exact term match)
* **Risk:** Need to run a background re-indexing job for existing candidate documents.
* **Testing:** Compare ES search execution time using `profile: true` before and after numeric field indexing; verify > 80% reduction in sort latency.

---

## 5. Database Index Strategy

To support fast writes and guarantee fallback safety, add the following composite database indexes to `database/schema/mysql-schema.sql`:

```sql
-- 1. Index for add_job_wise_condidates (63k+ rows)
ALTER TABLE `add_job_wise_condidates` 
ADD INDEX `idx_ajwc_app_company_status` (`job_application_id`, `company_id`, `master_status_id`),
ADD INDEX `idx_ajwc_thirdparty_company` (`thirdparty_company_id`, `master_status_id`);

-- 2. Index for job_application_skills (470k+ rows)
ALTER TABLE `job_application_skills` 
ADD INDEX `idx_jas_app_skill` (`job_application_id`, `skills`(30)),
ADD INDEX `idx_jas_skills` (`skills`(30));

-- 3. Index for job_applications (63k+ rows)
ALTER TABLE `job_applications` 
ADD INDEX `idx_ja_company_status` (`company_id`, `master_status_id`),
ADD INDEX `idx_ja_submitted_by` (`submitted_by`),
ADD INDEX `idx_ja_city_id` (`city_id`);

-- 4. Index for job_assign (9k+ rows)
ALTER TABLE `job_assign` 
ADD INDEX `idx_ja_job_company_spok` (`job_id`, `company_id`, `spok_user_id`);
```

### Recommendation 5: Apply Composite Database Indexes
* **Problem:** `add_job_wise_condidates` and `job_application_skills` have zero non-PK indexes, causing full table scans during joins and counter queries.
* **Solution:** Apply the composite SQL indexes listed above.
* **Risk:** Brief table lock during index creation in production (mitigate by using `ALGORITHM=INPLACE, LOCK=NONE` or running during low-traffic maintenance window).
* **Testing:** Run `EXPLAIN` on all candidate counter and lookup queries; verify access type changes from `ALL` to `ref` / `range` and scanned rows drop from 63k+ to < 100.

---

## 6. API & Frontend Optimization

### Recommendation 6: Transition from HTML Payload to JSON API
* **Problem:** `allcandidates` returns 85KB HTML string (`responseData.html`), wasting CPU, memory, and network bandwidth.
* **Solution:** Return a pure JSON API response containing structured candidate arrays:
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 101,
        "full_name": "John Doe",
        "current_designation": "Senior Laravel Developer",
        "city_name": "Mumbai",
        "experience": "5.5 Yrs",
        "ctc_formatted": "₹ 15,000,000",
        "notice_period": "Immediate",
        "skills": ["Laravel", "Vue.js", "MySQL"],
        "job_title": "Full Stack Engineer",
        "spokperson_name": "Alice Smith",
        "is_duplicate": false
      }
    ],
    "meta": {
      "total": 1250,
      "page": 1,
      "per_page": 20,
      "last_page": 63
    },
    "stats": { ... }
  }
  ```
  On the frontend, render table rows dynamically using client-side JavaScript templates.
* **Risk:** Frontend JS code in `index.blade.php` must be refactored to render rows from JSON objects.
* **Testing:** Measure payload transfer size (reduces from 85KB to ~4KB) and client rendering time in Chrome DevTools Network & Performance tabs.

---

## 7. Cache, Elasticsearch, and Queue Strategy

### Recommendation 7: Tenant-Level Shared Caching in Redis
* **Problem:** Cache key incorporates `$user->id`, preventing recruiters of the same company from sharing cached candidate search results.
* **Solution:** Structure cache key at the tenant level:
  `ats:cvtracker:{company_id}:{filters_hash}`
  Invalidate tenant cache when a candidate status or application is updated for that `company_id`.
* **Risk:** Must handle cache invalidation properly on candidate write events (`JobApplicationObserver`).
* **Testing:** Perform duplicate searches with two different recruiter accounts under the same tenant; verify 2nd request hits Redis cache (< 5ms).

### Recommendation 8: Async Queue Sync for Elasticsearch
* **Problem:** Synchronous ES indexing on web worker threads can slow down candidate updates.
* **Solution:** Dispatch `SyncCandidateToElasticJob` to Laravel Horizon queue workers on candidate write/update.
* **Risk:** Slight propagation delay (1-2 seconds) between MySQL save and ES search update.
* **Testing:** Monitor Horizon queue processing speed and verify job completion time < 100ms.

---

## 8. Server Optimization

* **PHP-FPM Config:** Tune `pm.max_children = 100`, `pm.start_servers = 20`, `pm.max_spare_servers = 40` to handle concurrent API hits.
* **MySQL Configuration:** Set `innodb_buffer_pool_size = 20G` (64% of 31.34 GB RAM) for memory-resident indexing.
* **Redis / DragonflyDB:** Ensure maxmemory policy is `allkeys-lru` for automatic cache eviction.

---

## 9. 10,000+ Concurrency Strategy

1. **Read Path Isolation:** 100% of read traffic served from Redis cache and Elasticsearch. MySQL CPU stays < 10% during peak read bursts.
2. **Bandwidth Reduction:** JSON payload shrinks response size by 95%, enabling 10k concurrent requests within a standard gigabit network link.
3. **Stateless Scale-Out:** API endpoints are fully stateless; additional PHP/Nginx app nodes can be added horizontally behind a load balancer without session stickiness issues.

---

## 10. Business Logic Preservation (NON-NEGOTIABLE)

The following core business logic rules must remain 100% intact without any alteration:
1. **Tenancy Boundaries:** Strict multi-tenant isolation by `company_id` and `thirdparty_company_id`.
2. **Recruiter Job Scope:** Non-admin recruiters must only see candidates for assigned jobs (`job_assign`).
3. **Role Permissions:** `view_job_applications` gate check must enforce 403 Forbidden on unauthorized access.
4. **Subscription Locking:** Unpaid accounts must be redirected via `GeneralMethod::checksubscription()`.
5. **Salary Conversion:** Salary filter values must convert foreign currencies to INR via `GeneralMethod::convertToINR()`.
6. **Skill Highlighting:** Row skill chips must correctly highlight starred (mandatory/blue) vs unstarred (optional/green) vs default job skills.

---

## 11. Testing, Rollout, and Rollback Plan

### Automated Testing Plan
* **Unit Tests:** `tests/Unit/CvTrackerServiceTest.php` to verify query building, currency conversion, and tenancy scoping.
* **Feature Tests:** `tests/Feature/CvTrackerV2ApiTest.php` to test API authorization, JSON schema validation, pagination, and filter accuracy.
* **Performance Tests:** Run `k6` or `Apache JMeter` load test simulating 1,000 to 10,000 concurrent requests; verify 99th percentile response time < 100ms.

### Gradual Rollout Strategy
1. Deploy DB composite indexes during off-peak hours.
2. Run background queue job to re-index candidate ES documents with new denormalized fields.
3. Deploy V2 API route (`/api/v2/service_company/cvtracker/candidates`).
4. Enable V2 via tenant feature flag (`config('features.cvtracker_v2')`). Enable for 10% of companies first, monitor telemetry, then scale to 100%.

### Rollback Plan
If any critical defect or discrepancy is detected in V2:
1. Instantly flip feature flag `cvtracker_v2 => false`.
2. System immediately falls back to V1 controller (`ServiceCompanyCvTrackerController@allcandidates`).
3. Zero downtime and zero data loss, as MySQL write path remains unaffected.

---

## 12. Phase-Wise Implementation List (Table Format)

| Phase | Target Scope | Key Tasks / Deliverables | Estimated Risk | Verification Method |
| :---: | :--- | :--- | :---: | :--- |
| **Phase 1** | Database Indexing | Apply composite indexes on `add_job_wise_condidates`, `job_application_skills`, `job_applications`, `job_assign` in `mysql-schema.sql` | Low | Run `EXPLAIN` on counter & join queries; verify zero full table scans |
| **Phase 2** | Elasticsearch Mapping & Re-indexing | Update `local_candidates` mapping with denormalized fields (`city_name`, `spokperson_name`, `child_status_name`, `is_duplicate`) and numeric sort fields (`notice_period_days`) | Medium | Run ES mapping get command; test search query performance without Painless scripts |
| **Phase 3** | Service Layer & V2 Controller | Create `CvTrackerService` and `ServiceCompanyCvTrackerV2Controller` with zero MySQL read queries and tenant Redis caching | Low | PHPUnit feature tests (`tests/Feature/CvTrackerV2ApiTest.php`); assert `DB::getQueryLog()` is empty |
| **Phase 4** | Pure JSON API & Client JS Rendering | Build `/api/v2/service_company/cvtracker/candidates` returning clean JSON; update `index.blade.php` JS to render rows via JSON template | Medium | Chrome DevTools inspect network payload (< 5KB); verify UI features & skill highlighting match V1 |
| **Phase 5** | Load Testing & Telemetry | Conduct `k6` load test simulating 10,000 concurrent recruiters; tune PHP-FPM, MySQL buffer pool, and Redis config | Low | Load test report confirming 99th percentile latency < 100ms and 0% HTTP errors |
| **Phase 6** | Feature Flag Rollout | Deploy behind `features.cvtracker_v2` flag; enable for 10% -> 50% -> 100% of tenants with V1 fallback ready | Low | Production APM dashboard monitoring error rates and response latency |

---

> **STOP:** Implementation plan created. Awaiting user review and explicit approval before any code modifications begin.
