# Find Jobs / Market Jobs Listing Page — V2 Architecture & Implementation Plan

> **Document Status:** PROPOSED PLAN ONLY — AWAITING USER APPROVAL  
> **Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/jobs/market-jobs/0`  
> **Target API Route:** `service_company.jobs.v2.marketJobList_ajax`  
> **Constraint:** NO CODE OR DATABASE CHANGES TO BE IMPLEMENTED UNTIL EXPLICIT APPROVAL IS GIVEN.

---

## Executive Summary & V2 Objective

The goal of the **Find Jobs V2 Architecture** is to transform the Market Jobs listing page from a query-heavy, server-rendered bottleneck into an enterprise-grade, high-throughput service capable of supporting **10,000+ concurrent recruiters**.

### Key V2 Metrics & Objectives
- **Target Single-Request Database Queries:** `0` (Zero MySQL queries during standard listing reads).
- **Target Single-Request Elasticsearch Queries:** `1` (Single primary search query on `ats_jobs`).
- **Target Payload Size Reduction:** From ~150 KB (raw HTML snippet) to ~5 KB (compressed JSON API response).
- **Target Read Latency:** < 50ms (p95) under 1,000 requests/second.
- **Cache Hit Latency:** < 5ms via Redis / DragonflyDB read-through cache.

---

## 1. Proposed V2 Architecture

```
[ Recruiter Browser ]
         │
         ├──> 1. Initial Page Load: GET /service_company/jobs/market-jobs/0
         │      └──> Serves Static Shell Blade (Metronic layout, JS components, static filters)
         │
         └──> 2. Async Data Request: GET /service_company/api/v2/market-jobs?page=1&...
                │
                ├──> [ Rate Limiter & Security Middleware ]
                │      └──> Verifies Session, Tenant Company ID, Permission ('view_jobs')
                │
                ├──> [ Redis Read-Through Cache Layer ]
                │      ├──> Cache Hit  ──> Return Cached JSON (< 5ms)
                │      └──> Cache Miss ──> Pass to Service Layer
                │
                ├──> [ MarketJobV2Service / Repository ]
                │      ├──> Builds Single Elasticsearch Search Query (`ats_jobs`)
                │      ├──> Denormalized Document Source contains:
                │      │      - Job Metadata (ID, Title, Category Name, Experience, Salary Modes)
                │      │      - Company Summary (Name, Size, Industry)
                │      │      - Embedded Skill Tags Array
                │      │      - Pre-indexed Location Objects (City, State, Country)
                │      │      - Vendor Count & Market Status Indicators
                │      │
                │      └──> Executes 1 Bulk Multi-Get for Tenant Recommendation Relations (If needed)
                │
                ├──> [ Response Transformer / DTO ]
                │      └──> Formats Clean JSON Payload
                │
                ├──> [ Redis Write-Back Cache ]
                │      └──> Stores JSON Payload in Tagged Cache (`tenant:{company_id}:market_jobs`)
                │
                └──> [ Client-Side Rendering Component ]
                       └──> JS Template Renders Metronic Job Cards & Controls Tagify State
