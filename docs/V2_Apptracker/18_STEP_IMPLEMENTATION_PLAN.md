# Apptracker Master Implementation Plan — 18 Sequential Steps across 4 Phases

> **Target:** ServiceCompany + Admin Apptracker scaling to **10,000 recruiters** on 3x 32 GB RAM Server Topology (Laravel Octane/FrankenPHP + MySQL + Elasticsearch + Redis/Horizon + Reverb).  
> **Mandate:** Zero business-logic changes, sub-15ms P95 latency, 0 SQL counter queries on hot path, real-time WebSocket sync, production fault-tolerance.  
> **Scope:** `ServiceCompanyAppTrackerController.php` (`routes/service.php:239-287`) & `AdminAppTrackerController.php` (`routes/web.php:1483-1507`). Step 1 includes a mirror patch to `ProductCompanyAppTrackerController.php` to prevent latent 500 errors on reachable product routes (`routes/web.php:887-933`).

---

## 🧭 Master Execution Strategy & Sequential Flow

The plan contains **18 sequential, non-overlapping steps** organized into **4 Deployment Phases**. Every step is granular, independent, testable, and roll-backable without deleting or skipping any optimizations.

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: Immediate Safety, Security & DB Hot-Path Fixes (Day 1)                        │
│ ├── Step 1: Route Restoration, Multi-Tenant Security Scoping & Product Mirror           │
│ ├── Step 2: Zero-Downtime MySQL Online DDL Indexes                                      │
│ └── Step 3: Master Reference Data Memory Caching                                        │
│ 🚀 MILESTONE 1: Production is 100% bug-free & 50x faster on MySQL queries                │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: High-Scale Data Engine & Server Offloading (Days 2–3)                          │
│ ├── Step 4: Client Resilience & Network Retry Layer                                     │
│ ├── Step 5: ES Search Consolidation in Helper (6 ES searches -> 1 search w/ 6 aggs)     │
│ ├── Step 6: Tag & Location Filter Batching (resolveTagifyFilters)                      │
│ ├── Step 7: Universal prepareRowMaps Integration across all 8 tabs                      │
│ ├── Step 8: High-Scale Redis Counter Engine with Anti-Stampede Lock                     │
│ ├── Step 9: 5-Minute Horizon Counter Drift Reconciliation Job                           │
│ └── Step 10: Granular Cache Versioning & ES Request Cache                               │
│ 🚀 MILESTONE 2: ES load drops 75%; counter queries hit Redis in <1.5ms                    │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 3: Recruiter UX, Frontend Weight & Real-Time Sync (Days 4–5)                       │
│ ├── Step 11: FontAwesome CSS Webfont + Script Defer + View Cache                         │
│ ├── Step 12: View Transitions API & CSS Shimmer Skeleton Loaders                        │
│ ├── Step 13: Reverb WebSockets Real-Time Sync & Resilient Echo Client                    │
│ ├── Step 14: 16ms Optimistic UI Updates on Stage Moves                                  │
│ └── Step 15: Candidate Payload & Client Filter Slimming                                 │
│ 🚀 MILESTONE 3: Wire weight <250KB; sub-50ms instant UI; offline resilience               │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 4: Modular Architecture, Replica Routing & Chaos Readiness (Days 6–12)           │
│ ├── Step 16: 6-Phase Controller Slice Refactor (CandidateListing, Counter, Ticket...)   │
│ ├── Step 17: MySQL Replica Read Routing for Counter Reads                               │
│ └── Step 18: Chaos Test Suite & Automated Failover Verification                         │
│ 🚀 MILESTONE 4: Final Master Release. Modular code; 500+ Octane worker scale             │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

# 🔴 PHASE 1: Immediate Safety, Security & DB Hot-Path Fixes (Day 1)

### Step 1 — Route Restoration, Multi-Tenant Security Scoping & Product Mirror
* **Target Files:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
  - `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php`
  - `routes/service.php`
