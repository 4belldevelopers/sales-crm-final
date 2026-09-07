# CLIENT DETAILS SCREEN: PHASE-WISE V2 IMPLEMENTATION PLAN

> **Goal**: Re-architect Client Details screen (Internal Client & Direct Client drawers) for 10,000+ concurrent active recruiters without changing business logic or user experience.
> **Directory**: `docs/V2_CLIENT_DETAILS/`
> **Audit Companion Document**: [CLIENT_DETAILS_TABS_AUDIT.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/CLIENT_DETAILS_TABS_AUDIT.md)

---

## PHASE 0 — Baseline & Instrumentation

- **Objective**: Establish precise latency, database query count, memory, and throughput telemetry for Client Details drawers before applying code changes.
- **Current Problem**: No targeted APM metrics exist specifically tracking tab-by-tab performance or ES aggregation execution times.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
- **Current Flow**: Requests execute without custom OpenTelemetry / Laravel Telescope / Prometheus span markers.
- **Proposed V2 Flow**: Wrap controller methods and ES queries in OpenTelemetry / Prometheus performance measurement spans with `client_id` and `tab` dimensions.
- **Expected Code Changes**: Add lightweight timing loggers or APM middleware markers around `viewMore`, `dviewMore`, and tab AJAX methods.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: None.
- **ES Changes**: None.
- **Queue Changes**: None.
- **Frontend Changes**: Add `performance.mark()` and `performance.measure()` tags in JavaScript for drawer shell rendering and tab click response times.
- **Performance Benefit**: High-precision visibility into p50, p95, p99 latency baseline.
- **Risk**: Low.
- **Dependencies**: None.
- **Testing**: Open Client Details drawer 50 times in local/staging environment and verify log/telemetry capture.
- **Rollback**: Remove instrumentation lines.
- **Acceptance Criteria**: Telemetry captures breakdown of server processing time vs network transfer vs DOM rendering.

---

## PHASE 1 — Initial Client Details Shell Optimization

- **Objective**: Deliver a zero-delay static Blade shell for the Client Details drawer (< 100ms response time).
- **Current Problem**: `viewMore` and `dviewMore` execute 5 heavy Elasticsearch aggregations and 3 SQL queries synchronously before rendering HTML.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` (`viewMore`, `dviewMore`)
  - `resources/views/service_company/client/show.blade.php`
  - `resources/views/service_company/client/dviewMore.blade.php`
- **Current Flow**: Controller builds full dashboard charts + header KPIs + tab HTML synchronously -> Returns full JSON payload (~170KB).
- **Proposed V2 Flow**: Controller fetches basic client info from MySQL/ES (or Redis cache) -> Renders lightweight header & Overview tab ONLY -> Client Dashboard and hidden tab data fetched asynchronously on demand.
- **Expected Code Changes**: Remove dashboard ES aggregations from `viewMore`/`dviewMore` controller methods.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Store cached client basic profile payload under `ats:client_shell:{client_id}` with 1-hour TTL.
- **ES Changes**: Fetch basic client details by document ID `ELASTIC_CLIENT_LIST` (`size: 0` aggregations removed).
- **Queue Changes**: None.
- **Frontend Changes**: Update drawer wrapper JS to receive lightweight shell and initialize tab event listeners without triggering 5 concurrent background AJAX requests.
- **Performance Benefit**: Initial drawer open latency drops from ~850ms to **< 60ms** (14x speedup). Response payload reduced from 170KB to **< 15KB**.
- **Risk**: Low.
- **Dependencies**: Phase 0.
- **Testing**: Verify drawer opens instantly with Overview data populated correctly.
- **Rollback**: Restore synchronous ES aggregation calls in `viewMore`/`dviewMore`.
- **Acceptance Criteria**: `viewMore` HTTP GET request executes **0 heavy ES aggregations** and completes under 100ms.

---

## PHASE 2 — KPI Query Optimization

- **Objective**: Consolidate and cache header KPI cards (Active Jobs, Candidates Submitted, Interviews Scheduled, Offers Generated, Successful Joins).
- **Current Problem**: Header KPI cards execute separate helper methods (`InternalClientJobsCountAllStatus`, `InternalClientCandidatesCountList`) inside Blade `@php` blocks, running 3 separate ES searches per page load.
- **Exact Files/Modules**:
  - `app/Helper/GeneralMethod.php` (`InternalClientJobsCountAllStatus`, `InternalClientCandidatesCountList`)
  - `resources/views/service_company/client/show.blade.php`
  - `resources/views/service_company/client/dviewMore.blade.php`
- **Current Flow**: 3 sequential ES queries executed inside Blade view during HTML compilation.
- **Proposed V2 Flow**: Combine job and candidate KPI metrics into a single batch helper function `getClientHeaderKPIs($clientId)` cached in Redis for 15 minutes.
- **Expected Code Changes**: Refactor KPI calls out of Blade templates into controller data array. Use single combined Redis key `ats:client_kpi:{company_id}:{client_id}`.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Add cache key `ats:client_kpi:{company_id}:{client_id}` (15 min TTL).
- **ES Changes**: Combine term aggregations into single multi-search request.
- **Queue Changes**: None.
- **Frontend Changes**: Populate KPI numbers dynamically from shell response data.
- **Performance Benefit**: Eliminates 3 ES queries per drawer open; header KPI latency drops to < 2ms (Redis cache hit).
- **Risk**: Low.
- **Dependencies**: Phase 1.
- **Testing**: Verify KPI numbers match database counts exactly across internal and direct clients.
- **Rollback**: Revert to inline `GeneralMethod` calls.
- **Acceptance Criteria**: ES queries for KPI headers reduced from 3 queries to 0 on cache hit.

---

## PHASE 3 — Contacts Optimization

- **Objective**: Optimize Contacts tab DataTables query and eliminate full table scans.
- **Current Problem**: Contacts DataTables server-side count query triggers full table scans on `client_contact_info` due to missing `client_id` index.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` (`contactdata`, `dcontactList`)
  - `resources/views/service_company/client/show.blade.php` (`#contactList` DataTable JS)
