# Career Jobs Listing V2 — Implementation Plan & Architecture Specification

> **Document Version:** 1.0  
> **Date:** August 17, 2026  
> **Target Page:** Career Jobs Listing (`/career-jobs/{companyName}/{companyId}`)  
> **Status:** PROPOSED & PENDING APPROVAL ONLY. DO NOT IMPLEMENT V2 UNTIL APPROVED.

---

## 1. V2 Objective & Vision

The objective of **Career Jobs Listing V2** is to transform the public career portal into an ultra-fast, zero-N+1 query, high-concurrency engine capable of handling **10,000+ concurrent visitors and recruiters** while keeping MySQL database load near zero through intelligent Redis caching and optimized Elasticsearch aggregations.

### Performance Targets:
- **Database Queries Per Request:** Reduced from **53+ queries** to **$\le 2$ queries** (or 0 queries when fully Redis cached).
- **Response Time (TTFB):** $< 50\text{ ms}$ average response time.
- **Concurrency Target:** 10,000+ active concurrent sessions with 0 connection pool exhaustion.
- **Filter Retention:** 100% state retention across all pagination, search, and filter changes.

---

## 2. Proposed Architecture

```
[Client / Browser / Iframe Request]
       │
       ▼
[Route: /career-jobs/{companyName}/{companyId}]
       │
       ▼
[HomeController@careerJobPage V2 Service]
       │
       ├──► 1. Redis Tenant Cache (`company:career_settings:{id}`, `company:metadata:{id}`)
       │       └── If Cache Hit: 0 MySQL Queries for Branding, Location, Commercials, Countries
       │       └── If Cache Miss: Single Batch SQL Query + Cache Set (TTL 1 Hour)
       │
       ├──► 2. Elasticsearch Service (ELASTIC_JOBS)
       │       ├── Optimized Boolean Filters + Analyzed Multi-Match Search (No leading wildcards)
       │       └── Dynamic Aggregations (aggs for Departments, Locations, Experience)
       │
       └──► 3. Batch Eager Loading (MySQL / Cache)
               ├── Batch fetch skills via `job_metronics_skills` WHERE job_id IN (...)
               └── Batch fetch city locations via `client_city_jobs` WHERE job_id IN (...)
       │
       ▼
[Response Payload] ──► [Option A: Server-Side Rendered Blade (Initial Load)]
                   ──► [Option B: Clean JSON API Endpoint (AJAX Dynamic Filter)]
```

---

## 3. Detailed Technical Solutions (Problem → Solution → Risk → Testing)

### 3.1 N+1 Query Elimination & Eager Batch Loading

- **Problem:** Blade template executes 4 SQL queries per job card inside `@foreach($data)` (40 queries for 10 cards), including the heavy candidate application pipeline aggregate query in `GeneralMethod::openCompanyDetails()`.
- **Solution:**
  1. Extract job IDs from Elasticsearch search hits array: `$jobIds = array_column($data, 'id')`.
  2. Batch query skills: `DB::table('job_metronics_skills')->whereIn('job_id', $jobIds)->get()->groupBy('job_id')`.
  3. Batch query city locations: `DB::table('client_city_jobs')->leftJoin('city_list', ...)->whereIn('job_id', $jobIds)->get()->groupBy('job_id')`.
  4. Replace `GeneralMethod::openCompanyDetails()` with a lightweight cached company/client logo lookup using company metadata already loaded in memory.
- **Risk:** Minor risk of missing skills or logo fallback if shared recommendation IDs are mismatched.
- **Testing:** Verify via Laravel Debugbar / Query Log that total DB queries on page load drops to $\le 2$. Test logo rendering for both self-company jobs and cross-tenant recommended jobs.

---

### 3.2 Tenant Metadata & Static Settings Caching (Redis Strategy)

- **Problem:** Every visitor forces 11+ duplicate SQL queries for company metadata, client details, commercial rates, commercial status, and `company_career_settings` (which is queried twice in Blade).
- **Solution:** Cache tenant metadata in Redis under tag `tenant_{company_id}`:
  ```php
  $careerSettings = Cache::remember("company:career_settings:{$company_id}", 3600, function () use ($company_id) {
      return DB::table('company_career_settings')->where('company_id', $company_id)->first();
  });
  ```
  Invalidate cache automatically when company settings are saved in `ServiceCompanyAccountSettingsController`.