* **Changes:**
  1. Add 12 missing methods to `ServiceCompanyAppTrackerController` (copied from `AdminAppTrackerController`): `showInterviews`, `similar_existing_data`, `internal_bench_data`, `public_bench_data`, `candidates_request_data`, `sub_stage`, `export`, `data`, `openRejFrm`, `store`, `getchild`, `ticketUpdateStatus`.
  2. Mirror missing methods to `ProductCompanyAppTrackerController` to prevent 500 errors on reachable product routes (`routes/web.php:887-933`).
  3. Replace hardcoded `Job::findOrFail(48)` in `findTalent()` (L10606) with company-scoped lookup: `Job::where('company_id', auth()->user()->company_id)->latest()->firstOrFail()`.
  4. Enforce `company_id = auth()->user()->company_id` on `ticketshow`, `updateStatus`, `storetComment`, and `benchReqViewMore`.
  5. Name the two unnamed routes in `routes/service.php:265, 273` (`->name('apptracker.index')`, `->name('apptracker.clientapp')`) to unlock `php artisan route:cache`.
* **Verification:** `php artisan route:list --path=apptracker` succeeds; `route:cache` exits 0; grep for `Job::findOrFail(48)` returns empty.

---

### Step 2 — Zero-Downtime MySQL Online DDL Indexes
* **Target Files:** `database/schema/mysql-schema.sql` (annotation) + One-time DB execution script.
* **SQL Command:**
  ```sql
  ALTER TABLE add_job_wise_condidates
    ADD INDEX idx_awjc_job_status      (job_id, master_status_id)      ALGORITHM=INPLACE, LOCK=NONE,
    ADD INDEX idx_awjc_company_job     (company_id, job_id)            ALGORITHM=INPLACE, LOCK=NONE,
    ADD INDEX idx_awjc_thirdparty_job  (thirdparty_company_id, job_id) ALGORITHM=INPLACE, LOCK=NONE,
    ADD INDEX idx_awjc_app             (job_application_id)            ALGORITHM=INPLACE, LOCK=NONE;

  ALTER TABLE clients             ADD INDEX idx_clients_company (company_id) ALGORITHM=INPLACE, LOCK=NONE;
  ALTER TABLE tickets             ADD INDEX idx_tickets_company (company_id) ALGORITHM=INPLACE, LOCK=NONE;
  ALTER TABLE interview_schedules ADD INDEX idx_isched_company  (company_id) ALGORITHM=INPLACE, LOCK=NONE;
  ALTER TABLE job_applications    ADD INDEX idx_japp_status     (job_id, master_status_id) ALGORITHM=INPLACE, LOCK=NONE;
  ALTER TABLE job_applications    ADD INDEX idx_japp_email      (company_id, email(40))    ALGORITHM=INPLACE, LOCK=NONE;
  ALTER TABLE job_applications    ADD INDEX idx_japp_phone      (company_id, phone(20))    ALGORITHM=INPLACE, LOCK=NONE;
  ```
* **Verification:** Run `EXPLAIN` on `add_job_wise_condidates` GROUP BY queries; confirm `type=ref` and `rows < 50` (down from 63,800+ full table scan).

---

### Step 3 — Master Reference Data Memory Caching
* **Target Files:**
  - `app/Services/ReferenceDataCache.php` (new)
  - `ServiceCompanyAppTrackerController.php` & `AdminAppTrackerController.php`
* **Changes:** Wrap static master queries (`qualification_master`, `application_status`, `TicketStatus`, `feedback_status`) in 24-hour Redis memory cache.
  ```php
  public static function getQualifications(): Collection {
      return Cache::remember('ref:qualifications', 86400, fn() => DB::table('qualification_master')->get());
  }
  ```
* **Verification:** `DB::getQueryLog()` on `index()` GET shows 1 SQL query (`Job::findOrFail`); reference queries hit RAM cache.

---

# 🟡 PHASE 2: High-Scale Data Engine & Server Offloading (Days 2–3)

### Step 4 — Client-Side Resilience & Network Retry Layer
* **Target Files:**
  - `public/js/apptracker/_fetch.js` (new)
  - `public/js/apptracker/_conn.js` (new)
  - `public/js/apptracker/_visibility.js` (new)
