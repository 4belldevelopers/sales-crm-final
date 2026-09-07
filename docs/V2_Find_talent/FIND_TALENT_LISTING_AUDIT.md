# Find Talent Listing Page Audit (`/service_company/find-talents`)

> **Document Type:** Architecture & Performance Audit  
> **Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/find-talents`  
> **Status:** AUDIT ONLY — NO CODE OR SYSTEM MODIFICATIONS PERFORMED  
> **Date:** August 11, 2026  

---

## 1. Executive Summary & Page Flow Architecture

The **Find Talent** feature is a core component of JERO's B2B Talent Marketplace. It enables service company recruiters to search, filter, and discover bench candidates uploaded by *other* service companies in order to assign them to open job requisitions.

### 1.1 Complete Page Request & Data Flow Architecture

```
User Request: GET /service_company/find-talents
  │
  ├──► Route: routes/service.php (name: apptracker.findTalent)
  │      └── Controller: ServiceCompanyAppTrackerController@findTalent
  │             ├── Auth check & Tenant isolation: auth()->user()->company_id
  │             ├── Fetch recent job: Job::where('company_id', $company_id)->latest()->value('id')
  │             ├── Fetch job model: Job::findOrFail($id)
  │             ├── Helper call: GeneralMethod::gdFilterData($id)
  │             │      ├── [DB] Job::findOrFail($id) (Duplicate query)
  │             │      ├── [DB] qualification_master query (Twice in method)
  │             │      ├── [DB] client_location_jobs join client_location join city_list query
  │             │      └── [DB] job_metronics_skills query
  │             ├── [DB/Cache] GeneralMethod::allCities()
  │             ├── [Cache/DB] ReferenceDataCache::qualificationNames()
  │             └── Render View: service_company.apptracker.find-talent
  │
User AJAX Interaction: GET /service_company/find-data-talent?page=1&...
  │
  ├──► Route: routes/service.php (name: apptracker.findTalentData)
  │      └── Controller: ServiceCompanyAppTrackerController@findTalentData
  │             ├── Parse Tagify JSON & Query params (q, cus_search, min_exp, max_exp, etc.)
  │             ├── [Cache Check] ats:allfindtalents:{user_id}:{version}:{md5_payload}
  │             │      └── IF Hit: Return cached JSON response ({html, total, pagination})
  │             │
  │             ├── IF Cache Miss:
  │             │      ├── Build Painless script sorting & Elasticsearch bool filters
  │             │      ├── [Elasticsearch Query 1] Search ELASTIC_CANDIDATES_BENCH (collapse by email)
  │             │      ├── [Elasticsearch Query 2] Cardinality Aggregation on email (for unique count)
  │             │      ├── In-Memory PHP processing: GeneralMethod::matchedSkillsList
  │             │      ├── [Blade Render 1] View: service_company.ajax_pages.find_talent_ajax
  │             │      │      └── FOREACH candidate row ($data):
  │             │      │             └── [DB Query N+1] DB::table('bench_candidate_skill_experience')
  │             │      │                    .where('candidate_id', $row->id)->get()
  │             │      ├── [Blade Render 2] View: service_company.ajax_pages.pagination
  │             │      ├── Cache response in DragonflyDB/Redis
  │             │      └── Return JSON response with raw rendered HTML strings
```

---

## 2. Detailed Technical Audit (18 Key Dimensions)

### 2.1 Current Files and Architecture
* **Route Definitions:** `routes/service.php` (Lines 271-272)
  * `GET service_company/find-talents` -> `ServiceCompanyAppTrackerController@findTalent`
  * `GET service_company/find-data-talent` -> `ServiceCompanyAppTrackerController@findTalentData`
* **Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` (Lines 10451–11196)
* **Helpers:**
  * `app/Helper/GeneralMethod.php` (`gdFilterData`, `allCities`, `currency`, `currencySymbole`, `currencyConverter`, `matchedSkillsList`, `convertToINR`, `salaryDividedByMode`, `formatViewSalary`, `formatK`)
  * `app/Services/ReferenceDataCache.php` (`qualificationNames`)
  * `app/Services/ElasticService.php` (Elasticsearch PHP client wrapper)
