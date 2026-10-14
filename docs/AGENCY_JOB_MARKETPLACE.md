# Agency Job Marketplace — Architecture V2.2: Master Implementation Specification
## Enterprise Operations, Multi-Dimensional State Governance, CQRS & Self-Healing Reconciliation

**Document Identifier:** `docs/AGENCY_JOB_MARKETPLACE.md`  
**Platform:** JERO B2B Talent Marketplace & GOD-Level ATS  
**Specification Version:** V2.2 (Final Freeze Master Implementation Specification)  
**Target Concurrency & Capacity:**  
- **10,000 Concurrent Connected Sessions / WebSocket Clients**  
- **1,200 RPS Marketplace Faceted Discovery & Search**  
- **250 RPS Transactional Mutations (Claims & Bench Submissions)**  
- **3,000 RPS Client RAM Cache & ETag Revalidations**  
**Data Architecture:** Strict CQRS | Zero Unnecessary MySQL Reads on Discovery/Listing Paths | 4M QPS DragonflyDB In-Memory Caching | Bounded Projection Staleness (P95 < 300ms)  
**Primary Controllers:**  
- Recruiter / Agency Operations: [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php)  
- SuperAdmin Command Center: [`app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php)  
**Primary Services:**  
- Discovery & Search Service: [`app/Services/MarketJobV2Service.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Services/MarketJobV2Service.php)  
- Bench Mutation Service: [`app/Services/BenchRequestMutationService.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Services/BenchRequestMutationService.php)  
- Moderation & Risk Engine: `app/Services/MarketplaceModerationService.php`  
- Coin Ledger Service: `app/Services/CoinLedgerService.php`  
- Reconciliation & Self-Healing Service: `app/Services/MarketplaceReconciliationService.php`  
**Primary Elasticsearch Indices:** `ats_jobs`, `ats_job_request`, `ats_candidates_bench`, `ats_agency_scorecards`  
**Authoritative MySQL Schema:** `database/schema/mysql-schema.sql` (217 tables)  
**Companion Architecture Blueprint:** [`docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md)

---

## 1. System Vision & Source-of-Truth Architectural Hierarchy

JERO is an enterprise multi-tenant B2B talent exchange platform that combines a **GOD-Level ATS** with an **Agency Talent Marketplace**. Hiring organizations (product companies, service companies, enterprises) exchange requisitions with certified staffing/agency partners through two distinct commercial mechanisms: **Outsource Job (Claim)** and **Submit Bench Talent**.

### 1.1 Strict Source-of-Truth Boundaries (CQRS Contract)
To guarantee data consistency without database deadlocks under 10,000 concurrent recruiters and partner agencies, the system enforces a strict boundary hierarchy:

```
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                  SOURCE-OF-TRUTH BOUNDARY CONTRACT                                       │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ TIER / COMPONENT      │ SYSTEM ROLE                  │ OPERATIONAL INVARIANT                             │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ MySQL (dbmaster)      │ Authoritative Transactional  │ ACID writes, row locks, atomic balance mutations, │
│                       │ Source of Truth              │ invariant validation. 0% SELECT queries on web    │
│                       │                              │ discovery and listing paths.                      │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ Elasticsearch Cluster │ Search & Read Projection     │ 100% of marketplace search, filters, aggregations,│
│ (ats_jobs, 60+ indices│ Tier (CQRS Engine)           │ and moderation queues. Sub-15ms P50 latency.     │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ DragonflyDB / Redis   │ Ephemeral In-Memory Cache    │ Distributed locks, idempotency keys, rate limits, │
│                       │ & Distributed Locks          │ and cached query ETags (4M QPS capacity).        │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ Transactional Outbox  │ Durable Event Handoff        │ Zero lost events. Guaranteed at-least-once delivery│
│ (sys_outbox_events)   │ Pipeline                     │ via Horizon background workers.                   │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ Laravel Reverb        │ Ephemeral Real-Time Broadcast│ WebSocket notification delivery only. Never        │
│                       │                              │ treated as a durable state store.                 │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ Browser RAM / IDB     │ Client-Side Acceleration     │ Local cache & trie-filtering. Server/ES is always  │
│                       │ Tier                         │ authoritative circuit-breaker fallback.          │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
```

> **Non-Negotiable Architecture Invariant:** No secondary projection (Elasticsearch, Redis, or Client Memory) may ever become authoritative. Authoritative state changes execute strictly via ACID transactions in MySQL.

### 1.2 Bounded Projection Staleness (Eventual Consistency Framework)
Real-world distributed systems cannot claim "zero staleness" at every nanosecond when using asynchronous projections. JERO defines a realistic, benchmarkable **Bounded Staleness Guarantee**:
- **Critical Business Invariants (Transactional Path):** **Zero Staleness (0ms).** Coin deductions, claim verifications, duplicate candidate checks, and state mutations execute directly against MySQL InnoDB with row-level locks.
- **Search & Discovery Grid (Projection Path):** **Bounded Staleness.** Background outbox workers project MySQL mutations to Elasticsearch with a guaranteed SLA:
  - **P95 Projection Propagation:** $< 300\text{ ms}$
  - **P99 Projection Propagation:** $< 1,500\text{ ms}$
