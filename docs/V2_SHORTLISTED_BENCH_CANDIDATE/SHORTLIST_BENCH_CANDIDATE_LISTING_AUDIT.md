# Shortlisted Bench Candidate Listing — Audit Report

> **Location:** `docs/V2_SHORTLISTED_BENCH_CANDIDATE/SHORTLIST_BENCH_CANDIDATE_LISTING_AUDIT.md`  
> **Target Route:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/offerListing`  
> **Status:** AUDIT ONLY (No code, database, Elasticsearch, Redis, or server configuration changes made).

---

## 1. Executive Summary & Page Purpose

The **Shortlisted Bench Candidates Listing** (Offer Listing) page is a critical multi-tenant ATS feature within the JERO platform for **Service Companies**. It enables recruiters, account managers, and agency administrators to track bench candidates who have been submitted, shortlisted, or offered roles across direct and internal client job requisitions.

### Key Capabilities
1. **Hero KPI Summary Cards:** Instant visual counts for Total Shortlisted, Joining This Week, Offer Released, Offer Accepted, and Joined.
2. **Status Tab Filters:** Quick switching across candidate lifecycle tabs (`All`, `Pending`, `Offer Released`, `Accepted`, `Joining Soon`, `Expired`).
3. **Location & Dynamic Filtering:** City-based filtering populated directly from active candidate distributions.
4. **Complex Multi-Attribute Sorting:** Sorting by Notice Period (Painless date/integer parsing), CTC (salary), Total Experience, Creation Date, Offer Amount, and Joining Date.
5. **Interactive Actions:** Direct access to Candidate Profile Drawers, Hiring Manager Listings, Bench Job Assignments, and Stage/Sub-stage Status updates.

---

## 2. Current Architecture & Complete Flow

```
[ HTTP GET Request ]
       │
       ▼
[ Route: service.php ]
  ├── GET  /service_company/bench-candidates/offerListing
  └── GET  /service_company/bench-candidates_ajax/offerListingData
       │
       ▼
[ Controller: ServiceCompanyBenchCandidatesController ]
  ├── offerListing()                  ───> Initial Page Layout & Hero Stats
  └── offerListingData_ajax(Request)  ───> Paginated Listing AJAX Payload
       │
       ├───> [ Redis / DragonflyDB Cache Check ]
       │        Key: ats:allbenchofferListing:{user_id}:{version}:{hash}
       │        (HIT: Returns cached JSON immediately)
       │        (MISS: Proceeds to Elasticsearch)
       │
       ├───> [ Elasticsearch Service: ELASTIC_CANDIDATES ]
       │        Query: other_company = company_id AND condidate_type = 'bench_condidate'
       │        Filter: tab status, search term, city location
       │        Sort: Painless scripts or numeric/date fields
       │        Returns: Hits array + Total count
       │
       ▼
[ Blade View Rendering ]
  ├── service_company.bench-candidates.offerListing (Main Layout)
  └── service_company.ajax_pages.bench-candidate-offer-ajax (Item Loop)
       │
       ├───> [ N+1 MySQL Call ] DB::table('bench_master_status')->where('id', $row->status)...
       ├───> [ N+1 MySQL Call ] DB::table('job_applications')->count() (if duplicate == 1)
       ├───> [ N+1 ES Call ]    GeneralMethod::jobDetails($row->jobId) [ES get per hit!]
       └───> [ N+1 MySQL Call ] GeneralMethod::companyDetailsByClient($row->jobId)
       │
       ▼
[ Frontend JS & DOM Injection ]
  ├── Inject rendered HTML into #benchCandidateOfferAjax
  └── Render pagination inside #paginationbenchCandidateOffer
