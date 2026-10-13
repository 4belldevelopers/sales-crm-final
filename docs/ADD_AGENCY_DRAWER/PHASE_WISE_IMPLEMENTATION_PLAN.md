# Phase-Wise Implementation Plan: Add Agency Drawer Performance & Reliability

**Project:** `C:\xampp\htdocs\git\JWMaincode28June2026`  
**Scope:** Service Company → Jobs → Add Agency Drawer  
**Governing Standard:** CQRS, 0% UI writes during GET, Client-First execution, Strict business-rule preservation.

---

## Plan Overview & Phasing

```
[Phase 1: Browser & UI-State Optimization]
  Eliminate external FontAwesome JS & pravatar images
  Fix AgencyMarketplaceTabManager job-scoped lifecycle (allow reopening & job switching)
  Clean up event listeners (.off()) and eliminate memory leaks
  Remove dead #boostlisting DataTable script
          │
          ▼
[Phase 2: Blade & N+1 Query Optimization]
  Eliminate synchronous cURL (getDomainIconCompany) and direct DB/ES writes in Blade loop
  Batch location data in controller to eliminate 4-table join per row
  Batch recommendation_relation status to eliminate 2 queries per row
  Reuse controller batch maps in Blade (zero duplicate count queries)
          │
          ▼
[Phase 3: Controller, Cache & Mutation Optimization]
  Optimize recommendedData pre-cache checks (zero MySQL reads on cache hit)
  Implement missing assignRejected method in ServiceCompanyJobsController
  Add atomic cache version bumping on all agency mutations (send invitation, ignore, remove)
          │
          ▼
[Phase 4: Concurrency, Idempotency & Error Handling]
  Client-side button debounce & disabling during in-flight mutations
  Server-side double-submission safeguards on recommendation_relation
  Standardized JSON error handling with visible SweetAlert feedback
          │
          ▼
[Phase 5: Verification, Load Capacity Analysis & Rollout Runbook]
  Execute functional regression tests across all 3 tabs and actions
  Profile cold vs warm response times and memory consumption
  Document 1K, 5K, 10K concurrency capacity planning
```

---

## Detailed Phase Specification

### Phase 1 — Browser & UI-State Optimization

| Field | Details |
|---|---|
| **Files/Components** | `resources/views/service_company/jobs/spartnerlisting.blade.php`<br>`resources/views/service_company/jobs/index.blade.php` |
| **Exact Changes** | 1. Remove external `<script src="https://cdnjs.cloudflare.com/.../all.min.js">`. Use Metronic's native LineAwesome / SVG icons.<br>2. Replace external `https://i.pravatar.cc` avatars with local asset default avatars.<br>3. Refactor `AgencyMarketplaceTabManager` to store state per `jobId`: `AgencyMarketplaceTabManager.getJobState(jobId)`. Reset active requests and tab initialization on drawer open.<br>4. In `index.blade.php`, ensure that reopening the drawer for a job properly initializes the active Recommended tab instead of showing an empty body.<br>5. Ensure all event listeners use `.off().on()` or namespaced handlers (`.tagifyStar`, `.agencyPagi`, `.sendReq`, `.removeJob`, etc.).<br>6. Remove the dead `boostlisting()` DataTable initialization block. |
| **Dependencies** | None. Pure frontend / Blade template. |
| **Business Behavior** | 100% preserved. All 3 tabs (Recommended, My Agency, Assigned Agency) load identically, with identical layout, CSS, filters, and modals. |
| **Risks** | Low. Missing icons if FontAwesome classes were used that are not in Metronic; verified that drawer uses native `<svg>` and `ti ti-*` Tabler icons. |
| **Rollback** | Revert `spartnerlisting.blade.php` and `index.blade.php` via Git. |
| **Validation** | Open drawer, close drawer, open for another job. Verify all tabs load and render rows on both jobs without page refresh. |
| **Acceptance Criteria** | 0 external CDN scripts evaluated on drawer open; 0 main thread freezes; reopening drawer works consistently across any job. |

---

### Phase 2 — Blade & N+1 Query Optimization

