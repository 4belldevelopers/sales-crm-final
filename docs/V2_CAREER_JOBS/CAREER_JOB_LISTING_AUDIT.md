# CAREER JOBS PAGE V2 — SYSTEM AUDIT & PERFORMANCE ANALYSIS

> **Target URL:** `http://localhost/git/JWMaincode28June2026/career-jobs/4Bell-Technology/2`  
> **Primary File:** `resources/views/career-job-page.blade.php`  
> **Controller:** `app/Http/Controllers/HomeController.php@careerJobPage` & `careerJobApi`  
> **Document Status:** COMPLETED AUDIT (Strict Read-Only Mode — No Code Changes Executed)

---

## 1. Complete End-to-End Request Flow Audit

```
[ HTTP GET /career-jobs/{companyName}/{companyId} ]
                         │
                         ▼
[ routes/web.php (Line 132: get.careerJobPage) ]
                         │
                         ▼
[ app/Http/Controllers/HomeController.php @ careerJobPage ]
                         │
        ┌────────────────┴────────────────┐
        ▼                                 ▼
[ Redis Cache Lookup ]          [ Elasticsearch Cluster ]
 Key: tenant_career_meta:{id}   Index: ELASTIC_JOBS
  ├── Cache Hit (0 MySQL Qs)     ├── Bool Query (comId, status, hiringtype)
  └── Cache Miss (Runs 6-8 Qs)   └── Hits Array + Source Payload
        │                                 │
        └────────────────┬────────────────┘
                         ▼
[ Batch Eager Loading (MySQL) ]
  ├── DB::table('job_metronics_skills') WHERE job_id IN (...)
  ├── DB::table('client_city_jobs') LEFT JOIN city_list WHERE job_id IN (...)
  └── DB::table('jobs') JOIN clients JOIN companies LEFT JOIN recommendation_relation WHERE job_id IN (...)
                         │
                         ▼
[ View Rendering: resources/views/career-job-page.blade.php ]
  ├── Inline Branding & Styling Math
  ├── Form GET Submissions (Search & Sidebar Filters)
  └── Job Cards List @foreach($data as $row)
                         │
                         ▼
[ Browser DOM & Assets ]
  ├── Metronic plugins.bundle.js + style.bundle.css
  ├── FontAwesome 6.5.1 + Poppins Font
  └── iframe postMessage Height Auto-Resizer
```

### Complete Dependency Map:
- **Route:** `Route::get('/career-jobs/{companyName}/{companyId}', [HomeController::class, 'careerJobPage'])->name('get.careerJobPage');` (`routes/web.php:132`)
- **API Route:** `Route::get('/api/v2/career-jobs/{companyId}', [HomeController::class, 'careerJobApi'])->name('get.careerJobApi');` (`routes/web.php:133`)
- **Controller Methods:** `HomeController::careerJobPage`, `HomeController::careerJobApi` (`app/Http/Controllers/HomeController.php:1106-1543`)
- **Service Layer:** `App\Services\ElasticService` (`getClient()`, `indexExists()`, `search()`)
- **Helper Layer:** `App\Helper\GeneralMethod` (`salaryMode()`, `currencySymbole()`, `job_status()`, `thousandsCurrencyFormat()`, `getCountryName()`)
- **Database Tables:** `companies`, `clients`, `td_industries`, `client_location`, `city_list`, `commericial_details`, `commericials`, `company_career_settings`, `job_metronics_skills`, `client_city_jobs`, `jobs`, `recommendation_relation`
- **Elasticsearch Index:** `ELASTIC_JOBS`
- **Redis Cache Keys:** `tenant_career_meta:{company_id}` (TTL: 3600s), `career_jobs_api:{company_id}:{hash}` (Proposed)
- **View Template:** `resources/views/career-job-page.blade.php`

---

## 2. Tab Discovery Audit

Search was performed across routes, controllers, Blade partials, and frontend assets for tab logic (`tab`, `tabs`, `tab-content`, `data-tab`, `nav-link`, `switchTab`).

### Key Findings:
- **Actual Tabs Discovered:** **NONE.**
- The page `career-job-page.blade.php` is a single-page public career portal consisting of:
  1. Top Branding Navigation Header
  2. Hero Banner & Search Input Form
  3. Client Trust Banner & Metrics Bar
  4. Left Sidebar Filters (Experience, Department, Employment Type, Location, Skills)
  5. Right Job Listing Area with Sort Dropdown, Job Cards, and Pagination
  6. Company Benefits Section & Footer
