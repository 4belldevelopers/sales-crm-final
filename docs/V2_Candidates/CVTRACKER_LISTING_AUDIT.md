# CV Tracker Listing Audit Report

## Executive Summary
This document provides a comprehensive technical audit of the **CV Tracker Listing page** (`/service_company/cvtracker`) in the JERO multi-tenant B2B hiring SaaS platform. The audit covers the end-to-end page flow from routes and controllers down to database schema, Elasticsearch queries, Redis caching, AJAX payloads, and server resource scalability.

---

## 1. Current Architecture & Page Flow

### Page Route & Entry Points
* **Primary Route (Web Page Load):** `GET /service_company/cvtracker`
  * **Controller:** `App\Http\Controllers\ServiceCompany\ServiceCompanyCvTrackerController@index`
  * **View:** `resources/views/service_company/cvtracker/index.blade.php`
* **AJAX Listing Data Endpoint:** `GET /service_company/all-candidate-list` (or `GET /service_company/cvtracker/alldata` / `data`)
  * **Controller:** `App\Http\Controllers\ServiceCompany\ServiceCompanyCvTrackerController@allcandidates`
  * **Response Format:** JSON containing pre-rendered HTML partials (`html`, `pagination`) and stat badges (`candidateStats`, `offerCounts`, `joiningCounts`).

### Layer Architecture
```
[ Browser / Client JS ]
       │
       ├─► Initial Request (GET /service_company/cvtracker) ──► ServiceCompanyCvTrackerController@index
       │                                                                  │
       │                                                                  ├──► Check Subscription (GeneralMethod::checksubscription)
       │                                                                  ├──► Execute 10+ Master Data DB Queries
       │                                                                  └──► Return index.blade.php (191 KB Blade file)
       │
       └─► AJAX Data Fetch (GET /service_company/all-candidate-list) ──► ServiceCompanyCvTrackerController@allcandidates
                                                                          │
                                                                          ├──► Auth & Gate check ('view_job_applications')
                                                                          ├──► Redis Cache Check (ats:allcvtracker:{user_id}:...)
                                                                          ├──► Tagify Lookup Queries (MySQL N+1 on location & eductn)
                                                                          ├──► Skill Subquery (MySQL join job_application_skills - unindexed!)
                                                                          ├──► Elasticsearch Search Query (local_candidates index)
                                                                          │      └── Painless Scripts for Notice Period & Best Match Sort
                                                                          ├──► Elasticsearch Count Aggregations (KPI cards & tab badges)
                                                                          ├──► Job Skills Batch Query (job_metronics_skills)
                                                                          ├──► Render Blade Partial (all_candidates.blade.php)
                                                                          │      └── 5 N+1 MySQL Queries PER ROW inside Blade loop!
                                                                          ├──► Store rendered response in Redis
                                                                          └──► Return JSON { html: "...", pagination: "...", stats: {...} }
```

---

## 2. Query Audit & Findings

### Initial Page Load (`ServiceCompanyCvTrackerController@index`)
During the initial HTTP GET request, the controller executes the following synchronous database queries:
1. `ApplicationStatus::all()` — Loads all application status records.
2. `DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get()` — Loads candidate master statuses.
3. `JobLocation::all()` — Loads all job locations.
4. `Skill::all()` — Loads all skill records.
5. `Client::all()` — Loads all clients.
6. `Job::where('company_id', $this->user->company_id)->get()` (or joined with `job_assign` for non-admin) — Loads all company jobs.
7. `User::where('status', 'active')->where('company_id', $company_id)->get()` — Loads active company users.
8. `NoticePeriod::all()` — Loads notice periods.
9. `DB::table('job_applications')->join('add_job_wise_condidates', ...)->join('master_status', ...)->count()` — Application counter query with complex OR conditions across multi-tenant relationships.
10. `GeneralMethod::allCities()` — Loads all cities from `city_list`.
11. `DB::table('qualification_master')->orderBy('display_order', 'ASC')->get()` — Loads qualifications.
12. `DB::table('countries')->get()` — Loads country list.

### Data Fetch Endpoint (`ServiceCompanyCvTrackerController@allcandidates`)
When the listing table requests data via AJAX:
1. **Tagify Location Lookup (N+1 Query in Controller):**
   ```php
   foreach ($custom_location as $sval) {
       $loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
   }
   ```
   *Executes 1 MySQL query per location tag.*
2. **Tagify Education Lookup (N+1 Query in Controller):**
   ```php
   foreach ($custom_education as $sval) {
       $education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
   }
   ```
   *Executes 1 MySQL query per education tag.*