```

---

## 2. Business Logic Preservation Matrix

The following non-negotiable business rules and security constraints **MUST remain 100% unchanged** in V2:

| Concern | Unchanged Business Logic | Enforcement Location in V2 |
|---|---|---|
| **Tenant Isolation** | Recruiters MUST NEVER see jobs posted by their own company (`comId != current_company_id`). | Elasticsearch `must_not: term: comId` filter. |
| **Marketplace Action Rules** | `public_market_status = 2` shows "Apply As Agency". `public_market_status != 2` shows "Send Bench Request". | JS View Model / DTO action resolver. |
| **Vendor Limit Cap** | Max 20 vendor additions per job (`market_job_added_count`). Status `6` or >= 20 shows "Vendor FullFilled". | Aggregated / Denormalized `vendor_added_count` field in DTO. |
| **Currency Conversion** | Salaries stored in multiple currencies converted to INR using `GeneralMethod::convertToINR()` for normalized filtering. | `MarketJobV2Service` salary query builder. |
| **Salary Modes** | Salary filters calculate annual, monthly, or hourly rate based on `sal_mode` parameter. | `MarketJobV2Service` dynamic salary field selector. |
| **User Permissions** | Route access gated by `all_requisition` and `view_jobs` permissions. | Middleware & Service authorization layer. |

---

## 3. Comprehensive Optimization Matrix (Problem → Solution → Risk → Testing)

For every identified architectural flaw, the table below details the exact problem, proposed V2 solution, potential implementation risks, and verification strategy:

### 3.1 N+1 Query Removal & Data Access

#### Recommendation 1: Denormalize Relational Metadata into `ats_jobs` Elasticsearch Index
- **Problem:** Currently, rendering 20 job cards executes 101 Elasticsearch queries (`ats_job_requests`, `ats_companies`, `ats_job_applications`, `ats_interviews`) and 101 MySQL queries (`job_categories`, `job_metronics_skills`, `city_list`, `market_job_added_count`) inside the Blade loop.
- **Solution:** Update the job indexing observer/event listener (`IndexJobToElasticsearch`) to denormalize company name/size, category name, skill tags array, location names, and vendor counts directly into the `ats_jobs` document source when a job is created or updated.
- **Risk:** Stale company or category names if updated in MySQL without triggering job re-indexing.
- **Testing:** Unit test `IndexJobToElasticsearch` job; verify document mapping in ES via `GET /ats_jobs/_mapping`; assert 0 secondary queries during AJAX execution via Laravel Telescope / Debugbar.

#### Recommendation 2: Adopt Bulk Helper `getrRcommendationRelationDataBulk()`
- **Problem:** `GeneralMethod::getrRcommendationRelationData()` is called per-item in the Blade loop, executing 20 separate ES requests per page.
- **Solution:** Call `GeneralMethod::getrRcommendationRelationDataBulk($company_id, $jobIds)` once in the controller/service after fetching job IDs, mapping relations in a single multi-term ES search.
- **Risk:** Large `$jobIds` array on high page size limits could increase ES bulk query payload.
- **Testing:** Benchmark single bulk relation query vs 20 individual queries; assert execution time < 10ms.

#### Recommendation 3: Remove Dead Subquery Pluck & Unexecuted Query Builder
- **Problem:** `marketJobList_ajax()` executes `DB::table('jobs')->leftJoin('job_metronics_skills', ...)->pluck('jobs.id')->toArray()` and discards the result. `$this->jobsAll` in `dataCnt()` is constructed without calling `.count()`.
- **Solution:** Delete the `$subQuery` database call entirely. Remove the unused `$this->jobsAll` builder in `dataCnt()`.
- **Risk:** None. The code is dead and unreferenced.
- **Testing:** Code diff review; run existing integration tests to confirm zero side effects.

---

### 3.2 Query, Search, Filter & Pagination Optimization

#### Recommendation 4: Replace Location Database JOIN Loop with ES Nested Search
- **Problem:** Location filter runs a `foreach` loop executing `DB::table('countries')->join('state_list')->join('city_list')` queries.
- **Solution:** Match location filter directly against pre-indexed nested location fields (`client_location.country_id`, `client_location.state_id`, `client_location.city_id`) in `ats_jobs` index.
- **Risk:** Mismatch if country/city IDs in database do not match indexed ES location IDs.
- **Testing:** Test filtering by single city, multiple cities, and remote options; compare query result set between V1 DB query and V2 ES query.

#### Recommendation 5: Streamline Elasticsearch Bool Query & Enable Request Cache
- **Problem:** Elasticsearch search query does not set `'request_cache' => true` systematically and mixes filter/must contexts sub-optimally.
- **Solution:** Restructure ES query so non-scoring filters (status, company exclusion, employment type, ranges) are strictly placed in `filter` context to utilize Elasticsearch request cache. Set `'request_cache' => true`.
- **Risk:** Wasted cache memory if search parameters vary randomly across requests.
- **Testing:** Measure ES response `took` time on repeated queries; verify cache hit metrics in Elasticsearch stats (`_stats/request_cache`).

---

### 3.3 API Payload & Frontend Optimization

#### Recommendation 6: Transition from Server-Rendered HTML to Clean JSON API
- **Problem:** `marketJobList_ajax` returns a 150 KB JSON payload containing raw rendered HTML strings (`responseData.html`), wasting CPU time on server rendering and saturating bandwidth.
- **Solution:** Create a clean JSON API endpoint returning structured job DTOs (~5 KB payload). Implement a lightweight JavaScript client-side rendering component using template literals or Metronic card renderer.
- **Risk:** Initial frontend JS refactor regression in job card event handlers (e.g. drawer triggers).
- **Testing:** Verify payload size via Chrome DevTools Network tab (< 10 KB); test all interactive drawer triggers ("View Requirement", "Send Bench Request", "View T&C").

#### Recommendation 7: Refactor Tagify Synchronization & Eliminate `autoclickstar()` Hack
- **Problem:** JavaScript uses artificial `setTimeout` and simulated DOM clicks (`autoclickstar()`) to force Tagify input synchronization.
- **Solution:** Unify search inputs into a single Tagify state manager object with clean event listeners (`tagify:add`, `tagify:remove`). Remove `autoclickstar()` and artificial timeouts.
- **Risk:** Minor Tagify UI behavioral changes during typing.
- **Testing:** Manual UI testing of tag addition/removal, popular search tag clicks, and keyboard Enter key navigation across modern browsers.

---

### 3.4 Caching & Server Tuning Strategy

#### Recommendation 8: Fix Redis Read-Through Cache Line
- **Problem:** `cache()->has($cacheKeySet)` check exists in controller, but `return response()->json(cache()->get($cacheKeySet))` is commented out, turning cache into write-only overhead.
- **Solution:** Uncomment and harden cache read-through logic. Implement cache tags (`cache()->tags(['tenant_'.$company_id, 'market_jobs'])`) to enable instant invalidation when a new marketplace job is created.
- **Risk:** Stale data if cache invalidation events are missed when a job status changes.
- **Testing:** Perform sequential identical AJAX requests; verify 2nd request returns in < 5ms directly from Redis without reaching controller/ES logic.

#### Recommendation 9: PHP-FPM / Octane Worker & MySQL Connection Tuning
- **Problem:** 8 CPU cores / 31 GB RAM server risk worker queue starvation under 10,000 recruiter peak load if workers spend CPU cycles on DB connection overhead.
- **Solution:** Configure Nginx micro-caching for static assets; tune PHP-FPM / Octane worker pool (`pm.max_children = 64` or Octane concurrency = 32); configure MySQL connection pool / ProxySQL to cap active connections.
- **Risk:** High memory usage if PHP worker limit exceeds RAM capacity.
- **Testing:** Monitor memory usage during load testing via aaPanel resource monitor / htop.

---

## 4. 10K+ Concurrency Scaling Strategy

To guarantee that the Market Jobs page handles **10,000 concurrent recruiters**, the V2 implementation enforces 4 architectural pillars:

```
                              ┌───────────────────────────────────────┐
                              │     10,000 CONCURRENT RECRUITERS      │
                              └──────────────────┬────────────────────┘
                                                 │
                                                 ▼
                              ┌───────────────────────────────────────┐
                              │           Nginx Web Server            │
                              │    (Rate Limiting & Static Edge)     │
                              └──────────────────┬────────────────────┘
                                                 │
                                                 ▼
                              ┌───────────────────────────────────────┐
                              │  Redis / DragonflyDB Read Cache       │
                              │    (Serves >85% Reads in < 5ms)       │
                              └──────────────────┬────────────────────┘
                                                 │ (Cache Miss Only)
                                                 ▼
                              ┌───────────────────────────────────────┐
                              │    Octane / FrankenPHP Worker Pool    │
                              │     (1 ES Search Query per Req)       │
                              └──────────────────┬────────────────────┘
                                                 │
                                                 ▼
                              ┌───────────────────────────────────────┐
                              │     Elasticsearch Cluster (Read)      │
                              │    (0 MySQL Queries on Read Path)     │
                              └───────────────────────────────────────┘