- **Current Flow**: DataTables initializes on `$(document).ready` -> Runs un-indexed SQL query on `client_contact_info`.
- **Proposed V2 Flow**: DataTables initializes ONLY when user clicks the "Contacts" tab -> Runs indexed SQL query with explicit column selection.
- **Expected Code Changes**: Update JS to initialize DataTables lazily on tab click. Refactor `contactdata()` query builder to select indexed columns only.
- **DB Changes**: None (Indexes added in Phase 11).
- **Index Changes**: `idx_client_contact_client_id` on `client_contact_info(client_id)` (See Phase 11).
- **Redis Changes**: Cache contact count in Redis `ats:client_contact_count:{client_id}`.
- **ES Changes**: None.
- **Queue Changes**: None.
- **Frontend Changes**: Bind DataTables init to `#contacts` tab activation event. Add single-instance check `if (!$.fn.DataTable.isDataTable('#contactList'))`.
- **Performance Benefit**: Eliminates 1 unnecessary AJAX call on page load. Contacts tab load time < 50ms.
- **Risk**: Low.
- **Dependencies**: Phase 1.
- **Testing**: Switch to Contacts tab, verify data populates correctly and searching/pagination works seamlessly.
- **Rollback**: Restore eager DataTables init in `$(document).ready`.
- **Acceptance Criteria**: DataTables request fires ONLY after clicking "Contacts" tab.

---

## PHASE 4 — Jobs Optimization

- **Objective**: Optimize Jobs tab AJAX endpoint, reduce payload size, and cache job list results.
- **Current Problem**: `client_job_data_ajax` retrieves entire ES job documents (including heavy descriptions), causing large response payloads.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (`client_job_data_ajax`)
  - `resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php`
- **Current Flow**: Eager AJAX load on page ready -> Search `ELASTIC_JOBS` without `_source` filtering -> Render heavy HTML table.
- **Proposed V2 Flow**: Lazy AJAX load on tab click -> Search `ELASTIC_JOBS` with strict `_source` list (`id`, `title`, `status`, `min_exp`, `max_exp`, `min_salary`, `max_salary`, `created_at`) -> Cache rendered view in Redis.
- **Expected Code Changes**: Add `_source` field filter array to ES query in `client_job_data_ajax`. Cache rendered HTML for 5 minutes.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Add cache key `ats:client_jobs_view:{client_id}:{page}` (5 min TTL).
- **ES Changes**: Add `_source: ['id', 'title', 'status', 'min_exp', 'max_exp', 'min_salary', 'max_salary', 'created_at', 'hiring_manager', 'skills']`.
- **Queue Changes**: None.
- **Frontend Changes**: Trigger `fetchJobsClient()` only when "Jobs" tab is selected.
- **Performance Benefit**: ES response payload size reduced by 85%. Tab rendering speed increased by 3x.
- **Risk**: Low.
- **Dependencies**: Phase 1.
- **Testing**: Verify Jobs tab displays title, experience, salary, status, and pipeline stages accurately.
- **Rollback**: Remove `_source` restriction.
- **Acceptance Criteria**: `client_job_data_ajax` returns payload < 20KB and executes in < 80ms.

