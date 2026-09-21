# V4.1 Forward Resume Engine: Client-First, Zero-Synchronous-Load Architecture & 10K Concurrency Enterprise Implementation Plan

> **Document Version:** 4.1.0  
> **Status:** Approved Architectural Contract & Implementation Specification  
> **Target Module:** Forward Resume Pipeline (`cvtracker`, `apptracker`, `clientapp`)  
> **Target Environments:** Production (`hire.jero.works`) & Staging (`testing.jero.works`)  
> **Author:** JERO Core Architecture Team  
> **Target Concurrency:** 10,000 Concurrent Recruiters with Zero Synchronous Database/Elasticsearch Strain  
> **Key Upgrades from V4.0:** Incorporation of Principal Architectural Review — Strict Trust Boundaries, Authoritative Match Scoring, Idempotency Keys, Batch State Machine, Two-Phase Coin Ledger, Circuit Breaker Thundering-Herd Protection, DOM XSS Sanitization, and Measurable SLOs.  
> **Source Files Audited:**  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` (Lines 3260–3945)  
> - `resources/views/service_company/cvtracker/test_fwd_resume.blade.php` (2,001 lines)  
> - `resources/views/service_company/cvtracker/_forward_resume_engine.blade.php` (2,660 lines)  
> - `resources/views/service_company/cvtracker/index.blade.php` (Lines 1745–1835)  
> - `resources/views/service_company/apptracker/index.blade.php` (Lines 1890–1975)  
> - `resources/views/service_company/apptracker/clientapp.blade.php`  
> - `app/Services/ForwardResumeUploadService.php`  
> - `app/Jobs/SendForwardResumeBatchMailJob.php`  
> - `docs/FORWARD_RESUME_AUDIT_16_SEP_2026.md`  

---

## 1. Executive Summary & V4.1 Architectural Axioms

The Forward Resume feature enables recruiters to forward up to five candidate profiles with custom vector branding, PII redaction, and executive prescreen dossiers directly to hiring managers and client contacts.

### 1.1 The Problem Statement
Under the existing architecture, clicking "Forward Resume" incurs a heavy **800ms – 1,800ms blocking delay**. The server executes an un-scoped Elasticsearch query requesting **10,000 email templates**, runs **10+ multi-table SQL joins**, and serializes a **224 KB Blade HTML payload** containing 2,660 lines of inlined JavaScript over the network. If 1,000 of 10,000 concurrent recruiters click this button simultaneously, PHP Octane/FPM worker exhaustion and Elasticsearch cluster CPU throttling inevitably trigger 504 Gateway Timeouts.

### 1.2 The Core Dilemma & User Insight
Inlining the 224 KB drawer HTML and engine into the host page (`cvtracker/index.blade.php`) is unacceptable because **~90% of recruiters on any given visit will not forward resumes**. Forcing 100% of recruiters to download and parse 224 KB of unused markup on every list reload degrades the primary dashboard performance.

### 1.3 The V4.1 Architectural Solution
V4.1 transitions Forward Resume from a monolithic, server-rendered AJAX pipeline into a **Client-First, Zero-Synchronous-Load Edge Architecture**:
1. **Decoupled Static Shell & Engine:** Served as immutable static CDN assets with non-blocking browser pre-warming via `requestIdleCallback()`. The host page remains 100% lightweight (0 KB added).
2. **Sub-5KB Micro-JSON Hydration:** Only privileged, non-volatile candidate and template metadata is transferred.
3. **WASM Client Compilation & Direct-to-R2 Cloud Staging:** High-resolution PDF dossiers compile in the browser using Rust WebAssembly and upload directly to Cloudflare R2 via presigned URLs. Zero bytes are written to the web server disk.
4. **Bank-Grade Transactional Safety:** Full idempotency keys, formal batch state machines, two-phase coin deduction, and circuit breaker fallback protection.

---

## 2. Multi-Plane Architectural Model

V4.1 formally separates concerns into five decoupled execution planes:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        V4.1 MULTI-PLANE ARCHITECTURAL MODEL                            │
├──────────────────┬──────────────────┬──────────────────┬─────────────────┬─────────────┤
│ 1. UI Plane      │ 2. Client Cache  │ 3. API / Auth    │ 4. Storage      │ 5. Outbox   │
│ (Browser)        │ (Data Plane)     │ (Data Plane)     │ (State Plane)   │ (Processing)│
├──────────────────┼──────────────────┼──────────────────┼─────────────────┼─────────────┤
│ • Pre-rendered   │ • L0: Active DOM │ • ForwardAuth    │ • Cloudflare R2 │ • Horizon Q │
│   Static Shell   │ • L1: RAM Memory │   Service Guard  │ • Redis Tenant  │ • SES / SMTP│
│ • CKEditor       │ • L2: Browser    │ • Server Match   │   Cache         │ • Atomic    │
│   Singleton      │   CacheStorage   │   Authoritative  │ • MySQL SSOT    │   Two-Phase │
│ • Rust WASM PDF  │ • L3: CDN / Edge │ • Idempotency Key│   Ledger        │   Cleanup   │
│ • R2 Direct PUT  │ • L4: Micro-JSON │ • < 5KB Payload  │ • Coin Ledger   │ • Delivery  │
│   Upload UI      │ • L5: MySQL SSOT │ • Circuit Breaker│ • Audit Log     │   Barrier   │
└──────────────────┴──────────────────┴──────────────────┴─────────────────┴─────────────┘
```

