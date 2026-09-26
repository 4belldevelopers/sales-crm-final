# V4 Add Candidate Flow: Production Engineering Audit, Architecture Specification & Remediation Plan

> **Document Version:** 4.3.0 (Strict Subsystem Isolation: Form Lifecycle, Submit Pipeline & 100% Decoupled Client Parser)  
> **Status:** Production Architecture Specification & Remediation Plan  
> **Target Module:** Add & Edit Candidate Page & Submission Pipeline (`resources/views/service_company/job-applications/create.blade.php`)  
> **Target Environment:** `testing.jero.works`  
> **Primary Source Files:**  
> - `resources/views/service_company/job-applications/create.blade.php` (Metronic 8 ATS Add/Edit Candidate Blade)  
> - `resources/views/service_company/job-applications/jero-client-parser.blade.php` (Working 100% Client-Side Resume Parser Integration Bridge)  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`create()`, `store()`, `update()`, `uploadresume()`)  
> - `public/js/resume-presign.js` (Cloudflare R2 Direct PUT Presigned Upload Engine)  
> **Dedicated Companion Subsystem (Strictly Isolated & Off-Limits to Regressions):**  
> - `docs/V4_Jeroworks_Upgrade/100_percent_client_side_resume_parsing.md` & `test_parser_jero_works.md`  
>   *(Governs the operational in-browser WASM/NLP parser at `https://parser.jero.works/` — working perfectly in production).*  

---

## 1. Executive Summary & Strict Architectural Isolation

### 1.1 The Golden Mandate: Isolation of Add Candidate Flow & Submit from Resume Parsing
The in-browser Resume Parsing Subsystem (`https://parser.jero.works/` via `jero-client-parser.blade.php` and `JeroCandidateFormBridge`) is **100% functional, battle-tested, and operating with zero server strain**. 

Under **no circumstances** may any upgrade to the Add Candidate form, validation layer, or submission pipeline touch, mutate, or regress the working resume parsing bridge. 

The architecture is strictly partitioned into **4 decoupled pipelines**:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                              JERO V4 ADD CANDIDATE ARCHITECTURE MATRIX                                 │
│                                                                                                        │
│  ┌─────────────────────────┐  ┌─────────────────────────┐  ┌────────────────────────────────────────┐  │
│  │ PIPELINE 1:             │  │ PIPELINE 2:             │  │ PIPELINE 3:                            │  │
│  │ CLIENT RESUME PARSER    │  │ STORAGE PERSISTENCE     │  │ ADD CANDIDATE FORM & UI                │  │
│  │ (Status: WORKING / LOCKED)│  │ (Status: ACTIVE / ISOLATED)│  │ (Status: TARGET FOR REMEDIATION)       │  │
│  │                         │  │                         │  │                                        │  │
│  │ • 100% Browser RAM/WASM │  │ • Direct Browser-to-R2  │  │ • Form Fields & Personal Details       │  │
│  │ • PDF.js + Mammoth NLP  │  │   Presigned PUT Upload  │  │ • Dynamic Salary Mode Math             │  │
│  │ • JeroCandidateFormBridge│  │ • Background Execution │  │ • Notice Period & Experience Calculators│  │
│  │ • ZERO Server Requests  │  │ • Zero PHP File Bytes   │  │ • Tiered Skills & Sharded Locations    │  │
│  │ • ZERO MySQL / ES Load  │  │ • Sets #resume_session_ │  │ • Metronic 8 FormValidation            │  │
│  │                         │  │   uuid / uploaded_path  │  │ • 100% Usable With or Without Resume   │  │
│  └────────────┬────────────┘  └───────────┬─────────────┘  └───────────────────┬────────────────────┘  │
│               │ (Autofills empty inputs)   │ (Provides attachment UUID)        │                       │
│               ▼                            ▼                                   ▼                       │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ PIPELINE 4: HIGH-PERFORMANCE RESILIENT SUBMISSION & CQRS PERSISTENCE                            │  │
│  │ (Status: TARGET FOR REMEDIATION)                                                                 │  │
│  │                                                                                                  │  │
│  │ • Instant Form Submission (Elimination of artificial 2000ms delay)                               │  │
│  │ • Cryptographic Idempotency Key (`client_submission_uuid`) — Zero Duplicate Submissions           │  │
│  │ • Authoritative Server Tenancy & Security Gate Verification                                      │  │
│  │ • High-Speed MySQL Transaction: INSERT `job_applications` + Claim R2 Attachment                  │  │
│  │ • Decoupled Post-Commit ES Sync: Zero Elasticsearch Network Locks inside MySQL Transaction       │  │
│  │ • Partitioned Queue Architecture: Candidate sync moved off overloaded 'emails' queue             │  │
│  └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1.2 The 4 Core Submission Scenarios
The Add Candidate pipeline must function reliably across all four operational scenarios:
1. **Scenario A — Add Candidate WITH Parsed Resume:** Recruiter drops resume → Parser immediately parses and autofills candidate fields in browser RAM → Storage upload finishes in background → Recruiter reviews/edits data → Clicks submit → Form dispatches immediately with `resume_session_uuid` and `client_submission_uuid`.
2. **Scenario B — Add Candidate WITH Resume (Parsing Failed / Scanned Image):** Recruiter drops scanned image or unsupported file → Parser cleanly falls back to manual entry prompt → Resume file uploads to R2 in background → Recruiter enters candidate details manually → Clicks submit → Candidate saved with resume attached.
3. **Scenario C — Add Candidate WITHOUT Resume:** Recruiter enters candidate details manually (no file provided) → Parser and R2 upload are never triggered → Recruiter clicks submit → Form submits instantly; backend saves candidate with `resume = ''`.
4. **Scenario D — Edit Candidate Mode (`/edit/{id}`):** Existing candidate data pre-populated → Recruiter updates details or replaces resume file → Form dispatches to `/update/{id}` without artificial delays.

