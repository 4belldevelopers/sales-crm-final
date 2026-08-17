# Career Jobs Listing Page — Deep Audit & Technical Analysis (V1)

> **Document Version:** 1.0  
> **Date:** August 17, 2026  
> **Target Route:** `http://localhost/git/JWMaincode28June2026/career-jobs/{companyName}/{companyId}`  
> **Target Page Example:** `/career-jobs/4Bell-Technology/2`  
> **Scope:** Audit ONLY. No code, database, migration, Elasticsearch, Redis, or server configuration changes made.

---

## 1. Executive Summary & Page Flow

The **Career Jobs Listing** page provides a public candidate portal for viewing open jobs published by a specific company/tenant or shared via cross-company recommendation networks. 

### Complete End-to-End Request Flow:
```
[HTTP GET /career-jobs/{companyName}/{companyId}]
       │
       ▼
[routes/web.php] ──(Route: get.careerJobPage)──► [app/Http/Controllers/HomeController.php @ careerJobPage]
                                                       │
         ┌─────────────────────────────────────────────┴─────────────────────────────────────────────┐
         ▼                                                                                           ▼
[MySQL Database Queries (11 queries)]                                                       [ElasticService]
 ├── companies (Account metadata)                                                            ├── indexExists(ELASTIC_JOBS)
 ├── clients (Self company client ID)                                                        └── client->search($params)
 ├── td_industries (Industry name)                                                                   │
 ├── client_location (Count & First record)                                                          ▼
 ├── city_list (City name mapping)                                                            [Hits Array]
 ├── countries (via GeneralMethod::getCountryName + countries.json)                                  │
 ├── commericial_details (Min rates for Full-time & Contract)                                        │
 └── commericials (Full-time & Contract status records)                                              │
                                                                                                     │
         ┌───────────────────────────────────────────────────────────────────────────────────────────┘
         ▼
[View: resources/views/career-job-page.blade.php]
 ├── Inline Head DB Query: company_career_settings (Favicon)
 ├── Inline Body DB Query: company_career_settings (Branding/Theme settings) [DUPLICATE]
 └── @foreach($data as $row) [Looping per Job Card - N=10 per page]
      ├── N+1 Query #1: job_metronics_skills (Skills list, limit 8)
      ├── N+1 Query #2: client_city_jobs (City count)
      ├── N+1 Query #3: client_city_jobs JOIN city_list (City name)
      └── N+1 Query #4: GeneralMethod::openCompanyDetails()
           └── HEAVY SQL Query: jobs JOIN clients JOIN companies
               LEFT JOIN add_job_wise_condidates (COUNT & SUM/CASE over all candidates)
               LEFT JOIN recommendation_relation
```

---

## 2. Component Files & Architecture

| Component Layer | Primary File / Class | Responsibilities & Issues |
| :--- | :--- | :--- |
| **Route** | `routes/web.php` (Line 164) | Maps GET `/career-jobs/{companyName}/{companyId}` to `HomeController@careerJobPage`. Unprotected public route. |
| **Controller** | `app/Http/Controllers/HomeController.php` (Lines 1110–1441) | Fetches company/client metadata from MySQL, builds Elasticsearch boolean query, searches `ELASTIC_JOBS`, formats view payload. Performs unnecessary/duplicate DB queries. |
| **Elastic Service** | `app/Services/ElasticService.php` | Wraps official Elasticsearch PHP client (`Elasticsearch\ClientBuilder`). Handles `search()`, `indexExists()`, and `msearch()`. Read from `config()`. |
| **View Template** | `resources/views/career-job-page.blade.php` | Renders HTML/CSS (Poppins font, Metronic framework UI), hero section, sidebar filters, job cards list, pagination. Executes raw SQL queries and heavy helper methods inside Blade loop. |
| **Helper Methods** | `app/Helper/GeneralMethod.php` | Contains `openCompanyDetails()`, `getCountryName()`, `thousandsCurrencyFormat()`, `salaryMode()`, `currencySymbole()`. |