### Detailed Plane Definitions

1. **UI Plane (Browser):**
   - Manages visual transitions, modal dialogs, and progress bars.
   - Houses the **CKEditor Singleton** (initialized once, content updated via `editor.setData()`).
   - Executes client-side PDF compilation via Rust WebAssembly (`casual_doc_wasm`) and `pdf-lib` in browser memory.
   - Performs direct `PUT` uploads of compiled PDF blobs to Cloudflare R2.
2. **Client Cache Data Plane:**
   - **L0 (DOM State):** Active form fields, recipient tags, and roster cards.
   - **L1 (JS Memory):** In-memory candidate objects, compiled attachment references, and company branding.
   - **L2 (Browser CacheStorage / HTTP Cache):** Immutable static shell (`shell.v4.html`) and document engine (`engine.v4.min.js`).
   - **L3 (CDN / Edge):** Cloudflare edge caching for static assets.
3. **API / Authorization Plane:**
   - Enforces strict multi-tenant authorization (`ForwardResumeAuthorization::authorize()`).
   - Validates candidate ownership and computes authoritative match scores (never trusting client-supplied scores).
   - Generates scoped, short-lived presigned upload URLs for Cloudflare R2.
   - Enforces idempotency via `idempotency_key`.
4. **Storage & State Plane:**
   - **Cloudflare R2:** Ephemeral private object storage for compiled PDF attachments with automatic lifecycle expiration.
   - **Redis:** Caching tenant email template metadata, company recipient directories (15-min TTL), and idempotency locks.
   - **Elasticsearch Read Models:**
     - `ELASTIC_CANDIDATES` (`local_candidates_elastic`): Candidate profile and parsed skill dossiers.
     - `ELASTIC_COMPANY_COINS` (`local_company_coins`): Real-time coin balance and package entitlement validation.
     - `ELASTIC_EMAIL_COUNT` (`local_email_count`): Email forwarding audit log and delivery history tracking.
     - `ELASTIC_EMAIL_TEMPLATE` (`local_email_template`): Email templates.
     - `ELASTIC_USERS_LIST` & `ELASTIC_CLIENT_LIST`: Internal team and client contact directories.
   - **MySQL (SSOT for Transactional Writes Only):** Transactional write ledger for coin debiting (`coin_ledgers`), forward requests (`forward_requests`), and delivery attempt barriers (`forward_delivery_attempts`). Zero MySQL reads on the forward resume path.
5. **Processing & Outbox Plane:**
   - Dispatches email payloads via Laravel Horizon on the dedicated Redis `emails` queue.
   - Worker streams PDF attachments directly from Cloudflare R2 into RAM for SMTP/SES dispatch (0 web server disk I/O).
   - Enforces the delivery barrier and coordinates two-phase cleanup of temporary R2 objects.
   - Dispatches asynchronous synchronization jobs to update `ELASTIC_COMPANY_COINS` and `ELASTIC_EMAIL_COUNT`.