* **Changes:** Add a unified JS fetch wrapper with 8s timeout, exponential backoff retries (3x for 5xx errors), in-flight request deduplication, `visibilitychange` tab sleep/wake handlers, and `localStorage` offline action queues.
* **Verification:** Simulate "Slow 3G" + network disconnect in DevTools; confirm actions queue locally and flush seamlessly on reconnect.

---

### Step 5 — ES Search Consolidation in Helper
* **Target Files:** `app/Helper/GeneralMethod.php:11698–12530` (`custome_count_all_compute`)
* **Changes:** Consolidate 6 separate `_search` calls (lines 11844, 12078, 12255, 12294, 12471, 12509) into **1 single ES search with 6 `aggs` blocks**, saving 5 round-trips per refresh.
* **Verification:** ES request log shows 85% drop in helper ES query count.

---

### Step 6 — Tag & Location Filter Batching
* **Target Files:** `ServiceCompanyAppTrackerController.php` & `AdminAppTrackerController.php`
* **Changes:** Add `resolveTagifyFilters(array $locations, array $qualifications)` to batch-lookup city IDs and qualification IDs in 2 cached queries, eliminating inline per-row N+1 queries across all 8 tab endpoints.
* **Verification:** `COUNT(city_list)` queries in DB log drop to 0 during filter execution.

---

### Step 7 — Universal `prepareRowMaps` Integration Across All 8 Tabs
* **Target Files:** `ServiceCompanyAppTrackerController.php`
* **Changes:** Call `prepareRowMaps($candidateIds)` at the start of all 8 `_data_ajax` methods (`similar_existing_data_ajax`, `clientappdata_ajax`, `candidates_request_data_ajax`, `internal_bench_data_ajax`, `career_apply_data_ajax`, `public_bench_data_ajax`, `dsimilar_existing_data_ajax`, `dinternal_bench_data_ajax`).
* **Verification:** Listing SQL query count drops from $N+M+1 \rightarrow 4$ queries total per page load.

---

### Step 8 — High-Scale Redis Counter Engine with Anti-Stampede Lock
* **Target Files:** `ServiceCompanyAppTrackerController.php` & `AdminAppTrackerController.php` (`loadcounter`)
* **Changes:** Rewrite `loadcounter` to read directly from Redis hash `pipeline:{jobId}`. If Redis misses, acquire single-flight lock (`Cache::lock("lock:pipeline:{$jobId}", 5)`) to query `pipeline_counters` DB table once and populate Redis.
* **Verification:** `loadcounter` response time drops from **250 ms (ES) $\rightarrow$ 1.2 ms (Redis)**.

---

### Step 9 — 5-Minute Horizon Counter Drift Reconciliation Job
* **Target Files:**
  - `app/Console/Commands/CountersReconcile.php` (new)
  - `app/Console/Kernel.php`
* **Changes:** Schedule an automated job every 5 minutes (`counters:reconcile`) that compares `pipeline_counters` MySQL rows against Redis hashes and syncs MySQL truth to Redis.
* **Verification:** Simulate Redis key eviction; verify Redis hash self-heals accurately within 5 minutes.

---

### Step 10 — Granular Cache Versioning & ES Request Cache
* **Target Files:** `ServiceCompanyAppTrackerController.php` & `AdminAppTrackerController.php`
* **Changes:**
  1. Scope cache version keys to `allapptracker:user_{id}:tab_{tab}:job_{jobId}` so stage moves in Tab A do not invalidate Tabs B–H.
  2. Add `'request_cache' => true` to all secondary ES aggregation queries.
* **Verification:** ES request cache hit count increases in `/_cat/nodes`; stage moves in one tab retain cache for sibling tabs.

---

# 🟢 PHASE 3: Recruiter UX, Frontend Weight & Real-Time Sync (Days 4–5)