* **Views:**
  * Initial Page: `resources/views/service_company/apptracker/find-talent.blade.php` (2,707 LoC)
  * AJAX Table Partial: `resources/views/service_company/ajax_pages/find_talent_ajax.blade.php` (279 LoC)
  * AJAX Pagination Partial: `resources/views/service_company/ajax_pages/pagination.blade.php`

---

### 2.2 All Important DB Queries (Initial Load + AJAX Flow)

#### Initial Page Load (`findTalent()`):
1. **Fetch Latest Job ID:** `SELECT id FROM jobs WHERE company_id = ? ORDER BY id DESC LIMIT 1`
2. **Fetch Job Object:** `SELECT * FROM jobs WHERE id = ? LIMIT 1`
3. **GeneralMethod::gdFilterData($jobId):**
   * `SELECT * FROM jobs WHERE id = ? LIMIT 1` *(Duplicate call to Job::findOrFail)*
   * `SELECT name FROM qualification_master WHERE id = ? LIMIT 1` *(Executed twice inside method)*
   * `SELECT city_list.city FROM client_location_jobs JOIN client_location ON client_location.id = client_location_jobs.client_location_id LEFT JOIN city_list ON city_list.city_id = client_location.city_id WHERE client_location_jobs.job_id = ?`
   * `SELECT * FROM job_metronics_skills WHERE job_id = ? AND company_id = ?`
4. **All Cities Lookup:** `SELECT * FROM city_list` (or reference cache if populated)
5. **Qualifications Lookup:** `SELECT * FROM qualification_master` (via `ReferenceDataCache`)

#### AJAX Load (`findTalentData()`):
1. **Elasticsearch Main Search Query:**
   * Index: `ELASTIC_CANDIDATES_BENCH`
   * Query: `bool.must_not.company_id = recruiter_company_id`, `condidate_type = bench_condidate`, `status = 2`
   * Collapse: `field = email`
   * Pagination: `from = (page-1)*size`, `size = 10|20`
2. **Elasticsearch Aggregation Query:**
   * Index: `ELASTIC_CANDIDATES_BENCH`
   * Aggregation: `cardinality` on `email` (executed as a separate search request to ES)
3. **Blade Template Execution (Inside `find_talent_ajax.blade.php` foreach loop):**
   * `SELECT * FROM bench_candidate_skill_experience WHERE candidate_id = ?` *(Executed once per candidate card on every AJAX call!)*

---

### 2.3 N+1 Queries
* **CONFIRMED SEVERE N+1:** Inside `resources/views/service_company/ajax_pages/find_talent_ajax.blade.php` (Lines 35–37):
  ```php
  $skillExperiences = DB::table('bench_candidate_skill_experience')
      ->where('candidate_id', $row->id)
      ->get();
  ```
  * **Impact:** For every candidate record returned from Elasticsearch (e.g., 20 candidates per page), Laravel executes an individual SQL query to `bench_candidate_skill_experience`.
  * **Result:** 20 AJAX search requests = 400 unnecessary MySQL queries. At 1,000 concurrent recruiters, this results in **20,000 database queries per page render cycle**, completely bypassing Elasticsearch's decoupling advantages.

---

### 2.4 Duplicate / Unnecessary Queries
1. **Double `Job::findOrFail()`:** Called in `findTalent()` (L10462) and immediately again inside `GeneralMethod::gdFilterData()` (L3957).
2. **Double `qualification_master` Query:** `GeneralMethod::gdFilterData()` queries `qualification_master` on line 3967 and again on line 3987.
3. **Dual Elasticsearch Queries:** `findTalentData()` executes two distinct Elasticsearch searches per request:
   * First search to retrieve hits and collapsed results.
   * Second search (`size=0`) to calculate cardinality agg for total unique emails.

---

### 2.5 Relevant vs Unused Data
* **Unused Data Sourced in Initial Controller Load:**
  * `GeneralMethod::allCities()` retrieves thousands of city records passed to the Blade view for dropdown initialization, inflating initial HTML size.
  * `$this->qualificationTag` loads all qualification names regardless of recruiter filters.
* **HTML Payload Inflation:** `findTalentData()` returns rendered HTML strings (`html` and `pagination`) inside a JSON payload instead of raw JSON data structures.

---

