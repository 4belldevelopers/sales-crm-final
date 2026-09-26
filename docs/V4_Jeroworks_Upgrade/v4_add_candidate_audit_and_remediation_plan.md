# V4 Add Candidate Flow: Production Engineering Audit, Architecture Specification & Implementation Blueprint

> **Document Version:** 4.5.1 (Production Implementation Master Contract: Resume Lifecycle, Mutation Safety & 36 Acceptance Gates)  
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
2. **Resume Asset Lifecycle:** Manages the durable database entity and versioned attachment to a candidate (`ACTIVE`, `SUPERSEDED`, `REMOVAL_PENDING`, `REMOVED`, `ORPHANED`, `CLEANUP_PENDING`).
3. **Resume Mutation Intent:** The explicit recruiter action dispatched to the server (`KEEP`, `REMOVE`, `REPLACE`, `ATTACH_IF_AVAILABLE`, `DISCARD`, `NONE`).

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
│  │ • JeroCandidateFormBridge│  │ • 10-State Upload Session│ │ • Notice Period & Experience Logic     │  │
│  │ • Untrusted Browser     │  │ • Durable `resume_assets`│  │ • Local Sharded Geo & Job Skills Trie  │  │
│  │   Input Policy          │  │ • Server Reconciliation │  │ • Metronic 8 FormValidation            │  │
│  │ • Protected by Contract │  │   Worker (Crash-Proof)  │  │ • Zero Dynamic Requests per Keystroke  │  │
│  │   Tests (Phase 0)       │  │ • Dual Cleanup Classes  │  │ • 100% Usable With or Without Resume   │  │
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

## 2. Non-Negotiable Resume Invariants (R-I01 to R-I15)

Every line of code written for the Add/Edit candidate flow must adhere to these 15 invariants:

* **R-I01:** A candidate can have at most one `ACTIVE` canonical resume at any point in time.
* **R-I02:** An R2 object is never authoritative; MySQL is the sole authority for ownership and attachment.
* **R-I03:** A late upload can never resurrect a removed or superseded resume.
* **R-I04:** A stale mutation can never overwrite a newer resume version (optimistic locking enforced).
* **R-I05:** A parser result is valid only for the resume version that generated it; mismatched runs are discarded.
* **R-I06:** Old resumes are never physically deleted from R2 before the new resume is canonically committed.
* **R-I07:** Removing a resume cannot delete or mutate candidate profile data.
* **R-I08:** Physical R2 deletion failure cannot fail or corrupt the MySQL database transaction.
* **R-I09:** Browser crashes can never create permanent ownership ambiguity or attach files to random candidates.
* **R-I10:** Every resume mutation (`KEEP`, `REMOVE`, `REPLACE`) is strictly idempotent.
* **R-I11:** Every resume mutation enforces authoritative server-side tenancy verification.
* **R-I12:** Every resume replacement or removal emits a dedicated, auditable outbox event.
* **R-I13:** Only the canonical `ACTIVE` resume is exposed by default across APIs, drawers, and download centers.
* **R-I14:** Elasticsearch documents, caches, and read models must be completely rebuildable from MySQL resume state.
* **R-I15:** A candidate record is a first-class entity and can exist permanently without a resume.

---

## 3. Complete 13-Scenario Matrix (Add, Edit, Remove, Replace & Races)