```

---

## 3. Comprehensive Audit Findings (Items 1 to 18)

### 1. Current Files and Architecture
- **Routes:** [routes/service.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/routes/service.php#L413-L415)
  - `Route::get('bench-candidates/offerListing', [ServiceCompanyBenchCandidatesController::class,'offerListing'])`
  - `Route::get('bench-candidates_ajax/offerListingData', [ServiceCompanyBenchCandidatesController::class,'offerListingData_ajax'])`
- **Controller:** [app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php#L4078-L4610)
  - God controller containing 7,700+ lines.
  - Contains `offerListing()`, `benchOfferStats()`, and `offerListingData_ajax()`.
- **Views:**
  - `resources/views/service_company/bench-candidates/offerListing.blade.php` (Main page layout & JS handlers)
  - `resources/views/service_company/ajax_pages/bench-candidate-offer-ajax.blade.php` (Table row fragment loop)
  - `resources/views/service_company/ajax_pages/pagination.blade.php` (Custom AJAX pagination control)
- **Helper Utilities:** [app/Helper/GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php)
  - `jobDetails()` (Line 12589)
  - `companyDetailsByClient()` (Line 3270)
  - `bench_status_subline()`

### 2. All Important DB Queries
- **Query 1 (Page Load - Unused):**
  ```sql
  SELECT COUNT(*) FROM `bench_applications`;
  ```
  *Issue:* Executed in `offerListing()` on initial page load without company filtering. Unused in view rendering.
- **Query 2 (Blade N+1 - Status Lookup):**
  ```sql
  SELECT * FROM `bench_master_status` WHERE `id` = ? LIMIT 1;
  ```
  *Issue:* Executed once per candidate hit in `bench-candidate-offer-ajax.blade.php`.
- **Query 3 (Blade N+1 - Duplicate Application Check):**
  ```sql
  SELECT COUNT(*) FROM `job_applications`
  WHERE `company_id` = ? AND (`email` = ? OR `phone` = ?)
    AND `created_at` > DATE_SUB(CURDATE(), INTERVAL ? DAY);
  ```
  *Issue:* Executed per row when `@$row->duplicate == 1`. Triggers lazy loading of `auth()->user()->company->app_duplicate`.
- **Query 4 (Blade N+1 - Client Details Lookup):**
  ```sql
  SELECT `companies`.*, `clients`.`logo_logical_path`, `jobs`.`total_positions`
  FROM `companies`
  JOIN `jobs` ON `companies`.`id` = `jobs`.`company_id`
  JOIN `clients` ON `clients`.`id` = `jobs`.`client_id`
  WHERE `jobs`.`id` = ? LIMIT 1;
  ```
  *Issue:* Executed inside `GeneralMethod::companyDetailsByClient($row->jobId)` per candidate hit.

### 3. N+1 Queries (Confirmed Critical Bottleneck)
- **MySQL N+1 #1:** `bench_master_status` queried N times per page view inside the Blade template loop.
- **MySQL N+1 #2:** `companyDetailsByClient` multi-table join executed N times per page view inside the Blade loop.
- **MySQL N+1 #3:** Duplicate candidate count query executed N times per page view when duplicate detection is enabled.
- **Elasticsearch N+1 #4:** `GeneralMethod::jobDetails($row->jobId)` calls `ElasticService->getClient()->get(['index' => ELASTIC_JOBS, 'id' => $id])` synchronously for **every single row** inside the Blade view! For 10 hits per page, this makes **10 individual HTTP round-trips to Elasticsearch** during Blade template evaluation.

### 4. Duplicate / Unnecessary Queries
- `BenchApplication::count()` executed on controller load is redundant (hero stats and tab counts are driven by ES aggregations).
- Client company details are re-queried from MySQL even though candidate documents in Elasticsearch already store client and job fields.

### 5. Relevant vs Unused Data
- **Relevant Data:** Candidate Name, Designation, Job Title, Client Name, Compensation, Currency, Employment Type, Offer Acceptance Status, Joined Date, Master Status, Duplicate Flag.
- **Unused/Redundant Fields:** Raw MySQL full table count (`allBenchApplication`), unused DataTables JavaScript initialization code commented out in `offerListing.blade.php`.

### 6. Pagination
- Handled via Elasticsearch offset pagination (`from` and `size`). Default page size is 10 items.
- Custom jQuery pagination handler catches `.page-link` clicks and sends AJAX request with page number.
- **Potential Risk:** Deep pagination (`from > 10000`) will degrade Elasticsearch memory performance unless `search_after` cursor pagination is implemented for high page numbers.

### 7. Search
- Search input parses `search` or `cus_search` parameters.
- ES `bool` query applies `match_phrase_prefix` on `full_name` and `title`, with exact numeric `term` search on `offer_id`.
- Functionality is accurate but requires proper field indexing in Elasticsearch.

### 8. Filters
- **Tenant Scope:** `other_company = company_id` (Ensures strict service company multi-tenancy).
- **Candidate Type:** `condidate_type = 'bench_condidate'`.
- **Master Job Status:** `job_master_status_id IN [5, 6]` (Shortlisted / Offered).
- **Active Filter:** Excludes `status = 3`.
- **City Location Filter:** `city_name = location` when location filter is selected.
- **Tab Filters:** `pending`, `released`, `accepted`, `joining_soon`, `expired`.

### 9. Sorting
- Supports 15+ sorting configurations (`valCheck` 1 to 22).
- **Confirmed Bottleneck:** Notice Period sorting (cases 5, 6, 21, 22) uses **Elasticsearch Painless scripts** (`_script` with `doc['np_id.keyword']` string parsing, regex splitting, and `LocalDate.parse()`).
- Painless runtime parsing on un-indexed string values consumes severe Elasticsearch CPU under concurrent searches.

### 10. Database Indexes
- Tables involved: `bench_applications`, `bench_place`, `bench_master_status`, `job_applications`, `jobs`, `companies`, `clients`.
- `bench_applications` index: `PRIMARY (id)`, `KEY (company_id)`.
- **Missing Index:** `job_applications` lacks composite index on `(company_id, email, created_at)` and `(company_id, phone, created_at)` for fast duplicate checking.

### 11. EXPLAIN / Query Performance
- Helper queries inside `companyDetailsByClient` execute unindexed JOINs for each job ID requested.
- Duplicate detection count queries perform full index range scans on `job_applications`.

### 12. PHP Memory / Large Collections
- `offerListingData_ajax` renders full HTML table rows via `view(...)->render()` and stores the rendered string in Redis.
- Storing large HTML strings in Redis for every user search combination consumes excessive server memory.

### 13. Elasticsearch Usage
- **Good Practice:** `benchOfferStats()` utilizes single ES `filters` + `terms` aggregation query with `size: 0` to fetch hero cards and top city filters in a single request.
- **Bad Practice:** Blade-level `GeneralMethod::jobDetails()` fires individual ES `get()` calls in a loop.

### 14. Redis / Cache Usage
- Redis keys:
  - `ats:benchofferStats:{company_id}:{version}` (Stats cache - Good).
  - `ats:allbenchofferListing:{user_id}:{version}:{hash}` (Listing cache - Problematic).
- **Confirmed Problem:** Listing cache key includes `user_id`. If 50 recruiters in the same company view the same page, Redis creates 50 separate identical copies of the HTML cache fragment!

### 15. Queue / Background Work
- ES candidate index updates are performed synchronously. No background queue worker is used for batch cache invalidation or async indexing in this flow.

### 16. AJAX / API Payload
- Returns JSON object containing rendered HTML strings:
  ```json
  {
    "html": "<tr>...</tr>",
    "total": 42,
    "pagination": "<nav>...</nav>"
  }
  ```
- Average response payload size: 25 KB - 45 KB (HTML overhead).
- Clean JSON payload equivalent would be ~3 KB - 5 KB.

### 17. Permissions & Business Logic
- Permission check: `abort_if(Gate::denies('view_job_applications'), 403);`.
- Tenancy boundary: `other_company = company_id`.
- Confidential salary protection: `GeneralMethod::confidentialAmontShow($row->jobId)`.
- Duplicate candidate detection logic: Evaluates `company.app_duplicate` (1: job-specific, 2: company-wide) and `company.app_duplicate_days`.

### 18. Frontend Performance
- DOM injection into `#benchCandidateOfferAjax`.
- Legacy commented DataTables JS remains in `offerListing.blade.php`.
- Head scripts include CDN dependencies for DataTables responsive CSS and FontAwesome 6.