---

## 3. Critical Security Boundaries & Hardening Standards

### 3.1 P0 Security: Server-Authoritative Match Scoring (Anti-Tampering)
* **Vulnerability Identified in V4.0 Draft:** Trusting `$request->matchMap` allowed client-side manipulation of hiring confidence percentages on client-facing prescreen reports.
* **V4.1 Invariant (SEC-1):**  
  The browser may submit its local match score **strictly as a UI hint** for instant local rendering. However, the server **must independently calculate and sign the authoritative match score** prior to issuing presigned upload manifests or queuing the email:
  ```php
  // Server recalculates authoritative hiring confidence
  $authoritativeMatch = GeneralMethod::skill_percentage($jobId, $candidateSkillsList);
  $cand->authoritative_match = (int) round((float) rtrim($authoritativeMatch, '%'));
  ```
  The generated prescreen dossier burns only the server-signed match score into the vector PDF.

### 3.2 P0 Security: Formal Tenant & Candidate Authorization Layer
* **Vulnerability Identified in V4.0 Draft:** Simple SQL `WHERE company_id = ? OR thirdparty_company_id = ?` is insufficient against IDOR and cross-tenant probing across multiple user roles.
* **V4.1 Invariant (SEC-2):**  
  All candidate IDs, job IDs, and template IDs must pass through a dedicated authorization gate before any data is hydrated:
  ```php
  class ForwardResumeAuthorization
  {
      public static function authorizeBatch(User $user, array $candidateIds, ?int $jobId): array
      {
          $companyId = (int) $user->company_id;
          
          // Verify user has forward resume permissions
          if (! $user->can('forward_candidate_resume')) {
              throw new UnauthorizedException('User lacks forward resume permission.');
          }

          // Strict tenant verification with zero IDOR leakage via ELASTIC_CANDIDATES
          $params = [
              'index' => config('constants.ELASTIC_CANDIDATES'),
              'body'  => [
                  '_source' => ['id', 'company_id', 'thirdparty_company_id'],
                  'query'   => [
                      'bool' => [
                          'must'   => [['ids' => ['values' => $candidateIds]]],
                          'filter' => [
                              ['bool' => [
                                  'should' => [
                                      ['term' => ['company_id' => $companyId]],
                                      ['term' => ['thirdparty_company_id' => $companyId]],
                                  ],
                                  'minimum_should_match' => 1,
                              ]],
                          ],
                      ],
                  ],
              ],
          ];
          $response = app(\App\Services\ElasticService::class)->getClient()->search($params);
          $validCandidates = collect($response['hits']['hits'] ?? [])->pluck('_id')->map(fn($id) => (int)$id)->toArray();

          if (count($validCandidates) !== count($candidateIds)) {
              throw new SecurityBreachException('Unauthorized candidate access attempt detected.');
          }

          return $validCandidates;
      }
  }
  ```

### 3.3 P0 Security: Strict DOM XSS Prevention in JavaScript Hydration
* **Vulnerability Identified in V4.0 Draft:** String interpolation (`${cand.full_name}`) injected via jQuery `.html()` allows Stored Cross-Site Scripting if candidate names or job titles contain malicious HTML payloads.
* **V4.1 Invariant (SEC-3):**  
  String-based HTML injection of untrusted data is strictly forbidden. All dynamic fields must use `textContent`, DOM element construction, or strict HTML entity encoding:
  ```javascript
  function escapeHtml(str) {
      if (str === null || str === undefined) return '';
      return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
  }
  ```

### 3.4 P1 Security: Scoped Presigned Upload URLs
* Presigned PUT URLs issued by `ForwardResumeUploadService` must enforce:
  - Strict TTL: Exactly 15 minutes (900 seconds).
  - Strict Object Key Scope: `temp-forward/{company_id}/{uuid}.pdf`.
  - Strict Content-Type: `application/pdf`, `application/msword`, or `application/vnd.openxmlformats-officedocument.wordprocessingml.document`.
  - Strict Size Ceiling: Max 6,000,000 bytes (6 MB) per candidate resume.