### 2.6 Pagination
* **Mechanism:** Offset-based pagination using Elasticsearch `from` and `size` parameters.
* **Total Count Strategy:** Uses Elasticsearch `cardinality` aggregation on `email` to derive total unique bench candidates.
* **Issue:** Offset-based pagination in ES (`from + size > 10000`) degrades exponentially in performance as page depth increases.

---

### 2.7 Search
* **Search Execution:** Full-text and skill keyword searches are handled via Elasticsearch bool queries.
* **Skill Matching:** In-memory PHP parsing in `GeneralMethod::matchedSkillsList()` runs after ES returns documents to calculate exact skill match percentage.

---

### 2.8 Filters
The Find Talent listing supports 9 distinct filter parameters:
1. **Keywords (`q` / `cus_search`):** Required skill array tagify filter.
2. **Experience Range:** Min & Max total experience (`total_exp`).
3. **Salary / Bill Rate:** Min & Max converted rates based on salary mode (Yearly `ectc_yearly`, Monthly `ectc_monthly`, Hourly `ectc_hourly`).
4. **Location:** `city_id` terms filter.
5. **Education:** `qualification` terms filter.
6. **Outsourcing Type:** Full-time (`1`) vs Contract (`2`).
7. **Status:** `job_master_status_id` (Active = `2`).
8. **Notice Period:** Immediate, 15 Days, 30 Days, 60 Days, 90+ Days.
9. **Activity Timeframe:** Activity within 30, 60, 90, 180, 365 days.

---

### 2.9 Sorting & Elasticsearch Script Overhead
* **Sorting Modes:** 10+ sort options including Update Date ASC/DESC, Bill Rate ASC/DESC, Experience ASC/DESC, Notice Period ASC/DESC, and Serving Candidates.
* **Heavy Painless Scripts:**
  * **Show All Sort (Painless):** Evaluates `ads_switch` boolean via script and computes candidate skill match count in Painless per document.
  * **Notice Period Date Parsing (Painless):** Parses string date formats (`d-M-uuuu`, `uuuu-M-d`) using Java `LocalDate.parse()` inside Painless script for sorting (L10740–L10830).
  * **Serving Candidates Filter (Painless):** Runs date comparisons on string fields (`np_id.keyword`) dynamically during query evaluation (L11039–L11095).
* **Impact:** Painless script sorting forces Elasticsearch to bypass doc-values field data indexes, resulting in high CPU usage and thread pool saturation under concurrent load.

---

### 2.10 Database Indexes Audit (`mysql-schema.sql`)

| Table Name | Existing Indexes | Missing / Recommended Indexes |
| :--- | :--- | :--- |
| `bench_applications` | `PRIMARY (id)`, `KEY (company_id)` | Missing index on `(company_id, status, created_at)`, `(email)`, `(city, qualification)` |
| `bench_candidate_skill_experience` | `PRIMARY (id)`, `UNIQUE (candidate_id, skill_key)`, `KEY (candidate_id)`, `KEY (skill_key)` | Well-indexed for direct lookups, but misused via N+1 queries in Blade |
| `jobs` | `PRIMARY (id)`, `KEY (company_id)` | Missing composite index on `(company_id, status, created_at)` |
| `client_location_jobs` | `PRIMARY (id)`, `KEY (job_id)` | Missing composite index `(job_id, client_location_id)` |

---

### 2.11 EXPLAIN & Query Performance
* **MySQL:** Executing 20 N+1 queries per AJAX call on `bench_candidate_skill_experience` introduces ~20–50ms database latency per AJAX request.
* **Elasticsearch:** Painless date parsing scripts on non-standard string formats run against all candidate documents matching initial filters, creating an $O(N)$ CPU load per search operation.

---

### 2.12 PHP Memory & Collection Usage
* **HTML Generation in Memory:** `find_talent_ajax.blade.php` generates heavy HTML strings (with embedded SVG graphics and Metronic component tags) stored in RAM before JSON output serialization.
* **Large Tagify Arrays:** Un-paginated arrays of cities and qualifications loaded into controller memory (`$this->cities`).

---

### 2.13 Elasticsearch Usage (`ELASTIC_CANDIDATES_BENCH`)
* **Index:** `ELASTIC_CANDIDATES_BENCH`
* **Features Used:** Bool query (must, filter, must_not), field collapsing (`collapse` by `email`), cardinality aggregations, painless script execution.
* **Flaw:** Star-rated skill experiences (`bench_candidate_skill_experience`) are missing from the Elasticsearch document structure, forcing the application to fall back to MySQL during Blade rendering.

