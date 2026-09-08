# Agency Listing Page Audit (V1 Architecture)

**Page URL:** `http://localhost/git/JWMaincode28June2026/service_company/consultancy`  
**Route Names:** `service_company.consultancy.index`, `service_company.consultancy.data_ajax`  
**Audit Date:** August 11, 2026  
**Status:** Audit Only — No Code/DB Changes Applied  

---

## 1. Current Files & Architecture

### Key Files Involved
1. **Routes File:** [routes/service.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/routes/service.php#L381-L392)
2. **Controller:** [ServiceCompanyConsultancyController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php)
3. **Helper Class:** [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php)
4. **Elastic Service:** [ElasticService.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/ElasticService.php)
5. **Blade Views:**
   - Container Layout: [index.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/consultancy/index.blade.php)
   - Ajax Table Partial: [staffing-partner-ajax.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/staffing-partner-ajax.blade.php)
   - Pagination Component: [pagination.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/pagination.blade.php)
6. **Database Schema:** [mysql-schema.sql](file:///c:/xampp/htdocs/git/JWMaincode28June2026/database/schema/mysql-schema.sql)

### End-to-End Architectural Flow

```
[ HTTP GET /service_company/consultancy ]
                 │
                 ▼
 [ ServiceCompanyConsultancyController@index ]
                 │
                 ├──► $this->spartnerDataCnt()  ──► [ 9 MySQL Queries + 2 ES Aggregations ]
                 └──► Renders index.blade.php
                 │
                 ▼ (Client-side JS DOM Ready)
 [ JS fetchstaffingpartner() ]
                 │
                 ▼ (AJAX GET /service_company/consultancy_ajax/data)
 [ ServiceCompanyConsultancyController@data_ajax ]
                 │
                 ├──► ES search ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX (size: 10000)
                 ├──► ES search ELASTIC_CLIENT_LIST (paginated)
                 └──► Renders HTML view 'staffing-partner-ajax'
                           │
                           ▼ (Inside Blade @foreach Loop for N rows)
                           ├──► GeneralMethod::getRelationData()  [MySQL]
                           ├──► DB::table('td_industries')        [MySQL]
                           ├──► DB::table('client_location')       [MySQL]
                           ├──► GeneralMethod::getCountryName()   [MySQL/Array]
                           ├──► DB::table('recommendation_relation') [MySQL Join/Agg]
                           ├──► GeneralMethod::agencyJobsCountAllstatus() [ES HTTP Query]
                           └──► GeneralMethod::agencyCandidatesCountList() [ES HTTP Query]
```

---

## 2. Query Audit & N+1 Analysis

### A. Initial Page Load (`index()` Method)
During the initial page hit to `/service_company/consultancy`, the controller executes `spartnerDataCnt()` to calculate stats for top summary cards. This executes:
1. `DB::table('companies')->leftJoin('staffing_company_relation')->leftJoin('clients')...` (Count connected agencies)
2. `DB::table('staffing_company_invitation')->where('from_company_id', $company_id)->where('to_invitation_status', '!=', 1)->count()` (Count pending sent invitations)
3. `DB::table('staffing_company_relation')->where('to_company_id', $company_id)->where('final_status', 1)->count()` (Count direct clients)
4. `Client::where('self_company', '!=', 1)->where('company_id', $company_id)->count()` (Count custom clients)
5. `DB::table('staffing_company_relation')->whereThisMonth()->count()` (Agencies added this month)
6. `DB::table('staffing_company_relation')->whereSubDays(7)->count()` (Newly onboarded agencies)
7. `DB::table('staffing_company_relation')->pluck('to_company_id')` (Fetch connected agency IDs)
8. **Elasticsearch Aggregation Search on `ELASTIC_JOBS` index:** Nested cardinality aggregation for `workingOnJobs`.
9. **Elasticsearch Aggregation Search on `ELASTIC_JOBS` index:** Nested cardinality aggregation for `workingOnJobsThisMonth`.
10. `DB::table('job_applications')->join('staffing_company_relation')->join('jobs')...` (Actively submitting in last 30 days)
11. `DB::table('job_applications')->join('staffing_company_relation')->join('jobs')...` (Actively submitting today)

**Summary:** **9 SQL queries + 2 ES aggregation HTTP calls** executed synchronously on initial HTML page load.

---

### B. AJAX Data Retrieval (`data_ajax()` & Blade Rendering)

When the frontend sends the AJAX request to `consultancy_ajax/data`:
1. `data_ajax()` issues an ES search to `ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX` requesting `size => 10000` to fetch relation records.
2. `data_ajax()` issues a primary ES query to `ELASTIC_CLIENT_LIST` using `from` and `size` (default page size 10).
3. The response array is passed to `staffing-partner-ajax.blade.php`.
4. **The Blade N+1 Nightmare:** Inside `staffing-partner-ajax.blade.php`, every single row in `@foreach ($data as $rowArr)` executes raw database queries inside `@php` tags:
   - Query 1: `GeneralMethod::getRelationData($row->company_id, $company_id)` (MySQL `staffing_company_relation` table query)
   - Query 2: `DB::table('td_industries')->where('ind_id', '=', $row->industries)->first()` (MySQL industry query)
   - Query 3: `DB::table('client_location')->leftJoin('city_list')->leftJoin('state_list')...` (MySQL location query)
   - Query 4: `GeneralMethod::getCountryName($locInfo->country_id)` (MySQL country lookup)
   - Query 5: `DB::table('recommendation_relation')->join('jobs')->join('add_job_wise_condidates')...` (MySQL candidate application aggregation query)
   - Query 6: `GeneralMethod::agencyJobsCountAllstatus($row->company_id, $company_id)` (**Separate HTTP API search to Elasticsearch `ELASTIC_JOBS`**)
   - Query 7: `GeneralMethod::agencyCandidatesCountList($row->company_id, $company_id)` (**Separate HTTP API search to Elasticsearch `ELASTIC_CANDIDATES`**)

#### Total Operations Per AJAX Request (Page Size N = 10):
- **MySQL Queries:** $1 + 1 + (5 \times 10) = \mathbf{52\text{ queries}}$
- **Elasticsearch HTTP Calls:** $1 + 1 + (2 \times 10) = \mathbf{22\text{ calls}}$
- **Total Remote Network/DB Roundtrips:** **74 operations per pagination click!**
- If page size is increased to 50: **354 remote calls per request!**

---

## 3. Database Indexes Audit

Inspecting `database/schema/mysql-schema.sql`:

1. **`staffing_company_relation`**
   - Current Index: `KEY idx_staffing_company_relation (to_company_id, from_company_id)`
   - Problem: Left-prefix column is `to_company_id`. Queries filtering `WHERE from_company_id = ? AND final_status = 1` CANNOT utilize this index effectively.
   - Status: **Confirmed Index Mismatch.**

2. **`staffing_company_invitation`**
   - Current Index: `PRIMARY KEY (id)` (No secondary indexes)
   - Query: `WHERE from_company_id = ? AND to_invitation_status != 1`
   - Status: **Confirmed Missing Index (Full Table Scan).**

3. **`client_location`**
   - Current Index: `PRIMARY KEY (id)` (No index on `client_id` or `company_id`)
   - Query in Blade loop: `WHERE client_id = ?`
   - Status: **Confirmed Missing Index (Full Table Scan on every row render).**

4. **`recommendation_relation`**
   - Current Indexes: `idx_recommendation_relation (to_company_id, job_id)`, `idx_recommendation (job_id, to_user_id)`
   - Query in Blade loop: `WHERE from_company_id = ? AND to_company_id = ? AND final_status = 1`
   - Status: **Confirmed Missing Index on `from_company_id`.**

---

## 4. Elasticsearch, Caching, & Frontend Performance

### Elasticsearch Usage
- **Over-fetching:** Relation query fetches `size: 10000` relation hits directly into memory array instead of using indexed fields or paginated filters.
- **Redundant Conditions:** `data_ajax()` builds deeply nested `must` / `should` boolean wrappers with duplicate `self_company = 1` rules.
- **Sub-queries inside loop:** Calling `agencyJobsCountAllstatus` and `agencyCandidatesCountList` via individual ES HTTP calls inside Blade loops bypasses Elasticsearch bulk aggregation design.

### Redis / DragonflyDB Caching
- Caches the fully rendered HTML string (`responseData.html`) using key: `ats:allconsultancy:{user_id}:{version}:{hash}`.
- **Issue:** Fragmented caching of rendered HTML blocks prevents reusing agency data for REST APIs, mobile apps, or alternative UI layouts.
- **Invalidation:** Uses key versioning (`allconsultancy_version_user_{user_id}`).

### Frontend & Blade Audit
- Data is rendered server-side into HTML strings and injected into `#staffingpartnerajax`.
- Table interactivity relies on client-side JS handlers (`openModal`, `show-detail-direct`).
- Heavy CSS overrides directly in `index.blade.php` head script section.

---

## 5. Server Configuration & Infrastructure Audit

### Server Environment (Target Staging/Production Architecture)
- **Operating System:** Ubuntu 24.04 LTS
- **Control Panel:** aaPanel 8.0.3
- **CPU Cores:** 8 Cores
- **RAM:** 31.34 GB
- **Disk Storage:** 386.4 GB (NVMe/SSD)
- **Web Server:** Apache / Nginx with PHP 8.x (PHP-FPM)
- **Database:** MySQL 8.0 / MariaDB
- **Search Engine:** Elasticsearch
- **Cache Store:** DragonflyDB / Redis

### Concurrency Risk Analysis (10,000+ Concurrent Recruiters)
Under high concurrency (10,000 active recruiters):
1. **PHP-FPM Worker Exhaustion:** Rendering server-side HTML with 70+ DB/ES calls per request will instantly saturate PHP-FPM process pools (`max_children`), leading to 504 Gateway Timeouts.
2. **Database Connection Exhaustion:** 52 SQL queries per page view across 10,000 recruiters = 520,000 SQL queries per page load cycle. MySQL `max_connections` limit will be exceeded immediately.
3. **Elasticsearch Thread Pool Rejection:** Opening 20+ ES HTTP connections per user request will exhaust Elasticsearch `search` thread pools (`EsRejectedExecutionException`).

---

## 6. Classification of Audit Findings

### A. Confirmed Problems
1. **Severe N+1 in Blade View:** 5 SQL queries + 2 ES HTTP searches executed inside `@foreach` loop in `staffing-partner-ajax.blade.php`.
2. **Missing Database Indexes:** `client_location` (missing `client_id` index), `staffing_company_invitation` (missing `from_company_id` index), `staffing_company_relation` (left-prefix index mismatch).
3. **Synchronous Page Load Overheads:** `spartnerDataCnt()` fires 9 SQL queries + 2 ES aggregation searches synchronously on every initial page hit.
4. **Hardcoded ES Result Size:** `size: 10000` in relation search allocates excessive array memory in PHP.

### B. Potential Problems
1. **Wildcard ES Search Performance:** `match_phrase_prefix` and `wildcard` on nested location fields may degrade ES latency as candidate/agency catalog expands.
2. **Redis HTML Payload Bloat:** Storing raw HTML in Redis consumes significantly more memory than caching compact JSON payloads.

### C. Not Measured
1. **Exact Latency (ms):** MySQL query execution times without real-time APM telemetry (e.g. Laravel Telescope / New Relic).
2. **Exact Redis Memory Consumption:** Total byte footprint of `ats:allconsultancy` cache keys on production DragonflyDB instance.

---

## 7. Non-Negotiable Business Logic & Data Contracts

The following core business logic MUST remain 100% identical in V2:
1. **Agency Relation Status:** Only show staffing partners where `final_status = 1` and `from_company_id` matches logged-in company.
2. **Summary Counter Calculations:**
   - Connected Agencies count.
   - Sent pending invitations count (`to_invitation_status != 1`).
   - Direct clients count.
   - Custom clients count (`self_company != 1`).
   - Newly onboarded (last 7 days) and added this month.
3. **Job Counts per Agency:** Active, Closed, and Hold jobs.
4. **Candidate Pipeline Metrics per Agency:** Sourced (stage 1), Evaluated, Interviews, Offers, Joined (stage 6).
5. **Tenancy Isolation:** Strict filtering by `auth()->user()->company_id`.

---

## 8. Audit Recommendations Summary

| # | Problem | Proposed Solution | Risk Level | Verification & Testing |
|---|---|---|---|---|
| 1 | N+1 queries in Blade view loop | Pre-aggregate agency job/candidate metrics into ES or load via bulk SQL query in controller | Medium | Verify job & candidate count numbers match V1 exactly |
| 2 | Missing DB index on `client_location.client_id` | Add index `idx_client_location_client_id (client_id)` | Low | Run `EXPLAIN` to confirm index usage |
| 3 | Index prefix mismatch on `staffing_company_relation` | Add index `idx_scr_from_final (from_company_id, final_status)` | Low | Run `EXPLAIN` on `spartnerDataCnt` queries |
| 4 | Synchronous stat card load in `index()` | Cache stat cards in Redis for 10 min or fetch via async AJAX component | Low | Verify initial HTML response time drops significantly |
| 5 | HTML rendering inside backend AJAX | Return JSON payload from controller and render via JS or clean Blade loop without sub-queries | Medium | Verify table rendering and client UI interactions |