| Field | Details |
|---|---|
| **Files/Components** | `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php`<br>`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` |
| **Exact Changes** | 1. In `find_agency_recom_ajax.blade.php:21`, remove `GeneralMethod::getDomainIconCompany($googleIcon, $row->id, true)`. Fallback cleanly to company initials avatar if logo is empty, with 0 HTTP cURL calls and 0 DB/ES writes.<br>2. In `ServiceCompanyJobsController@recommendedData`, add batch lookup for agency primary location (`client_location` + `countries` + `city_list`) for all 20 sliced agencies in a single query, storing in `_loc_info`.<br>3. In `ServiceCompanyJobsController@recommendedData`, batch query `recommendation_relation` for the current `$jobId` across the 20 agencies in a single query, storing in `_recom_rel_info`.<br>4. In `find_agency_recom_ajax.blade.php`, replace raw `DB::table(...)` calls with the pre-computed batch properties (`_loc_info`, `_recom_rel_info`, `_match_found`). |
| **Dependencies** | Phase 1. |
| **Business Behavior** | 100% preserved. Logos, initials avatars, locations, rates, active job counts, and "Send Requirement" / "Request Sent" / "Remove" button states match existing logic exactly. |
| **Risks** | Low. Location formatting must match current formatting (`City, CountryCode`). |
| **Rollback** | Revert `find_agency_recom_ajax.blade.php` and `ServiceCompanyJobsController.php` changes. |
| **Validation** | Run `scratch/audit_recommended_data.php`. Measure query count drop from 16–80 queries down to ≤ 5 queries. |
| **Acceptance Criteria** | 0 cURL calls during `recommendedData`; MySQL queries per uncached request reduced by ≥ 75%; 0 UI writes during GET. |

---

### Phase 3 — Controller, Cache & Mutation Optimization

| Field | Details |
|---|---|
| **Files/Components** | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` |
| **Exact Changes** | 1. In `recommendedData`, optimize job summary retrieval: check cache using `$jobId` and query parameters. Store job summary and skills in a 60-minute Redis cache to avoid hitting MySQL `jobs` and `job_metronics_skills` on cache hits and 304 checks.<br>2. Implement `assignRejected(Request $request)` in `ServiceCompanyJobsController` to match the route and frontend expectation (`recommendation_relation.final_status = -1`).<br>3. In `send_job_recommanded_request`, `send_only_job_invitation`, `resend_job_recommanded_request`, `removeJob`, and `assignRejected`, bump the user version keys (`allrecommendedagency_version_user_` and `allsendinvitationtoagency_v5_version_user_`) and delete the tenant agency cache tags in Redis. |
| **Dependencies** | Phase 2. |
| **Business Behavior** | 100% preserved. Agency assignment status, coin deductions, mail notifications, and status transitions remain completely untouched. |
| **Risks** | Low. Version bumping must ensure cache is invalidated atomically. |
| **Rollback** | Revert `ServiceCompanyJobsController.php`. |
| **Validation** | Send requirement for an agency, reopen drawer, verify button immediately displays "Request Sent". Click Ignore in Assigned Agency, verify no 500 error. |
| **Acceptance Criteria** | 0 MySQL reads when ETag 304 or Redis cache hit; zero stale data after agency invitation or status change; `assignRejected` returns 200 success. |

---

### Phase 4 — Concurrency, Idempotency & Error Handling

| Field | Details |
|---|---|
| **Files/Components** | `resources/views/service_company/jobs/spartnerlisting.blade.php`<br>`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` |
| **Exact Changes** | 1. Ensure all invitation and removal buttons (`.send_job_recommanded_request`, `.send_only_job_invitation`, `.resend_job_recommanded_request`, `.remove_job`, `.remove_assign_job`) disable immediately upon click and show a loading spinner state.<br>2. In `ServiceCompanyJobsController`, enforce database transaction and duplicate check (`where('job_id', $jobid)->where('to_company_id', $to_company_id)->lockForUpdate()`) to prevent duplicate recommendation rows on concurrent clicks.<br>3. Return structured JSON responses with descriptive error messages when validation fails. |
| **Dependencies** | Phase 3. |
| **Business Behavior** | 100% preserved. Preserves existing status responses (`closeJob`, `info`, `warning`, `success`). |
| **Risks** | None. Standard idempotency pattern. |
| **Rollback** | Revert `spartnerlisting.blade.php` and controller methods. |
| **Validation** | Rapidly double-click "Send Requirement". Verify exactly one database record is created and button updates gracefully. |
| **Acceptance Criteria** | 0 duplicate records created on concurrent clicks; button disabled during mutation; error alerts shown gracefully on failure. |

---

### Phase 5 — Verification, Load Capacity Analysis & Rollout

| Field | Details |
|---|---|
| **Files/Components** | Test suite, documentation, metrics table |
| **Exact Changes** | 1. Run automated PHP benchmarking harness to record post-implementation metrics.<br>2. Verify zero functional regressions across all drawer tabs and workflows.<br>3. Formulate the 1K, 5K, 10K concurrency model with calculations. |
| **Dependencies** | Phases 1–4. |
| **Business Behavior** | Full system stability. |
| **Risks** | None. |
| **Rollback** | N/A. |
| **Validation** | Automated benchmark script and test execution. |
| **Acceptance Criteria** | All tests pass; metrics demonstrate dramatic latency and query reduction; 0 regressions. |
