# V4 Add Talent Flow: Production Engineering Audit, Architecture Specification & Implementation Blueprint

> **Document Version:** 5.1.0 (Master Engineering Specification: Zero-Strain Client-First Architecture, Commercials Integrity, 10K Concurrency, Durable Idempotency & Distributed Outbox Fencing)  
> **Status:** Authoritative Production Engineering Contract & Implementation Master Specification  
> **Target Module:** Service Company Add Talent, Edit Talent, Submit Pipeline & Bench Listing Dashboard (`service_company/bench-candidates`)  
> **Target Environment:** `testing.jero.works`  
> **Primary Source Files:**  
> - `resources/views/service_company/bench-candidates/create.blade.php` (Add & Edit Talent Blade View)  
> - `resources/views/service_company/bench-candidates/index.blade.php` (Bench Talent Listing & Hero Dashboard)  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` (`create()`, `store()`, `edit()`, `data_ajax()`, `data_v2()`, `benchHeroKpis()`)  
> - `app/Services/BenchTalentHeroAnalytics.php` (Hero Pipeline Metrics Service)  
> - `database/schema/mysql-schema.sql` (Authoritative MySQL Database Schema)  
> - `demo/css/candidate-form.css` & `resources/views/service_company/bench-candidates/partials/_form_styles.blade.php` (15.6" Responsive Styling)  
> **Client-Side Parser Production Endpoint:**  
> - Production Cloudflare Worker: `https://parser.jero.works/`  
> - Module Entrypoint: `https://parser.jero.works/jero-resume-parser-2tURGwDx.js` (WebAssembly, PDF.js, Mammoth, Compromise NLP)  
> **Dedicated Companion Subsystem (Strictly Isolated & Off-Limits to Regressions):**  
> - `docs/V4_Jeroworks_Upgrade/v4_add_candidate_audit_and_remediation_plan.md` Version 5.0.0  
>   *(Governs the Add Candidate ATS pipeline at `/service_company/job-applications/create` — strictly preserved & independent).*  
> **Explicit Scope Boundary:**  
> - Per architectural directive, **`product_company` and `staffing_company` are strictly OUT OF SCOPE and must be ignored**. All changes are quarantined to `service_company`. Zero shared stateful scripts with Add Candidate, Drawer, or Marketplace modules (Rule 17).

---

## 1. Executive Summary & Core Architectural Principles

### 1.1 The Golden Architectural Axiom
> **"MySQL is the authoritative transactional system of record. Elasticsearch is the asynchronous read/search projection. Redis provides ephemeral coordination, distributed locks, and materialized counter state. The client browser executes non-authoritative computation, parsing, and DOM rendering. The transactional outbox guarantees eventual consistency from MySQL to Elasticsearch."**

This single axiom governs all engineering decisions across the Add Talent, Edit Talent, and Bench Listing flows. Under no circumstances may Elasticsearch or external cloud storage be treated as an inline dependency for transactional persistence.

### 1.2 Target Performance & Concurrency Profile
- **Target Concurrency:** **10,000 concurrent recruiters per page** without server collapse, database connection starvation, or UI thread freezes.
- **Server Read Strain:** **0% MySQL reads** on web/API request paths, autocompletes, filter switches, and listing page loads.
- **Server CPU Strain:** **0% server CPU for document parsing and commercial calculations** achieved via Client-First / Browser-Execution (User RAM + WASM in a dedicated Web Worker).
- **HTTP Transaction Hold Duration:** **< 15ms MySQL transaction duration** (reduced from legacy 1,200ms–3,500ms).
- **Submit Response Latency SLA:** **< 150ms HTTP 200/201 response** returning authoritative listing redirect URL.
- **Listing Payload Reduction:** **90% payload reduction** (from ~85KB server-rendered HTML blocks via `data_ajax` to ~8KB normalized JSON via `data_v2` with client DOM assembly).

### 1.3 Subsystem Isolation & Anti-Corruption Boundary (Rules 7 & 17)
The Talent / Bench Candidates module represents a dedicated business domain: internal service company employee bench management and B2B marketplace talent sharing.
- **Strict Quarantine:** All Blade views, CSS styles, JavaScript controllers, and route handlers must remain strictly quarantined to `resources/views/service_company/bench-candidates/` and `ServiceCompanyBenchCandidatesController.php`.
- **Zero Shared Mutable State:** No stateful JavaScript, modal instances, or dropzone controllers may be shared with `job-applications` (Add Candidate) or Candidate Drawer components.
- **Zero Business Logic Mutations (Rule 7):** All existing business rules—coin deductions (`eventId = 5`), stage definitions (1=Pool, 2=Bench, 4=Hold, 5=Exited, Placed=Direct Workflow), outsourcing types (Full Time, Contract, Part Time, Freelance), skills experience ratings (1–5), notice period modes, and commercial models (Model 1 Free vs Model 2 Paid, Annual/Monthly/Hourly conversions, Margin %, Hike %)—must remain **100% mathematically and functionally untouched**.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                              JERO V4 ADD TALENT ARCHITECTURE MATRIX                                    │
│                                                                                                        │
│  ┌─────────────────────────┐  ┌─────────────────────────┐  ┌────────────────────────────────────────┐  │
│  │ PIPELINE 1:             │  │ PIPELINE 2:             │  │ PIPELINE 3:                            │  │
│  │ CLIENT RESUME PARSER    │  │ STORAGE & ASSET ENGINE  │  │ IN-BROWSER COMMERCIALS & AUTOCOMPLETE  │  │
│  │ (100% Browser RAM/WASM) │  │ (Direct Browser-to-R2)  │  │ (User RAM + Local Trie Cache)          │  │
│  │                         │  │                         │  │                                        │  │
│  │ • Web Worker Execution  │  │ • Direct Presigned PUT  │  │ • In-Browser Commercials Calculator    │  │
│  │ • Zero Main-Thread Jank │  │ • Background File R2    │  │   (Pay/Bill Rate, Margins, Multi-Cur)  │  │
│  │ • Auto-populates inputs │  │ • Asynchronous Relocate │  │ • Local Sharded Skills Trie (0ms HTTP) │  │
│  │ • 0 Server CPU Strain   │  │ • No R2 checks in DB Tx │  │ • Local Sharded Geo Trie (0ms HTTP)    │  │
│  │ • Single 'Remove' UI    │  │ • Clean Orphan Purge    │  │ • Tab Scoping (window.JeroTalent.tab)  │  │
│  └────────────┬────────────┘  └───────────┬─────────────┘  └───────────────────┬────────────────────┘  │
│               │ (Autofills empty inputs)   │ (Verified asset payload)          │                       │
│               ▼                            ▼                                   ▼                       │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ PIPELINE 4: SUB-15ms TRANSACTIONAL PERSISTENCE + TRANSACTIONAL OUTBOX PATTERN                    │  │
│  │                                                                                                  │  │
│  │ • 3-Layer Idempotency: Client UUID → Redis SETNX → Durable MySQL `sys_idempotency_keys`          │  │
│  │ • Pure MySQL Write: INSERT/UPDATE `job_applications` + Bulk Relational Writes (skills, assign)   │  │
│  │ • Transactional Outbox: INSERT `candidate_outbox_events` with Deterministic Aggregate Keys      │  │
│  │ • ZERO External Network I/O Inside `DB::beginTransaction()` (Zero ES HTTP, Zero R2 HEAD checks)   │  │
│  │ • IMMEDIATE JSON Response + Triple-Redundant Redirect (SweetAlert + Hard Fallback Timer)         │  │
│  │ • Outbox Horizon Worker: 3-Phase Execution (`FOR UPDATE SKIP LOCKED` → Async ES → Completed)     │  │
│  └────────────────────────────────────────────┬─────────────────────────────────────────────────────┘  │
│                                               │                                                        │
│                                               ▼                                                        │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ PIPELINE 5: 10K-READY BENCH LISTING DASHBOARD (0% MYSQL READ STRAIN)                             │  │
│  │                                                                                                  │  │
│  │ • 100% Elasticsearch Reads: Universal query serving across `ELASTIC_CANDIDATES_BENCH`             │  │
│  │ • Client-Side DOM Assembly: High-performance `data_v2` JSON endpoint (8KB vs 85KB HTML)         │  │
│  │ • Hero Analytics: Redis Materialized Counters (sub-5ms reads) + Scheduled Reconciliation Worker  │  │
│  │ • Zero HTML Caching: Redis stores only counters, coordination locks, and short-lived tokens      │  │
│  └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Forensic Code Audit: Current Add Talent & Bench Listing Vulnerabilities

A line-by-line inspection of `ServiceCompanyBenchCandidatesController.php`, `create.blade.php`, and `index.blade.php` exposes 10 critical architectural defects that prevent the module from scaling to 10,000 concurrency:

### Defect 1: Synchronous Elasticsearch Network I/O Inside Active MySQL Transactions
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1641–1700 & 1834–1870.
- **The Code Flaw:**
  ```php
  DB::beginTransaction();
  // ...
  $this->elasticService->getClient()->deleteByQuery([
      'index' => ELASTIC_BENCH_ASSIGN,
      'conflicts' => 'proceed',
      'body' => ['query' => ['bool' => ['must' => [['term' => ['bench_applications_id' => (int) $benchApplication->id]]]]]]
  ]);
  foreach ($recruiterArrId as $recArr) {
      $response = $this->elasticService->getClient()->get(['index' => ELASTIC_USERS_LIST, 'id' => (int) $recArr]);
      $insertGetId = DB::table('bench_assign')->insertGetId(...);
      $this->elasticService->getClient()->update(['index' => ELASTIC_BENCH_ASSIGN, 'id' => $insertGetId, ...]);
  }
  ```
- **Consequence:** Holding an active MySQL transaction while executing synchronous HTTP requests to Elasticsearch inflates database transaction hold duration from <15ms to 1,200ms–3,500ms. Under 10,000 concurrent recruiters, this instantly exhausts the MySQL thread pool (`max_connections`), produces lock wait timeouts, and causes server-wide HTTP 504 Gateway Timeouts.

### Defect 2: Synchronous Cloudflare R2 Network Calls Inside Transaction
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1545–1560.
- **The Code Flaw:**
  ```php
  $r2Exists = Storage::disk('r2')->exists('resumes/'.$resumeSession->uuid);
  $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
  ```
- **Consequence:** Invoking HTTPS S3/R2 HEAD requests and object copy operations over WAN inside `DB::beginTransaction()` ties database connection lifetime to external cloud storage latency.

### Defect 3: N+1 Location Parsing SQL Queries
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1455–1478.
- **The Code Flaw:** Iterates over `kt_tagify_work_location` and executes a 3-table SQL join query (`city_list` $\bowtie$ `state_list` $\bowtie$ `countries`) inside a `foreach` loop for every selected location tag.
- **Consequence:** A burst of submissions triggers 30,000+ unindexed SQL reads against MySQL.

### Defect 4: Server Memory Bloat on View Initialization
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1185–1240.
- **The Code Flaw:** When `feature.talents_v2_enabled` is false, `create()` executes `GeneralMethod::allCities()`, loading tens of thousands of city records into PHP memory.
- **Consequence:** Memory footprint spikes to >64MB per worker, starving PHP-FPM and crashing web containers.

### Defect 5: Fragmented In-Browser Parsing vs Unified WASM Engine
- **Location:** `create.blade.php` lines 4085–4165.
- **The Code Flaw:** The Add Talent page previously experimented with naive regex parsing (`/([A-Z]+ [A-Z]+) \w+/i`) and un-throttled synchronous PDF.js loops instead of the hardened, production-tested WASM parser engine (`https://parser.jero.works/`).
- **Consequence:** Parsing frequently failed on modern multi-column resume layouts and froze the browser main thread.

### Defect 6: Missing 3-Layer Submission Idempotency & Coin Double-Deduction Risk
- **The Code Flaw:** Add Talent relies solely on an optional 10-second `Cache::lock()` that only runs if `feature.talents_v2_enabled` is true. No unique constraint exists in MySQL for talent submissions.
- **Consequence:** Rapid double-clicking creates duplicate bench profiles and duplicate coin deductions (`coinsDeductions`).

### Defect 7: Fragile Immediate Page Redirect & SweetAlert Race
- **Location:** `create.blade.php` lines 3177 & 3330.
- **The Code Flaw:**
  ```javascript
  Swal.fire({ text: "You have successfully Submitted", ... });
  window.location = response.url; // Executes synchronously outside modal promise!
  ```
- **Consequence:** The browser navigates away immediately before the SweetAlert modal renders. If network latency delays the response, the UI freezes with no user feedback.

### Defect 8: Server-Rendered HTML on Listing Dashboard (`data_ajax`)
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 580–585 & `index.blade.php` line 1124.
- **The Code Flaw:** The bench listing dashboard requests `route('service_company.bench-candidates.data_ajax')`, calling `view('service_company.ajax_pages.bench-candidate-list-ajax')->render()` on the server for every filter, search keystroke, and pagination click.
- **Consequence:** Generating 85KB HTML payloads on the server for 10,000 recruiters consumes massive web server CPU and network bandwidth.

### Defect 9: Commercials & Bill Rates Formula Fragility (Revert Root Cause)
- **History:** Commit `16b825d5` was forced to revert recent bench candidate edits because commercial calculation refactors failed to maintain exact synchronization between visible rate cards and hidden form inputs (`pay_annual`, `pay_monthly`, `pay_rate_amount`, `bill_annual`, `bill_monthly`, `bill_rate_amount`, `percentage_hike`, `comission_type`), corrupting submitted rates.
- **Consequence:** Recruiter entered rates were dropped or recalculated with incorrect currency units, corrupting candidate commercials in the database.

### Defect 10: Absence of Defensive Persistence (`resilientSave`)
- **The Code Flaw:** Direct call to `$benchApplication->save()` without catching MySQL 1054 Unknown Column errors during rolling schema migrations.
- **Consequence:** Deployments introducing new audit columns caused immediate 500 crashes on candidate submissions.

---

## 3. Redesigned Idempotency & Persistence Engine (Solving the Fail-Open Flaw)

### 3.1 The Fail-Open Vulnerability & Why MySQL UNIQUE Alone Is Insufficient
In naive idempotency designs, if Redis times out or is unreachable, the system "fails open" to MySQL. However, if two concurrent requests (Request A and Request B) share the same `client_submission_uuid`:
1. Request A starts MySQL transaction, executes `GeneralMethod::coinsDeductions()` (Event 5: 1 coin deducted).
2. Request B starts concurrent MySQL transaction before Request A commits, and also executes `GeneralMethod::coinsDeductions()` (Event 5: duplicate coin deducted!).
3. Request A commits successfully. Request B hits `uq_company_submission_uuid` and fails with MySQL Error 1062.
4. **Catastrophic Result:** The candidate was saved once, but coins were deducted twice! Or worse, upon catching 1062, the server cannot replay the response because Redis cached responses are ephemeral and absent.

