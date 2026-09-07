# V2 BENCH CANDIDATE DETAIL PAGE IMPLEMENTATION PLAN
**Target Concurrency:** 10,000 Concurrent Active Recruiters  
**Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/{id}`  
**Primary View:** `resources/views/service_company/bench-candidates/showdetail.blade.php`  
**Primary Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`  
**Status:** PROPOSAL / WAITING FOR APPROVAL  

---

## 1. Target V2 Architecture Overview

The V2 Bench Candidate Detail Architecture shifts from monolithic initial rendering to a **Lightweight Modular Lazy-Loaded Architecture**.

```
+-----------------------------------------------------------------------------------+
|                            INITIAL DRAWER REQUEST                                 |
|          GET /service_company/bench-candidates/{id} (Profile Overview Only)        |
+-----------------------------------------------------------------------------------+
                                          │
                                          ▼
                      ┌───────────────────────────────────────┐
                      │ HTML Payload: ~15.5 KB (Overview Only) │
                      └───────────────────────────────────────┘
                                          │
                  ┌───────────────────────┴───────────────────────┐
                  ▼                                               ▼
+-----------------------------------+           +-----------------------------------+
|      TAB 2: ADS SETTING           |           |        TAB 3: RESUME / CV         |
|  GET /.../bench-candidates/{id}   |           |   GET /.../bench-candidates/{id}  |
|           /tab/ads                |           |           /tab/resume             |
|    (Triggered on Tab Click)       |           |    (Triggered on Tab Click)       |
+-----------------------------------+           +-----------------------------------+
                  │                                               │
                  ▼                                               ▼
       [Loads Ads Setting Component]                   [Loads Resume Viewer & PDF Stream]
```

### Core Architecture Pillars:
1. **Shared Tenant Redis Caching:** Unified cache key (`ats:bench_detail:{company_id}:{candidate_id}:v2`) shared across all recruiters within the same tenant.
2. **True Tab Lazy Loading:** `Ads Setting` and `Resume / CV` tab HTML & data are rendered on-demand via distinct lightweight endpoints when clicked.
3. **Client-Side Tab Manager (`BenchCandidateDetailTabManager`):** Manages tab lifecycle, loaded states, request cancellation (`AbortController`), and race-condition resolution (`Latest-Wins`).
4. **Zero-Elasticsearch Drawer Path:** Eliminates redundant ES searches for detail views, fetching relationship managers via optimized MySQL queries / Redis cache.
5. **Local Asset Bundling:** Eliminates external CDN dependencies for PDF.js workers and libraries.

---

## 2. Phase-Wise Implementation Roadmap (Phases 0 to 25)

---

### Phase 0 — Baseline & Metrics Collection
- **Objective:** Establish empirical performance baselines for page load time, HTML payload size, DB query count, and Redis memory usage before any code modifications.
- **Files:** N/A (Tooling/Metrics setup).
- **Routes:** `GET /service_company/bench-candidates/{id}`
- **Methods:** `ServiceCompanyBenchCandidatesController@show`
- **Changes Proposed:** Log baseline metrics using Laravel Telescope / Debugbar / APM in dev environment.
- **Dependencies:** None.
- **Risk:** Zero.
- **Testing:** Verify baseline TTFB (ms) and HTML payload size (KB).
- **Rollback:** N/A.
- **Acceptance Criteria:** Baseline metrics recorded: Payload ~50.5 KB, DB queries 8-11, ES calls 1.

---

### Phase 1 — Request Flow & Route Restructuring
- **Objective:** Define explicit lazy-loading routes for Ads Setting and Resume/CV tabs.
- **Files:** `routes/service.php`
- **Routes:**
  - `GET service_company/bench-candidates/{id}/tab/ads` (`bench-candidates.tab.ads`)
  - `GET service_company/bench-candidates/{id}/tab/resume` (`bench-candidates.tab.resume`)
- **Methods:** `ServiceCompanyBenchCandidatesController@getAdsTab`, `getResumeTab`
- **Changes Proposed:** Register route definitions in `routes/service.php` scoped under `service_company` middleware.
- **Dependencies:** Phase 0.
- **Risk:** Low.
- **Testing:** Run `php artisan route:list --path=service_company/bench-candidates`.
- **Rollback:** Remove new routes.
- **Acceptance Criteria:** New tab routes registered clean without route conflicts.

---