| Scenario | Operation & Trigger | Upload Status | `resume_intent` | Persistence & Asset Lifecycle | Recruiter Outcome |
| :---: | :--- | :--- | :---: | :--- | :--- |
| **A** | Add + Parsed Resume | `VERIFIED` | `ATTACH_IF_AVAILABLE` | Candidate created; ResumeAsset created as `ACTIVE`; Outbox event created | Instant 200 JSON Redirect; Candidate in AppTracker with resume |
| **B** | Add + Failed Parser | `VERIFIED` | `ATTACH_IF_AVAILABLE` | Candidate created with manual fields; ResumeAsset `ACTIVE`; Outbox created | Instant 200 JSON Redirect; Candidate in AppTracker with resume |
| **C** | Add + Upload Failed | `FAILED` | `ATTACH_IF_AVAILABLE` | Candidate saved in MySQL; UI prompts to retry resume or saves without | Candidate created; resume flagged for retry/re-upload |
| **D1**| Add + In-Flight Upload | `UPLOADING` | `ATTACH_IF_AVAILABLE` | Candidate saved; session marked `PENDING_ATTACHMENT`; background links | Instant submit; resume attaches asynchronously without blocking |
| **D2**| Add + In-Flight Upload | `UPLOADING` | `DISCARD` | Candidate saved with `resume = ''`; upload session abandoned/purged | Instant submit; resume is permanently omitted per recruiter intent |
| **E** | Add Without Resume | `NONE` | `NONE` | Candidate saved with `resume = ''`; Outbox event created | Instant 200 JSON Redirect; Candidate in AppTracker without resume |
| **F** | Edit (Keep Resume) | Existing | `KEEP` | Candidate fields updated; `current_resume_id` unchanged; version incremented | Candidate updated; return to previous grid view |
| **G** | Edit (Replace Resume)| New `VERIFIED` | `REPLACE` | Old asset → `SUPERSEDED`; New asset → `ACTIVE`; Outbox `resume.replaced` | Candidate updated with newly attached resume |
| **H** | Double-Click / Retry | Same UUID | Any | Redis lock intercepts or MySQL UNIQUE constraint triggers idempotent replay | Returns original success response; zero duplicate candidates |
| **I** | Edit (Remove Resume) | Existing | `REMOVE` | `current_resume_id = NULL`; Old asset → `REMOVAL_PENDING`; Outbox `resume.removed` | Candidate updated; resume detached; R2 cleaned async |
| **J** | Add: Remove Pre-Submit| Cleaned | `NONE` | Dropzone resets; session marked `DISCARDED`; 0 rows saved | Form clear; dropzone restored to initial state |
| **K** | Add: Replace Pre-Submit| Replaced | `ATTACH_IF_AVAILABLE` | Previous upload session abandoned; new upload session initiated | Form updated to new file; previous file purged by R2 TTL |
| **L** | Remove While Uploading| In-Flight | `DISCARD` | In-flight XHR aborted; session marked `DISCARDED`; dropzone reset | Clean dropzone state; late packets discarded safely |
| **M** | Replace While Uploading| In-Flight | `REPLACE` | Previous XHR aborted; fresh presigned PUT initiated for replacement | Clean transition to new file; zero orphaned attachment links |

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
  `status` ENUM('ACTIVE', 'SUPERSEDED', 'REMOVAL_PENDING', 'REMOVED', 'ORPHANED', 'CLEANUP_PENDING') NOT NULL DEFAULT 'ACTIVE',
  `parser_status` ENUM('PARSED', 'FAILED', 'BYPASSED') NOT NULL DEFAULT 'BYPASSED',
  `parser_version` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attached_at` TIMESTAMP NULL DEFAULT NULL,
  `superseded_at` TIMESTAMP NULL DEFAULT NULL,
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

### 4.2 Atomic Replace & Remove Semantics

#### Atomic Replace Semantics
When replacing an existing resume with a new verified upload:
```php
DB::beginTransaction();
try {
    // 1. Fetch current candidate with lock
    $candidate = DB::table('job_applications')->where('id', $candidateId)->lockForUpdate()->first();
    $oldResumeId = $candidate->current_resume_id;

    // 2. Create new active ResumeAsset
    $newAssetId = DB::table('resume_assets')->insertGetId([
        'company_id'        => $companyId,
        'candidate_id'      => $candidateId,
        'upload_session_id' => $sessionUuid,
        'object_key'        => $newR2Key,
        'original_filename' => $newFilename,
        'mime_type'         => $mimeType,
        'file_size'         => $fileSize,
        'version'           => $candidate->resume_version + 1,
        'status'            => 'ACTIVE',
        'attached_at'       => now(),
        'created_at'        => now(),
    ]);

    // 3. Mark old ResumeAsset as SUPERSEDED (Never physically delete inside transaction!)
    if ($oldResumeId) {
        DB::table('resume_assets')->where('id', $oldResumeId)->update([
            'status'        => 'SUPERSEDED',
            'superseded_at' => now(),
        ]);
    }

    // 4. Update candidate pointer & increment resume_version (Optimistic Lock)
    $updated = DB::table('job_applications')
        ->where('id', $candidateId)
        ->where('resume_version', $candidate->resume_version)
        ->update([
            'current_resume_id' => $newAssetId,
            'resume'            => "https://file.jero.works/{$newR2Key}",
            'resume_version'    => $candidate->resume_version + 1,
            'updated_at'        => now(),
        ]);

    if (! $updated) {
        throw new StaleMutationException('Concurrent resume update detected. Please refresh.');
    }

    // 5. Insert outbox events for replacement and async cleanup
    DB::table('candidate_outbox_events')->insert([
        'company_id'     => $companyId,
        'dedupe_key'     => "candidate:{$candidateId}:resume_replaced:v{$candidate->resume_version}",
        'aggregate_type' => 'Candidate',
        'aggregate_id'   => $candidateId,
        'event_type'     => 'resume.replaced',
        'payload'        => json_encode([
            'candidate_id'  => $candidateId,
            'new_resume_id' => $newAssetId,
            'old_resume_id' => $oldResumeId,
        ]),
    ]);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

#### Atomic Remove Semantics
```php
DB::beginTransaction();
try {
    $candidate = DB::table('job_applications')->where('id', $candidateId)->lockForUpdate()->first();
    $oldResumeId = $candidate->current_resume_id;

    if ($oldResumeId) {
        DB::table('resume_assets')->where('id', $oldResumeId)->update([
            'status'     => 'REMOVAL_PENDING',
            'removed_at' => now(),
        ]);
    }

    DB::table('job_applications')
        ->where('id', $candidateId)
        ->where('resume_version', $candidate->resume_version)
        ->update([
            'current_resume_id' => null,
            'resume'            => '',
            'resume_version'    => $candidate->resume_version + 1,
            'updated_at'        => now(),
        ]);

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

Race 2: Late Old PUT After User Replaced File
 File A streaming ────────► Recruiter picks File B
 Client aborts XHR A ─────► Session A marked SUPERSEDED / DISCARDED
 Session B verified ──────► Candidate links to Resume B (ACTIVE)
 If late packet A lands ──► Session A is already discarded; DB ignores it.
 Result: File B is ACTIVE. File A is NEVER attached.

Race 3: Multi-Tab Stale Mutation
 Tab 1 submits Replace (version 1 -> 2) ──► Succeeds. DB version = 2.
 Tab 2 submits Replace (version 1 -> 2) ──► WHERE resume_version = 1 affects 0 rows!
 Result: StaleMutationException thrown. Tab 2 cannot overwrite newer resume.

Race 4: Late Parser Result from Superseded File
 File A starts parsing ──► parser_run_id = 'run_A'
 Recruiter picks File B ─► parser_run_id = 'run_B'
 Parser A finishes late ─► Bridge checks: if (current_parser_run_id !== 'run_A') discard!
 Result: Parser A result dropped. Form fields NEVER overwritten with old skills.
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

Class 2: Superseded & Removed Asset Cleanup (candidate-resumes/*)
 - Triggered when a ResumeAsset enters status 'SUPERSEDED' or 'REMOVED'.
 - Managed by background job `PurgeSupersededResumeAssetsJob` with 7-day retention grace period.
 - Guarantees that accidental removals can be recovered by support before permanent object purge.
```

---

### 4.7 Targeted Cache Invalidation Discipline

When resume mutations occur, the following cache keys are invalidated strictly post-commit:
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

## 6. Formal 36-Gate Production Acceptance Matrix

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
| **G-07B**| Edit Mode | Edit candidate → replace resume (`REPLACE`) | New asset `ACTIVE`; old asset `SUPERSEDED`; Outbox emitted |
| **G-07C**| Edit Mode | Edit candidate → remove resume (`REMOVE`) | `current_resume_id = NULL`; old asset `REMOVAL_PENDING` |
| **G-07D**| Edit Mode | Replace resume while upload is running | Previous XHR aborted; new upload presigned and linked |
| **G-07E**| Edit Mode | Remove resume while upload is running | In-flight XHR aborted; session marked discarded |
| **G-07F**| Race Defense | Old upload completes after replacement | Resume B remains `ACTIVE`; late packet A marked superseded |
| **G-07G**| Race Defense | Old parser result completes after replacement | Bridge checks `parser_run_id`; discards stale skills |
| **G-07H**| Race Defense | Two simultaneous edit operations on same candidate | Optimistic lock checks `resume_version`; rejects stale edit |
| **G-08** | Scenario H | Double-click submit button rapidly | Exactly 1 candidate created; 0 duplicate rows |
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
| **G-32** | Drawer Sync | Candidate drawer renders updated resume immediately | Verify drawer reads active `current_resume_id` |
| **G-33** | Download Center | Download center fetches correct active resume version | Download file; verify matches latest replaced asset |
| **G-34** | Custom Skills | Server rejects skill tags > 60 chars or count > 50 | Submit 60+ skills; assert 422 validation error |
| **G-35** | Salary Mode Math | INR to USD currency conversion exact across all modes | Assert numeric fidelity across all salary modes |
| **G-36** | Notice Period | Notice period interactive states validate correctly | Toggle notice period radio buttons; assert validation |

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
  │  ├── Formalize 10 upload states & 6 asset states (ACTIVE, SUPERSEDED, REMOVAL_PENDING, etc.)
  │  ├── Implement resume_intent parameter (KEEP, REMOVE, REPLACE, ATTACH_IF_AVAILABLE, DISCARD)
  │  ├── Build atomic Replace and Remove transactional handlers
  │  └── Build scheduled reconciliation command (candidates:reconcile-pending-resumes)
  ▼
PHASE 3: Clean Database Transaction & Post-Commit Cache Discipline
  │  ├── Strip all remote network calls (ES) from DB::beginTransaction()
  │  ├── Move GeneralMethod::cacheClean() strictly after DB::commit()
  │  └── Achieve MySQL transaction duration P95 < 15ms
  ▼
PHASE 4: Transactional Outbox Pattern, Horizon Queues & Fencing Workers
  │  ├── Create candidate_outbox_events table with dedupe_key and lock_token
  │  ├── Write lean versioned outbox events (candidate.created, resume.replaced, resume.removed)
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
     └── Authoritatively verify all 36 Acceptance Gates before production release on testing.jero.works
```