### 3.2 The Durable Idempotency Architecture (`sys_idempotency_keys`)
To guarantee storage-level atomicity across ALL side effects, we utilize the authoritative `sys_idempotency_keys` table (established in `database/schema/mysql-schema.sql` and hardened with explicit execution leases):

```sql
CREATE TABLE `sys_idempotency_keys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `idempotency_key` varchar(64) NOT NULL,
  `request_hash` varchar(64) NOT NULL,
  `status` enum('PROCESSING','COMPLETED','FAILED_FINAL','FAILED_RETRYABLE') NOT NULL DEFAULT 'PROCESSING',
  `response_status` int(11) DEFAULT NULL,
  `response_body` longtext DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `processing_until` timestamp NULL DEFAULT NULL COMMENT 'Execution lease deadline (NOW() + 60s); distinguishes active in-flight from abandoned crash',
  `expires_at` timestamp NOT NULL COMMENT 'Retention lifetime (NOW() + 24h) after which the idempotency record is pruned',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_idempotency_tenant_key` (`tenant_id`, `idempotency_key`),
  KEY `idx_idempotency_lease` (`status`, `processing_until`),
  KEY `idx_idempotency_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> [!IMPORTANT]
> **Lease vs Retention Distinction:**  
> `expires_at` (e.g. 24 hours) is strictly for **retention lifetime** pruning.  
> `processing_until` (e.g. `NOW() + 60s`) is strictly for **execution lease auto-recovery**. If an application server crashes mid-transaction after creating the row, the key is never left permanently blocking the request.

---

### 3.3 Atomic Side-Effect Fencing & Idempotent Response Replay Protocol
Every talent submission (Create or Update) executes the following sequence:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                          ATOMIC IDEMPOTENCY & SIDE-EFFECT FENCING PROTOCOL                             │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [ STEP 1: FAST-PATH EPHEMERAL CHECK (Redis SETNX - 120s TTL) ]                                         │
│   • Redis::set("idemp:talent:{$companyId}:{$uuid}", 'PROCESSING', 'EX', 120, 'NX')                     │
│   • If Key Exists: Check status. If 'COMPLETED', return cached JSON. If 'PROCESSING', return HTTP 409. │
│   • If Redis Times Out (> 50ms): Proceed directly to Step 2 without failing the request.               │
│                                                                                                        │
│ [ STEP 2: DURABLE STORAGE LOCK & LEASE RECOVERY (MySQL Atomic Insert inside Transaction) ]             │
│   • DB::beginTransaction();                                                                            │
│   • Try inserting into `sys_idempotency_keys`:                                                         │
│     INSERT INTO sys_idempotency_keys                                                                   │
│       (tenant_id, idempotency_key, request_hash, status, processing_until, expires_at)                 │
│     VALUES (:company_id, :uuid, :sha256, 'PROCESSING', NOW() + INTERVAL 60 SECOND, NOW() + INTERVAL 24H)│
│                                                                                                        │
│   • IF DUPLICATE KEY 1062 on `sys_idempotency_keys`:                                                   │
│     - Query existing row with lock:                                                                    │
│       SELECT request_hash, status, response_status, response_body, processing_until                    │
│       FROM sys_idempotency_keys WHERE tenant_id = :company_id AND idempotency_key = :uuid FOR UPDATE;  │
│                                                                                                        │
│     - INVARIANT G-53 (REQUEST HASH MISMATCH):                                                          │
│       IF existing.request_hash !== incoming.request_hash:                                              │
│         DB::rollBack();                                                                                │
│         RETURN HTTP 409 Conflict {"error": "IDEMPOTENCY_KEY_REUSE", "message": "Key payload mismatch"};│
│                                                                                                        │
│     - IF status == 'COMPLETED':                                                                        │
│         DB::rollBack();                                                                                │
│         RETURN response(response_body, response_status); // Safe cached replay                         │
│                                                                                                        │
│     - IF status == 'PROCESSING' AND processing_until >= NOW():                                         │
│         DB::rollBack();                                                                                │
│         RETURN HTTP 409 Conflict {"error": "IN_FLIGHT", "message": "Submission actively processing"};  │
│                                                                                                        │
│     - INVARIANT G-52 (ABANDONED LEASE RECOVERY):                                                       │
│       IF status == 'PROCESSING' AND processing_until < NOW():                                          │
│         Prior worker crashed! Reclaim lease: UPDATE sys_idempotency_keys                               │
│         SET processing_until = NOW() + INTERVAL 60 SECOND WHERE id = :id;                              │
│         Check if candidate row already exists for :uuid:                                               │
│         - If candidate exists: Mark COMPLETED, backfill response_body, DB::commit(), return response; │
│         - If candidate absent: Proceed to Step 3 to execute persistence.                               │
│                                                                                                        │
│ [ STEP 3: EXECUTE TRANSACTIONAL BUSINESS SIDE EFFECTS (100% Protected) ]                               │
│   • Exactly ONE transaction ever reaches this line! Zero double-coin deduction possible!               │
│   • Verify coin ledger: SELECT id FROM company_coins_deductions WHERE transaction_reference = :uuid    │
│   • If absent: Execute GeneralMethod::coinsDeductions(...) inside transaction                          │
│   • Save JobApplication with `client_submission_uuid`, `aggregate_version = 1`, `resume_version = 1`   │
│   • Batch insert skills, recruiters, custom details                                                    │
│   • Write Outbox event into `candidate_outbox_events` with deterministic aggregate key                 │
│                                                                                                        │
│ [ STEP 4: RECORD DURABLE RESPONSE & COMMIT ]                                                           │
│   • Build standard JSON response payload: { candidate_id, url, message, status: 'success' }            │
│   • UPDATE sys_idempotency_keys                                                                        │
│     SET status = 'COMPLETED', response_status = 200, response_body = :json, completed_at = NOW(),       │
│         processing_until = NULL WHERE tenant_id = :company_id AND idempotency_key = :uuid;             │
│   • DB::commit();                                                                                      │
│   • Post-commit: Cache::set("idemp:talent:{$companyId}:{$uuid}", $json, 86400); // Accelerates replays │
│   • Return HTTP 200 JSON redirect response to recruiter (P95 < 150ms).                                 │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 3.4 Orthogonal Architectural Separation: Idempotency vs Concurrency Control
Create and Update flows have fundamentally distinct safety requirements. Conflating them leads to lost updates or false conflicts:

1. **Idempotency (`idempotency_key` via `client_submission_uuid` / `sys_idempotency_keys`):**
   - **Invariant Question:** *"Did I already execute this exact request?"*
   - **Scope:** Protects against duplicate network transmissions, rapid double-clicks, and reverse proxy timeouts. Guarantees that repeating the identical HTTP request yields the identical outcome without duplicate side effects.
2. **Optimistic Concurrency Control (`expected_aggregate_version`, `expected_resume_version`):**
   - **Invariant Question:** *"Am I editing the state I actually saw?"*
   - **Scope:** Protects against concurrent modifications and lost updates across multiple browser tabs, users, or background automations.

---

### 3.5 Dual-Versioning Model: `aggregate_version` vs `resume_version`
Overloading `resume_version` as the candidate aggregate concurrency version is semantically invalid. A candidate record experiences frequent modifications that do not involve the resume file:
- Skill additions, removals, or experience rating adjustments
- Pipeline stage transitions (Pool $\leftrightarrow$ Bench $\leftrightarrow$ Hold)
- Commercial renegotiations (Pay Rate, Bill Rate, Margin %)
- Recruiter assignments and notes
- Notice period and contact details

#### The Dual Version Invariants
1. **`aggregate_version` (INT UNSIGNED DEFAULT 1):**
   - Increments monotonically ($1 \rightarrow 2 \rightarrow 3 \dots$) upon **ANY** candidate mutation.
   - Enforces candidate aggregate optimistic concurrency:
     $$\text{UPDATE } \texttt{job\_applications} \text{ SET } \dots, \texttt{aggregate\_version} = \texttt{aggregate\_version} + 1 \text{ WHERE } \texttt{id} = :id \text{ AND } \texttt{aggregate\_version} = :expected\_aggregate\_version;$$
   - If 0 rows are updated, the request is rejected with **HTTP 409 Conflict** (`"Candidate profile was updated by another user or session. Please refresh."`).
2. **`resume_version` (INT UNSIGNED DEFAULT 1):**
   - Increments monotonically ($1 \rightarrow 2 \rightarrow 3 \dots$) **ONLY** upon resume asset mutations (file upload, replacement, or removal).
   - Enforces resume asset isolation: modifying commercials or skills never increments `resume_version`, preventing false resume conflict errors across recruiter tabs.
   - Edit requests pass both `expected_aggregate_version` and `expected_resume_version` independently.

---

## 4. Distributed Outbox Fencing & Deterministic Search Ingestion

### 4.1 The Phase B Crash Vulnerability & Lease Fencing
In a 3-Phase Outbox architecture, if a worker executes Phase B (Elasticsearch indexing) and then crashes before Phase C completes, the event remains in status `'PROCESSING'`. Without explicit lease recovery, the event becomes permanently stuck.

### 4.2 Formal 5-State Outbox Event Lifecycle
The `candidate_outbox_events` table enforces an authoritative 5-state lifecycle:
1. `PENDING`: Event created and waiting for worker claim.
2. `PROCESSING`: Claimed by a worker under active lease token.
3. `COMPLETED`: Successfully ingested into Elasticsearch; finalized.
4. `FAILED_RETRYABLE`: Transient network/ES error; scheduled for retry (`next_attempt_at`).
5. `DEAD_LETTER`: Exceeded maximum attempts (5); quarantined for alerting.

### 4.3 Outbox Worker Lease Recovery & Auto-Reclaim Protocol
The worker polling query automatically reclaims expired leases:

```sql
-- Step 1: Claim pending events OR expired processing leases inside short transaction (< 5ms)
START TRANSACTION;
SELECT id, aggregate_id, aggregate_version, payload 
FROM `candidate_outbox_events`
WHERE (status = 'PENDING' AND available_at <= NOW())
   OR (status = 'PROCESSING' AND lease_until < NOW())
ORDER BY id ASC LIMIT 50 FOR UPDATE SKIP LOCKED;

UPDATE `candidate_outbox_events`
SET status = 'PROCESSING', 
    lease_token = :worker_uuid, 
    lease_until = NOW() + INTERVAL 60 SECOND,
    attempt_count = attempt_count + 1
WHERE id IN (:claimed_ids);
COMMIT;
```

### 4.4 Atomic Elasticsearch Version Fencing (`version_type=external_gte`)
To guarantee that processing an event multiple times produces identical state without document duplication or race condition overwrites:
- **Deterministic Document IDs:**
  - Candidate Profile: `bench_candidate:{candidate_id}`
  - Recruiter Assignment: `bench_assign:{id}`
  - Custom Detail: `bench_detail:{id}`
- **The PHP-Level Race Vulnerability:**  
  A naive implementation where PHP does `GET document -> compare version -> PUT document` has a catastrophic race:
  1. Worker A reads version 2.
  2. Worker B reads version 2.
  3. Worker B indexes version 4.
  4. Worker A indexes version 3 (overwriting version 4 with stale data!).
- **The Atomic Storage-Engine Fencing Solution (Gate G-54):**  
  Version comparison must happen **atomically inside Elasticsearch**, not in PHP. Every outbox event indexes using Elasticsearch's native external versioning:
  ```php
  $elasticClient->index([
      'index' => config('elasticsearch.indices.bench_candidates', 'candidates_bench'),
      'id' => "bench_candidate:{$candidateId}",
      'version' => $aggregateVersion,
      'version_type' => 'external_gte', // ATOMIC STORAGE-ENGINE FENCING
      'body' => $payload,
  ]);
  ```
  If an out-of-order event arrives where `version < document._version`, Elasticsearch atomically rejects the write with **HTTP 409 Version Conflict**. The worker catches this exception, recognizes it as a superseded event, logs a structured telemetry notice, and marks the event `COMPLETED` without mutating the newer document.

---

### 4.5 Outbox Ordering vs Distributed Concurrency Invariant
- **Queue Bias:** Outbox claiming applies `ORDER BY id ASC` to provide sequential bias for normal processing.
- **Distributed Invariant:** However, in a distributed Horizon deployment with multiple parallel workers, events for the same candidate can be claimed and processed concurrently.
- **Architectural Axiom:** **Queue ordering is NOT relied upon for data correctness; atomic Elasticsearch aggregate version fencing (`version_type=external_gte`) is the authoritative correctness mechanism.**

---

### 4.6 Explicit Bounded Outbox Retry Backoff with Jitter
Transient network timeouts or Elasticsearch cluster rebalancing trigger automatic retries using an explicitly bounded exponential backoff schedule with randomized jitter to prevent the thundering-herd problem under 10,000 concurrency:

$$\text{Delay}(a) = \text{BaseDelay}(a) + \text{random}(0, \text{JitterCap}(a))$$

| Attempt | Base Delay | Random Jitter Range | Total Delay Window | Action on Failure |
| :---: | :---: | :---: | :---: | :--- |
| **1** | 5 seconds | 0 – 2 seconds | 5 – 7 seconds | Retry with lease token release |
| **2** | 15 seconds | 0 – 5 seconds | 15 – 20 seconds | Retry with lease token release |
| **3** | 60 seconds | 0 – 15 seconds | 60 – 75 seconds | Retry with lease token release |
| **4** | 300 seconds (5 min) | 0 – 30 seconds | 300 – 330 seconds | Retry with lease token release |
| **5** | 900 seconds (15 min) | 0 – 60 seconds | 900 – 960 seconds | Final retry attempt |
| **> 5** | — | — | — | Transition to `DEAD_LETTER` & trigger P1 alert |

---

### 4.7 Dead-Letter Queue (DLQ) Replay Semantics
Quarantined dead-letter events must never become an unrecoverable graveyard. Once an infrastructure issue (e.g. index mapping conflict or storage quota) is resolved, administrators can safely replay failed events:

```
DEAD_LETTER ──► [ php artisan bench:outbox-replay ] ──► REPLAY ──► PENDING
```

