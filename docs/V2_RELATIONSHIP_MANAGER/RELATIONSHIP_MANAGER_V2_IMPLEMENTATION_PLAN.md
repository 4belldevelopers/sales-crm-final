# IMPLEMENTATION PLAN — SELECT RELATIONSHIP MANAGER DRAWER V2

> **Document Type:** Production Engineering Implementation Blueprint  
> **Module:** Relationship Manager Drawer V2 (Bench Candidates & Positions)  
> **Platform:** JERO ATS & Talent Marketplace  
> **Target Scale:** 10,000 Concurrent Recruiters / 0% Avoidable Infrastructure Strain  
> **Status:** Ready for Phased Execution  

---

## 1. Executive Overview

This plan details the step-by-step implementation to transition the **Select Relationship Manager Drawer** from a heavy, server-rendered, MySQL-reading legacy model into a **Client-First, Browser-RAM-Executed, 0% Server/DB Load Architecture**.

### The 4 Non-Negotiable Targets
1. **0% MySQL Reads on Drawer Open:** Zero `SELECT` queries or multi-joins on opening the drawer.
2. **0% Uncached ES Aggregations:** Zero real-time Elasticsearch aggregations (`size: 2000`) on drawer open; workload stats pre-aggregated in Redis.
3. **100% Client/Browser RAM Execution:** Drawer rendering, filtering, search, and state caching executed in user browser memory (`window.JeroRmStore` + `sessionStorage`), opening in `< 2ms`.
4. **Zero Stale ATS Pipeline Data:** Immediate Elasticsearch index update (`hmData` in `ELASTIC_CANDIDATES_BENCH`) and multi-tenant cache invalidation upon any assignment change.

---

## 2. Phase-by-Phase Technical Blueprint

### Phase 1: Backend CQRS & Workload Pre-Aggregation

#### 1.1 Redis Team Roster Caching
* **Goal:** Eliminate raw 5-table MySQL JOIN in `ServiceCompanyBenchCandidatesController::hmlisting()`.
* **Implementation:**
  * When fetching the company's active team roster, read from Redis cache key `team:roster:{company_id}`.
  * If cache misses, query MySQL (write authority) with tenant scope `company_id` and cache for 3,600 seconds (1 hour).
  * Payload structure (JSON):
    ```json
    [
      {
        "id": 142,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "phone": "+1 555-0199",
        "role": "Senior Technical Recruiter",
        "initial": "J",
        "avatarClass": "bg-light-primary text-primary",
        "active": 1
      }
    ]
    ```

#### 1.2 Redis Workload Stats Pre-Aggregation
* **Goal:** Eliminate dynamic Elasticsearch aggregations (`size: 2000` on `ELASTIC_JOBS`) from Blade templates.
* **Implementation:**
  * Workload counts (`activeJobs`, `captainJobs`, `trmTalents`) are stored in Redis hash: `team:workload:{company_id}`.
  * Updated automatically via existing job assignment events or cached with a 300-second TTL.
  * When serving team roster, merge workload counts in PHP memory in <1ms without calling Elasticsearch.

#### 1.3 New Slim REST API Endpoint
* Create route in `routes/service.php`:
  `GET /service_company/bench-candidates/rm-roster` -> `ServiceCompanyBenchCandidatesController::rmRosterJson()`
* Returns pure JSON:
  ```json
  {
    "status": "success",
    "version": 104,
    "team": [ ... ]
  }
  ```
* Size: ~3.5 KB compressed (vs ~65 KB HTML today).

---

### Phase 2: Frontend Client-First Engine (`JeroRmStore`)

#### 2.1 Implement In-Memory RAM Store
* File: `resources/views/service_company/bench-candidates/partials/_rm_store_js.blade.php` (or integrated into page scripts).
* Features:
  * `window.JeroRmStore = { roster: [], version: 0, isLoaded: false, ... }`
  * Caches team roster in `sessionStorage.getItem('jero_rm_roster_' + companyId)`.
  * If cached, hydrates instantly on page load with 0 network calls.
  * If cache expired, performs 1 background AJAX fetch to `rm-roster`.

#### 2.2 In-Memory Drawer Presentation Engine
* The drawer markup (`#kt_activitiesHMB`) is static in the page layout.
* On clicking `.hmb-listing` on any candidate row:
  ```javascript
  function openRelationshipManagerDrawer(candidateId, candidateName, currentRmIds) {
      // 1. Instant open (0ms)
      $('#kt_activitiesHMB_toggle').trigger('click');
      $('#rmDrawerCandidateName').text(candidateName);
      
      // 2. Resolve selected users & available users directly in RAM
      var team = window.JeroRmStore.roster;
      var selected = team.filter(u => currentRmIds.includes(u.id));
      
      // 3. Render cards & table via DocumentFragment in < 2ms
      renderRmSelectedCards(selected);
      renderRmTeamTable(team, currentRmIds);
  }
  ```
* **Latency:** < 2 milliseconds. Zero spinner, zero network delay.

#### 2.3 Browser RAM Search Filter (Zero DOM Reflows)
* On search input keystroke:
  ```javascript
  $('#searchInputRM').on('input', function() {
      var term = this.value.trim().toLowerCase();
      var filtered = window.JeroRmStore.roster.filter(u => 
          u.name.toLowerCase().includes(term) || u.role.toLowerCase().includes(term)
      );
      renderRmTeamTableRows(filtered, currentRmIds);
  });
  ```