### Phase 2 — Security & Tenancy Hardening
- **Objective:** Eliminate cross-tenant ID enumeration risk by enforcing strict `company_id` scoping on candidate queries.
- **Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`, `getAdsTab`, `getResumeTab`
- **Changes Proposed:** Ensure all queries include `->where('job_applications.company_id', $company_id)` or verify candidate tenancy authorization before returning data.
- **Dependencies:** Phase 1.
- **Risk:** Low (Security improvement).
- **Testing:** Test accessing Candidate ID of Company A using Company B credentials -> must return 403 / 404.
- **Rollback:** Revert query condition.
- **Acceptance Criteria:** 100% tenant isolation verified on drawer endpoints.

---

### Phase 3 — Shared Tenant Candidate Cache Engine (Redis)
- **Objective:** Replace fragmented per-user cache key with unified shared tenant candidate cache key.
- **Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`
- **Changes Proposed:** Change cache key format from `ats:benchviewmore:{user_id}:...` to `ats:bench_detail:{company_id}:{candidate_id}:v2`.
- **Dependencies:** Phase 2.
- **Risk:** Medium.
- **Testing:** Inspect Redis keys when Recruiter A and Recruiter B view the same candidate -> verify single shared key hit.
- **Rollback:** Revert cache key generator method.
- **Acceptance Criteria:** 99%+ Redis cache sharing across recruiters within the same company.

---

### Phase 4 — Elimination of Elasticsearch Overhead in Drawer
- **Objective:** Remove redundant `ELASTIC_BENCH_ASSIGN` ES query from candidate detail view path.
- **Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`
- **Changes Proposed:** Remove `$this->elasticService->getClient()->search($params)` from `show()` method; rely on optimized MySQL `bench_assign` join/query.
- **Dependencies:** Phase 3.
- **Risk:** Low.
- **Testing:** Verify relationship manager list renders identically without ES call.
- **Rollback:** Re-enable ES block if needed.
- **Acceptance Criteria:** Zero Elasticsearch HTTP requests on drawer open.

---

### Phase 5 — MySQL Query & N+1 Optimization
- **Objective:** Consolidate SQL queries, remove duplicate `$skillExperiencesCount` count query, and optimize JOINs.
- **Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`
- **Changes Proposed:**
  - Replace separate `where(...)->count()` with `$skillExperiences->count()`.
  - Fix `companies` join condition to use `job_applications.company_id`.
  - Eager-load master data (qualification, status, location).
- **Dependencies:** Phase 4.
- **Risk:** Low.
- **Testing:** Monitor query count with Laravel Query Monitor / Telescope -> verify reduction to 3 SQL queries on cache miss.
- **Rollback:** Revert query adjustments.
- **Acceptance Criteria:** Uncached query count drops from 11 to <= 3 queries.

---

### Phase 6 — Blade Template Optimization & Direct Query Extraction
- **Objective:** Move inline raw SQL queries (`\DB::table('companies')...`) out of Blade views into the controller/service layer.
- **Files:** `resources/views/service_company/bench-candidates/showdetail.blade.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`
- **Changes Proposed:** Pass pre-fetched, cached company logo and name variables directly to Blade view; remove `@php \DB::table('companies')... @endphp` from Blade.
- **Dependencies:** Phase 5.
- **Risk:** Low.
- **Testing:** Inspect Blade template for zero raw DB calls.
- **Rollback:** Restore template variable assignment.
- **Acceptance Criteria:** Zero raw database calls in Blade templates.

---

### Phase 7 — Profile Overview Light-Weight Initial View Extraction
- **Objective:** Extract Profile Overview HTML into an initial drawer payload, excluding inactive tab markup.
- **Files:** `resources/views/service_company/bench-candidates/showdetail.blade.php`
- **Routes:** `GET service_company/bench-candidates/{id}`
- **Methods:** `show`
- **Changes Proposed:** Remove `@include('service_company.partials.resume_cv_viewer')` and `#boostkeywords` inner HTML from `showdetail.blade.php`, replacing them with lazy-load container containers: `<div id="tab_ads_content" data-loaded="false"></div>` and `<div id="tab_resume_content" data-loaded="false"></div>`.
- **Dependencies:** Phase 6.
- **Risk:** Medium.
- **Testing:** Verify initial HTML payload size drops from 50.5 KB to < 16 KB.
- **Rollback:** Re-include partials.
- **Acceptance Criteria:** Payload size <= 16 KB (-68% reduction).

---

### Phase 8 — Ads Setting Tab Endpoint & Lazy Loading
- **Objective:** Implement dedicated lightweight controller method and Blade view for Ads Setting tab.
- **Files:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
  - `resources/views/service_company/bench-candidates/partials/tab_ads_setting.blade.php` [NEW]