```

1. **Zero Database Traffic on Read Path:** All read queries resolved via Elasticsearch and Redis. MySQL CPU stays at < 10% during peak read bursts.
2. **Read-Through Caching (> 85% Cache Hit Ratio):** Popular filter combinations (e.g. Default view, Remote jobs, Full-time jobs) served directly from DragonflyDB/Redis in < 5ms.
3. **Payload Compression & Offloading:** Reducing response size from 150 KB to 5 KB reduces server egress bandwidth from 1.2 Gbps to ~40 Mbps under 1,000 RPS.
4. **Asynchronous Cache Invalidation:** Job creation and application actions dispatch background queue events (`JobMarketplaceUpdated`) to clear tenant cache tags asynchronously without blocking user requests.

---

## 5. Phased Rollout & Migration Plan

### 5.1 Phase-Wise Implementation Matrix

| Phase | Phase Name | Key Tasks / Work Items | Targeted Files / Components | Expected Outcome / Milestone | Verification & Test Criteria | Risk Level |
|---|---|---|---|---|---|---|
| **Phase 1** | **Read-Model & ES Denormalization** | 1. Add denormalized fields to `ats_jobs` mapping (`company_name`, `company_size`, `category_name`, `skill_tags`, `location_list`, `vendor_added_count`).<br>2. Update `IndexJobToElasticsearch` job & observer to populate denormalized fields during create/update.<br>3. Write background reindex command (`php artisan reindex:ats_jobs_v2`). | • `app/Services/ElasticService.php`<br>• `app/Jobs/IndexJobToElasticsearch.php`<br>• `app/Console/Commands/ReindexAtsJobsV2.php` | All required display metadata embedded inside primary `ats_jobs` document source. | • Run reindex command in dry-run.<br>• Assert `GET /ats_jobs/_doc/{id}` includes all denormalized fields. | **Low** (Write side only, read side untouched). |
| **Phase 2** | **V2 Service Layer & Single-Pass Bulk Fetchers** | 1. Create `MarketJobV2Service` to build single ES search query on `ats_jobs`.<br>2. Replace per-item ES calls in Blade with single `GeneralMethod::getrRcommendationRelationDataBulk()` call.<br>3. Remove dead subquery plucks (`$subQuery`) and unexecuted query builders (`$this->jobsAll`).<br>4. Replace location DB query loop with nested ES location query. | • `app/Services/MarketJobV2Service.php`<br>• `app/Helper/GeneralMethod.php`<br>• `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | 0 MySQL queries and 1 primary ES query during read execution path. | • Execute `php artisan test --filter=MarketJobV2Test`<br>• Assert 0 DB queries via Laravel Telescope / Debugbar logs. | **Medium** (Core query logic refactor). |
| **Phase 3** | **Redis Read-Through Cache Layer** | 1. Fix commented-out cache read line in `marketJobList_ajax`.<br>2. Implement tagged cache (`cache()->tags(['tenant_'.$companyId, 'market_jobs'])`).<br>3. Add cache invalidation event listener on marketplace application or status update (`JobMarketplaceUpdated`). | • `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`<br>• `app/Listeners/InvalidateMarketJobsCache.php` | > 85% cache hit ratio on repeated searches; < 5ms response time for cached hits. | • Perform repeated identical AJAX search.<br>• Assert 2nd request latency < 5ms and 0 ES hits recorded. | **Low** (Cache read-through wrapper). |
| **Phase 4** | **Clean JSON API & Client Rendering** | 1. Create `MarketJobV2Resource` DTO transformer to format ~5 KB JSON response.<br>2. Add V2 AJAX route `/service_company/api/v2/market-jobs`.<br>3. Build client-side JS card template renderer.<br>4. Unify Tagify event listeners and remove `autoclickstar()` JS hack. | • `app/Http/Resources/MarketJobV2Resource.php`<br>• `routes/service.php`<br>• `resources/views/service_company/jobs/market-jobs.blade.php` | Response payload reduced from 150 KB HTML to ~5 KB JSON. Uncontrolled DOM hacks eliminated. | • Inspect Network tab in DevTools.<br>• Confirm JSON payload < 10 KB.<br>• Test all interactive drawer triggers ("View Requirement", "Send Bench Request", "View T&C"). | **Medium** (Frontend JavaScript UI refactor). |
| **Phase 5** | **Automated Testing & Performance Gate** | 1. Write automated feature tests for V2 API route.<br>2. Run static analysis and linter (`php -l`, `pint`).<br>3. Conduct load testing via ApacheBench / k6 (5,000 requests, 100 concurrency). | • `tests/Feature/MarketJobV2Test.php`<br>• `scripts/ai-guards.sh` | Clean pass on all feature tests, 0 lint errors, p95 latency < 50ms under load. | • Run `php artisan test --filter=MarketJobV2Test`.<br>• Run `./vendor/bin/pint --test`.<br>• Run k6 / ApacheBench load script. | **Low** (QA & Validation stage). |
| **Phase 6** | **Canary Rollout & Legacy Deprecation** | 1. Deploy feature flag (`market_jobs_v2_enabled`).<br>2. Enable V2 for 10% → 50% → 100% of tenant companies.<br>3. Monitor server CPU, MySQL connection count, and ES thread pool.<br>4. Deprecate legacy V1 N+1 methods. | • `config/features.php`<br>• `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | Zero-downtime transition to V2 architecture for 10,000+ concurrent recruiters. | • Real-time aaPanel resource monitoring.<br>• Rollback toggle test (verify instant fallback to V1 if disabled). | **Low** (Guarded by instant rollback flag). |

---

### 5.2 Rollout Roadmap Summary

```
  Phase 1: Read-Model & ES Denormalization (Zero Downtime)
  ├── 1. Add denormalized fields to `ats_jobs` mapping.
  ├── 2. Run Background Backfill Command: `php artisan reindex:ats_jobs_v2`.
  └── 3. Update `IndexJobToElasticsearch` job & observer.

  Phase 2: V2 Service Layer & Single-Pass Bulk Fetchers
  ├── 1. Create `MarketJobV2Service` & query builder.
  ├── 2. Replace per-item ES calls with `getrRcommendationRelationDataBulk()`.
  ├── 3. Remove dead subquery plucks (`$subQuery`) & unexecuted query builders.
  └── 4. Replace location DB query loop with nested ES query.

  Phase 3: Redis Read-Through Cache Layer
  ├── 1. Fix commented-out cache read-through line.
  ├── 2. Implement tenant-tagged Redis cache (`cache()->tags()`).
  └── 3. Add `InvalidateMarketJobsCache` listener.

  Phase 4: Clean JSON API & Client Rendering
  ├── 1. Create `MarketJobV2Resource` DTO transformer (~5 KB payload).
  ├── 2. Deploy V2 route `/service_company/api/v2/market-jobs`.
  ├── 3. Build JS client-side card renderer.
  └── 4. Unify Tagify handlers & delete `autoclickstar()` JS hack.

  Phase 5: Automated Testing & Performance Gate
  ├── 1. Execute `php artisan test --filter=MarketJobV2Test`.
  ├── 2. Run `./vendor/bin/pint --test` & `php -l`.
  └── 3. Run ApacheBench / k6 load test (5,000 requests, 100 concurrency).

  Phase 6: Canary Rollout & Legacy Deprecation
  ├── 1. Deploy feature flag `market_jobs_v2_enabled`.
  ├── 2. Ramp tenant traffic 10% → 50% → 100%.
  └── 3. Deprecate legacy V1 N+1 code.