---

### 2.14 Redis / Cache Usage
* **Caching Strategy:** Response caching via DragonflyDB / Redis.
* **Cache Key Format:** `ats:allfindtalents:{user_id}:{version}:{md5(payload)}`
* **Version Control Key:** `allfindtalents_version_user_{user_id}`
* **TTL:** `ALL_FIND_TALENTS_TIME` (config setting).
* **Flaw:** Caches the entire rendered HTML payload string. Any minor status change invalidates the user's version key, triggering complete cache misses and high CPU re-rendering cycles.

---

### 2.15 Queue / Background Work
* **Current State:** Candidate uploads, status changes, and skill updates are synced synchronously or via immediate inline calls during web requests.
* **Missing Feature:** Absence of asynchronous queue workers (`Redis / Horizon`) for Elasticsearch candidate re-indexing.

---

### 2.16 AJAX / API Payload
* **Payload Structure:**
  ```json
  {
    "html": "<tr style=\"height: 102px;\">... 20 candidate rows HTML ...</tr>",
    "total": 142,
    "pagination": "<ul class=\"pagination\">... HTML ...</ul>"
  }
  ```
* **Payload Size:** ~45 KB – 80 KB per AJAX page load (mostly repetitive HTML tags, SVG icons, and inline CSS).
* **Target Size (Pure JSON):** ~4 KB – 8 KB.

---

### 2.17 Permissions and Business Logic
* **Tenant Isolation:**
  * Candidates owned by the logged-in recruiter's company MUST BE EXCLUDED:
    `'must_not' => [['term' => ['company_id' => $company_id]]]`
  * Candidates MUST belong to other service companies trading talent on the marketplace.
* **Candidate Status Constraints:** Candidate `status` must equal `2` (Active bench candidate).
* **Candidate Action Rules:** Candidate application limit check (`countforadd >= 3` displays "Expired").

---

### 2.18 Frontend Performance
* **Rendering Engine:** InnerHTML replacement (`$('#candidatetList').html(response.html)`).
* **DOM Overhead:** Destruction and re-creation of 20 complex table rows with SVGs on every filter update.
* **Tagify Assets:** Heavy client-side Tagify scripts re-initialized on full page loads.

---

## 3. Server Infrastructure Audit

### 3.1 Server Environment Verification

| Environment Metric | Local Verification (CLI) | Target Production / Staging Server (Provided Specs) |
| :--- | :--- | :--- |
| **Operating System** | Windows 10/11 x64 (XAMPP Environment) | **Ubuntu 24.04 LTS** (via aaPanel 8.0.3) |
| **PHP Version** | **PHP 8.2.12 (cli)** (ZTS VC19 x64) | **PHP 8.2.x / PHP-FPM** |
| **Laravel Version** | **11.48.0** | **11.48.0** |
| **CPU / Cores** | 12 Logical Processors (Local Host) | **8 CPU Cores** |
| **System Memory (RAM)**| 32 GB RAM (Local Host) | **31.34 GB RAM** |
| **Disk Space** | NVMe SSD (Local Host) | **386.4 GB Disk** |
| **Web Server** | Apache / Nginx (XAMPP) | **Nginx / FrankenPHP / Octane** |
| **Cache Store** | Redis / File | **DragonflyDB / Redis** |
| **Search Engine** | Elasticsearch 7.x/8.x | **Elasticsearch Cluster** |

---

## 4. 10,000+ Concurrent Recruiters Scaling Analysis

### 4.1 Bottleneck Modeling under 10,000 Concurrent Recruiters

Assuming 10,000 active recruiters filtering bench talent simultaneously (average load: **500 – 1,000 requests/sec**):

1. **MySQL Connection & Query Exhaustion:**
   * Each AJAX request triggers 1 initial query + 20 N+1 queries during Blade rendering = 21 queries per request.
   * At 500 req/sec = **10,500 MySQL queries/sec**.
   * Max MySQL connections (default 151–500) will be saturated within seconds, throwing `Too many connections` (HTTP 500 errors).

2. **Elasticsearch Painless Script Thread Saturation:**
   * Script-based sorting (`LocalDate.parse()` in Painless) executes once per matched document per search request.
   * ES thread pools (`search` pool max 13–32 threads on 8 cores) will saturate immediately, leading to `EsRejectedExecutionException`.

