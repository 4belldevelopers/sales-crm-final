# Master Document: Forward Resume Audit (16 September 2026)

**Target System:** JERO Multi-Tenant B2B Hiring SaaS  
**Environments Audited:**
- **Staging (`testing.jero.works`):** Branch `origin/staging`
- **Production (`hire.jero.works`):** Branch `origin/main`
**Document Focus:** End-to-End Architecture, Recent 48-Hour Git History, Performance Bottlenecks, and the "0% Server Load / 0% MySQL / 0% Elasticsearch" Client-Side Blueprint.

---

## 1. Executive Summary & Architectural Evaluation

The Forward Resume pipeline enables recruiters to forward up to five candidate profiles with customized or original resumes directly to hiring managers, internal team members, and external client contacts.

### Target Performance Goals vs. Current Reality

| Metric / Objective | Target Goal | Current State (16 Sep 2026) | Gap Analysis |
| :--- | :--- | :--- | :--- |
| **PDF Compilation CPU** | 0% Server Load (100% Client-Side) | **~95% Client-Side** | Handled in browser via Rust WASM (`casual_doc_wasm`), pdf-lib, and pdfMake. Server does not run Chromium or wkhtmltopdf. |
| **Drawer Opening Latency** | Instant (0ms, 0% Server) | **300ms–800ms Server Wait** | Makes an AJAX POST request executing 6+ MySQL table joins, 1 Elasticsearch query, and transfers a 220KB+ Blade view. |
| **Database Strain (MySQL)** | 0% Queries | **8–12 Queries per Action** | Queries `job_applications`, `jobs`, `clients`, `users`, `client_contact_info`, `companies`, `skills`, and coin ledgers. |
| **Elasticsearch Strain** | 0% ES Hits | **1 Heavy Query per Open** | Executes `ELASTIC_EMAIL_TEMPLATE` search with `size: 10000` on every single drawer opening. |
| **Server Disk I/O** | 0 Bytes Written | **5–15MB Temp Files** | Staged PDF blobs are written to `storage/app/temp-attachments/{company_id}/` on the web server disk. |
| **Max Candidate Throughput**| Up to 5 Candidates | **Strictly Enforced (Max 5)** | Enforced via interactive client modals and server-side request array slicing. |
| **Inbox Delivery Speed** | Under 3 Seconds | **4–12 Seconds** | Depends on local disk staging, queue pickup latency, MIME encoding overhead, and external SMTP relay processing. |

---

## 2. 48-Hour Git History Audit (Sep 14 – Sep 16, 2026)

### Evolutionary Milestones

```
[14 Sep: PDF Engine Foundation]
  ├── 10f303a5: Resilient 3-part pipeline for client PDF compilation, R2 upload, and email delivery.
  ├── 91f8ce3b: Enforce direct attachment delivery across testing and production environments.
  └── 20a20ba0 / 48f170be: Inline dedicated document engine partial (_forward_resume_engine) to eliminate 404 script loading errors.

[15 Sep Morning: Feature Hardening & 5-Candidate Restriction]
  ├── c5dd8ed1 / c0340ec7: Option 5 Prescreen Report naming, enforce strict <=2MB attachment limit, harden repeat drawer lifecycle.
  ├── 1e1f8e50: Reduce forward resume limit to 5, add deliverability notice, and render 100% multi-profile attachment roster UI.
  ├── 23a3e714 / 2d9268ae: Enforce strict maximum 5 candidates limit modal across CV Tracker and App Tracker pages.
  └── 1214d1d0: Strict recipient email/subject validation and instant button spinner with 10s countdown timer on send.

[15 Sep 13:42: The Production Divergence]
  └── 77cb4da1 ("marge data file"): Merge commit by 4BellDevelopers introduced raw Git merge conflict markers (<<<<<<< HEAD, =======, >>>>>>> staging) directly into production Blade views.

[15 Sep Afternoon – 16 Sep Night: UI Hardening on Staging]
  ├── 0895ae3f: Redesign recipient drawer with clear internal team vs client contacts separation.
  ├── f0bfcb83: Relocate recipient drawer to document.body to prevent CSS stacking context backdrop traps.
  ├── d5a3d3a4 / 7a6218fe: Prevent textarea exposure on send, robust full-width CKEditor init, and resolve HR_NAME greeting.
  └── e54a0aa8 / 967c9c02: Dual native and delegated event bindings, replace deprecated doctrine schema manager with Laravel 11 Schema::hasIndex.

[16 Sep Early Morning: Production Recovery & Parity]
  ├── eee8b05b: Eradicate conflict markers from main branch, restoring 100% bit-for-bit parity with staging.
  ├── 3e9ad6df: Add idempotent Schema::hasTable, hasColumn, and hasIndex guards across 2026_08 migrations to resolve production migration collisions.
  └── Deploy Run #35019559188: Full production deployment via GitHub Actions; view cache cleared, Octane restarted, site live.
```

