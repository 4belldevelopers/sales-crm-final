# GOD-LEVEL APPTRACKER CONSISTENCY ARCHITECTURE & AUDIT SPECIFICATION

**Document Version:** 8.0.0 (Enterprise God-Level Master Specification)  
**Date:** October 7, 2026  
**Status:** Architecture Locked — Awaiting User Sign-Off to Code  
**Target Entities:**
- `resources/views/service_company/apptracker/index.blade.php` (Bounded RAM Map, Non-Authoritative Row Highlight, Reverb Listener Invariant, Bounded Post-Timeout Reconciliation)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` (Two-Tier Dataset Versioning, `allDataAjax()`)
- `resources/views/service_company/job-applications/create.blade.php` (Readiness Gating, URL Navigation Intent, Bounded Timeout)
- `resources/views/service_company/job-applications/clientcreate.blade.php` (Readiness Gating, URL Navigation Intent, Bounded Timeout)
- `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`store()`, `clientappstore()`, Idempotency, Resilient Redis Non-Blocking Broadcast)
- `app/Events/AppTrackerCandidateAssignedEvent.php` (Real-time Reverb Delta Broadcasting)
- `app/Jobs/ProcessOutboxEventJob.php` (Horizon Asynchronous CQRS Ingestion)

---

## 1. THE SUPREME TRUTH HIERARCHY

```text
══════════════════════════════════════════════════════════════════════════════════════════
                            THE SUPREME TRUTH HIERARCHY
           Truth  ──►  Freshness  ──►  Consistency  ──►  Performance  ──►  UX
══════════════════════════════════════════════════════════════════════════════════════════
```

Under no circumstances may Performance or Caching mechanisms compromise Data Truth or Freshness.

---

## 2. THE 6 GOLDEN LAWS OF APPTRACKER CONSISTENCY

1. **Law 1 — CQRS Division of Truth:** MySQL is the authoritative transactional persistence layer (ACID master). Elasticsearch is the authoritative read model for AppTracker UI presentation.
2. **Law 2 — Browser Persistent Storage May Never Contain Authoritative Candidate Data:** `sessionStorage`, `localStorage`, and `IndexedDB` must NEVER store candidate listing snapshots (neither HTML tables nor JSON records). Ephemeral browser RAM (`ramMap`) is the only client cache permitted.
3. **Law 3 — Performance Layer is Subordinate to Freshness Layer:** In-memory RAM optimizes rendering speed (<1ms), but MUST instantly yield whenever a version mismatch or direct navigation occurs.
4. **Law 4 — Reverb Broadcasts Invalidation Signals; It Never Forces Destructive Whole-Page Rebuilds:** Active recruiter workflows are protected from layout thrashing; counters patch live in 0ms, and active views re-fetch only if relevant.
5. **Law 5 — Candidate Creation Idempotency is Enforced Server-Side:** `client_submission_uuid + tenant_id + job_id` guarantees that network retries, double-clicks, and browser refreshes can never commit duplicate candidate records.
6. **Law 6 — URL Parameters are Non-Authoritative Presentation Hints Only:** Parameters like `new_candidate_id` exist purely for client-side UI highlighting and smooth scroll. Backend queries authoritatively enforce authenticated tenant and job boundaries.

---

## 3. DUAL FAILURE MODE TAXONOMY

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                              DUAL FAILURE MODE TAXONOMY                                │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ Failure Mode A (PRIMARY CAUSE — ~90% of occurrences):                                  │
│ Stale Persistent Browser Storage Interception:                                         │
│ Candidate #97836 was already written to MySQL and indexed in Elasticsearch, but the    │
│ AppTracker page served a pre-submission candidate-list snapshot from browser           │
│ sessionStorage (which had a 60-second TTL), completely aborting the network fetch.    │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ Failure Mode B (SECONDARY RACE CONDITION — ~10% of occurrences):                       │
│ Premature Navigation Before Index Readiness:                                           │
│ If Horizon worker queues experience backpressure or if a user clicked "Go to Tracker  │
│ Now" before checkIndexStatus() confirmed search-readiness, navigation redirected to    │
│ AppTracker before Elasticsearch committed the document into its searchable segment.    │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

Both failure modes are structurally eliminated by this specification.

---

## 4. TWO-TIER OPERATIONAL DATASET VERSIONING & REDIS RESILIENCE