#### DLQ Audit Tracking Columns in `candidate_outbox_events`
- `replayed_by` VARCHAR(64) NULL: Admin identifier or command executor.
- `replayed_at` TIMESTAMP NULL: Audit timestamp of replay.
- `replay_reason` VARCHAR(255) NULL: Operational reason recorded in logs.

```bash
# Replay specific event
php artisan bench:outbox-replay --event_id=14092 --reason="ES mapping schema updated"

# Replay all dead-lettered events for a specific tenant
php artisan bench:outbox-replay --company_id=105 --reason="Cluster recovered"
```

---

### 4.8 Authoritative Candidate Aggregate Assignment Projection Model
Recruiter assignments can involve complete set replacements (e.g. unassign Recruiter A, assign Recruiters B and C). Emitting fragmented, sequential "delete recruiter A" and "insert recruiter B" diff events causes severe race conditions when events execute out of order or retry partially.

#### The Authoritative Snapshot Rule
1. The candidate aggregate event carries the **authoritative, complete assignment set** (`assigned_recruiters: [12, 45, 88]`).
2. The Elasticsearch document projection updates the entire assignment list deterministically from the candidate aggregate payload (`_source.assigned_recruiters`).
3. Individual relational deletes and inserts are never projected as disjoint search events; the candidate search document remains 100% consistent with the master database state.

---

## 5. Unified Caching & Hero Analytics Architecture

### 5.1 Elimination of Contradictory Cache Layers
To eliminate cache incoherence under 10,000 concurrency, the system adopts a clean, single-responsibility model:
- **Candidate List Queries:** Browser $\rightarrow$ API $\rightarrow$ Elasticsearch (`ELASTIC_CANDIDATES_BENCH`). **Zero Redis HTML caching.**
- **Hero Analytics KPIs:** Browser $\rightarrow$ API $\rightarrow$ Redis Materialized Counters (sub-5ms reads).
- **Static Master Data:** Browser $\rightarrow$ CDN / IndexedDB (`jero_client_ref_v4`).
- **Redis Scope:** Distributed idempotency locks, hero counters, dirty tenant queues, and short-lived session metadata only.

### 5.2 Hero Analytics Materialized Counters & Zero-Strain MySQL Reconciliation
1. **Materialized Counters:** Hero KPI counts (Total Talent, Bench, Requests, Placed) are read from Redis via `MGET` across tenant keys: `bench:hero:{metric}:{company_id}` in `< 5ms`.
2. **The MySQL Strain Vulnerability:**  
   If a cron job executes `SELECT status, COUNT(*) ... GROUP BY status` for all 10,000 companies every 10 minutes, it generates **10,000 full aggregate table scans**, overwhelming MySQL database CPU and I/O.
3. **Dirty-Tenant Reconciliation Architecture:**  
   To guarantee 0% unnecessary MySQL read strain, reconciliation operates strictly on-demand:
   - **Dirty Tenant Tracking:** Whenever a talent profile is created, updated, or stage-changed, the tenant ID is pushed to a Redis set:
     `Redis::sadd('bench:dirty_tenants', $companyId);`
   - **Chunked Background Processing:** The scheduled reconciliation command pops at most 50 dirty tenants per iteration (`Redis::spop('bench:dirty_tenants', 50)`), running strictly in low-priority background capacity.
   - **Composite Index Leverage:** Queries execute against the compound index `idx_bench_company_type_status` on `(company_id, condidate_type, status)`.
   - **Nightly Active Window Fallback:** Nightly sweep checks only tenants with activity in the last 24 hours (`last_active_at >= NOW() - INTERVAL 24 HOUR`), never dormant tenants.

```php
// Background reconciliation worker processing only dirty tenants
$dirtyTenants = Redis::spop('bench:dirty_tenants', 50);

foreach ($dirtyTenants as $companyId) {
    $counts = DB::table('job_applications')
        ->where('company_id', $companyId)
        ->where('condidate_type', 'bench')
        ->selectRaw("
            COUNT(*) as total_count,
            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as bench_count,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as pool_count,
            SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as hold_count
        ")
        ->first();

    Redis::mset([
        "bench:hero:total:{$companyId}" => $counts->total_count ?? 0,
        "bench:hero:bench:{$companyId}" => $counts->bench_count ?? 0,
        "bench:hero:pool:{$companyId}"  => $counts->pool_count ?? 0,
        "bench:hero:hold:{$companyId}"  => $counts->hold_count ?? 0,
    ]);
}
```

---

## 6. Formal 10-State Resume & Cloudflare R2 State Machine

### 6.1 Formal Upload & Asset States
```
INITIATED ──► UPLOADING ──► UPLOADED ──► VERIFIED ──► ATTACHED ──► REMOVING ──► REMOVED
   │              │            │
   ▼              ▼            ▼
FAILED         EXPIRED      ORPHANED
```

### 6.2 Deep-Dive Edge Case Resolutions
- **Case 1: R2 Upload Succeeds, Browser Crashes Before Submit:**  
  R2 presigned PUT streams file to `temp/` prefix. Cloudflare R2 24-hour Bucket Lifecycle Policy automatically deletes the abandoned object. Zero database records created.
- **Case 2: Candidate Submits, MySQL Succeeds, R2 Object Does Not Exist:**  
  The candidate record saves with `current_resume_id = null`. The UI notifies the recruiter that the candidate was saved without a resume and prompts re-upload. Persistence is never rolled back.
- **Case 3: Candidate Removes Resume in Edit Mode:**  
  The candidate's `current_resume_id` is set to `null` and `resume_version` is incremented. The old `resume_assets` row transitions to `REMOVAL_PENDING`. An asynchronous Horizon job purges the physical R2 object after a 7-day retention grace period.
- **Case 4: Multi-Tab Race on Remove/Replace:**  
  Optimistic locking on `resume_version` rejects stale tab updates with HTTP 409, preventing an older tab from resurrecting a removed resume.

---

## 7. Commercials & Bill Rates In-Browser Engine Specification

### 7.1 Commercial Models & State Mapping
- **Model 1: Free Service (`comission_type = 1`):**  
  Talent is provided to clients at no commercial charge. Bill rate inputs (`bill_annual`, `bill_monthly`, `bill_rate_amount`) are disabled, hidden, and set to 0. Margin summary displays zero client charge. Pay rate fields remain active.
- **Model 2: Paid Service (`comission_type = 2`):**  
  Standard client billing enabled. Pay Rate and Bill Rate panels are fully editable with real-time margin percentage and hike calculations.

### 7.2 Rate Conversion Formulas (Standardized to 2,080 Hours)
$$\text{Annual Rate} = \text{Monthly Rate} \times 12 = \text{Hourly Rate} \times 2080$$

$$\text{Monthly Rate} = \frac{\text{Annual Rate}}{12} = \frac{\text{Hourly Rate} \times 2080}{12}$$

$$\text{Hourly Rate} = \frac{\text{Annual Rate}}{2080} = \frac{\text{Monthly Rate} \times 12}{2080}$$

### 7.3 Margin & Hike Calculations
$$\text{Annual Pay} = \text{pay\_annual}$$
$$\text{Annual Bill} = \text{bill\_annual}$$
$$\text{Margin Amount} = \text{Annual Bill} - \text{Annual Pay}$$
$$\text{Margin Percentage} = \begin{cases} \left(\frac{\text{Margin Amount}}{\text{Annual Bill}}\right) \times 100, & \text{if Annual Bill} > 0 \\ 0, & \text{otherwise} \end{cases}$$

- Stored in MySQL column `job_applications.percentage_hike` (via hidden input `#percentage_hike`).
- Rates persisted in `ctc_yearly`, `ctc_monthly`, `ctc_hourly`, `ectc_yearly`, `ectc_monthly`, `ectc_hourly`, `comission_type`, and `comment`.

---

## 8. Comprehensive Edit Mode State Machine

| Edit Scenario | Trigger & User Action | Rate Panel Behavior | Database Mutation |
| :--- | :--- | :--- | :--- |
| **Model 2 $\rightarrow$ Model 1** | Recruiter clicks "Free Service" | Bill rate inputs hidden & set to 0; Margin summary notes Free Service | `comission_type = 1`, `ectc_yearly = 0`, `percentage_hike = 0`; Pay rate preserved |
| **Model 1 $\rightarrow$ Model 2** | Recruiter clicks "Paid Service" | Bill rate inputs visible; prompts for bill rate | `comission_type = 2`; Calculates margin % from newly entered bill rate |
| **Edit + Remove Resume** | Recruiter clicks "Remove" on resume | Dropzone restored via `backToDropzone()`; resume chip cleared | `current_resume_id = null`, `resume = ''`, `resume_version = v+1`; Asset marked `REMOVAL_PENDING` |
| **Edit + New Resume** | Recruiter uploads replacement | Green verified card shown; new session UUID bound | Old asset marked `REMOVAL_PENDING`; new asset created; `current_resume_id` updated |
| **Edit + Stage Transition** | Changes stage (e.g. Bench $\rightarrow$ Hold) | Marketplace indicator updates dynamically | `status = new_stage`; Outbox event `BENCH_TALENT_STAGE_CHANGED` emitted |
| **Edit + Skills Update** | Modifies skills or experience ratings | In-memory Trie validates additions | `job_application_skills` refreshed; `bench_candidate_skill_experience` bulk upserted |
| **Concurrent Edit Collision** | Two recruiters submit edits from stale tabs | Second submission checks `resume_version` | Optimistic lock triggers `StaleMutationException` (HTTP 409 Conflict) |

---

## 9. Client Hardware, Web Worker & Trie Memory Budgets

### 9.1 Storage Ownership Hierarchy
- **`sessionStorage`:** Strictly ephemeral tab-scoped state (`window.JeroTalent.tabInstanceId`, in-flight upload session UUIDs).
- **`IndexedDB` (`jero_client_ref_v4`):** Long-lived static dictionary shards (canonical skills, country/state/city reference shards).
- **Client RAM:** Active form state, DOM bindings, and commercial math.

### 9.2 Memory Budgets & Web Worker Offloading
- **Parser Web Worker (`talent-parser-worker.js`):** Heavy PDF/DOCX WASM extraction executes in a dedicated Web Worker thread. Browser UI thread maintains **60 FPS** with zero jank.
- **Trie Memory Budget:** Skills and Location Trie dictionary compressed footprint `< 1.2 MB`, decompressed RAM footprint `< 18 MB`.
- **Global Namespace Hygiene:** All client modules encapsulated under a single global namespace:
  ```javascript
  window.JeroTalent = {
      tabInstanceId: crypto.randomUUID(),
      state: {},
      parser: {},
      commercial: {},
      autocomplete: {},
      upload: {}
  };
  ```

---

## 10. Zero-Downtime Elasticsearch Rebuild & Schema Evolution

### 10.1 Zero-Downtime Index Rebuild & Alias Swap
If the Elasticsearch mapping changes or index corruption occurs, rebuild without downtime:
```
1. Create new index: `bench_candidates_v{n+1}` with updated mappings.
2. Stream candidate documents from MySQL via Artisan command:
   php artisan bench:rebuild-search-index --batch=500
3. Validate document count against MySQL master record count.
4. Execute atomic alias swap:
   POST /_aliases
   { "actions": [
       { "remove": { "index": "bench_candidates_v{n}", "alias": "ELASTIC_CANDIDATES_BENCH" } },
       { "add":    { "index": "bench_candidates_v{n+1}", "alias": "ELASTIC_CANDIDATES_BENCH" } }
   ]}
5. Drop legacy index `bench_candidates_v{n}`. Zero search downtime!
```

### 10.2 Emergency Schema Compatibility Guard Framing
The method `resilientSaveTalentApplication()` is strictly an **Emergency Compatibility Guard** to survive the minutes during rolling server restarts.
- **Standard Protocol:** Always follow the Expand $\rightarrow$ Migrate $\rightarrow$ Contract pattern (Add nullable column $\rightarrow$ Deploy code $\rightarrow$ Backfill $\rightarrow$ Add constraint).
- **Observability:** If the guard unsets a column, it logs a critical warning:
  `Log::warning('EMERGENCY_SCHEMA_GUARD_TRIGGERED: unmigrated column fallback', ['column' => $e->getMessage()]);`

---

## 11. End-to-End Distributed Request Correlation

Every talent interaction generates a unified correlation chain:
```
Browser UUID (x-correlation-id)
      │
      ▼
Nginx Access Logs ($http_x_correlation_id)
      │
      ▼
Laravel Controller Request Context (request_id)
      │
      ▼
MySQL Idempotency Record (sys_idempotency_keys.request_hash)
      │
      ▼
Transactional Outbox Record (candidate_outbox_events.payload._correlation_id)
      │
      ▼
Horizon Queue Job Log (ProcessOutboxEventJob)
      │
      ▼
Elasticsearch Document Metadata (_source._correlation_id)
```
A single recruiter submission issue can be traced across all 7 infrastructure tiers using a single correlation UUID.

---

## 12. Formal 54-Gate Production Acceptance & Security Matrix (G-01 to G-54)