---

## PHASE 5 — Candidate Optimization

- **Objective**: Eliminate N+1 SQL queries in candidate list rendering and convert pagination to keyset/cursor mode.
- **Current Problem**: `custom_client_candidates.blade.php` executes 6 SQL queries per row inside `@foreach`, generating 60+ DB queries per page view.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`clientCandidateList`, `directclientCandidateList`)
  - `resources/views/service_company/ajax_pages/custom_client_candidates.blade.php`
- **Current Flow**: Controller queries ES `ELASTIC_CANDIDATES` -> Passes array to Blade -> Blade executes raw `DB::table()` queries inside loop.
- **Proposed V2 Flow**: Denormalize display values (job mode, currency symbol, city name, spok person name, child status name) into `ELASTIC_CANDIDATES` ES index during sync/indexing OR pre-batch all lookup maps in controller before passing to Blade.
- **Expected Code Changes**: In `clientCandidateList()`, collect all `jobId`, `city_id`, `child_status_id` from ES hits -> Query lookup tables once using `whereIn()` -> Pass lookup maps to Blade. Zero SQL queries inside Blade loop!
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Cache candidate status map in Redis `ats:ref:candidate_statuses` (24 hr TTL).
- **ES Changes**: Include pre-computed display attributes in `_source`.
- **Queue Changes**: None.
- **Frontend Changes**: Trigger `fetchCandidates()` only when "Candidate" tab is selected. Add 300ms debounce to candidate search input.
- **Performance Benefit**: Reduces candidate tab SQL queries from 60+ to **0** (with ES denormalization) or **3 batched queries** (with controller lookup maps).
- **Risk**: Medium (Requires careful verification of display fields).
- **Dependencies**: Phase 1.
- **Testing**: Verify candidate name, experience, salary, notice period, location, source, upload date, job, and stage render identically.
- **Rollback**: Revert controller lookup map logic.
- **Acceptance Criteria**: Candidate tab AJAX request executes **0 N+1 queries** in Blade.

---

## PHASE 6 — Interview Tracker Optimization

- **Objective**: Optimize Interview Tracker tab data fetching, batch interviewer details, and add optimistic UI updates for follow-ups.
- **Current Problem**: Interview Tracker tab eagerly loads on page ready; follow-up status updates trigger blocking synchronous AJAX calls with browser alerts.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`clientCandidateInterviewTracker`)
  - `resources/views/service_company/ajax_pages/custom_client_candidates_interview_tracker.blade.php`
  - `resources/views/service_company/client/show.blade.php` (`citSetFollowUp`)
- **Current Flow**: Eager load on page ready -> Search `ELASTIC_INERTVIEW_TRACKER` -> Follow-up update makes blocking POST request.
- **Proposed V2 Flow**: Lazy load on tab click -> Fetch indexed interview documents -> Follow-up update applies optimistic UI change immediately and sends background non-blocking AJAX request.
- **Expected Code Changes**: Add optimistic UI state update in `citSetFollowUp()`. Batch interviewer user profile lookups in controller.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Cache interview status options in Redis.
- **ES Changes**: Ensure `ELASTIC_INERTVIEW_TRACKER` has index mapping on `schedule_date` and `client_id`.
- **Queue Changes**: Optional background queue job for non-critical follow-up notification dispatch.
- **Frontend Changes**: Trigger `fetchCandidatesInverViewTracker()` only on "Interview Tracker" tab click. Update follow-up dropdown to update DOM state instantly.
- **Performance Benefit**: Instant UI response on follow-up action (< 5ms perceived latency). Eliminates 1 initial page load request.
- **Risk**: Low.
- **Dependencies**: Phase 1.
- **Testing**: Test scheduling follow-ups, changing statuses, and verifying state persistence after page refresh.
- **Rollback**: Restore synchronous AJAX update handler.
- **Acceptance Criteria**: Perceived follow-up update latency < 10ms; initial tab load lazy.