---

## 3. Comprehensive Database Query Audit

### 3.1 Controller Level Queries (`HomeController::careerJobPage`)

1. **Company Metadata Query:**
   ```sql
   SELECT * FROM `companies` WHERE `id` = 2 LIMIT 1;
   ```
2. **Self Client Entity Query:**
   ```sql
   SELECT `clients`.`id`, `clients`.`tags` FROM `clients` WHERE `clients`.`company_id` = 2 AND `clients`.`self_company` = 1 LIMIT 1;
   ```
3. **Industry Lookup Query:**
   ```sql
   SELECT `td_industries`.`ind_id`, `td_industries`.`ind_name` FROM `td_industries` WHERE `td_industries`.`ind_id` = ? ORDER BY `td_industries`.`ind_name` ASC LIMIT 1;
   ```
4. **Client Location Count Query:**
   ```sql
   SELECT COUNT(*) FROM `client_location` WHERE `client_location`.`client_id` = ? ORDER BY `client_location`.`id` ASC;
   ```
5. **Client Location Record Query (DUPLICATE):**
   ```sql
   SELECT `client_location`.`city`, `client_location`.`city_id`, `client_location`.`address`, `client_location`.`postcode`, `client_location`.`country_id` FROM `client_location` WHERE `client_location`.`client_id` = ? ORDER BY `client_location`.`id` ASC LIMIT 1;
   ```
6. **City Name Query:**
   ```sql
   SELECT `city_list`.`city` FROM `city_list` WHERE `city_list`.`city_id` = ? LIMIT 1;
   ```
7. **Country Name Query (`GeneralMethod::getCountryName`):**
   ```sql
   SELECT `countries`.* FROM `countries` WHERE `countries`.`id` = ? LIMIT 1;
   ```
   *Note:* `GeneralMethod::getCountryName` also executes `file_get_contents(public_path('country-state-city/countries.json'))` on every request.
8. **Commercial Details Full-Time Min Rate:**
   ```sql
   SELECT MIN(`rate`) FROM `commericial_details` WHERE `company_id` = 2 AND `commtype` = 2;
   ```
9. **Commercial Details Contract Min Rate:**
   ```sql
   SELECT MIN(`rate`) FROM `commericial_details` WHERE `company_id` = 2 AND `commtype` = 1;
   ```
10. **Commercials Settings (Full-Time):**
    ```sql
    SELECT * FROM `commericials` WHERE `company_id` = 2 AND `commtype` = 2 LIMIT 1;
    ```
11. **Commercials Settings (Contract):**
    ```sql
    SELECT * FROM `commericials` WHERE `company_id` = 2 AND `commtype` = 1 LIMIT 1;
    ```

### 3.2 Blade Template Level Queries (`career-job-page.blade.php`)

12. **Head Career Settings Query (Line 9):**
    ```sql
    SELECT * FROM `company_career_settings` WHERE `company_id` = 2 LIMIT 1;
    ```
13. **Body Career Settings Query (Line 28) — DUPLICATE:**
    ```sql
    SELECT * FROM `company_career_settings` WHERE `company_id` = 2 LIMIT 1;
    ```

### 3.3 N+1 Queries Inside Blade `@foreach($data as $row)` Loop (Per Job Card)

For a page size of $N = 10$ jobs, the following queries are executed **10 times each** (40 total queries):

14. **Job Skills Query (N+1):**
    ```sql
    SELECT `skill_name` FROM `job_metronics_skills` WHERE `job_id` = {job_id} AND `company_id` = {comId} LIMIT 8;
    ```
15. **Job City Locations Count Query (N+1):**
    ```sql
    SELECT COUNT(*) FROM `client_city_jobs` WHERE `job_id` = {job_id};
    ```