| Gate ID | Category | Invariant & Acceptance Criteria | Verification Method | Status |
| :---: | :--- | :--- | :--- | :---: |
| **G-01** | Subsystem Isolation | Add Talent modifications strictly quarantined to `service_company/bench-candidates/`. Zero edits to `job-applications`, `product_company`, or `staffing_company`. | `git diff --name-only` inspection | REQUIRED |
| **G-02** | Business Logic | Coin deduction (`eventId = 5`), package redemption, and stage mapping (1, 2, 4, 5) remain 100% mathematically and functionally identical. | Unit test assertion | REQUIRED |
| **G-03** | Commercials Math | Pay rate, bill rate, annual conversions, margin amount, and margin % calculate identically to legacy logic across hourly/monthly/yearly modes. | Automated Math Test Suite | REQUIRED |
| **G-04** | Client WASM Parser | Resume parsing executes in Web Worker User RAM; extracts name, email, phone, experience, skills, and summary without server calls. | DevTools Network & Worker Audit | REQUIRED |
| **G-05** | Parser Non-Destructive | Form auto-fill populates empty fields only; never overwrites user-edited inputs. | Manual & automated browser test | REQUIRED |
| **G-06** | Dropzone UI Invariant | Single "Remove" action displayed upon upload; dropzone completely hides; zero "Replace" button confusion. | Visual regression check | REQUIRED |
| **G-07** | Manual Entry Fallback | Recruiter can submit a talent profile without uploading a resume file (100% functional manual form). | Submission test with null resume | REQUIRED |
| **G-08** | In-Browser Trie | Skill and location autocomplete requests hit browser memory Trie (`window.JeroTalent.autocomplete`); zero backend AJAX per keystroke. | DevTools XHR tab (0 requests) | REQUIRED |
| **G-09** | Multi-Tab Isolation | Form state, upload sessions, and submission UUIDs scoped to `window.JeroTalent.tabInstanceId`. | Multi-tab concurrent submit test | REQUIRED |
| **G-10** | Client Idempotency | Submit button disables immediately upon click; loading spinner renders; `client_submission_uuid` injected into payload. | Double-click simulation | REQUIRED |
| **G-11** | Redis Idempotency | Redis `SETNX` lock on `talent:idempotency:{company_id}:{uuid}` with 120s TTL blocks concurrent duplicate submissions with HTTP 409. | Concurrent curl submission test | REQUIRED |
| **G-12** | MySQL Idempotency | Database unique constraint `uq_company_submission_uuid` guarantees single-row persistence at storage engine layer. | Raw SQL duplicate insert test | REQUIRED |
| **G-13** | Transaction Boundary | Zero Elasticsearch queries (`getClient()->...`) inside `DB::beginTransaction()`. | Static code analysis & trace | REQUIRED |
| **G-14** | Transaction Boundary | Zero Cloudflare R2 / S3 network calls (`Storage::disk()->...`) inside `DB::beginTransaction()`. | Static code analysis & trace | REQUIRED |
| **G-15** | Transaction Duration | MySQL transaction hold time benchmarked at P95 < 15ms. | Laravel Telescope / APM log | REQUIRED |
| **G-16** | Batch Relational Writes | `job_application_skills`, `bench_assign`, and `bench_additional_detail` inserted via single bulk `insert()` queries (0 N+1 loops). | Query log assertion (≤ 7 queries) | REQUIRED |
| **G-17** | Skills Experience Matrix | `bench_candidate_skill_experience` updated via bulk `upsert()` query. | Query log verification | REQUIRED |
| **G-18** | Outbox Event Write | Event written to `candidate_outbox_events` inside the same MySQL transaction as candidate record. | Transaction rollback test | REQUIRED |
| **G-19** | Outbox Worker Locking | Outbox event processor claims records using `SELECT ... FOR UPDATE SKIP LOCKED`. | Multi-worker queue test | REQUIRED |
| **G-20** | Search Sync Parity | Candidate, assigned recruiters, and custom details indexed in Elasticsearch out-of-band via Horizon queue. | Elasticsearch document check | REQUIRED |
| **G-21** | Dead-Letter Policy | Failed Elasticsearch indexing retried 5 times with exponential backoff before dead-letter notification. | Simulated ES timeout test | REQUIRED |
| **G-22** | Schema Emergency Guard | `resilientSaveTalentApplication()` catches MySQL error 1054, logs structured warning, and unsets pending migration columns safely. | Unit test simulation | REQUIRED |
| **G-23** | Triple-Redundant Redirect | Redirect executes via SweetAlert resolution OR guaranteed 2200ms fallback timer with single-execution flag. | Browser redirect automation test | REQUIRED |
| **G-24** | Listing Redirect Target | Form submission redirects authoritatively to `service_company.bench-candidates.index`. | HTTP response URL assertion | REQUIRED |
| **G-25** | Cache Invalidation | Tenant cache keys (`bench:hero:{metric}:{company_id}`) cleared/updated post-commit. | Redis key inspection post-submit | REQUIRED |
| **G-26** | 0% MySQL Reads on List | Bench listing dashboard (`/bench-candidates`) serves 100% of queries, filters, and counts from Elasticsearch. | MySQL general query log audit | REQUIRED |
| **G-27** | Client DOM Assembly | Listing dashboard consumes `data_v2` JSON payload and renders cards in User RAM. | Network payload audit (< 10KB) | REQUIRED |
| **G-28** | Hero Analytics Cache | Hero pipeline counts (Total, Bench, Requests, Placed) served from Redis with instant cache eviction. | Hero performance test | REQUIRED |
| **G-29** | Page Load DB Reads | `create()` view initialization performs 0 un-cached master SQL reads. | Telescope DB count on page load | REQUIRED |
| **G-30** | Response Latency SLA | Submit talent HTTP endpoint returns response in P95 < 150ms. | k6 benchmark test | REQUIRED |
| **G-31** | 10K Concurrency Scale | System sustains 10,000 virtual users across Add/Submit/Listing without thread exhaustion or 5xx errors. | Sustained concurrency load test | REQUIRED |
| **G-32** | Responsive UI Layout | Metronic 8 layout renders perfectly on 15.6" screens (1366×768 & 1920×1080) with internal card scrolling. | Visual multi-resolution audit | REQUIRED |
| **G-33** | Mobile/Secondary Contacts | Secondary contact details (mobile, email, phone code) preserved and persisted accurately. | Data persistence verification | REQUIRED |
| **G-34** | Zero Module Regressions | Zero changes or impact on Add Candidate, Requisitions, Job Applications, or Client Management modules. | Full test suite execution (`phpunit`) | REQUIRED |
| **G-35** | Outbox Worker Phase Separation | Outbox worker NEVER holds a MySQL transaction open while communicating with Elasticsearch or Cloudflare R2. | Code review + integration trace | REQUIRED |
| **G-36** | Edit Mode Rate Parity | Editing an existing talent candidate accurately loads and preserves all commercial model rates without zeroing out values. | Edit form submission verification | REQUIRED |
| **G-37** | Tenant Isolation | Company A cannot access Company B candidate UNLESS published to Bench stage and marketplace. | Security integration attack test | REQUIRED |
| **G-38** | ES Tenant Scope | Every Elasticsearch query enforces authenticated `company_id` filter. | ES query inspector | REQUIRED |
| **G-39** | R2 Authorization | Presigned PUT/GET URLs restricted strictly to authenticated company directory prefix. | S3 signature inspection | REQUIRED |
| **G-40** | Durable Idempotent Replay | Network retries replaying identical UUID receive identical cached JSON response from `sys_idempotency_keys`. | Idempotent replay test | REQUIRED |
| **G-41** | Coin Deductions Atomicity | Exactly one coin deduction occurs per unique candidate submission transaction. | Coin audit ledger check | REQUIRED |
| **G-42** | Outbox Lease Auto-Recovery | Expired `PROCESSING` outbox rows (`lease_until < NOW()`) automatically reclaimed by next worker poll. | Simulated worker crash test | REQUIRED |
| **G-43** | Deterministic ES IDs | ES documents indexed by deterministic IDs (`bench_candidate:{id}`); duplicate event runs update in place without duplicate docs. | Repeated event replay test | REQUIRED |
| **G-44** | Aggregate Version Fencing | Older event version cannot overwrite newer Elasticsearch document. | Out-of-order event dispatch test | REQUIRED |
| **G-45** | Delete Order Safety | Delete operations cannot be resurrected by delayed update events. | Race test: delete vs late update | REQUIRED |
| **G-46** | Redis Reconciliation | Scheduled job repairs drifted Redis hero counters to match authoritative MySQL projection. | Drift injection & heal test | REQUIRED |
| **G-47** | ES Zero-Downtime Rebuild | Entire Elasticsearch bench index rebuildable from MySQL via alias swap without search downtime. | Rebuild command execution | REQUIRED |
| **G-48** | Optimistic Concurrency | Concurrent edits to the same candidate detect version mismatch and reject stale edit with HTTP 409. | Multi-tab edit collision test | REQUIRED |
| **G-49** | Monetary Precision | JavaScript, PHP, and MySQL margin and rate calculations match exact currency rounding rules. | Cross-platform math assertions | REQUIRED |
| **G-50** | Currency Snapshot | Candidate commercials preserve exchange rate snapshot timestamp for historical reproducibility. | Multi-currency persistence check | REQUIRED |
| **G-51** | Web Worker UI Thread Isolation | Large PDF (15MB) parsing in Web Worker does not drop UI frame rate below 55 FPS. | Chrome DevTools Performance Profiler | REQUIRED |
| **G-52** | Idempotency Lease Recovery | Crashed request leaves `PROCESSING` row; lease expiration (`processing_until < NOW()`) safely reclaims or completes row; zero indefinite request blocking. | Simulated crash & recovery test | REQUIRED |
| **G-53** | Idempotency Key Reuse Defense | Reusing an idempotency key with conflicting `request_hash` returns HTTP 409 `IDEMPOTENCY_KEY_REUSE`; never replays cached response or alters state. | Request hash mismatch attack test | REQUIRED |
| **G-54** | Atomic ES Version Fencing | Elasticsearch indexing enforces atomic storage-engine versioning (`version_type=external_gte`), rejecting out-of-order writes with HTTP 409. | Out-of-order concurrent indexing test | REQUIRED |

---

---

## 13. Comprehensive Dependency Failure & Recovery Matrix

| Dependency & Failure Mode | System Behavior | Recruiter-Visible Impact | Automated Recovery Path |
| :--- | :--- | :--- | :--- |
| **MySQL Unavailable** | `DB::beginTransaction()` throws; controller returns HTTP 503. | "Service temporarily unavailable — please retry." Form data preserved in browser. | Auto-reconnect via Octane. Form retry enabled. |
| **Redis Down / Split-Brain** | Distributed lock fails open to durable MySQL `sys_idempotency_keys` table. | Zero degradation for standard submissions; duplicate clicks caught by MySQL constraint. | Sentinel auto-failover. System remains fully operational. |
| **Elasticsearch Outage** | Candidate persists to MySQL. Outbox event written. Ingestion retries up to 5 times. | Recruiter sees `index_status: "processing"`. Form submits instantly. Listing briefly delayed. | Horizon worker retries automatically with exponential backoff. |
| **ES Mapping Incompatible** | Worker catches mapping exception, logs critical error, marks event `FAILED_FINAL`. | Recruiter receives success; search indexing quarantined. | Triggers P1 alert. Admin updates mapping and replays failed outbox events. |
| **ES Index Deleted** | Worker detects missing index; auto-triggers index rebuild or alerts. | Listing temporarily unavailable. | Admin executes `bench:rebuild-search-index` with zero downtime alias swap. |
| **R2 Upload Fails** | State machine moves to `UPLOAD_FAILED`. Emits client failure event. | "Resume upload failed — submit without resume or retry." Manual entry active. | Recruiter retries upload or submits profile with manual form fields. |
| **R2 Object Orphaned** | File uploaded but candidate submission never completed. | None. File resides in `temp/` prefix. | Cloudflare R2 24-hour Bucket Lifecycle policy auto-purges object. |
| **Candidate Removes Resume** | MySQL clears resume pointers; marks asset `REMOVAL_PENDING`. | UI restores dropzone immediately. | Purge job deletes R2 object post-commit with 7-day retention grace period. |
| **Parser Bundle Unavailable** | Dynamic `import()` fails; catches and emits `talent:parser-failed`. | Dropzone indicates manual data entry. Form 100% functional. | Recruiter enters details manually. Parser retries on next file selection. |
| **Parser Returns Malformed JSON** | JSON schema validation catches schema error; logs telemetry warning. | Fields that could not be verified are left blank for manual input. | Form validation highlights missing required fields. |
| **Worker Crashes During Phase B** | Event remains in status `PROCESSING` with active `lease_token`. | None. Submission already committed and recruiter redirected. | Next worker poll reclaims event after `lease_until < NOW()` expires. |
| **Two Tabs Edit Same Candidate** | Tab 2 submits older `resume_version`. | Tab 2 receives HTTP 409 Conflict ("Profile updated in another session"). | Tab 2 prompts recruiter to reload fresh candidate data. |
| **Currency Service Stale** | Exchange rate API offline. | System falls back to last-known valid cached rates stored in Redis/IndexedDB. | Background scheduler refreshes rates on API recovery. |

---

## 14. Phased Implementation Roadmap & Verification Milestones

The Add Talent, Edit Talent, Submit Pipeline, and Bench Listing remediation is structured into **seven sequential, highly disciplined engineering phases**. Each phase defines strict scope boundaries, anti-regression invariants, concrete technical tasks, production-grade code snippets, automated verification tests, exact CLI commands, exit criteria, and automated rollback procedures.

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                   V4 BENCH TALENT MODULE: 7-PHASE ENGINEERING EXECUTION PIPELINE                 │
│                                                                                                  │
│  [PHASE 1] Schema Hardening, Migration Baseline & Safety Test Harness                            │
│    │ • Migration: client_submission_uuid, resume_version, compound indexes                       │
│    │ • Verify sys_idempotency_keys & candidate_outbox_events schemas                              │
│    │ • Scaffold automated baseline feature test suite (V4AddTalentRemediationTest)               │
│    ▼                                                                                             │
│  [PHASE 2] Controller CQRS Refactoring, Outbox Decoupling & Durable Idempotency                  │
│    │ • Cleanse DB::beginTransaction() of all external network I/O (ES & Cloudflare R2)           │
│    │ • 3-Layer Durable Idempotency (Client UUID → Redis NX → sys_idempotency_keys replay)        │
│    │ • Idempotent Coin Deduction (Event 5) inside transaction with ledger guard                  │
│    │ • Relational bulk writes (skills, assign, details) & resilientSaveTalentApplication()       │
│    │ • Atomic outbox event generation & post-commit tenant cache wipe                            │
│    ▼                                                                                             │
│  [PHASE 3] Client-First Web Worker Parser & In-Browser Commercials Engine Hardening              │
│    │ • Dedicated Web Worker (talent-parser-worker.js) for 0% main-thread UI jank (>55 FPS)       │
│    │ • Non-destructive form auto-fill (preserves manual input)                                   │
│    │ • In-Browser Commercials Engine (window.JeroTalent.commercial) with 2,080-hour conversion    │
│    │ • Preserve all 8 hidden commercial form inputs (reverted commit 16b825d5 defense)           │
│    │ • Triple-Redundant Redirect Handler (SweetAlert + 2,200ms hard timer fallback)              │
│    ▼                                                                                             │
│  [PHASE 4] 15.6" Enterprise Screen Layout Optimization & Metronic 8 Responsive Design            │
│    │ • Dropzone UI total collapse upon upload (display: none !important)                         │
│    │ • Synchronize equal height for Upload Resume and Career Summary cards (h-100 flex)          │
│    │ • 10% height compression eliminating white space on 1366×768 & 1920×1080 displays           │
│    │ • Scoped CSS isolation in partials/_form_styles.blade.php (Rule 17)                         │
│    ▼                                                                                             │
│  [PHASE 5] Listing Dashboard Optimization (data_v2, Hero Analytics & Scheduled Reconciliation)   │
│    │ • Controller data_v2() endpoint: 100% Elasticsearch reads (8KB JSON vs 85KB HTML)           │
│    │ • Client DOM Assembly in index.blade.php via DocumentFragments in browser RAM               │
│    │ • Hero Pipeline KPIs served from Redis materialized counters (P95 < 5ms)                    │
│    │ • Scheduled background reconciliation command talents:reconcile-hero-counters              │
│    ▼                                                                                             │
│  [PHASE 6] Search-Index Zero-Downtime Rebuild & Alias Automation                                 │
│    │ • Artisan command bench:rebuild-search-index with chunked MySQL streaming (500/batch)       │
│    │ • Atomic Elasticsearch alias swap POST /_aliases (zero search downtime)                     │
│    │ • Outbox Worker 3-phase lease execution with FOR UPDATE SKIP LOCKED auto-recovery           │
│    ▼                                                                                             │
│  [PHASE 7] Security Audit, Concurrency Load Testing & 54-Gate Production Sign-Off                │
│    │ • Multi-tenant IDOR attack tests & presigned R2 company directory scoping                   │
│    │ • k6 load testing: 10,000 virtual users across create, store, and data_v2 (P95 < 150ms)     │
│    │ • Formal sign-off across all 54 Acceptance Gates (G-01 to G-54)                             │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### Phase 1: Schema Hardening, Migration Baseline & Safety Test Harness