---

## PHASE 7 — Joinings Optimization

- **Objective**: Optimize Joinings tab pipeline, eliminate duplicate queries, and share optimized candidate view component.
- **Current Problem**: Joinings tab eagerly executes on page load and suffers from the same N+1 SQL loop in `custom_client_candidates.blade.php`.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`clientCandidateJoinList`)
  - `resources/views/service_company/ajax_pages/custom_client_candidates.blade.php`
- **Current Flow**: Eager load on page ready -> Search `ELASTIC_CANDIDATES` with `job_master_status_id = 6` -> Render via N+1 Blade template.
- **Proposed V2 Flow**: Lazy load on tab click -> Reuse batched lookup map logic from Phase 5 -> Render clean view with zero N+1 queries.
- **Expected Code Changes**: Apply batched lookup maps in `clientCandidateJoinList()`.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Cache joining summary counts in Redis.
- **ES Changes**: Add cached filter on `job_master_status_id = 6`.
- **Queue Changes**: None.
- **Frontend Changes**: Trigger `fetchCandidatesJoin()` only when "Joinings" tab is activated.
- **Performance Benefit**: SQL query count reduced from 60+ to 0-3 queries.
- **Risk**: Low.
- **Dependencies**: Phase 5.
- **Testing**: Verify joined candidates list matches active placement records.
- **Rollback**: Revert to previous method.
- **Acceptance Criteria**: Joinings tab loads lazily with 0 N+1 queries.

---

## PHASE 8 — Client Dashboard Optimization

- **Objective**: Implement lazy initialization and async API loading for Client Dashboard Chart.js analytics.
- **Current Problem**: 5 complex ES date_histogram aggregations run synchronously on initial page load, even when user never visits the Client Dashboard tab.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
  - `resources/views/service_company/client/show.blade.php` (`#candidateDashboard` tab pane & Chart.js scripts)
- **Current Flow**: Server computes all chart datasets during `viewMore` GET request -> Embeds raw JSON datasets in inline script tags.
- **Proposed V2 Flow**: Server returns empty canvas containers -> When user clicks "Client Dashboard" tab for the first time, JS fires a single async AJAX request GET `service_company/clients/dashboard-data/{id}` -> Renders Chart.js charts asynchronously.
- **Expected Code Changes**: Create dedicated API endpoint `getDashboardAnalytics($clientId)`. Move Chart.js initialization logic into tab activation event handler (`shown.bs.tab`). Cache dashboard payload in Redis for 30 minutes.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Add cache key `ats:client_dashboard_data:{client_id}` (30 min TTL).
- **ES Changes**: Execute ES multi-search (`msearch`) for all 5 chart aggregations in a single network round-trip.
- **Queue Changes**: None.
- **Frontend Changes**: Remove inline Chart.js execution on page load. Add lazy chart init on `#candidateDashboard` tab click.
- **Performance Benefit**: Removes 5 heavy ES aggregations from initial page load. Saves ~400ms server processing time per drawer open!
- **Risk**: Medium (Requires proper Chart.js canvas destruction and re-render handling).
- **Dependencies**: Phase 1.
- **Testing**: Click Client Dashboard tab, verify all 5 charts render smoothly with correct data and tooltips.
- **Rollback**: Restore server-side inline chart data injection.
- **Acceptance Criteria**: Initial page request executes **0 dashboard ES aggregations**. Dashboard tab fetches data via 1 cached AJAX call on click.

---

## PHASE 9 — AJAX / Frontend Optimization

- **Objective**: Implement tab lazy loading architecture, search debouncing, request cancellation, and DOM lifecycle cleanup.
- **Current Problem**: Duplicate requests, missing request Cancellation (`abort()`), un-debounced search inputs, and DataTables re-initialization memory leaks.
- **Exact Files/Modules**:
  - `resources/views/service_company/client/show.blade.php`
  - `resources/views/service_company/client/dviewMore.blade.php`
