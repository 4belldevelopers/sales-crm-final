# V4 Add Candidate Flow: Production Engineering Audit, Architecture Specification & Remediation Plan

> **Document Version:** 4.2.0 (Strictly Modular Architecture & Decoupled Parser Contract)  
> **Status:** Production Architecture Specification & Implementation Blueprint  
> **Target Module:** Add & Edit Candidate Page & Submission Pipeline (`job-applications/create.blade.php`)  
> **Target Environment:** `testing.jero.works`  
> **Primary Source Files:**  
> - `resources/views/service_company/job-applications/create.blade.php` (4,668 lines, 234.8 KB)  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (`create()`, `store()`, `uploadresume()`)  
> - `public/js/resume-presign.js` (V2 presign and R2 direct PUT module)  
> **Dedicated Companion Subsystem (Completely Decoupled):**  
> - `docs/V4_Jeroworks_Upgrade/100_percent_client_side_resume_parsing.md` (Governs all in-browser text extraction, PDF.js, Mammoth, Web Workers, and client regex entity parsing).  

---

## 1. Executive Summary & Modular Scope Definition

### 1.1 Architectural Decoupling: Form Lifecycle vs. Resume Parsing Subsystem
To ensure clean code isolation, maintainability, and parallel implementation tracks, this document strictly isolates the **Add Candidate Form & Persistence Lifecycle** from the **In-Browser Resume Parsing Subsystem**:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ JERO V4 CANDIDATE CREATION PLATFORM                                                             │
│                                                                                                 │
│  ┌──────────────────────────────────────────────┐  ┌──────────────────────────────────────────┐  │
│  │ MODULE A: ADD CANDIDATE PAGE & SUBMISSION    │  │ MODULE B: RESUME PARSING SUBSYSTEM       │  │
│  │ (This Document)                              │  │ (Dedicated Subsystem Document)           │  │
│  │                                              │  │                                          │  │
│  │ • Page Load & Tenancy Initialization         │  │ • In-Browser Web Worker Engine           │  │
│  │ • Form Fields & Salary Calculators           │  │ • PDF.js & Mammoth Binary Decompression  │  │
│  │ • Tiered Skills & Sharded Location Lookups   │  │ • Regex & Layout Entity Extractor        │  │
│  │ • Direct Cloudflare R2 Upload & Session Auth │  │ • Parser Failure & Scanned PDF Handler   │  │
│  │ • Client-Side Form Validation                │  │                                          │  │
│  │ • Submission Idempotency Keys                │  │ Implemented & Audited Separately in:     │  │
│  │ • CQRS MySQL Write & Transactional Outbox    │  │ docs/V4_Jeroworks_Upgrade/               │  │
│  │ • Redis Horizon Asynchronous Pipeline        │  │ 100_percent_client_side_resume_parsing.md│  │
│  └──────────────────────┬───────────────────────┘  └─────────────────────┬────────────────────┘  │
│                         │                                                │                       │
│                         └──────────────◄ EVENT CONTRACT ►────────────────┘                       │
│                                     'jero:candidate-parsed'                                      │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Concurrency Framing & Defensible Engineering Targets
We strictly differentiate between three operational concurrency profiles:
1. **Concurrently Open Pages:** Recruiters holding the Add Candidate page open in browser tabs, performing data entry, viewing requirements, or idling.
2. **Concurrently Interacting Users:** Recruiters actively typing into skills, filtering locations, switching salary modes, or initiating direct R2 uploads.
3. **Simultaneous Submissions:** Recruiters actively submitting candidate records at the exact same millisecond.

> **Static code inspection identifies multiple architectural scalability bottlenecks that make the current implementation unsuitable for a 10,000-concurrent-user target. The exact failure threshold must be established through controlled load testing against production-equivalent infrastructure.**

### 1.3 Target Architecture Statement: 0% Unnecessary Application-Server Load
* **Candidate Data Entry:** 100% Client-Side (Browser RAM).
* **Application Server (Laravel) Interaction:** **0 requests during data entry**.
* **Elasticsearch Interaction:** **0 requests during data entry**.
* **MySQL Interaction:** **0 requests during data entry**.
* **Resume Upload:** **Browser → Cloudflare R2 directly** (external object storage, 0 PHP bytes).
* **Final Candidate Persistence:** **Browser → Laravel API → MySQL Transaction + Outbox Event**.
* **Post-Commit Pipeline:** **Outbox Worker → Queue → Elasticsearch / Ranking / Notifications**.

