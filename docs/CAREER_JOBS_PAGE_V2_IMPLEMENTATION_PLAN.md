# CAREER JOBS PAGE V2 — PHASE-WISE IMPLEMENTATION PLAN & SCALABILITY SPECIFICATION

> **Target Page:** Career Jobs Listing (`/career-jobs/{companyName}/{companyId}`)  
> **Primary File:** `resources/views/career-job-page.blade.php`  
> **Target Concurrency:** 10,000+ Concurrent Active Users  
> **Document Status:** PROPOSED PLAN (Strict Execution Mode — Awaiting User Approval "APPROVED — IMPLEMENT")

---

## 1. High-Level Architecture Strategy

```
[ INITIAL PUBLIC PAGE SHELL ]
  ├── Renders static HTML framework (Header, Hero, Sidebar, Stats, Benefits, Footer)
  └── Empty Job Cards Container with Skeleton Loaders
                     │
                     ▼
[ CLIENT-SIDE AJAX REQUEST (CareerJobsManager) ]
  ├── Fetches GET /api/v2/career-jobs/{companyId}?page=1&custom_search=...
  ├── Includes AbortController (Cancels stale in-flight requests)
  └── Debounced inputs (300ms for search)
                     │
                     ▼
[ HIGH-PERFORMANCE BACKEND (HomeController@careerJobApi) ]
  ├── Step 1: Redis Tenant Cache (`tenant_career_meta:{id}`) — TTL 3600s
  ├── Step 2: Redis Response Cache (`career_jobs_api:{id}:{hash}`) — TTL 300s
  ├── Step 3: ES Query Optimization (Bounded _source, multi_match search, filter clauses)
  └── Step 4: Batch Eager Loading for current page (Skills, Locations, Logos)
                     │
                     ▼
[ COMPACT JSON RESPONSE (~8 KB) ]
  └── Client-Side Dynamic DOM Rendering (Zero Page Reload, Smooth Height Adjust)
```

---

## 2. Phase-Wise Implementation Roadmap (Phases 0 – 28)

### PHASE 0 — Baseline Benchmarking & Performance Metrics
- **Objective:** Record baseline TTFB, query count, memory, and payload size for `/career-jobs/4Bell-Technology/2`.
- **Target Files:** N/A (Diagnostic)
- **Impact:** Establishes metric baseline (Queries: 13-53, TTFB: 120ms, Payload: 120 KB).

### PHASE 1 — Route, Controller, Service & Query Flow Mapping
- **Objective:** Finalize contracts for `Route::get('/career-jobs/{companyName}/{companyId}')` and `Route::get('/api/v2/career-jobs/{companyId}')`.
- **Target Files:** `routes/web.php`, `app/Http/Controllers/HomeController.php`
- **Impact:** Ensures 100% backward URL compatibility.

### PHASE 2 — Real Tab Discovery & Verification
- **Objective:** Confirm absence of tabs on career page and establish `CareerJobsTabManager` component shell for future extensibility without creating fake tabs.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Clean modular architecture; no fake tab clutter.

### PHASE 3 — Lightweight Initial Page Shell
- **Objective:** Render initial HTML page shell instantly with skeleton placeholders for job cards.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** First Contentful Paint (FCP) drops to < 30ms.

### PHASE 4 — Job List Dynamic Lazy Loading
- **Objective:** Fetch job card list asynchronously via JSON API on page load.
- **Target Files:** `resources/views/career-job-page.blade.php`, `app/Http/Controllers/HomeController.php`
- **Impact:** Eliminates heavy server-side Blade HTML rendering loops.

### PHASE 5 — Tab Lazy Loading Architecture (Tab Manager Engine)
- **Objective:** Equip frontend state manager to handle tab/section switching with zero-request inactive state if tabs are added in future.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Modular tab-ready client engine.

### PHASE 6 — Client Tab & State Manager (`CareerJobsTabManager`)
- **Objective:** Implement client JS manager handling pagination, active filters, sorting, loading skeletons, error states, and retry logic.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Centralized client state handling.

### PHASE 7 — Search Optimization & Debounce Control
- **Objective:** Add 300ms debounce on search input, cancel pending AJAX requests using `AbortController`, and reset pagination to page 1 on search.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Reduces search API request volume by up to 70%.

### PHASE 8 — Sidebar Filter AJAX Optimization
- **Objective:** Convert filter checkboxes (Experience, Department, Employment Type, Location, Skills) to dynamic AJAX triggers without full page reloads.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Smooth, flicker-free UX; updates browser URL via `history.pushState`.

### PHASE 9 — Sorting Preservation & Optimization
- **Objective:** Preserve all 7 sorting options exactly (`Newest First`, `Oldest First`, `Salary Low->High`, `Salary High->Low`, `Company A-Z`, `Full-Time`, `Contract`).
- **Target Files:** `app/Http/Controllers/HomeController.php`, `resources/views/career-job-page.blade.php`
- **Impact:** Identical sort behavior, instant AJAX sort switching.

### PHASE 10 — Pagination State & Filter Query Parameter Audit
- **Objective:** Ensure all active search and sidebar filters are preserved across page changes without losing filter state.
- **Target Files:** `app/Http/Controllers/HomeController.php`, `resources/views/career-job-page.blade.php`
- **Impact:** 100% pagination state accuracy.