- **Current Flow**: Ad-hoc jQuery functions attached to `keyup` and `$(document).ready`. No state tracking of active tab requests.
- **Proposed V2 Flow**: Clean modular tab manager (`ClientDrawerTabManager` JS object) with:
  1. `loadedTabs` set tracking initialized tabs.
  2. `activeXHR` map for in-flight request cancellation on search/page change.
  3. 300ms debounce on search inputs.
  4. Memory cleanup on drawer close (`destroy()` DataTables & Tooltips).
- **Expected Code Changes**: Refactor inline scripts in `show.blade.php` and `dviewMore.blade.php` into structured JS tab manager.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: None.
- **ES Changes**: None.
- **Queue Changes**: None.
- **Frontend Changes**: Add `ClientDrawerTabManager` script block.
- **Performance Benefit**: Eliminates duplicate AJAX requests, prevents stale search response race conditions, and eliminates browser memory leaks.
- **Risk**: Low.
- **Dependencies**: Phases 1-8.
- **Testing**: Rapidly switch between tabs, type fast in search boxes, and inspect network tab to confirm stale requests are canceled.
- **Rollback**: Revert to standard jQuery handlers.
- **Acceptance Criteria**: Rapid tab switching fires 0 duplicate requests; search inputs debounced by 300ms.

---

## PHASE 10 — N+1 Elimination

- **Objective**: Guarantee zero N+1 database queries across all Client Details tabs and sub-views.
- **Current Problem**: Relational lookup queries executed iteratively inside Blade `@foreach` loops.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `resources/views/service_company/ajax_pages/custom_client_candidates.blade.php`
  - `resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php`
- **Current Flow**: Queries executed per row.
- **Proposed V2 Flow**: Strict separation of data fetching and presentation:
  - All lookup maps (`jobs`, `cities`, `users`, `candidate_statuses`) pre-fetched in batch via `whereIn()` array keys.
  - Zero DB queries inside Blade views.
- **Expected Code Changes**: Add strict query linting check in dev/test environment to fail tests if N+1 queries occur during tab rendering.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Cache static global lookup tables (`candidate_status`, `city_list`, `job_categories`) in Redis.
- **ES Changes**: None.
- **Queue Changes**: None.
- **Frontend Changes**: None.
- **Performance Benefit**: Consistent O(1) database performance regardless of page size or candidate count.
- **Risk**: Low.
- **Dependencies**: Phases 4, 5, 7.
- **Testing**: Enable Laravel Query Log (`DB::enableQueryLog()`) and assert query count <= 3 per tab AJAX request.
- **Rollback**: Revert controller batching.
- **Acceptance Criteria**: `DB::getQueryLog()` reports <= 3 SQL queries for any tab list endpoint.

---

## PHASE 11 — Database Index & Query Optimization

- **Objective**: Create missing composite indexes on MySQL tables to accelerate filtering and eliminate table scans.
- **Current Problem**: Key queries filter by `client_id` on un-indexed columns in `client_contact_info`, `client_file`, and `jobs`.
- **Exact Files/Modules**:
  - `database/schema/mysql-schema.sql`
- **Current Flow**: MySQL performs full table scans for client contacts, files, and job status checks.
- **Proposed V2 Flow**: Recommended MySQL composite indexes:
  ```sql
  -- 1. Contacts table index
  ALTER TABLE `client_contact_info` ADD INDEX `idx_client_contact_client_id` (`client_id`);

  -- 2. Client files table index
  ALTER TABLE `client_file` ADD INDEX `idx_client_file_client_id` (`client_id`);

  -- 3. Jobs client & status composite index
  ALTER TABLE `jobs` ADD INDEX `idx_jobs_client_status` (`client_id`, `status`);

  -- 4. Client location composite index
  ALTER TABLE `client_location` ADD INDEX `idx_client_loc_comp_client` (`company_id`, `client_id`);
  ```
- **Expected Code Changes**: Create migration adding the above composite indexes.
- **DB Changes**: Add 4 composite indexes.
- **Index Changes**: As specified above.
- **Redis Changes**: None.
- **ES Changes**: None.
- **Queue Changes**: None.
- **Frontend Changes**: None.
- **Performance Benefit**: MySQL query execution times drop from > 150ms to **< 2ms** (Index Seek).
- **Risk**: Low (Online index creation in MySQL 8 / MariaDB).
- **Dependencies**: None.
- **Testing**: Run `EXPLAIN` on all SQL queries related to Client Details and verify `type: ref` or `type: range` with zero `Using filesort` or `Using temporary`.
- **Rollback**: Drop created indexes via migration rollback.
- **Acceptance Criteria**: All Client Details SQL queries utilize indexes (`EXPLAIN` key column non-null).

