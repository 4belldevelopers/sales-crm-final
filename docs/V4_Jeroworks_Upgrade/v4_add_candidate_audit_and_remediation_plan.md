# V4 Add Candidate Flow: Production Engineering Audit, Architecture Specification & Implementation Blueprint

> **Document Version:** 4.6.0 (Production Implementation Contract: Streamlined Remove Lifecycle, Outbox Fencing & Strict Subsystem Isolation)  
> **Status:** Authoritative Production Engineering Contract & Implementation Master Specification  
> **Target Module:** Service Company Add & Edit Candidate Page & Submission Pipeline (`resources/views/service_company/job-applications/create.blade.php`)  
> **Target Environment:** `testing.jero.works`  
> **Primary Source Files:**  
> - `resources/views/service_company/job-applications/create.blade.php` (Metronic 8 ATS Add/Edit Candidate Blade)  
> - `resources/views/service_company/job-applications/jero-client-parser.blade.php` (Working 100% Client-Side Resume Parser Integration Bridge)  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`create()`, `store()`, `update()`, `uploadresume()`)  
> - `public/js/resume-presign.js` (Cloudflare R2 Direct PUT Presigned Upload Engine)  
> - `database/schema/mysql-schema.sql` (Authoritative MySQL database schema)  
> **Dedicated Companion Subsystem (Strictly Isolated & Off-Limits to Regressions):**  
> - `docs/V4_Jeroworks_Upgrade/100_percent_client_side_resume_parsing.md` & `test_parser_jero_works.md`  
>   *(Governs the operational in-browser WASM/NLP parser at `https://parser.jero.works/` — working perfectly in production).*  
> **Explicit Scope Boundary:**  
> - Per architectural directive, **`product_company` and `staffing_company` are strictly OUT OF SCOPE and must be ignored**. All changes are quarantined to `service_company`.

---

## 1. Executive Summary & Core Architectural Principles

### 1.1 The Golden Architectural Axiom
> **"MySQL is the system of record. Redis accelerates. Cloudflare R2 stores files. Outbox guarantees events. Elasticsearch is a derived read model."**

This single axiom governs all engineering decisions across the Add Candidate and persistence flow.

### 1.2 Separation of Concerns: Upload Session vs. Resume Asset vs. Resume Mutation
A common architectural flaw is attempting to force every resume concept into a single state column. We strictly separate three distinct domains:
1. **Upload Session State:** Manages the ephemeral transport of raw file bytes from browser to Cloudflare R2 (`CREATED`, `PRESIGNED`, `UPLOADING`, `UPLOADED`, `VERIFIED`, `PENDING_ATTACHMENT`, `CLAIMED`, `ATTACHED`, `FAILED`, `RETRYABLE`).
2. **Resume Asset Lifecycle:** Manages the durable database entity and versioned attachment to a candidate (`ACTIVE`, `REMOVAL_PENDING`, `REMOVED`, `ORPHANED`, `CLEANUP_PENDING`).
3. **Resume Mutation Intent:** The explicit recruiter action dispatched to the server (`KEEP`, `REMOVE`, `ATTACH_IF_AVAILABLE`, `DISCARD`, `NONE`).