- **Risk:** Stale branding colors or logo if cache invalidation listener fails.
- **Testing:** Change tenant primary color in settings; verify instant update on career page after invalidation. Verify 0 MySQL queries executed on repeated career page loads.

---

### 3.3 Database Index Optimization Strategy

- **Problem:** Table scans on `client_city_jobs` and `commericial_details` due to missing database indexes on filter columns.
- **Solution:** Add required composite database indexes in database schema:
  - `ALTER TABLE client_city_jobs ADD INDEX idx_ccj_job_id (job_id);`
  - `ALTER TABLE commericial_details ADD INDEX idx_cd_company_comm (company_id, commtype);`
  - `ALTER TABLE job_metronics_skills ADD INDEX idx_jms_job_company (job_id, company_id);`
- **Risk:** Minimal index creation locks during schema migration.
- **Testing:** Run `EXPLAIN SELECT ...` on all 3 target queries to confirm `type: ref` or `type: range` and verify key usage.

---

### 3.4 Pagination State & Filter Query String Preservation

- **Problem:** Blade pagination URLs currently only append `custom_search` and `custom_hiringtype`, erasing active experience, location, department, and skill filters upon clicking Page 2.
- **Solution:** Build pagination URLs using Laravel request query builder `request()->except('page')`:
  ```php
  $queryParams = request()->except('page');
  $pageUrl = url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => $pageNumber]));
  ```
- **Risk:** Extremely low.
- **Testing:** Select "Remote" location filter and "Contract" employment filter; click Page 2; verify both filters remain checked and results filtered on page 2.

---

### 3.5 Elasticsearch Search & Aggregation Optimization

- **Problem:** Search query uses expensive leading wildcards (`*term*`), which bypass ES inverted indexes and consume high CPU under load. Sidebar filters use static hardcoded values.
- **Solution:**
  1. Replace wildcard queries with ES `multi_match` on `title`, `job_category_name`, `company_name` using `phrase_prefix` or edge_ngram analyzers.
  2. Implement ES aggregations (`aggs`) to dynamically compute available departments, locations, and experience bands for the specific tenant:
     ```json
     "aggs": {
       "departments": {"terms": {"field": "job_category_name.keyword"}},
       "locations": {"nested": {"path": "client_city_jobs"}, "aggs": {"cities": {"terms": {"field": "client_city_jobs.city_name.keyword"}}}}
     }
     ```
- **Risk:** Requires updating ES mapping to ensure `.keyword` fields exist for terms aggregations.
- **Testing:** Search for partial job titles; verify sub-10ms response from ES and accurate dynamic filter counts.

---

### 3.6 AJAX Endpoint & Smooth Iframe Experience

- **Problem:** Every filter selection triggers a full page reload, causing UI flicker and high bandwidth usage.
- **Solution:** Provide a lightweight JSON API endpoint `/api/v2/career-jobs/{companyId}`. On filter change, JS fetches JSON and re-renders job card list dynamically without full page reload. Retain Blade SSR for initial SEO page load. Re-trigger `window.parent.postMessage({frameHeight: ...})` after dynamic rendering.
- **Risk:** Requires maintaining consistent rendering logic between Blade SSR and JS template string/component.
- **Testing:** Check browser network tab on filter toggle; verify asynchronous JSON response with $< 30\text{ ms}$ latency and instant iframe height adjustment.

---

## 4. Concurrency & Server Tuning Strategy (10,000+ Recruiters)

### 4.1 System Resource Allocation

```
8 CPU Cores / 31.34 GB RAM / 386.4 GB Disk (Ubuntu 24 + aaPanel 8.0.3)
 ├── Octane / FrankenPHP Workers: 16 Workers (2 × CPU Cores)
 ├── PHP OPcache: 512 MB memory, JIT enabled (opcache.jit=tracing)
 ├── MySQL InnoDB Buffer Pool: 18 GB RAM (57% of total RAM)
 ├── Elasticsearch Heap Size: 8 GB RAM (25% of total RAM)
 └── Redis In-Memory Allocation: 2 GB RAM (Dedicated tenant cache)
```

### 4.2 Traffic Scaling Mechanics
1. **0 DB Queries for Cached Portal Loads:** 95%+ of public candidate traffic hits Redis-cached tenant metadata and Elasticsearch indices.
2. **Connection Protection:** MySQL pool utilization stays under 5% during high concurrency spikes.
3. **Static File Handling:** Nginx direct delivery for Metronic bundle assets and images with 30-day browser caching headers.

---

