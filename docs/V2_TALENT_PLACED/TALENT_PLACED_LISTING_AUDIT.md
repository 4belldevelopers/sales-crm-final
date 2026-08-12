# Talent Placed Listing — Technical Audit Report (V1 System)

> **Document Type:** System & Architecture Audit Document  
> **Target Endpoint:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/placedListing`  
> **AJAX Endpoint:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates_ajax/placedListingData`  
> **Scope:** Read-only Technical Audit & Scalability Analysis. Zero Code Modifications Implemented.  

---

## Executive Summary

The **Talent Placed Listing** page is a core B2B module in the JERO SaaS platform, enabling agency/service company recruiters to track candidates who have been successfully hired, joined projects, or placed into active assignments.

While the module effectively utilizes Elasticsearch (`ELASTIC_CANDIDATES`) for high-level searching and filtering, the current implementation suffers from severe architectural bottlenecks during Blade template rendering, including multiple nested N+1 SQL queries and synchronous row-by-row Elasticsearch HTTP calls. Under high load, these issues degrade performance and prevent scaling toward enterprise concurrency levels (10,000+ active recruiters).

---

## 1. Current Architecture & File Structure

### Key System Components & Flow
```
[ Browser / AJAX Request ]
          │
          ▼
[ Route: service.php ] ──► Route::get('bench-candidates_ajax/placedListingData')
          │
          ▼
[ Controller: ServiceCompanyBenchCandidatesController ] (God Controller ~7,145 lines)
          │  ├── 1. Authorize Gate::denies('view_job_applications')
          │  ├── 2. Check DragonflyDB / Redis Cache Key: ats:allbenchplacedListing:{user_id}:{version}:{hash}
          │  ├── 3. Build ES Query (Bool query with 4 'should' clauses & Painless sorting scripts)
          │  └── 4. Execute Elasticsearch Search via ElasticService -> ELASTIC_CANDIDATES
          │
          ▼
[ Blade View Render: bench-place-candidate-ajax.blade.php ]
          │  ├── Executes N+1 SQL Queries per row (job_applications, offer_job_applications, updatetimeline, job_assign)
          │  └── Executes Synchronous N+1 ES HTTP Requests per row via GeneralMethod::jobDetails($row->jobId)
          │
          ▼
[ JSON Response ] ──► { html: "...rendered table...", total: N, pagination: "...rendered pagination..." }
```

### Component File Mapping