---

## 2. Architecture Comparison: Current Reality vs. V4 Modular Pipeline

```
CURRENT CHATTY & SERVER-DEPENDENT FLOW (High Latency, Bottlenecks under Load)
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Browser (create.blade.php)                                                                      │
│  ├── [Page Load] ──────────> 14 Multi-Table MySQL Joins (Controller::create)                   │
│  ├── [Type Skill] ─────────> GET /skill-suggestions ─────────> Direct Elasticsearch Cluster Hit │
│  ├── [Type Location] ──────> GET /city-job-suggestions ──────> Direct Elasticsearch Cluster Hit │
│  ├── [Change Country] ─────> GET /countrycitylist/{cid} ─────> MySQL DB Query                   │
│  ├── [Change State] ───────> GET /clients/citylist/{id} ─────> MySQL DB Query                   │
│  ├── [Legacy Upload] ──────> POST /uploadresume ─────────────> PHP multipart stream buffer      │
│  ├── [Legacy Extract] ─────> POST /extract-resume-applications > PHP CPU Regex Worker Lock      │
│  ├── [Every 4 Seconds] ────> GET /resume-status/{id} ────────> Polling Laravel Route Loop       │
│  └── [Submit Click] ───────> 2000ms setTimeout() Delay ──────> Heavy Sync MySQL Tx + Sync ES    │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

```
V4 TARGET MODULAR ARCHITECTURE (10,000 CONCURRENCY READY / CQRS STRICT)
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ CLIENT BROWSER                                                                                  │
│                                                                                                 │
│  ┌───────────────────────────────────┐  ┌────────────────────────────────────────────────────┐  │
│  │ Local Tiered Dictionaries (RAM)   │  │ External Event: 'jero:candidate-parsed'            │  │
│  │  - Canonical Skills Shard (Trie)  │  │ (Fired by independent client parser when active;   │  │
│  │  - Sharded Geo Tree (IndexedDB)   │  │  form listens and autofills; manual entry 100% ok) │  │
│  └─────────────────┬─────────────────┘  └────────────────────────────┬───────────────────────┘  │
│                    │                                                 │                          │
│                    └─────────────────────────┬───────────────────────┘                          │
│                                              ▼                                                  │
│                                 [ Candidate Form & Inputs ]                                     │
│                              Instant Client-Side FormValidation                                 │
│                               0 Application Server Requests                                     │
│                                                                                                 │
│                  ┌───────────────────────────┴───────────────────────────┐                      │
│                  ▼                                                       ▼                      │
│         Direct Cloudflare R2                                    Final Form Submit               │
│         Presigned PUT                                           (with Client UUID)              │
│         (Zero PHP bytes, 0 Polling)                                      │                      │
└──────────────────┬───────────────────────────────────────────────────────┼──────────────────────┘
                   │                                                       │
                   │                                                       ▼
                   │                                                 Laravel API
                   │                                                 ├── Auth & Gate Verification
                   │                                                 ├── Payload Validation
                   │                                                 └── Verify R2 Session State
                   │                                                       │
                   │                                                       ▼
                   │                                                 MySQL Transaction
                   │                                                 ├── INSERT `job_applications`
                   │                                                 ├── INSERT `add_job_wise_...`
                   │                                                 ├── ATOMIC CLAIM `resume_...`
                   │                                                 └── INSERT `outbox_events`
                   │                                                       │
                   │                                                       ▼
                   │                                                 COMMIT TRANSACTION
                   │                                                 Return HTTP 200 JSON Redirect
                   │                                                       │
                   │                                                       ▼
                   │                                                 Outbox Worker
                   │                                                       │
                   │                                                       ▼
                   │                                                 Horizon Queue
                   │                                                       │
                   │                                        ┌──────────────┼──────────────┐
                   ▼                                        ▼              ▼              ▼
     R2 Object Claimed                                Elasticsearch     Ranking     Notifications
     (candidate-temp -> candidate/{id})               Indexing          Engine      (Emails/SMS)