- If an agency attempts an action on a listing that was recently modified, the transactional boundary in MySQL validates the state version and rejects stale interactions gracefully.

---

## 2. 6-Dimensional State Governance Architecture

### 2.1 The Legacy Pathology (Single State Overloading)
The legacy codebase overloaded a single integer (`jobs.public_market_status`) to represent multiple orthogonal concepts: visibility (`0: Private`), moderation (`1: Pending`, `2: Approved`, `3: Rejected`), capacity (`4: Limit Over`), and recruiter intent (`6: Unpublished`). This created edge cases where a job could not simultaneously be "Approved" and "Limit Reached" or "On Hold" and "Marketplace Approved".

### 2.2 The V2.2 Multi-Dimensional State Model
In Architecture V2.2, marketplace governance is decoupled into **6 independent, orthogonal dimensions**:

```
                                  [ REQUISITION CORE ]
                                           │
         ┌───────────────────┬─────────────┴─────┬───────────────────┬───────────────────┐
         ▼                   ▼                   ▼                   ▼                   ▼
  [ 1. Job Lifecycle ] [ 2. Publication ] [ 3. Moderation ] [ 4. Claim Cap ]  [ 5. Bench Talent ] [ 6. Commercial State ]
  ├── Active           ├── Private         ├── Not Required   ├── Open          ├── Disabled        ├── Configured
  ├── Hold             ├── Pending Review  ├── Pending        └── Limit Reached ├── Enabled         ├── Incomplete
  ├── Closed           ├── Published       ├── Auto-Approved                    └── Paused          ├── Locked
  └── Expired          ├── Unpublished     ├── Manual-Approved                                      └── Disputed
                       └── Withdrawn       ├── Rejected
                                           └── Suspended
```

```
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                 MULTI-DIMENSIONAL STATE MATRIX                                           │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ DIMENSION             │ ALLOWED VALUES               │ OPERATIONAL MEANING & GOVERNANCE                  │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 1. Core Job Lifecycle │ active, hold, closed,        │ Governed by Hiring Company in ATS. If not active, │
│    (`jobs.status`)    │ expired                      │ job is automatically excluded from all searches.  │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 2. Publication Intent │ private, pending_review,     │ Recruiter's declared intention for partner network│
│    (`market_pub_state`)published, unpublished,       │ distribution.                                     │
│                       │ withdrawn                    │                                                   │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 3. Moderation State   │ not_required, pending,       │ SuperAdmin & Risk Engine authority. Governs       │
│    (`market_mod_state`)auto_approved, approved,      │ whether the requisition passes the quality gate.  │
│                       │ rejected, suspended          │                                                   │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 4. Claim Capacity     │ open, limit_reached          │ Dynamic capacity cap based on claimed agencies    │
│    (`claim_capacity`) │                              │ vs. `global_settings.vendor_limit`.               │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 5. Bench Submissions  │ disabled, enabled, paused    │ Recruiter's toggle allowing agencies to submit    │
│    (`bench_submission`│                              │ matching bench consultants directly.              │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
│ 6. Commercial State   │ configured, incomplete,      │ Tracks whether placement fee (% of CTC / fixed fee│
│    (`commercial_state`locked, disputed               │ and credit payment days are valid and binding.   │
+───────────────────────+──────────────────────────────+───────────────────────────────────────────────────+
```

### 2.3 Strict Read-Only Deprecation of `public_market_status`
To protect the system from state corruption:
> **MANDATORY CODING RULE:** `public_market_status` MUST NEVER be directly mutated by any controller, service, or job in application code.

It is maintained **strictly as a read-only computed backward-compatibility projection** generated by Eloquent model accessors and database views:
- `0 (Private)`: `market_pub_state = 'private'` OR `jobs.status != 'active'`
- `1 (Pending)`: `market_pub_state = 'pending_review'` AND `market_mod_state = 'pending'`
- `2 (Live)`: `market_pub_state = 'published'` AND `market_mod_state IN ('auto_approved', 'approved')` AND `claim_capacity = 'open'`
- `3 (Rejected)`: `market_mod_state = 'rejected'`
- `4 (Limit Reached)`: `claim_capacity = 'limit_reached'` (Bench submissions remain active if enabled)
- `5 (Suspended)`: `market_mod_state = 'suspended'` (Quarantined for compliance)
- `6 (Unpublished)`: `market_pub_state = 'unpublished'`

---

## 3. Decoupled Claim & Candidate Sourcing Workflows

The platform strictly separates marketplace collaboration into two unambiguous, enterprise workflows:

```
                            [ Recruiter Publishes Requisition ]
                                             │
                       ┌─────────────────────┴─────────────────────┐
                       ▼                                           ▼
          [ Method 1: Outsource Job ]                 [ Method 2: Submit Bench Talent ]
          [    (Requisition Claim)   ]                [     (Direct Candidate Rec)    ]
                       │                                           │
         Recruiter authorizes vendor work            Job published for bench discovery
                       │                                           │
         Agency commits to position & terms          Agency recommends bench consultant
         via POST /jobs/market/claim                 via POST /jobs/recommandedrequestcondidate
                       │                                           │
         Atomic Coin Reservation & Ledger:           Atomic Coin Reservation & Ledger:
         ├── Reserve agency coins (38/39/40/41)      ├── Reserve agency coins (Event 25)
         ├── Commit staffing_company_relation        ├── Commit rec_candidate_assign (ac_status=0)
         └── Insert recommendation_relation          └── Insert sys_outbox_events
                       │                                           │
         Job IMMEDIATELY reflects in agency's        Candidate profile appears directly in
         "Direct Client Jobs" ATS Pipeline           recruiter's ATS App Tracker
         (Agency acts as dedicated vendor)           (Bench Talent Submissions Tab)
```

### 3.1 Method 1: "Outsource Job" (Requisition Claim Workflow)
- **Business Purpose:** Hiring company delegates requisition hunting to specialized agency partners under agreed placement commission terms (% of CTC or Fixed Fee).
- **Agency Action:** Agency reviews terms and clicks **"Accept & Outsource Job"** (`POST /jobs/market/claim`).

#### Decoupled Claim vs. Candidate Lifecycle State Machine
A claim represents a commercial agreement between two companies. It must **not** be tied to the lifecycle of a single candidate, because one claimed agency may submit 10 candidates.

```
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                  DECOUPLED TWO-TIER STATE MACHINES                                       │
+─────────────────────────────────────────────+────────────────────────────────────────────────────────────+
│ TIER 1: CLAIM AGREEMENT LIFECYCLE           │ TIER 2: CANDIDATE APPLICATION LIFECYCLE (PER CANDIDATE)    │
+─────────────────────────────────────────────+────────────────────────────────────────────────────────────+
│ AVAILABLE                                   │ SUBMITTED                                                  │
│   ↓                                         │   ↓                                                        │
│ CLAIM_REQUESTED                             │ SCREENING / STAGE 1 REVIEW                                 │
│   ↓                                         │   ↓                                                        │
│ CLAIMED                                     │ INTERVIEW SCHEDULED                                        │
│   ↓                                         │   ↓                                                        │
│ ACTIVE (Recruiters Assigned & Sourcing)     │ OFFER RELEASED                                             │
│   ↓                                         │   ↓                                                        │
│ COMPLETED (Hiring Cap Fulfilled)            │ JOINED (Placement Confirmed)                               │
│                                             │   OR REJECTED / WITHDRAWN                                  │
│ Failure States:                             │                                                            │
│ ├── EXPIRED (SLA breached with 0 CVs)       │                                                            │
│ ├── REVOKED (SuperAdmin manual takedown)    │                                                            │
│ └── CANCELLED (Client closed job early)     │                                                            │
+─────────────────────────────────────────────+────────────────────────────────────────────────────────────+
```

#### Configurable SLA Engine (Zero Hardcoded Rules)
The claim SLA and forfeiture penalties are dynamically resolved from `global_settings` and requisition priority:
- `claim_sla_hours`: Configurable per requisition priority:
  - *Urgent Positions:* 48 Hours
  - *Standard Positions:* 168 Hours (7 Days)
  - *Strategic / Executive Positions:* 240 Hours (10 Days)
- `claim_penalty_percentage`: Configurable (Default 50%). If the agency submits zero candidates within `claim_sla_hours`, the claim transitions to `EXPIRED`, the vendor slot returns to the public marketplace, and `claim_penalty_percentage` of the operational coins are forfeited to preserve ecosystem liquidity.

### 3.2 Method 2: "Submit Bench Talent" (Direct Candidate Recommendation)
- **Business Purpose:** Monetize pre-vetted bench consultants sitting on agency payrolls without negotiating requisition ownership or acting as an outsourced vendor.
- **Agency Action:** Agency clicks **"Submit Bench Talent"** (`.open-bench-talents`) $\rightarrow$ Selects consultant $\rightarrow$ Dispatches `POST /jobs/recommandedrequestcondidate/{jobId}`.
- **Recruiter App Tracker Destination:**
  - Consultant CV and rate card land directly in the recruiter's ATS App Tracker under **"Bench Requests / Recommended Candidates"** (`rec_candidate_assign`, `source_from_type = 1`, `ac_status = 0`).
  - **Recruiter Action:** Recruiter reviews rate and CV. Clicking "Accept" moves the candidate into active interview stages **without** transferring job ownership to that agency!