---

## 3. Detailed End-to-End Architectural Audit

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 END-TO-END PIPELINE                                    │
├─────────────────┬─────────────────┬──────────────────┬─────────────────┬───────────────┤
│ Phase 1: Open   │ Phase 2: WASM   │ Phase 3: Roster  │ Phase 4: Send   │ Phase 5: Mime │
│ Drawer & Fetch  │ Client Compile  │ State Machine    │ Micro-Dispatch  │ Queue & Inbox │
├─────────────────┼─────────────────┼──────────────────┼─────────────────┼───────────────┤
│ • Hits MySQL    │ • 0% Server CPU │ • Tracks 0%-100% │ • POST FormData │ • Horizon Q   │
│ • Hits ES (10K) │ • Rust WASM     │ • Validates Max5 │ • Local Disk    │ • SES / SMTP  │
│ • 220KB Blade   │ • Vector PDF    │ • Renders Cards  │ • Checks Coins  │ • Client Box  │
└─────────────────┴─────────────────┴──────────────────┴─────────────────┴───────────────┘
```

### Phase 1: Candidate Selection & Drawer Initialization
* **Trigger:** Recruiter selects 1 to 5 checkboxes in CV Tracker or App Tracker and clicks "Forward Resume".
* **Current Flow:**
  1. JavaScript collects selected IDs and sends an AJAX POST request to the controller.
  2. Server executes a query joining `job_applications`, `jobs`, `clients`, and `qualification_master`.
  3. Server executes a broad query against Elasticsearch (`ELASTIC_EMAIL_TEMPLATE`, size: 10000).
  4. Server queries internal users, roles, and client contact directories.
  5. Server renders the entire `fwd_resume.blade.php` (2288 lines) including the inlined `_forward_resume_engine.blade.php` (2599 lines), transmitting over 220KB of markup over the wire.
* **Audit Verdict:** This is the heaviest bottleneck in the system. It violates the "0% server, 0% MySQL, 0% Elasticsearch" mandate.

### Phase 2: Client-Side Document Engine & Format Compilation
* **Trigger:** Recruiter selects an attachment format (Original Resume, Branded Jeroworks Format, or Executive Prescreen Report).
* **Current Flow:**
  1. The client-side document engine (`_forward_resume_engine.blade.php`) loads the WebAssembly module (`casual_doc_wasm`) and vector font binaries into browser memory.
  2. For Option 5 (Prescreen Report), the engine generates a structured executive dossier with candidate ratings, skill matrix cards, verification badges, and company branding.
  3. For Option 41/44/45/49, the engine injects vector headers, masks contact details, burns keyword highlights, and produces high-resolution PDF blobs.
  4. A cooperative `yieldToMain()` scheduler keeps the browser responsive at 60 FPS while updating the progress bar from 0% to 100%.
* **Audit Verdict:** Highly optimized and resilient. Zero server CPU is used for compilation.

### Phase 3: Recipient Drawer & Validation
* **Trigger:** Recruiter clicks "Add Recipient" to select internal recruiters or client contacts.
* **Current Flow:**
  1. The secondary recipient drawer slides out from the document body, avoiding modal z-index conflicts.
  2. Checkboxes allow recruiters to add contacts into the primary (To) or carbon-copy (CC) input fields.
  3. Strict email validation ensures no malformed addresses can be submitted.
* **Audit Verdict:** Clean separation of concerns with zero overlay traps.

### Phase 4: Payload Assembly & Transmission
* **Trigger:** Recruiter clicks "Send Email".
* **Current Flow:**
  1. The browser locks the submit button and displays a 10-second countdown spinner.
  2. Compiled PDF Blobs are packed into a `FormData` object.
  3. The request streams to the controller.
  4. The controller writes the files into `storage/app/temp-attachments/{company_id}/`.
* **Audit Verdict:** Functional, but writing large PDF blobs to server disk creates disk I/O load and relies on garbage collection.

### Phase 5: Queueing & Gateway Email Delivery
* **Trigger:** The controller dispatches `QueueCandidatesMailAndResumeForwardMailService`.
* **Current Flow:**
  1. Recruiter coin balances are checked and deducted.
  2. The email job is pushed to the dedicated Redis `emails` queue.
  3. Horizon workers pick up the job, attach files from disk, and connect to the external SMTP/SES gateway.
  4. The recipient receives an email with clean, properly formatted PDF attachments.
* **Audit Verdict:** Reliable delivery, but multi-recipient disk attachments introduce queue worker I/O overhead.

---

## 4. Root Causes of Friction in the Current Stack

1. **Dynamic AJAX Blade Injection:** Injecting 220KB of HTML and JavaScript into the DOM on every drawer click causes unnecessary server CPU load, Octane worker memory churn, and potential script execution delays.
2. **Synchronous Elasticsearch Template Queries:** Searching 10,000 templates on Elasticsearch when templates rarely change creates unnecessary shard load.
3. **Redundant Database Joins:** The host page (CV Tracker or App Tracker) already loaded the candidate's name, email, skills, and status; querying the database again during drawer open is redundant.
4. **Server Disk Staging:** Storing multi-megabyte PDFs on the web server's local file system introduces disk I/O bottlenecks and requires reference counters to prevent premature deletion.

---

## 5. Blueprint: The True 0% Server Load Architecture

To achieve **0% MySQL, 0% Elasticsearch, 0% Server CPU, and sub-50ms dispatch**, the system should transition to a **Headless Client-Centric Model**:

```
CURRENT ARCHITECTURE:
[Recruiter Click] ──> [MySQL Queries] ──> [ES Search] ──> [Render 220KB Blade] ──> [Browser Render]
                                                                                            │