## 5. Non-Negotiable Business Logic Preservation Matrix

| Feature / Logic | Business Requirement | V2 Verification Standard |
| :--- | :--- | :--- |
| **Tenant Customization** | Primary color, logo, favicon, template, hero images must match `company_career_settings`. | Visually compare V1 and V2 rendered HTML styling. |
| **Shared Recommendations** | Cross-company shared jobs must render receiving company name and handle `final_status` removals. | Verify job card display for shared jobs between Company A and Company B. |
| **Salary & Currency** | Format salary via `thousandsCurrencyFormat` and append correct currency symbol (`₹`, `$`, etc.). | Confirm `$10K – $20K/M` formatting matches V1 output exactly. |
| **Job Badging** | Active $\le 3$ days = `New`; Active $> 3$ days = `Hiring`; Inactive = `Closed`; Removed = `Removed`. | Test badge assignment logic across all 4 status conditions. |
| **Apply & Details Links** | Links must point to `/job-details/{id}/{company_id}` and `/job-apply/{id}/{company_id}`. | Click Apply/Details buttons; verify target URL parameters. |
| **Iframe Frame Height** | PostMessage height reporting to parent frame on load and resize. | Embed in test iframe; verify postMessage fires on load and filter update. |

---

## 6. Phase-Wise Implementation Plan

> **Note:** Implementation MUST proceed strictly in phases after explicit user approval. Do NOT run `RefreshDatabase`.

| Phase | Module / Task | Description | Deliverable / Output | Estimated Impact |
| :---: | :--- | :--- | :--- | :--- |
| **Phase 1** | **Database Schema & Indexing** | Add missing indexes to `client_city_jobs`, `commericial_details`, and `job_metronics_skills` in schema. | Updated `mysql-schema.sql` index definitions | Eliminates full table scans |
| **Phase 2** | **Redis Tenant Caching Layer** | Implement Redis caching for `company_career_settings`, company branding, client info, and commercial rates. | Cached metadata repository in `HomeController` | 11 Controller SQL queries $\rightarrow$ 0 queries |
| **Phase 3** | **Controller Batch Eager-Loading** | Extract ES job IDs and batch-fetch skills and city locations in 2 single `WHERE IN` SQL queries. Remove `openCompanyDetails()` from Blade loop. | Refactored `HomeController@careerJobPage` | 40 Blade loop SQL queries $\rightarrow$ 2 batch queries |
| **Phase 4** | **Blade Template & Pagination Fix** | Update `career-job-page.blade.php` to remove inline SQL queries and preserve all filter query parameters across pagination links. | Clean, zero-SQL Blade template | Fixes broken pagination filter state |
| **Phase 5** | **Elasticsearch & Filter Optimization** | Upgrade search query to `multi_match` without leading wildcards. Add dynamic terms aggregations for sidebar filters. | Dynamic ES filter payload | Sub-10ms search execution |
| **Phase 6** | **AJAX Endpoint & Iframe Polish** | Add clean JSON API endpoint `/api/v2/career-jobs/{companyId}` for asynchronous filtering and seamless iframe height adjustment. | Instant AJAX portal updates | Zero full page reloads on filter click |
| **Phase 7** | **Verification & Concurrency Testing** | Perform syntax checks (`php -l`), Pint formatting, route validation, and verification of zero N+1 queries. | Completed Walkthrough & Verification Report | Ready for 10K+ concurrent load |

---

## 7. Testing, Rollout & Rollback Strategy

### 7.1 Automated & Manual Testing Rules
- **Syntax Verification:** `php -l app/Http/Controllers/HomeController.php`
- **Code Style:** `./vendor/bin/pint --test`
- **Database Rules:** **DO NOT USE `RefreshDatabase`** (destructive to local environment database). Use transactional tests or manual database verifications.
- **Tenancy Validation:** Assert job listing results for Company ID 2 cannot leak jobs from Company ID 3.

### 7.2 Rollout Plan
1. Apply database indexes to target environment.
2. Deploy refactored `HomeController` and `career-job-page.blade.php`.
3. Flush tenant Redis cache tag (`php artisan cache:clear`).
4. Perform smoke test on live page URL: `/career-jobs/4Bell-Technology/2`.

### 7.3 Instant Rollback Plan
1. Revert Git commit for `HomeController.php` and `career-job-page.blade.php`.
2. Clear Redis cache.
3. System immediately returns to original V1 behavior with 0 database schema corruption (since schema indexes are non-breaking additions).