* Pure JavaScript array filtering in RAM; replaces heavy jQuery DOM iteration and layout thrashing.

---

### Phase 3: REST Mutation, Atomic Locking & Elasticsearch Sync

#### 3.1 Migrate Mutation Endpoint to HTTP POST
* Convert `hmlistingupdate` from `GET` to `POST`:
  Route: `POST /service_company/bench-candidates/rm-assign`
  Payload: `{ candidate_id: 120, user_id: 14, action: 'assign' | 'remove', _token: csrf }`
* Distributed Redis Lock:
  `Cache::lock("rm_assign:{$candidateId}:{$userId}", 5)->get(function() { ... });`
  Prevents duplicate clicks or race conditions under high concurrency.

#### 3.2 Asynchronous Elasticsearch Synchronization
* When `bench_assign` row is inserted or deleted in MySQL:
  ```php
  // 1. MySQL Write (Authority)
  if ($action === 'assign') {
      DB::table('bench_assign')->insertOrIgnore([...]);
  } else {
      DB::table('bench_assign')->where(...)->delete();
  }

  // 2. Dispatch Async Horizon ES Indexing Job
  SyncBenchCandidateRmJob::dispatch($candidateId, $companyId)->afterCommit();

  // 3. Multi-Tenant Atomic Epoch Bump (Rule 6)
  Cache::increment("bench:tenant:{$companyId}:epoch");
  ```
* `SyncBenchCandidateRmJob` pulls updated `hmData` and executes a partial document update (`_update`) on `ELASTIC_CANDIDATES_BENCH`:
  ```php
  $elasticClient->update([
      'index' => ELASTIC_CANDIDATES_BENCH,
      'id'    => $candidateId,
      'body'  => [
          'doc' => ['hmData' => $freshRmData]
      ]
  ]);
  ```
* **Outcome:** Zero stale data in ATS; candidates queried via ES immediately display updated Relationship Managers.

#### 3.3 Optimistic UI & Zero Secondary Reloads
* When the user clicks "Add" or "Remove":
  1. Instantly swap button class and text (0ms).
  2. Instantly update Selected Cards grid in RAM (0ms).
  3. Instantly update the RM avatar stack on the parent candidate row in RAM (0ms).
  4. Send background `POST` request to server.
  5. Remove `fetchBenchBenchCandidatesCandidateList()`; do NOT re-fetch the entire page.

---

### Phase 4: Metronic UI & 15.6-Inch Screen Sizing

#### 4.1 Strict Responsive Calibration
* Calibrate drawer container width:
  ```css
  #kt_activitiesHMB {
      width: min(740px, 94vw) !important;
      max-width: 94vw !important;
  }
  ```
* Drawer Table Column Widths:
  * Avatar + Name: `min-width: 170px`
  * Contact: `min-width: 65px`
  * Workload Stats: `min-width: 180px`
  * Status: `min-width: 75px`
  * Action Button: `min-width: 85px`
* Fixes all horizontal scrollbar regressions on 1366×768 laptops and 1920×1080 screens with 125% OS scaling.

#### 4.2 Typography & Icon Standards
* Cap all typography to `font-family: 'Poppins', sans-serif !important; font-size: <= 14px;`.
* Unify all icons to Metronic Line Awesome (`la la-user-check`, `la la-briefcase`, `la la-crown`).

---

## 3. Verification & Acceptance Checklist

| Step # | Verification Test | Expected Standard | Tool / Method |
|---|---|---|---|
| 1 | Warm Drawer Open Query Count | **0 MySQL queries** | Laravel Telescope / Query Logger |
| 2 | Warm Drawer ES Aggregations | **0 ES aggregations** | ES Slow Log / Telemetry |
| 3 | Drawer Presentation Speed | **< 2 ms** | Chrome DevTools Performance tab |
| 4 | Network Request on Warm Open | **0 bytes (0 HTTP requests)** | Chrome DevTools Network tab |
| 5 | Filter Input Latency | **< 1 ms (0 DOM reflows)** | DevTools Heap & CPU Profiler |
| 6 | Mutation HTTP Method | **HTTP POST with CSRF & Lock** | Network Inspector |
| 7 | ATS Recruitment Freshness | Candidate card updates in ES < 500ms | ES GET API on `ELASTIC_CANDIDATES_BENCH` |
| 8 | Multi-Tenant Invalidation | Other recruiters see update on next tick | Dual browser session test |
| 9 | 15.6-Inch Screen Sizing | 0 horizontal scroll, internal scroll only | Chrome DevTools 1366×768 resolution |
| 10 | Code Quality & Syntax | `php -l` PASS, Pint format clean | CLI command line check |

---

## 4. Rollback & Contingency Plan

* The feature is guarded by a feature toggle in `config/feature.php`:
  `'rm_drawer_v2_enabled' => env('FEATURE_RM_DRAWER_V2_ENABLED', true)`
* In the event of an unexpected edge-case failure, setting `FEATURE_RM_DRAWER_V2_ENABLED=false` in `.env` immediately reverts the drawer trigger to the legacy server-rendered route without downtime.