### 4.1 Redis is a Consistency Accelerator, Never the Source of Truth
- If Redis is temporarily unavailable or experiences a connection timeout, candidate creation **MUST NOT** fail or roll back MySQL.
- Post-commit Redis operations (`cache()->increment(...)` and `AppTrackerCandidateAssignedEvent::dispatch(...)`) run in resilient non-blocking try/catch blocks.
- The redirect to AppTracker carries `?new_candidate_id=...`, which bypasses RAM and queries Elasticsearch directly, guaranteeing candidate visibility even during Redis downtime.

### 4.2 Two-Tier Invalidation Scope
| Dimension | **Job Dataset Version** (`ats:version:job:{comp}:{job}`) | **Tenant Dataset Version** (`ats:version:tenant:{comp}`) |
|---|---|---|
| **Primary Scope** | **AppTracker Listing Invalidation** (Granular Requisition Scope) | **Tenant-Wide Integrity & Sanity Checking** (Broad Scope) |
| **When Incremented?** | Candidate added, updated, or stage-moved within that specific `job_id`. | Structural tenant updates (bulk import, company settings change, stage configuration mutations). |
| **Who Increments?** | `JobApplicationController` (via atomic `INCR`), `ProcessOutboxEventJob`. | Admin actions, tenant configuration services. |
| **Client Listener** | `tenant.{companyId}.job.{jobId}` Reverb private channel. | `company.{companyId}` broad notification channel. |
| **Client Effect** | Clears `ramMap` for that job; triggers immediate active view re-fetch. | Verifies session validity; triggers background reconciliation during focus if mismatch detected. |
| **AppTracker Impact** | **Job A writes do NOT evict Job B's cache.** | **Zero routine AppTracker cache busting.** |

### 4.3 Atomic Sequence Guarantee
The event version must **never** be determined via a separate `get()` call after `increment()`. The return value of atomic Redis `INCR` is captured directly:
```php
$jobDatasetVersion = (int) cache()->increment("ats:version:job:{$company_id}:{$rawJobId}");
```

---

## 5. READINESS & BOUNDED POST-TIMEOUT RECONCILIATION

### 5.1 Add Candidate Readiness State Machine
```text
  [ USER SUBMIT ]
         │
         ▼
    SUBMITTING   (Button disabled + Metronic spinner)
         │
         ▼
       SAVED     (MySQL transaction committed <15ms + Outbox recorded)
         │
         ▼
     INDEXING    (Polling checkIndexStatus with 10s ceiling)
     ┌───┴──────────────────────────────┐
     ▼                                  ▼
   READY                            TIMEOUT_GRACEFUL (At 10s ceiling)
   (res.indexed === true)           (MySQL is safe; ES is catching up)
     │                                  │
     ▼                                  ▼
"Candidate Ready!"               "Candidate saved! Search index updating..."
Modal confirms                   Modal confirms & unlocks navigation
     │                                  │
     └─────────────────┬────────────────┘
                       ▼
            [ Redirect to AppTracker ]
```

### 5.2 AppTracker Bounded Post-Timeout Reconciliation
If the recruiter navigates under `TIMEOUT_GRACEFUL` with `?new_candidate_id=97836`, and the candidate is not yet returned in the first Elasticsearch read:
```text
Authoritative ES Read
         │
         ├── Found? ──► YES ──► Highlight row (3.5s Metronic green pulse) & clean URL.
         │
         └── Found? ──► NO  ──► Start Bounded Reconciliation:
                                 ├── Retry 1: Wait 1.0s -> Query ES
                                 ├── Retry 2: Wait 2.0s -> Query ES
                                 ├── Retry 3: Wait 4.0s -> Query ES
                                 └── STOP (Never poll infinitely!)
```

---

## 6. MASTER DATA & CACHE HIERARCHY