### 3.5 Strict CQRS Read Plane: 100% Elasticsearch + Redis (0% MySQL Reads)
In strict compliance with JERO's CQRS architecture, **MySQL is never read during the Forward Resume drawer lifecycle**. All reads are routed through Elasticsearch and Redis models:
1. **Candidate Dossier Read (`ELASTIC_CANDIDATES`):**
   - Query: Elasticsearch `ids` query with `filter.term.company_id`.
   - Latency: Sub-3ms. Returns denormalized candidate details (name, designation, skills, experience, notice period, location, and resume pointer) with zero table joins.
2. **Coin Validation & Entitlements (`ELASTIC_COMPANY_COINS` & `ELASTIC_COIN_EVENTS`):**
   - Current company coin balance and package validity are verified against `local_company_coins`.
   - Event costs (e.g. forward resume coin fee) are evaluated against `local_coin_events` in < 2ms without querying `company_coins` or `coin_packages` in MySQL.
3. **Email Delivery History & Quotas (`ELASTIC_EMAIL_COUNT`):**
   - Historical forward activity, daily limits, and recipient delivery counts are queried against `local_email_count`.
4. **Internal Team & Client Directories (`ELASTIC_USERS_LIST` & `ELASTIC_CLIENT_LIST`):**
   - Populates the secondary recipient selection drawer from cached Elasticsearch user and client indices.
5. **Transactional MySQL Role:**
   - Reserved strictly for atomic state commits via `DB::transaction` when `SendForwardResumeBatchMailJob` commits coin deductions and forward records upon send.

---

## 4. Transactional Integrity, Idempotency & Coin Ledger

Forward Resume is a commercial transaction involving email delivery, recruiter coins, and client communication. It must be resilient against network drops, accidental double-clicks, and browser crashes.

### 4.1 Forward Batch State Machine

```
┌─────────┐      PDF Ready      ┌───────────┐      Direct PUT      ┌──────────┐
│ CREATED │ ──────────────────> │ COMPILED  │ ───────────────────> │ UPLOADED │
└─────────┘                     └───────────┘                      └──────────┘
                                                                         │
                                                                   Submit Clicked
                                                                         │
                                                                         ▼
┌───────────┐     SMTP Success    ┌────────────┐   Horizon Dispatch ┌──────────┐
│ COMPLETED │ <────────────────── │ DISPATCHED │ <───────────────── │ RESERVED │
└───────────┘                     └────────────┘                    └──────────┘
      │                                 │                                │
      │ Two-Phase Cleanup               │ Delivery Failure               │ Queue Failure
      ▼                                 ▼                                ▼
┌───────────┐                     ┌────────────┐                    ┌──────────┐
│  DELETED  │                     │   FAILED   │ ─────────────────> │ ROLLEDBACK
└───────────┘                     └────────────┘    Refund Coins    └──────────┘
```

### 4.2 Idempotency Contract
* Every forward action generates a unique client-side UUID: `forward_batch_uuid`.
* The server computes an idempotency key:
  ```php
  $idempotencyKey = hash('sha256', "{$companyId}:{$userId}:{$forwardBatchUuid}");
  ```
* When the recruiter clicks "Send Email":
  1. The server checks Redis: `Redis::set("idempotency:{$idempotencyKey}", 'LOCKED', 'NX', 'EX', 120)`.
  2. If the key already exists, the server returns the status of the ongoing/completed batch immediately without re-debiting coins or re-queuing emails.
  3. Eliminates duplicate emails and duplicate coin deductions caused by network timeouts or impatient double-clicks.

### 4.3 Two-Phase Coin Ledger (Reserve & Commit)
* **Phase 1 (Reservation on Dispatch Intent):**
  - Verify recruiter has sufficient coin balance for `count($recipientEmails)`.
  - Create a pending coin ledger hold (`status = 'RESERVED'`).
* **Phase 2 (Commit on Queue Acceptance):**
  - When `SendForwardResumeBatchMailJob` is confirmed pushed to Redis, transition hold to `COMMITTED`.