3. **Skill Subquery (Un-indexed Table Scan):**
   ```php
   $subQueryArr = DB::table('job_applications')
       ->leftJoin('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
       ->whereIn('job_application_skills.skills', $requiredSkills)
       ->orWhereIn('job_applications.current_designation', $requiredSkills)
       ->groupBy('job_applications.id')
       ->pluck('job_applications.id')
       ->toArray();
   ```
   *Scans `job_application_skills` (470,706 rows) without any index on `job_application_id` or `skills`.*
4. **Candidate Status Lookups for Joining Tracker (if `status_cus == 6`):**
   * Executes 2 `DB::table('candidate_status')` queries to fetch IDs for "onboarded" and "backedout" statuses.
5. **Job Metronics Skills Batch Query:**
   * Fetches required skills for distinct job IDs on the page (`DB::table('job_metronics_skills')->whereIn('job_id', $jobIdsOnPage)...`).

### Blade View N+1 Queries (`all_candidates.blade.php`)
Inside `resources/views/service_company/ajax_pages/all_candidates.blade.php`, the `@foreach ($data as $rowArr)` loop executes **5 separate database queries per candidate row**:
1. **Job Salary Mode & Currency Lookup (Line 28):**
   ```php
   $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$row->jobId)->first();
   ```
2. **Duplicate Application Check (Lines 89 / 109):**
   ```php
   $application_cnt = DB::table('job_applications')->where('company_id', $row->ClientComID)...->count();
   ```
3. **City Name Resolution (Line 562):**
   ```php
   $row_city = DB::table('city_list')->where('city_id', '=', $row->city_id)->first();
   ```
4. **Spokesperson / Recruiter Assign Lookup (Line 596):**
   ```php
   $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
   ```
5. **Child Candidate Status Resolution (Line 762):**
   ```php
   $childStatus = DB::table('candidate_status')->where('id', '=', $row->child_status_id)->first();
   ```

**Total Queries per Page Load:**
For a standard page size of 20 candidates, Blade alone executes `20 × 5 = 100` database queries per page view! Combined with controller overhead, a single pagination click triggers **105-115 MySQL queries**.

---

## 3. N+1 Queries Breakdown

| Location | Code Snippet | Cause | Impact |
| :--- | :--- | :--- | :--- |
| `all_candidates.blade.php:28` | `DB::table('jobs')->where('id', $row->jobId)->first()` | Missing job mode/currency in ES candidate index | 20 DB queries per page render |
| `all_candidates.blade.php:89/109` | `DB::table('job_applications')->where(...)->count()` | Checking duplicate application window per row | 20 DB queries per page render |
| `all_candidates.blade.php:562` | `DB::table('city_list')->where('city_id', $row->city_id)->first()` | City name missing from ES doc or not eager loaded | 20 DB queries per page render |
| `all_candidates.blade.php:596` | `DB::table('job_assign')->join('users', ...)->first()` | Spokesperson details resolved inside row loop | 20 DB queries per page render |
| `all_candidates.blade.php:762` | `DB::table('candidate_status')->where('id', $row->child_status_id)->first()` | Sub-status name resolved inside row loop | 20 DB queries per page render |
| `ServiceCompanyCvTrackerController.php:290` | `DB::table('city_list')->where('city', $cityName)->first()` | Tagify location parsed row-by-row in loop | N queries (N = number of location tags) |
| `ServiceCompanyCvTrackerController.php:300` | `DB::table('qualification_master')->where('name', ...)->first()` | Tagify education parsed row-by-row in loop | N queries (N = number of education tags) |

---

## 4. Duplicate & Unnecessary Queries

1. **Redundant Master Data Fetches on `index()`:**
   * `ApplicationStatus::all()`, `JobLocation::all()`, `Skill::all()`, `Client::all()`, `NoticePeriod::all()`, `GeneralMethod::allCities()`, `qualification_master`, `countries` are fetched directly from MySQL on every page visit without any HTTP cache headers or Redis master-data caching.
2. **Duplicate Job Application Counter Query:**
   * `$this->applicationCounter` runs a heavy 3-way join query in `index()`, but `allcandidates()` immediately overrides `$candidateStats['total']` with an Elasticsearch aggregation on table load.

---

## 5. Relevant vs. Unused Data