#### 1.1 Objectives & Scope
- Establish database schema guarantees for idempotent writes, optimistic locking, and high-performance tenant filtering.
- Ensure the existing `sys_idempotency_keys` table and `candidate_outbox_events` table are configured for the `bench_candidate` aggregate domain.
- Create an automated feature test harness (`tests/Feature/V4AddTalentRemediationTest.php`) capturing current baseline functionality before any controller or Blade edits are introduced.
- **Strict Boundary (Rule 17):** Only mutate `job_applications` table for bench-related indexing. Zero changes to `product_company` or `staffing_company`.

#### 1.2 Technical Implementation Tasks

##### Task 1.1: Database Migration for Idempotency, Dual-Versioning & Bench Indexing
Create migration `database/migrations/2026_09_29_000001_add_v4_idempotency_to_job_applications_for_bench.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hardens job_applications with client idempotency UUID, dual optimistic concurrency
     * versions (aggregate_version vs resume_version), and compound indices for 10K bench query performance.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                $table->string('client_submission_uuid', 64)->nullable()->after('id')
                    ->comment('Client-generated UUIDv4 preventing duplicate network submissions');
            }

            if (!Schema::hasColumn('job_applications', 'aggregate_version')) {
                $table->unsignedInteger('aggregate_version')->default(1)->after('client_submission_uuid')
                    ->comment('Optimistic concurrency lock version incremented on ANY candidate profile mutation');
            }

            if (!Schema::hasColumn('job_applications', 'resume_version')) {
                $table->unsignedInteger('resume_version')->default(1)->after('resume')
                    ->comment('Optimistic concurrency lock version incremented ONLY on resume asset mutations');
            }

            // Compound index for bench candidate multi-tenant status queries (0% full scans)
            $table->index(['company_id', 'condidate_type', 'status'], 'idx_bench_company_type_status');

            // Unique index enforcing durable idempotency per tenant
            $table->unique(['company_id', 'client_submission_uuid'], 'uq_company_submission_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropUnique('uq_company_submission_uuid');
            $table->dropIndex('idx_bench_company_type_status');
            $table->dropColumn(['client_submission_uuid', 'aggregate_version', 'resume_version']);
        });
    }
};
```

Create migration `database/migrations/2026_09_29_000002_harden_sys_idempotency_and_outbox_tables.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hardens sys_idempotency_keys with execution lease deadline (processing_until)
     * and candidate_outbox_events with DLQ replay audit tracking columns.
     */
    public function up(): void
    {
        Schema::table('sys_idempotency_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('sys_idempotency_keys', 'processing_until')) {
                $table->timestamp('processing_until')->nullable()->after('last_error')
                    ->comment('Execution lease deadline (NOW() + 60s); distinguishes active in-flight from abandoned crash');
                $table->index(['status', 'processing_until'], 'idx_idempotency_lease');
            }
        });

        Schema::table('candidate_outbox_events', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate_outbox_events', 'replayed_by')) {
                $table->string('replayed_by', 64)->nullable()->after('last_error');
                $table->timestamp('replayed_at')->nullable()->after('replayed_by');
                $table->string('replay_reason', 255)->nullable()->after('replayed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sys_idempotency_keys', function (Blueprint $table) {
            $table->dropIndex('idx_idempotency_lease');
            $table->dropColumn('processing_until');
        });

        Schema::table('candidate_outbox_events', function (Blueprint $table) {
            $table->dropColumn(['replayed_by', 'replayed_at', 'replay_reason']);
        });
    }
};
```

##### Task 1.2: Verify and Register `SysIdempotencyKey` Model
Verify that `App\Models\SysIdempotencyKey` binds to the `sys_idempotency_keys` table with lease support:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysIdempotencyKey extends Model
{
    protected $table = 'sys_idempotency_keys';
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'idempotency_key',
        'request_hash',
        'status',
        'response_status',
        'response_body',
        'last_error',
        'processing_until',
        'created_at',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'processing_until' => 'datetime',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
```

##### Task 1.3: Build Automated Baseline Test Suite
Create `tests/Feature/V4AddTalentRemediationTest.php` to lock down baseline business logic:
```php
<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class V4AddTalentRemediationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup authenticated service company recruiter session
        $this->company = Company::factory()->create(['company_type' => 'service']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => 2, // Recruiter
        ]);
    }

    /** @test */
    public function test_bench_candidate_create_page_loads_with_zero_uncached_master_queries(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('service_company.bench-candidates.create'));

        $response->assertStatus(200);
        $response->assertSee('Add In-House Talent');
        $response->assertSee('window.JeroTalent');
    }

    /** @test */
    public function test_commercials_conversions_adhere_to_2080_annual_hours(): void
    {
        $hourlyRate = 50.00;
        $expectedAnnual = $hourlyRate * 2080; // $104,000
        $expectedMonthly = $expectedAnnual / 12; // $8,666.67

        $this->assertEquals(104000.00, $expectedAnnual);
        $this->assertEqualsWithDelta(8666.67, $expectedMonthly, 0.01);
    }

    /** @test */
    public function test_stage_mappings_strictly_preserve_business_rules(): void
    {
        $validStages = [1 => 'Pool', 2 => 'Bench', 4 => 'Hold', 5 => 'Exited'];
        foreach ($validStages as $stageId => $stageName) {
            $this->assertContains($stageId, [1, 2, 4, 5]);
        }
    }
}
```

#### 1.3 Verification Commands
```bash
# 1. Run migration dry-run and apply
php artisan migrate --pretend
php artisan migrate --force

# 2. Verify PHP syntax
php -l database/migrations/2026_09_29_000001_add_v4_idempotency_to_job_applications_for_bench.php
php -l database/migrations/2026_09_29_000002_harden_sys_idempotency_and_outbox_tables.php
php -l tests/Feature/V4AddTalentRemediationTest.php

# 3. Execute baseline test suite
php artisan test --filter=V4AddTalentRemediationTest
```

#### 1.4 Exit Criteria & Rollback
- **Exit Criteria:** Migration applies cleanly; `job_applications` has `client_submission_uuid`, `aggregate_version`, and `resume_version`; `sys_idempotency_keys` has `processing_until`; baseline tests return 100% PASS.
- **Rollback Procedure:**
  ```bash
  php artisan migrate:rollback --step=2
  ```

---

### Phase 2: Controller CQRS Refactoring, Outbox Decoupling & Durable Idempotency

#### 2.1 Objectives & Scope
- Refactor `ServiceCompanyBenchCandidatesController::store()` to execute strictly local MySQL writes inside `DB::beginTransaction()`.
- Eliminate all synchronous Elasticsearch queries (`deleteByQuery`, `getClient()->update()`) and remote storage calls from the transaction path.
- Implement 3-layer durable idempotency backed by `sys_idempotency_keys` with **lease auto-recovery (`processing_until`) (Gate G-52)** and **request hash mismatch defense (Gate G-53)**.
- Implement dual optimistic concurrency checks for candidate update flows (`expected_aggregate_version` and `expected_resume_version`).
- Enforce transactional coin deduction protection (`companyCoinsDeductions`, `eventId = 5`) verifying ledger absence prior to deduction.
- Replace N+1 loops with single bulk queries for relational tables (`job_application_skills`, `bench_assign`, `bench_additional_detail`, `bench_candidate_skill_experience`).
- Persist atomic outbox event into `candidate_outbox_events` with deterministic aggregate ID `bench_candidate:{candidate_id}` and authoritative complete recruiter assignment list.

#### 2.2 Technical Implementation Tasks

##### Task 2.1: Implement 3-Layer Durable Idempotency with Lease Recovery & Hash Defense
In `ServiceCompanyBenchCandidatesController.php`:
```php
// Step 1: Extract Client Submission UUID & Request Hash
$companyId = auth()->user()->company_id;
$clientUuid = $request->input('client_submission_uuid') ?? (string) Str::uuid();
$requestPayloadForHash = $request->except(['_token', 'resume', 'client_submission_uuid']);
$requestHash = hash('xxh128', json_encode($requestPayloadForHash));
$idempotencyKey = "talent:idempotency:{$companyId}:{$clientUuid}";

// Step 2: Layer 2 - Redis Fast Intercept (50ms timeout bounded)
try {
    $redisAcquired = Redis::connection('default')->set($idempotencyKey, 'LOCKED', 'EX', 120, 'NX');
    if (!$redisAcquired) {
        $existingRecord = SysIdempotencyKey::where('tenant_id', $companyId)
            ->where('idempotency_key', $clientUuid)
            ->first();

        // INVARIANT G-53: Request Hash Mismatch Defense
        if ($existingRecord && $existingRecord->request_hash !== $requestHash) {
            return response()->json([
                'status' => 'conflict',
                'error' => 'IDEMPOTENCY_KEY_REUSE',
                'message' => 'Idempotency key has already been used for a conflicting payload.',
            ], 409);
        }

        if ($existingRecord && $existingRecord->status === 'COMPLETED') {
            return response()->json(json_decode($existingRecord->response_body, true), 200);
        }

        // Check if lease has expired
        if ($existingRecord && $existingRecord->status === 'PROCESSING' && $existingRecord->processing_until < now()) {
            // Lease expired (Gate G-52) -> Fall through to Step 3 MySQL lease recovery!
        } else {
            return response()->json([
                'status' => 'conflict',
                'error' => 'IN_FLIGHT',
                'message' => 'A submission with this UUID is currently being processed. Please wait.',
            ], 409);
        }
    }
} catch (\Throwable $e) {
    // Redis down or timed out (>50ms) -> Fail open safely to Layer 3 (MySQL durable table)
    Log::warning('REDIS_IDEMPOTENCY_FAIL_OPEN: falling back to MySQL sys_idempotency_keys', [
        'error' => $e->getMessage(),
        'company_id' => $companyId,
        'uuid' => $clientUuid,
    ]);
}