---

## 2. Architecture Comparison: Current Production Reality vs. Target State

```
CURRENT ARCHITECTURE (Bottlenecks & Fragilities Identified in Code)
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Browser (create.blade.php)                                                                      │
│  ├── [Resume Pick] ───────► Pipeline 1 (Client Parser) & Pipeline 2 (R2 Upload) [WORKING]       │
│  ├── [Type Skill] ────────► GET /skill-suggestions ─────────► ES Wildcard Query (*query*)       │
│  ├── [Type Location] ─────► GET /city-job-suggestions ──────► ES Wildcard Query (*query*)       │
│  ├── [Country Change] ────► GET /countrycitylist/{cid} ─────► Sync MySQL Query                  │
│  ├── [Form Submit] ───────► setTimeout(..., 2000) ──────────► 2000ms ARTIFICIAL LAG!           │
│  │                                                            No client_submission_uuid token   │
│  ▼                                                                                              │
│ Laravel Backend (Controller::store)                                                             │
│  ├── [DB::beginTransaction]                                                                     │
│  │    ├── INSERT `job_applications`                                                            │
│  │    ├── INSERT `add_job_wise_candidate`                                                       │
│  │    └── GeneralMethod::JobApplicationsAddEditUpload() ────► SYNCHRONOUS HTTP CALL TO ES!     │
│  │        (MySQL locks held open while Elasticsearch processes network request)                 │
│  ├── [DB::commit]                                                                               │
│  └── [Queue Dispatch] ────► Dispatched to 'emails' queue ───► Trapped behind SMTP jobs          │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

```
V4 TARGET ARCHITECTURE (Zero Delay, Resilient CQRS, Dedicated Queues)
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Browser (create.blade.php)                                                                      │
│  ├── [Resume Pick] ───────► Pipeline 1 (Client Parser) & Pipeline 2 (R2 Upload) [PRESERVED]     │
│  ├── [Type Skill] ────────► Client-Side Trie Search (Local Job Skills & Cached Shards)          │
│  ├── [Type Location] ─────► Progressive Sharded Geo Lookup (0 ES Requests)                      │
│  ├── [Form Submit] ───────► Instant Dispatch (0ms Delay) + Unique `client_submission_uuid`      │
│  ▼                                                                                              │
│ Laravel Backend (Controller::store)                                                             │
│  ├── [Atomic Idempotency Lock] ──► Redis/Cache check `client_submission_uuid` (reject dupes)    │
│  ├── [DB::beginTransaction]                                                                     │
│  │    ├── INSERT `job_applications`                                                            │
│  │    ├── INSERT `add_job_wise_candidate`                                                       │
│  │    └── ATOMIC CLAIM `resume_session` (Link R2 object to candidate ID)                        │
│  │    (Zero network calls, zero remote service locks — Tx duration < 15ms)                      │
│  ├── [DB::commit]                                                                               │
│  ├── [Post-Commit Indexing] ──► ProcessCandidateSubmissionJob dispatched to 'candidate-index'   │
│  └── [HTTP Response] ─────────► Fast 200 JSON Redirect to AppTracker (P95 < 80ms)               │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Technical Audit Findings: Status, Severity & Target Remediation