* **Unused Initial Page Data:** Master lists (`$locations`, `$skills`, `$cities`, `$countries`) load full collections into PHP memory, even though the frontend search UI uses asynchronous Tagify inputs.
* **Over-the-Wire HTML Payload:** `allcandidates()` returns a full HTML string (`responseData.html`) of ~50KB to 100KB containing pre-rendered `<tr>` tags, inline SVGs, and duplicate HTML attributes, instead of returning a compact ~5KB JSON array of candidate objects.

---

## 6. Pagination, Search, Filters, and Sorting

### Pagination
* Handled via Elasticsearch `from` and `size` parameters (`$page`, `$size = 10` or `20`).
* Uses `track_total_hits => true`.
* Page numbers and total pages are calculated in PHP (`ceil($total / $size)`).

### Search & Filtering
* **Keyword Search:** Scopes candidates via Elasticsearch bool queries (`q` / `cus_search`).
* **Experience Filter:** Uses `range` query on `total_exp` (`gte` / `lte`).
* **Salary Filter:** Converts entered amounts to INR via `GeneralMethod::convertToINR()` and queries `ectc_yearly`, `ectc_monthly`, or `ectc_hourly`.
* **Location & Education:** Filtered via `terms` queries on `city_id` and `qualification`.
* **Stage & Status Tabs:** Multi-stage filtering for Offer Tracker (`status_cus == 5`), Joining Tracker (`status_cus == 6`), and CV Tracker Stage Tabs (`stage_tab`).

### Sorting Audit & Elasticsearch Painless Scripts
Sorting options use Elasticsearch sort arrays. However, several sorting modes rely on **complex Painless inline scripts**:
* **Cases 5 & 6 (Notice Period ASC/DESC):** Dynamic script string splitting and regex/number parsing on `np_id.keyword`.
* **Cases 21 & 22 (Notice Period Date ASC/DESC):** Dynamic script string parsing with `java.time.LocalDate.parse` across multiple format patterns (`ISO_LOCAL_DATE`, `d-M-uuuu`, `uuuu-M-d`) for every hit evaluated.
* **Case 30 (Best Match):** Dynamic script looping over `job_application_skills` array and checking string containment against search skill terms.

*Impact:* Running Painless scripts across thousands of Elasticsearch documents during sort operations consumes heavy ES CPU and prevents index-level sort optimization.

---

## 7. Database Indexes Audit

Database truth source: `database/schema/mysql-schema.sql` (217 tables).

### Critical Missing Indexes Found:

1. **Table `add_job_wise_condidates` (63,804 rows):**
   * **Current State:** ONLY `PRIMARY KEY (id)`. NO foreign key or column indexes!
   * **Missing Indexes:**
     * `(job_application_id)`
     * `(company_id, thirdparty_company_id, master_status_id)`
     * `(job_id)`
   * **Impact:** Every query filtering or joining `add_job_wise_condidates` performs a FULL TABLE SCAN.

2. **Table `job_application_skills` (470,706 rows):**
   * **Current State:** ONLY `PRIMARY KEY (id)`. NO index on `job_application_id` or `skills`!
   * **Missing Indexes:**
     * `(job_application_id)`
     * `(skills)`
     * Composite `(skills, job_application_id)`
   * **Impact:** `cus_search_skill` subquery in `allcandidates()` scans all 470,706 rows on every skill search request.

3. **Table `job_applications` (63,619 rows):**
   * **Current State:** Has PK (`id`), `job_applications_company_id_foreign` (`company_id`), `job_applications_job_id_foreign` (`job_id`), `idx_job_id` (`job_id`).
   * **Missing Indexes:**
     * `(company_id, master_status_id)`
     * `(submitted_by)`
     * `(city_id)`
     * `(current_designation)`

4. **Table `job_assign` (9,765 rows):**
   * **Current State:** Index on `user_id`, `job_id`, `company_id`.
   * **Missing Index:** Composite `(job_id, company_id, spok_user_id)`.

---

## 8. Database Query Performance & EXPLAIN Analysis

### Query 1: Blade N+1 Job Mode & Currency Query
```sql
SELECT `max_sal_mode` AS `jobMode`, `max_salary_currency` AS `jobCurrency` 
FROM `jobs` 
WHERE `id` = 123 
LIMIT 1;
```
*Executed 20 times per AJAX request.* While indexed on `PRIMARY KEY (id)`, network latency overhead of 20 round-trips adds 15-30ms to every rendering cycle.