> **UI & UX Principle (Remove Only):** In alignment with the Metronic ATS design, once a resume is uploaded, **there is NO 'Replace' button or condition**. The UI exposes strictly a single **Remove** action (`#upRemoveBtn` / `#removeFile`). A recruiter may either keep the uploaded resume or remove it. If a recruiter removes a resume, the UI reverts back to the clean dropzone/empty state (`backToDropzone()`), allowing a fresh file selection if desired.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                              JERO V4 ADD CANDIDATE ARCHITECTURE MATRIX                                 │
│                                                                                                        │
│  ┌─────────────────────────┐  ┌─────────────────────────┐  ┌────────────────────────────────────────┐  │
│  │ PIPELINE 1:             │  │ PIPELINE 2:             │  │ PIPELINE 3:                            │  │
│  │ CLIENT RESUME PARSER    │  │ STORAGE PERSISTENCE     │  │ ADD CANDIDATE FORM & UI                │  │
│  │ (Status: LOCKED / PRESERVED)│ (Status: ACTIVE / ISOLATED)│  │ (Status: TARGET FOR REMEDIATION)       │  │
│  │                         │  │                         │  │                                        │  │
│  │ • 100% Browser RAM/WASM │  │ • Direct Browser-to-R2  │  │ • Form Fields & Personal Details       │  │
│  │ • PDF.js + Mammoth NLP  │  │   Presigned PUT Upload  │  │ • Dynamic Salary Mode Math (INR/USD)   │  │
│  │ • JeroCandidateFormBridge│  │ • Background Execution │  │ • Notice Period & Experience Logic     │  │
│  │ • Untrusted Browser     │  │ • 10-State Upload Session│ │ • Local Sharded Geo & Job Skills Trie  │  │
│  │   Input Policy          │  │ • Durable `resume_assets`│  │ • Metronic 8 FormValidation            │  │
│  │ • Protected by Contract │  │ • Server Reconciliation │  │ • Zero Dynamic Requests per Keystroke  │  │
│  │   Tests (Phase 0)       │  │   Worker (Crash-Proof)  │  │ • Single 'Remove' Action UI            │  │
│  │                         │  │ • Dual Cleanup Classes  │  │ • 100% Usable With or Without Resume   │  │
│  └────────────┬────────────┘  └───────────┬─────────────┘  └───────────────────┬────────────────────┘  │
│               │ (Autofills empty inputs)   │ (Provides verified asset ID)      │                       │
│               ▼                            ▼                                   ▼                       │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ PIPELINE 4: HIGH-PERFORMANCE TRANSACTIONAL PERSISTENCE                                           │  │
│  │             + POST-COMMIT EVENT PROCESSING + OUTBOX-BASED SEARCH INDEXING                        │  │
│  │ (Status: TARGET FOR REMEDIATION)                                                                 │  │
│  │                                                                                                  │  │
│  │ • Instant Form Submission (0ms artificial delay; removed setTimeout 2000ms)                      │  │
│  │ • 3-Layer Idempotency (Client UUID → Bounded Redis NX Lock → MySQL UNIQUE Constraint + Replay)   │  │
│  │ • Optimistic Locking on `resume_version` (Eliminates multi-tab & stale mutation conflicts)       │  │
│  │ • Fast MySQL Transaction: INSERT `job_applications` + INSERT `candidate_outbox_events`           │  │
│  │ • Zero Remote Service Network Calls inside MySQL Transaction (Duration P95 < 15ms)               │  │
│  │ • IMMEDIATE HTTP 200 JSON Redirect Response to Recruiter (Target P95 < 150ms)                   │  │
│  │ • Outbox Worker with Two-Step `SKIP LOCKED`, Lease Token Fencing & Event Deduplication Key       │  │
│  │ • Outbox Worker & Horizon Queues on Redis: Async, Repeatable, Idempotent Elasticsearch Upsert    │  │
│  └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1.3 Core Architectural Invariant: Zero Remote Dependencies at Submission Time
> **Critical Invariant:** The candidate database record must **NEVER** depend on Elasticsearch, SMTP email servers, Cloudflare R2 upload completion, or any other remote network service being operational at submission time.

If Elasticsearch is down, if R2 has a transient timeout, or if Redis queue workers are restarting, **the candidate record in MySQL must commit reliably**, and the recruiter must receive a prompt success redirect. Background workers and outbox reconciliation guarantee eventual consistency without recruiter downtime.

---

### 1.4 The 360-Degree Lifecycle: From "Click Add Candidate" to "Candidate Submitted & Live"

This architecture covers the **entire recruiter journey end-to-end**, guaranteeing that the page loads instantly, operates with zero server strain, and submits instantly without failing under 10,000 concurrency:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                        THE 360° RECRUITER JOURNEY (CLICK-TO-LOAD → INSTANT SUBMIT)                     │
│                                                                                                        │
│  [ STEP 1: CLICK 'ADD CANDIDATE' ] ──► Instant Page Load (P95 < 50ms)                                  │
│   • 0% MySQL Reads: Job context, skills, and templates loaded from Redis / ReferenceDataCache         │
│   • Octane / FrankenPHP delivers pre-compiled Blade view directly from RAM                             │
│   • Zero DB connection pool strain even if 10,000 recruiters open the page simultaneously             │
│   • Browser pre-warms parser via requestIdleCallback while user looks at the screen                   │
│                                                                                                        │
│  [ STEP 2: IN-PAGE DATA ENTRY & PARSE ] ──► 0% Server Strain (User RAM + Edge)                         │
│   • Resume Parsing: 100% in-browser RAM (WASM/PDF.js/NLP) — 0 PHP, 0 MySQL, 0 ES                     │
│   • Resume Upload: Direct browser-to-Cloudflare R2 PUT (0 PHP bytes, external edge storage)            │
│   • Skills & Locations: In-memory Trie over local cache — 0 dynamic API requests per keystroke         │
│   • Salary Math: 100% Client-side JavaScript calculators (60 FPS responsiveness)                       │
│                                                                                                        │
│  [ STEP 3: CLICK 'SUBMIT CANDIDATE' ] ──► Instant Dispatch & Persistence (P95 < 150ms)                 │
│   • 0ms Artificial Delay: Removed setTimeout(..., 2000) submit lag                                    │
│   • 3-Layer Idempotency: Client UUID + Bounded Redis NX (50ms) + MySQL UNIQUE constraint               │
│   • Sub-15ms MySQL Transaction: Pure local DB write (job_applications + candidate_outbox_events)       │
│   • Immediate Recruiter Redirect: HTTP 200 JSON with AppTracker URL returned in < 150ms               │
│   • Real-Time Pipeline Freshness: Post-commit cache wipe + Async high-priority queue indexing (<500ms)│
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Non-Negotiable Resume Invariants (R-I01 to R-I15)

