# Agency Marketplace / Staffing Partner Listing V2 — Implementation Plan

> **Document Purpose:** Phase-by-phase engineering plan to implement True Tab Lazy Loading, frontend state management (`AgencyMarketplaceTabManager`), search debouncing, XHR cancellation, and backend query optimizations on `spartnerlisting.blade.php`.  
> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/jobs/spartnerlisting/268`  
> **Primary Target File:** [spartnerlisting.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/jobs/spartnerlisting.blade.php)  
> **Controller:** [ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php)  

---

## Architecture Overview — `AgencyMarketplaceTabManager`

A lightweight JS state manager will encapsulate tab state, track loading status, maintain filter/pagination state, enforce 300ms input debouncing, and cancel obsolete in-flight XHR requests.

```javascript
const AgencyMarketplaceTabManager = {
    activeTab: 'recommended', // 'recommended' | 'registered' | 'assigned'
    tabs: {
        recommended: {
            loaded: false,
            loading: false,
            page: 1,
            filters: {},
            xhr: null,
            lastQuery: ''
        },
        registered: {
            loaded: false,
            loading: false,
            page: 1,
            filters: {},
            xhr: null,
            lastQuery: ''
        },
        assigned: {
            loaded: false,
            loading: false,
            page: 1,
            filters: {},
            xhr: null,
            lastQuery: ''
        }
    },
    // Methods: init(), switchTab(tabName), loadTab(tabName, force), abort(tabName), debounceSearch(tabName, callback)
};
```

---

## 28-Phase Implementation Plan

### Phase 0 — Baseline & Profiling
- **Objective:** Establish baseline measurements for initial page load, AJAX request count, QPS, and memory usage.
- **Exact Files:** None (profiling phase).
- **Target:** Record metrics for 10K concurrency simulation.

### Phase 1 — Full Route, Controller, Service & Query Mapping
- **Objective:** Verify all endpoint contracts and parameter signatures.
- **Exact Files:** `routes/service.php`, `ServiceCompanyJobsController.php`.
- **Endpoints:**
  - `jobs.recommendedData`
  - `jobs.staffingPartnersRegister`
  - `jobs.recommendedAssignList`

### Phase 2 — Initial Page Shell Optimization
- **Objective:** Ensure initial Blade response contains only lightweight HTML structure and skeleton loaders for tabs.
- **Exact Files:** `resources/views/service_company/jobs/spartnerlisting.blade.php`.
- **Changes:** Replace default empty `<tbody>` tags with CSS skeleton loader placeholders.

### Phase 3 — Remove Duplicate Initial Loading Architecture (P0 Fix)
- **Objective:** Eliminate `loadAllDrawerLists()` and `setTimeout(loadAllDrawerLists, 200)` pattern.
- **Exact Files:** `resources/views/service_company/jobs/spartnerlisting.blade.php` (Lines 2206–2212).
- **Impact:** Prevents 6 redundant initial AJAX calls on page load.

### Phase 4 — Recommended Agency True Lazy Loading
- **Objective:** Fetch Recommended Agency data ONCE when active tab is rendered or opened.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Method:** `tableLoad(page, force)` called via `AgencyMarketplaceTabManager.loadTab('recommended')`.

### Phase 5 — My Agency True Lazy Loading
- **Objective:** Fetch My Agency data ONCE when recruiter clicks "My Agency" tab.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Method:** `staffingPartnersRegister(page, force)` called via `AgencyMarketplaceTabManager.loadTab('registered')`.

### Phase 6 — Assigned Agency True Lazy Loading
- **Objective:** Fetch Assigned Agency data ONCE when recruiter clicks "Assigned Agency" tab.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Method:** `recommendedAssignList(page, force)` called via `AgencyMarketplaceTabManager.loadTab('assigned')`.

### Phase 7 — AgencyMarketplaceTabManager State Management
- **Objective:** Implement modular tab state manager in Blade script block.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Features:** Encapsulates state, prevents duplicate first-load calls, coordinates loading indicators.

### Phase 8 — Search Input Debounce Implementation
- **Objective:** Add 300ms debounce to search inputs (`#kt_tagify_3`, `#kt_tagify_4`, `#kt_tagify_5`).
- **Exact Files:** `spartnerlisting.blade.php`.
- **Logic:** `clearTimeout(searchTimer); searchTimer = setTimeout(() => { ... }, 300);`.