```

---

## 3. Form & Page Technical Audit Findings (P0 → P2)

### 🚨 P0 — Critical Architectural & Scalability Findings

#### Finding P0-1: Keystroke-Driven Elasticsearch Saturation (`/skill-suggestions` & `/city-job-suggestions`)
- **File & Line:** [`create.blade.php:3028`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3028) & [`create.blade.php:3126`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3126)
- **Controller Backend:** [`HomeController.php:13476`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/HomeController.php#L13476) (`skillSuggestions`) & [`HomeController.php:13560`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/HomeController.php#L13560) (`citySuggestions`)
- **Vulnerability:** Typing in Primary Skills or Location triggers debounced GET requests to Laravel which execute unindexed wildcard searches (`*query*`) directly on Elasticsearch clusters.
- **Remediation:** Replace with in-memory client Trie search over tiered, sharded reference dictionaries in IndexedDB. 0 requests to Elasticsearch during typing.

---

#### Finding P0-2: Duplicate Resume Upload Race Condition
- **File & Line:** [`create.blade.php:1515`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L1515) vs [`create.blade.php:3918`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3918)
- **Vulnerability:** `processFile()` invokes `uploadWithProgress(file)` and then immediately calls `handleFileInputChange()` which executes a second parallel `$.ajax` upload to `/uploadresume` due to timing resets on `window.__upXhrChannelClaimed`.
- **Remediation:** Eliminate the redundant AJAX block in `handleFileInputChange()`. Route all file storage exclusively through direct Cloudflare R2 presigned PUTs via `ResumePresign.js`.

---

#### Finding P0-3: Polling Storm via `_startParsePoller`
- **File & Line:** [`create.blade.php:1643–1669`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L1643-L1669)
- **Vulnerability:** Fires an unthrottled `setInterval` requesting `/resume-status/{id}` every 4,000ms per user until an upload state is reached.
- **Remediation:** Eradicate polling entirely. Direct R2 uploads confirm state via client XHR status events; status cards update locally without polling the application server.

---

#### Finding P0-4: Synchronous Remote Service Call Inside MySQL Transaction
- **File & Line:** [`ServiceCompanyJobApplicationController.php:3389`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L3389)
- **Vulnerability:** Inside `store()`, `GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId)` executes a synchronous HTTP REST request to Elasticsearch while the MySQL transaction is open.
- **Remediation:** **Hard Requirement: No remote service or network call inside the database transaction.** Elasticsearch indexing must be decoupled via the Transactional Outbox Pattern and processed by background queue workers.

---

### ⚠️ P1 — High Severity Flow & Reliability Findings

#### Finding P1-1: Deprecate Legacy Server Extraction (`resumExtract`) & Cleanup (`unsetFile`)
- **File & Line:** [`create.blade.php:3747–3807`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3747-L3807) & [`ServiceCompanyJobApplicationController.php:13592`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L13592)
- **Vulnerability:** Legacy flow POSTs parsed resume text to PHP for extraction and calls `/unset-file` for temp cleanup.
- **Remediation:** Completely sever these legacy calls from `create.blade.php`. Form inputs are populated strictly via the decoupled event contract `'jero:candidate-parsed'` or through manual recruiter entry.

#### Finding P1-2: Lack of Submission Idempotency Key (Duplicate Risk)
- **File & Line:** [`create.blade.php:3576–3620`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3576-L3620)
- **Vulnerability:** Duplicate prevention relies solely on disabling the submit button in JavaScript (`submitButton.disabled = true;`). Under network dropouts, browser refreshes, form re-entrancy, or proxy retries, duplicate candidate rows are inserted into MySQL.
- **Remediation:** Introduce a cryptographically random `client_submission_uuid` generated before submission and enforced as an idempotent transaction key on the server.

#### Finding P1-3: Artificial 2000ms Sleep Delay on Form Submit
- **File & Line:** [`create.blade.php:3606`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3606) & [`create.blade.php:2518`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L2518)
- **Vulnerability:** Form submit forces a `setTimeout(..., 2000)` delay before initiating the AJAX request, degrading user responsiveness.
- **Remediation:** Remove artificial timeouts. Dispatch submission immediately upon successful client validation.

#### Finding P1-4: Fragile Asynchronous Timeout Cascades for Location Selection
- **File & Line:** [`create.blade.php:3827–3851`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3827-L3851)
- **Vulnerability:** Uses hardcoded `setTimeout(1000)` for state and `setTimeout(2000)` for city selection. Breaks if network latency exceeds timer values.
- **Remediation:** Use deterministic client-side location hierarchy lookups without asynchronous timers.

---

## 4. Decoupled Subsystem Boundary & Event Contract

To maintain clean separation between the Candidate Creation page and the independent Resume Parsing engine:

### 4.1 The Pluggable Event Contract
The Candidate Form in `create.blade.php` exposes a standardized custom DOM event listener:

```javascript
/**
 * JERO V4 Pluggable Autofill Contract
 * Listen for candidate parsed events fired by the independent parsing module.
 * Form functions with 100% fidelity even if the parsing module is completely absent.
 */
