# Agency Listing (`spartnerlisting`) V2 - Comprehensive Implementation Plan

> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/jobs/spartnerlisting/263`  
> **Environment Context:** JERO Multi-tenant B2B Hiring SaaS (Laravel 11, MySQL, Elasticsearch, Redis/DragonflyDB)  
> **Document Status:** PROPOSED DESIGN & PLANNING ONLY. STRICTLY DO NOT IMPLEMENT V2 UNTIL APPROVED BY USER.

---

## 1. V2 Objective & High-Level Architecture

The primary goal of Agency Listing V2 is to transform the agency partnership listing page into a high-performance, enterprise-grade module capable of scaling to **10,000+ concurrent recruiters** with sub-100ms response times.

### Architectural Improvements:
1. **Decoupled JSON API Architecture:** Eliminate rendering server-side Blade HTML templates in AJAX responses. Replace with lightweight, structured JSON payloads rendered on the client side via vanilla JS templates.
2. **Zero N+1 Query Execution:** Batch-fetch agency metrics, user details, industry classifications, and client profiles in a single bulk query in the controller layer prior to response serialization.
3. **Optimized Search Engine Integration:** Replace dynamic Painless scripts and wildcard ES queries with pre-tokenized edge-ngram / term filters on `ELASTIC_COMPANIES_INDEX`.
4. **Multi-Layered Caching Strategy:** Utilize ETag-based HTTP 304 Client Revalidation combined with DragonflyDB / Redis 60-minute TTL caching for all warm agency listing requests.

```
+-----------------------------------------------------------------------------------+
|                                 CLIENT BROWSER                                    |
|  - Tagify Debounced Input (300ms)                                                 |
|  - Vanilla JS Component Renderer (Zero DataTables overhead)                       |
|  - Local Storage / State Management for Filters                                   |
+-----------------------------------------------------------------------------------+
                                         │
                             HTTP GET (ETag Validation)
                                         ▼
+-----------------------------------------------------------------------------------+
|                                NGINX REVERSE PROXY                                |
|  - HTTP 304 Cache Revalidation (If-None-Match header)                             |
|  - Gzip / Brotli Compression                                                      |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼
+-----------------------------------------------------------------------------------+
|                              LARAVEL 11 OCTANE / PHP-FPM                          |
|  - ServiceCompanyJobsV2Controller (Thin Controller)                              |
|  - PartnerListingService (Business Logic Layer)                                   |
|  - PartnerListingRepository (Data Layer)                                          |
+-----------------------------------------------------------------------------------+
                       │                                   │
                       ▼                                   ▼
        +-----------------------------+     +-----------------------------+
        |     REDIS / DRAGONFLYDB     |     |    ELASTICSEARCH 8.X CLUSTER |
        |  - 60-min Versioned Cache   |     |  - Pre-tokenized Term Match |
        |  - Instant Cache Invalidate |     |  - Optimized Filter Scopes  |
        +-----------------------------+     +-----------------------------+
                       │
                       ▼
        +-----------------------------+
        |   MYSQL 8.0 (WRITE MODEL)   |
        |  - Indexed Tenancy Lookups  |
        |  - Single Batch SQL Queries |
        +-----------------------------+
```

---

## 2. Detailed Technical Action Plan

### 2.1 Query Optimization & N+1 Removal Strategy

#### Proposed Solution:
Instead of calling `GeneralMethod::agencyCandidatesCount($agency_id, $job_id)` inside a 20-iteration Blade loop, collect all `$agency_ids` returned from Elasticsearch or MySQL, and run a single grouped aggregation query in the repository layer:

```php
// Single batch query for all agencies in the current page payload:
$agencyMetrics = DB::table('candidate_request')
    ->select(
        'agency_company_id',
        DB::raw('COUNT(CASE WHEN stage_id = 1 THEN 1 END) as sourced_count'),
        DB::raw('COUNT(CASE WHEN stage_id = 4 THEN 1 END) as interview_count'),
        DB::raw('COUNT(CASE WHEN stage_id = 7 THEN 1 END) as joined_count')
    )
    ->where('job_id', $jobId)
    ->whereIn('agency_company_id', $agencyIds)
    ->groupBy('agency_company_id')
    ->get()
    ->keyBy('agency_company_id');
```

Similarly, batch-load primary agency contact users in one query:
```php
$contactUsers = DB::table('users')
    ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
    ->select('users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.image')
    ->whereIn('users.company_id', $agencyIds)
    ->where('job_assign.job_id', $jobId)
    ->get()
    ->groupBy('company_id');
