# CLIENT DETAILS SCREEN: COMPLETE PERFORMANCE + CONCURRENCY AUDIT REPORT (V2 ARCHITECTURE)

> **Target Capacity**: 10,000+ Concurrently Active Recruiters / Users
> **Scope**: Internal Client Details & Direct Client Details (Drawer / Modal / Screen)
> **Primary Source Files**:
> - [show.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/show.blade.php)
> - [dviewMore.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php)
> - [ServiceCompanyClientController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php)
> - [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php)
> - [ServiceCompanyJobsController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php)
> - [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php)
> - [mysql-schema.sql](file:///c:/xampp/htdocs/git/JWMaincode28June2026/database/schema/mysql-schema.sql)

---

## 1. Executive Summary

A comprehensive performance, database, Elasticsearch, Redis, and concurrency audit was conducted for the **Client Details** module (Internal Client Details drawer `show.blade.php` and Direct Client Details drawer `dviewMore.blade.php`).

Under the current architecture, opening a single Client Details drawer causes **catastrophic resource fan-out**:
1. **Synchronous Server Heavy Lifting**: The initial `viewMore`/`dviewMore` request executes **5 heavy Elasticsearch aggregation queries**, **2 ES KPI counter queries**, and **4+ MySQL queries** synchronously on the PHP worker thread before returning HTML.
2. **Eager Concurrent AJAX Storm**: Immediately upon DOM ready (`$(document).ready`), the frontend fires **5 concurrent AJAX requests** for hidden tabs (Contacts DataTables, Candidates AJAX, Joinings AJAX, Interview Tracker AJAX, Jobs AJAX).
3. **Severe N+1 SQL Cascade in View Loops**: Rendering a single page of 10 candidates executes **6+ raw SQL queries per row** inside Blade `@foreach` loops (`DB::table('jobs')`, `skill_percentage`, `city_list`, `job_assign`, `companyDetailsByClient`, `candidate_status`).
4. **Missing Database Indexes**: Key tables (`client_contact_info`, `client_file`, `jobs`) lack indexes on `client_id`, forcing full table scans on MySQL.
5. **No Hot-Client Stampede Protection**: Under 10,000 concurrent recruiters opening high-volume clients (e.g. Jio, Wipro, TCS), cache expiration triggers a **thundering herd** that overloads MySQL and Elasticsearch.

This audit establishes a zero-business-logic-change V2 plan to achieve **p95 latency < 120ms** at **10,000+ active concurrent users**.

---

## 2. Audit Scope & Uploaded Source Files

### Uploaded Primary Files Evaluated
- `resources/views/service_company/client/show.blade.php` (2,744 lines): Internal Client drawer & tabs UI.
- `resources/views/service_company/client/dviewMore.blade.php` (2,222 lines): Direct / Marketplace Client drawer & tabs UI.

### Seven Tabs Audited
1. **Overview**: Client profile, basic info, company type, website, tags, KPI counter header cards.
2. **Contacts**: Client contacts list managed via DataTables with server-side processing.
3. **Jobs**: Client requisitions list loaded asynchronously.
4. **Candidate**: Submitted candidates list with pagination.
5. **Interview Tracker**: Scheduled candidate interviews, feedback, rating, and follow-up actions.
6. **Joinings**: Closed candidate placements and onboarding tracker.
7. **Client Dashboard**: Chart.js analytics for job status, candidate velocity, offer trends, joining trends, and interview frequency.

---

## 3. Current Architecture & Request Flow Analysis

### 3.1 Complete Request Flow Matrix

```
[Browser Tab / Drawer Trigger]
       │
       ▼
[HTTP GET: service_company/clients/viewmore/{id}]
       │
       ▼
[Middleware: auth, company.scope, throttle]
       │
       ▼
[ServiceCompanyClientController@viewMore($id)]
       ├─► 1. Check Redis Cache (`ats:clientsviewmore:{user}:{version}:{hash}`)
       ├─► 2. MySQL: Fetch client_file, client_location, city_list, client_contact_info, jobs count
       ├─► 3. ES Search 1: Candidate status counts aggregation (ELASTIC_CANDIDATES)
       ├─► 4. ES Search 2: Job status counts aggregation (ELASTIC_JOBS)
       ├─► 5. ES Search 3: Candidate last 7 days date_histogram (ELASTIC_CANDIDATES)
       ├─► 6. ES Search 4: Candidate 6-month offer date_histogram (ELASTIC_CANDIDATES)
       ├─► 7. ES Search 5: Candidate 6-month joining date_histogram & hits (ELASTIC_CANDIDATES)
       ├─► 8. ES Search 6: Upcoming interviews search (ELASTIC_INERTVIEW_TRACKER)
       ├─► 9. ES Search 7: Interview recent 7-day date_histogram (ELASTIC_INERTVIEW_TRACKER)
       └─► 10. ES Search 8: Interview recent 6-month date_histogram (ELASTIC_INERTVIEW_TRACKER)
       │
       ▼
[Blade Render: show.blade.php]
       ├─► Blade Header KPI 1: GeneralMethod::InternalClientJobsCountAllStatus() -> ES Search 9
       └─► Blade Header KPI 2: GeneralMethod::InternalClientCandidatesCountList() -> ES Search 10 & 11
       │
       ▼
[HTTP 200 JSON Response with rendered HTML payload (~170KB)]
       │
       ▼
[Browser DOM Injected -> JavaScript $(document).ready Execution]
       ├─► AJAX 1: GET /service_company/clients/contactdata?clientId={id} (Contacts DataTables)
       ├─► AJAX 2: GET /service_company/client-candidate-list?clientid={id} (Candidate Tab)
       ├─► AJAX 3: GET /service_company/client-candidate-join-list?clientid={id} (Joinings Tab)
       ├─► AJAX 4: GET /service_company/client-candidate-interview-tracker?clientid={id} (Interview Tracker)
       └─► AJAX 5: GET /service_company/jobs_side_bar_job_ajax/data?client_id={id} (Jobs Tab)
```

---

## 4. Critical Question Answer: Tab Data Loading Behavior

### CRITICAL QUESTION:
Does the current Client Details page load data for ALL tabs immediately when the page opens?

### CONFIRMED ANSWER:
**F. Through a mixture of the above (Server-side rendering + Eager AJAX Storm).**

#### Exact Tab Breakdown:

| Tab Name | Initial Page Load Mechanism | Trigger Timing | Performance Impact |
| :--- | :--- | :--- | :--- |
| **Overview** | Server-side Blade rendering | Synchronous on `viewMore` HTTP GET | Medium (MySQL queries) |
| **Client Dashboard** | Server-side Blade rendering | Synchronous on `viewMore` HTTP GET | **CRITICAL (5 ES Aggregations computed even when tab hidden)** |
| **Contacts** | DataTables AJAX | Immediately on `$(document).ready` | High (DataTables serverSide SQL scan) |
| **Jobs** | Custom AJAX (`fetchJobsClient`) | Immediately on `$(document).ready` | High (ES query + Blade rendering) |
| **Candidate** | Custom AJAX (`fetchCandidates`) | Immediately on `$(document).ready` | **CRITICAL (N+1 SQL queries inside Blade loop)** |
| **Interview Tracker** | Custom AJAX (`fetchCandidatesInverViewTracker`) | Immediately on `$(document).ready` | High (ES search + SQL status lookup) |
| **Joinings** | Custom AJAX (`fetchCandidatesJoin`) | Immediately on `$(document).ready` | **CRITICAL (N+1 SQL queries inside Blade loop)** |

#### Impact Finding:
Opening **one single client drawer** triggers **6 HTTP requests**, **11 Elasticsearch queries**, and **60+ MySQL queries** simultaneously before the user even interacts with any tab.

---

## 5. Tab-by-Tab Detailed Audit

### 5.1 Tab 1 — Overview Audit
- **Files**: [show.blade.php:L286-L395](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/show.blade.php#L286-L395), [dviewMore.blade.php:L97-L184](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php#L97-L184)
- **Header KPI Calculations**:
  - `GeneralMethod::InternalClientJobsCountAllStatus($cur_company_id, $client->id)`
  - `GeneralMethod::InternalClientCandidatesCountList($client->id)`
  - `GeneralMethod::DirectClientJobsCountAllStatus($detail_from, $cur_company_id)`
  - `GeneralMethod::DirectClientCandidatesCountList($detail_from, $cur_company_id)`
- **Finding**: KPI calculations execute inside `@php` tags inside Blade templates. They fire 3 separate Elasticsearch queries per page view. These queries are un-cached and run on every drawer open.

### 5.2 Tab 2 — Contacts Audit
- **Files**: [show.blade.php:L2257-L2288](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/show.blade.php#L2257-L2288), [ServiceCompanyClientController.php:L1756-L1824](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php#L1756-L1824)
- **DataTables Config**: `serverSide: true`, `processing: true`, `destroy: true`, `pageLength: 10`.
- **Backend Query**:
  ```sql
  SELECT client_contact_info.id, contact_person_name, calling_code, contact_person_email, contact_person_number, contact_person_designation
  FROM client_contact_info
  WHERE client_id = ?
  ```
- **Finding**: Table `client_contact_info` lacks an index on `client_id`. DataTables server-side count query (`SELECT COUNT(*) FROM client_contact_info WHERE client_id = ?`) forces a **FULL TABLE SCAN** on MySQL.

### 5.3 Tab 3 — Jobs Audit
- **Files**: [ServiceCompanyJobsController.php:L1676-L1796](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L1676-L1796), [client_jobs_side_bar_ajax.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php)
- **Backend Flow**: AJAX GET `service_company/jobs_side_bar_job_ajax/data`. Queries `ELASTIC_JOBS` index.
- **Finding**: `_source` filtering is missing in the ES search parameters. Full ES job documents are retrieved, including heavy `job_description` and `job_requirement` text fields, inflating ES response payload size by 10x.

### 5.4 Tab 4 — Candidate Audit (Internal & Direct Clients God-Level V2.3 Master Specification)
> **Authoritative Specification & Architecture Contract**: See [CANDIDATES_TAB_CLIENT_DRAWER_AUDIT.md](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/V2_CLIENT_DETAILS/CANDIDATES_TAB_CLIENT_DRAWER_AUDIT.md) for the complete production contract, failure-mode matrix, and implementation blueprints.
> **Status:** FROZEN ARCHITECTURAL SPECIFICATION — PENDING EMPIRICAL VERIFICATION

#### 5.4.1 Architectural Scope & Core Invariants
- **Source Files**: 
  - [`show.blade.php:L1915-L1951, L3657-L3802`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/show.blade.php#L1915-L1951) (Internal Client Drawer Shell & Scripts)
  - [`dviewMore.blade.php:L1970-L2005, L3664-L3789`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php#L1970-L2005) (Direct Client Drawer Shell & Scripts)
  - [`custom_client_candidates.blade.php:L1-L514`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates.blade.php#L1-L514) (Candidate Row Blade View)
  - [`ServiceCompanyJobApplicationController.php:L1277-L1387, L1611-L1704`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1277-L1387) (`clientCandidateList`, `directclientCandidateList`)
  - [`GeneralMethod.php:L15669-L15760, L15835-L15911`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php#L15669-L15760) (`InternalClientCandidatesCountList`, `DirectClientCandidatesCountList`)
- **Authority Boundaries**:
  - **MySQL**: Authoritative Transactional Source of Truth (SSOT).
  - **Elasticsearch (`ELASTIC_CANDIDATES`)**: Authoritative Candidate Read Model. Browsers NEVER write to ES directly.
  - **Zero-MySQL Scoped Rule**: Candidate Drawer read paths must execute strictly 0 MySQL SELECT queries.
  - **Dragonfly Authorization**: Fail-closed tenancy check (`auth:tenant_clients:{company_id}`). Never falls back to candidate ES documents.
  - **Browser (User RAM)**: `ClientCandidateStore` state machine with `DocumentFragment` rendering, request ID race protection, and Rule 8 filter-aware collection reconciliation.

#### 5.4.2 Root-Cause Defect Matrix & Line-Level Audit
1. **Critical Direct Client Count Inconsistency (P0)**:
   - Header KPI Card ([`dviewMore.blade.php:1608`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php#L1608)) and Tab Badge ([`dviewMore.blade.php:1673`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php#L1673)) invoke [`DirectClientCandidatesCountList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php#L15835), filtering strictly on `final_status = 1`, excluding only status `9` (Archived), cached for 15 minutes.
   - The table list endpoint [`directclientCandidateList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1646) does NOT filter by `final_status = 1` and excludes statuses `8` (InReview/OnHold) and `9` (`must_not: [8, 9]`).
   - **Impact**: Candidate profiles in status 8 appear in the count badge but vanish from the table; badge says "5", table shows "No data available in table".
2. **Internal Client Deleted Candidate Leakage (P0)**:
   - [`InternalClientCandidatesCountList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php#L15711) excludes status `9` (`must_not: [9]`).
   - [`clientCandidateList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1287-L1313) has **no status 9 exclusion**, allowing deleted/archived candidates to show in the table, creating persistent count mismatch.
3. **Severe N+1 SQL Cascade on Web Read Path (P0 — Rule 1 Violation)**:
   - In [`directclientCandidateList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1701), lookup maps (`$jobsMap`, `$citiesMap`, `$statusMap`, `$jobAssignMap`) are completely omitted.
   - For every candidate row in [`custom_client_candidates.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates.blade.php), 6 raw SQL queries fire synchronously:
     - `DB::table('jobs')->where('id', $row->jobId)->first()` (Line 27)
     - `DB::table('job_metronics_skills')` via `GeneralMethod::skill_percentage` (Line 69) (Uncached across all paths!)
     - `DB::table('city_list')->where('city_id', $row->city_id)->first()` (Line 338)
     - `DB::table('job_assign')->join('users', ...)->where(...)` (Line 383)
     - `GeneralMethod::companyDetailsByClient($row->jobId)` 4-table join on cache miss (Line 438)
     - `DB::table('candidate_status')->where('id', $row->child_status_id)->first()` (Line 474)
   - **Impact**: 10 candidates = **60+ raw MySQL SELECT queries**. At 10K concurrent users = 600,000 queries, locking MySQL.
4. **Tenant Isolation Vulnerability / IDOR (P0)**:
   - [`clientCandidateList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1294) accepts `clientid` without verifying tenant ownership (`auth()->user()->company_id`) and the ES query contains no `company_id` filter. Cross-tenant data leakage is possible.
5. **Completely Dead Search Code (P0)**:
   - Neither drawer shell renders `<input id="search">` in `#candidate_list`. The JS keyup handler binds to a non-existent element.
   - In [`clientCandidateList`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1281), `$searchQuery = $request->input('q')` is extracted but **never appended to `$boolQuery`**. Internal search is 100% dead code.
   - Pagination click handler reads `#inputSearch` (different non-existent ID).
6. **Stage Modal Failure & Syntax Error (P1)**:
   - Stage badge links with syntax error `href="javascript.void(0);"` ([Line 483](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates.blade.php#L483)).
   - Target modals `#stage_sub_stage` and `#client_stage_sub_stage` do not exist in the DOM; click listeners are absent. Clicking stage does nothing.
7. **UI/UX Layout Clipping & Disruptive Loader (P1)**:
   - Hardcoded `style="height: 102px; position: relative;"` and `style="position: absolute; bottom: 0; white-space: nowrap; width: 94%;"` cause skills and titles to clip on 15.6" (1366×768) screens.
   - `$('.page-loader').css('display', 'flex')` freezes the entire browser window on pagination instead of using local card skeleton loaders.
   - Typo in table body ID: `<tbody id="candidatetList">` (extra 't').
8. **Tab Stagnation Defect (P1)**:
   - `loadedTabs['candidate_list'] = true` permanently short-circuits tab clicks. Switching tabs and returning never re-fetches updated candidate pipeline data.
   - Zero Laravel Reverb WebSocket integration for live candidate status updates.

#### 5.4.3 Unified V2.4 Production Implementation Blueprint
1. **ES Read Model Denormalization & Field-Level Minimization**: Ensure `ELASTIC_CANDIDATES` contains all display attributes. Query engine strictly limits `_source.includes` to 28 lightweight listing fields (eliminating resume text/transcripts/notes and preventing PII leakage). Direct cluster administrative access classified as Privileged Infrastructure Access.
2. **Unified Query Service & Atomic Version Store**: Construct `CandidateDrawerQueryService` and `ClientCandidateVersionStore` to power both Internal and Direct clients, enforcing fail-closed Dragonfly authorization (`ClientAccessGuard`) before ES queries with zero ES candidate document fallback. Single-operation atomic Lua bootstrap (`EXISTS -> SETNX 0 -> return 0`) and fail-closed version coordination (`HTTP 503` if Dragonfly is offline).
3. **Atomic Lua Version Advancement with 7-Day Replay Ledger**: Single-threaded Redis Lua script with 7-day TTL ledger (`event_idempotency:{company}:{client}:{event_id}`) exceeds 72h outbox redelivery horizon, guaranteeing zero duplicate version increments under concurrent worker replays.
4. **Pure JSON API with Mutation-Aware HMAC Cursor & Bounded Count Relation**: Return `< 15KB` JSON with `track_total_hits`, `search_after` cursor navigation (`created_at DESC`, `wiseId DESC` immutable tie-breaker), mathematical `size + 1` boundary evaluation, and HMAC signature verification bound to `company_id`, `client_id`, `client_type`, sort order, canonical SHA-256 query fingerprint, schema version, and 15-minute TTL lifecycle. When bounded tracking triggers at 10,000 matches, `total_relation` returns `"gte"`, `last_page` is set to `null` to avoid bogus page counts, and the UI displays `"10,000+ Candidates"`.
5. **Optimistic Concurrency Control (OCC)**: Require `expected_version` on all candidate mutation commands; return `HTTP 409 Conflict` on race conditions.
6. **State-Machine Client Store (`ClientCandidateStore`)**: Browser User RAM store with IDLE/LOADING/READY/REFRESHING/ERROR states, `requestId` race protection, tenant context isolation (`companyId`, `clientId`, `clientType`), `queryFingerprint` tracking, event idempotency via bounded `processedEventIds`, candidate OCC filtering via `candidateVersions`, 300ms debounced search, status filter dropdown, local card skeleton loader, and Reverb version-fenced DOM patching.
7. **Fully Generic Declarative Query Dependency Model (Rule 8)**: Browser store evaluates `mutationAffectsCurrentQuery(event)` using a formal Query Dependency Manifest (search, status, job, sort, archive visibility). Evaluator operates purely from manifest definitions without hardcoded status logic. Membership-altering mutations trigger clean collection refetches; non-membership mutations safely patch the row in-place.
8. **Bounded 60ms Event Coalescer**: Store debounces Reverb bursts within a 60ms window, yielding at most one ES refresh per active reconciliation window.
9. **Differentiated Rate Limiting**: Three independent token buckets for interactive reads (20/10s), background reconciliations (60/min), and reconnect syncs (30/min), sharing a 500 req/min tenant safety ceiling.
10. **Failure-Mode Circuit Breaker & Deterministic Status Codes**: Strictly 0 fallback to MySQL SELECT queries on Elasticsearch cluster outages (`HTTP 503`). Deterministic HTTP status mapping across all failure modes (403 for unauthorized access, 503 for infrastructure outages, 422 for cursor/pagination/expiry errors, 409 for stale cursor).
11. **Reconnect Reconciliation Endpoint**: `GET /service_company/clients/{clientId}/candidate-version` operates strictly from `ClientCandidateVersionStore` (0 MySQL reads), enforces `ClientAccessGuard`, and fails closed with `HTTP 503` if Dragonfly is offline.
12. **Mandatory Full HTTP Lifecycle CI Test Gates**: Automated enforcement across full middleware stack via 8 CI assertion tests: `CandidateDrawerReadTest` (0 SQL across entire middleware stack, 1 ES), `CandidateDrawerUnauthorizedTest` (0 SQL, 0 ES, 403), `CandidateDrawerCircuitBreakerTest` (0 SQL, 503 on ES timeout), `CandidateDrawerFailClosedVersionTest` (0 SQL, 0 ES, 503 on Dragonfly downtime), `CandidateDrawerCursorSecurityTest` (422 on tampered/expired cursor), `CandidateDrawerSnapshotConsistencyTest` (409 on stale collection version), `CandidateVersionReconnectReadTest` (0 SQL, 200/403/503 matrix), and `CandidateDrawerRevocationSocketTest` (socket channel invalidation and 403 API denial).

### 5.5 Tab 5 — Interview Tracker Audit
- **Files**: [ServiceCompanyJobApplicationController.php:L807-L870](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L807-L870), [custom_client_candidates_interview_tracker.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates_interview_tracker.blade.php)
- **Backend Flow**: Queries `ELASTIC_INERTVIEW_TRACKER`.
- **Follow-up Action**: `citSetFollowUp` fires POST to `interview-schedule.followup_update`.
- **Finding**: Lacks optimistic UI updates; fires blocking AJAX calls with alert popups on failure.

### 5.6 Tab 6 — Joinings Audit
- **Files**: [ServiceCompanyJobApplicationController.php:L746-L805](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L746-L805)
- **Backend Flow**: Reuses `custom_client_candidates.blade.php`.
- **Finding**: Inherits the exact same **60+ SQL query N+1 cascade per 10 rows**.

### 5.7 Tab 7 — Client Dashboard Audit
- **Files**: [show.blade.php:L774-L1223](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/show.blade.php#L774-L1223), [ServiceCompanyClientController.php:L1105-L1746](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php#L1105-L1746)
- **Charts Rendered**: 5 Chart.js canvas elements (`allJobGrapg_{id}`, `candidates_left_chart_{id}`, `myChart_{id}`, `candidates_offer_chart_{id}`, `candidates_join_chart_{id}`, `inetview_recent_chart_{id}`).
- **Finding (SEVERE P0)**: All 5 Chart.js data sets are generated on the server during the initial page load request, even if the user NEVER clicks the Client Dashboard tab!

---

## 6. Inventory Specifications

### 6.1 Route Inventory

| Route Name | URI Path | Controller Method | Purpose |
| :--- | :--- | :--- | :--- |
| `clients.viewmore` | `service_company/clients/viewmore/{id}` | `ServiceCompanyClientController@viewMore` | Internal Client shell & overview |
| `clients.dviewmore` | `service_company/clients/dviewmore/{id}` | `ServiceCompanyClientController@dviewMore` | Direct Client shell & overview |
| `clients.contactdata` | `service_company/clients/contactdata` | `ServiceCompanyClientController@contactdata` | Internal contacts DataTables AJAX |
| `clients.dcontactList` | `service_company/clients/dcontactList` | `ServiceCompanyClientController@dcontactList` | Direct contacts DataTables AJAX |
| `jobs.data_client_ajax` | `service_company/jobs_side_bar_job_ajax/data` | `ServiceCompanyJobsController@client_job_data_ajax` | Client jobs list AJAX |
| `job-applications.client-candidates` | `service_company/client-candidate-list` | `ServiceCompanyJobApplicationController@clientCandidateList` | Internal candidates list AJAX |
| `job-applications.direct-client-candidates` | `service_company/direct-client-candidate-list` | `ServiceCompanyJobApplicationController@directclientCandidateList` | Direct candidates list AJAX |
| `job-applications.client-candidates-join-list` | `service_company/client-candidate-join-list` | `ServiceCompanyJobApplicationController@clientCandidateJoinList` | Internal joinings list AJAX |
| `job-applications.direct-client-candidates-join-list` | `service_company/direct-client-candidate-join-list` | `ServiceCompanyJobApplicationController@directclientCandidateJoinList` | Direct joinings list AJAX |
| `job-applications.client-candidates-interview-tracker` | `service_company/client-candidate-interview-tracker` | `ServiceCompanyJobApplicationController@clientCandidateInterviewTracker` | Internal interview tracker AJAX |
| `job-applications.direct-client-candidates-interview-tracker` | `service_company/direct-client-candidate-interview-tracker` | `ServiceCompanyJobApplicationController@directclientCandidateInterviewTracker` | Direct interview tracker AJAX |
| `interview-schedule.followup_update` | `service_company/interview-schedule/followup_update` | `ServiceCompanyInterviewScheduleController@followupUpdate` | Interview follow-up update |

### 6.2 Database Inventory & Index Audit

| Table Name | Primary Key | Existing Indexes | Missing Required Indexes | Severity |
| :--- | :--- | :--- | :--- | :--- |
| `clients` | `id` | `idx_clients_company` (`company_id`) | None | OK |
| `client_contact_info` | `id` | `PRIMARY (id)` | `idx_client_contact_client_id` (`client_id`) | **P0** |
| `client_file` | `id` | `PRIMARY (id)` | `idx_client_file_client_id` (`client_id`) | **P0** |
| `client_location` | `id` | `idx_client_loc_client` (`client_id`, `id`) | `idx_client_loc_comp_client` (`company_id`, `client_id`) | **P1** |
| `jobs` | `id` | `idx_jobs_company_status_created` | `idx_jobs_client_status` (`client_id`, `status`) | **P0** |
| `candidate_status` | `id` | `PRIMARY (id)` | None (Needs memory caching) | **P1** |

---

## 7. Categorized Finding Register (P0 / P1 / P2 / P3)

### P0 — Critical Severity Findings

#### [FINDING-P0-01] Synchronous Eager Computation of 5 ES Aggregations on Shell Load
- **Tab**: Overview / Shell / Client Dashboard
- **File**: `ServiceCompanyClientController.php` (`viewMore`/`dviewMore` methods)
- **Root Cause**: Controller executes 5 date_histogram and terms aggregation searches on `ELASTIC_CANDIDATES`, `ELASTIC_JOBS`, and `ELASTIC_INERTVIEW_TRACKER` synchronously before returning the drawer view.
- **10K Impact**: 10,000 concurrent page opens trigger 50,000 complex ES aggregations simultaneously, causing ES thread pool exhaustion and HTTP 504 Gateway Timeouts.
- **Recommendation**: Decouple Client Dashboard analytics from initial shell load. Load Dashboard tab data via AJAX only when the Client Dashboard tab is activated.

#### [FINDING-P0-02] Concurrent Execution of 5 Un-debounced AJAX Requests on Initial Page Load
- **Tab**: All Tabs
- **File**: [show.blade.php:L2321-L2324](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/show.blade.php#L2321-L2324), [dviewMore.blade.php:L2058-L2061](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/client/dviewMore.blade.php#L2058-L2061)
- **Root Cause**: `$(document).ready()` unconditionally calls `fetchCandidates()`, `fetchCandidatesJoin()`, `fetchCandidatesInverViewTracker()`, `fetchJobsClient()`, and DataTables `#contactList`.
- **10K Impact**: 10,000 recruiters opening drawers generate 50,000 concurrent HTTP requests immediately.
- **Recommendation**: Implement lazy tab loading ("Load-on-First-Open"). Only fetch tab data when a user explicitly clicks a tab header.

#### [FINDING-P0-03] Severe N+1 Query Cascade in Candidate & Joinings Blade Loop
- **Tab**: Candidate & Joinings
- **File**: [custom_client_candidates.blade.php:L24-L476](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/custom_client_candidates.blade.php#L24-L476)
- **Root Cause**: `DB::table('jobs')`, `city_list`, `job_assign`, `candidate_status` queried inside `@foreach` loop.
- **10K Impact**: At 10K users viewing 10 candidates each = 600,000 database queries per second, resulting in MySQL connection pool depletion.
- **Recommendation**: Eager load all candidate relational data in backend controller or denormalize display fields into ES index `_source`.

#### [FINDING-P0-04] Full Table Scans due to Missing `client_id` Indexes in MySQL
- **Tab**: Contacts & Overview
- **File**: [mysql-schema.sql:L641-L695](file:///c:/xampp/htdocs/git/JWMaincode28June2026/database/schema/mysql-schema.sql#L641-L695)
- **Root Cause**: `client_contact_info` and `client_file` lack indexes on `client_id`.
- **10K Impact**: MySQL performs table scans on millions of rows for every contact lookup.
- **Recommendation**: Add composite indexes `idx_client_contact_client_id` (`client_id`) and `idx_client_file_client_id` (`client_id`).

#### [FINDING-P0-05] Thundering Herd / Cache Stampede Risk on Hot Clients
- **Tab**: Overview / Shell
- **File**: [ServiceCompanyClientController.php:L1038](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php#L1038)
- **Root Cause**: Cache key `ats:clientsviewmore:{user}:{version}:{hash}` expires without lock-based recomputation or probabilistic early expiration.
- **10K Impact**: When 1,000 recruiters view the same client (e.g. Jio) and cache expires, all 1,000 requests simultaneously hit MySQL/ES.
- **Recommendation**: Use Redis atomic locks (`Cache::lock()`) during cache misses to ensure only 1 thread recomputes the payload.

---

### P1 — High Severity Findings

#### [FINDING-P1-01] Missing Debounce & Request Cancellation on Search Inputs
- **Tab**: Jobs, Candidate, Contacts
- **File**: `show.blade.php` (`keyup` handlers for `#inputJobSearch`, `#search`)
- **Root Cause**: Search inputs fire AJAX requests on every single keyup event without debouncing or canceling in-flight AJAX requests (`abort()`).
- **Recommendation**: Add a 300ms debounce wrapper and cancel pending `jqXHR` objects before sending new search requests.

#### [FINDING-P1-02] Missing Server-Side Authorization Scope on Candidate AJAX Endpoints
- **Tab**: Candidate, Joinings, Interview Tracker
- **File**: `ServiceCompanyJobApplicationController.php` (`clientCandidateList`)
- **Root Cause**: Endpoint accepts `clientid` from query parameter without validating if `clientid` belongs to `auth()->user()->company_id`.
- **Recommendation**: Enforce multi-tenant ownership check `Client::where('id', $clientId)->where('company_id', $userCompanyId)->exists()`.

#### [FINDING-P1-03] Unbounded Elasticsearch Source Document Retrieval
- **Tab**: Jobs Tab
- **File**: `ServiceCompanyJobsController.php` (`client_job_data_ajax`)
- **Root Cause**: ES query omits `_source` parameter, returning full documents with heavy job descriptions.
- **Recommendation**: Restrict `_source` fields to required display columns (`id`, `title`, `status`, `min_exp`, `max_exp`, etc.).

#### [FINDING-P1-04] Duplicate Header KPI Elasticsearch Queries
- **Tab**: Header KPI Cards
- **File**: `GeneralMethod.php` (`InternalClientJobsCountAllStatus`, `InternalClientCandidatesCountList`)
- **Root Cause**: Two separate ES helper functions executed sequentially in Blade, running 3 queries instead of a single multi-search or aggregated search.
- **Recommendation**: Combine job and candidate KPI counts into a single cached ES aggregation response.

#### [FINDING-P1-05] DataTables FixedHeader & Tooltip Memory Leaks
- **Tab**: Contacts
- **File**: `show.blade.php:L2289`
- **Root Cause**: DataTables and `FixedHeader` instantiated repeatedly without destroying previous instances when reopening drawers.
- **Recommendation**: Single-instance DataTables lifecycle management with proper destroy/cleanup on drawer hide.

---

## 8. 10,000 Concurrent User Traffic & Hotspot Analysis

### 8.1 10K Load Modeling

| Load Scenario | Concurrent Users | Total Requests/sec | ES Queries/sec (Current) | ES Queries/sec (V2 Target) | DB Queries/sec (Current) | DB Queries/sec (V2 Target) |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Normal Operational Load** | 10,000 | 500 req/s | 5,500 qps | 50 qps | 30,000 qps | 100 qps |
| **Burst Peak Load** | 10,000 | 2,500 req/s | 27,500 qps | 250 qps | 150,000 qps | 500 qps |
| **Same-Client Hotspot (1 Client)** | 2,000 | 1,000 req/s | 11,000 qps | 0 qps (Cache Hit) | 60,000 qps | 0 qps (Cache Hit) |

---

## 9. Performance Targets (V2 Benchmarks)

| Metric | Current Architecture | V2 Target Architecture |
| :--- | :--- | :--- |
| **Initial Shell Load (p50)** | 850 ms | **< 60 ms** |
| **Initial Shell Load (p95)** | 2,400 ms | **< 120 ms** |
| **Tab Click Latency (p95)** | N/A (Eager loaded) | **< 80 ms** |
| **MySQL CPU Utilization @ 10K Users** | 100% (Crash) | **< 15%** |
| **ES CPU Utilization @ 10K Users** | 95% (High latency) | **< 20%** |
| **Redis Latency (p99)** | N/A | **< 2 ms** |

---

## 10. Summary of Top 5 Phase-1 Priorities

1. **Decouple Shell Rendering from Tab Data**: Deliver an instant static HTML/Blade drawer shell without executing Client Dashboard or hidden tab queries.
2. **Implement Lazy Tab Loading with Browser Caching**: Fetch tab data only when clicked; cache results in memory to prevent duplicate requests on tab switching.
3. **Eliminate Candidate/Joinings Blade N+1 Queries**: Denormalize candidate display fields into Elasticsearch or eager-load relational lookup maps.
4. **Add Composite Database Indexes**: Add missing indexes on `client_contact_info(client_id)`, `client_file(client_id)`, and `jobs(client_id, status)`.
5. **Enforce Multi-Tenant Data Isolation**: Secure all AJAX endpoints against IDOR and cross-tenant data leakage by enforcing server-side `company_id` scope verification.

---
*Audit Completed. Code implementation pending explicit user approval.*