window.addEventListener('jero:candidate-parsed', function (event) {
    if (!event.detail) return;
    const data = event.detail;

    // Autofill text inputs only if currently empty (never overwrite user edits)
    if (data.full_name && !$('#full_name').val()) $('#full_name').val(data.full_name);
    if (data.email && !$('#email').val()) $('#email').val(data.email);
    if (data.phone && !$('#phone').val()) $('#phone').val(data.phone);
    if (data.secondary_email && !$('#secondrary_email').val()) $('#secondrary_email').val(data.secondary_email);
    if (data.secondary_mobile && !$('#secondary_mobile').val()) $('#secondary_mobile').val(data.secondary_mobile);
    if (data.careersummary && !$('#careersummary').val()) $('#careersummary').val(data.careersummary);
    if (data.linkedin && !$('#linkedin').val()) $('#linkedin').val(data.linkedin);
    if (data.github && !$('#github').val()) $('#github').val(data.github);
    if (data.behance && !$('#behance').val()) $('#behance').val(data.behance);

    // Autofill matched skills via Tagify
    if (tagifyskill && Array.isArray(data.skills) && data.skills.length > 0) {
        tagifyskill.addTags(data.skills);
        if (typeof window.refreshCandidatePrimarySkills === 'function') {
            window.refreshCandidatePrimarySkills();
        }
    }

    // Trigger instant validation refresh
    if (typeof validator !== 'undefined' && validator.revalidateField) {
        validator.revalidateField('full_name');
        validator.revalidateField('email');
        validator.revalidateField('phone');
    }
});
```

### 4.2 Non-Blocking Guarantee
* All candidate form fields remain **100% interactive and editable at all times**.
* If a resume is uploaded without parsing, or if the parser fails or is disabled, the recruiter enters candidate details manually.
* Form submission is **never blocked or gated by parsing status**.

---

## 5. Tiered Reference Data & Search Architecture (Skills & Locations)

### 5.1 Rejection of Monolithic Client JSON Dumps
Dumping entire multi-megabyte geographical or skill datasets into monolithic browser JSON files (e.g. 1.5M worldwide cities) causes severe decompressed memory bloat, high JSON parse latency, expensive IndexedDB read locks, and mobile device crashes.

### 5.2 Tiered Architecture Specification

#### A. Skills Tiering:
1. **Tier 1 (Instant):** Job-Required Skills (`candidateRequiredSkills`), pre-injected in Blade layout (`< 5 KB`).
2. **Tier 2 (Session Cache):** Canonical Industry Skills & Common Aliases (~3,000 core skills, ~120 KB gzip) loaded once into `IndexedDB` and queried via Web Worker.
3. **Tier 3 (User Free-Text):** User is always permitted to add custom skills not present in the dictionary.

#### B. Location Tiering & Sharding:
Do not load all cities globally. Shard geographically:
```text
storage/app/public/geo/
    countries.json               (~12 KB, 250 countries)
    states/{country_code}.json   (~8 KB per country)
    cities/{state_code}.json     (~15 KB per state shard)