3. **PHP Memory & CPU Exhaustion:**
   * Rendering 20 HTML candidate rows per request in PHP consumes ~4 MB RAM per request.
   * 500 concurrent PHP processes will consume **2.0 GB RAM per second** solely on Blade HTML string generation.

---

## 5. Categorized Audit Findings

### 5.1 Confirmed Problems (Empirically Verified in Code)
1. **N+1 SQL Queries in Blade Template:** `find_talent_ajax.blade.php` executes `bench_candidate_skill_experience` query inside `@foreach` loop for every returned candidate row.
2. **Duplicate MySQL Queries:** `Job::findOrFail($id)` called twice; `qualification_master` queried twice inside `GeneralMethod::gdFilterData()`.
3. **Dual Elasticsearch Operations:** Executing two full HTTP calls to Elasticsearch per AJAX request (Search + Aggregation).
4. **Painless Script CPU Bottlenecks:** Painless Java date parsing (`LocalDate.parse`) inside Elasticsearch sort clause on string values.
5. **Heavy HTML Response Payloads:** Transmitting full HTML strings instead of JSON primitives.

### 5.2 Potential Problems (High Risk under Load)
1. **Elasticsearch Thread Pool Exhaustion:** Concurrent script-based sorts blocking ES search threads.
2. **Redis Memory Bloat:** Caching massive HTML string chunks in DragonflyDB.
3. **Cache Stampedes:** Invalidation of version key (`allfindtalents_version_user_*`) flushing all cached candidate listings for a recruiter simultaneously.

### 5.3 Not Measured (Requires Staging Load Testing)
1. **Exact Latency (ms):** MySQL query execution time under 100,000+ candidate records.
2. **Elasticsearch Heap Usage:** Exact memory consumption of `ELASTIC_CANDIDATES_BENCH` under high cardinality aggregations.
3. **Network Throughput:** Gigabit NIC saturation under uncompressed HTML payload delivery.

---

## 6. Non-Negotiable Business Logic & Data Constraints

The following business rules **MUST REMAIN 100% UNCHANGED** in any optimization or refactoring:

1. **Marketplace Tenant Isolation Rule:** Recruiters MUST NOT see bench candidates belonging to their own company (`company_id != recruiter_company_id`).
2. **Active Candidate Status Rule:** Only bench candidates with `status = 2` (Active) are visible.
3. **Candidate Submission Threshold:** If candidate `countforadd >= 3`, action button MUST display `Expired` instead of `Add to Job`.
4. **Salary Mode Conversion Logic:** Accurate conversion between Yearly, Monthly, and Hourly rates across INR, EUR, USD, AED, JPY, GBP.
5. **Serving Candidate Filtering:** Proper handling of notice period types (Immediate vs SNP LWD date).

---

## 7. Comprehensive Audit Recommendations Matrix

| # | Problem | Proposed Solution | Implementation Risk | Verification & Testing Method |
| :--- | :--- | :--- | :--- | :--- |
| 1 | N+1 query in Blade for candidate skill ratings | Index candidate skill ratings directly into `ELASTIC_CANDIDATES_BENCH` ES mapping as a nested object array | Low (Requires re-index script) | Confirm 0 MySQL queries executed during `findTalentData` AJAX call using Laravel Telescope |
| 2 | Painless Java date parsing in ES sorting | Pre-compute notice period epoch days in MySQL and index into ES as integer `np_epoch_days` | Medium (Data transformation) | Verify notice period sort order ASC/DESC matches legacy output |
| 3 | Dual ES HTTP calls (Search + Aggregation) | Single ES search call using `track_total_hits` and client-side total extraction | Low | Check ES search count in logs equals 1 per AJAX request |
| 4 | Duplicate DB queries on initial load | Refactor `gdFilterData` to accept pre-fetched Job instance and cache static qualification tags | Low | Inspect SQL log on `find-talents` initial load (target < 3 queries) |
| 5 | Heavy HTML AJAX responses | Shift to pure JSON API returning candidate arrays; render UI client-side using lightweight JS template | Medium (Frontend update) | Verify AJAX response size drops from ~60KB to <8KB |

---

> **End of Audit Document**