### 🚨 Critical Severity Findings (P0)

#### Finding P0-1: Keystroke-Driven Elasticsearch Saturation (`/skill-suggestions` & `/city-job-suggestions`)
- **Location:** [`create.blade.php:3100`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3100) & [`create.blade.php:3205`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3205)
- **Status:** **Active Vulnerability**
- **Impact:** Recruiters typing in Primary Skills or Location trigger live GET requests to Laravel that execute wildcard searches directly against Elasticsearch. Under 10,000 concurrent recruiters, this saturates Elasticsearch cluster threadpools.
- **Remediation:**
  1. Priority tiering: Pre-seed Tagify with the active job's required skills (`candidateRequiredSkills`).
  2. Cache top canonical skills (~3,000 skills) and sharded geo locations in browser memory (`IndexedDB` / Web Worker Trie).
  3. 0 HTTP requests to Laravel or Elasticsearch during typing.

#### Finding P0-2: Synchronous Elasticsearch Network Request Inside MySQL Transaction
- **Location:** [`ServiceCompanyJobApplicationController.php:3389`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L3389)
- **Status:** **Active Vulnerability**
- **Impact:** `GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId);` performs a synchronous HTTP request to Elasticsearch *while the MySQL transaction is open*. Any latency spike or ES timeout holds database locks open, causing MySQL connection pool exhaustion.
- **Remediation:**
  1. **Strict Architectural Rule:** Move `GeneralMethod::JobApplicationsAddEditUpload` **after `DB::commit()`**.
  2. Dispatch document indexing asynchronously via `ProcessCandidateSubmissionJob` or an immediate post-commit event.
  3. Database transaction duration drops from ~450ms to **< 15ms**.

#### Finding P0-3: Candidate Pipeline Queued on Overloaded 'emails' Connection
- **Location:** [`ServiceCompanyJobApplicationController.php:3364, 3419, 3426`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L3364)
- **Status:** **Active Vulnerability**
- **Impact:** Post-commit candidate processing (`ProcessCandidateSubmissionJob`, `UploadResumeToTestRTwo`, `QueueCandidateNotificationJob`) is dispatched with `->onConnection('emails')`. If SMTP encounters delays or backpressure, candidate indexing and resume attachments stall.
- **Remediation:**
  1. Route candidate indexing to dedicated high-priority queue: `candidate-index`.
  2. Route file processing to `candidate-write`.
  3. Keep only SMTP email alerts on `notifications` / `emails`.

---

### ⚠️ High Severity Findings (P1)

#### Finding P1-1: Artificial 2000ms `setTimeout` Delay on Form Submission
- **Location:** [`create.blade.php:3699`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3699) (Create Mode) & [`create.blade.php:2589`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L2589) (Edit Mode)
- **Status:** **Active Bottleneck**
- **Impact:** Form submission code explicitly wraps the AJAX request in `setTimeout(..., 2000);`, artificially forcing every recruiter to wait 2 full seconds before the submission payload is even sent to the server.
- **Remediation:** Remove the artificial 2000ms delay. Trigger submission immediately once client validation succeeds.

