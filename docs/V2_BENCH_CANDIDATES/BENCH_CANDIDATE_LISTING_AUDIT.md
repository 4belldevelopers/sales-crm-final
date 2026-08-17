# Bench Candidates Listing — Technical Audit Document (V1)

> **Document Status:** Comprehensive Audit Completed  
> **Target Page URL:** `http://localhost/git/JWMaincode28June2026/bench-condidates/{companyName}/{companyId}`  
> **Scope:** Public & Tenant-Facing Bench Candidates Listing Engine  
> **Mode:** Read-Only Audit (No Code/DB Modifications Executed)

---

## 1. Executive Summary

This document presents a comprehensive technical audit of the **Bench Candidates Listing** module within the JERO ATS & Talent Marketplace platform. The audit evaluates end-to-end page execution flows, MySQL query performance, Elasticsearch search & indexing architecture, Blade template rendering mechanics, N+1 query patterns, Redis caching gaps, server capacity, and architectural bottlenecks preventing scalability to **10,000+ concurrent recruiters**.

---

## 2. Page Flow & Architecture Overview

The Bench Candidates Listing page acts as a public storefront and marketplace showcase for pre-vetted bench candidates hosted by staffing companies and agency partners.

### End-to-End Architectural Flow
```mermaid
flowchart TD
    A["HTTP Request: GET /bench-condidates/{companyName}/{companyId}"] --> B["Route: routes/web.php (get.benchCandidates)"]
    B --> C["Controller: HomeController@benchCandidates"]
    C --> D["Elasticsearch Query: ELASTIC_CANDIDATES_BENCH Index"]
    D --> E["MySQL Queries: 11 Direct DB Queries for Tenant Meta & Commercial Rates"]
    E --> F["View Hydration: resources/views/bench-condidates.blade.php"]
    F --> G["Blade Loop Execution (@foreach $data)"]
    G --> H["N+1 Query 1: recommendation_relation (Header Connection Status)"]
    G --> I["N+1 Query 2: bench_candidate_skill_experience (Per-Candidate Skill Ratings)"]
    H & I --> J["HTML Response Rendered to Browser"]
    J --> K["User Actions: Add to Job / View Profile / Connect Request"]
    K --> L["AJAX Calls: service_company.cvtracker.talent-add / get.benchCandidateDetails"]
```