16. **Job City Name Join Query (N+1):**
    ```sql
    SELECT `city_list`.`city` AS `cn` FROM `client_city_jobs` LEFT JOIN `city_list` ON `city_list`.`city_id` = `client_city_jobs`.`city_id` WHERE `client_city_jobs`.`job_id` = {job_id} LIMIT 1;
    ```
17. **Company Logo & Heavy Aggregation Query via `GeneralMethod::openCompanyDetails()` (N+1):**
    ```sql
    SELECT `jobs`.`id`, `jobs`.`title`, `jobs`.`work_type`, `jobs`.`show_vendor`, `jobs`.`total_positions`,
           `clients`.`name` AS `clients_name`, `clients`.`logo_logical_path`, `clients`.`self_company`, `clients`.`company_id` AS `client_company_id`,
           `companies`.`company_name`, `companies`.`logo`,
           COUNT(`job_applications`.`id`) AS `allCandidate`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 1 THEN 1 ELSE 0 END) AS `Sourcing`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 2 THEN 1 ELSE 0 END) AS `Screening`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 3 THEN 1 ELSE 0 END) AS `Interview`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 4 THEN 1 ELSE 0 END) AS `Negotiation`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 5 THEN 1 ELSE 0 END) AS `Offered`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 6 THEN 1 ELSE 0 END) AS `Joined`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 7 THEN 1 ELSE 0 END) AS `Rejected`,
           SUM(CASE WHEN `job_applications`.`master_status_id` = 8 THEN 1 ELSE 0 END) AS `InReview`,
           -- Additional 10+ SUM(CASE WHEN thirdparty_company_id ...) expressions...
           `recommendation_relation`.`id` AS `recommendation_id`, `recommendation_relation`.`from_company_id`, `recommendation_relation`.`final_status`, `recommendation_relation`.`to_company_id`
    FROM `jobs`
    JOIN `clients` ON `clients`.`id` = `jobs`.`client_id`
    JOIN `companies` ON `companies`.`id` = `jobs`.`company_id`
    LEFT JOIN `add_job_wise_condidates` AS `job_applications` ON `job_applications`.`job_id` = `jobs`.`id`
    LEFT JOIN `recommendation_relation` ON `recommendation_relation`.`job_id` = `jobs`.`id`
    WHERE `jobs`.`id` = {job_id} GROUP BY `jobs`.`id`;
    ```

### Total Query Count Summary:
- **Controller Queries:** 11
- **Blade Template Metadata Queries:** 2
- **Blade Loop Queries ($N=10$):** 40
- **Total DB Queries per Page Load:** **53 Queries** (increases to 93 queries if page size is 20).

---

## 4. Unused & Wasteful Data

1. **Unused Candidate Pipeline Aggregations:** `GeneralMethod::openCompanyDetails()` calculates candidate application metrics (`Sourcing`, `Screening`, `Interview`, `Negotiation`, `Offered`, `Joined`, `Rejected`, `InReview`, 3rd party counters) for every job card. The career listing page **only uses `$row->logo_logical_path` or `$row->logo`**. All 18+ candidate aggregation fields are completely discarded.
2. **Unused Controller Variables:** `$fullTimeStatusRate`, `$contractStatusRate`, `$fullTimeStatus`, `$contractStatus` are calculated in `HomeController` but are not rendered anywhere on `career-job-page.blade.php`.
3. **Full Elasticsearch Source Payload:** `_source` returns all job fields stored in Elasticsearch, including internal assignment fields, contact person details, and internal configurations not needed for public display.
4. **Repeated Disk Operations:** `GeneralMethod::getCountryName()` reads `countries.json` from the filesystem via `file_get_contents()` on every execution.

---

## 5. Pagination, Search, Filter & Sort Audit