- **Routes:** `GET service_company/bench-candidates/{id}/tab/ads`
- **Methods:** `getAdsTab($id)`
- **Changes Proposed:** Render Ads Setting tab markup on demand. Cache response per candidate.
- **Dependencies:** Phase 7.
- **Risk:** Low.
- **Testing:** Click "Ads Setting" tab -> verify AJAX GET request succeeds and content renders.
- **Rollback:** Revert endpoint.
- **Acceptance Criteria:** Ads tab loads on demand in < 40ms.

---

### Phase 9 — Resume / CV Tab Endpoint & Lazy Loading
- **Objective:** Implement dedicated controller method and partial for Resume / CV viewer.
- **Files:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
  - `resources/views/service_company/bench-candidates/partials/tab_resume_cv.blade.php` [NEW]
- **Routes:** `GET service_company/bench-candidates/{id}/tab/resume`
- **Methods:** `getResumeTab($id)`
- **Changes Proposed:** Move `resume_cv_viewer.blade.php` inclusion into `getResumeTab()` endpoint.
- **Dependencies:** Phase 8.
- **Risk:** Medium.
- **Testing:** Click "Resume / CV" tab -> verify viewer renders and PDF loads dynamically.
- **Rollback:** Revert endpoint.
- **Acceptance Criteria:** Resume tab markup loaded ONLY on demand.

---

### Phase 10 — Client-Side `BenchCandidateDetailTabManager` Implementation
- **Objective:** Create modular JavaScript Tab Manager class to handle active/loading/loaded/error states.
- **Files:** `public/demo/js/bench_candidate_tab_manager.js` [NEW]
- **Routes:** N/A (Frontend JS).
- **Methods:** Class `BenchCandidateDetailTabManager`
- **Changes Proposed:**
  ```javascript
  class BenchCandidateDetailTabManager {
      constructor(candidateId) {
          this.candidateId = candidateId;
          this.tabs = {
              overview: { loaded: true, loading: false },
              ads: { loaded: false, loading: false, url: `/service_company/bench-candidates/${candidateId}/tab/ads` },
              resume: { loaded: false, loading: false, url: `/service_company/bench-candidates/${candidateId}/tab/resume` }
          };
          this.abortControllers = {};
      }
      switchTab(tabName) { ... }
      loadTab(tabName) { ... }
  }
  ```
- **Dependencies:** Phase 9.
- **Risk:** Low.
- **Testing:** Test switching tabs repeatedly -> verify subsequent clicks reuse cached DOM without duplicate AJAX.
- **Rollback:** Revert script.
- **Acceptance Criteria:** First click fetches tab via AJAX; second click reuses loaded content instantaneously.

---

### Phase 11 — AJAX Cancellation, AbortControllers & Latest-Request-Wins Logic
- **Objective:** Prevent race conditions and network congestion when recruiters rapidly switch tabs or candidates.
- **Files:** `public/demo/js/bench_candidate_tab_manager.js`
- **Routes:** N/A.
- **Methods:** `cancelPendingRequests()`
- **Changes Proposed:** Attach `AbortController` to in-flight tab requests. Abort pending request if recruiter switches tab or closes drawer before response arrives.
- **Dependencies:** Phase 10.
- **Risk:** Low.
- **Testing:** Rapidly click tabs 10 times in 1 second -> verify stale requests are aborted (`DOMException: AbortError`).
- **Rollback:** Remove `AbortController` logic.
- **Acceptance Criteria:** Zero stale DOM overwrites during rapid tab switching.

---

### Phase 12 — Resume Viewer & PDF.js Memory Optimization
- **Objective:** Clean up PDF.js canvas elements and object URLs when switching away from Resume tab or closing drawer.
- **Files:** `resources/views/service_company/partials/resume_cv_viewer.blade.php`
- **Routes:** N/A.
- **Methods:** `rcvDestroyViewer()`
- **Changes Proposed:** Add explicit cleanup function to destroy `pdfDoc`, clear canvas contexts, and unbind event listeners.
- **Dependencies:** Phase 11.
- **Risk:** Low.
- **Testing:** Open and close Resume tab 20 times -> inspect browser memory graph for memory leaks.
- **Rollback:** Revert cleanup function.
- **Acceptance Criteria:** Zero DOM node / memory accumulation on repeated tab toggles.

---