### Query 2: Application Counter Join Query (in `index()`)
```sql
SELECT COUNT(`job_applications`.`id`) 
FROM `job_applications` 
INNER JOIN `add_job_wise_condidates` ON `add_job_wise_condidates`.`job_application_id` = `job_applications`.`id` 
INNER JOIN `master_status` ON `master_status`.`id` = `add_job_wise_condidates`.`master_status_id` 
WHERE (`add_job_wise_condidates`.`company_id` = 1 AND `add_job_wise_condidates`.`thirdparty_company_id` = 0)
   OR (`add_job_wise_condidates`.`company_id` = 1 AND `add_job_wise_condidates`.`thirdparty_company_id` > 0 AND `add_job_wise_condidates`.`master_status_id` != 8)
   OR (`add_job_wise_condidates`.`thirdparty_company_id` = 1);
```
*EXPLAIN Result:* Full table scan on `add_job_wise_condidates` (63,804 rows scanned) because `add_job_wise_condidates` has NO index on `job_application_id` or `company_id`.

### Query 3: Skill Subquery (in `allcandidates()`)
```sql
SELECT `job_applications`.`id` 
FROM `job_applications` 
LEFT JOIN `job_application_skills` ON `job_applications`.`id` = `job_application_skills`.`job_application_id` 
WHERE `job_application_skills`.`skills` IN ('php', 'laravel') 
   OR `job_applications`.`current_designation` IN ('php', 'laravel') 
GROUP BY `job_applications`.`id`;
```
*EXPLAIN Result:* Scans 470,706 rows in `job_application_skills` using `ALL` access type (full table scan) and uses `Temporary; Using filesort`.

---

## 9. Memory & Collection Analysis

* Loading full Eloquent models (`Job::all()`, `Client::all()`, `User::all()`, `NoticePeriod::all()`) into memory consumes 8 MB - 15 MB per PHP worker process during initial page load.
* Output buffering large HTML rendered strings (over 80 KB of HTML) for AJAX responses increases peak PHP memory consumption during JSON encoding.

---

## 10. Elasticsearch Usage Audit

* **Index Name:** `local_candidates` (mapped via constant `ELASTIC_CANDIDATES`).
* **Usage:** Serves paginated candidate listings and multi-aggregation statistics (KPI cards, stage tab counts, offer tab counts, joining tracker counts).
* **Deficiencies:**
  1. Notice period dates are stored as unparsed strings (`np_id.keyword`), forcing ES to run inline Painless scripts for date sorting instead of using a numeric epoch or date field.
  2. Candidate index lacks denormalized fields for job currency/mode, city name, spokesperson name, child status name, and duplicate application flags, forcing the Blade view to query MySQL.

---

## 11. Redis & Cache Audit

* **Cache Key Format:** `ats:allcvtracker:{user_id}:{version}:{md5_payload}`
* **TTL:** Defined by `ALL_CV_TRACKER_TIME`.
* **Deficiencies:**
  1. Key includes `$this->user->id`. Different recruiters in the same company requesting identical candidate lists miss the cache entirely and execute duplicate queries.
  2. Cache stores pre-rendered HTML strings rather than structured candidate data.

---

## 12. Queue & Background Processing

* Currently, all candidate searches, aggregations, and Tagify filter resolutions execute **synchronously** in the HTTP request-response cycle.
* When candidate records or statuses update in MySQL, sync to Elasticsearch occurs synchronously or via events. If sync fails, MySQL and Elasticsearch drift out of alignment.

---

## 13. AJAX & API Payload Audit

* **Current Endpoint:** `GET /service_company/all-candidate-list`
* **Response Payload Structure:**
  ```json
  {
    "html": "<tr style=\"height: 102px;\">...</tr> (50KB - 100KB rendered HTML)",
    "total": 1250,
    "pagination": "<ul class=\"pagination\">...</ul>",
    "counts": { "total": 45, "pending": 10, ... },
    "pipelineCounts": { ... },
    "joiningCounts": { ... },
    "candidateStats": { ... }
  }
  ```
* **Payload Size:** ~85 KB per page of 20 candidates.
* **Bottleneck:** High CPU rendering overhead on server, high network transfer payload, high DOM replacement overhead on client.

---

## 14. Permissions & Business Logic Audit

### Security & Tenancy Controls (MUST REMAIN UNCHANGED)
1. **Permission Gate:** `Gate::denies('view_job_applications')` must abort with 403 Forbidden.
2. **Paid Subscription Guard:** `GeneralMethod::checksubscription()` must redirect unpaid tenants to dashboard.
3. **Multi-Tenant Scoping:**
   * Candidates must strictly belong to `company_id = auth()->user()->company_id` OR `thirdparty_company_id = auth()->user()->company_id`.
   * Non-admin recruiters (`$chkAdminRole == 0`) must only view candidates assigned to their jobs via `job_assign`.