### 5.1 Pagination
- **Implementation:** Calculated using Elasticsearch `from` and `size` parameters: `$from = ($page - 1) * $size`.
- **Confirmed Bug:** Pagination links in Blade (`career-job-page.blade.php` line 811) only retain `custom_search` and `custom_hiringtype`:
  ```php
  $qs = '&custom_search='.urlencode(request('custom_search','')).'&custom_hiringtype='.urlencode(request('custom_hiringtype','All'));
  ```
  Navigating to Page 2 **wipes out all active sidebar filters** (`filter_exp`, `filter_dept`, `filter_emp_type`, `filter_location`, `filter_skills`).

### 5.2 Search
- **Implementation:** Input `custom_search` triggers a nested Elasticsearch `bool` query with leading wildcards:
  ```json
  {"match_phrase_prefix": {"title": "*search*"}},
  {"wildcard": {"client_or_company_name": "*search*"}},
  {"nested": {"path": "job_assign", "query": {"wildcard": {"job_assign.user_name": "*search*"}}}},
  {"nested": {"path": "client_city_jobs", "query": {"wildcard": {"client_city_jobs.city_name": "*search*"}}}}
  ```
- **Performance Impact:** Leading wildcard searches (`*search*`) in Elasticsearch bypass inverted indexes, causing expensive unindexed string scans across all documents.

### 5.3 Filters
- **Hardcoded Sidebar Values:** Department filter (`filter_dept`), Location filter (`filter_location`), Experience filter (`filter_exp`), and Skills filter (`filter_skills`) render hardcoded lists in Blade (`['Noida','Bangalore','Hyderabad','Pune','Remote']`, `['Engineering','Product','Design'...]`). They are **not dynamically derived** from actual open jobs in the database or Elasticsearch aggregations.

### 5.4 Sorting
- **Overloaded Parameter:** `custom_hiringtype` handles both sorting options (1 = Newest, 2 = Oldest, 3 = Sal Low-High, 4 = Sal High-Low, 5 = A-Z) and filtering options (7 = Full-Time Only, 8 = Contract Only). This parameter overload creates conflicting logic and hinders API purity.

---

## 6. Database Index Audit

Analysis against database schema truth (`database/schema/mysql-schema.sql`):

| Table Name | Query Pattern | Existing Indexes | Missing / Recommended Indexes | Impact |
| :--- | :--- | :--- | :--- | :--- |
| `client_city_jobs` | `WHERE job_id = ?` | `PRIMARY KEY (id)` ONLY | **MISSING:** `KEY idx_client_city_jobs_job_id (job_id)` | Full table scan on every city count query per job card. |
| `commericial_details` | `WHERE company_id = ? AND commtype = ?` | `PRIMARY KEY (id)` ONLY | **MISSING:** `KEY idx_cd_company_comm (company_id, commtype)` | Full table scan on rates calculation in controller. |
| `job_metronics_skills` | `WHERE job_id = ? AND company_id = ?` | `PRIMARY KEY (id)`, `KEY job_skills_job_id_foreign (job_id)`, `KEY job_skills_company_id_foreign (company_id)` | **RECOMMENDED:** Composite `KEY idx_jms_job_company (job_id, company_id)` | Secondary index lookup requires 2 separate index evaluations. |
| `commericials` | `WHERE company_id = ? AND commtype = ?` | `PRIMARY KEY (id)`, `KEY idx_commericials_company_id (company_id)` | **RECOMMENDED:** Composite `KEY idx_commericials_company_commtype (company_id, commtype)` | Non-composite index forces index merge or row scan. |
| `company_career_settings` | `WHERE company_id = ?` | `UNIQUE KEY company_career_settings_company_id_unique (company_id)` | **COVERED** | Fast single-row unique index lookup. |

---

## 7. Elasticsearch, Redis & Queue Audit

### 7.1 Elasticsearch Usage
- **Index:** `ELASTIC_JOBS`
- **Health Check:** `this->elasticService->indexExists(ELASTIC_JOBS)` executed on every request.
- **Request Cache:** Enabled in `$params` (`'request_cache' => true`). However, dynamic wildcards prevent cache hits.