```
* **Client Behavior:**
  - Initial page load fetches only `countries.json` or uses the tenant's primary operating country.
  - When a country or state is selected, the client progressively fetches and caches the specific sub-shard in `IndexedDB`.
  - Prefix search is offloaded to a Web Worker using a Trie or lightweight index (`minisearch` or `Fuse.js`), ensuring `< 5ms` match latency and **0 requests to Elasticsearch or MySQL**.

---

## 6. Client Browser Resource & Performance Budgets

To protect client devices from memory exhaustion, the Add Candidate page enforces strict browser resource limits:

| Resource Metric | Maximum Target Budget | Mitigation Mechanism |
| :--- | :--- | :--- |
| **Initial Compressed JS Payload** | **< 1.5 MB gzip** | Exclude unused bundle dependencies; load dynamic features on demand. |
| **Reference Dictionary Footprint** | **Progressively loaded (< 200 KB initial)** | Geographically sharded JSON datasets cached in `IndexedDB`. |
| **Trie Search RAM Allocation** | **< 20 MB active heap** | Lightweight Trie structures; zero DOM-cloned search lists. |
| **Main-Thread Blocking Time** | **< 50 ms (INP Green Tier)** | Form input calculations and validations bounded to `< 16 ms`. |
| **Candidate Typing Latency** | **< 16 ms (60 FPS responsiveness)** | In-memory Trie lookup; zero synchronous DOM thrashing. |

---

## 7. Security, Tenancy & Backend Authority Specification

### 7.1 Separation of Concerns: Client UX vs. Server Authority
* **Client-Side Validation:** Designed for immediate, responsive user feedback (UX).
* **Server-Side Validation:** The **authoritative source of truth for security, tenancy, and data integrity**.

### 7.2 Non-Bypassable Server-Side Validation Checklist on `store()`
The server performs authoritative validation on every submission:
1. **Authentication & Authorization:** Valid user session, tenant verification (`auth()->user()->company_id`), Gate permission `add_job_applications`.
2. **Job Tenancy & Ownership:** Authoritative check that `request->job_id` belongs to `auth()->user()->company_id`.
3. **Resume Session Authorization:** Authoritative verification that `resume_session_uuid` belongs to the authenticated tenant and is in `Uploaded` state.
4. **R2 Object Integrity & Size:** Pre-flight verification that the object exists in R2 storage, matches expected content-type, and does not exceed size limits.
5. **Business & Duplicate Rules:** Tenant-configured duplicate candidate checks (`app_duplicate` policy on email and phone).
6. **Input Sanitization:** HTML entity cleansing and database constraint validation across all submitted fields.

---

## 8. Idempotency & Resilient Submission Architecture

### 8.1 Client-Generated Submission UUID
To prevent duplicate records from double-clicks, browser refreshes, or network retries:
1. The client generates a unique UUIDv4 (`client_submission_uuid`) when the form initializes.
2. The UUID is passed as a hidden field and via the `X-Idempotency-Key` header on `POST /job-applications/store`.
3. The server tracks submitted keys in Redis / MySQL table `submission_idempotency_keys` with a 60-second TTL:
   - If key exists and status is `Processing`: Return `409 Conflict` (Duplicate submission in progress).
   - If key exists and status is `Completed`: Replay original success JSON response with redirect URL.
   - If key is new: Mark `Processing` and proceed with transaction.

---

## 9. Direct Cloudflare R2 Upload & Orphan-Cleanup Lifecycle

### 9.1 Upload State Machine
```text
[ CREATED ] ──► [ UPLOADING ] ──► [ UPLOADED ] ──► [ CLAIMED ] ──► [ ATTACHED ]
                                        │
                         (If abandoned > 24 Hours)
                                        │
                                        ▼
                                   [ EXPIRED ] ──► [ DELETED via R2 Lifecycle ]