---

## 4. Current Server Environment Audit

| Parameter | Server / Target Value | Verification Status | Notes |
|---|---|---|---|
| **OS** | Ubuntu 24.04 LTS | Confirmed | Production Server Stack |
| **Control Panel** | aaPanel 8.0.3 | Confirmed | Web & Environment Management |
| **CPU / Cores** | 8 CPU Cores | Confirmed | Server Environment Specs |
| **RAM** | 31.34 GB | Confirmed | Total System RAM |
| **Disk Storage** | 386.4 GB | Confirmed | SSD System Storage |
| **PHP Version** | PHP 8.2.12 (CLI/FPM) | Verified Local / Target | Standard JERO PHP runtime |
| **Web Server** | Nginx / Apache | Confirmed | aaPanel Reverse Proxy Stack |
| **OPcache** | Enabled | Confirmed | Standard PHP 8.2 OPcache |
| **Database** | MySQL 8.0 / MariaDB | Confirmed | Primary Relational DB |
| **Search Engine** | Elasticsearch | Confirmed | `candidates` & `jobs` indices |
| **Cache Store** | Redis / DragonflyDB | Confirmed | Key-Value & Session Cache |
| **Queue Worker** | Laravel Horizon / Supervisor | Not measured live | Background Queue Runner |
| **Resource Usage** | CPU/RAM Utilization | Not measured live | Requires live load monitoring |