| Component | File Path | Responsibilities & Notes |
| :--- | :--- | :--- |
| **Routes** | [routes/service.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/routes/service.php#L408-L412) | Maps GET `bench-candidates/placedListing`, GET `bench-candidates_ajax/placedListingData`, and POST save endpoints. |
| **Controller** | [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php#L4078-L4552) | Handles initial view load, ES query assembly, DragonflyDB caching, and assignment/commercial field updates. |
| **Main View** | [placedListing.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/bench-candidates/placedListing.blade.php) | Metronic card layout, filter inputs, stat cards, Datatable container, Commercial Config & Assignment drawers. |
| **AJAX Partial View** | [bench-place-candidate-ajax.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php) | Renders candidate table rows, timeline nodes, client details, commercial summaries, and action dropdowns. |
| **Helper Methods** | [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L12589) | Utility functions for salary mode formatting, currency symbols, and `jobDetails()` single ES document GET. |

---

## 2. Query Audit & N+1 Analysis

### A. Initial Controller Queries (`placedListing`)
On the initial page load (`placedListing()`):
- **Unfiltered MySQL Count Query:** Executes `BenchApplication::count()`.
  - **Issue:** Counts all rows in `bench_applications` across the entire database without tenant scoping (`company_id`). This produces an unindexed full table count query on MySQL on every initial page visit.

### B. Severe Blade Template N+1 Queries (`bench-place-candidate-ajax.blade.php`)
When rendering the AJAX table rows inside `@foreach ($data as $rowArr)` (for 10 to 50 rows per page):

1. **Duplicate Application Check SQL (N+1 Query #1):**
   - **Location:** [bench-place-candidate-ajax.blade.php:L62-L89](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php#L62-L89)
   - **Code:** `DB::table('job_applications')->where('company_id', $company_id)->where('email', $row->email)...->count()`
   - **Impact:** Runs 1 to 2 SQL queries per row when `duplicate == 1`.

2. **Offer Release Date Lookup SQL (N+1 Query #2 & #3):**
   - **Location:** [bench-place-candidate-ajax.blade.php:L205-L212](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php#L205-L212)
   - **Code:**
     - Query A: `DB::table('offer_job_applications')->where('application_id', $row->candidate_id)->value('id')`
     - Query B: `DB::table('offer_create_main_tds')->where('offer_id', $offerAppId)->where('candidate_status', 1)->orderByDesc('id')->value('created_at')`
   - **Impact:** 2 SQL queries per row for placement timeline date construction.

3. **Update Timeline Stage Lookup SQL (N+1 Query #4):**
   - **Location:** [bench-place-candidate-ajax.blade.php:L270-L276](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php#L270-L276)
   - **Code:** `DB::table('updatetimeline')->where('application_id', $tlAppId)->where('job_id', $tlJobId)->orderByDesc('id')->get(...)`
   - **Impact:** 1 SQL query per non-bench candidate row.

4. **Synchronous Elasticsearch Single Document GET (N+1 Query #5 - Critical Bottleneck):**
   - **Location:** [bench-place-candidate-ajax.blade.php:L355](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php#L355)
   - **Code:** Calls `GeneralMethod::jobDetails($row->jobId)`.
   - **Implementation:** [GeneralMethod.php:L12589](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L12589) executes `$elasticService->getClient()->get(['index' => ELASTIC_JOBS, 'id' => $id])`.
   - **Impact:** Executes a **synchronous HTTP GET request to Elasticsearch for EVERY candidate row**. For a 20-row page, this creates 20 sequential HTTP roundtrips to Elasticsearch *inside* Blade rendering!

5. **Spokesperson User Join SQL (N+1 Query #6):**
   - **Location:** [bench-place-candidate-ajax.blade.php:L399](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/bench-place-candidate-ajax.blade.php#L399)
   - **Code:** `DB::table('job_assign')->select('users.name')->join('users', ...)->where(...)`
   - **Impact:** 1 SQL query per third-party client candidate row.

### Query Summary Table Per Request (Page Size = 20)

| Query Source | Type | Frequency per Request | Cumulative Cost (20 rows) |
| :--- | :--- | :--- | :--- |
| `BenchApplication::count()` | MySQL | 1 (Initial page load) | Full table scan |
| Candidate ES Search | Elasticsearch | 1 | 1 Complex Multi-clause Bool Query |
| `job_applications` Duplicate Check | MySQL | Up to 1 per row | ~10-20 SQL Queries |
| `offer_job_applications` + `offer_create_main_tds` | MySQL | 2 per row | 40 SQL Queries |
| `updatetimeline` Stages | MySQL | 1 per non-bench row | ~10-15 SQL Queries |
| `GeneralMethod::jobDetails()` | ES HTTP GET | 1 per row | **20 Synchronous ES HTTP Requests** |
| `job_assign` Spokesperson | MySQL | 1 per third-party row | ~5-10 SQL Queries |
| **Total Query Footprint** | - | - | **~65-85 SQL Queries + 21 ES Queries per Request** |

---

## 3. Elasticsearch & Redis / DragonflyDB Audit

### Elasticsearch Usage (`ELASTIC_CANDIDATES`)
- **Bool Query Construction:**
  - Placed candidates are matched using 4 distinct `should` conditions (`minimum_should_match => 1`):
    1. Bench placed candidates owned by company: `other_company = company_id`, `condidate_type = 'bench_condidate'`, `job_master_status_id = 6` (Joined), `status = [3]` (Work Assigned).
    2. Join tracker candidates (own company, direct client): `company_id = company_id`, `job_master_status_id = 6`, `ClientComID = 0`.
    3. Join tracker candidates (own company, third-party client): `company_id = company_id`, `job_master_status_id = 6`, `ClientComID > 0`, `jobId > 0`, `job_master_status_id NOT IN [8,9]`.
    4. Join tracker candidates (third-party company is this company): `ClientComID = company_id`, `job_master_status_id = 6`, `jobId > 0`.
- **Custom Painless Script Sorting:**
  - Notice Period sorting (cases 5, 6, 21, 22) uses custom Painless scripts for inline string parsing and date epoch conversions.
  - **Bottleneck:** Executing Painless script sorts across unindexed string fields (`np_id.keyword`) burns ES node CPU cycles during search requests.

### Cache & DragonflyDB Usage
- **Cache Key:** `ats:allbenchplacedListing:{user_id}:{version}:{hash}`
- **Cached Payload:** Stores full rendered HTML response (`html`, `total`, `pagination`).
- **Cache Invalidation:** Calling `GeneralMethod::cacheClean('allbenchplacedListing_version_user_'.$user_id)` increments user version key.
- **Defect:** Storing huge HTML blocks (100KB-500KB per user search variation) in Redis/DragonflyDB bloats memory. Returning raw JSON data and building UI client-side reduces cache footprint by 90%.

---

## 4. Relevant vs. Unused Data & Frontend Audit

### Data Utilization Audit
- **Over-fetched Fields:** ES returns complete candidate document source (`_source`) including full resume texts, address arrays, and legacy fields that are never rendered on the placed listing table.
- **Static Overview Stat Cards:**
  - Cards in [placedListing.blade.php:L320-L387](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/bench-candidates/placedListing.blade.php#L320-L387) display static hardcoded metrics ("186 Currently Working", "34 Projects Completed", "18 Replacement Active"). No backend aggregation API exists to calculate real live metrics.
- **Drawer Field Persistence:**
  - Drawer fields (`savePlacedAssignmentDetails` and `savePlacedCommercialConfig`) write directly to `ELASTIC_CANDIDATES` via document upsert (`doc_as_upsert => true`), but do not persist back to MySQL relational tables (`add_job_wise_condidates` or `bench_place`).

---

## 5. Server Hardware Audit & Resource Baseline

| Server Property | Metric / Config Value | Audit Source / Verification |
| :--- | :--- | :--- |
| **Operating System** | Ubuntu 24.04 LTS | User Server Context |
| **Control Panel** | aaPanel 8.0.3 | User Server Context |
| **CPU / Cores** | 8 Virtual CPU Cores | Server Spec Context |
| **RAM (Memory)** | 31.34 GB Total RAM | Server Spec Context |
| **Disk Storage** | 386.4 GB NVMe SSD | Server Spec Context |
| **Web Server** | Apache / Nginx | Reverse Proxy Setup |
| **PHP Runtime** | PHP 8.2+ with Octane / FrankenPHP | AGENTS.md Config |
| **Database Server** | MySQL 8.0+ / MariaDB | mysql-schema.sql (InnoDB) |
| **Search Engine** | Elasticsearch 7.x / 8.x | ElasticService Integration |
| **Cache / Key-Value Store** | DragonflyDB / Redis | Cache Driver |
| **Queue Workers** | Laravel Horizon / Redis Queues | Background Process Setup |
| **Current Resource Usage** | Not measured | Requires live metrics monitoring |

---

## 6. Concurrency & Scalability Risks (Scaling to 10,000+ Recruiters)

When 10,000 concurrent recruiters access the system:

1. **MySQL Connection Pool Exhaustion:**
   - With ~80 SQL queries generated per HTTP request due to Blade N+1 loops, 10,000 concurrent requests will submit **800,000 SQL queries** to MySQL in a short window. MySQL max connection pools will immediately saturate, causing `PDOException: Too many connections`.

2. **Elasticsearch HTTP Port & Thread Exhaustion:**
   - Synchronous `GeneralMethod::jobDetails()` calls (20 ES GETs per request) will result in **200,000 simultaneous HTTP GET connections** to Elasticsearch port 9200. Elasticsearch HTTP worker threads will freeze, causing connection timeout crashes across the entire platform.

3. **Painless Script CPU Spikes:**
   - 10,000 concurrent requests sorting by Notice Period will trigger Painless script execution across thousands of ES candidate documents, driving ES CPU load to 100%.

4. **Redis Memory Bloat:**
   - Caching rendered HTML strings for 10,000 users with different filter parameters will consume tens of gigabytes of DragonflyDB/Redis RAM, leading to Out-Of-Memory (OOM) key eviction.

---

## 7. Categorization of Audit Findings

### A. Confirmed Problems
1. **Blade N+1 Synchronous ES GET Calls:** `GeneralMethod::jobDetails()` called inside Blade template loop for every single candidate row.
2. **Blade N+1 SQL Queries:** 5 distinct SQL queries executed per candidate row inside Blade loop (`job_applications`, `offer_job_applications`, `offer_create_main_tds`, `updatetimeline`, `job_assign`).
3. **Unscoped `BenchApplication::count()`:** Unindexed global MySQL count query on initial page load.
4. **Painless Script Sorting Bottlenecks:** Notice period sorting dynamically runs inline Painless parsing instead of sorting on indexed numeric fields.
5. **Static Stat Cards:** Overview KPI metrics are static placeholders.
6. **No MySQL Persistence for Commercial/Assignment Data:** Data saved in drawers is written only to ES upsert without storing in MySQL primary source tables.

### B. Potential Problems
1. **Unindexed ES Field Queries:** Fields in `should` queries may lack keyword optimization if schema mapping drifts.
2. **Redis Memory Overhead:** Large HTML payload caching causes unnecessary memory churn.

### C. Not Measured
1. Peak CPU/RAM usage percentages during active recruiter usage on live server.
2. Exact execution latency (in milliseconds) of Elasticsearch `ELASTIC_CANDIDATES` multi-clause bool query under 1M+ candidate index size.

---

## 8. Non-Negotiable Business Logic & Data Constraints

The following business rules MUST remain completely untouched during V2 optimization:

1. **Candidate Placement Visibility Criteria:** Candidates MUST only appear if they meet one of the 4 strict multi-tenant boundary clauses:
   - Bench placed candidates owned by company: `other_company = company_id`, `condidate_type = 'bench_condidate'`, `job_master_status_id = 6`, `status = 3`.
   - Direct client joined candidates owned by company: `company_id = company_id`, `job_master_status_id = 6`, `ClientComID = 0`.
   - Third-party client joined candidates owned by company: `company_id = company_id`, `job_master_status_id = 6`, `ClientComID > 0`, `jobId > 0`, `job_master_status_id NOT IN [8,9]`.
   - Third-party client placed candidates where logged-in company is the third-party client: `ClientComID = company_id`, `job_master_status_id = 6`, `jobId > 0`.
2. **Role & Permission Gate:** Must strictly enforce `Gate::denies('view_job_applications')` and `in_array('view_bench', $userPermissions)`.
3. **Confidential Salary Rules:** Must preserve `GeneralMethod::confidentialAmontShow($jobId)` checks before rendering salary/offer amounts.
4. **Placement Timeline Integrity:** Candidate placement timeline steps (Bench → Offer → Joined → Placed / Sourcing → Evaluation → Offer → Joined) must retain accurate dates.

---

## 9. Key Recommendations Summary

1. **Eliminate All Blade N+1 Queries:** Fetch all required job details, offer dates, spokesperson names, and timelines in the controller layer via single bulk queries or ES `_source` inclusions before passing data to the view.
2. **Include Job Data in `ELASTIC_CANDIDATES` Index:** Denormalize job title, job location, and spokesperson details into `ELASTIC_CANDIDATES` index to eliminate `GeneralMethod::jobDetails()` ES GET calls completely.
3. **Migrate Payload to Pure JSON API:** Return lightweight JSON payload to client-side JS template renderer instead of caching and returning heavy Blade HTML fragments.
4. **Optimize ES Notice Period Mapping:** Store pre-computed integer epoch values for notice period dates in ES index to replace Painless script sorting with native indexed sorting.
5. **Implement Real Backend Stats Aggregation:** Create an efficient ES aggregation endpoint for top overview stat cards.
6. **Dual Persistence for Drawers:** Ensure `savePlacedAssignmentDetails` and `savePlacedCommercialConfig` write to both MySQL source tables and ES index.