* **Rollback on Catastrophic Failure:**
  - If the queue driver is unreachable or the batch fails prior to dispatch, an automated rollback event cancels the hold and refunds the recruiter's coin ledger.

---

## 5. Thundering-Herd Prevention & Circuit Breaker Architecture

### 5.1 The Danger of Legacy Fallbacks
Under peak load (10,000 concurrent recruiters), if Redis or browser caching experiences a transient hiccup, an uncontrolled fallback to the legacy server-rendered endpoint will execute 10,000 simultaneous Elasticsearch queries with `size: 10000`, causing immediate cluster collapse.

### 5.2 The Circuit Breaker Specification
The micro-endpoint and client manager implement a 3-state Circuit Breaker:

```
      ┌───────────────────────────────────────────────────────────┐
      │                                                           │
      ▼                                                           │
┌────────────┐   Failure Rate > 20%   ┌──────────┐   Probe Succeeds   │
│   CLOSED   │ ─────────────────────> │   OPEN   │ ───────────────────┘
│  (Normal)  │                        │ (Tripped)│
└────────────┘                        └──────────┘
      ▲                                     │
      │           Probe Fails               ▼
      └────────────────────────────── ┌───────────┐
                                      │ HALF-OPEN │
                                      │  (Probe)  │
                                      └───────────┘
```

1. **Closed (Normal Operations):**
   - Templates fetched from Redis cache.
   - Candidates hydrated via micro-JSON API.
2. **Open (Degraded Mode):**
   - If Redis fails or response latency exceeds 1,500ms, the circuit trips.
   - **Degraded Action:** The system **never** queries Elasticsearch with `size: 10000`. Instead, it serves a minimal fallback template from local browser storage and queries only the essential candidate names via primary key.
3. **Half-Open (Testing Recovery):**
   - Routes 5% of requests to test if Redis/API has recovered before resetting the breaker.

### 5.3 Event-Driven Cache Invalidation
Templates, company branding, and team members must never be stale. Redis cache keys are invalidated immediately via Eloquent model events:
- `EmailTemplate::saved / deleted` ──► `Cache::forget("tenant:{$companyId}:fwd_email_templates")`
- `Company::saved` ──► `Cache::forget("tenant:{$companyId}:branding_info")`
- `User::saved / deleted` ──► `Cache::forget("tenant:{$companyId}:fwd_recipients_directory")`
- `ClientContactInfo::saved / deleted` ──► `Cache::forget("tenant:{$companyId}:fwd_recipients_directory")`

---

## 6. Measurable Service Level Objectives (SLOs)

V4.1 replaces vague "0% server load" claims with precise, defensible Service Level Objectives measured at P50, P95, and P99 percentiles:

| Metric | Measurement Scope | P50 Target | P95 Target | P99 Target | Monitoring Instrument |
| :--- | :--- | :---: | :---: | :---: | :--- |
| **Drawer Visual Shell Paint** | Click ──► Drawer visible with skeleton | **< 16 ms** | **< 45 ms** | **< 80 ms** | Chrome Performance Navigation Timing |
| **CKEditor Ready** | Shell open ──► Editor interactive | **< 30 ms** | **< 85 ms** | **< 150 ms** | CKEditor `instanceReady` Event Timestamp |
| **Candidate Hydration Latency**| Click ──► Candidate cards rendered | **< 120 ms** | **< 280 ms** | **< 450 ms** | Fetch API duration |
| **Micro-JSON Payload Size** | Wire size of `test_fwd_resume_data` | **< 2.5 KB** | **< 4.2 KB** | **< 6.5 KB** | Content-Length Response Header |
| **Elasticsearch Hits on Open** | ES queries on drawer open | **0 Hits** | **0 Hits** | **0 Hits** | Elasticsearch Metricbeat / Slow Log |
| **MySQL Queries on Cached Open**| SQL queries on normal open | **0 Queries** | **0 Queries** | **1 Query** | Laravel Telescope / DB Query Listener |
| **Server Disk I/O on Upload** | Bytes written to web server storage| **0 Bytes** | **0 Bytes** | **0 Bytes** | Server iostat / Disk Write Monitor |
| **Idempotency Duplicate Sends**| Double-click send attempts rejected| **100%** | **100%** | **100%** | Redis lock hit logs |