```text
 ┌────────────────────────────────────────────────────────────────────────────────────────┐
 │                              AUTHORITATIVE WRITE PATH                                  │
 └────────────────────────────────────────────────────────────────────────────────────────┘
                                 [ Add Candidate Submit ]
                                            │
                                            ▼
                           ┌──────────────────────────────────┐
                           │      MySQL ACID Transaction      │
                           │  - SysIdempotencyKey Check       │
                           │  - job_applications (Insert)     │
                           │  - add_job_wise_condidates       │
                           │  - candidate_outbox_events       │
                           └─────────────────┬────────────────┘
                                             │ Commit (<15ms)
                                             ▼
                           ┌──────────────────────────────────┐
                           │       Horizon Outbox Worker      │
                           │     - ProcessOutboxEventJob      │
                           │     - refresh: true              │
                           └─────────────────┬────────────────┘
                                             │
                                             ▼
                           ┌──────────────────────────────────┐
                           │     Elasticsearch Indexing       │
                           │      (ats_candidates_elastic)    │
                           └─────────────────┬────────────────┘
                                             │ Confirmed Search-Ready
                    ┌────────────────────────┴────────────────────────┐
                    ▼                                                 ▼
      ┌───────────────────────────┐                     ┌───────────────────────────┐
      │   Atomic Redis INCR       │                     │  Reverb Broadcast Event   │
      │   $jobDatasetVersion =    │                     │  AppTrackerCandidate      │
      │   INCR ats:version:job    │                     │  AssignedEvent (v: 174)   │
      └─────────────┬─────────────┘                     └─────────────┬─────────────┘
                    │                                                 │
 ┌──────────────────┼─────────────────────────────────────────────────┼───────────────────┐
 │                  ▼                                                 ▼                   │
 │   ┌─────────────────────────────┐                    ┌─────────────────────────────┐   │
 │   │ Initial Render / Redirect   │                    │ Real-Time Active Screen     │   │
 │   │ - ?new_candidate_id=97836   │                    │ - Echo receives v: 174      │   │
 │   │ - Injects dataset_v: 174    │                    │ - If active view: re-fetch  │   │
 │   │ - Bypasses RAM cache        │                    │ - Else: patch counter badge │   │
 │   └──────────────┬──────────────┘                    └─────────────┬───────────────┘   │
 │                  │                                                 │                   │
 │                  ▼                                                 ▼                   │
 │   ┌────────────────────────────────────────────────────────────────────────────────┐   │
 │   │                   AppTracker Browser RAM Store (LRU ramMap)                    │   │
 │   │                   - Max 30 entries & Max 20MB Memory Cap                       │   │
 │   │                   - Key: tenant:job:dataset_v:page:filters:sort                │   │
 │   │                   - Sub-millisecond tab switching                              │   │
 │   └──────────────────────────────────────┬─────────────────────────────────────────┘   │
 │                                          ▼                                             │
 │   ┌────────────────────────────────────────────────────────────────────────────────┐   │
 │   │                      Authoritative ES Read & Highlight                         │   │
 │   │           (Candidate rendered < 500ms; CSS pulse highlight on row)             │   │
 │   └────────────────────────────────────────────────────────────────────────────────┘   │
 │                                APPTRACKER BROWSER CLIENT                               │
 └────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 7. T0–T11 LATENCY & OBSERVABILITY FRAMEWORK

```text
 T0  Submit button clicked (Client JS mark)
 T1  MySQL transaction committed (PHP microtime)
 T2  Outbox event created (PHP microtime)
 T3  Horizon worker picks up job (Job handle start)
 T4  Elasticsearch indexing initiated (GeneralMethod start)
 T5  Elasticsearch search-ready confirmed (refresh=true executed)
 T6  Job dataset version incremented in Redis (Captured INCR return value)
 T7  Reverb WebSocket event broadcasted (Event dispatch)
 T8  Browser Echo client receives WebSocket packet
 T9  AppTracker initiates authoritative ES query
 T10 AppTracker receives ES JSON payload
 T11 DOM render complete & candidate highlighted