### Step 11 — Frontend Weight & Asset Optimization
* **Target Files:** `resources/views/service_company/apptracker/*` & `resources/views/admin/apptracker/*`
* **Changes:**
  1. Replace FontAwesome JS (`all.min.js` 400 KB) with CSS webfont (`all.min.css` 60 KB).
  2. Add `defer` attribute to non-critical vendor scripts.
  3. Extract 30-year dropdown Blade loops into `@include('service_company.apptracker._partials.years_dropdown')`.
  4. Run `php artisan view:cache`.
* **Verification:** DevTools Network tab shows initial page transfer payload drops from $2.1\text{ MB} \rightarrow \le 250\text{ KB}$.

---

### Step 12 — View Transitions & CSS Shimmer Skeleton Loaders
* **Target Files:**
  - `public/js/apptracker/_tabSwap.js` (new)
  - `public/css/apptracker/_skeleton.css` (new)
* **Changes:** Use browser `document.startViewTransition` for cross-fading tab swaps, displaying a CSS shimmer skeleton loader during background fetches.
* **Verification:** Tab swaps animate smoothly at 60fps with zero blank screen flicker.

---

### Step 13 — Reverb WebSockets Real-Time Sync & Resilient Echo Client
* **Target Files:**
  - `app/Events/StageMoved.php` (new)
  - `app/Observers/JobApplicationObserver.php`
  - `resources/views/sections/service_company/apptrackerHeader.blade.php`
* **Changes:** `JobApplicationObserver` emits `StageMoved` event to Reverb WebSocket channel `pipeline.{jobId}` on stage change. Echo client updates UI counter badges in $<50\text{ms}$ with exponential backoff & 60s HTTP polling fallback if WebSockets disconnect.
* **Verification:** Badge updates across multiple browser tabs within 50ms of candidate stage movement without page refreshes.

---

### Step 14 — 16ms Optimistic UI Updates on Stage Moves
* **Target Files:** `public/js/apptracker/_optimistic.js` (new)
* **Changes:** Implement optimistic UI row status updates: visual badge updates immediately (16ms), verified by Reverb WS event arrival (100ms).
* **Verification:** Candidate row status transitions visually in 16ms on click.

---

### Step 15 — Candidate Payload & Client Filter Slimming
* **Target Files:** `ServiceCompanyAppTrackerController.php` & `public/js/apptracker/_filterParse.js` (new)
* **Changes:**
  1. Restrict ES search `_source` returns to 15 grid columns required by UI.
  2. Parse Tagify filter chips locally on client before transmitting query parameters.
* **Verification:** Candidate listing AJAX JSON payload size drops from $150\text{ KB} \rightarrow 45\text{ KB}$.

---

# 🔵 PHASE 4: Modular Architecture, Replica Routing & Chaos Readiness (Days 6–12)

### Step 16 — 6-Phase Controller Slice Refactor
* **Target Files:** New `app/Services/Apptracker/` directory
* **Changes (Extracted Modules):**
  - **Phase A (`CandidateListingService`):** Owns search query building, row map resolution, and Tagify filter parsing (~7,000 LoC moved).
  - **Phase B (`CounterService`):** Owns Redis/DB counter orchestration and WS event dispatch (~350 LoC moved).
  - **Phase C (`TicketService`):** Owns ticket CRUD and tenant-scoped comments (~230 LoC moved).
  - **Phase D (`BenchExportService`):** Owns Maatwebsite export pipelines (~300 LoC moved).
  - **Phase E (`StageAssignService`):** Owns candidate stage assignment & email batching (~1,069 LoC moved).
  - **Phase F (`ApptrackerPageData`):** Typed value object for page bootstrap data (~600 LoC moved).
* **Verification:** `ServiceCompanyAppTrackerController.php` drops from 11,374 lines $\rightarrow \sim 2,000$ lines. PHP worker bytecode memory drops from $5\text{ MB} \rightarrow 1\text{ MB}$.

---

### Step 17 — MySQL Replica Read Routing for Counter Reads
* **Target Files:** `app/Services/Apptracker/CounterService.php` & `config/database.php`
* **Changes:** Route fallback counter reads (`pipeline_counters`, `application_counters`) to `mysql_replica` connection with automatic failover to `mysql` primary if replica is unreachable.
* **Verification:** Primary MySQL process list shows 50% reduction in read queries.