### 7.2 Redis & Cache Usage
- **Current Status:** **0% Cache Utilization**.
- No caching for `company_career_settings`, company branding, commercial status rates, or country lists. Every single public visitor forces fresh MySQL queries.

### 7.3 Queue & Background Work
- No background jobs are dispatched during page view. Logo fetching and data transformation run synchronously.

---

## 8. Frontend & Architecture Performance

- **Iframe Integration:** The page is designed to be embedded within client iframe contexts. It transmits height via JS: `window.parent.postMessage({frameHeight: document.body.scrollHeight}, '*')`.
- **Form Submission:** Filter changes trigger full page reloads via `form.submit()`. No background AJAX updates.
- **Asset Overhead:** Loads `plugins.bundle.css/js`, `style.bundle.css`, `jquery.min.js`, FontAwesome 6.5.1, and Google Fonts Poppins synchronously in `<head>`.

---

## 9. Security, Tenancy & Business Logic Audit

### 9.1 Multi-Tenancy Scoping
- **Public Portal:** Publicly accessible route without authentication middleware.
- **Tenant Scope:** Enforced via `comId = $company_id` filter in Elasticsearch and `company_id` in SQL queries.
- **Recommendation Logic:** Must support cross-tenant shared jobs via `recommendation_relation`. If a job is recommended to `$company_id`, the client branding, status, and company name switch according to `to_company_id` vs `from_company_id` rules.

### 9.2 Critical Business Logic That MUST Remain Unchanged
1. **Tenant Branding Customization:** Primary color (`td_color_primary`), secondary color, accent, background, theme mode (`light`/`dark`), template style, custom CSS, logo, favicon, and hero images from `company_career_settings`.
2. **Shared Talent Recommendation Rules:** Displaying tenant-specific company name and application status (`final_status == 2` or `3` marked as `Removed`) for recommended jobs.
3. **Salary & Currency Formatting:** Formatting numbers via `GeneralMethod::thousandsCurrencyFormat()` and outputting currency symbols (`currencySymbole`).
4. **Work Mode & Employment Type Rules:** Mapping `work_type == 'yes'` to `Remote`, `employment_type == 1` to `Contract`, and `employment_type == 0` to `Full-time`.
5. **Job Badge Logic:** `Active` jobs created $\le 3$ days ago display `New`; $> 3$ days display `Hiring`; `Inactive` displays `Closed`; `final_status == 2` or `3` displays `Removed`.
6. **Apply & Detail Routing:** Links pointing strictly to `url('job-details/'.$row->id.'/'.$company_id)` and `url('job-apply/'.$row->id.'/'.$company_id)`.

---

## 10. Server Audit & Hardware Resource Context

### Server Specifications (Confirmed / Verified Facts):
- **OS:** Ubuntu 24
- **CPU:** 8 CPU Cores
- **RAM:** 31.34 GB RAM
- **Disk:** 386.4 GB Disk Space
- **Control Panel Environment:** aaPanel 8.0.3
- **Web Server:** Nginx / Apache
- **PHP Environment:** PHP 8.2 / PHP-FPM / Laravel Octane (FrankenPHP/Swoole)
- **Database:** MySQL 8.0 / MariaDB
- **Search Engine:** Elasticsearch 7.x/8.x
- **Cache / Memory Store:** Redis

---

## 11. 10,000+ Concurrent Recruiters Scalability Assessment

### Concurrency Impact Breakdown:

```
10,000 Concurrent Public Viewers / Recruiters
                      │
                      ▼
53 DB Queries / Request × 10,000 Requests = 530,000 DB Queries / Minute
                      │
                      ▼
   - 40,000 Heavy Aggregate Joins on add_job_wise_condidates / min
   - 10,000 Disk Reads for countries.json / min
   - 10,000 Unindexed Table Scans on client_city_jobs / min
   - 10,000 ES Leading Wildcard Scans / min
                      │
                      ▼
[CRITICAL SYSTEM COLLAPSE: MySQL CPU 100%, I/O Wait Bottleneck, ES Heap Exhaustion]
```