---

## 4. Recruiter Step 4 Publishing Matrix (Create / Edit Job)

In [`create.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/jobs/create.blade.php), Step 4 ("Review & Publish") presents 3 unambiguous, independent toggles with clear human-centric terminology:

```html
<div class="r4-visibility-list">
    <!-- Option A: Company Career Site -->
    <label class="r4-check-item">
        <input type="checkbox" class="r4-custom-check" name="visibility_career" id="rv_visibility_career" checked value="1">
        <span class="r4-check-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
        <span class="r4-check-content">
            <span class="r4-check-text">Publish on Company Career Site</span>
            <span class="r4-check-sublabel">Direct applications from public candidates on your branded portal</span>
        </span>
    </label>

    <!-- Option B: Outsource Job (Method 1) -->
    <label class="r4-check-item">
        <input type="checkbox" class="r4-custom-check" name="visibility_agency_outsource" id="rv_visibility_agency_outsource" value="1">
        <span class="r4-check-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
        <span class="r4-check-content">
            <span class="r4-check-title-row">
                <span class="r4-check-text">Outsource Job to Partner Agencies</span>
                <span class="r4-badge-recommended">Partner Sourcing</span>
            </span>
            <span class="r4-check-sublabel">Agencies accept your commercial terms, claim the position, and act as dedicated hiring vendors</span>
        </span>
    </label>

    <!-- Option C: Submit Bench Talent (Method 2) -->
    <label class="r4-check-item">
        <input type="checkbox" class="r4-custom-check" name="visibility_marketplace_bench" id="rv_visibility_marketplace_bench" checked value="1">
        <span class="r4-check-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
        <span class="r4-check-content">
            <span class="r4-check-title-row">
                <span class="r4-check-text">Accept Direct Bench Talent Submissions</span>
                <span class="r4-badge-recommended">Instant Bench</span>
            </span>
            <span class="r4-check-sublabel">Agencies cannot claim this job, but can share ready bench consultants directly to your candidate inbox</span>
        </span>
    </label>
</div>
```

---

## 5. Composite Risk Engine, Anti-Circumvention & Duplicate Intelligence

To maintain sub-25ms ingestion velocity at 10,000 concurrency, SuperAdmin cannot manually inspect every requisition. JERO deploys a **Composite Risk Engine** combining hard blocking rules with heuristic scoring.

```
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                  COMPOSITE RISK & MODERATION ENGINE                                      │
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                                                                          │
│   [ 1. HARD BLOCKING RULES (Zero-Tolerance Guardrails) ] ──────────────────────────────────────────┐     │
│   ├── Anti-Circumvention Leakage (Phone / Email / WhatsApp detected in JD)                        │     │
│   ├── Blacklisted Domain or Suspicious Employer Pattern                                           │     │
│   ├── Deep Market Salary Anomaly (Deviation factoring seniority, location, scarcity)              │     │
│   ├── Duplicate Requisition Match (Exact Hash or Semantic Similarity across history)               │     │
│   └── Flagged / Defaulting Tenant Profile                                                         │     │
│                                                                                                    │     │
│   ANY HARD BLOCK TRIGGERED? ──► MANDATORY SUPERADMIN REVIEW (market_mod_state = 'pending')        │     │
│                                                                                                    │     │
│   [ 2. HEURISTIC QUALITY SCORING (Evaluated only if Hard Blocks pass) ] ──────────────────────────┤     │
│   ├── Tenant Trust Tier (Verified GSTIN/CIN, Account Age >60d, 0 Strikes) ──────► 35 Pts           │     │
│   ├── Semantic JD Quality Score (Clarity, Responsibilities, Requirements) ──────► 25 Pts           │     │
│   ├── Compensation Sanity (Normalized CTC band vs. benchmark) ──────────────────► 20 Pts           │     │
│   └── Commercial Clarity (Explicit placement fee % and credit payment days) ────► 20 Pts           │     │
│                                                                                                    │     │
│   SCORE >= 85 AND ZERO HARD BLOCKS ──► AUTO-APPROVED (market_mod_state = 'auto_approved')         │     │
│   SCORE < 85                        ──► SUPERADMIN REVIEW QUEUE (market_mod_state = 'pending')     │     │
│                                                                                                          │
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

### 5.1 Hard Block Rule 1: Anti-Circumvention Regex Heuristics
The engine scans plain text in-memory before database persistence:
- **Phone Number Regex:** `/(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}|\b\d{10}\b/`
- **Personal Email Regex:** `/[a-zA-Z0-9._%+-]+@(gmail|yahoo|hotmail|outlook|proton|icloud)\.com/i`
- **WhatsApp Link Regex:** `/(wa\.me|chat\.whatsapp\.com|api\.whatsapp\.com)\/[a-zA-Z0-9]+/i`
- *Outcome:* Immediate quarantine to SuperAdmin queue with alert chip: `"Potential Off-Platform Circumvention Detected"`.

### 5.2 Hard Block Rule 2: Deep Market Salary Intelligence (No Rigid Floors)
Instead of a crude ₹100,000 floor or a flat percentage deviation, the engine calculates salary confidence based on 5 factors:
```php
$deviation = abs($postedSalary - $benchmarkSalary) / $benchmarkSalary;
$isExtremeAnomaly = ($deviation > 3.0 && $confidenceScore > 0.8 && ! $isExemptNicheRole);
if ($isExtremeAnomaly || ($postedSalary < 3000 && $benchmarkSalary > 300000)) {
    $hardBlockFired = 'REJ_SALARY_ANOMALY';
}
```

### 5.3 Semantic JD Quality Scoring (Beyond Word Count)
Evaluates genuine semantic structure across 8 vectors:
1. **Role Clarity:** Explicit job title and seniority level (+4 pts)
2. **Skill Mapping:** At least 3 verified Metronics skills mapped (+4 pts)
3. **Structured Responsibilities:** Clear duty bullet points (+4 pts)
4. **Structured Requirements:** Clear prerequisite skills (+4 pts)
5. **Work Mode & City:** Declared remote/hybrid/onsite with city (+3 pts)
6. **Experience Range:** Logical min and max experience (+2 pts)
7. **Notice Period Requirement:** Preferred joining timeline stated (+2 pts)
8. **Employment Type:** Explicit full-time, contract, or part-time (+2 pts)

### 5.4 Multi-Layer Historical Duplicate Detection Engine
Scans beyond active jobs to prevent churning or recycling rejected/closed positions:
- **Exact Duplicate:** Normalized SHA-256 hash of `(company_id + lowercase(title) + city_id)` matches any job created within 30 days $\rightarrow$ Hard Block `REJ_DUPLICATE_LISTING`.
- **Historical Semantic Duplicate:** Elasticsearch `more_like_this` query scanning across `active`, `published`, `closed`, `expired`, and `rejected` documents:
  - If cosine similarity $>0.92$ against an active job of the same client $\rightarrow$ Hard Block.
  - If cosine similarity $>0.90$ against a job rejected in the past 14 days $\rightarrow$ Quarantined with alert: `"Potential Recycled Rejection"`.

---

## 6. SuperAdmin Command Center (3-Panel Cockpit with AI Decision Radar)

Instead of forcing operators to repeatedly open and close drawers, SuperAdmin utilizes an enterprise **3-Panel Moderation Cockpit** optimized for 15.6" laptop screens:

```
+──────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ JERO SUPER ADMIN — MARKETPLACE OPERATIONS COMMAND CENTER                                  [15.6" Display]│
├─────────────────────────┬───────────────────────────────────────────────┬────────────────────────────────┤
│ PANEL 1: INGESTION QUEUE│ PANEL 2: REQUISITION SPECIFICATION & CONTEXT  │ PANEL 3: RISK RADAR & ACTIONS  │
│ (280px Width)           │ (Flexible Center Column)                      │ (340px Width)                  │
├─────────────────────────┼───────────────────────────────────────────────┼────────────────────────────────┤
│ 🔍 Filter & SLA Sort    │ Senior Full-Stack Engineer                    │ DECISION RADAR:                │
│ ─────────────────────── │ FinTech Global Solutions (Verified Enterprise)│ [✓ SAFE TO APPROVE (92% Conf)] │
│ ⚠️ #1042 — FinTech Corp │ Location: Bangalore (Hybrid) | Exp: 5-8 Years │ ────────────────────────────── │
│    SLA: 18m | Score: 92 │ CTC: ₹24,00,000 – ₹32,00,000 PA               │ ├── Trust Tier: 35/35 (Tier 1) │
│                         │                                               │ ├── JD Quality: 23/25 (Good)   │
│ 🛑 #1045 — Acme Staffing│ Commercial Terms:                             │ ├── Salary Sanity: 18/20 (Norm)│
│    SLA: 42m | Score: 64 │ 8.33% Placement Commission | 45-day payment   │ └── Anti-Leakage: 20/20 (Clean)│
│    [Contact Leak Alert] │ Max Vendor Cap: 5 agencies                    │                                │
│                         │                                               │ HARD BLOCK INSPECTION:         │
│ ⏳ #1050 — CloudNet     │ Responsibilities:                             │ [✓] Zero Phone/Email Leaks     │
│    SLA: 1h 12m | Sc: 81 │ - Architect React/Node micro-frontends...     │ [✓] No Historical Duplicates   │
│                         │ - Maintain sub-50ms API response budgets...   │                                │
│ ⏳ #1052 — DataCorp     │                                               │ ACTION CONTROLS:               │
│    SLA: 2h 05m | Sc: 88 │ Requirements:                                 │ [ ✓ APPROVE + PUBLISH ]        │
│                         │ - Strong proficiency in TypeScript, Docker... │ [ ✓ APPROVE (HOLD PUBLISH) ]   │
│                         │                                               │ [ ✕ REJECT WITH GUIDANCE ]     │
│                         │                                               │ [ ✎ REQUEST SPECIFIC EDIT ]    │
└─────────────────────────┴───────────────────────────────────────────────┴────────────────────────────────┘
```

### 6.1 Atomic Business Actions (Preserving Dimensional Separation)
The UI exposes separate controls that execute single atomic transactions in the backend:
1. **"Approve + Publish":** Atomically sets `market_mod_state = 'approved'` AND `market_pub_state = 'published'`. Immediately live on the marketplace.
2. **"Approve Only (Hold Publication)":** Sets `market_mod_state = 'approved'` while keeping `market_pub_state = 'unpublished'`, allowing the recruiter to launch when ready.
3. **"Reject with Guidance":** Sets `market_mod_state = 'rejected'`, records structured reason code, and dispatches remediation email.
4. **"Request Specific Edit":** Non-destructive feedback sending actionable prompts to the recruiter's ATS dashboard without rejecting the position outright.

---

## 7. Complete Cross-Touchpoint Synchronization

```
+─────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                 BIDIRECTIONAL STATE SYNCHRONIZATION MATRIX                             │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ TOUCHPOINT            │ ACTION TAKEN             │ STATE TRANSITION             │ CROSS-MODULE SYNC     │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 1. Add Job            │ Check Career / Outsource │ market_pub_state = published │ Initial persistence & │
│    (create.blade.php) │ / Bench checkboxes       │ market_mod_state = auto/pend │ Outbox queue dispatch │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 2. Edit Job           │ Uncheck Marketplace      │ market_pub_state = unpub     │ Agency Drawer button  │
│    (edit / update)    │ options B & C            │ market_mod_state = not_req   │ flips to "Publish";   │
│                       │                          │                              │ ES index delisted     │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 2. Edit Job           │ Modify Salary, Title,    │ If Risk Score < 85:          │ Re-enters moderation; │
│    (edit / update)    │ JD, or Skills            │ market_mod_state = pending   │ Recruiter alerted;    │
│                       │                          │ Else: market_mod_state=auto  │ Live ES update        │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 2. Edit Job / ATS     │ Change Job Status to     │ jobs.status = hold/closed    │ Automatically hidden  │
│    Quick Status       │ "Hold" or "Closed"       │                              │ from all agencies     │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 3. Agency Drawer      │ Click "Unpublish from    │ market_pub_state = unpub     │ Step 4 checkboxes     │
│    (spartnerlisting)  │ Marketplace"             │                              │ uncheck; ES wiped;    │
│                       │                          │                              │ Reverb WS broadcast   │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
│ 3. Agency Drawer      │ Click "Publish to        │ market_pub_state = published │ Step 4 checkboxes     │
│    (spartnerlisting)  │ Marketplace"             │ market_mod_state = auto/pend │ check; Live on grid;  │
│                       │                          │                              │ Drawer CTA toggles    │
+───────────────────────+──────────────────────────+──────────────────────────────+───────────────────────+
```

---

## 8. Financial Governance & Double-Entry Coin Ledger (`coin_ledger`)

To eliminate coin deduction discrepancies, double charges, and reconciliation disputes, all coin operations route through an **Immutable Double-Entry Coin Ledger with Multi-Coin Support**:

```sql
CREATE TABLE IF NOT EXISTS `coin_ledger` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `coin_type` ENUM('MARKETPLACE_COIN', 'PREMIUM_COIN', 'PROMOTION_CREDIT') NOT NULL DEFAULT 'MARKETPLACE_COIN',
  `event_id` INT(11) NOT NULL COMMENT 'Event code (25, 38, 39, 40, 41)',
  `reference_type` ENUM('job_claim', 'bench_recommendation', 'refund', 'penalty') NOT NULL,
  `reference_id` INT(11) NOT NULL COMMENT 'job_id or rec_candidate_assign_id',
  `debit` INT(11) NOT NULL DEFAULT 0,
  `credit` INT(11) NOT NULL DEFAULT 0,
  `balance_before` INT(11) NOT NULL,
  `balance_after` INT(11) NOT NULL,
  `idempotency_key` VARCHAR(64) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_coin_idempotency` (`idempotency_key`),
  KEY `idx_cl_company_created` (`company_id`, `created_at`),
  KEY `idx_cl_reference` (`reference_type`, `reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 9. Secure Real-Time Event Architecture & Distributed Tracing

To protect corporate confidentiality and enable end-to-end debugging across micro-services, broadcasting on unauthenticated public presence channels is strictly prohibited.

### 9.1 Distributed Tracing Event Envelope
All outbox events, queue messages, and WebSocket frames wrap payloads in an enterprise tracing envelope:

```json
{
  "event_id": "evt_01J8F948K93P29Z19X",
  "event_type": "JobMarketplaceStatusChanged",
  "event_version": 2,
  "aggregate_type": "job",
  "aggregate_id": 1042,
  "tenant_id": 42,
  "actor_id": 105,
  "correlation_id": "c1a93e82-2710-4bf2-a39c-5e4832bf64e1",
  "causation_id": "req_8492048102948201",
  "occurred_at": "2026-10-14T19:20:00Z",
  "payload": {
    "action": "published",
    "market_pub_state": "published",
    "market_mod_state": "approved",
    "show_vendor": "no",
    "allow_bench_submissions": true
  }
}
```

### 9.2 Channel Security Architecture
- **Agency Channel:** `private-marketplace.agency.{agencyCompanyId}`
- **Recruiter Channel:** `private-company.{recruiterCompanyId}`
- **SuperAdmin Channel:** `private-superadmin.moderation`

---

## 10. Resilient Outbox, Idempotent Replay & Dead-Letter Queue (DLQ)

To guarantee zero lost notifications and enable safe operational recovery:

```
[ MySQL Outbox Table ]
        │
        ▼
[ Horizon Worker ] ──(Attempt 1)──► Success: Commit & Mark Done
        │
        ├──(Failure)──► Backoff 5s ──► Attempt 2
        │
        ├──(Failure)──► Backoff 30s ──► Attempt 3
        │
        └──(Permanent Failure)──► [ Dead-Letter Queue (DLQ) ]
                                            │
                                            ▼
                           [ SuperAdmin Replay Console ]
                           - Inspect failure stack trace & payload
                           - Safe Idempotent 1-Click Replay
```

### Idempotent Replay Safety Protocol
Replaying a failed event must never trigger double-deductions or duplicate candidate recommendations:
1. Every consumer checks `processed_events` table before executing side-effects:
   ```sql
   INSERT INTO sys_processed_events (event_id, consumer_id, processed_at) VALUES (?, ?, NOW());
   ```
2. If `event_id + consumer_id` exists, the worker acknowledges the event and skips duplicate processing.

---

## 11. Automated MySQL ↔ Elasticsearch Self-Healing Reconciliation Engine

Even with transactional outboxes, edge-case failures (e.g. Elasticsearch cluster restarts or network partitions) can create projection drift. JERO implements an **Automated Self-Healing Reconciliation Engine**:

```
                              [ Scheduled Reconciliation Daemon ]
                              (Runs every 15 minutes in background)
                                               │
                                               ▼
                         [ Compare MySQL State vs. ES ats_jobs ]
                         - Checks active jobs created/updated in last 24h
                         - Validates public_market_status & state_version
                                               │
                      ┌────────────────────────┴────────────────────────┐
                      ▼                                                 ▼
             [ MATCH: 100% In Sync ]                           [ MISMATCH DETECTED ]
                      │                                                 │
                      ▼                                                 ▼
              Log Health Heartbeat                        [ Auto-Dispatches Repair Job ]
                                                          - Re-indexes specific document
                                                          - Purges Redis tenant cache
                                                          - Logs incident in Audit Trail
```

---

## 12. Elasticsearch Document Versioning & Optimistic Concurrency

To prevent race conditions where an older asynchronous event arrives after a newer update:
Every Elasticsearch document stores state versions:

```json
{
  "job_id": 1042,
  "state_version": 14,
  "projection_version": 38,
  "source_updated_at": "2026-10-14T19:22:00Z",
  "indexed_at": "2026-10-14T19:22:01Z"
}
```

- **Optimistic Concurrency Check:** When the Elasticsearch projection worker processes an event, it executes an Elasticsearch update with condition:
  `if (ctx._source.state_version >= params.new_state_version) { ctx.op = 'none'; }`
  This guarantees that **out-of-order events are safely dropped without overwriting fresh data**.

---

## 13. Dimensional Moderation Audit Log Schema

Correcting the single-integer status flaw in the audit logging table:

```sql
CREATE TABLE IF NOT EXISTS `job_marketplace_moderation_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_id` INT(11) NOT NULL,
  `company_id` INT(11) NOT NULL,
  `admin_id` INT(11) NOT NULL DEFAULT 0 COMMENT '0 for system auto-approval engine',
  `action` ENUM('submitted', 'auto_approved', 'manual_approved', 'rejected', 'suspended', 'delisted', 'resubmitted', 'claim_approved', 'claim_revoked') NOT NULL,
  `state_dimension` ENUM('publication', 'moderation', 'capacity', 'bench', 'commercial', 'lifecycle') NOT NULL,
  `previous_state` VARCHAR(32) NOT NULL,
  `new_state` VARCHAR(32) NOT NULL,
  `transition_reason` VARCHAR(64) DEFAULT NULL,
  `feedback_notes` TEXT DEFAULT NULL,
  `risk_score` INT(11) NOT NULL DEFAULT 0,
  `risk_flags` JSON DEFAULT NULL COMMENT 'Structured audit of triggered heuristics',
  `correlation_id` VARCHAR(64) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jmml_job_created` (`job_id`, `created_at`),
  KEY `idx_jmml_dimension_action` (`state_dimension`, `action`),
  KEY `idx_jmml_admin_created` (`admin_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 14. Transactional Support Indexes (Not Discovery Indexes)

> **Architectural Reminder:** The following indexes are strictly for **transactional mutations, row locks, foreign key checks, and reconciliation jobs** in MySQL. They are **never** used for public marketplace discovery queries.

```sql
-- Transactional Lookup & Lock Support Index
CREATE INDEX IF NOT EXISTS idx_jobs_txn_market_lookup 
ON jobs (company_id, status, created_at);

-- Recommendation Relations Verification Index (Method 1 Claims)
CREATE INDEX IF NOT EXISTS idx_rec_rel_marketplace_claim 
ON recommendation_relation (job_id, to_company_id, final_status);

-- Bench Candidate Review Pipeline Index (Method 2 Submissions)
CREATE INDEX IF NOT EXISTS idx_rec_candidate_assign_market 
ON rec_candidate_assign (source_jobid, source_company_id, ac_status, source_from_type);
```

---

## 15. Measurable End-to-End Service Level Objectives (SLOs)

Performance targets reflect **complete end-to-end latency** (Controller + Database + ES + Network overhead), benchmarked under full capacity:

| Service / Operation | P50 Latency Target | P95 Latency Target | P99 Latency Target | Max Error Rate | Test Concurrency & RPS Target |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **ES Marketplace Search** | **< 15 ms** | **< 65 ms** | **< 120 ms** | < 0.01% | 1,200 RPS across 10K clients |
| **SuperAdmin 3-Panel Cockpit** | **< 10 ms** | **< 35 ms** | **< 80 ms** | < 0.01% | 250 RPS operator load |
| **Recruiter Publish Action (TTFB)** | **< 12 ms** | **< 45 ms** | **< 90 ms** | < 0.001% | 100 RPS submission rate |
| **Method 1 Claim Mutation API** | **< 20 ms** | **< 55 ms** | **< 110 ms** | < 0.001% | 50 RPS atomic claim rate |
| **Method 2 Bench Submit API** | **< 18 ms** | **< 50 ms** | **< 95 ms** | < 0.001% | 80 RPS recommendation rate |
| **ES Outbox Projection SLA** | **< 120 ms** | **< 300 ms** | **< 1,500 ms** | < 0.001% | Background Horizon worker load |
| **Reverb WebSocket Delivery** | **< 25 ms** | **< 80 ms** | **< 150 ms** | < 0.05% | 10,000 connected clients |

---

## 16. Final Master Verification Checklist (Freeze V2.2)

1. [x] **Strict CQRS Source-of-Truth Hierarchy:** Authoritative MySQL mutations, Elasticsearch read projections, DragonflyDB caching, and durable outbox handoff.
2. [x] **Bounded Staleness Defined:** Realistic P95 $<300\text{ms}$ projection propagation replacing ambiguous "zero stale" claims.
3. [x] **6-Dimensional State Governance:** Core Lifecycle, Publication Intent, Moderation, Claim Capacity, Bench Permissions, and Commercial Terms decoupled.
4. [x] **Strict Read-Only Deprecation:** `public_market_status` locked against direct writes; computed from dimensional states.
5. [x] **Decoupled Claim & Candidate Lifecycles:** Claim state machine decoupled from candidate application pipeline.
6. [x] **Configurable SLA & Penalty Engine:** Dynamic `claim_sla_hours` and `claim_penalty_percentage`.
7. [x] **Composite Risk & Historical Duplicate Intelligence:** Hard block rules + semantic scoring + Elasticsearch MLT historical duplicate detection.
8. [x] **SuperAdmin 3-Panel Cockpit with AI Decision Radar:** High-density 15.6" screen layout with confidence indicators.
9. [x] **Multi-Coin Double-Entry Ledger:** `coin_ledger` schema tracking `coin_type`, debits, credits, and balances.
10. [x] **Secure Event Tracing & Authenticated WebSockets:** Private presence channels with `correlation_id` and minimal envelopes.
11. [x] **Idempotent DLQ & Replay Safety:** Outbox retry protocol protected by `sys_processed_events`.
12. [x] **Self-Healing Reconciliation Engine:** Background daemon repairing projection mismatches automatically.
13. [x] **Elasticsearch Optimistic Concurrency:** Document `state_version` preventing out-of-order race conditions.
14. [x] **Dimensional Audit Schema:** `job_marketplace_moderation_logs` tracking state transitions per dimension.
15. [x] **Empirical SLO Framework:** Complete separation of 10,000 concurrent sessions from RPS throughput targets.