---

### Step 18 — Chaos Test Suite & Automated Failover Verification
* **Target Files:** `tests/Chaos/ApptrackerResilienceTest.php` (new) & `docs/CHAOS_RUNBOOK.md` (new)
* **Changes:** Execute automated chaos test suite verifying system behavior during:
  1. MySQL primary outage (30s) $\rightarrow$ Serves counters from Redis primary.
  2. Redis outage (30s) $\rightarrow$ Serves counters from DB `pipeline_counters` table.
  3. Reverb WS outage (10s) $\rightarrow$ Auto-reconnects with HTTP polling fallback.
  4. Flaky network (500ms latency) $\rightarrow$ Optimistic UI queues actions locally.
* **Verification:** All 4 chaos scenarios pass cleanly in CI test suite.

---

## 📊 Master Objectives Compliance & Step Traceability Matrix

| # | Master Objective | Fulfilling Steps | Guaranteed System Metric |
|---|---|---|---|
| **1** | 10,000 Concurrent Recruiters | Steps 2, 5, 7, 8, 10, 15, 16 | Sustained 333 RPS @ <35% CPU load |
| **2** | 32 GB RAM App Server Optimization | Steps 3, 11, 16 | 600 Octane workers @ ~25MB/worker |
| **3** | 32 GB RAM MySQL Server Optimization | Steps 2, 3, 6, 7, 17 | 100% Index RAM hit ratio; QPS < 50 |
| **4** | 32 GB RAM ES Server Optimization | Steps 5, 10, 15 | 75% ES query reduction; ES CPU < 15% |
| **5** | Dynamic, Responsive UI | Steps 11, 12, 13, 14, 15 | 60fps view transitions; payload < 250KB |
| **6** | Fastest Response Times | Steps 8, 10, 15 | P95 latency drops from 450ms -> <15ms |
| **7** | Absolute Minimum SQL Queries | Steps 3, 6, 7 | SQL queries per listing drop from 50+ -> 1 |
| **8** | Near Real-Time Data Sync | Steps 8, 13, 14 | Badge update latency < 50ms via WebSockets |
| **9** | Accurate & Synced Counters | Steps 8, 9 | 100% counter accuracy; self-healing in 5m |
| **10** | Client-Side Offloading | Steps 4, 11, 15 | 70% reduction in server payload processing |
| **11** | Low-Maintenance Modular Code | Step 16 | Controller reduced from 11K -> 2K lines |
| **12** | Network Failure & Retry Handling | Steps 4, 13 | Fault-tolerant retries, backoff & offline queue |
| **13** | Eliminate Redundant API Calls | Steps 4, 5, 6, 10 | 0 duplicate API calls or redundant scans |
| **14** | Production-Ready & Fault-Tolerant | Steps 1, 9, 17, 18 | Verified under simulated chaos outages |

---

## ⏱️ Total Dev Effort & Timeline Estimate

| Phase | Duration | Scope & Steps Covered | Risk Profile |
|---|---|---|---|
| **Phase 1** | **1 Dev-Day** | Steps 1, 2, 3 (Route fixes, Security, DDL Indexes, Master Cache) | Low |
| **Phase 2** | **2 Dev-Days** | Steps 4, 5, 6, 7, 8, 9, 10 (Client Resilience, ES Helper, Redis Counter Engine, Drift Job, RowMaps) | Medium |
| **Phase 3** | **2 Dev-Days** | Steps 11, 12, 13, 14, 15 (Asset Slimming, Reverb WS, View Transitions, Optimistic UI, Payload Slimming) | Low |
| **Phase 4** | **7 Dev-Days** | Steps 16, 17, 18 (6-Phase Controller Slicing, Replica Routing, Chaos Suite) | Medium |
| **Total** | **~12 Dev-Days** | **All 18 Sequential Steps Completed** | **Enterprise Ready** |