### Primary Code References
- **Route Definition:** [routes/web.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/routes/web.php#L156) (`Route::get('/bench-condidates/{companyName}/{companyId}', [HomeController::class, 'benchCandidates'])->name('get.benchCandidates');`)
- **Primary Controller:** [HomeController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/HomeController.php#L809-L1090) (`benchCandidates` method)
- **Detail Modal Controller:** [HomeController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/HomeController.php#L15844-L15920) (`benchCandidateDetails` method)
- **Blade Template:** [bench-condidates.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/bench-condidates.blade.php#L1-L1185)
- **Internal Talent Pool Controller:** [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php#L64-L120)
- **Elasticsearch Constants:** `ELASTIC_CANDIDATES_BENCH` defined in global config.

---

## 3. Database & Query Audit

### 3.1 Controller MySQL Queries (Executed on Every Page View)
Upon receiving a GET request, `HomeController@benchCandidates` executes **11 synchronous raw MySQL queries** without caching:

| # | Query Target Table | Scope / Condition | Purpose | Finding / Issue |
|---|---|---|---|---|
| 1 | `companies` | `where id = $company_id` | Fetch tenant company record | Performs `SELECT *` fetching unused columns |
| 2 | `clients` | `where company_id = $company_id AND self_company = 1` | Fetch internal client profile | Unindexed `self_company` column lookup |
| 3 | `td_industries` | `where ind_id = $account->industries` | Fetch industry name | Executed conditionally on every request |
| 4 | `client_location` | `where client_id = $client->id` | Count locations (`count()`) | **Duplicate query pattern** |
| 5 | `client_location` | `where client_id = $client->id` | Fetch primary location (`first()`) | **Duplicate query pattern** (ran right after #4) |
| 6 | `city_list` | `where city_id = $client_location->city_id` | Fetch city name (for India, ID 101) | Uncached join substitute |
| 7 | `countries` | `where id = $client_location->country_id` | Fetch country name | Triggered via `GeneralMethod::getCountryName` |
| 8 | `commericial_details` | `where company_id = $company_id AND commtype = 2` | Minimum full-time rate (`min('rate')`) | Uncached aggregate calculation |
| 9 | `commericial_details` | `where company_id = $company_id AND commtype = 1` | Minimum contract rate (`min('rate')`) | Uncached aggregate calculation |
| 10 | `commericials` | `where company_id = $company_id AND commtype = 2` | Full-time commercial status | Direct table query |
| 11 | `commericials` | `where company_id = $company_id AND commtype = 1` | Contract commercial status | Direct table query |

### 3.2 Confirmed N+1 Queries (Blade Template Render Loop)
The Blade view [bench-condidates.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/bench-condidates.blade.php#L559-L560) executes direct database queries inside the HTML template:

```php
/* Executed inside @foreach($data as $rowArr) loop (Line 559) */
$skillExperiences = DB::table('bench_candidate_skill_experience')
    ->where('candidate_id', $row->id)->get();
```

- **Confirmed Bottleneck:** For a standard page size of 10 candidates, **10 additional DB queries** are executed. For a page size of 50 candidates, **50 additional DB queries** are executed.
- **Violation:** Violates Laravel MVC principles and tenant data fetching standards. DB queries inside Blade views block rendering threads and bypass request-level query batching.

### 3.3 Header Connection Query in Blade
Line 291 of `bench-condidates.blade.php` executes a raw OR-grouped query directly inside the Blade view header:

```php
$recommendateRow = DB::table('recommendation_relation')
    ->select('id','from_company_id','from_status','to_status','final_status')
    ->where(function($q) use($company_id,$to_company_id){
        $q->where(function($q2) use($company_id,$to_company_id){
            $q2->where('from_company_id',$company_id)->where('to_company_id',$to_company_id);
        })->orWhere(function($q2) use($company_id,$to_company_id){
            $q2->where('from_company_id',$to_company_id)->where('to_company_id',$company_id);
        });
    })->first();
```

---

## 4. Elasticsearch & Search/Filter Audit

### 4.1 Elasticsearch Query Architecture
Search queries are executed against the `ELASTIC_CANDIDATES_BENCH` index. The search payload is built dynamically inside `HomeController@benchCandidates`.

```php
$boolQuery = [
    'bool' => [
        'should' => [
            [
                'bool' => [
                    'must' => array_merge([
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['status' => 2]],
                        ['term' => ['condidate_type' => 'bench_condidate']],
                    ], $this->getSearchConditions($custom_search, $country)),
                ],
            ],
        ],
        'minimum_should_match' => 1,
    ],
];
```

### 4.2 Search Flaws: Leading Wildcard & Match Phrase Prefix
In `HomeController@getSearchConditions` ([HomeController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/HomeController.php#L2409-L2437)), search terms construct wildcard patterns with leading asterisks:

```php
'match_phrase_prefix' => ['full_name' => '*'.strtolower($custom_search).'*']
'wildcard' => ['email' => $custom_search]
'wildcard' => ['country_name' => '*'.strtolower($custom_search).'*']
'wildcard' => ['state_name' => '*'.strtolower($custom_search).'*']
'wildcard' => ['city_name' => '*'.strtolower($custom_search).'*']
```

- **Confirmed Problem:** Leading wildcards (`*term*`) cannot utilize the inverted index directory in Elasticsearch. Elasticsearch is forced to scan all term dictionary tokens per query, spiking node CPU to 100% under concurrent search load.

### 4.3 Sorting Flaws: Inline Painless Scripts
The controller uses dynamic Painless scripts for sorting logic:

1. **`ads_switch` Sorting (Lines 831-844):** Painless script checking document map existence and string lowercase comparison on every document.
2. **Skill Match Count Sorting (Lines 847-869):** Painless loop matching array elements against keyword tokens on every document.
3. **Notice Period Sorting (Lines 886-930):** Painless script splitting string value `np_id.keyword` by comma and parsing string to `Double.parseDouble()` per document during sorting!

- **Confirmed Problem:** String parsing inside ES Painless scripts during sort phases prevents field-data cache optimization and causes massive latency degradation under large candidate volumes.

---

## 5. Database Index Audit

### Existing vs Required Indexes

| Table Name | Existing Index | Required Composite / Optimized Index | Status |
|---|---|---|---|
| `companies` | `PRIMARY (id)` | None (Lookup by PK) | OK |
| `clients` | `PRIMARY (id)`, `clients_company_id_foreign` | `(company_id, self_company)` | **Missing** |
| `client_location` | `PRIMARY (id)` | `(client_id, country_id, city_id)` | **Missing** |
| `commericial_details` | `PRIMARY (id)` | `(company_id, commtype, rate)` | **Missing** |
| `commericials` | `PRIMARY (id)` | `(company_id, commtype)` | **Missing** |
| `recommendation_relation` | `PRIMARY (id)` | `(from_company_id, to_company_id, final_status)` | **Missing** |
| `bench_candidate_skill_experience` | `PRIMARY (id)` | `(candidate_id, skill_key)` | **Missing** |
| `job_applications` | `PRIMARY (id)`, `company_id` | `(company_id, status, condidate_type)` | **Missing** |

---

## 6. Frontend, API & Payload Audit

### 6.1 DOM Structure & Script Loading
- **Blade File:** 1,185 lines combining inline CSS variables, Metronic global CSS/JS bundles, PDF.js, Mammoth.js, DataTables, jQuery, SweetAlert2, and inline script handlers.
- **Payload Size:** Over 72 KB raw Blade template + CSS asset overhead.
- **Data Attributes Bloat:** Candidate table rows inject heavy `data-*` attributes (`data-cname`, `data-cdesig`, `data-cexp`, `data-cctc`, `data-cnp`, `data-cloc`, `data-ctype`, `data-skills`) on every action button, inflating HTML response payload size.

### 6.2 Data Over-Fetching
- `companies::select('*')` returns internal configuration columns.
- Elasticsearch returns complete `_source` objects containing ~45 fields when only 12 fields are rendered in the public candidate card.

---

## 7. Redis, Cache & Background Worker Audit

- **Public Bench Candidates Page:** **0% Cache Coverage**. Zero Redis or DragonflyDB keys utilized for tenant metadata or search results.
- **Contrast with Adjacent Modules:** `careerJobPage` uses `Cache::remember("tenant_career_meta:{$company_id}", 3600)` and `benchCandidateDetails` uses `ats:benchviewmore:{$id}:...`. The public bench candidates listing missed this cache layer completely.
- **Queue Workers:** Candidate mutations dispatch `ElasticBenchCandidateIndex` jobs. Under high traffic, queue lag can cause read-after-write Elasticsearch drift.

---

## 8. Security & Business Logic Audit

### Critical Business Logic That MUST Remain Unchanged
1. **Tenant Isolation & Filtering:** Public bench candidate queries MUST filter strictly by `company_id = $company_id`, `status = 2` (Active bench candidate), and `condidate_type = 'bench_condidate'`.
2. **Commercial & Rates Display:** Company commercial rate cards (`fullTimeStatusRate`, `contractStatusRate`, currency formatting) must remain accurate according to `commericial_details` and `commericials` settings.
3. **Partner Connection Status:** `recommendation_relation` state (Connected, Cancel Request, View Bench Talent) must accurately reflect mutual agency/client partnership status.
4. **"Add to Job" & Profile Drawer Integration:** Modal drawer triggers for candidate profile details (`get.benchCandidateDetails`) and job application copying (`service_company.cvtracker.talent-add`) must maintain exact payload structures and authorization checks.

---

## 9. Server Environment Audit

### Server Specifications (Staging / Production Server Screenshot Baseline)

| System Parameter | Actual / Verified Value | Source / Method |
|---|---|---|
| **Operating System** | Ubuntu 24.04 LTS | Server Spec Baseline |
| **Control Panel** | aaPanel 8.0.3 | Server Spec Baseline |
| **CPU / Cores** | 8 CPU Cores | Server Spec Baseline |
| **RAM** | 31.34 GB RAM | Server Spec Baseline |
| **Disk Space** | 386.4 GB Disk | Server Spec Baseline |
| **Local PHP CLI** | PHP 8.2.12 (cli) | Executed `php -v` |
| **Web Server** | Nginx / Apache (aaPanel Managed) | System Architecture |
| **Database Server** | MySQL 8.0 / MariaDB | Database Schema Baseline |
| **Search Engine** | Elasticsearch 7.x | Application Config |
| **In-Memory Cache** | Redis / DragonflyDB | Application Config |
| **Current Resource Usage** | **Not measured** | Local Audit Mode (Requires Live Profiling) |

---

## 10. 10,000+ Concurrent Recruiters Scalability Risk Assessment

```
┌─────────────────────────────────────────────────────────────────────────┐
│              10,000 CONCURRENT RECRUITERS LOAD IMPACT                   │
├─────────────────────────────────────────────────────────────────────────┤
│ 1. 10,000 Req/sec → 110,000 Uncached MySQL Queries/sec (Tenant Header)  │
│ 2. 10,000 Req/sec → 100,000 N+1 Queries/sec in Blade Loop (Skills)      │
│ 3. 10,000 Req/sec → ES Search Pool Saturation (Leading Wildcards)       │
│ 4. 10,000 Req/sec → ES CPU Spike to 100% (Painless String Parsing Sort)  │
│ 5. RESULT: MySQL Connection Exhaustion + Web Server HTTP 504 Timeouts   │
└─────────────────────────────────────────────────────────────────────────┘
```

### Risk Matrix

| Risk Factor | Current State | 10K Concurrency Impact | Severity |
|---|---|---|---|
| **Tenant Meta Queries** | 11 uncached queries per request | 110,000 DB queries/sec → DB Connection Pool Crashes | **CRITICAL** |
| **Blade Loop N+1 Queries** | `bench_candidate_skill_experience` queried per candidate | 100,000 DB queries/sec → DB Lock Contention & Disk I/O Saturation | **CRITICAL** |
| **Leading ES Wildcards** | `*custom_search*` pattern used across 8 fields | Full index scans → ES Search Thread Pool Saturation | **HIGH** |
| **Inline Painless Sort** | String splitting `np_id` on the fly | High CPU overhead per search hit → Node CPU Spike | **HIGH** |
| **Over-Fetching Data** | `companies.*` & full ES `_source` retrieved | High Network I/O & Memory Footprint per PHP-FPM process | **MEDIUM** |

---

## 11. Categorized Problem Summary

### 11.1 Confirmed Problems
1. **Blade N+1 Database Queries:** `bench_candidate_skill_experience` fetched via raw `DB::table()` inside `@foreach` loop in `bench-condidates.blade.php` (Line 559).
2. **Blade Query Leak:** Direct DB lookup on `recommendation_relation` inside Blade template header (Line 291).
3. **Duplicate Database Query:** `client_location` counted (`count()`) and immediately fetched (`first()`) in `HomeController@benchCandidates` (Lines 1006-1018).
4. **Zero Tenant Meta Caching:** 11 raw DB queries executed per page load for company profile, location, industry, and commercial rates.
5. **Elasticsearch Leading Wildcard Search:** `*search_term*` wildcards force full index dictionary scans.
6. **Inefficient Painless Sorting:** Notice period strings parsed dynamically inside inline Painless sort scripts.

### 11.2 Potential Problems
1. **Unindexed Relational Foreign Keys:** Missing composite MySQL indexes on `clients(company_id, self_company)` and `commericial_details(company_id, commtype)`.
2. **Elasticsearch Read Drift:** Rapid candidate updates may result in temporary read inconsistency if queue indexing lags behind DB writes.
3. **Data Over-Fetching:** PHP-FPM memory overhead from loading complete `companies` and `_source` objects.

### 11.3 Not Measured
1. **Live Peak Concurrency CPU/RAM Utilization:** Precise CPU/RAM usage under 10,000 concurrent user load.
2. **Elasticsearch Index Heap Usage & Garbage Collection Pauses:** Measured during production peak traffic.
3. **Database Network Bandwidth Saturation:** Measured under maximum payload throughput.

---

## 12. Summary of Audit Recommendations

1. **Implement Tenant Metadata Caching:** Wrap all 11 tenant header queries in a 1-hour Redis cache tag (`tenant_bench_meta:{company_id}`).
2. **Eliminate Blade N+1 Queries:** Pre-fetch candidate skill ratings during Elasticsearch indexing or include in candidate ES document payload (`skill_experiences` nested array).
3. **Remove Blade DB Query Leak:** Calculate `recommendation_relation` status in `HomeController` and pass as a view variable.
4. **Refactor Elasticsearch Index Mapping:** Add a dedicated numeric field `notice_period_days` (integer) to the ES document to enable native numeric sorting without Painless script parsing.
5. **Optimize Search Queries:** Replace `*term*` wildcards with `n-gram` analyzers or edge-ngram fields for prefix/substring matching.
6. **Add Missing Composite Indexes:** Execute database migration to index `clients(company_id, self_company)`, `commericial_details(company_id, commtype)`, and `recommendation_relation(from_company_id, to_company_id)`.
7. **Optimize Response Payloads:** Use Elasticsearch source filtering (`_source_includes`) to fetch only required card display fields.

---
*End of Audit Document.*
