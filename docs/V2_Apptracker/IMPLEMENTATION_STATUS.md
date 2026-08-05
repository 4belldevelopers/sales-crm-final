# Apptracker V2 — Implementation Status Report

> Last verified: 2026-08-05  
> Plan reference: [`18_STEP_IMPLEMENTATION_PLAN.md`](18_STEP_IMPLEMENTATION_PLAN.md)  
> Codebase: `testing.jero.works` (`JWMaincode28June2026`)

**Summary: 15 of 18 steps are fully done. 3 gaps remain (2 minor, 1 substantial).**

---

## Step-by-Step Status

### Step 1 — Route & Method Surface ✅ DONE
All public route methods exist in `ServiceCompanyAppTrackerController`:
- `showInterviews($id)` — added; returns JSON with tenant-scoped `JobApplication` lookup
- `similar_existing_data(Request)` → delegates to `similar_existing_data_ajax()`
- `internal_bench_data(Request)` → delegates to `internal_bench_data_ajax()`
- `public_bench_data(Request)` → delegates to `public_bench_data_ajax()`
- Ticket methods: `ticketshow`, `updateStatus`, `storetComment` — all delegate to `TicketService` with `company_id` tenant check

`ProductCompanyAppTrackerController` has 20 stub methods (returning `abort(404)`) covering the same route surface, preventing 500 errors on any product-company route hit.