#### Finding P1-2: Lack of Submission Idempotency Key (`client_submission_uuid`)
- **Location:** [`create.blade.php:3669–3713`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3669-L3713) & [`ServiceCompanyJobApplicationController.php:2915`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L2915)
- **Status:** **Active Vulnerability**
- **Impact:** Duplicate prevention relies entirely on visual button disabling in JavaScript (`submitButton.disabled = true;`). Under network retries, browser refresh, or unstable mobile proxies, duplicate candidate records are created in MySQL.
- **Remediation:**
  1. Client initializes a hidden `client_submission_uuid` (UUIDv4) upon form render.
  2. Server uses atomic cache lock `Cache::add("candidate_submit_{$companyId}_{$uuid}", 'processing', 60)`:
     - If key exists: return `409 Conflict` or replay previous response.
     - If key is new: proceed with submission transaction.

#### Finding P1-3: Country Change Direct MySQL Ajax Cascade
- **Location:** [`create.blade.php:3925`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3925)
- **Status:** **Active Vulnerability**
- **Impact:** Changing country fires an AJAX request to `/countrycitylist/{cid}` which queries MySQL directly (`state_list` / `city_list`).
- **Remediation:** Pre-render or shard static country/state lists into browser storage.

---

### 🔍 Verified & Neutralized Findings (Preserved Working State)