### Phase 13 — Local PDF.js & Worker Asset Bundling
- **Objective:** Eliminate external CDN dependencies (`cdnjs.cloudflare.com`) by serving PDF.js library and worker locally.
- **Files:**
  - `public/assets/plugins/pdfjs/pdf.min.js` [NEW]
  - `public/assets/plugins/pdfjs/pdf.worker.min.js` [NEW]
  - `resources/views/service_company/partials/resume_cv_viewer.blade.php`
- **Routes:** N/A.
- **Methods:** Update script loader paths.
- **Changes Proposed:** Change `workerSrc` from `https://cdnjs.cloudflare.com/...` to `{{ asset('assets/plugins/pdfjs/pdf.worker.min.js') }}`.
- **Dependencies:** Phase 12.
- **Risk:** Low.
- **Testing:** Disconnect internet access to CDN -> verify PDF viewer still renders perfectly.
- **Rollback:** Restore CDN URLs.
- **Acceptance Criteria:** Zero external network calls required to load PDF viewer.

---

### Phase 14 — Client-Side "Download with Logo" Worker Optimization
- **Objective:** Optimize high-resolution canvas capture during "Download Resume with Logo" to prevent browser freezes.
- **Files:** `resources/views/service_company/partials/resume_cv_viewer.blade.php`
- **Routes:** N/A.
- **Methods:** `buildPdf(opts)`
- **Changes Proposed:** Process PDF pages asynchronously in chunks with `requestAnimationFrame` yielding to prevent blocking main UI thread.
- **Dependencies:** Phase 13.
- **Risk:** Low.
- **Testing:** Trigger "Download with Logo" on a 10-page resume -> verify browser UI remains smooth and responsive.
- **Rollback:** Revert async generator loop.
- **Acceptance Criteria:** Zero browser thread blocking during PDF generation.

---

### Phase 15 — Tagify & DOM Memory Cleanup
- **Objective:** Prevent Tagify instance duplication and memory leaks on drawer reload.
- **Files:** `resources/views/service_company/bench-candidates/showdetail.blade.php`
- **Routes:** N/A.
- **Methods:** Tagify binding script.
- **Changes Proposed:** Check if Tagify instance already exists on input before creating new instance (`if (!input.__tagify) new Tagify(input)`).
- **Dependencies:** Phase 14.
- **Risk:** Low.
- **Testing:** Open candidate drawer 15 times -> check JS heap for multiple Tagify instances.
- **Rollback:** Revert instance check.
- **Acceptance Criteria:** Exactly 1 Tagify instance created per element.

---

### Phase 16 — Failure Handling & Fallback UI States
- **Objective:** Add graceful error handling and retry UI components for failed tab AJAX requests.
- **Files:** `public/demo/js/bench_candidate_tab_manager.js`
- **Routes:** N/A.
- **Methods:** `renderErrorState(tabName)`
- **Changes Proposed:** Display user-friendly error card with "Retry" button when network request fails or times out.
- **Dependencies:** Phase 15.
- **Risk:** Low.
- **Testing:** Simulate 500 error on tab endpoint -> verify retry UI appears and retry button re-fetches content.
- **Rollback:** Revert error template.
- **Acceptance Criteria:** Clean error state displayed; no broken drawer UI.

---

### Phase 17 — Functional Regression Testing
- **Objective:** Perform thorough regression testing across all candidate detail features.
- **Files:** All modified files.
- **Routes:** All bench candidate routes.
- **Methods:** All modified methods.
- **Changes Proposed:** N/A (Testing phase).
- **Dependencies:** Phase 16.
- **Risk:** Low.
- **Testing:** Verify Profile Overview display, Edit Profile link, Change Status modal, Add Relationship Manager drawer, Contact Masking, Resume download, and Ads switch toggle.
- **Rollback:** Fix any regression found.
- **Acceptance Criteria:** 100% existing functional behavior preserved.

---

### Phase 18 — Security & Tenancy Verification Matrix
- **Objective:** Validate multi-tenant data isolation and contact masking under all user role permissions.
- **Files:** N/A.
- **Routes:** All drawer endpoints.
- **Methods:** Tenancy authorization logic.
- **Changes Proposed:** N/A (Verification phase).
- **Dependencies:** Phase 17.
- **Risk:** Critical.
- **Testing:** Verify cross-company candidate access requests return HTTP 403 Forbidden. Verify contact masking active for external candidates.
- **Rollback:** N/A.
- **Acceptance Criteria:** Zero authorization leaks or contact masking bypasses.

---