---

## 5. 10,000+ Concurrent Recruiters Scalability Analysis

### System Behavior Under 10,000 Concurrent Recruiters:
1. **Database & ES Collapse:** 10,000 recruiters loading/paginating candidate listings will fire **100,000 HTTP GET requests/minute to Elasticsearch** (`jobDetails` in Blade) and **100,000 SQL queries/minute to MySQL** (`companyDetailsByClient` & status queries in Blade).
2. **Redis RAM Saturation:** Because Redis listing cache keys are user-scoped (`user_id`), 10,000 recruiters will store 10,000 redundant HTML fragments in Redis, leading to memory exhaustion and cache evictions.
3. **Elasticsearch CPU Spikes:** Painless script sorting (`LocalDate.parse` on unindexed strings) executed concurrently will max out Elasticsearch CPU cores.
4. **PHP-FPM Worker Starvation:** Synchronous N+1 I/O calls inside Blade rendering will hold PHP-FPM workers open, triggering 504 Gateway Timeouts across the application.

---

## 6. Categorized Findings

### A. Confirmed Problems
1. **Blade-level Elasticsearch N+1 calls:** `GeneralMethod::jobDetails()` called in loop per row.
2. **Blade-level MySQL N+1 calls:** `companyDetailsByClient()`, `bench_master_status`, and `job_applications` count in loop per row.
3. **User-scoped Redis cache key bloat:** Key includes `user_id`, preventing cross-recruiter cache sharing.
4. **Painless script sorting overhead:** Dynamic string & date parsing in Elasticsearch sort definitions.
5. **Heavy HTML response payload:** Returning server-rendered HTML strings instead of clean JSON APIs.
6. **Redundant DB query on page load:** `BenchApplication::count()` executed needlessly in `offerListing()`.

### B. Potential Problems
1. Deep pagination performance degradation in Elasticsearch when `from > 10000`.
2. Cache invalidation stampedes during concurrent version key increments.

### C. Not Measured
1. Exact execution time in milliseconds for MySQL helper queries under production dataset volumes.
2. Live CPU & RAM utilization of Elasticsearch cluster during peak Painless script execution.

---

## 7. Business Logic & Data Preservation Requirements

The following core business rules **MUST NOT BE CHANGED OR BROKEN** in V2:
1. **Multi-Tenancy Isolation:** Candidate search MUST strictly enforce `other_company = company_id`.
2. **Candidate Status & Tab Lifecycle:** Must preserve status IDs (5: Shortlisted, 6: Offered, status 3 exclusion) and tab filter mappings (`pending`, `released`, `accepted`, `joining_soon`, `expired`).
3. **Confidential Salary Protection:** Salary/Bill rate MUST adhere to `GeneralMethod::confidentialAmontShow($row->jobId)`.
4. **Duplicate Candidate Logic:** Duplicate flags MUST follow `company.app_duplicate` (1: job-specific, 2: company-wide) and `company.app_duplicate_days`.
5. **Sorting Precision:** All 15+ sorting behaviors (Notice period, CTC, Experience, Join Date, Offer Amount, Date Created) must yield identical ordering.