#### Finding P2-1: Client-Side Resume Parsing Subsystem (`JeroClientParser`)
- **Location:** [`jero-client-parser.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/jero-client-parser.blade.php) & [`create.blade.php:1512–1542`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L1512-L1542)
- **Status:** **VERIFIED & WORKING PERFECTLY**
- **Details:** 100% in-browser RAM execution via `https://parser.jero.works/`. Memory buffer snapshotting prevents stream detachment. Fields populated via `JeroCandidateFormBridge`. Status badge cleanly isolated. **Must remain untouched.**

#### Finding P2-2: Legacy Server Extraction & Duplicate Poller Neutralized
- **Location:** [`create.blade.php:3836`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3836) (`re_test`), [`create.blade.php:3874`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3874) (`handleFileInputChange`), [`create.blade.php:1691`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L1691) (`_startParsePoller`)
- **Status:** **NEUTRALIZED IN BLADE**
- **Details:** Previous duplicate AJAX uploads, legacy server text extractions, and 4-second polling loops have already been hollowed out in `create.blade.php`.

---

## 4. Subsystem Isolation & Form Integration Contract

### 4.1 Strict Independence Guarantee
The Add Candidate flow must interact with the Resume Parser strictly through the existing, verified bridge:
- **Parser Bridge Object:** `window.JeroCandidateFormBridge.populate(result, isEditMode)`
- **Parser Invocation:** `window.JeroClientParser.parseAndPopulate(selectedFile, options)`
- **Behavioral Rule:** The form lifecycle **never** requires, blocks on, or awaits parsing completion. If parsing is running, fields fill as they become available. If a recruiter submits while parsing is in flight, the manual or currently entered fields are submitted immediately.

### 4.2 Non-Destructive Autofill Policy
The parser bridge enforces non-destructive field population:
```javascript
// Rule: Never overwrite recruiter manual edits if the input already contains a value
if (atsJson.full_name && !$('#full_name').val()) {
    $('#full_name').val(atsJson.full_name);
}
if (atsJson.email && !$('#email').val()) {
    $('#email').val(atsJson.email);
}
if (atsJson.phone && !$('#phone').val()) {
    $('#phone').val(atsJson.phone);
}
```

---

## 5. Resilient Form Submission & Idempotency Architecture

### 5.1 Client-Side Idempotency Generation
When `create.blade.php` is rendered:
1. Blade generates a hidden field:
   ```html
   <input type="hidden" name="client_submission_uuid" id="client_submission_uuid" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
   ```
2. On submit, this token is sent in both `FormData` and via request header `X-Idempotency-Key`.
3. If submission fails due to client network drop, the token remains identical so retries are recognized as identical actions.
4. When a new submission starts after successful save, a new UUID is generated.

### 5.2 Server-Side Atomic Enforcement in `store()`
```php
$submissionUuid = $request->input('client_submission_uuid') ?: $request->header('X-Idempotency-Key');
$companyId = auth()->user()->company_id;

if ($submissionUuid) {
    $lockKey = "candidate_sub_{$companyId}_{$submissionUuid}";
    $acquired = \Illuminate\Support\Facades\Cache::add($lockKey, 'processing', 60);

    if (! $acquired) {
        $existingStatus = \Illuminate\Support\Facades\Cache::get($lockKey);
        if ($existingStatus === 'completed') {
            return Reply::successWithData(__('messages.createdSuccessfully'), [
                'action' => 'redirect',
                'url' => route('service_company.job-applications.table'),
            ]);
        }
        return Reply::dataOnly([
            'status' => 'duplicate',
            'msgType' => 'warning',
            'message' => 'Candidate submission is currently being processed. Please do not double-click.',
        ]);
    }
}
```

---

## 6. Decoupled CQRS Persistence: Fast MySQL Tx + Post-Commit Queue

### 6.1 MySQL Transaction Scope (< 15ms Duration)
The database transaction in `store()` must execute **only local database writes**:
```php
DB::beginTransaction();
try {
    // 1. Save JobApplication row
    $jobApplication->save();

    // 2. Save AddJobWiseCandidate association
    $addJobWiseCandidate->job_application_id = $jobApplication->id;
    $addJobWiseCandidate->save();

    // 3. Atomically claim R2 resume session
    if ($resumeSession) {
        $resumeSession->status = 'Claimed';
        $resumeSession->candidate_id = $jobApplication->id;
        $resumeSession->save();
    }

    // 4. Invalidate tenant Redis cache keys
    $cachekeys = [
        'alljobslist_version_user_' . auth()->user()->id,
        'allcvtracker_version_user_' . auth()->user()->id,
        'allapptracker_version_user_' . auth()->user()->id,
    ];
    foreach ($cachekeys as $key) {
        GeneralMethod::cacheClean($key);
    }

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    if ($submissionUuid) {
        \Illuminate\Support\Facades\Cache::forget($lockKey);
    }
    throw $e;
}
```

### 6.2 Post-Commit Indexing & Queue Dispatch
Immediately **after** `DB::commit()`:
```php
// Mark submission completed in cache
if ($submissionUuid) {
    \Illuminate\Support\Facades\Cache::put($lockKey, 'completed', 300);
}

// Post-commit ES document indexing (outside the MySQL transaction)
try {
    GeneralMethod::JobApplicationsAddEditUpload($jobApplication->id);
} catch (\Throwable $esErr) {
    \Log::warning('Synchronous ES sync deferred to queue worker', ['error' => $esErr->getMessage()]);
}

// Secondary heavy processing dispatched to dedicated candidate queues
ProcessCandidateSubmissionJob::dispatch(
    (int) $jobApplication->id,
    (int) $addJobWiseCandidate->id,
    (int) $request->job_id,
    (int) $company_id,
    (int) $submitted_by,
    (int) $company_id,
    (int) $jobApplication->status_id,
    (string) $jobApplication->email,
    $requestData
)->onQueue('candidate-index');
```

---

## 7. Client-First Skills & Location Search (Zero Server Strain)

### 7.1 Skills Tiering
1. **Tier 1 (Job-Specific Required Skills):** Injected directly into Blade as `candidateRequiredSkills`. Always suggested first in Tagify dropdown.
2. **Tier 2 (Canonical Skills Local Trie):** Stored in `IndexedDB` or local static array. Typing in Tagify matches via in-memory prefix Trie.
3. **Tier 3 (Recruiter Custom Skill):** User can create new tags freely without server validation.
4. **Result:** **0 requests to `/skill-suggestions` or Elasticsearch during typing.**

### 7.2 Sharded Location Lookups
1. Eliminate keystroke requests to `/city-job-suggestions`.
2. Pre-cache countries and top cities in client memory.
3. On country select, dynamically fetch static JSON shards (`/geo/states_{cid}.json`) cached in browser storage.
4. **Result:** **0 requests to `/city-job-suggestions` or MySQL `countrycitylist` during typing.**

---

## 8. Target Scope Boundary: Exclusive `service_company` Isolation

> **Scope Clarification:** Per active architectural directive, **`product_company` and `staffing_company` are strictly OUT OF SCOPE and must be ignored**. 

All engineering audits, remediation plans, and implementations are exclusively scoped to:
1. **Primary Blade View:** `resources/views/service_company/job-applications/create.blade.php`
2. **Primary Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
3. **Primary Parser Bridge:** `resources/views/service_company/job-applications/jero-client-parser.blade.php`

Per **Rule 7 (Zero Module Regressions)** and **Rule 17 (Complete Blade Isolation)**, all changes are strictly encapsulated to `service_company`. Zero shared state or side-effects may leak into or impact any other module.

---

## 9. Formal Acceptance Criteria Matrix

| Gate ID | Verification Test | Pass Criteria | Verification Method |
| :---: | :--- | :--- | :--- |
| **G-01** | Resume Parser Working & Isolated | 100% in-browser RAM parsing; zero server requests; fields autofill | Drop PDF/DOCX resume; observe DevTools Network tab |
| **G-02** | Submit with Parsed Resume | Candidate created; resume attached; redirect to AppTracker | Submit after parsing; verify DB row & R2 attachment |
| **G-03** | Submit with Unparsed/Failed Resume | Candidate created; resume attached; manual fields preserved | Drop invalid file; enter manual fields; verify DB row |
| **G-04** | Submit without Resume | Candidate created; resume = ''; no errors or exceptions | Clear file input; enter manual fields; submit form |
| **G-05** | Form Submission Latency | 0ms artificial delay; HTTP POST dispatches immediately | Chrome DevTools Timings on submit click |
| **G-06** | Duplicate Submission Prevention | Exactly 1 record created when double-clicking submit button | Fast double-click submit; check MySQL count |
| **G-07** | MySQL Transaction Duration | P95 transaction duration < 15ms | Laravel APM / DB Query Log |
| **G-08** | Elasticsearch Typing QPS | 0 requests to ES while typing skills or locations | Network Tab & Elasticsearch Slow Log |
| **G-09** | Queue Partitioning | Candidate jobs routed to `candidate-index`, not `emails` | Horizon dashboard queue metrics |
| **G-10** | Edit Mode Fidelity | Updating candidate details preserves existing attachments and notes | Edit candidate; verify updated fields |

---

## 10. Phase-Wise Remediation Roadmap

### Phase 1: Harden Form Submission & Eliminate Artificial Delays (Add Candidate Flow)
- Remove `setTimeout(..., 2000)` from `create.blade.php:3699` (create mode) and `create.blade.php:2589` (edit mode).
- Implement `client_submission_uuid` hidden field in `create.blade.php`.
- Add atomic idempotency check in `ServiceCompanyJobApplicationController::store()`.
- **Constraint:** Zero modifications to `jero-client-parser.blade.php` or `create.blade.php:1512-1542`.

### Phase 2: Decouple MySQL Transaction & Elasticsearch Indexing (Backend Flow)
- Move `GeneralMethod::JobApplicationsAddEditUpload` outside the MySQL transaction (`DB::commit()`).
- Re-route queued candidate jobs (`ProcessCandidateSubmissionJob`, `UploadResumeToTestRTwo`) from `'emails'` connection to dedicated candidate queues.
- Ensure failure in post-commit ES indexing does not roll back or fail the candidate's creation.

### Phase 3: Client-First Skills & Location Lookups
- Disconnect live AJAX calls to `/skill-suggestions` and `/city-job-suggestions`.
- Wire Tagify directly to local job skills and in-memory Trie dictionary.
- Replace dynamic AJAX `/countrycitylist` with pre-cached static dataset.

### Phase 4: Service Company End-to-End Hardening & Regression Quarantine
- Verify that all `service_company` candidate entry, edit, and submission workflows operate autonomously.
- Quarantine all blade JavaScript and CSS strictly to `service_company/job-applications/create.blade.php`.
- Ensure zero shared state or modifications touch any other company portal (`product_company` and `staffing_company` are strictly ignored).

### Phase 5: Verification & Production Gate Sign-Off
- Execute all 10 criteria in the Formal Acceptance Matrix.
- Verify 100% responsiveness on 15.6" screens (1366×768 & 1920×1080).
- Sign off for production release on `testing.jero.works`.