### Phase 19 — 1K Load Test Verification
- **Objective:** Benchmark system under 1,000 concurrent active recruiters.
- **Files:** N/A (Load test execution).
- **Routes:** All bench candidate endpoints.
- **Methods:** N/A.
- **Changes Proposed:** Execute k6 / Locust load testing script simulating 1,000 active recruiters opening candidate drawers.
- **Dependencies:** Phase 18.
- **Risk:** Low (Staging environment).
- **Testing:** Measure p95 latency (< 60ms), error rate (< 0.01%), and DB CPU (< 15%).
- **Rollback:** Optimize bottleneck if targets missed.
- **Acceptance Criteria:** 1K load test passes all performance thresholds.

---

### Phase 20 — 5K Load Test Verification
- **Objective:** Benchmark system under 5,000 concurrent active recruiters.
- **Files:** N/A.
- **Routes:** All bench candidate endpoints.
- **Methods:** N/A.
- **Changes Proposed:** Execute k6 load test simulating 5,000 active recruiters.
- **Dependencies:** Phase 19.
- **Risk:** Low (Staging environment).
- **Testing:** Measure p95 latency (< 90ms), error rate (< 0.01%), Redis CPU (< 20%).
- **Rollback:** Adjust Redis memory limits if needed.
- **Acceptance Criteria:** 5K load test passes all performance thresholds.

---

### Phase 21 — 10K Load Test Verification
- **Objective:** Benchmark system under target scale of 10,000 concurrent active recruiters.
- **Files:** N/A.
- **Routes:** All bench candidate endpoints.
- **Methods:** N/A.
- **Changes Proposed:** Execute full scale k6 load test with 10,000 virtual users performing drawer opens, tab switches, and resume downloads.
- **Dependencies:** Phase 20.
- **Risk:** Low (Staging environment).
- **Testing:** Measure p95 latency (< 120ms), p99 latency (< 250ms), error rate (< 0.01%), MySQL connection pool utilization (< 40%).
- **Rollback:** Tweak Octane/FrankenPHP worker limits if required.
- **Acceptance Criteria:** 10,000 concurrent load test successfully passed.

---

### Phase 22 — Hotspot Protection & Rate Limiting
- **Objective:** Configure adaptive rate limits on tab lazy loading endpoints to prevent DoS abuse.
- **Files:** `app/Http/Kernel.php` or `routes/service.php`
- **Routes:** Lazy tab endpoints.
- **Methods:** `throttle:120,1`
- **Changes Proposed:** Apply throttle middleware (120 requests/minute per recruiter) to tab lazy loading endpoints.
- **Dependencies:** Phase 21.
- **Risk:** Low.
- **Testing:** Send >120 requests in 1 minute -> verify 429 Too Many Requests response.
- **Rollback:** Adjust throttle limits.
- **Acceptance Criteria:** Rate limiter blocks rapid automated scraping/DoS attempts cleanly.

---

### Phase 23 — Observability & APM Instrumentation
- **Objective:** Add performance logging and metrics tags to monitor V2 drawer latency in production.
- **Files:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **Routes:** All drawer endpoints.
- **Methods:** Log / APM spans.
- **Changes Proposed:** Instrument key operations with custom APM tags (`bench_detail_load_ms`, `tab_lazy_load_ms`).
- **Dependencies:** Phase 22.
- **Risk:** Low.
- **Testing:** Verify custom metrics appear in APM dashboard.
- **Rollback:** Remove instrumentation spans.
- **Acceptance Criteria:** Real-time visibility into drawer performance in production.

---

### Phase 24 — Canary Deployment Strategy
- **Objective:** Deploy V2 architecture to 5% of service companies initially to validate live performance.
- **Files:** Feature flag configuration.
- **Routes:** All drawer endpoints.
- **Methods:** `FeatureFlag::isEnabled('v2_bench_candidate_detail')`
- **Changes Proposed:** Wrap V2 tab manager and lazy endpoints behind tenant feature flag.
- **Dependencies:** Phase 23.
- **Risk:** Low.
- **Testing:** Enable feature flag for Test Company -> verify V2 active; disable -> verify V1 fallback active.
- **Rollback:** Disable feature flag globally.
- **Acceptance Criteria:** Seamless zero-downtime canary rollout capability.

---

### Phase 25 — Rollback Strategy & Final 10K Acceptance
- **Objective:** Complete full 100% rollout upon passing canary verification and establish emergency rollback protocol.
- **Files:** Feature flag / Deployment scripts.
- **Routes:** All drawer endpoints.
- **Methods:** Full activation.
- **Changes Proposed:** Enable V2 globally across 100% of tenants.
- **Dependencies:** Phase 24.
- **Risk:** Low.
- **Testing:** Verify zero production errors across all service companies over 24-hour window.
- **Rollback:** Instant toggle of feature flag back to V1 if critical anomaly detected.
- **Acceptance Criteria:** Global 100% deployment completed; 10K concurrency verified.