### Phase 9 — AJAX Cancellation & Latest-Request-Wins Logic
- **Objective:** Cancel in-flight XHR requests when a new search or tab switch is triggered.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Logic:** `if (tabState.xhr) { tabState.xhr.abort(); }`.

### Phase 10 — Pagination State & Event Optimization
- **Objective:** Handle tab pagination cleanly without listener duplication or page race conditions.
- **Exact Files:** `spartnerlisting.blade.php`.
- **Containers:** `#paginationregisteragency`, `#paginationAssignListagency`.

### Phase 11 — Blade Server-Side Query Cleanup
- **Objective:** Audit and eliminate inline DB lookups in main page view.
- **Exact Files:** `spartnerlisting.blade.php`.

### Phase 12 — N+1 Query & Helper Call Audit
- **Objective:** Ensure all helper functions in `find_agency_recom_ajax.blade.php`, `rec_register_agency_ajax.blade.php`, and `assigned_gency_ajax.blade.php` consume pre-bound batch properties (`_client_info`, `_relation_info`, `_user_size_count`, etc.).
- **Exact Files:**
  - `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php`
  - `resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php`
  - `resources/views/service_company/ajax_pages/assigned_gency_ajax.blade.php`

### Phase 13 — Helper Method Optimization
- **Objective:** Verify batch helper functions in `GeneralMethod.php` (`batchGetClientIdCompany`, `batchGetAgencyRelationData`, etc.) handle empty arrays safely.
- **Exact Files:** `app/Helper/GeneralMethod.php`, `tests/Feature/PartnerListingV2Test.php`.

### Phase 14 — Count & Query Optimization
- **Objective:** Optimize total count queries across Elasticsearch and MySQL.
- **Exact Files:** `ServiceCompanyJobsController.php`.

### Phase 15 — DataTables Frontend Cleanup
- **Objective:** Defer DataTables initialization until table content is populated in active tab.
- **Exact Files:** `spartnerlisting.blade.php`.

### Phase 16 — Response Payload & HTML Payload Optimization
- **Objective:** Minify returned HTML strings from AJAX partial views to reduce network transfer bytes.
- **Exact Files:** `ServiceCompanyJobsController.php`.

### Phase 17 — Redis / DragonflyDB Caching Strategy Audit
- **Objective:** Ensure cache keys are properly versioned per tenant/user and TTLs set to 3600s.
- **Exact Files:** `ServiceCompanyJobsController.php`.

### Phase 18 — Elasticsearch Query Tuning
- **Objective:** Validate ES search body parameters (`track_total_hits`, size limits, painless script scoring).
- **Exact Files:** `ServiceCompanyJobsController.php`.

### Phase 19 — Authorization & Tenant Data Isolation Hardening
- **Objective:** Enforce tenant job ownership check (`Job::where('id', $jobId)->where('company_id', $companyId)->firstOrFail()`) in controller endpoints.
- **Exact Files:** `ServiceCompanyJobsController.php`.

### Phase 20 — 10K Concurrency Load Simulation
- **Objective:** Run Apache JMeter / Locust stress testing against 10,000 concurrent user load model.

### Phase 21 — Stress Testing Under High Write Conditions
- **Objective:** Test concurrent tab loading while invitations are being sent simultaneously.

### Phase 22 — Soak & Memory Leak Testing
- **Objective:** Run 2-hour soak test to verify Redis/Elasticsearch memory stability.

### Phase 23 — Automated & Regression Testing
- **Objective:** Execute PHPUnit test suite `tests/Feature/PartnerListingV2Test.php` and verify route lists.
- **Command:** `php artisan test --filter=PartnerListingV2Test`

### Phase 24 — Production Deployment Plan
- **Objective:** Safe zero-downtime deployment instructions.

### Phase 25 — Real-Time Monitoring & Telemetry
- **Objective:** Configure Laravel Horizon & Elasticsearch APM tracing for `spartnerlisting` endpoints.

### Phase 26 — Emergency Rollback Protocol
- **Objective:** Define immediate rollback steps if issues occur.

### Phase 27 — Final Acceptance Sign-off
- **Objective:** Formal verification against performance targets.

---

## Verification Strategy & Automated Testing

```bash
# Code Syntax & Format Verification
php -l resources/views/service_company/jobs/spartnerlisting.blade.php
php artisan route:list --path=service_company/jobs

# Run Feature Test Suite
php artisan test --filter=PartnerListingV2Test
```