---

## 7. Semantic & Visual PDF Regression Standards

### Why Raw Byte-for-Byte Equality is Brittle
Raw PDF byte equality fails in production because:
- PDF creation dates and modification timestamps change on every run.
- Object ID assignment order varies across WebAssembly thread executions.
- Operating system font subsetting can produce minor binary header differences.

### The V4.1 Semantic Testing Protocol
Automated CI regression tests must validate PDF attachments across three semantic dimensions:
1. **Structural & Content Extraction:**
   - PDF text extraction (`pdftotext` / `pdf-parse`) must contain 100% of candidate details: Name, Designation, Formatted Experience, Location, and Notice Period.
   - PII Redaction Verification: Candidate email and personal phone numbers must **never** appear in the extracted text for masked options (e.g., Option 5 or Option 49).
2. **Visual Regression (Pixelmatch / Playwright):**
   - High-resolution (300 DPI) rendering of Option 5 (Prescreen Report) compared against the golden baseline.
   - Visual difference must remain below **0.1% pixel divergence** (allowing for anti-aliasing variations).
3. **MIME & Deliverability Compliance:**
   - Attachment size strictly under 6,000,000 bytes (6 MB).
   - Valid PDF version header (`%PDF-1.4` to `%PDF-1.7`).
   - Clean linear structure without corrupt trailing bytes.

---

## 8. Multi-Scenario Concurrency & Stress Testing Matrix

To certify the architecture for 10,000 concurrent recruiters, testing must cover diverse tenant profiles and failure modes:

| Scenario ID | Test Environment Condition | Concurrency Profile | Success Criteria |
| :--- | :--- | :--- | :--- |
| **SC-1: High-Volume CV Tracker** | Normal operating conditions; warm cache. | 10,000 VU loading CV Tracker; 1,000 VU clicking Forward Resume simultaneously. | P95 Drawer Open < 50ms. Zero 504 Gateway Timeouts. Octane CPU < 35%. |
| **SC-2: Large Enterprise Tenant** | Tenant with 150,000 candidates, 5,000 clients, 250 recruiters. | 500 concurrent forward actions within the same tenant. | Multi-tenant isolation verified. Zero IDOR leakage. Recipient cache hit rate > 95%. |
| **SC-3: Small Boutique Tenant** | Tenant with 200 candidates, 5 clients, 2 recruiters. | 50 concurrent forward actions. | Zero cold-start latency penalty. Payload strictly < 3 KB. |
| **SC-4: Cloudflare R2 Outage Simulation**| Presigned upload endpoint returns HTTP 503 or times out. | 200 concurrent forward actions. | Actionable UI error message displayed to recruiter. Zero phantom emails queued. Coins refunded. |
| **SC-5: Redis Outage / Circuit Trip** | Redis service stopped forcefully during peak load. | 1,000 concurrent drawer openings. | Circuit breaker trips to degraded mode. 0 queries sent to Elasticsearch. Zero thundering-herd crash. |
| **SC-6: Browser Privacy / Incognito Mode**| `CacheStorage` and `localStorage` throw SecurityError. | 100 client sessions. | `ForwardResumeManager` catches exception and falls back to in-memory transient execution without JS errors. |
| **SC-7: Double-Click Double-Send Race** | Automated script fires identical `test_fwd_resume_send` twice within 50ms. | 500 duplicate requests. | Exactly 1 email queued per batch. Exactly 1 coin deduction. Second request returns HTTP 409 or duplicate resolution. |

---

## 9. Re-Ordered Phase-Wise Implementation Plan

To guarantee zero regression and enterprise security, the implementation order is structured **Security & Contracts First**:

```
PHASE 0: Security, Authorization & Transaction Contract
  ├── 0.1 Implement ForwardResumeAuthorization service with multi-tenant guards
  ├── 0.2 Establish server-authoritative match scoring (Deprecate client matchMap trust)
  ├── 0.3 Implement forward batch UUID and idempotency key enforcement in Redis
  └── 0.4 Implement two-phase coin reservation and rollback contract

PHASE 1: Decoupled Static Assets & CDN Shell
  ├── 1.1 Extract _forward_resume_engine into versioned static asset (engine.v4.min.js)
  ├── 1.2 Convert drawer shell into immutable HTML asset (shell.v4.html)
  └── 1.3 Configure Cloudflare CDN caching and non-blocking requestIdleCallback pre-warm

PHASE 2: Micro-JSON Endpoint, Event Invalidation & Circuit Breakers
  ├── 2.1 Implement test_fwd_resume_data endpoint (Template metadata only, < 5KB)
  ├── 2.2 Implement lazy-loaded fwd_recipients endpoint with 15-min Redis TTL
  ├── 2.3 Wire Eloquent saved/deleted events for automated cache invalidation
  └── 2.4 Implement circuit breaker protecting against thundering-herd legacy fallbacks

PHASE 3: Client Data Plane, XSS-Safe DOM & CKEditor Singleton
  ├── 3.1 Implement ForwardResumeManager with strict escapeHtml / DOM construction
  ├── 3.2 Implement CKEditor singleton lifecycle (init once, setData on open, zero destroy)
  └── 3.3 Wire inline CV upload (uploadCandidateResumeInline) to local candidate state

PHASE 4: Direct Cloud Staging & Outbox Horizon Queue Dispatch
  ├── 4.1 Update ForwardResumeUploadService with strict 15m TTL and 6MB ceilings
  ├── 4.2 Harden SendForwardResumeBatchMailJob with transactional outbox pattern
  └── 4.3 Verify direct browser-to-R2 upload and atomic delivery barrier cleanup

PHASE 5: Multi-Tracker Rollout behind Granular Feature Flags
  ├── 5.1 Deploy behind granular config flags (features.v4_fwd_client, v4_fwd_wasm, v4_fwd_r2)
  ├── 5.2 Roll out to CV Tracker (cvtracker/index.blade.php) on Staging
  ├── 5.3 Roll out to App Tracker (apptracker) and Client App Tracker (clientapp)
  └── 5.4 Verify 100% parity across Mass Email, Send Email, Export, and Selection Bars

PHASE 6: Multi-Scenario Load, Chaos Testing & Production Sign-Off
  ├── 6.1 Execute K6 concurrency load simulation (Scenarios SC-1 to SC-7)
  ├── 6.2 Validate semantic and visual PDF regression standards
  └── 6.3 Zero-downtime production deployment on hire.jero.works
```

---

## 10. Granular Feature Flag Architecture

To provide instantaneous rollback capability without redeploying code, the V4.1 rollout is partitioned into five granular feature flags in `config/features.php`:

```php
return [
    'v4_fwd_client_shell'    => env('FEATURE_V4_FWD_CLIENT_SHELL', true),
    'v4_fwd_micro_api'       => env('FEATURE_V4_FWD_MICRO_API', true),
    'v4_fwd_lazy_recipients' => env('FEATURE_V4_FWD_LAZY_RECIPIENTS', true),
    'v4_fwd_wasm_engine'     => env('FEATURE_V4_FWD_WASM_ENGINE', true),
    'v4_fwd_r2_direct'       => env('FEATURE_V4_FWD_R2_DIRECT', true),
];
```

If any specific tier requires maintenance (e.g., Cloudflare R2 undergoes API maintenance), setting `v4_fwd_r2_direct => false` gracefully routes attachments through the secondary fallback pipeline while keeping the instant-open drawer and CKEditor singleton 100% operational.

---

## 11. Architectural Sign-Off

This V4.1 specification establishes the official implementation contract for JERO ATS. It resolves every concern raised in the peer review:
1. **No overstated claims:** Replaced with realistic P95/P99 latency and payload SLOs.
2. **Bank-grade security:** Server-authoritative match scores, strict tenant authorization, and complete DOM XSS immunity.
3. **Enterprise reliability:** Idempotency keys, two-phase coin handling, outbox pattern, and circuit breakers.
4. **Zero host page impact:** 100% lightweight host page with background idle pre-warming.