---

## 3. Testing Matrix (Phase 18)

| # | Test Scenario | Expected Outcome | Status |
|---|---|---|---|
| 1 | Profile Overview Initial Open | Drawer opens in < 50ms; renders Profile Overview ONLY. HTML size < 16 KB. | Pending |
| 2 | Ads Setting First Click | AJAX GET to `/tab/ads` triggered; spinner shown; Ads content loaded in < 40ms. | Pending |
| 3 | Ads Setting Second Click | Instant display from DOM cache; 0 network requests. | Pending |
| 4 | Resume / CV First Click | AJAX GET to `/tab/resume` triggered; viewer markup rendered; PDF fetched asynchronously. | Pending |
| 5 | Resume / CV Second Click | Instant display from DOM cache; PDF viewer remains active without re-parsing. | Pending |
| 6 | Rapid Tab Switching | AbortController cancels pending request; latest clicked tab wins; no DOM mismatch. | Pending |
| 7 | Close Drawer Pending Request | In-flight AJAX requests aborted immediately; JS memory released. | Pending |
| 8 | Candidate Missing Resume | Resume tab disabled/hidden cleanly; no JS console errors. | Pending |
| 9 | Corrupted PDF File | Displays fallback Google Docs viewer / user notice cleanly. | Pending |
| 10 | DOC / DOCX Resume | Triggers server-side LibreOffice converter proxy cleanly. | Pending |
| 11 | External CDN Offline | Local PDF.js assets load flawlessly without CDN connection. | Pending |
| 12 | Resume Download Regular | Native PDF file downloaded directly via download endpoint. | Pending |
| 13 | Resume Download with Logo | High-res canvas generated asynchronously; banner overlaid; download completed. | Pending |
| 14 | Network Timeout (3000ms) | Displays error card with "Retry" button. | Pending |
| 15 | HTTP 401 Unauthorized | Redirects cleanly to login modal/page without script crash. | Pending |
| 16 | HTTP 403 Cross-Tenant Access | Returns 403 Forbidden error card; no candidate data exposed. | Pending |
| 17 | Contact Masking (Other Company) | Candidate email and phone masked with asterisk/hashes server-side. | Pending |
| 18 | Contact Masking (Own Company) | Candidate email and phone revealed completely. | Pending |
| 19 | Ads Switch Toggle Click | Sends POST `benchSwitch`; updates UI status text & sweetAlert notice. | Pending |
| 20 | Change Status Modal Trigger | Opens Change Status modal with target candidate ID pre-bound. | Pending |
| 21 | Add Relationship Manager Drawer | Opens RM assignment drawer seamlessly. | Pending |
| 22 | 1,000 Concurrent Recruiters | Average response time < 60ms; 0% error rate. | Pending |
| 23 | 5,000 Concurrent Recruiters | Average response time < 90ms; 0% error rate. | Pending |
| 24 | 10,000 Concurrent Recruiters | Average response time < 120ms; p99 < 250ms; 0.00% error rate. | Pending |
| 25 | Emergency Rollback Test | Feature flag toggle instantly reverts UI to V1 layout within 1 second. | Pending |

---

## 4. Final Acceptance Checklist

- [ ] Deep audit completed and documented in `docs/V2_BENCH_CANDIDATE_DETAIL/BENCH_CANDIDATE_DETAIL_AUDIT.md`.
- [ ] Phase-wise implementation plan documented in `docs/V2_BENCH_CANDIDATE_DETAIL/BENCH_CANDIDATE_DETAIL_V2_IMPLEMENTATION_PLAN.md`.
- [ ] True tab lazy-loading architecture designed for Ads Setting and Resume / CV tabs.
- [ ] Client-side `BenchCandidateDetailTabManager` designed with `AbortController` and latest-request-wins logic.
- [ ] Tenancy and security authorization controls verified for lazy-loaded endpoints.
- [ ] 10,000 concurrency risk analysis modeled with zero-ES and shared Redis cache strategies.
- [ ] **CONFIRMED:** No application code, database schema, Redis, Elasticsearch, or server configuration files were modified.
- [ ] **STOPPED:** Awaiting user approval (`APPROVED — IMPLEMENT`).