```

---

### 2.2 Pagination, Search, Filter & Sort Optimization

1. **Standardized Page Size:** Enforce a strict default page size of 20 items across all three agency tabs (`Recommended`, `My Agency`, `Assigned`).
2. **Edge-Ngram Keyword Search:** Replace wildcard `*skill*` queries on `ELASTIC_COMPANY_SKILLS` with Elasticsearch `edge_ngram` tokenizers during index mapping setup.
3. **Filter State Preservation:** Persist filter selections (`hiringtype`, `location`, `experience`, `industry`) in browser `URLSearchParams` to enable deep-linking and browser back-button compatibility.

---

### 2.3 Database & Index Strategy

To ensure sub-10ms database lookups during batch enrichment, the following composite database index additions are recommended:

```sql
-- Recommended composite index for job_assign user queries
ALTER TABLE `job_assign` 
ADD INDEX `idx_ja_job_company_user` (`job_id`, `company_id`, `user_id`);

-- Recommended composite index for staffing_company_relation lookup
ALTER TABLE `staffing_company_relation` 
ADD INDEX `idx_scr_from_to_final` (`from_company_id`, `to_company_id`, `final_status`);

-- Recommended composite index for candidate_request aggregations
ALTER TABLE `candidate_request` 
ADD INDEX `idx_cr_job_agency_stage` (`job_id`, `agency_company_id`, `stage_id`);
```

---

### 2.4 API & Frontend Optimization

1. **Pure JSON API Payloads:** Convert AJAX endpoints (`recommendedData`, `staffingPartnersRegister`, `recommendedAssignList`) to return structured JSON arrays instead of rendered Blade HTML.
   ```json
   {
     "status": "success",
     "total": 78,
     "current_page": 1,
     "per_page": 20,
     "data": [
       {
         "agency_id": 102,
         "company_name": "Demo Staffing Agency",
         "logo": "https://...",
         "industry": "IT Services",
         "contact": { "name": "John Doe", "email": "john@demo.com", "mobile": "+91..." },
         "metrics": { "sourced": 12, "interviews": 4, "joined": 2 },
         "status": "active"
       }
     ]
   }
   ```
2. **Frontend Component Rendering:** Use an lightweight JavaScript component to render agency cards, reducing response payload sizes by ~90% (from ~85KB to ~4KB).
3. **Single Tagify Engine:** Consolidate multiple Tagify initializations into a single manager instance with a 300ms debounce on API autocompletion calls.

---

### 2.5 Cache, Elasticsearch & Queue Strategy

1. **ETag 304 Client Validation:** Retain and expand the V3 ETag feature flag (`config('feature.use_three_drawers_v3')`). When an employer views the agency listing page without making modifications, Nginx returns `HTTP 304 Not Modified`, skipping database and Elasticsearch lookups entirely.
2. **60-Minute Redis Cache TTL:** Store warm agency listing JSON payloads in DragonflyDB / Redis with a 60-minute TTL. Invalidate cache tags instantly whenever a user assigns/removes an agency or updates job metadata.
3. **Asynchronous Write Syncing:** Queue agency marketplace status updates and notification emails via Laravel Horizon background jobs (`ProcessAgencyAssignmentJob`).

---

### 2.6 Server Tuning & 10K+ Concurrency Strategy

1. **PHP-FPM Pool Sizing:** Set `pm = dynamic`, `pm.max_children = 120`, `pm.start_servers = 20`, `pm.max_requests = 1000` to handle spike traffic across 8 vCPU cores.
2. **OPcache Optimization:** Set `opcache.memory_consumption = 512`, `opcache.max_accelerated_files = 30000`, `opcache.validate_timestamps = 0` in production.
3. **MySQL InnoDB Buffer Pool:** Allocate 20GB of the available 31.34GB RAM to `innodb_buffer_pool_size` to cache all active table indexes in system memory.
4. **Elasticsearch JVM Heap:** Allocate 8GB JVM heap (`-Xms8g -Xmx8g`) with G1GC garbage collection enabled.

---

## 3. Recommendation-by-Recommendation Matrix

| ID | Problem Statement | Proposed Solution | Potential Risk | Verification & Testing Method |
| :--- | :--- | :--- | :--- | :--- |
| **R-01** | N+1 queries in Blade loop execution across agency rows. | Batch-fetch all agency metrics and contact users in a single grouped query before response payload generation. | Low risk. Missing agency IDs return zeroed metrics. | Run query log assertion (`DB::enableQueryLog()`) to verify exact SQL query count <= 3 per tab load. |
| **R-02** | Large HTML fragment responses (~85KB) inflate AJAX network payload. | Transform controller to return pure JSON schema; render UI via lightweight client-side JS template. | Low risk. Requires careful front-end template migration. | Test DOM rendering across Chrome, Firefox, Safari; inspect DevTools Network tab for ~4KB payload size. |
| **R-03** | Wildcard ES searches (`*skill*`) cause high disk I/O & slow searches. | Implement pre-tokenized `edge_ngram` analyzer on skill fields in Elasticsearch mappings. | Medium risk. Requires index re-indexing script execution. | Execute benchmark skill search queries against ES node and compare latency metrics (<20ms target). |
| **R-04** | Short 3-minute Redis cache TTL forces frequent cache misses under load. | Extend TTL to 60 minutes with strict event-driven cache invalidation on write paths. | Low risk. Stale cache risk if write triggers miss cache purge keys. | Perform write operation (assign agency) and verify target cache key is instantly deleted from Redis. |
| **R-05** | Legacy DataTables JS dual initializations cause heavy DOM re-flows. | Remove unused DataTables libraries; adopt clean, direct DOM table insertion. | Low risk. Ensure column sorting functions remain intact. | Test search, pagination, and sorting UX on low-spec client devices. |

---

## 4. Non-Negotiable Business Logic Preservation Protocol

During implementation of V2, the following rules MUST be preserved without exception:
1. **Multi-Tenancy Scoping:** All Eloquent and DB builder queries must explicitly scope by `company_id = auth()->user()->company_id`.
2. **Marketplace Publishing Flow:** Preserved status state mapping:
   - Status 1: Approval Pending
   - Status 2: Published
   - Status 3/4: Publishable / Unpublishable
3. **Vendor Limit Enforcement:** Strictly validate `global_settings.vendor_limit` count prior to allowing new agency assignments.
4. **Preserve Legacy Naming & Schema:** Preserve existing table column typos (e.g. `commericial_countries`, `bench_condidate`) in database schemas and ES index mappings.

---

## 5. Testing & Verification Strategy

> [!IMPORTANT]
> Standard Rule Enforced: Do NOT use `RefreshDatabase`. Use targeted transactions or mock repositories.

1. **Automated Unit & Integration Tests:**
   - Execute `php artisan test --filter=PartnerListingTest` validating JSON response keys, multi-tenancy boundaries, and HTTP status codes.
2. **Query Count Assertion Tests:**
   ```php
   public function test_assigned_agency_tab_executes_minimal_queries()
   {
       DB::enableQueryLog();
       $response = $this->actingAs($user)->get(route('service_company.jobs.recommendedAssignList', ['jobid' => 263]));
       $response->assertStatus(200);
       $this->assertLessThanOrEqual(4, count(DB::getQueryLog()));
   }
   ```
3. **Performance & Stress Verification:**
   - Conduct HTTP load testing using Apache Bench (`ab`) or k6:
     ```bash
     ab -n 1000 -c 50 -H "Authorization: Bearer token" http://testing.jero.works/service_company/jobs/recommendedAssignList?jobid=263
     ```
   - Target Criteria: p95 response time < 80ms; 0 error responses.

---

## 6. Phase-Wise Implementation Roadmap

| Phase | Phase Title | Included Tasks & Scope | Potential Risk | Verification Milestone |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | **Backend Batching & N+1 Removal** | - Refactor `recommendedAssignList`, `staffingPartnersRegister`, and `recommendedData` to batch-fetch metrics.<br>- Remove raw SQL calls from Blade partial views. | Low | Query log count <= 3 SQL queries per endpoint invocation. |
| **Phase 2** | **JSON API & Frontend Decoupling** | - Convert AJAX endpoints to return structured JSON payloads.<br>- Replace Blade AJAX templates with client-side JavaScript card renderers.<br>- Remove legacy DataTables script dependencies. | Low | Verify UI rendering, payload size reduction (>85% smaller), and cross-browser stability. |
| **Phase 3** | **Elasticsearch Mapping & Search Tuning** | - Update `ELASTIC_COMPANY_SKILLS` & `ELASTIC_COMPANIES_INDEX` mappings to use `edge_ngram`.<br>- Replace wildcard search queries with term/match filters. | Medium | Search benchmark execution (<20ms latency per query). |
| **Phase 4** | **Caching, ETag & Write Invalidation** | - Extend Redis cache TTL to 60 minutes.<br>- Implement ETag HTTP 304 revalidation across all agency listing tabs.<br>- Add write-path cache purging logic on assignment actions. | Low | Verify 304 response on unchanged requests; verify instant cache purge on agency assignment. |
| **Phase 5** | **Database Indexing & Server Tuning** | - Apply database composite indexes to `job_assign`, `staffing_company_relation`, `candidate_request`.<br>- Tune PHP-FPM pool, OPcache, and MySQL InnoDB buffer pool settings. | Medium | Load test simulation (1,000 requests @ 50 concurrency); p95 latency < 80ms. |

---

## 7. Rollout & Rollback Strategy

### 7.1 Zero-Downtime Rollout Strategy
1. **Feature Flag Control:** Wrap V2 logic inside a dedicated feature flag (`config('feature.use_partner_listing_v2')`).
2. **Canary Deployment:** Enable V2 feature flag for internal testing tenants first.
3. **Gradual Traffic Shift:** Monitor server CPU, memory, and error rates via Horizon & APM before enabling globally.

### 7.2 Rollback Strategy
1. If error rate exceeds 0.1% or JVM heap usage exceeds 85%, immediately flip the feature flag back:
   ```env
   FEATURE_USE_PARTNER_LISTING_V2=false
   ```
2. Re-flush application cache: `php artisan cache:clear`.
3. System instantly falls back to legacy V1 behavior without downtime or data corruption.

---
*End of Implementation Plan Document.*