```

### 9.2 Path & Lifecycle Isolation
* **Temporary Unclaimed Storage Path:**  
  `candidate-temp/{company_id}/{session_uuid}/{sanitized_filename}`
* **Cloudflare R2 Bucket Lifecycle Policy:**  
  All objects under the `candidate-temp/` prefix are configured with a strict **24-hour expiration TTL**. If a user uploads a resume but abandons the page without submitting, R2 automatically purges the object with zero custom cron overhead.
* **Permanent Claimed Storage Path:**  
  Upon successful candidate creation inside the MySQL transaction, the backend moves the object to:  
  `Companies/{company_slug}_{company_id}/Resumes/{year}/{month}/{candidate_id}_{sanitized_filename}`

### 9.3 Upload Protocol & Integrity Verification
Do not rely on ETag alone. The presigned upload and finalization handshake enforces:
* Exact file size bounds (`X-File-Size` verified against R2 metadata).
* Content-Type restrictions (`application/pdf`, `application/msword`, `application/vnd.openxmlformats...`).
* HMAC signature expiration (15-minute token TTL with automatic client re-presign if expired before upload).

---

## 10. Transactional Outbox Pattern & Queue Architecture

### 10.1 Dedicated Queue Allocation
**Never route Elasticsearch indexing jobs through the `'emails'` queue.**  
Candidate indexing must not wait behind third-party SMTP dispatch or notification bursts. The queue hierarchy is strictly partitioned:

| Queue Name | Purpose | Priority | SLA Target |
| :--- | :--- | :---: | :---: |
| `candidate-write` | Critical DB operations & outbox processing | High | < 100 ms |
| `candidate-index` | Elasticsearch 7.17 document ingestion & sync | High | < 500 ms |
| `candidate-ranking`| Team rank & skill match calculations | Normal | < 5,000 ms |
| `notifications` | Candidate confirmation emails & SMS alerts | Low | < 30,000 ms |

### 10.2 Outbox Event Pattern
To guarantee consistency without synchronous remote calls:
```php
DB::beginTransaction();
try {
    $jobApplication->save();
    $addJobWiseCandidate->save();
    
    // Write outbox event inside same transaction
    DB::table('outbox_events')->insert([
        'event_type'    => 'CandidateCreated',
        'aggregate_id'  => $jobApplication->id,
        'payload'       => json_encode([
            'candidate_id'  => $jobApplication->id,
            'job_id'        => $jobApplication->job_id,
            'company_id'    => $jobApplication->company_id,
            'submitted_by'  => $submitted_by,
        ]),
        'status'        => 'pending',
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}

// Dispatch event worker post-commit
ProcessOutboxEventJob::dispatch($outboxEventId)->onQueue('candidate-write');
```
* **Performance Target:** P95 database transaction duration **< 15–20 ms** under defined production-like load.
* **Guaranteed Reliability:** If the queue broker temporarily fails, the outbox table retains the event, and a fallback poller replays uncommitted events.

---

## 11. Multi-Profile Load-Testing & Verification Methodology

A single simple load test is insufficient. Concurrency validation requires **5 distinct test profiles** executed via k6 / Locust against production-equivalent hardware (Dedicated 32 GB RAM Web Server, 32 GB RAM MySQL, 32 GB RAM Elasticsearch):

```
                                  5-PROFILE LOAD TESTING SUITE
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                 │
│  [ Profile A: 10K Open Pages Concurrency ]                                                      │
│  Simulate 10,000 users holding the Add Candidate page open simultaneously, performing local      │
│  data entry and browsing requirements.                                                          │
│  └── Pass Criteria: 0 interaction QPS to Laravel/MySQL/ES; Client memory < 100 MB.              │
│                                                                                                 │
│  [ Profile B: 10K Users Typing Concurrency ]                                                    │
│  Simulate 10,000 users typing into skills and location inputs with debounced input events.      │
│  └── Pass Criteria: Elasticsearch search QPS = 0; Laravel app QPS = 0; Input latency < 16 ms.  │
│                                                                                                 │
│  [ Profile C: 1K Simultaneous Submissions ]                                                     │
│  Simulate 1,000 users submitting candidate forms at the exact same second.                      │
│  └── Pass Criteria: P95 Tx duration < 25 ms; 0 deadlocks; 0 duplicate candidates; Outbox lag < 2s│
│                                                                                                 │
│  [ Profile D: Burst Traffic Ramp ]                                                              │
│  Ramp virtual users from 0 -> 2,000 -> 5,000 -> 10,000 over a 5-minute interval.                │
│  └── Pass Criteria: HTTP 5xx error rate < 0.01%; No connection pool exhaustion.                 │
│                                                                                                 │
│  [ Profile E: Chaos & Failure Injection ]                                                        │
│  Inject faults: Take Elasticsearch cluster offline; simulate R2 timeouts; simulate broker drop. │
│  └── Pass Criteria: Candidate records persist reliably in MySQL; Outbox queues replay indexing  │
│                      automatically upon Elasticsearch recovery.                                 │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 12. Formal Acceptance Criteria Matrix (Production Gate)

Before marking the V4 Add Candidate feature as production-ready, every test in this gate must pass:

| Gate ID | Verification Test | Pass Criteria | Verification Method |
| :---: | :--- | :--- | :--- |
| **G-01** | Skill Typing Traffic | **0 HTTP requests to Laravel / ES** | Chrome DevTools Network Tab / Access Logs |
| **G-02** | Location Typing Traffic | **0 HTTP requests to Laravel / ES** | Chrome DevTools Network Tab / Access Logs |
| **G-03** | Resume Upload Path | **0 PHP file bytes** (Direct R2 PUT only) | Web server network I/O profiler |
| **G-04** | Elasticsearch Typing QPS | **0 QPS to Elasticsearch cluster** | Elasticsearch cluster monitoring dashboard |
| **G-05** | MySQL Typing Queries | **0 SQL queries executed** | MySQL General Query Log inspection |
| **G-06** | Status Polling | **0 polling requests** (`/resume-status` 404/removed)| Web server access log audit |
| **G-07** | Form Submission Count | **Exactly 1 application HTTP request** | Network tab transaction audit |
| **G-08** | Duplicate Submit Re-entry | **0 duplicate records created** | Concurrent replay test with same idempotency key |
| **G-09** | Elasticsearch Outage | **Candidate successfully saved in MySQL** | Stop ES node; execute candidate submit |
| **G-10** | R2 Object Claim Integrity| **Cannot submit unconfirmed/tampered UUID** | Submit with invalid session UUID; expect 422 |
| **G-11** | Queue Broker Outage | **Candidate persists; Outbox recovers** | Stop Redis; submit candidate; verify outbox row |
| **G-12** | Expired Presigned URL | **Automatic client-side token refresh** | Wait 16 mins; initiate upload; expect success |
| **G-13** | 10K Open Pages Load | **P95 Latency < 150 ms; 0 DB connection errors** | k6 Concurrency Benchmark (Profile A) |
| **G-14** | 1K Simultaneous Submits | **P95 Tx < 25 ms; 0 deadlocks** | k6 Burst Benchmark (Profile C) |

---

## 13. Architectural Compliance Tracking

| Metric / Rule in AGENTS.md | Target Specification Standard | Verification Status |
| :--- | :--- | :---: |
| **Rule 1: 0% MySQL Reads on Interaction** | Zero DB reads between initial page load and final submit. | **Target Architecture** (Verified by G-05) |
| **Rule 2: Elasticsearch Read Scope** | Zero direct UI write locks; queries isolated to read domains. | **Target Architecture** (Verified by G-04) |
| **Rule 3: Client-First / Browser Execution**| Data entry, validation, and search run in user RAM. | **Target Architecture** (Verified by G-01, G-02) |
| **Rule 4: 10K Concurrency Target** | 10K open pages supported with zero server interaction strain. | **Unverified** (Pending Profile A Load Test) |
| **Rule 7: Zero Business Logic Mutations** | Tenant isolation, duplicate policies, and schema preserved. | **Requires Automated Regression Tests** |
| **Rule 14: UI & Metronic Standards** | Poppins font, max 14px, responsive 1366px–1080px, no 3D styles.| **Requires Visual Design QA** |
| **Rule 17: Complete Blade Isolation** | Styles, scripts, and state strictly encapsulated to create page. | **Requires Code Verification** |

---

## 14. Phase-Wise Implementation Roadmap

### Phase 1: Deprecate Legacy Server Extraction & Wire Pluggable Event Contract
- Remove `re_test()` and `/extract-resume-applications` calls from `create.blade.php`.
- Remove `/unset-file` AJAX calls.
- Implement the `jero:candidate-parsed` listener hook in `create.blade.php` to receive autofill data from the decoupled parsing subsystem.
- Deprecate `ServiceCompanyJobApplicationController::resumExtract` and `ServiceCompanyJobApplicationController::unsetFile`.

### Phase 2: Tiered Local Reference Dictionaries & Sharded Search
- Generate sharded static location files (`countries.json`, state/city shards).
- Build client Trie / IndexedDB search engine in a dedicated Web Worker.
- Replace AJAX endpoints `/skill-suggestions` and `/city-job-suggestions` with local search.

### Phase 3: Direct Cloudflare R2 Upload Hardening & Orphan Cleanup
- Remove duplicate upload logic in `handleFileInputChange()`.
- Enforce R2 24-hour expiration lifecycle on `candidate-temp/`.
- Remove `_startParsePoller` and the 4-second polling endpoint `/resume-status/{id}`.
- Wire `ResumePresign` state changes directly to the post-upload Metronic card.

### Phase 4: Backend CQRS Decoupling, Idempotency & Transactional Outbox
- Introduce `client_submission_uuid` and server idempotency middleware.
- Remove synchronous `GeneralMethod::JobApplicationsAddEditUpload` from `store()`.
- Implement `outbox_events` table and dispatch indexing via `candidate-index` queue.
- Remove artificial 2000ms `setTimeout` on form submission.

### Phase 5: Production Concurrency Testing & Acceptance Gate Sign-off
- Execute all 5 load testing profiles (A through E).
- Audit all 14 criteria in the Formal Acceptance Matrix.
- Achieve sign-off for deployment to `testing.jero.works`.