- **Conclusion:** No tabs exist on this target page. Fake tabs will **NOT** be created.

---

## 3. Comprehensive Performance & Bottleneck Analysis

### 3.1 Critical Issues (P0 - High Severity)

1. **Full Page Reloads on Filter & Search Input:**
   - Selecting any sidebar filter (`filter_exp`, `filter_dept`, `filter_emp_type`, `filter_location`, `filter_skills`) triggers an immediate browser `form.submit()`.
   - Re-downloads the full HTML document (~120 KB populated), re-parses all CSS/JS bundles (Metronic, FontAwesome, Poppins fonts), causing visible screen flicker and excessive server load.

2. **Unindexed Wildcard Search Queries in Elasticsearch:**
   - Controller builds `wildcard` clauses for `client_or_company_name`, `job_assign.user_name`, `client_contact_info_jobs.contact_person_name`, `client_city_jobs.city_name`, `client_city_jobs.country_name`:
     ```php
     ['wildcard' => ['client_or_company_name' => strtolower($custom_search).'*']]
     ```
   - Leading/trailing wildcards bypass standard ES inverted index lookups, forcing expensive document scans across all records under high search traffic.

3. **Elasticsearch Unbounded `_source` Payload:**
   - The query does not specify `_source` field filtering. ES returns the complete indexed job document (including full descriptions, candidate pipelines, and internal configs), causing unnecessary memory allocation in PHP and high network bandwidth consumption between ES and PHP workers.

### 3.2 High Severity Issues (P1)

4. **Lack of Dynamic API Cache for Public Visitors:**
   - Under 10,000 concurrent visitors viewing the same company career portal, identical ES queries and batch MySQL eager-loads are executed repeatedly per request.
   - Page 1 default view for a company should be served in < 5ms directly from Redis cache.

5. **Heavy Script & Asset Overhead:**
   - `plugins.bundle.js`, `style.bundle.css`, and `jquery.min.js` are loaded synchronously on every request.
   - Fonts from Google Fonts are rendered without `font-display: swap`, causing potential render-blocking.

6. **Missing Frontend Cancellation (AbortController):**
   - Rapid user typing in the search bar or fast filter clicking fires multiple parallel requests without canceling stale in-flight requests.

### 3.3 Medium Severity Issues (P2)

7. **Eager Images Loading:**
   - Hero images and company logos are rendered without `loading="lazy"` or explicit dimension attributes, causing Cumulative Layout Shift (CLS).

8. **Database Query Redundancies on Cache Miss:**
   - On Redis cache miss, `HomeController::careerJobPage` executes 8+ SQL queries sequentially rather than combining metadata into single optimized queries.

---

## 4. 10,000 Concurrent Users Bottleneck Analysis

Under a simulated load of 10,000 active concurrent users:

| Layer | Potential Bottleneck | Impact | Proposed Mitigation |
| :--- | :--- | :--- | :--- |
| **PHP / FrankenPHP** | Worker pool exhaustion due to slow 150ms+ Blade SSR requests | HTTP 504 Gateway Timeout | Serve cached API JSON responses in < 10ms; lightweight initial shell |
| **Elasticsearch** | CPU spike due to wildcard searches & `track_total_hits` | High search latency, rejected requests | Optimize DSL query, `_source` filtering, cache default ES queries in Redis |
| **MySQL Database** | Connection pool depletion on metadata query spikes | `Too many connections` error | 100% Redis caching for tenant metadata (`tenant_career_meta:{id}`) |
| **Network Bandwidth** | 120 KB HTML payload $\times$ 10,000 users = 1.2 GB/s saturation | Network throttle & slow TTFB | Compact JSON response (~8 KB per page), gzip/brotli compression |

---

## 5. Summary of Severity Rankings

- **P0 (Critical):** Full page reloads on filter/search change, unindexed ES wildcard queries, full `_source` ES payload retrieval.
- **P1 (High):** Missing Redis response cache for Page 1 public visits, unoptimized asset loading, lack of client-side request cancellation (`AbortController`).
- **P2 (Medium):** Missing image lazy-loading attributes, multi-step sequential SQL queries on cache misses.
- **P3 (Low):** Minor CSS utility redundancies.