---

## PHASE 12 — Elasticsearch Optimization

- **Objective**: Optimize Elasticsearch query structures, source field filtering, and aggregation caching.
- **Current Problem**: ES queries request full `_source` documents, use broad un-indexed terms, and perform redundant aggregations.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`
- **Current Flow**: ES searches return full documents with `request_cache: true` on dynamic queries.
- **Proposed V2 Flow**:
  1. Specify explicit `_source` fields on all search queries.
  2. Use filter context (`filter` instead of `must`) for non-scored term filters (`client_id`, `company_id`, `status`) to enable ES bitset caching.
  3. Set `track_total_hits: 10000` only when exact count is required; use default for paginated queries.
- **Expected Code Changes**: Update ES query builder bodies across all 3 controllers.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: None.
- **ES Changes**: Utilize ES filter clause caching for tenant filters.
- **Queue Changes**: None.
- **Frontend Changes**: None.
- **Performance Benefit**: ES search response time drops from ~45ms to **< 8ms**. ES CPU utilization drops by 60%.
- **Risk**: Low.
- **Dependencies**: Phases 1-8.
- **Testing**: Inspect ES query log and benchmark search latencies under concurrent query load.
- **Rollback**: Revert ES query body parameters.
- **Acceptance Criteria**: ES p95 query latency < 15ms under load.

---

## PHASE 13 — Redis / Cache Optimization

- **Objective**: Establish robust multi-tenant Redis caching strategy with atomic locks to prevent cache stampedes.
- **Current Problem**: Cache keys rely on user ID and MD5 hashes without atomic lock protection on cache misses.
- **Exact Files/Modules**:
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`
- **Current Flow**: `if (cache()->has($cacheKeySet)) return cache()->get($cacheKeySet);` -> Multiple concurrent requests on miss all re-execute database queries.
- **Proposed V2 Flow**: Use atomic lock pattern:
  ```php
  $cacheKey = "ats:client_shell:{$companyId}:{$clientId}";
  return Cache::remember($cacheKey, now()->addMinutes(15), function() use ($clientId) {
      return $this->buildClientShellData($clientId);
  });
  ```
  For high-traffic hot clients, use atomic locking (`Cache::lock()`) during re-computation.
- **Expected Code Changes**: Refactor cache lookups to use `Cache::remember()` with tenant-scoped key format `ats:client_shell:{company_id}:{client_id}`.
- **DB Changes**: None.
- **Index Changes**: None.
- **Redis Changes**: Standardize key schemas and TTLs:
  - `ats:client_shell:{company_id}:{client_id}` (15 min)
  - `ats:client_kpi:{company_id}:{client_id}` (15 min)
  - `ats:client_dashboard:{company_id}:{client_id}` (30 min)
- **ES Changes**: None.
- **Queue Changes**: Trigger cache invalidation event `ClientUpdatedEvent` on client edit to clear client cache keys instantly.
- **Frontend Changes**: None.
- **Performance Benefit**: 100% cache stampede protection; 99% cache hit ratio on hot client views.
- **Risk**: Low.
- **Dependencies**: Phases 1-2.
- **Testing**: Simulate 100 concurrent requests on cache invalidation and verify database query executes exactly ONCE.
- **Rollback**: Revert to previous cache helper.
- **Acceptance Criteria**: 0 duplicate DB recomputations during cache expiry window under concurrent load.

---

## PHASE 14 — 10K+ Concurrency Architecture

- **Objective**: Validate system architecture stability at 10,000+ active concurrent users across all load scenarios.
- **Current Problem**: Architecture degrades exponentially under concurrent traffic due to resource fan-out.
- **Exact Files/Modules**: Complete Client Details stack.
- **Current Flow**: Direct DB/ES hit per user action -> Resource exhaustion.
- **Proposed V2 Flow**: Multi-layered defensive architecture:
  1. **Layer 1 (Browser)**: Client-side tab result caching + request debouncing + single-instance DOM tables.
  2. **Layer 2 (Edge / Web Server)**: Static shell response + HTTP ETag header validation.
  3. **Layer 3 (Redis Cache)**: Pre-computed header KPIs + cached tab fragments + atomic locks.
  4. **Layer 4 (Elasticsearch & MySQL)**: Indexed seek queries + filter bitset caching + zero N+1 queries.