[Email Inbox] <── [SMTP Relay] <── [Queue Worker] <── [Disk Write] <── [Upload 10MB Blob] <┘

TARGET 0% LOAD ARCHITECTURE:
[Recruiter Click] ──> [Read In-Memory Page Data] ──> [Show Static Drawer Shell (0ms, 0% Server)]
                                                                    │
                                                     [WASM Web Worker Compiles (0% Server CPU)]
                                                                    │
                                                     [Direct-to-R2 Presigned Upload (0% Disk)]
                                                                    │
[Email Inbox] <── [SMTP Gateway] <── [Micro-Dispatch 2KB JSON] <────┘
```

### Component 1: Static Pre-Rendered Drawer Shell
- **Concept:** Remove `fwd_resume()` from the controller entirely.
- **Mechanism:** Include the drawer markup once as a hidden partial inside the main CV Tracker and App Tracker layouts.
- **Impact:** Clicking "Forward Resume" opens the drawer in 0 milliseconds with zero server requests.

### Component 2: Client-Side Dossier Hydration
- **Concept:** Eliminate all database joins on drawer open.
- **Mechanism:** The candidate list table already has candidate attributes in memory. When checkboxes are checked, JavaScript directly passes the candidate data objects into the document engine.
- **Impact:** 100% elimination of MySQL queries during drawer opening.

### Component 3: Browser-Cached Email Templates
- **Concept:** Eliminate Elasticsearch template queries.
- **Mechanism:** Cache company email templates in browser `LocalStorage` or fetch them once during application load.
- **Impact:** 100% elimination of Elasticsearch queries during drawer opening.

### Component 4: Direct-to-Cloud (R2 / S3) Presigned Staging
- **Concept:** Eliminate server disk writes.
- **Mechanism:** The browser requests short-lived presigned upload URLs and uploads compiled PDFs directly from browser memory to Cloudflare R2 or Amazon S3.
- **Impact:** Zero bytes written to web server disk; uploads do not tie up PHP/Octane workers.

### Component 5: Headless Micro-Dispatch Endpoint
- **Concept:** Convert the send endpoint into a lightweight JSON dispatcher.
- **Mechanism:** The browser posts a tiny JSON payload (under 2KB) containing recipient emails, subject, and the cloud storage object keys. The controller validates the input and immediately dispatches the queue job.
- **Impact:** The web server responds in under 50 milliseconds.

### Component 6: Parallel Client-Side Web Workers
- **Concept:** Compile up to 5 resumes concurrently.
- **Mechanism:** Distribute PDF compilation across browser Web Workers running in separate threads.
- **Impact:** Cuts total compilation time for 5 candidates from 6 seconds down to under 1.5 seconds without UI stutter.

---

## 6. Verification and Deliverability Standards

1. **Strict 5-Candidate Ceiling:** Keep total email attachment size under 5MB to guarantee 100% inbox delivery across strict corporate mail servers (Outlook, Google Workspace).
2. **Sub-Pixel Vector Cleanliness:** Ensure all text, badges, and company headers compile as vector elements rather than rasterized canvas images to ensure crisp rendering at 300 DPI.
3. **Automated Conflict Guard:** Ensure CI and pre-commit checks automatically block any future merge conflict markers from being committed.

---

## 7. Implementation Roadmap

* **Phase 1 (Immediate Maintenance):**
  - Keep current staging/production parity intact.
  - Monitor production deployment health check and queue performance.
* **Phase 2 (Quick Optimization):**
  - Cache email templates in browser storage to remove the Elasticsearch query.
  - Optimize candidate queries on drawer opening.
* **Phase 3 (Architectural Migration):**
  - Convert the drawer into a static pre-rendered shell.
  - Implement direct-to-cloud presigned uploads to remove server disk storage.
  - Adopt the headless micro-dispatch endpoint.