```


---

## 6. Rollback Strategy

If any critical issue, data inconsistency, or unexpected regression occurs during V2 rollout:

1. **Instant Feature Flag Toggle:** Disable `market_jobs_v2_enabled` in `.env` or Redis runtime config (`config('features.market_jobs_v2', false)`). The application instantly falls back to the V1 Blade rendering pipeline.
2. **Zero Database Migration Rollback Needed:** V2 relies on Elasticsearch denormalization and new API endpoints. No destructive MySQL schema migrations are performed.
3. **Cache Purge Command:** Run `php artisan cache:clear` if stale cache issues are detected.

---

## 7. Verification & Testing Plan

### 7.1 Automated Testing Commands
- **PHP Syntax & Linting Verification:**
  `php -l app/Services/MarketJobV2Service.php`  
  `./vendor/bin/pint --test`
- **Route Registration Check:**
  `php artisan route:list --path=service_company`
- **Unit & Integration Test Suite:**
  `php artisan test --filter=MarketJobV2Test`

### 7.2 Manual & Visual Verification
- Verify search by job title, skills, and location on `http://localhost/git/JWMaincode28June2026/service_company/jobs/market-jobs/0`.
- Verify action drawers: "View Requirement", "Send Bench Request", "View T&C".
- Confirm "Apply As Agency" button state transitions and vendor fulfillment limit behavior.

### 7.3 Load & Performance Verification
- **Target Load Test Command (via k6 / ApacheBench):**
  `ab -n 5000 -c 100 -H "Authorization: Bearer <token>" http://localhost/service_company/api/v2/market-jobs`
- **Pass Criteria:**
  - 0 Failed Requests (0% error rate).
  - p95 Response Time < 50ms.
  - Total MySQL Queries during load test = 0 (verified via MySQL general log).

---

*End of Implementation Plan.*