Every line of code written for the Add/Edit candidate flow must adhere to these invariants:

* **R-I01:** A candidate can have at most one `ACTIVE` canonical resume at any point in time.
* **R-I02:** An R2 object is never authoritative; MySQL is the sole authority for ownership and attachment.
* **R-I03:** A late upload can never resurrect a removed resume.
* **R-I04:** A stale mutation can never overwrite a newer resume version (optimistic locking enforced).
* **R-I05:** A parser result is valid only for the active upload session that generated it; mismatched runs are discarded.
* **R-I06:** Removed resumes are never physically deleted inside the transaction; deletion is handled asynchronously post-commit.
* **R-I07:** Removing a resume cannot delete or mutate candidate profile data.
* **R-I08:** Physical R2 deletion failure cannot fail or corrupt the MySQL database transaction.
* **R-I09:** Browser crashes can never create permanent ownership ambiguity or attach files to random candidates.
* **R-I10:** Every resume mutation (`KEEP`, `REMOVE`) is strictly idempotent.
* **R-I11:** Every resume mutation enforces authoritative server-side tenancy verification.
* **R-I12:** Every resume removal emits a dedicated, auditable outbox event (`resume.removed`).
* **R-I13:** Only the canonical `ACTIVE` resume is exposed by default across APIs, drawers, and download centers.
* **R-I14:** Elasticsearch documents, caches, and read models must be completely rebuildable from MySQL resume state.
* **R-I15:** A candidate record is a first-class entity and can exist permanently without a resume.

---

## 3. Complete 10-Scenario Matrix (Add, Edit, Remove & Races)

| Scenario | Operation & Trigger | Upload Status | `resume_intent` | Persistence & Asset Lifecycle | Recruiter Outcome |
| :---: | :--- | :--- | :---: | :--- | :--- |
| **A** | Add + Parsed Resume | `VERIFIED` | `ATTACH_IF_AVAILABLE` | Candidate created; ResumeAsset created as `ACTIVE`; Outbox event created | Instant 200 JSON Redirect; Candidate in AppTracker with resume |
| **B** | Add + Failed Parser | `VERIFIED` | `ATTACH_IF_AVAILABLE` | Candidate created with manual fields; ResumeAsset `ACTIVE`; Outbox created | Instant 200 JSON Redirect; Candidate in AppTracker with resume |
| **C** | Add + Upload Failed | `FAILED` | `ATTACH_IF_AVAILABLE` | Candidate saved in MySQL; UI prompts to retry resume or saves without | Candidate created; resume flagged for retry/re-upload |
| **D1**| Add + In-Flight Upload | `UPLOADING` | `ATTACH_IF_AVAILABLE` | Candidate saved; session marked `PENDING_ATTACHMENT`; background links | Instant submit; resume attaches asynchronously without blocking |
| **D2**| Add + In-Flight Upload | `UPLOADING` | `DISCARD` | Candidate saved with `resume = ''`; upload session abandoned/purged | Instant submit; resume is permanently omitted per recruiter intent |
| **E** | Add Without Resume | `NONE` | `NONE` | Candidate saved with `resume = ''`; Outbox event created | Instant 200 JSON Redirect; Candidate in AppTracker without resume |
| **F** | Edit (Keep Resume) | Existing | `KEEP` | Candidate fields updated; `current_resume_id` unchanged; version incremented | Candidate updated; return to previous grid view |
| **G** | Edit (Remove Resume) | Existing | `REMOVE` | `current_resume_id = NULL`; Old asset → `REMOVAL_PENDING`; Outbox `resume.removed` | Candidate updated; resume detached; R2 cleaned async |
| **H** | Add: Remove Pre-Submit| Cleaned | `NONE` | Dropzone resets; session marked `DISCARDED`; 0 rows saved | Form clear; dropzone restored to initial state |
| **I** | Remove While Uploading| In-Flight | `DISCARD` | In-flight XHR aborted; session marked `DISCARDED`; dropzone reset | Clean dropzone state; late packets discarded safely |
| **J** | Double-Click / Retry | Same UUID | Any | Redis lock intercepts or MySQL UNIQUE constraint triggers idempotent replay | Returns original success response; zero duplicate candidates |

---

## 4. Deep-Dive Implementation Master Specifications

### 4.1 Durable Resume Asset Identity & Schema