```

Calculated Telemetry Metrics:
- **`DB_Commit_Latency`:** `T1 - T0`
- **`Outbox_Queue_Latency`:** `T3 - T2`
- **`ES_Index_Latency`:** `T5 - T4`
- **`WebSocket_Transit_Latency`:** `T8 - T7`
- **`AppTracker_Render_Latency`:** `T11 - T9`
- **`Total_Visibility_Latency`:** `T11 - T0`

---

## 8. DETAILED COMPONENT IMPLEMENTATION SPECIFICATION

### Step 1: Sanitize `AppTrackerRamStore` (Pure RAM LRU, Zero Persistent HTML/JSON/IndexedDB)
- **Target File:** `resources/views/service_company/apptracker/index.blade.php`
- **Specification:**
  1. **Strict Storage Ban:** Completely eliminate all `sessionStorage`, `localStorage`, and `IndexedDB` reads and writes for candidate tables and records.
  2. **Bounded LRU RAM Store:** `ramMap` holds a maximum of 30 entries and operates under a 20MB ceiling.
  3. **Composite Key:** `tenant:job:dataset_version:page:filters:sort`. Version change automatically invalidates prior entries.
  4. **Cold Starts:** On any browser refresh (F5), redirect, or initial entry, `ramMap` starts empty, guaranteeing an authoritative Elasticsearch read.

### Step 2: Establish the Two-Tier Monotonic Dataset Version Contract
- **Target Files:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
  - `resources/views/service_company/apptracker/index.blade.php`
- **Specification:**
  1. In `ServiceCompanyAppTrackerController::index()`:
     ```php
     $jobId = (int) GeneralMethod::value_decrypt($id);
     $jobDatasetVersion = 1;
     try {
         $jobDatasetVersion = (int) cache()->get("ats:version:job:{$company_id}:{$jobId}", 1);
     } catch (\Throwable) {}
     $this->job_dataset_version = $jobDatasetVersion;
     ```
  2. In `ServiceCompanyAppTrackerController::allDataAjax()`:
     Return `'job_dataset_version' => $jobDatasetVersion` in the JSON response payload.
  3. In `index.blade.php`:
     Render `<input type="hidden" id="apptracker_job_dataset_version" value="{{ $job_dataset_version }}">` and bind `var currentJobDatasetVersion = parseInt($('#apptracker_job_dataset_version').val() || 1, 10);`.

### Step 3: Readiness Gating with Bounded Timeout in Add Candidate
- **Target Files:**
  - `resources/views/service_company/job-applications/create.blade.php`
  - `resources/views/service_company/job-applications/clientcreate.blade.php`
- **Specification:**
  1. **Gating:** Keep the "Go to Tracker Now" button disabled while `pollSearchIndex()` is in-flight.
  2. **Bounded Terminal State:**
     - If `res && res.indexed === true`: State transitions to `READY`, SweetAlert confirms "Candidate Ready!", and redirects after 500ms.
     - If elapsed time reaches **10 seconds** without ES confirmation: Transition modal to:
       *"Candidate saved successfully. Search index is updating in the background."*  
       Unlock the "Proceed to Tracker" button so recruiter workflow is never blocked.
  3. **URL Navigation Intent:** In `executeSaveRedirect()`, redirect with:
     ```javascript
     var targetUrl = response.url + (response.url.indexOf('?') > -1 ? '&' : '?') + 'new_candidate_id=' + candidateId;
     window.location.assign(targetUrl);
     ```

### Step 4: Active Consumption of `new_candidate_id` & Non-Authoritative Row Pulse
- **Target File:** `resources/views/service_company/apptracker/index.blade.php`
- **Specification:**
  1. On `$(document).ready()`:
     ```javascript
     var urlParams = new URLSearchParams(window.location.search);
     var newCandidateId = urlParams.get('new_candidate_id');
     if (newCandidateId) {
         fetchCandidates(1, '', true).then(function() {
             var $targetRow = $('tr[data-candidate-id="' + newCandidateId + '"]');
             if ($targetRow.length) {
                 $targetRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                 $targetRow.addClass('bg-light-success border-success');
                 setTimeout(function() {
                     $targetRow.removeClass('bg-light-success border-success');
                 }, 3500);
             } else {
                 // Post-Timeout Bounded Reconciliation (1s, 2s, 4s, stop)
                 runBoundedCandidateRecovery(newCandidateId, 1, [1000, 2000, 4000]);
             }
         });
         urlParams.delete('new_candidate_id');
         var cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
         window.history.replaceState({}, document.title, cleanUrl);
     }
     ```

### Step 5: Atomic Version INCR, Out-of-Order Reverb Invariant & Non-Blocking Fallback
- **Target Files:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
  - `resources/views/service_company/apptracker/index.blade.php`
- **Specification:**
  1. In `store()` and `clientappstore()`, resilient non-blocking atomic execution:
     ```php
     try {
         $jobDatasetVersion = (int) cache()->increment("ats:version:job:{$company_id}:{$rawJobId}");
         AppTrackerCandidateAssignedEvent::dispatch(
             (int) $company_id,
             (int) $rawJobId,
             $jobDatasetVersion,
             (int) $jobApplication->id,
             (int) $wiseCandidateId,
             (int) ($jobApplication->status_id ?: 8)
         );
     } catch (\Throwable $redisEx) {
         \Log::warning('AppTracker Redis version increment/broadcast resilient fallback: '.$redisEx->getMessage());
     }
     ```
  2. In `index.blade.php`, the Echo listener enforces the **Strict Monotonic Invariant**:
     ```javascript
     window.Echo.private(channelName)
         .listen('.candidate.assigned', function(event) {
             if (!event || !event.version) return;
             var incomingVer = parseInt(event.version, 10);

             // REJECT OUT-OF-ORDER OR DUPLICATE PACKETS
             if (incomingVer <= currentJobDatasetVersion) {
                 return;
             }

             currentJobDatasetVersion = incomingVer;
             if (typeof AppTrackerRamStore !== 'undefined') {
                 AppTrackerRamStore.clear(); // Clear RAM cache for this job
             }

             // Invalidate counters in 0ms and trigger active stage re-fetch if on Page 1
             handleIncomingCandidateAssignment(event);
         });
     ```
  3. **Throttled Focus Reconciliation:**
     ```javascript
     var _lastReconcileTs = 0;
     window.addEventListener('focus', function() {
         var now = Date.now();
         if (now - _lastReconcileTs > 15000) { // 15s debounce
             _lastReconcileTs = now;
             if (typeof countder === 'function') countder();
         }
     });
     ```

---

## 9. THE 18-SCENARIO TESTING & VERIFICATION MATRIX

| ID | Scenario | Execution Steps | Acceptance Criteria |
|:---:|---|---|---|
| **A** | **Standard Submit** | Submit candidate -> Redirect to AppTracker | Candidate visible on Page 1 in `< 500ms` |
| **B** | **5s Fast Submit** | Submit form within 5 seconds | Candidate visible immediately |
| **C** | **30s Submit** | Submit form within 30 seconds | Candidate visible immediately |
| **D** | **59s Submit** | Submit form within 59 seconds | Candidate visible (no 60s TTL bug) |
| **E** | **61s Submit** | Submit form within 61 seconds | Candidate visible (no 60s TTL bug) |
| **F** | **Instant F5 Refresh** | Land on AppTracker -> Press F5 within 1s | Candidate still rendered |
| **G** | **Multi-Tab User** | Tab A: AppTracker, Tab B: Add Candidate -> Submit | Tab A counters update via Reverb in `< 100ms` |
| **H** | **Multi-Recruiter** | Recruiter 1 submits candidate -> Recruiter 2 on Job 1947 | Recruiter 2 counters update; view protected |
| **I** | **Cross-Tenant** | Tenant 2 creates candidate | Tenant 3 is 100% unaffected (0 leakage) |
| **J** | **Offline Reconnect** | Drop WiFi, submit on another device, reconnect WiFi | Reconnect handler reconciles in `< 1s` |
| **K** | **Double Submit** | Rapid double-click on "Submit Candidate" button | Exactly 1 candidate created (HTTP 409/replay) |
| **L** | **History Traversal** | AppTracker -> Add Candidate -> Submit -> AppTracker -> Back -> Forward | Fresh data rendered; zero zombie HTML/JSON |
| **M** | **Missed Reverb** | Disconnect WebSocket -> Submit -> Reconnect | Version reconciliation recovers list |
| **N** | **Delayed ES Index** | Simulate 5s ES queue lag | UI indicates indexing; no false errors |
| **O** | **Concurrent Candidates** | Recruiter 1 adds Cand X, Recruiter 2 adds Cand Y | Both candidates visible; zero version races |
| **P** | **Browser Crash / Restore** | Submit -> kill browser tab -> reopen AppTracker | Fresh ES read; candidate visible; 0 zombie cache |
| **Q** | **ES Cluster Failure** | MySQL commits -> simulate ES failure | Candidate safe in DB; outbox retry; 0 lost data |
| **R** | **Redis Version Failure** | ES indexed -> Redis momentarily unavailable | Candidate remains searchable; fallback recovers |

---

## 10. CODE INTEGRITY & DIRECTIVE COMPLIANCE

1. **Rule 18 / Directive 12 (Freeze Directive):** Zero modifications to `resources/views/product_company/` and `resources/views/staffing_company/`. Changes strictly confined to Service Company ATS and Admin.
2. **Rule 17 (Isolation Directive):** Changes to `apptracker/index.blade.php` and `create.blade.php` are strictly isolated to their own page scopes with zero shared stateful controllers.
3. **Rule 7 (Zero Business Logic Mutations):** Candidate status semantics, coin deductions, duplicate detection algorithms, and MySQL transactional boundaries remain 100% untouched.
4. **Current Status:** **ZERO CODE MODIFICATIONS COMMITTED.** All changes await explicit user approval.