- **Expected Code Changes**: Ensure HTTP ETag and Conditional Headers (`If-None-Match`) are supported on client shell responses.
- **DB Changes**: None.
- **Index Changes**: Composite indexes active.
- **Redis Changes**: Redis cluster / DragonflyDB cache tier active.
- **ES Changes**: Filter bitset cache active.
- **Queue Changes**: Asynchronous logging and event processing.
- **Frontend Changes**: HTTP 304 Not Modified handling on drawer shell requests.
- **Performance Benefit**: System easily handles 10,000 concurrent active users with < 15% MySQL CPU and < 20% ES CPU.
- **Risk**: Low.
- **Dependencies**: Phases 1-13.
- **Testing**: Execute 10K user concurrency simulation (Phase 15).
- **Rollback**: N/A.
- **Acceptance Criteria**: 10,000 active concurrent users supported with 0 HTTP 5xx errors and p95 latency < 120ms.

---

## PHASE 15 — Load / Stress / Soak Testing

- **Objective**: Conduct rigorous automated load testing using k6 / Locust simulating 10,000 active recruiters.
- **Current Problem**: No automated load test suite exists for Client Details screen.
- **Exact Files/Modules**: Test scripts directory `tests/Load/client_details_10k_soak.js`.
- **Current Flow**: Untested under 10K concurrent load.
- **Proposed V2 Flow**: Run 10 automated test scenarios:
  1. **Scenario 1**: 10,000 recruiters open Client Details simultaneously.
  2. **Scenario 2**: 10,000 recruiters open Candidate tab simultaneously.
  3. **Scenario 3**: 10,000 recruiters open Jobs tab simultaneously.
  4. **Scenario 4**: 10,000 recruiters open Interview Tracker simultaneously.
  5. **Scenario 5**: 10,000 recruiters open Joinings tab simultaneously.
  6. **Scenario 6**: 10,000 recruiters open Client Dashboard simultaneously.
  7. **Scenario 7**: 10,000 recruiters search candidates/jobs simultaneously.
  8. **Scenario 8**: 10,000 recruiters rapidly switch tabs back and forth.
  9. **Scenario 9**: 10,000 recruiters open distinct client IDs.
  10. **Scenario 10 (Hotspot)**: 10,000 recruiters open the SAME high-volume client ID simultaneously.
- **Performance Thresholds**:
  - `p50` < 60 ms
  - `p95` < 120 ms
  - `p99` < 250 ms
  - Error rate < 0.01%
- **Acceptance Criteria**: All 10 scenarios pass without breaking error or latency thresholds during 2-hour soak test.

---

## PHASE 16 — Production Rollout

- **Objective**: Safe, zero-downtime production deployment of Client Details V2 architecture.
- **Current Problem**: Deployment risk if database indexes or cache keys are missing.
- **Exact Files/Modules**: Deployment scripts & Feature Flags.
- **Proposed Flow**:
  1. Run database index migration (`php artisan migrate --force`) during low-traffic window.
  2. Deploy V2 code behind feature flag `FEATURE_V2_CLIENT_DETAILS = true`.
  3. Canary rollout: Enable for 5% of companies -> 25% -> 50% -> 100%.
  4. Monitor error rates, DB CPU, ES CPU, and Redis latency.
- **Acceptance Criteria**: 100% rollout completed with zero downtime and positive performance telemetry.

---

## PHASE 17 — Monitoring & Rollback

- **Objective**: Continuous production monitoring and immediate 1-click rollback strategy.
- **Current Problem**: Unmonitored latency spikes could affect recruiter productivity.
- **Monitoring Metrics**:
  - Alert if `clients.viewmore` p95 latency > 300ms for 5 consecutive minutes.
  - Alert if MySQL CPU > 70% or ES CPU > 80%.
  - Alert if Redis cache hit ratio < 90%.
- **Rollback Procedure**:
  - Set `FEATURE_V2_CLIENT_DETAILS = false` in config to instantly revert to legacy code path without deploying code or reverting database indexes.
- **Acceptance Criteria**: Rollback completes in < 5 seconds via feature flag toggle.

---
*V2 Implementation Plan Created. Awaiting user approval to proceed with execution.*