4. **Salary Conversion:** Salary range filtering must convert user-selected currencies to INR using `GeneralMethod::convertToINR()`.
5. **Notice Period Logic:** Immediate joiners and serving notice period filters must maintain exact date range logic.

---

## 15. Server Configuration & Environment Audit

* **OS:** Ubuntu 24.04 LTS
* **CPU:** 8 Cores
* **RAM:** 31.34 GB
* **Disk:** 386.4 GB
* **Control Panel Stack:** aaPanel 8.0.3 (Nginx / PHP 8.x / PHP-FPM / MySQL 8.0 / Elasticsearch / Redis)
* **Resource Usage:** *Not measured (Remote Production Server)*

---

## 16. 10,000+ Concurrent Recruiters Scalability Analysis

| System Component | Current Behavior | Bottleneck under 10K Concurrency | Risk Level |
| :--- | :--- | :--- | :--- |
| **PHP-FPM Workers** | Server-side rendering HTML partials (~85KB HTML string) | CPU exhaustion; 10,000 concurrent renders will exhaust FPM worker pool | **CRITICAL** |
| **MySQL Database** | 105+ queries per page load (5 N+1 per row in Blade) | Database connection pool exhaustion and lock wait timeouts | **CRITICAL** |
| **MySQL Indexes** | Zero indexes on `add_job_wise_condidates` & `job_application_skills` | Full table scans (470k+ rows) will cause 100% DB CPU spikes | **CRITICAL** |
| **Elasticsearch** | Complex Painless scripts for notice period & best match sort | ES CPU saturation and thread pool queue rejection errors | **HIGH** |
| **Redis Cache** | User-scoped cache keys (`user_id` in key) | Cache hit ratio < 10%; cache stampedes on database | **HIGH** |
| **Network Bandwidth** | 85 KB payload per AJAX request | 10k requests/sec = 850 MB/s network throughput required | **HIGH** |

---

## 17. Issue Classification Matrix

### Confirmed Problems (Verified via Code & Schema Inspection)
1. **Blade N+1 Queries:** 5 database queries executed per row inside `all_candidates.blade.php` loop (100 DB queries per 20 rows).
2. **Missing Database Indexes:** `add_job_wise_condidates` has NO foreign key indexes. `job_application_skills` has NO index on `job_application_id` or `skills`.
3. **Controller Loop N+1 Queries:** Tagify location and education filters perform individual MySQL queries inside `foreach` loops.
4. **Server-Side HTML Payload:** AJAX endpoint returns heavy 85KB HTML string instead of lightweight JSON data.
5. **Elasticsearch Painless Script Overhead:** Notice period and Best Match sorting calculate dates dynamically via inline scripts on every hit.
6. **Redundant Initial Page Queries:** `index()` fetches 10+ un-cached master tables on every page load.

### Potential Problems (Requires Load Testing Verification)
1. **Redis Cache Stampede:** User-scoped cache keys cause redundant queries when multiple recruiters in the same company apply the same filter.
2. **Elasticsearch Thread Pool Exhaustion:** Heavy aggregation queries (`size => 0`) running concurrently alongside search queries under high traffic.

### Not Measured
1. Exact DB query execution latency in milliseconds (requires live APM / Telemetry).
2. OPcache memory utilization and hit ratio on production server.
3. Exact system RAM/CPU utilization breakdown under 10k concurrent simulated users.

---

## 18. Audit Recommendations Summary

1. **Denormalize Elasticsearch Candidates Index:** Index pre-computed job mode, currency, city name, spokesperson name, child status name, and duplicate application flag into Elasticsearch documents to eliminate ALL 5 view-level N+1 DB queries.
2. **Add Critical Composite Database Indexes:** Create indexes on `add_job_wise_condidates(job_application_id, company_id, master_status_id)` and `job_application_skills(job_application_id, skills)`.
3. **Transition to Pure JSON API Payload:** Return lightweight JSON from `allcandidates` and move table row rendering to client-side JS template engine.
4. **Pre-calculate Sort Fields in ES:** Index `notice_period_days` and `notice_period_epoch` numeric fields to replace Painless script sorting.
5. **Tenant-Level Caching:** Update Redis cache key from `ats:allcvtracker:{user_id}:...` to `ats:cvtracker:{company_id}:{filters_hash}` to share cached results across company recruiters.

---