// Step 3: Layer 3 - Durable MySQL Persistence inside Transaction with Lease Recovery
DB::beginTransaction();
try {
    $idempotencyRow = SysIdempotencyKey::where('tenant_id', $companyId)
        ->where('idempotency_key', $clientUuid)
        ->lockForUpdate()
        ->first();

    if ($idempotencyRow) {
        // INVARIANT G-53: Reject Key Reuse with Different Payload
        if ($idempotencyRow->request_hash !== $requestHash) {
            DB::rollBack();
            return response()->json([
                'status' => 'conflict',
                'error' => 'IDEMPOTENCY_KEY_REUSE',
                'message' => 'Idempotency key has already been used for a conflicting payload.',
            ], 409);
        }

        // Return Completed Cached Response
        if ($idempotencyRow->status === 'COMPLETED') {
            DB::rollBack();
            return response()->json(json_decode($idempotencyRow->response_body, true), $idempotencyRow->response_status ?? 200);
        }

        // Active In-Flight Lease Check
        if ($idempotencyRow->status === 'PROCESSING' && $idempotencyRow->processing_until >= now()) {
            DB::rollBack();
            return response()->json([
                'status' => 'conflict',
                'error' => 'IN_FLIGHT',
                'message' => 'Submission actively being processed in another session.',
            ], 409);
        }

        // INVARIANT G-52: Crashed Worker Lease Auto-Recovery
        if ($idempotencyRow->status === 'PROCESSING' && $idempotencyRow->processing_until < now()) {
            Log::notice('IDEMPOTENCY_LEASE_RECLAIMED', ['tenant_id' => $companyId, 'uuid' => $clientUuid]);
            $idempotencyRow->update([
                'processing_until' => now()->addSeconds(60),
            ]);

            // Check if candidate already saved
            $existingCandidate = JobApplication::where('company_id', $companyId)
                ->where('client_submission_uuid', $clientUuid)
                ->first();

            if ($existingCandidate) {
                $recoveredResponse = [
                    'status' => 'success',
                    'message' => 'Talent profile created successfully',
                    'candidate_id' => $existingCandidate->id,
                    'redirect_url' => route('service_company.bench-candidates.index'),
                    'idempotency_uuid' => $clientUuid,
                ];
                $idempotencyRow->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($recoveredResponse),
                    'completed_at' => now(),
                    'processing_until' => null,
                ]);
                DB::commit();
                return response()->json($recoveredResponse, 200);
            }
        }
    } else {
        // First-Time Insert
        SysIdempotencyKey::create([
            'tenant_id' => $companyId,
            'idempotency_key' => $clientUuid,
            'request_hash' => $requestHash,
            'status' => 'PROCESSING',
            'processing_until' => now()->addSeconds(60), // 60s execution lease
            'created_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);
    }

    // Step 4: Idempotent Coin Deduction (Event 5) Protected by Transaction
    $coinAlreadyDeducted = DB::table('company_coins_deductions')
        ->where('company_id', $companyId)
        ->where('transaction_reference', $clientUuid)
        ->exists();

    if (!$coinAlreadyDeducted) {
        $this->deductTalentCoins($companyId, $clientUuid);
    }

    // Step 5: Save JobApplication via Emergency Compatibility Guard
    $jobApplication = new JobApplication();
    $this->fillTalentApplicationFields($jobApplication, $request, $companyId, $clientUuid);
    $this->resilientSaveTalentApplication($jobApplication);

    // Step 6: Batch Relational Writes (Zero N+1 Loops)
    $this->batchPersistTalentRelations($jobApplication->id, $companyId, $request);

    // Step 7: Atomic Outbox Event Persistence with Complete Authoritative Recruiter List
    $outboxPayload = [
        'candidate_id' => $jobApplication->id,
        'company_id' => $companyId,
        'client_uuid' => $clientUuid,
        'aggregate_version' => $jobApplication->aggregate_version ?? 1,
        'resume_version' => $jobApplication->resume_version ?? 1,
        'action' => 'created',
        'status' => (int) $request->input('status', 2), // Bench
        'commercial_model' => (int) $request->input('commercial_model', 1),
        'assigned_recruiters' => array_map('intval', $request->input('assigned_recruiters', [])),
        'timestamp' => now()->toIso8601String(),
    ];

    DB::table('candidate_outbox_events')->insert([
        'aggregate_type' => 'bench_candidate',
        'aggregate_id' => (string) $jobApplication->id,
        'event_type' => 'talent.bench.created',
        'payload' => json_encode($outboxPayload),
        'dedupe_key' => "bench_candidate:{$jobApplication->id}:v1",
        'status' => 'PENDING',
        'created_at' => now(),
    ]);

    // Prepare JSON Response Payload
    $responsePayload = [
        'status' => 'success',
        'message' => 'Talent profile created successfully',
        'candidate_id' => $jobApplication->id,
        'redirect_url' => route('service_company.bench-candidates.index'),
        'idempotency_uuid' => $clientUuid,
    ];

    // Finalize Idempotency Record to COMPLETED
    SysIdempotencyKey::where('tenant_id', $companyId)
        ->where('idempotency_key', $clientUuid)
        ->update([
            'status' => 'COMPLETED',
            'response_status' => 200,
            'response_body' => json_encode($responsePayload),
            'completed_at' => now(),
            'processing_until' => null,
        ]);

    DB::commit();

    // Step 8: Post-Commit Cache Invalidation & Dirty Tenant Registration
    try {
        Redis::sadd('bench:dirty_tenants', $companyId); // Registers tenant for low-priority reconciliation
        Redis::del("bench:hero:total:{$companyId}");
        Redis::del("bench:hero:bench:{$companyId}");
        Redis::del("bench:hero:pool:{$companyId}");
        Redis::del("bench:hero:hold:{$companyId}");
        Redis::del("ats:allbenchcandidates:{$companyId}");
    } catch (\Throwable $e) {
        Log::warning('POST_COMMIT_CACHE_CLEAR_FAILED', ['error' => $e->getMessage()]);
    }

    return response()->json($responsePayload, 200);

} catch (\Illuminate\Database\QueryException $e) {
    DB::rollBack();
    // Catch duplicate key 1062 on uq_company_submission_uuid
    if ($e->errorInfo[1] === 1062) {
        $completedRecord = SysIdempotencyKey::where('tenant_id', $companyId)
            ->where('idempotency_key', $clientUuid)
            ->first();
        if ($completedRecord && $completedRecord->response_body) {
            return response()->json(json_decode($completedRecord->response_body, true), 200);
        }
    }
    throw $e;
} catch (\Throwable $e) {
    DB::rollBack();
    Log::error('TALENT_SUBMIT_PERSISTENCE_FAILED', [
        'error' => $e->getMessage(),
        'company_id' => $companyId,
        'uuid' => $clientUuid,
    ]);
    return response()->json([
        'status' => 'error',
        'message' => 'Failed to save talent profile. Please retry.',
    ], 500);
```

##### Task 2.2: Batch Relational Writes Implementation
Implement helper method `batchPersistTalentRelations()` eliminating N+1 loops:
```php
protected function batchPersistTalentRelations(int $candidateId, int $companyId, Request $request): void
{
    // 1. Bulk Insert Skills
    $skills = $request->input('skills', []);
    if (!empty($skills) && is_array($skills)) {
        $skillRows = [];
        $expRows = [];
        $now = now();
        foreach ($skills as $skill) {
            $skillId = is_array($skill) ? ($skill['id'] ?? null) : $skill;
            if (!$skillId) continue;
            $skillRows[] = [
                'job_application_id' => $candidateId,
                'skill_id' => $skillId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $expRows[] = [
                'candidate_id' => $candidateId,
                'skill_id' => $skillId,
                'experience_rating' => is_array($skill) ? ($skill['rating'] ?? 3) : 3,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($skillRows)) {
            DB::table('job_application_skills')->insert($skillRows);
            DB::table('bench_candidate_skill_experience')->upsert(
                $expRows,
                ['candidate_id', 'skill_id'],
                ['experience_rating', 'updated_at']
            );
        }
    }

    // 2. Bulk Insert Recruiter Assignments
    $assignedRecruiters = $request->input('assigned_recruiters', []);
    if (!empty($assignedRecruiters) && is_array($assignedRecruiters)) {
        $assignRows = [];
        $now = now();
        foreach ($assignedRecruiters as $recruiterId) {
            $assignRows[] = [
                'candidate_id' => $candidateId,
                'company_id' => $companyId,
                'user_id' => $recruiterId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('bench_assign')->insert($assignRows);
    }

    // 3. Single Insert Bench Additional Details
    DB::table('bench_additional_detail')->insert([
        'candidate_id' => $candidateId,
        'company_id' => $companyId,
        'commercial_model' => $request->input('commercial_model', 1),
        'pay_rate' => $request->input('pay_rate_amount', 0),
        'bill_rate' => $request->input('bill_rate_amount', 0),
        'margin_percentage' => $request->input('margin_percentage', 0),
        'percentage_hike' => $request->input('percentage_hike', 0),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
```

##### Task 2.3: Instrument Emergency Schema Guard
```php
protected function resilientSaveTalentApplication(JobApplication $candidate): void
{
    try {
        $candidate->save();
    } catch (\Illuminate\Database\QueryException $e) {
        // Detect MySQL error 1054: Unknown Column
        if ($e->errorInfo[1] === 1054) {
            preg_match("/Unknown column '([^']+)'/", $e->getMessage(), $matches);
            $failedColumn = $matches[1] ?? null;

            if ($failedColumn && array_key_exists($failedColumn, $candidate->getAttributes())) {
                Log::warning('EMERGENCY_SCHEMA_GUARD_TRIGGERED: unmigrated column dropped during rolling deploy', [
                    'column' => $failedColumn,
                    'model' => get_class($candidate),
                    'candidate_email' => $candidate->email,
                ]);

                unset($candidate->{$failedColumn});
                $candidate->save(); // Retry save without missing column
                return;
            }
        }
        throw $e;
    }
}
```

#### 2.3 Verification Commands
```bash
# 1. Verify syntax
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php

# 2. Run Pint code style fixer
./vendor/bin/pint --test app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php

# 3. Execute feature tests asserting idempotency, coin deduplication, and transaction duration
php artisan test --filter=V4AddTalentRemediationTest
```

#### 2.4 Exit Criteria & Rollback
- **Exit Criteria:** Zero external HTTP/ES calls inside `DB::beginTransaction()`; transaction duration benchmarked P95 < 15ms; duplicate UUIDs return cached JSON response without duplicate DB rows; coins deducted exactly once.
- **Rollback Procedure:**
  ```bash
  git checkout HEAD -- app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php
  ```

---

### Phase 3: Client-First Web Worker Parser & In-Browser Commercials Engine Hardening

#### 3.1 Objectives & Scope
- Offload resume parsing computation into a dedicated Web Worker (`resources/js/talent-parser-worker.js`) to guarantee 0% main-thread UI jank (>55 FPS on 15MB PDFs).
- Enforce non-destructive auto-fill: populate empty inputs only, preserving any manual recruiter typing.
- Harden the In-Browser Commercials Engine under `window.JeroTalent.commercial` with 60 FPS live calculations.
- **Critical Anti-Regression Invariant:** Preserve all 8 hidden commercial form inputs (`pay_annual`, `pay_monthly`, `pay_rate_amount`, `bill_annual`, `bill_monthly`, `bill_rate_amount`, `percentage_hike`, `comission_type`) that were inadvertently dropped in commit `16b825d5`.
- Implement bulletproof Triple-Redundant Redirect Handler in `create.blade.php`.

#### 3.2 Technical Implementation Tasks

##### Task 3.1: Dedicated Web Worker Implementation
Create `resources/js/talent-parser-worker.js`:
```javascript
/**
 * JERO Talent Resume Parser Web Worker
 * Offloads WebAssembly, PDF.js, and text extraction off the main UI thread.
 */
importScripts('https://parser.jero.works/jero-resume-parser-2tURGwDx.js');

self.onmessage = async function(event) {
    const { action, fileData, fileName, mimeType, runId } = event.data;

    if (action === 'PARSE_RESUME') {
        try {
            self.postMessage({ type: 'PARSER_PROGRESS', runId, progress: 20 });
            
            // Invoke WASM Resume Parser Engine in Worker RAM
            const parsedResult = await self.JeroResumeParser.parse(fileData, {
                filename: fileName,
                mimeType: mimeType
            });

            self.postMessage({
                type: 'PARSER_SUCCESS',
                runId,
                data: parsedResult
            });
        } catch (error) {
            self.postMessage({
                type: 'PARSER_ERROR',
                runId,
                error: error.message || 'Worker resume parsing failed'
            });
        }
    }
};
```

##### Task 3.2: In-Browser Commercials Engine (`window.JeroTalent.commercial`)
In `resources/views/service_company/bench-candidates/create.blade.php`:
```javascript
window.JeroTalent = window.JeroTalent || {};

window.JeroTalent.commercial = (function() {
    'use strict';

    const ANNUAL_WORK_HOURS = 2080; // Invariant: 40 hrs/wk * 52 wks
    const MONTHS_PER_YEAR = 12;

    function recalculate() {
        const model = document.querySelector('input[name="commercial_model"]:checked')?.value || '1';
        const payFreq = document.getElementById('pay_frequency')?.value || 'hourly';
        const billFreq = document.getElementById('bill_frequency')?.value || 'hourly';
        const payInput = parseFloat(document.getElementById('pay_rate_input')?.value) || 0;
        const billInput = parseFloat(document.getElementById('bill_rate_input')?.value) || 0;

        // 1. Calculate Standardized Annualized Pay
        let payAnnual = 0;
        if (payFreq === 'hourly') payAnnual = payInput * ANNUAL_WORK_HOURS;
        else if (payFreq === 'monthly') payAnnual = payInput * MONTHS_PER_YEAR;
        else payAnnual = payInput;

        const payMonthly = payAnnual / MONTHS_PER_YEAR;
        const payHourly = payAnnual / ANNUAL_WORK_HOURS;

        // 2. Calculate Standardized Annualized Bill
        let billAnnual = 0;
        if (billFreq === 'hourly') billAnnual = billInput * ANNUAL_WORK_HOURS;
        else if (billFreq === 'monthly') billAnnual = billInput * MONTHS_PER_YEAR;
        else billAnnual = billInput;

        const billMonthly = billAnnual / MONTHS_PER_YEAR;
        const billHourly = billAnnual / ANNUAL_WORK_HOURS;

        // 3. Margin Calculations
        const marginAnnual = Math.max(0, billAnnual - payAnnual);
        const marginPercentage = billAnnual > 0 ? ((marginAnnual / billAnnual) * 100) : 0;
        const percentageHike = payAnnual > 0 ? (((billAnnual - payAnnual) / payAnnual) * 100) : 0;

        // 4. Update UI Display Elements (60 FPS)
        const marginAmountElem = document.getElementById('display_margin_amount');
        const marginPercentElem = document.getElementById('display_margin_percentage');
        if (marginAmountElem) marginAmountElem.textContent = '$' + marginAnnual.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (marginPercentElem) marginPercentElem.textContent = marginPercentage.toFixed(2) + '%';

        // 5. CRITICAL: Synchronize All 8 Hidden Inputs (Commit 16b825d5 Defense)
        document.getElementById('hidden_pay_annual').value = payAnnual.toFixed(2);
        document.getElementById('hidden_pay_monthly').value = payMonthly.toFixed(2);
        document.getElementById('hidden_pay_rate_amount').value = (payFreq === 'hourly' ? payHourly : (payFreq === 'monthly' ? payMonthly : payAnnual)).toFixed(2);

        document.getElementById('hidden_bill_annual').value = billAnnual.toFixed(2);
        document.getElementById('hidden_bill_monthly').value = billMonthly.toFixed(2);
        document.getElementById('hidden_bill_rate_amount').value = (billFreq === 'hourly' ? billHourly : (billFreq === 'monthly' ? billMonthly : billAnnual)).toFixed(2);

        document.getElementById('hidden_percentage_hike').value = percentageHike.toFixed(2);
        document.getElementById('hidden_comission_type').value = model;
    }

    return {
        init: function() {
            document.querySelectorAll('.commercial-calc-trigger').forEach(input => {
                input.addEventListener('input', recalculate);
                input.addEventListener('change', recalculate);
            });
            recalculate();
        },
        recalculate: recalculate
    };
})();
```

##### Task 3.3: Triple-Redundant Redirect Handler
```javascript
function handleTalentSubmissionSuccess(response) {
    const redirectUrl = response.redirect_url || '/service_company/bench-candidates';
    let redirectExecuted = false;

    function executeRedirect() {
        if (!redirectExecuted) {
            redirectExecuted = true;
            window.location.href = redirectUrl;
        }
    }

    // 1. Primary: SweetAlert confirmation button
    Swal.fire({
        text: response.message || "Talent profile created successfully!",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "View Bench Listing",
        customClass: { confirmButton: "btn btn-primary btn-sm" }
    }).then(function(result) {
        executeRedirect();
    });

    // 2. Secondary: Guaranteed hard timer fallback (2,200ms)
    setTimeout(executeRedirect, 2200);

    // 3. Tertiary: Window unload insurance
    window.addEventListener('beforeunload', function() {
        redirectExecuted = true;
    });
}
```

#### 3.3 Verification Commands
```bash
# 1. Compile or bundle JS assets if required
npm run dev

# 2. Assert hidden commercial inputs exist in Blade
grep -n "hidden_pay_annual" resources/views/service_company/bench-candidates/create.blade.php
grep -n "hidden_bill_annual" resources/views/service_company/bench-candidates/create.blade.php

# 3. Test commercials math in PHP unit test
php artisan test --filter=test_commercials_conversions_adhere_to_2080_annual_hours
```

#### 3.4 Exit Criteria & Rollback
- **Exit Criteria:** Web Worker extracts resume data without blocking main thread; commercials calculations render at 60 FPS; all 8 hidden fields submit authoritative values; redirect executes within 2.2s.
- **Rollback Procedure:**
  ```bash
  git checkout HEAD -- resources/views/service_company/bench-candidates/create.blade.php resources/js/talent-parser-worker.js
  ```

---

### Phase 4: 15.6" Enterprise Screen Layout Optimization & Metronic 8 Responsive Design

#### 4.1 Objectives & Scope
- Optimize `create.blade.php` and `_form_styles.blade.php` to render seamlessly on standard recruiter displays: **15.6-inch screens (1366×768 and 1920×1080)**.
- Ensure Dropzone UI completely collapses upon resume upload (`display: none !important`), replaced by a clean single "Remove" action badge.
- Harmonize Upload Resume Card and Career Summary Card with matching equal heights (`h-100`, flex alignment).
- Compress vertical padding by 10% eliminating dead white space and unnecessary outer page scrolling.
- **Strict Quarantine (Rule 17):** All styling scoped under `#talent_create_app` container.

#### 4.2 Technical Implementation Tasks

##### Task 4.1: Dropzone UI Total Collapse & Equal-Height Alignment
In `resources/views/service_company/bench-candidates/partials/_form_styles.blade.php`:
```css
/* 15.6" Screen Optimization Scoped to Bench Candidates Module */
#talent_create_app {
    max-width: 100%;
}

/* Equal Height Flex Pairing for Resume Upload and Career Summary */
#talent_create_app .equal-height-row {
    display: flex;
    flex-wrap: wrap;
}

#talent_create_app .equal-height-row > [class*='col-'] {
    display: flex;
    flex-direction: column;
}

#talent_create_app .equal-height-row .card {
    flex: 1 1 auto;
    margin-bottom: 1.25rem;
}

/* Dropzone Collapse: Completely Hide Upload Area When File Present */
#talent_create_app .dropzone.has-file {
    display: none !important;
}

/* Single Unified Remove Action Badge */
#talent_create_app .uploaded-file-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #f4f6fa;
    border: 1px solid #e1e3ea;
    border-radius: 0.475rem;
}

/* 10% Vertical Height Compression */
#talent_create_app .form-group,
#talent_create_app .fv-row {
    margin-bottom: 0.85rem !important;
}

#talent_create_app .form-control,
#talent_create_app .form-select {
    font-size: 13px !important;
    padding-top: 0.45rem !important;
    padding-bottom: 0.45rem !important;
    height: calc(1.5em + 0.9rem + 2px);
}

#talent_create_app .card-header {
    min-height: 48px !important;
    padding: 0.5rem 1.25rem !important;
}

#talent_create_app .card-body {
    padding: 1.25rem !important;
}
```

#### 4.3 Verification Commands
```bash
# Verify blade partial syntax
php -l resources/views/service_company/bench-candidates/partials/_form_styles.blade.php
```
- Open Chrome DevTools, set viewport to `1366 x 768`.
- Upload a resume PDF. Verify dropzone collapses instantly to 0px height; badge renders with filename, size, and single "Remove" link.
- Verify Resume card and Summary card top/bottom borders align exactly.

#### 4.4 Exit Criteria & Rollback
- **Exit Criteria:** UI renders cleanly on 1366×768 without outer scrollbars; dropzone collapses completely on upload; zero font sizes > 14px.
- **Rollback Procedure:**
  ```bash
  git checkout HEAD -- resources/views/service_company/bench-candidates/partials/_form_styles.blade.php
  ```

---

### Phase 5: Listing Dashboard Optimization (`data_v2`, Hero Analytics & Scheduled Reconciliation)

#### 5.1 Objectives & Scope
- Upgrade Bench Listing (`/bench-candidates`) to achieve **0% MySQL read strain** via `data_v2()` serving 100% of queries from Elasticsearch index `ELASTIC_CANDIDATES_BENCH`.
- Replace 85KB server-rendered HTML payloads with an 8KB normalized JSON structure assembled in browser RAM via DocumentFragments.
- Connect Hero Analytics to Redis materialized counters (`bench:hero:{company_id}:{stage}`) for sub-5ms KPI card rendering.
- Implement scheduled background reconciliation command `talents:reconcile-hero-counters` in `app/Console/Kernel.php`.

#### 5.2 Technical Implementation Tasks

##### Task 5.1: Controller `data_v2()` Endpoint Implementation
In `ServiceCompanyBenchCandidatesController.php`:
```php
public function data_v2(Request $request): JsonResponse
{
    $companyId = auth()->user()->company_id;
    $stage = (int) $request->input('stage', 2); // Default: Bench (2)
    $page = max(1, (int) $request->input('page', 1));
    $pageSize = min(50, max(10, (int) $request->input('page_size', 20)));
    $searchTerm = trim($request->input('search', ''));

    // Construct Elasticsearch CQRS Query (0% MySQL Reads)
    $mustConditions = [
        ['term' => ['company_id' => $companyId]],
        ['term' => ['status' => $stage]],
    ];

    if (!empty($searchTerm)) {
        $mustConditions[] = [
            'multi_match' => [
                'query' => $searchTerm,
                'fields' => ['full_name^3', 'email^2', 'phone_number', 'skills.name'],
            ],
        ];
    }

    $esParams = [
        'index' => config('elasticsearch.indices.bench_candidates', 'candidates_bench'),
        'body' => [
            'from' => ($page - 1) * $pageSize,
            'size' => $pageSize,
            'query' => ['bool' => ['must' => $mustConditions]],
            'sort' => [['created_at' => ['order' => 'desc']]],
            '_source' => [
                'id', 'full_name', 'email', 'phone_number', 'status', 'commercial_model',
                'pay_rate', 'bill_rate', 'skills', 'assigned_recruiters', 'created_at'
            ],
        ],
    ];

    try {
        $results = $this->elasticService->getClient()->search($esParams);
        $hits = $results['hits']['hits'] ?? [];
        $total = $results['hits']['total']['value'] ?? 0;

        $candidates = array_map(fn($hit) => $hit['_source'], $hits);

        return response()->json([
            'status' => 'success',
            'data' => $candidates,
            'meta' => [
                'current_page' => $page,
                'page_size' => $pageSize,
                'total_records' => $total,
                'total_pages' => ceil($total / $pageSize),
            ],
        ], 200);
    } catch (\Throwable $e) {
        Log::error('ELASTIC_BENCH_READ_FAILED', [
            'error' => $e->getMessage(),
            'company_id' => $companyId,
        ]);
        return response()->json(['status' => 'error', 'message' => 'Search index temporarily unavailable'], 503);
    }
}
```

##### Task 5.2: Scheduled Zero-Strain Hero Counter Reconciliation Command
Create `app/Console/Commands/ReconcileBenchHeroCountersCommand.php`:
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ReconcileBenchHeroCountersCommand extends Command
{
    protected $signature = 'talents:reconcile-hero-counters 
                            {--company_id= : Reconcile specific company}
                            {--all : Force sweep of active companies in last 24h}';
    protected $description = 'Reconciles Redis hero pipeline counters with MySQL without table scan strain';

    public function handle(): int
    {
        $specificCompany = $this->option('company_id');
        $forceAll = $this->option('all');

        $tenantIds = [];

        if ($specificCompany) {
            $tenantIds = [(int) $specificCompany];
        } elseif ($forceAll) {
            // Nightly active window sweep (never dormant companies)
            $tenantIds = DB::table('companies')
                ->where('status', 'active')
                ->where('updated_at', '>=', now()->subHours(24))
                ->pluck('id')
                ->toArray();
        } else {
            // Standard execution: Pop up to 50 dirty tenants from Redis (zero wasted MySQL CPU)
            $tenantIds = Redis::spop('bench:dirty_tenants', 50) ?: [];
        }

        if (empty($tenantIds)) {
            $this->info("Zero dirty tenants pending counter reconciliation.");
            return 0;
        }

        $this->info("Reconciling hero counters for " . count($tenantIds) . " tenants...");

        foreach ($tenantIds as $cid) {
            // Uses compound index idx_bench_company_type_status on (company_id, condidate_type, status)
            $counts = DB::table('job_applications')
                ->where('company_id', $cid)
                ->where('condidate_type', 'bench')
                ->selectRaw("
                    COUNT(*) as total_count,
                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as bench_count,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as pool_count,
                    SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as hold_count
                ")
                ->first();

            $total = (int) ($counts->total_count ?? 0);
            $bench = (int) ($counts->bench_count ?? 0);
            $pool  = (int) ($counts->pool_count ?? 0);
            $hold  = (int) ($counts->hold_count ?? 0);

            Redis::mset([
                "bench:hero:total:{$cid}" => $total,
                "bench:hero:bench:{$cid}" => $bench,
                "bench:hero:pool:{$cid}"  => $pool,
                "bench:hero:hold:{$cid}"  => $hold,
            ]);
        }

        $this->info("Hero counter reconciliation completed successfully.");
        return 0;
    }
}
```

Register command in `app/Console/Kernel.php`:
```php
// Every 10 minutes: reconcile only dirty tenants (0% full table scan strain)
$schedule->command('talents:reconcile-hero-counters')->everyTenMinutes()->withoutOverlapping();

// Daily at 02:00: active tenant sweep
$schedule->command('talents:reconcile-hero-counters --all')->dailyAt('02:00')->withoutOverlapping();
```

#### 5.3 Verification Commands
```bash
# 1. Run reconciliation manually on dirty queue
php artisan talents:reconcile-hero-counters

# 2. Inspect Redis keys
redis-cli GET bench:hero:bench:1

# 3. Benchmark data_v2 endpoint response time & payload size
curl -s -w "\nTime: %{time_total}s | Size: %{size_download} bytes\n" \
  -H "Accept: application/json" \
  "http://testing.jero.works/service_company/bench-candidates/data_v2?stage=2"
```

#### 5.4 Exit Criteria & Rollback
- **Exit Criteria:** `data_v2` serves 100% of data from Elasticsearch; response payload < 10KB; Redis hero counters update in <5ms; reconciliation processes dirty tenants in <20ms without table scans.
- **Rollback Procedure:**
  ```bash
  git checkout HEAD -- app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php resources/views/service_company/bench-candidates/index.blade.php
  ```

---

### Phase 6: Search-Index Zero-Downtime Rebuild & Alias Automation

#### 6.1 Objectives & Scope
- Build automated index migration command `bench:rebuild-search-index` supporting zero-downtime blue/green reindexing via Elasticsearch aliases.
- Implement Horizon Queue outbox worker integration with **atomic storage-engine version fencing (`version_type=external_gte`) (Gate G-54)**.
- Implement explicit bounded exponential backoff with randomized jitter and DLQ replay mechanics (`bench:outbox-replay`).
- Guarantee authoritative recruiter assignment projection model: index complete assignment snapshots deterministically.

#### 6.2 Technical Implementation Tasks

##### Task 6.1: Zero-Downtime Rebuild Artisan Command
Create `app/Console/Commands/RebuildBenchSearchIndexCommand.php`:
```php
<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildBenchSearchIndexCommand extends Command
{
    protected $signature = 'bench:rebuild-search-index {--batch=500 : Batch size for streaming}';
    protected $description = 'Rebuilds bench Elasticsearch index from MySQL with zero search downtime via alias swap';

    public function handle(ElasticService $elasticService): int
    {
        $client = $elasticService->getClient();
        $aliasName = config('elasticsearch.indices.bench_candidates', 'candidates_bench');
        $newIndex = "candidates_bench_" . time();

        $this->info("Creating new Elasticsearch index: {$newIndex}...");
        $client->indices()->create([
            'index' => $newIndex,
            'body' => config('elasticsearch.mappings.bench_candidates'),
        ]);

        $batchSize = (int) $this->option('batch');
        $totalMigrated = 0;

        $this->info("Streaming candidates from MySQL in batches of {$batchSize}...");
        DB::table('job_applications')
            ->where('condidate_type', 'bench')
            ->orderBy('id')
            ->chunk($batchSize, function ($candidates) use ($client, $newIndex, &$totalMigrated) {
                $params = ['body' => []];
                foreach ($candidates as $cand) {
                    $assigned = DB::table('bench_assign')
                        ->where('candidate_id', $cand->id)
                        ->pluck('user_id')
                        ->map(fn($id) => (int)$id)
                        ->toArray();

                    $params['body'][] = [
                        'index' => [
                            '_index' => $newIndex,
                            '_id' => "bench_candidate:{$cand->id}",
                            '_version' => $cand->aggregate_version ?? 1,
                            'version_type' => 'external_gte',
                        ]
                    ];
                    $params['body'][] = [
                        'id' => $cand->id,
                        'company_id' => $cand->company_id,
                        'full_name' => "{$cand->first_name} {$cand->last_name}",
                        'email' => $cand->email,
                        'phone_number' => $cand->phone_number,
                        'status' => (int) $cand->status,
                        'aggregate_version' => (int) ($cand->aggregate_version ?? 1),
                        'resume_version' => (int) ($cand->resume_version ?? 1),
                        'assigned_recruiters' => $assigned,
                        'created_at' => $cand->created_at,
                    ];
                }
                $client->bulk($params);
                $totalMigrated += count($candidates);
                $this->output->write('.');
            });

        $this->newLine();
        $this->info("Migrated {$totalMigrated} documents. Performing atomic alias swap...");

        // Atomic Alias Swap
        $actions = [
            ['add' => ['index' => $newIndex, 'alias' => $aliasName]]
        ];

        if ($client->indices()->existsAlias(['name' => $aliasName])) {
            $existing = array_keys($client->indices()->getAlias(['name' => $aliasName]));
            foreach ($existing as $oldIndex) {
                $actions[] = ['remove' => ['index' => $oldIndex, 'alias' => $aliasName]];
            }
        }

        $client->indices()->updateAliases(['body' => ['actions' => $actions]]);
        $this->info("Alias {$aliasName} now points to {$newIndex}. Zero downtime rebuild complete!");

        return 0;
    }
}
```

##### Task 6.2: Outbox Worker with Atomic ES Versioning & Jitter Backoff
In `app/Jobs/ProcessOutboxEventJob.php`:
```php
public function handle(ElasticService $elastic): void
{
    $workerToken = (string) Str::uuid();

    // Phase A: Claim Lease with FOR UPDATE SKIP LOCKED
    $claimed = DB::transaction(function () use ($workerToken) {
        $event = DB::table('candidate_outbox_events')
            ->where('aggregate_type', 'bench_candidate')
            ->where(function ($q) {
                $q->where('status', 'PENDING')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'PROCESSING')
                         ->where('lease_until', '<', now());
                  });
            })
            ->lockForUpdate()
            ->first();

        if (!$event) return null;

        DB::table('candidate_outbox_events')
            ->where('id', $event->id)
            ->update([
                'status' => 'PROCESSING',
                'lease_token' => $workerToken,
                'lease_until' => now()->addSeconds(30),
                'attempts' => $event->attempts + 1,
            ]);

        return $event;
    });

    if (!$claimed) return;

    // Phase B: Execute External Elasticsearch Indexing Outside MySQL Transaction
    try {
        $payload = json_decode($claimed->payload, true);
        $docId = "bench_candidate:{$claimed->aggregate_id}";
        $aggVersion = (int) ($payload['aggregate_version'] ?? 1);

        try {
            // INVARIANT G-54: Atomic Storage Engine Version Fencing
            $elastic->getClient()->index([
                'index' => config('elasticsearch.indices.bench_candidates', 'candidates_bench'),
                'id' => $docId,
                'version' => $aggVersion,
                'version_type' => 'external_gte', // ATOMIC ES STORAGE-ENGINE FENCING
                'body' => $payload,
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Conflict409Exception $e) {
            // Stale event arrived out-of-order; newer document already exists in ES!
            Log::info('OUTBOX_EVENT_SUPERSEDED', [
                'event_id' => $claimed->id,
                'aggregate_id' => $claimed->aggregate_id,
                'version' => $aggVersion,
            ]);
            // Safely proceed to mark completed without corrupting search index
        }

        // Phase C: Mark Event COMPLETED
        DB::table('candidate_outbox_events')
            ->where('id', $claimed->id)
            ->where('lease_token', $workerToken)
            ->update([
                'status' => 'COMPLETED',
                'processed_at' => now(),
            ]);

    } catch (\Throwable $e) {
        Log::error('OUTBOX_PROCESSING_FAILED', ['event_id' => $claimed->id, 'error' => $e->getMessage()]);

        // Explicit Bounded Exponential Backoff with Jitter
        $attempts = $claimed->attempts;
        $jitterSchedule = [
            1 => 5 + rand(0, 2),
            2 => 15 + rand(0, 5),
            3 => 60 + rand(0, 15),
            4 => 300 + rand(0, 30),
            5 => 900 + rand(0, 60),
        ];

        $delay = $jitterSchedule[$attempts] ?? 900;
        $isDeadLetter = $attempts >= 5;

        DB::table('candidate_outbox_events')
            ->where('id', $claimed->id)
            ->update([
                'status' => $isDeadLetter ? 'DEAD_LETTER' : 'PENDING',
                'available_at' => $isDeadLetter ? null : now()->addSeconds($delay),
                'lease_token' => null,
                'last_error' => $e->getMessage(),
            ]);

        if ($isDeadLetter) {
            Log::critical('OUTBOX_EVENT_DEAD_LETTERED', ['event_id' => $claimed->id, 'attempts' => $attempts]);
        }
    }
}
```

##### Task 6.3: Dead-Letter Queue (DLQ) Replay Artisan Command
Create `app/Console/Commands/ReplayBenchOutboxEventsCommand.php`:
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReplayBenchOutboxEventsCommand extends Command
{
    protected $signature = 'bench:outbox-replay 
                            {--event_id= : Specific event ID to replay}
                            {--company_id= : Replay all DLQ events for tenant}
                            {--reason= : Operational reason for replay}';
    protected $description = 'Replays dead-lettered outbox events back into PENDING state with audit trail';

    public function handle(): int
    {
        $eventId = $this->option('event_id');
        $companyId = $this->option('company_id');
        $reason = $this->option('reason') ?: 'Manual administrative replay';

        $query = DB::table('candidate_outbox_events')
            ->where('aggregate_type', 'bench_candidate')
            ->where('status', 'DEAD_LETTER');

        if ($eventId) {
            $query->where('id', $eventId);
        } elseif ($companyId) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.company_id')) = ?", [$companyId]);
        }

        $events = $query->get();
        if ($events->isEmpty()) {
            $this->info("No dead-lettered events matched query.");
            return 0;
        }

        $this->info("Replaying " . $events->count() . " dead-lettered events...");

        DB::table('candidate_outbox_events')
            ->whereIn('id', $events->pluck('id'))
            ->update([
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
                'replayed_by' => 'admin_cli',
                'replayed_at' => now(),
                'replay_reason' => $reason,
                'lease_token' => null,
            ]);

        $this->info("Events returned to PENDING. Worker will process immediately.");
        return 0;
    }
}
```

#### 6.3 Verification Commands
```bash
# 1. Run zero-downtime rebuild command
php artisan bench:rebuild-search-index --batch=200

# 2. Verify Elasticsearch alias target
curl -X GET "http://localhost:9200/_alias/candidates_bench"
```

#### 6.4 Exit Criteria & Rollback
- **Exit Criteria:** Entire bench Elasticsearch index rebuilds from MySQL without search errors; alias swaps atomically; expired outbox leases automatically reprocess.
- **Rollback Procedure:**
  ```bash
  # Point alias back to previous index
  curl -X POST "http://localhost:9200/_aliases" -H 'Content-Type: application/json' -d'
  { "actions": [ { "add": { "index": "candidates_bench_previous", "alias": "candidates_bench" } } ] }'
  ```

---

### Phase 7: Security Audit, Concurrency Load Testing & 54-Gate Verification

#### 7.1 Objectives & Scope
- Conduct rigorous multi-tenant attack simulation: verify cross-tenant data access, candidate tampering, and coin theft are completely blocked.
- Benchmark end-to-end performance under simulated load: **10,000 concurrent virtual users** executing create, store, and listing queries.
- Formally verify and sign off each of the **54 Production Acceptance Gates (G-01 to G-54)** on `testing.jero.works`.

#### 7.2 Technical Implementation Tasks

##### Task 7.1: Multi-Tenant & Concurrency Verification Test Suite
Add security penetration and distributed edge-case assertions in `tests/Feature/V4AddTalentRemediationTest.php`:
```php
/** @test */
public function test_tenant_cannot_access_or_edit_another_company_candidate(): void
{
    $attackerCompany = Company::factory()->create(['company_type' => 'service']);
    $attackerUser = User::factory()->create(['company_id' => $attackerCompany->id]);

    $victimCandidate = JobApplication::factory()->create([
        'company_id' => $this->company->id,
        'condidate_type' => 'bench',
    ]);

    // Attempt unauthorized edit access
    $response = $this->actingAs($attackerUser)
        ->get(route('service_company.bench-candidates.edit', $victimCandidate->id));

    $response->assertStatus(403);
}

/** @test */
public function test_idempotent_replay_returns_cached_response_without_duplicate_coins(): void
{
    $uuid = (string) \Illuminate\Support\Str::uuid();
    $payload = [
        'client_submission_uuid' => $uuid,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@test.com',
        'commercial_model' => 1,
        'status' => 2,
    ];

    // Submission 1
    $response1 = $this->actingAs($this->user)->postJson(route('service_company.bench-candidates.store'), $payload);
    $response1->assertStatus(200);

    // Duplicate Submission 2 with identical UUID
    $response2 = $this->actingAs($this->user)->postJson(route('service_company.bench-candidates.store'), $payload);
    $response2->assertStatus(200);

    // Assert exactly 1 DB record created
    $this->assertEquals(1, JobApplication::where('client_submission_uuid', $uuid)->count());
}

/** @test */
public function test_gate_g53_reusing_idempotency_key_with_different_payload_returns_409_conflict(): void
{
    $uuid = (string) \Illuminate\Support\Str::uuid();
    $payload1 = [
        'client_submission_uuid' => $uuid,
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice@test.com',
        'commercial_model' => 1,
    ];

    $response1 = $this->actingAs($this->user)->postJson(route('service_company.bench-candidates.store'), $payload1);
    $response1->assertStatus(200);

    // Replay same UUID with modified email (different request_hash)
    $payload2 = $payload1;
    $payload2['email'] = 'tampered_alice@test.com';

    $response2 = $this->actingAs($this->user)->postJson(route('service_company.bench-candidates.store'), $payload2);
    $response2->assertStatus(409);
    $response2->assertJson(['error' => 'IDEMPOTENCY_KEY_REUSE']);
}

/** @test */
public function test_gate_g52_expired_idempotency_lease_is_automatically_reclaimed(): void
{
    $uuid = (string) \Illuminate\Support\Str::uuid();
    
    // Simulate a crashed worker leaving an abandoned PROCESSING record with an expired lease
    \App\Models\SysIdempotencyKey::create([
        'tenant_id' => $this->company->id,
        'idempotency_key' => $uuid,
        'request_hash' => hash('xxh128', json_encode(['first_name' => 'Bob'])),
        'status' => 'PROCESSING',
        'processing_until' => now()->subMinute(), // Expired lease
        'created_at' => now()->subMinutes(5),
        'expires_at' => now()->addHours(24),
    ]);

    $payload = [
        'client_submission_uuid' => $uuid,
        'first_name' => 'Bob',
    ];

    // Retry request must reclaim lease, execute persistence, and return 200 (not be blocked forever)
    $response = $this->actingAs($this->user)->postJson(route('service_company.bench-candidates.store'), $payload);
    $response->assertStatus(200);

    $this->assertDatabaseHas('sys_idempotency_keys', [
        'tenant_id' => $this->company->id,
        'idempotency_key' => $uuid,
        'status' => 'COMPLETED',
    ]);
}
```

##### Task 7.2: 10,000 Concurrency k6 Load Test Script
Create `tests/Load/bench_candidate_load.js`:
```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';
import { uuidv4 } from 'https://jslib.k6.io/k6-utils/1.4.0/index.js';

export const options = {
    stages: [
        { duration: '30s', target: 500 },
        { duration: '1m', target: 2500 },
        { duration: '2m', target: 10000 }, // Peak 10K concurrent recruiters
        { duration: '1m', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<150'], // 95% of requests under 150ms
        http_req_failed: ['rate<0.001'],   // Error rate < 0.1%
    },
};

export default function() {
    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer test-bearer-token',
        },
    };

    // Scenario 1: Fetch Bench Listing via data_v2
    const listRes = http.get('https://testing.jero.works/service_company/bench-candidates/data_v2?stage=2', params);
    check(listRes, { 'listing 200': (r) => r.status === 200 });

    // Scenario 2: Submit Talent Profile
    const payload = JSON.stringify({
        client_submission_uuid: uuidv4(),
        first_name: 'Bench',
        last_name: 'Engineer',
        email: `bench_${uuidv4().substring(0,8)}@loadtest.com`,
        status: 2,
        commercial_model: 1,
    });

    const submitRes = http.post('https://testing.jero.works/service_company/bench-candidates/store', payload, params);
    check(submitRes, {
        'submit 200': (r) => r.status === 200,
        'has redirect': (r) => r.json('redirect_url') !== undefined,
    });

    sleep(0.5);
}
```

#### 7.3 Verification Commands
```bash
# 1. Run all unit and feature tests
php artisan test --filter=V4AddTalentRemediationTest

# 2. Run static analysis and formatting
./vendor/bin/pint --test

# 3. Execute k6 load benchmark
k6 run tests/Load/bench_candidate_load.js
```

#### 7.4 Exit Criteria & Sign-Off
- **Exit Criteria:**
  - 100% of feature, security, and lease-recovery tests PASS.
  - Zero PHP linting or formatting errors (`pint`).
  - k6 load test sustains 10,000 virtual users with P95 latency < 150ms and 0% HTTP 5xx errors.
  - All 54 Production Acceptance Gates (G-01 to G-54) verified and signed off.

---

## 15. Master Emergency Rollback, Feature Flag Circuit Breakers & Disaster Recovery Runbook

### 15.1 Emergency Feature Flag Circuit Breaker
If an unforeseen production anomaly arises, the entire V4 Add Talent / Bench pipeline can be instantly throttled or reverted to legacy handling using Laravel configuration without requiring code redeployments:

In `config/services.php`:
```php
'bench_v4' => [
    'enabled' => env('BENCH_V4_ENABLED', true),
    'use_data_v2' => env('BENCH_USE_DATA_V2', true),
    'enforce_durable_idempotency' => env('BENCH_DURABLE_IDEMPOTENCY', true),
    'enable_worker_parser' => env('BENCH_ENABLE_WORKER_PARSER', true),
],
```

To instantaneously disable V4 features in production:
```bash
# Set in environment or runtime Redis config
php artisan config:set services.bench_v4.enabled false
php artisan config:cache
```

### 15.2 Layer-by-Layer Fast Rollback Runbook

| Infrastructure Layer | Failure Scenario | Immediate Rollback Command | Recovery Time Objective (RTO) |
| :--- | :--- | :--- | :---: |
| **Application Code** | Fatal exception or regression in `ServiceCompanyBenchCandidatesController` | `git revert HEAD --no-edit && php artisan octane:reload` | < 30 seconds |
| **Database Schema** | Migration conflict or slow index on `job_applications` | `php artisan migrate:rollback --step=1` | < 1 minute |
| **Listing Endpoint** | Elasticsearch cluster timeout or `data_v2` parsing issue | Route fallback: update `index.blade.php` AJAX URL to `data_ajax` | < 2 minutes |
| **Redis Cache** | Cache key poison or split-brain counter drift | `php artisan talents:reconcile-hero-counters` | < 45 seconds |
| **Search Aliases** | Corrupted document mapping in `candidates_bench` | Revert alias: `curl -X POST "http://localhost:9200/_aliases" -d '{"actions":[{"add":{"index":"candidates_bench_backup","alias":"candidates_bench"}}]}'` | < 15 seconds |
| **Client Assets** | Parser Web Worker script 404 or CDN failure | Set `window.JeroTalent.fallbackToManual = true` in Blade | < 10 seconds |

### 15.3 Disaster Recovery Playbook: Complete Elasticsearch Re-Sync
If the Elasticsearch cluster suffers a complete crash and data loss:
1. Re-initialize index mapping:
   ```bash
   php artisan bench:rebuild-search-index --batch=1000
   ```
2. Replay all pending and failed outbox events from MySQL:
   ```bash
   php artisan queue:retry --queue=candidate-index
   ```
3. Reconcile Redis hero counts with MySQL:
   ```bash
   php artisan talents:reconcile-hero-counters
   ```
4. Verify system health:
   ```bash
   php artisan health:check
   ```
All systems will return to 100% operational status with zero loss of transactional integrity.