#### The `resume_assets` Table Definition
```sql
CREATE TABLE `resume_assets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT UNSIGNED NOT NULL,
  `candidate_id` INT UNSIGNED NULL,
  `upload_session_id` VARCHAR(64) NULL,
  `object_key` VARCHAR(500) NOT NULL,
  `original_filename` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` BIGINT UNSIGNED NOT NULL,
  `checksum_sha256` CHAR(64) NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `status` ENUM('ACTIVE', 'REMOVAL_PENDING', 'REMOVED', 'ORPHANED', 'CLEANUP_PENDING') NOT NULL DEFAULT 'ACTIVE',
  `parser_status` ENUM('PARSED', 'FAILED', 'BYPASSED') NOT NULL DEFAULT 'BYPASSED',
  `parser_version` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attached_at` TIMESTAMP NULL DEFAULT NULL,
  `removed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_resume_candidate` (`candidate_id`, `status`),
  KEY `idx_resume_company_session` (`company_id`, `upload_session_id`),
  KEY `idx_resume_cleanup` (`status`, `removed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Alterations to `job_applications`
```sql
ALTER TABLE `job_applications`
  ADD COLUMN `current_resume_id` BIGINT UNSIGNED NULL AFTER `resume`,
  ADD COLUMN `resume_version` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `current_resume_id`,
  ADD COLUMN `client_submission_uuid` VARCHAR(36) NULL AFTER `resume_version`,
  ADD UNIQUE KEY `uq_company_submission_uuid` (`company_id`, `client_submission_uuid`),
  ADD KEY `idx_job_app_current_resume` (`current_resume_id`);
```
> **Backward Compatibility Guarantee:** `$jobApplication->resume` (storing the active Cloudflare R2 URL) is **preserved and kept in sync** with `current_resume_id`. Existing blades, drawers, and APIs that read `$candidate->resume` continue to work with 100% fidelity.

---

### 4.2 Atomic Remove Semantics

When a recruiter clicks **Remove** on an existing resume in Edit mode:
```php
DB::beginTransaction();
try {
    // 1. Fetch current candidate with lock
    $candidate = DB::table('job_applications')->where('id', $candidateId)->lockForUpdate()->first();
    $oldResumeId = $candidate->current_resume_id;

    // 2. Mark existing ResumeAsset as REMOVAL_PENDING
    if ($oldResumeId) {
        DB::table('resume_assets')->where('id', $oldResumeId)->update([
            'status'     => 'REMOVAL_PENDING',
            'removed_at' => now(),
        ]);
    }

    // 3. Update candidate pointer & increment resume_version (Optimistic Lock)
    $updated = DB::table('job_applications')
        ->where('id', $candidateId)
        ->where('resume_version', $candidate->resume_version)
        ->update([
            'current_resume_id' => null,
            'resume'            => '',
            'resume_version'    => $candidate->resume_version + 1,
            'updated_at'        => now(),
        ]);

    if (! $updated) {
        throw new StaleMutationException('Concurrent update detected. Please refresh.');
    }

    // 4. Insert outbox events for removal and async cleanup
    DB::table('candidate_outbox_events')->insert([
        'company_id'     => $companyId,
        'dedupe_key'     => "candidate:{$candidateId}:resume_removed:v{$candidate->resume_version}",
        'aggregate_type' => 'Candidate',
        'aggregate_id'   => $candidateId,
        'event_type'     => 'resume.removed',
        'payload'        => json_encode([
            'candidate_id'  => $candidateId,
            'old_resume_id' => $oldResumeId,
        ]),
    ]);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

---

### 4.3 Race Condition Defenses

```
Race 1: Late Old PUT After User Clicked Remove
 Recruiter clicks REMOVE ──► Candidate.current_resume_id = NULL (COMMITTED)
 Late PUT arrives at R2 ──► Session marked UPLOADED
 Reconciliation Worker ──► Sees session was DISCARDED or Candidate has current_resume_id = NULL
 Result: Object marked ORPHANED. Candidate resume is NEVER resurrected.

Race 2: Multi-Tab Stale Edit / Remove Mutation
 Tab 1 submits Remove (version 1 -> 2) ──► Succeeds. DB version = 2.
 Tab 2 submits Edit (version 1 -> 2) ────► WHERE resume_version = 1 affects 0 rows!
 Result: StaleMutationException thrown. Tab 2 cannot overwrite newer state.

Race 3: Late Parser Result from Removed File
 File starts parsing ────► parser_run_id = 'run_A'
 Recruiter clicks REMOVE ─► current_parser_run_id set to null; fields reset
 Parser finishes late ───► Bridge checks: if (current_parser_run_id !== 'run_A') discard!
 Result: Parser result dropped. Form fields NEVER populated from removed file.
```

---

### 4.4 3-Layer Idempotency & Bounded Timeout Protection

```
Client UUIDv4
     │
     ▼
Layer 1: Frontend Guard & Form UUID
 - crypto.randomUUID() generated on blade initialization
 - Create mode: `client_submission_uuid` | Edit mode: `client_update_uuid`
 - Form submit button disabled with Metronic spinner indicator
     │
     ▼
Layer 2: Redis Atomic In-Flight Lock (Accelerator with Bounded Timeout)
 - Strict Connection & Read Timeout: 50 ms maximum
 - Redis::set("idemp:cand:{$companyId}:{$uuid}", 'PROCESSING', 'EX', 120, 'NX')
 - If key exists and status == 'PROCESSING': Return HTTP 409 (Submission in progress)
 - If key exists and status == 'COMPLETED': Replay cached redirect response
 - Fallback Rule: If Redis connection drops or times out (> 50ms), FAIL OPEN immediately
   to Layer 3 (MySQL authority) without destroying the submission SLA (< 150ms)
     │
     ▼
Layer 3: MySQL Authoritative UNIQUE Constraint
 - Unique Index: `UNIQUE KEY uq_company_submission_uuid (company_id, client_submission_uuid)`
 - Guarantees storage-level impossibility of duplicate candidate records
     │
     ▼
Idempotent Replay on MySQL Constraint Violation
 - If MySQL throws QueryException (Error 1062 Duplicate entry):
   1. Catch exception deterministically in Controller::store()
   2. Query existing candidate by `(company_id, client_submission_uuid)`
   3. Verify tenant security ownership (`company_id == auth()->user()->company_id`)
   4. Replay original HTTP 200 JSON success redirect response
   5. Zero 500 errors returned to recruiter on network retries
```

---

### 4.5 Transactional Outbox Pattern: Fencing Tokens, Dedupe Key & Two-Step `SKIP LOCKED`

#### Outbox Table Schema Definition (`candidate_outbox_events`)
```sql
CREATE TABLE `candidate_outbox_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT UNSIGNED NOT NULL,
  `dedupe_key` VARCHAR(191) NOT NULL,
  `aggregate_type` VARCHAR(50) NOT NULL DEFAULT 'Candidate',
  `aggregate_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `event_version` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `payload` JSON NOT NULL,
  `status` ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
  `retry_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `available_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locked_at` TIMESTAMP NULL DEFAULT NULL,
  `locked_by` VARCHAR(100) NULL DEFAULT NULL,
  `lock_token` VARCHAR(36) NULL DEFAULT NULL,
  `last_error` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_outbox_dedupe` (`dedupe_key`),
  KEY `idx_outbox_claim` (`status`, `available_at`, `locked_at`),
  KEY `idx_outbox_company_aggregate` (`company_id`, `aggregate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Outbox Worker Two-Step `SKIP LOCKED` & Fencing Lease Protocol
```sql
-- Step 1: Claim batch inside short transaction
START TRANSACTION;
SELECT id FROM `candidate_outbox_events`
WHERE `status` = 'pending' AND `available_at` <= NOW()
  AND (`locked_at` IS NULL OR `locked_at` < NOW() - INTERVAL 5 MINUTE)
ORDER BY `id` ASC LIMIT 50 FOR UPDATE SKIP LOCKED;

UPDATE `candidate_outbox_events`
SET `status` = 'processing', `locked_at` = NOW(), `locked_by` = :worker_id, `lock_token` = :lock_token
WHERE `id` IN (:selected_ids);
COMMIT;

-- Step 2: Complete event using fencing token check
UPDATE `candidate_outbox_events`
SET `status` = 'completed', `updated_at` = NOW()
WHERE `id` = :id AND `locked_by` = :worker_id AND `lock_token` = :lock_token;
```
If a lock expires and Worker B takes over with a new `lock_token`, Worker A's late write affects 0 rows and is rejected safely.

---

### 4.6 Dual R2 Cleanup Classes

```
Class 1: Temporary Ephemeral Cleanup (candidate-temp/*)
 - Cloudflare R2 Bucket Lifecycle Policy: 24-Hour Expiration TTL.
 - Automatically deletes abandoned uploads without running cron jobs.

Class 2: Removed Asset Cleanup (candidate-resumes/*)
 - Triggered when a ResumeAsset enters status 'REMOVAL_PENDING' or 'REMOVED'.
 - Managed by background job `PurgeRemovedResumeAssetsJob` with 7-day retention grace period.
 - Guarantees that accidental removals can be recovered by support before permanent object purge.
```

---

### 4.7 Targeted Cache Invalidation Discipline

When resume removal or candidate updates occur, the following cache keys are invalidated strictly post-commit:
* `candidate:{id}`
* `candidate:{id}:resume`
* `allcvtracker_version_user_{userId}`
* `allapptracker_version_user_{userId}`

This guarantees that AppTracker, Candidate Drawer, and Resume Download Center remain 100% synchronous with zero stale resume URLs.

---

### 4.8 Client Hardware, Device & Memory Resource Budgets

To protect 15.6" recruiter laptops from memory exhaustion:
* **Target Hardware Profile:** Enterprise 15.6" laptops (1366×768 and 1920×1080) with 4 GB to 8 GB RAM.
* **Component Budgets:**
  - **JavaScript Active Heap:** **< 40 MB**
  - **WASM Linear Memory:** **< 32 MB**
  - **Detached ArrayBuffer Snapshot:** Bounded strictly to file size (max 15 MB)
  - **Total Browser Tab RSS Overhead:** **< 120 MB above baseline**

---

### 4.9 IndexedDB Reference Store & Multi-Tab Session Isolation Architecture

#### 4.9.1 In-Browser IndexedDB Engine (`jero_client_ref_v4`)
To achieve true 0% server strain during recruiter data entry, static datasets are offloaded to client browser storage:
* **Database Name:** `jero_client_ref_v4`
* **Object Store 1 (`canonical_skills`):** Pre-caches ~3,000 canonical industry skills and common aliases. Queried via an in-memory prefix Trie in a dedicated Web Worker (< 5ms match latency).
* **Object Store 2 (`geo_shards`):** Stores static country and state/city JSON shards progressively loaded from CDN.
* **Storage Footprint:** < 15 MB total compressed disk space; zero impact on device performance.
* **Cache Invalidation:** Versioned via database schema upgrade events (`onupgradeneeded`). When backend dictionaries update, client seamlessly downloads new shards in background.

#### 4.9.2 Multi-Tab Session Isolation & Anti-Collision Engine
A critical real-world ATS workflow is recruiters opening **multiple Add Candidate tabs simultaneously** (e.g. comparing profiles, drafting multiple candidates for the same job requisition).

* **The Multi-Tab Hazard in Legacy Implementations:**  
  Relying on domain-wide `localStorage` keys (e.g. `resume_v2_session_comp1_job2_app0`) means all open tabs for that job share the exact same key. Opening Tab 2 calls `cancelUpload()`, silently clobbering or wiping Tab 1's active upload or parsed form state!
* **The Multi-Tab Solution (Tab-Unique Scoping via `sessionStorage` & RAM):**
  1. **Tab Instance Identity:** Each tab generates a unique `tab_instance_id` (UUIDv4) stored strictly in window RAM on initial DOM render.
  2. **Tab-Scoped Storage (`sessionStorage`):** All in-flight upload session UUIDs, idempotency tokens, and temporary state are stored in `sessionStorage` (which the browser sandboxes strictly to that individual tab):
     ```javascript
     const tabSessionKey = `resume_v2_${tabInstanceId}`;
     sessionStorage.setItem(tabSessionKey, sessionUuid);
     ```
  3. **Unique `client_submission_uuid` Per Tab:** Every open tab generates its own cryptographic submission UUID. Tab 1 and Tab 2 submit completely distinct, idempotent transaction keys.
  4. **Multi-Tab Independence Guarantee:** A recruiter can open 2, 5, or 20 Add Candidate tabs simultaneously. Each tab maintains **100% session isolation**:
     - Uploading a resume in Tab 1 never touches or affects Tab 2.
     - Resetting or removing a resume in Tab 2 never cancels Tab 1.
     - Submitting Tab 1 commits safely without invalidating Tab 2's draft.

---

## 5. Concurrency Profiling & Corpus-Based Parser SLAs

### 5.1 Concurrency Profiling Distinction
* **10,000 Concurrent Active Sessions:** Recruiters holding the Add Candidate page open, typing into skills, reviewing job requirements, and running local parser tasks (generates **0 dynamic QPS** to MySQL or Elasticsearch).
* **1,000 Simultaneous Submissions:** Recruiters submitting candidates at the exact same second (stress-tests Redis idempotency lock, MySQL transaction duration, and Outbox event queue throughput).

### 5.2 Measurable Engineering SLA Targets
| Metric | Target SLA (P95) | Target SLA (P99) | Measurement Tool |
| :--- | :---: | :---: | :--- |
| **MySQL Transaction Duration** | **< 15 ms** | **< 35 ms** | Laravel APM / MySQL Slow Query Log |
| **Submission HTTP API Response**| **< 150 ms** | **< 300 ms** | Chrome DevTools Timings / Access Logs |
| **R2 Direct PUT Upload Time** | **< 1,500 ms** | **< 3,000 ms** | Network XHR Progress Observer |
| **Dynamic API Requests (Typing)**| **0 requests** | **0 requests** | DevTools Network Tab / Nginx Access Log |
| **Outbox-to-ES Indexing Lag** | **< 500 ms** | **< 2,000 ms** | Horizon Queue Metrics / Elasticsearch Ingest |

### 5.3 Parser SLA Targets by Document Test Corpus
| Corpus Category | Characteristics | Target SLA (P95) | Target SLA (P99) |
| :--- | :--- | :---: | :---: |
| **Small Clean PDF** | 1–2 pages, clean vector text (< 2 MB) | **< 400 ms** | **< 700 ms** |
| **Medium Complex PDF** | 2–4 pages, multi-column tables (2–5 MB) | **< 800 ms** | **< 1,500 ms** |
| **Large/Heavy DOCX** | Multi-table, XML styles (< 10 MB) | **< 1,500 ms** | **< 2,500 ms** |
| **Complex/Scanned PDF**| Image layers, layout reconstruction (< 15 MB) | **< 3,000 ms** | **< 5,000 ms** |

---

## 6. Formal 32-Gate Production Acceptance Matrix

Every single gate must achieve an authoritative PASS before production deployment:

| Gate ID | Area | Acceptance Requirement | Pass Verification Method |
| :---: | :--- | :--- | :--- |
| **G-01** | Parser Contract | Working parser contract untouched; passes all test files | Automated contract test suite |
| **G-02** | Scenario A | Add candidate with parsed resume + successful R2 upload | Candidate created, ResumeAsset `ACTIVE`, Outbox event saved |
| **G-03** | Scenario B | Add candidate with failed parser + successful R2 upload | Candidate created with manual fields; ResumeAsset `ACTIVE` |
| **G-04** | Scenario C | Add candidate with parsed resume + failed R2 upload | Candidate saved; UI prompts resume re-upload |
| **G-05** | Scenario D1 | Submit while upload running (`ATTACH_IF_AVAILABLE`) | Candidate saved; session `PENDING_ATTACHMENT`; reconciles async |
| **G-06** | Scenario D2 | Submit while upload running (`DISCARD`) | Candidate saved with `resume = ''`; upload session abandoned |
| **G-07A**| Edit Mode | Edit candidate without touching resume (`KEEP`) | Details updated; `current_resume_id` and resume URL unchanged |
| **G-07B**| Edit Mode | Edit candidate → remove resume (`REMOVE`) | `current_resume_id = NULL`; old asset `REMOVAL_PENDING` |
| **G-07C**| Edit Mode | Remove resume while upload is running | In-flight XHR aborted; session marked discarded |
| **G-07D**| Race Defense | Old upload completes after removal | `current_resume_id` remains `NULL`; upload marked `ORPHANED` |
| **G-07E**| Race Defense | Old parser result completes after removal | Bridge checks `parser_run_id`; discards stale parsed fields |
| **G-07F**| Race Defense | Two simultaneous edit/remove operations on same candidate | Optimistic lock checks `resume_version`; rejects stale edit |
| **G-08** | Scenario J | Double-click submit button rapidly | Exactly 1 candidate created; 0 duplicate rows |
| **G-09** | Idempotency | Network retry replaying identical `client_submission_uuid` | Replays original 200 response; 0 new DB rows |
| **G-10** | DB Cleanliness | Zero HTTP or Elasticsearch calls inside MySQL transaction | MySQL query log audit during transaction |
| **G-11** | Outbox Guarantee | Outbox event row guaranteed to exist after DB commit | SQL inspection of `candidate_outbox_events` |
| **G-12** | ES Resilience | Candidate persists even if Elasticsearch cluster is stopped | Stop ES container; submit form; expect success |
| **G-13** | Queue Isolation | Candidate jobs execute on `candidate-index`, not `emails` | Horizon queue dashboard inspection |
| **G-14** | Tenant Security | Company A cannot submit or claim Company B jobs or sessions| API attack test with mismatched company IDs (403/422) |
| **G-15** | State Machine | Rejects illegal state jumps with 422 (`CREATED` → `CLAIMED` ❌) | Negative unit tests asserting 422 |
| **G-16** | Zero Typing | 0 dynamic requests per keystroke after client initialization| Chrome DevTools Network inspection |
| **G-17** | Cache Rollback | Cache remains intact if MySQL transaction rolls back | Force DB exception; verify cache untouched |
| **G-18** | UI Responsiveness| Complete layout fidelity on 15.6" screens (1366×768 / 1080p)| Visual layout inspection across responsive views |
| **G-19** | Zero Lag Submit | Artificial 2000ms delay removed; HTTP request fires instantly | DevTools submit click-to-dispatch latency (< 20ms) |
| **G-20** | Concurrency Load | 1,000 concurrent submissions; 0 deadlocks; HTTP error < 0.01%| k6 / Locust load testing benchmark |
| **G-21** | Outbox Dedupe | Force duplicate event delivery to worker | Exactly 1 logical ES document; 0 duplicate alerts |
| **G-22** | Worker Recovery | Kill worker during outbox processing | Event lock expires; event retries and completes |
| **G-23** | Browser Crash R2 | Browser killed mid-upload after R2 PUT succeeds | Scheduled reconciliation attaches resume to candidate |
| **G-24** | Redis Outage | Redis down during submit | Bounded timeout fails open; MySQL unique constraint protects |
| **G-25** | Crash Pre-Submit | Browser crash after R2 PUT but candidate never submitted | Session expires; object marked `ORPHANED`; purged by TTL |
| **G-26** | Tenant Linkage | Reconciliation verifies `session.company_id == candidate.company_id` | Mismatched session rejects attachment |
| **G-27** | Outbox Dedupe Key| Attempt to insert identical `dedupe_key` | MySQL rejects with 1062 duplicate key error |
| **G-28** | R2 Purge Safety | Physical R2 deletion failure does not roll back MySQL state | Simulate R2 500 error on delete; verify MySQL row intact |
| **G-29** | Memory Heap Cap | Total tab JS heap + WASM overhead < 72 MB during large PDF | Chrome DevTools Memory Profiler |
| **G-30** | Stale Outbox Lock| Expired `locked_at` reclaimed by Worker B | Worker A late update affects 0 rows via fencing token |
| **G-31** | Discarded Upload | Discarded upload session never links to candidate | Verify `current_resume_id = NULL` in MySQL |
| **G-32** | Drawer Sync | Candidate drawer renders updated resume status immediately | Verify drawer reflects removed resume state |
| **G-33** | Multi-Tab Isolation| Open 5 tabs for same job; upload in Tab 1, clear Tab 2 | Tab 1 upload unaffected; Tab 2 clean; 0 session collision |

---

## 7. Revised 7-Phase Implementation Roadmap

```
PHASE 0: Safety Baseline & Parser Contract Protection
  │  ├── Build automated contract tests for JeroClientParser & JeroCandidateFormBridge
  │  └── Establish automated regression test baseline for current service_company create/store endpoints
  ▼
PHASE 1: Submission Correctness, 3-Layer Idempotency & Optimistic Locking
  │  ├── Remove artificial 2000ms delay from create.blade.php:3699 and 2589
  │  ├── Add hidden client_submission_uuid / client_update_uuid fields to forms
  │  ├── Implement Bounded Redis atomic idempotency lock (50ms timeout, fail-open) in store()
  │  ├── Add UNIQUE constraint on (company_id, client_submission_uuid) in mysql-schema.sql
  │  ├── Add resume_version column to job_applications for optimistic locking
  │  └── Implement QueryException duplicate entry catch & response replay
  ▼
PHASE 2: Canonical 10-State Upload Session & Durable ResumeAsset Lifecycle
  │  ├── Create resume_assets table in mysql-schema.sql with versioning and status
  │  ├── Formalize 10 upload states & 5 asset states (ACTIVE, REMOVAL_PENDING, REMOVED, etc.)
  │  ├── Implement resume_intent parameter (KEEP, REMOVE, ATTACH_IF_AVAILABLE, DISCARD, NONE)
  │  ├── Build atomic Remove transactional handler
  │  └── Build scheduled reconciliation command (candidates:reconcile-pending-resumes)
  ▼
PHASE 3: Clean Database Transaction & Post-Commit Cache Discipline
  │  ├── Strip all remote network calls (ES) from DB::beginTransaction()
  │  ├── Move GeneralMethod::cacheClean() strictly after DB::commit()
  │  └── Achieve MySQL transaction duration P95 < 15ms
  ▼
PHASE 4: Transactional Outbox Pattern, Horizon Queues & Fencing Workers
  │  ├── Create candidate_outbox_events table with dedupe_key and lock_token
  │  ├── Write lean versioned outbox events (candidate.created, resume.removed)
  │  ├── Implement ProcessOutboxEventJob worker with two-step FOR UPDATE SKIP LOCKED
  │  ├── Configure exponential backoff and dead-letter queueing (max 5 retries)
  │  └── Dispatch idempotent async Elasticsearch indexing on dedicated 'candidate-index' queue
  ▼
PHASE 5: Client-First Search (Zero Dynamic Requests During Typing)
  │  ├── Replace live /skill-suggestions with local Trie over job-required skills & cached dictionary
  │  └── Replace live /city-job-suggestions and /countrycitylist with sharded static datasets
  ▼
PHASE 6: Load Testing, Observability & Acceptance Sign-Off
     ├── Implement structured telemetry metrics and Horizon outbox lag dashboard
     ├── Execute 5-profile k6 test suite (10K active sessions & 1K concurrent submissions)
     └── Authoritatively verify all 32 Acceptance Gates before production release on testing.jero.works
```