### PHASE 11 — MySQL Database Query Optimization
- **Objective:** Select explicit columns instead of `SELECT *` for company metadata lookups.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** Reduces database I/O and RAM usage.

### PHASE 12 — N+1 Query Elimination & Eager Batch Loading
- **Objective:** Batch load skills, locations, and company logos in 3 single `WHERE IN` queries for current page jobs.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** Guarantees 0 per-job SQL queries inside loop.

### PHASE 13 — Compact View Model & DTO Formatting
- **Objective:** Precompute salary display strings, experience ranges, job badges, and time-ago strings upstream in PHP before returning JSON/Blade.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** Zero formatting overhead in frontend/Blade.

### PHASE 14 — Elasticsearch Query DSL & Payload Optimization
- **Objective:** Add `_source` filtering to return only required fields (`id`, `title`, `company_name`, `min_exp`, `max_exp`, `min_salary`, `max_salary`, `min_sal_mode`, `max_sal_mode`, `min_salary_currency`, `max_salary_currency`, `work_type`, `employment_type`, `status`, `created_at`, `job_category_name`, `recommendation_relation`). Replace wildcard searches with `multi_match`.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** ES query response payload size reduced by ~75%.

### PHASE 15 — Redis Cache Layer for Public Visitors
- **Objective:** Cache tenant metadata (`tenant_career_meta:{id}`, TTL: 3600s) and Page 1 API responses (`career_jobs_api:{id}:{hash}`, TTL: 300s).
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** 95%+ of public traffic served directly from Redis in < 5ms.

### PHASE 16 — Compact JSON Payload Optimization
- **Objective:** Compress API response JSON structure to return minimal data (~8 KB per 10 jobs).
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** High bandwidth efficiency under heavy traffic.

### PHASE 17 — Asset & Image Performance Polish
- **Objective:** Add `loading="lazy"` and `decoding="async"` to all images (hero images, logos).
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Prevents Cumulative Layout Shift (CLS) and reduces initial bandwidth.

### PHASE 18 — Frontend Event Delegation & Iframe Polish
- **Objective:** Use event delegation for bookmark buttons and skill tags; optimize `window.parent.postMessage({frameHeight: ...})`.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Seamless height auto-resizing when embedded in iframe.

### PHASE 19 — Security & Multi-Tenant Data Isolation Audit
- **Objective:** Ensure strict tenant scoping (`comId = companyId`) on all ES and MySQL queries.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** Zero cross-tenant data leakage risk.

### PHASE 20 — 10K Concurrent Active Users Load Testing
- **Objective:** Simulate load scenarios A to H (10,000 concurrent users searching, filtering, and paging).
- **Target Files:** N/A (Load Testing)
- **Impact:** Verifies system stability under target concurrency.

### PHASE 21 — Spike Traffic Load Testing
- **Objective:** Simulate sudden burst of 5,000 requests in 1 second.
- **Target Files:** N/A (Load Testing)
- **Impact:** Validates queueing, worker pool resilience, and Redis cache hit rate.

### PHASE 22 — Sustained Soak Testing
- **Objective:** Run continuous load at 2,000 RPS for 30 minutes.
- **Target Files:** N/A (Load Testing)
- **Impact:** Confirms memory leak absence in Octane/FrankenPHP workers and Redis memory stability.

### PHASE 23 — Failure Mode & Circuit Breaker Testing
- **Objective:** Test behavior when MySQL, ES, or Redis is slow/unavailable.
- **Target Files:** `app/Http/Controllers/HomeController.php`
- **Impact:** Graceful error handling and retry prompts on frontend.

### PHASE 24 — Functional & Visual Regression Testing
- **Objective:** Verify 100% feature parity (Apply button links, Details button links, branding colors, fonts, hero layouts).
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Zero business logic regression.

### PHASE 25 — SEO & Canonical URL Compatibility
- **Objective:** Ensure title tags, meta descriptions, robots, canonical tags, Google Analytics (GA4), and Facebook Pixel work seamlessly.
- **Target Files:** `resources/views/career-job-page.blade.php`
- **Impact:** Preserves SEO rankings and analytics tracking.

### PHASE 26 — Production Rollout Strategy
- **Objective:** Clear Redis cache tags, deploy optimized controller and Blade files.
- **Target Files:** All affected files
- **Impact:** Safe zero-downtime deployment.

### PHASE 27 — Instant Rollback Strategy
- **Objective:** Restore previous controller and view files if anomalies occur.
- **Target Files:** All affected files
- **Impact:** Complete safety guarantee.

### PHASE 28 — Final Acceptance & Verification
- **Objective:** Validate response times, query counts, and load test results against SLAs.
- **Target Files:** N/A (Sign-off)
- **Impact:** Project sign-off.

---

## 3. Business Logic Preservation Checklist

- [x] Multi-tenant isolation (`company_id` filter mandatory).
- [x] Recommendation logic (cross-company shared jobs display correct company name and handle `final_status`).
- [x] Salary formatting (`thousandsCurrencyFormat` and currency symbols).
- [x] Job status badging (`Hiring`, `New`, `Closed`, `Removed`, `Hold`).
- [x] Apply (`/job-apply/{id}/{company_id}`) and View Details (`/job-details/{id}/{company_id}`) routing.
- [x] Iframe height auto-resizing via `postMessage`.
- [x] Advanced branding customizations from `company_career_settings`.
- [x] SEO meta tags, Google Analytics 4, and Facebook Pixel tracking.