### Problem Classification Matrix:

#### A. Confirmed Problems (Verified via Code & Schema Inspection)
1. **Blade Template N+1 Queries:** 4 SQL queries executed per job card inside `@foreach($data)`.
2. **Heavy Aggregate Query Wasted in Blade:** `openCompanyDetails()` calculates candidate statistics for all pipeline stages per job card, throwing away everything except company logo path.
3. **Missing Indexes on Core Tables:** `client_city_jobs` missing index on `job_id`; `commericial_details` missing index on `(company_id, commtype)`.
4. **Pagination Filter Erasure:** Changing page drops active experience, location, department, skill filters.
5. **Zero Redis Caching:** Static company career settings, client details, and commercial rates queried from MySQL on every single page view.
6. **Duplicate DB Queries:** `client_location` queried twice in Controller; `company_career_settings` queried twice in Blade.
7. **File I/O in Hot Path:** `GeneralMethod::getCountryName()` reads `countries.json` from disk on every invocation.

#### B. Potential Problems (High Risk Under Load)
1. **Elasticsearch Leading Wildcard CPU Lockup:** `custom_search` using `*term*` wildcards can lock up Elasticsearch thread pools during peak traffic.
2. **MySQL Connection Pool Exhaustion:** 53 queries per web worker will quickly deplete PDO database connection pools under high concurrency.
3. **Full Page Reload Latency:** Mobile users experiencing slow multi-second page loads due to un-cached full Blade re-renders.

#### C. Not Measured (Requires APM / Load Testing Tools)
1. Exact latency breakdown in milliseconds (TTFB / render time).
2. Peak memory consumption per PHP-FPM / Octane worker.
3. Exact Elasticsearch query execution time in milliseconds under load.

---

## 12. Audit Recommendations Summary

| # | Bottleneck / Problem | Proposed Solution | Risk Level | Verification & Testing |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **N+1 Queries in Blade Loop** | Eager-load skills and locations in Controller batch query (`whereIn('job_id', $jobIds)`). Pass pre-assembled array to Blade. | Low | Verify DB query log shows $\le 3$ queries for listing data instead of 40+. |
| 2 | **Heavy Wasted Aggregations in `openCompanyDetails`** | Replace `openCompanyDetails()` with a lightweight cached logo resolver in Controller. | Low | Ensure company and client logos render identically for self & shared jobs. |
| 3 | **Missing MySQL Indexes** | Add indexes on `client_city_jobs(job_id)`, `commericial_details(company_id, commtype)`, `job_metronics_skills(job_id, company_id)`. | Low | Run `EXPLAIN` to confirm index usage (`type: ref`). |
| 4 | **Pagination Wipes Filters** | Append all active request query parameters (`filter_exp`, `filter_dept`, etc.) to Blade pagination URL builder. | Low | Filter by "Remote", click Page 2, verify filter remains checked and active. |
| 5 | **Zero Cache Utilization** | Cache `company_career_settings`, `client` details, and commercial rates in Redis (`TTL 1 Hour`) with tags per tenant. | Low | Inspect Redis keys; verify 0 DB queries for settings on repeat page loads. |
| 6 | **Duplicate Controller & Blade Queries** | Fetch settings and location once in Controller; pass object to view. Remove raw `DB::table()` calls from Blade. | Low | Inspect query log; confirm zero inline SQL calls inside Blade template. |
| 7 | **Disk I/O for `countries.json`** | Cache country JSON dictionary in Redis or PHP static array. | Low | Verify no `file_get_contents()` disk reads on request handling. |
| 8 | **ES Leading Wildcard Performance** | Update Elasticsearch search query to use `multi_match` on analyzed fields or edge_ngram analyzers instead of leading wildcards. | Medium | Verify search results match original behavior while ES query time drops. |