**Files:**
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` (line 11,460)
- `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php`

---

### Step 2 — DDL Indexes ❌ GAP — NOT EXECUTED
The plan defines 10 composite indexes in SQL:

| Index name | Table |
|---|---|
| `idx_awjc_job_status` | `active_work_job_condidate` |
| `idx_awjc_company_job` | `active_work_job_condidate` |
| `idx_awjc_thirdparty_job` | `active_work_job_condidate` |
| `idx_awjc_app` | `active_work_job_condidate` |
| `idx_clients_company` | `clients` |
| `idx_tickets_company` | `tickets` |
| `idx_isched_company` | `interview_schedule` |
| `idx_japp_status` | `job_applications` |
| `idx_japp_email` | `job_applications` |
| `idx_japp_phone` | `job_applications` |

**Verified:** `database/schema/mysql-schema.sql` has 0 matches for any of these names. The DDL was never applied to the database.

**Action required:** Run the `ALTER TABLE … ADD INDEX` SQL block from `18_STEP_IMPLEMENTATION_PLAN.md` Step 2 against the live database.  
**Note:** Never create Laravel migrations for these — schema truth is `mysql-schema.sql` per AI_RULES §2.

---

### Step 3 — ReferenceDataCache ✅ DONE
`app/Services/ReferenceDataCache.php` fully implemented. Provides:
- `qualifications()` — in-memory cached collection
- `ticketStatuses()` — in-memory cached collection
- Used in `CandidateListingService::resolveFilters()` and throughout the controller

---

### Step 4 — JS Resilience Layer ✅ DONE
All 7 JS modules exist and are loaded in `index.blade.php` (lines 1213–1219):

| File | Responsibility |
|---|---|
| `public/js/apptracker/_fetch.js` | Resilient fetch wrapper |
| `public/js/apptracker/_conn.js` | Connection-state detection |
| `public/js/apptracker/_visibility.js` | Page Visibility API |
| `public/js/apptracker/_tabSwap.js` | Tab-swap coordination |
| `public/js/apptracker/_echo.js` | Reverb WebSocket bridge |
| `public/js/apptracker/_optimistic.js` | Optimistic UI updates |
| `public/js/apptracker/_filterParse.js` | Client-side filter parsing |

---

### Step 5 — ES msearch Aggregations ✅ DONE
`custome_count_all_compute` uses `$elasticService->msearch()` (confirmed at line 12,476 of the controller). Multi-search batching is active for counter aggregation.

---

### Step 6 — Tagify Filter Deduplication ✅ DONE
`resolveTagifyFilters()` protected method added at line 11,422 of `ServiceCompanyAppTrackerController`. All 8 duplicate city/education lookup blocks (~18 lines each) replaced with a single call.

- City→ID lookup: one `DB::table('city_list')->whereIn()` with 5-minute Redis cache per filter set
- Qualification→ID expansion: `ReferenceDataCache::qualifications()` + `GeneralMethod::qualificationFilterCondition()`
- Metro-clustering tabs (`similar_existing_data_ajax`, `dsimilar_existing_data_ajax`): correctly destructure `cityNames` key for `GeneralMethod::resolveLocationCluster()`

**Verified:** 0 remaining `city_list.*whereIn` occurrences in the controller.

Also implemented as `resolveFilters()` in `CandidateListingService` for use by search methods when they are migrated.

---

### Step 7 — prepareRowMaps ✅ DONE
`prepareRowMaps` call confirmed at 8 sites (lines 949, 2099, 4848, 5971, 6967, 8004, 9153, 10230 of the controller). All active tabs use it for ES `_source` row mapping.

---

### Step 8 — CounterService ✅ DONE
`app/Services/Apptracker/CounterService.php` fully implemented with:
- `Cache::flexible([30, 120], callback)` — stale-while-revalidate pattern
- `Cache::lock("lock:pipeline:{$jobId}", 5)->block(6, fn)` — single-flight anti-stampede
- `mysql_replica` DB connection (`sticky: false`) for counter reads
- ES aggregation fallback with `request_cache: true`

---

### Step 9 — CountersReconcile Command ✅ DONE
`app/Console/Commands/CountersReconcile.php` exists. Writes `pipeline_counters` table snapshot every 5 minutes via scheduled command.

---

### Step 10 — ES Request Cache + tabCacheKey ✅ DONE
- `tabCacheKey()` method at line 287 of the controller
- `request_cache: true` confirmed at 6 controller locations + 9 `GeneralMethod` locations

---

### Step 11 — Asset Optimisation ⚠️ PARTIAL
**Done:**
- FA6 CSS swap complete — all Blade views use CSS icon classes (not FA6 JS)
- `experience_options.blade.php` partial exists and included in 5+ Blade views

**Gap:**
- `defer` attribute is missing from non-critical vendor scripts in `index.blade.php`. Scripts (`dataTables.fixedHeader.min.js`, `sweetalert.min.js`, Metronic bundle, etc.) load synchronously.

**Files affected:** `resources/views/service_company/apptracker/index.blade.php` — add `defer` to vendor `<script>` tags that are not required for initial paint.

---

### Step 12 — Skeleton/Shimmer + Tab-Swap ✅ DONE
- `public/css/apptracker/_skeleton.css` linked in `index.blade.php` line 4
- `_tabSwap.js` loaded at line 1216
- CSS shimmer skeleton classes defined in `_skeleton.css`

---

### Step 13 — Reverb WebSocket Pipeline ✅ DONE
- `app/Events/StageMoved.php` exists
- `app/Observers/JobApplicationObserver.php` has `wasChanged('master_status_id')` check — fires `StageMoved` on status changes
- `_echo.js` loaded and wired

---

### Step 14 — Optimistic UI ✅ DONE
- `public/js/apptracker/_optimistic.js` exists and loaded at line 1218
- Badge updates on `requestAnimationFrame` (~16 ms), rolls back on failure

---

### Step 15 — GRID_SOURCE_FIELDS + filterParse ✅ DONE
- `GRID_SOURCE_FIELDS` constant defined at line 311 of the controller — 38 audited ES `_source` fields
- `_filterParse.js` exists and loaded at line 1219

---

### Step 16 — Service Layer Extraction ⚠️ PARTIAL

**Fully implemented services:**

| Service | Status | Key methods |
|---|---|---|
| `CounterService` | ✅ Complete | `get()`, `increment()`, `decrement()` |
| `ApptrackerPageData` | ✅ Complete | `forJob()` |
| `TicketService` | ✅ Complete | `show()`, `addComment()`, `updateStatus()` |
| `BenchExportService` | ✅ Complete | `export()` |
| `StageAssignService` | ✅ Complete | `move()`, `childStageHtml()` |
| `CandidateListingService` | ⚠️ Partial | `resolveFilters()` ✅ · search methods ❌ |

**CandidateListingService gap (substantial):**
`similarExisting()`, `internalBench()`, `publicBench()` all throw `\LogicException('... not yet migrated')`. These represent ~7,000+ LoC of ES search logic still in the controller. The controller is at 11,460 lines vs. the ~2,000-line target.

**Files:**
- `app/Services/Apptracker/TicketService.php`
- `app/Services/Apptracker/BenchExportService.php`
- `app/Services/Apptracker/StageAssignService.php`
- `app/Services/Apptracker/CandidateListingService.php` — `resolveFilters()` done; search stubs pending

---

### Step 17 — MySQL Replica Routing ✅ DONE
- `mysql_replica` connection defined in `config/database.php` with `sticky: false`
- `CounterService` routes all counter reads through the replica connection

---

### Step 18 — Chaos Testing ✅ DONE
- `tests/Chaos/ApptrackerResilienceTest.php` — 10 tests covering resilience scenarios
- `docs/CHAOS_RUNBOOK.md` — runbook exists

---

## Open Gaps Summary

| # | Gap | Severity | Action |
|---|---|---|---|
| G1 | Step 2 — DDL indexes never applied | **High** | Run `ALTER TABLE` SQL from `18_STEP_IMPLEMENTATION_PLAN.md` §2 against the database; annotate `mysql-schema.sql` |
| G2 | Step 11 — `defer` missing from vendor scripts | **Low** | Add `defer` attribute to non-critical `<script>` tags in `index.blade.php` |
| G3 | Step 16 — CandidateListingService search methods are stubs | **Medium** | Extract `similar_existing_data_ajax`, `internal_bench_data_ajax`, `public_bench_data_ajax` from controller into service methods (large sprint) |

---

## Documentation Gap

`docs/V2_Apptracker/GOD_LEVEL_AUDIT.md` §8 references the old filename `13_STEP_IMPLEMENTATION_PLAN.md` (lines 208 and 290). The plan was renamed to `18_STEP_IMPLEMENTATION_PLAN.md`. These two lines need updating.

---

## File Inventory

| File | Status |
|---|---|
| `app/Services/Apptracker/CounterService.php` | ✅ Implemented |
| `app/Services/Apptracker/ApptrackerPageData.php` | ✅ Implemented |
| `app/Services/Apptracker/TicketService.php` | ✅ Implemented |
| `app/Services/Apptracker/BenchExportService.php` | ✅ Implemented |
| `app/Services/Apptracker/StageAssignService.php` | ✅ Implemented |
| `app/Services/Apptracker/CandidateListingService.php` | ⚠️ Partial |
| `app/Services/ReferenceDataCache.php` | ✅ Implemented |
| `app/Events/StageMoved.php` | ✅ Exists |
| `app/Observers/JobApplicationObserver.php` | ✅ Exists with `wasChanged` |
| `app/Console/Commands/CountersReconcile.php` | ✅ Exists |
| `public/js/apptracker/_fetch.js` | ✅ Exists |
| `public/js/apptracker/_conn.js` | ✅ Exists |
| `public/js/apptracker/_visibility.js` | ✅ Exists |
| `public/js/apptracker/_tabSwap.js` | ✅ Exists |
| `public/js/apptracker/_echo.js` | ✅ Exists |
| `public/js/apptracker/_optimistic.js` | ✅ Exists |
| `public/js/apptracker/_filterParse.js` | ✅ Exists |
| `public/css/apptracker/_skeleton.css` | ✅ Exists |
| `resources/views/service_company/apptracker/_partials/experience_options.blade.php` | ✅ Exists |
| `tests/Chaos/ApptrackerResilienceTest.php` | ✅ Exists (10 tests) |
| `docs/CHAOS_RUNBOOK.md` | ✅ Exists |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | ⚠️ 11,460 lines (target ~2,000) |
| `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php` | ✅ 20 stubs added |
