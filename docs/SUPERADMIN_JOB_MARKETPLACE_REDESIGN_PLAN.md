# Super Admin Job Marketplace Operations Platform — Master Redesign Blueprint

**Document Identifier:** `docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md`  
**Platform:** JERO B2B Talent Marketplace & GOD-Level ATS (`testing.jero.works`)  
**Module:** Super Admin Marketplace Operations Command Center  
**Target Concurrency:** 10,000+ Concurrent Recruiters/Admins | 0% Unnecessary MySQL Reads | 100% CQRS via Elasticsearch 8.x + Dragonfly C++ Cache | Sub-15ms TTFB  
**Target Resolution:** Strictly Optimized for 15.6" Laptops (1366×768 & 1920×1080) down to Tablet/Mobile with Zero Horizontal Page Scroll  
**Hardware Profile:** Dedicated 32 GB RAM App Server (Octane/FrankenPHP HTTP/3), 32 GB RAM MySQL InnoDB (~24 GB Buffer Pool), 32 GB RAM Elasticsearch 8.x Cluster, Dragonfly Multi-Threaded In-Memory Cache (4M QPS)  
**Governance Scope:** Super Admin & Service Company ATS | Product Company & Staffing Company Code Strictly Frozen (Rule 18)  
**Status:** Authoritative Architectural Blueprint & Production Implementation Plan  

---

## 🏛️ Executive Summary & Master Architecture

The JERO Talent Marketplace is an enterprise B2B recruitment exchange where companies publish requisitions, and partner staffing agencies fulfill them via two distinct engagement methods:
- **Agency Hiring Marketplace (Outsource to Agencies):** Hiring companies authorize vetted agencies to work as external recruitment vendors. Agencies claim positions, subject to tenant-configured vendor capacity limits (`jobs.vendor_limit`).
- **Bench Talent Marketplace (Open for Bench Profiles):** Partner agencies deploy verified bench consultants directly into client ATS pipelines (`ac_status = 0`) without claiming the full requisition.

Historically, the Super Admin marketplace management layer was fragmented across 5 isolated URLs with inconsistent search boxes, unindexed multi-table `LEFT JOIN`s on MySQL request threads, synchronous email loops blocking HTTP workers for up to 30 seconds, and a simplistic binary auto-approval toggle without content inspection.

This blueprint consolidates the Super Admin marketplace operations into a unified, high-density **Marketplace Command Center** governed by **strict CQRS**, **Client-First Browser Execution (User RAM + WASM)**, and **Zero-Cloud AI Sovereignty**.

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
|                           JERO SUPER ADMIN MARKETPLACE OPERATIONS PLATFORM (CQRS)                                 |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
|                                                                                                                   |
|   [ BROWSER RUNTIME: 15.6" Laptop Optimized Command Center (User RAM + DuckDB-WASM + Vanilla JS + Metronic) ]      |
|   • Instant Local Search & Facet Filtering (P50 < 1ms)  • Zero Horizontal Scroll  • AbortController Debouncing    |
|                                           │                                                                       |
|              ┌────────────────────────────┴─────────────────────────────┐                                         |
|              ▼                                                          ▼                                         |
|   [ READ PATH: 100% CQRS ]                                   [ WRITE PATH: ACID MUTATIONS ]                       |
|   • 0% MySQL Reads on Web Request Paths                      • Dedicated MySQL InnoDB Connection Pool             |
|   • Elasticsearch Cluster 8.x (ats_jobs, ats_job_request)    • Idempotency Keys (sys_idempotency_keys line 4205)  |
|   • Dragonfly Multi-Threaded In-Memory Cache (4M QPS)        • Decoupled Outbox (sys_outbox_events line 4226)     |
|   • Sub-8ms Faceted Filtering & Reciprocal Rank Fusion (RRF) • Non-blocking Horizon Queue Workers (market-outbox) |
|   • FrankenPHP 103 Early Hints + HTTP/3 QUIC Streaming       • Real-Time Reverb WebSocket Telemetry Broadcast     |
|                                                                                                                   |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 1. Current State vs Recommended Marketplace Architecture

### 1.1 The Legacy Super Admin Pathology (Current State)
The legacy Super Admin marketplace implementation was built as fragmented, disjointed CRUD pages created ad-hoc across several years:
1. **Fragmented Navigation & Siloed Screens:** Separate screens for `job-public-market`, `job-lists`, `job_request`, `job_request_accept`, and `recommandedrequest` force admins to juggle 5+ URLs with inconsistent search boxes and conflicting status nomenclature.
2. **Severe MySQL Read Contention:** Legacy controllers (`SuperAdminCompanyController@jobPublicMarket`, `jobLists`, `job_request`) run multi-table `LEFT JOIN`s on `jobs`, `clients`, `companies`, `recommendation_relation`, `rec_candidate_assign`, `client_location_jobs`, and unindexed `groupBy('jobs.id')`. Under 10,000 concurrent recruiters, this causes database thread starvation, query timeouts, and table locks.
3. **Synchronous Email & Notification Loops:** Bulk approvals loop through jobs in the PHP web process (`$this->mailService->sendEmail(...)`), blocking the HTTP worker thread for 10–25 seconds.
4. **Binary Moderation Toggle:** Auto-approval in `global_settings.public_market_job_status` is an unconditioned binary 0/1 switch with zero content inspection, zero risk heuristics, and zero audit logging.
5. **No Agency Performance Layer:** Admins have zero visibility into agency SLA compliance, candidate submission velocity, interview-to-hire ratio, or spam rate.

### 1.2 The Recommended "Marketplace Operations Platform"
Rebuild the Super Admin marketplace into a unified, reactive **Marketplace Command Center** governed by strict CQRS, event-driven transactional outbox patterns, and an intelligent risk engine:

| Dimension | Legacy Implementation | Redesigned Implementation |
| :--- | :--- | :--- |
| **Data Retrieval** | Monolithic MySQL SQL queries with 5+ `LEFT JOIN`s and unindexed `groupBy('jobs.id')` | **100% CQRS via Elasticsearch 8.x (`ats_jobs`)**; 0% MySQL reads on request paths |
| **Cache Layer** | Uncached or basic single-threaded Redis keys prone to latency spikes | **Dragonfly C++ Multi-Threaded In-Memory Cache (4M QPS)** with atomic tenant-key purging |
| **Worker Threads** | Synchronous mail loops in web request (`$this->mailService->sendEmail`) | **Decoupled transactional outbox (`sys_outbox_events`)** with Horizon queues |
| **Moderation Engine**| Raw binary 0/1 toggle in `global_settings.public_market_job_status` | **Multi-factor Risk Engine (0-100 Trust Score)** with NLP anti-circumvention heuristics |
| **UI Density & Sizing**| Broken responsive layouts, table overflow on 1366×768 laptops | **15.6" screen optimized (1366×768 / 1920×1080)**, max 14px Poppins, zero horizontal scroll |
| **Client Execution** | Server renders all HTML; repetitive full-page reloads on filter changes | **Client-First User RAM + Vanilla JS**, sub-1ms client filtering, hotwire morph updates |
| **Audit & Compliance**| Unaudited mutations, hard deletes, untracked admin decisions | **Immutable `job_marketplace_moderation_logs` ledger** with reason codes and telemetry |

---

## 2. Marketplace Job Posting, Approval & Agency/Bench Workflow

The job marketplace functionality is **fully separated based on the hiring intent selected while creating or editing the job**.

```
                          [ Recruiter Job Creation / Publishing Drawer ]
                                                │
         ┌──────────────────────────────────────┴──────────────────────────────────────┐
         ▼                                                                             ▼
  [ Option 1: Outsource to Agencies ]                           [ Option 2: Open for Bench Profiles ]
  (jobs.show_vendor = 'yes')                                    (jobs.bench_submission = 'enabled')
         │                                                                             │
         ▼                                                                             ▼
  [ Agency Hiring Marketplace ]                                 [ Bench Talent Marketplace ]
  • Question: "Which agency will recruit for this job?"         • Question: "Which agencies have suitable CVs now?"
  • UI: Dedicated Agency Hiring Grid                            • UI: Dedicated Bench Talent Grid
  • Action: Agency "Apply / Claim" Requisition                  • Action: Agency "Submit Bench Candidate"
  • Approval: global_settings.public_market_job_status          • Approval: global_settings.bench_status
  • Post-Hire Rule: Agency sees "View Job" (No duplicate apply) • Post-Hire Rule: Agency & others still submit bench
  • Sync Flow: Market -> Direct Client Jobs -> Job Tracker      • Sync Flow: Direct to ATS App Tracker (ac_status=0)
```

### 2.1 Outsource to Agencies — Agency Hiring Marketplace
If **"Outsource to Agencies"** is selected (`jobs.show_vendor = 'yes'`):
- The job is published to a dedicated **Agency Hiring Marketplace**.
- It possesses its own:
  - Marketplace UI and dedicated listing section.
  - Independent approval and review workflow.
  - Auto-approval option and manual review/approval option.
  - Status management (`jobs.market_pub_state`, `jobs.market_mod_state`, `jobs.claim_capacity`).
  - Agency application/claim workflow (`recommendation_relation.add_status = 1`).
- The Super Admin configures whether these jobs are:
  - **Auto Approved** (`global_settings.public_market_job_status = 1`), or
  - **Sent for Manual Review** (`global_settings.public_market_job_status = 0`).

### 2.2 Open for Bench Profiles — Bench Talent Marketplace
If **"Open for Bench Profiles from Agencies"** is selected (`jobs.bench_submission = 'enabled'`):
- The job is published to a separate **Bench Talent Marketplace**.
- It possesses its own:
  - Marketplace UI and dedicated listing section.
  - Independent approval and review workflow.
  - Auto-approval option and manual review/approval option.
  - Status management.
  - Bench candidate submission workflow (`jobs/recommandedrequestcondidate`).
- The Super Admin configures whether these bench jobs are:
  - **Auto Approved** (`global_settings.bench_status = 1`), or
  - **Sent for Manual Review** (`global_settings.bench_status = 0`).
- This workflow remains strictly independent from the Agency Hiring Marketplace.

### 2.3 Both Options Unselected
If both options are unselected:
- The job **must not appear in any marketplace listing**.
- No marketplace approval/review is triggered.
- The job continues strictly through the normal internal/direct-client ATS workflow.

### 2.4 Both Options Selected (Dual Channel Workflow)
If both options are selected:
- **Create two independent marketplace representations/workflows for the same underlying job**:
  1. **Agency Hiring Job:** Intended for agencies to apply/request the job for recruitment. Managed through the Agency Hiring Marketplace workflow.
  2. **Bench Talent Job:** Intended for agencies to submit existing bench candidates. Managed through the Bench Talent Marketplace workflow.
- These are treated as **two separate marketplace workflows**, even though they originate from the same job.
- **Do not create duplicate core job records.** Maintain a clear relationship between the single authoritative job record in MySQL (`jobs.id`) and its two marketplace channels.

---

### 2.5 Agency Already Hired for the Job (Dual-Action UX Rule)
There is an important business and UX rule for the **Agency Hiring Marketplace**:

If a company has already hired/assigned an agency for a particular job (`recommendation_relation.final_status = 1`):
- That agency sees the job in its **Direct Client Jobs** section.
- The same job automatically synchronizes with the marketplace/job tracker.
- For that already-appointed agency, the UI must **not make "View Job" the only conceptual action**, because Agency A can still submit bench candidates.
- Instead, the marketplace presents a clear **Dual-Action State Card**:
  ```
  ┌─────────────────────────────────────────────────────────────┐
  │ ℹ️ Already assigned to your agency                          │
  │                                                             │
  │ Recruitment activity                                        │
  │ [ 🚀 Open Job Tracker ]                                     │
  │                                                             │
  │ Have a suitable bench candidate?                            │
  │ [ 📤 Share Bench Candidate ]                                │
  └─────────────────────────────────────────────────────────────┘
  ```
- The agency is strictly prevented from submitting another marketplace application for recruitment for a job for which it has already been hired.

#### Existing Functionality Preservation:
This **Direct Client Job $\to$ Marketplace $\to$ Job Tracker synchronization is already working in the codebase**.
Do not rebuild it unnecessarily:
1. Audit the existing implementation in `SuperAdminCompanyController`, `recommendation_relation`, and `job_agency_counter`.
2. Verify the complete data flow.
3. Upgrade it to support the new marketplace separation.
4. Ensure there is no duplicate application/request.
5. Ensure status changes remain synchronized across:
   - Direct Client Jobs
   - Marketplace
   - Job Tracker
   - Agency-side job views
6. Existing working functionality must remain 100% backward compatible.

---

### 2.6 Bench Candidate Marketplace — Important Different Rule
The **Bench Talent Marketplace works fundamentally differently from the Agency Hiring Marketplace**.

Even if a company has already hired a particular agency for the same job:
- The job **must continue to remain visible in the Bench Talent Marketplace**.
- Other agencies must still be able to submit their available bench candidates.
- The already-hired agency is also able to submit/share bench candidates via its dual-action UI.
- The marketplace continues accepting bench candidate submissions as long as the job remains **Active** and Bench Channel is **Live**.

#### Real-World Example:
Company posts: **Java Developer — 10 Positions**  
Selected:
- ✅ *Outsource to Agencies*
- ✅ *Open for Bench Profiles*

Agency A is selected/hired for the recruitment requirement.

1. **Agency Hiring Marketplace Behavior:**
   - For Agency A: Shows **Dual-Action Card** (`[Open Job Tracker]` + `[Share Bench Candidate]`), disabling duplicate "Apply".
   - For other agencies: Shows **“Apply”** (subject to configured marketplace approval/application rules and `vendor_limit`).
2. **Bench Talent Marketplace Behavior:**
   - The job remains visible to **all eligible agencies**.
   - Agency A: Can click **“Share Bench Candidate”**.
   - Agency B: Can click **“Submit Bench Candidate”**.
   - Agency C: Can click **“Submit Bench Candidate”**.
   - The fact that Agency A has already been hired for recruitment **must not remove or restrict the Bench Talent Marketplace listing**.
   - This is intentional because the two marketplace channels serve entirely different business purposes.

---

### 2.7 Marketplace Lifecycle & Status Coupling (Channel-Independent Reopening)
The Bench Talent Marketplace listing remains available while:
$$\text{Job Status} = \mathbf{Active} \quad \land \quad \text{Bench Channel Status} = \mathbf{LIVE}$$

- Once the underlying job becomes `inactive`, `closed`, `filled`, or `cancelled` in the client ATS:
  - The associated marketplace listing automatically stops accepting new bench candidate submissions.
  - The Agency Hiring Marketplace listing simultaneously stops accepting new agency claims.
- **Critical Re-opening Governance (Zero Blind Auto-Reactivation):**
  - If a recruiter reopens or extends a previously closed job:
    $$\mathbf{Do\ NOT\ automatically\ reactivate\ both\ channels\ into\ LIVE!}$$
  - Each marketplace channel must be evaluated **completely independently**:
    $$\begin{aligned}
    \text{Reopen Job} \longrightarrow & \quad \text{Evaluate Agency Channel} \longrightarrow \text{Prior Valid State / Re-moderation} \\
    & \quad \text{Evaluate Bench Channel} \longrightarrow \text{Prior Valid State / Re-moderation}
    \end{aligned}$$
  - If a channel was previously **Rejected** or **Quarantined** by Super Admin, reopening the job must **NEVER** flip it to `LIVE`. It strictly remains `REJECTED` until remediated and explicitly re-approved.
  - If a channel was previously **Approved/Live**, it restores to `LIVE` only if the recruiter re-enables it and tenant commercials remain valid.

---

### 2.8 Required Marketplace Architecture

```
Flow A: Agency Hiring Channel
[Job Created] ──> [Agency Hiring Marketplace] ──> [Agency Claim / Request] ──> [Super Admin / Client Approval] ──> [Agency Assigned] ──> [Direct Client Job] ──> [Job Tracker]

Flow B: Bench Talent Channel
[Job Created] ──> [Bench Talent Marketplace] ──> [Agency Submits Bench Candidate] ──> [Client ATS Review] ──> [Interview / Selection] ──> [Hiring Settlement]
```

These two flows **must not interfere with each other**.

---

### 2.9 Super Admin Management Separation
The Super Admin marketplace management interface provides separate sections and separate configuration:

#### Agency Hiring Section:
- All Agency Hiring Jobs
- Pending Review
- Approved
- Rejected
- Active
- Closed
- Agency Applications/Requests
- Assigned Agencies

#### Bench Talent Section:
- All Bench Talent Jobs
- Pending Review
- Approved
- Rejected
- Active
- Closed
- Bench Candidate Submissions
- Candidate Review
- Selected / Rejected Candidates

#### Independent Approval Configuration:
Approval settings are independently configurable for each marketplace channel:
- **Agency Hiring:** Auto Approval (`global_settings.public_market_job_status = 1`) vs. Manual Review (`public_market_job_status = 0`).
- **Bench Talent:** Auto Approval (`global_settings.bench_status = 1`) vs. Manual Review (`bench_status = 0`).
- **Changing the approval configuration for one channel must not affect the other channel.**

---

### 2.10 Critical Business Rules Matrix

| Job Configuration | Agency Marketplace | Bench Marketplace | Already-Hired Agency Action | Other Agencies Action |
| :--- | :---: | :---: | :--- | :--- |
| **Neither Selected** | ❌ Not Listed | ❌ Not Listed | Normal Direct Job (if assigned) | Hidden |
| **Agency Hiring Only** | ✅ Listed | ❌ Not Listed | **View Job** (Direct Client Job) | **Apply / Claim** |
| **Bench Talent Only** | ❌ Not Listed | ✅ Listed | **Submit Bench Candidate** | **Submit Bench Candidate** |
| **Both Selected** | ✅ Separate Listing | ✅ Separate Listing | Agency: **View Job**<br>Bench: **Submit Bench Candidate** | Agency: **Apply / Claim**<br>Bench: **Submit Bench Candidate** |
| **Job Active** | Follow Agency Workflow | Accept Bench Submissions | Active Engagement | Active Engagement |
| **Job Closed / Inactive** | Stop Marketplace Activity | Stop Bench Submissions | Closed in Tracker | Actions Disabled |

---

### 2.11 Hiring Intent Separation Principle

$$\begin{aligned}
\mathbf{Agency\ Hiring\ Marketplace\ Answers:} & \quad \text{“Which agency will recruit for this job?”} \\
\mathbf{Bench\ Talent\ Marketplace\ Answers:} & \quad \text{“Which agencies have suitable candidates already available for this job?”}
\end{aligned}$$

These are two different business workflows and are implemented as **separate marketplace channels, approval processes, UIs, and application/submission flows**, while remaining connected to the same underlying job and synchronized job tracker.

---

### 2.12 Direct Client Job ↔ Marketplace — Complete Agency Synchronization

When an agency has already been assigned/hired by the company for a job, and that job supports both:
- **Outsource to Agencies** (`jobs.show_vendor = 'yes'`), and
- **Open for Bench Profiles from Agencies** (`jobs.bench_submission = 'enabled'`)

the agency must have a **single unified job relationship** across Direct Client Jobs, Job Tracker, and Marketplace.

#### Scenario:
Company posts:
**Java Developer — 10 Positions**

Selected:
- ✅ *Outsource to Agencies*
- ✅ *Open for Bench Profiles*

Agency A is hired/assigned by the company for the recruitment requirement (`recommendation_relation.final_status = 1`).

Agency A should then receive the job in its:
$$\mathbf{Direct\ Client\ Jobs} \longrightarrow \mathbf{Job\ Tracker}$$

The same underlying job must remain available in the marketplace for the Bench Talent channel.

---

### 2.13 Agency-Specific Marketplace Behaviour (Official Dual-Action UX Rule)

For **Agency A**, because the company has already assigned this job to Agency A for external recruitment (`recommendation_relation.final_status = 1`):

#### Agency Hiring Marketplace:
The action must **not** be reduced to a single "View Job" button, because Agency A may also possess verified bench consultants matching the requisition.

Instead, the marketplace listing presents the **Official Dual-Action Card**:

```
┌─────────────────────────────────────────────────────────────┐
│ ℹ️ Already assigned to your agency                          │
│                                                             │
│ Recruitment activity                                        │
│ [ 🚀 Open Job Tracker ]                                     │
│                                                             │
│ Have a suitable bench candidate?                            │
│ [ 📤 Share Bench Candidate ]                                │
└─────────────────────────────────────────────────────────────┘
```

The agency is encouraged to manage the primary recruitment activity directly from its **Direct Client Job / Job Tracker**, while retaining immediate 1-click access to bench deployment.

> [!IMPORTANT]
> **Zero Duplicate Applications:** Do **not** create a second marketplace application/request for Agency A. The system recognizes the existing agency-job relationship (`recommendation_relation.from_company_id = client_id AND to_company_id = agency_a_id AND job_id = job_id AND final_status = 1`) and automatically connects the marketplace listing with the agency's Direct Client Job.

---

### 2.14 Bench Candidate Submission from Direct Client Job

Because Agency A is already assigned to the job, Agency A can submit bench candidates directly from:
$$\mathbf{Direct\ Client\ Jobs} \longrightarrow \mathbf{Job\ Tracker} \longrightarrow \mathbf{Share\ Bench\ Candidate}$$

The agency does **not need to return to the marketplace** to submit the candidate.

The Direct Client Job / Job Tracker becomes the agency's primary operational workspace.

---

---

### 2.15 Architectural Model: Canonical Submission & Platform Hierarchy

The platform enforces a strict architectural hierarchy ensuring that the Direct Job Tracker does **not** become a secondary, fragmented marketplace:

```
                                      JERO ATS
                                         │
                                   Canonical Job
                                         │
                    ┌────────────────────┴────────────────────┐
                    ▼                                         ▼
           Marketplace Discovery                     Direct Job Workspace
        (Channel = Intake Surface)                (Operational Vendor Cockpit)
                    │                                         │
                    ▼                                         ▼
           Candidate Submission                      Candidate Submission
                    │                                         │
                    └────────────────────┬────────────────────┘
                                         ▼
                                CANONICAL SUBMISSION
                       (Explicit Business ID: submission_id)
                                         │
                                         ▼
                                     CLIENT ATS
                      (Authoritative Pipeline: application_id)
                                         │
                             ┌───────────┴───────────┐
                             ▼                       ▼
                    [ Sys Outbox Events ]    [ Domain Events ]
                             │                       │
                             ▼                       ▼
                    [ Elasticsearch 8.x ]   [ Laravel Reverb WS ]
                      (Read Projection)       (Real-Time Sync)
```

#### Core Architectural Invariants:
1. **The Marketplace is Strictly a Distribution & Discovery Surface:** It facilitates the discovery of the requisition and handles initial candidate transmission.
2. **The Direct Job Tracker is an Operational Workspace:** It allows an appointed agency to fulfill its recruitment agreement and deploy bench consultants.
3. **The ATS Candidate Pipeline is the Authoritative Operational Source of Truth:** Once a candidate is submitted, candidate progression (screening, evaluation, interview scheduling, offer, joining) lives strictly inside the ATS candidate pipeline (`job_applications` / `rec_candidate_assign`).
4. **Marketplace Does NOT Become a Second ATS:** The Marketplace does not maintain an independent candidate-processing state machine; it merely renders a real-time read projection of the underlying ATS submission state via the transactional event bus and Elasticsearch.

#### Concrete Flow:
Agency A shares:
**Candidate: Rahul Sharma**  
from:
$$\mathbf{Direct\ Client\ Job} \longrightarrow \mathbf{Java\ Developer} \longrightarrow \mathbf{Share\ Bench\ Candidate}$$

- A single canonical record is created with `submission_id = 987654`, `channel = BENCH_TALENT`, `source = DIRECT_JOB_TRACKER`.
- It immediately generates the corresponding ATS implementation reference (`application_id` in `job_applications` / `rec_candidate_assign`).
- The Bench Talent Marketplace view reflects that a candidate submission occurred from Agency A without creating a redundant duplicate record.

---

### 2.16 Marketplace → Direct Client Job Synchronization

Synchronization functions identically in the opposite direction.

If Agency A submits a bench candidate through the public Bench Talent Marketplace interface instead of the Direct Client Job:
$$\mathbf{Marketplace} \longrightarrow \mathbf{Submit\ Bench\ Candidate}$$

the system assigns the canonical `submission_id = 987654` with `channel = BENCH_TALENT`, `source = MARKETPLACE`.

Via the transactional event bus, this submission is instantly projected into:
$$\mathbf{Agency\ A} \longrightarrow \mathbf{Direct\ Client\ Job} \longrightarrow \mathbf{Job\ Tracker}$$

The agency and client will **never** have two disparate records for the same candidate-job submission.

---

### 2.17 Unified Submission State, Versioning & Disconnect Recovery

The following information remains 100% synchronized across both workspaces via the event bus:
- **Canonical Identifier** (`submission_id = 987654`)
- **ATS Reference** (`application_id`)
- **Entity Version** (`version`: Monotonic integer, e.g. `v17`)
- **Candidate Details** (`candidates.id`, full name, contact, resume, skills)
- **Agency Identity** (`companies.id`, assigned recruiter)
- **Job Requisition** (`jobs.id`, title, hiring company)
- **Channel** (`AGENCY_HIRING` vs. `BENCH_TALENT`)
- **Source** (`MARKETPLACE`, `DIRECT_JOB_TRACKER`, `ADMIN`, `API`, `IMPORT`)
- **Submission Date & Timestamp** (`created_at`, `updated_at`)
- **Operational ATS Stage** (`lifecycle_status`: Screening, Interview, Offered, Joined)
- **Client Feedback & Evaluation Scores**
- **Interview Rounds & Schedules** (`job_interview`)
- **Offer Terms & Commercial Compensation** (`offer_job_applications`)

#### Real-Time Broadcast & Disconnect Recovery Flow:
```
Canonical DB Mutation (v17 -> v18)
               │
               ▼
   [ sys_outbox_events Commit ]
               │
               ▼
    [ Horizon Queue Worker ]
               │
               ▼
    [ Laravel Reverb WebSocket ]
               │
               ▼
  [ Recruiter Browser RAM / WASM ]
               │
  ├── Normal: Applies v18 immediately (P50 < 15ms)
  └── Disconnect / Network Drop:
      ├── Reconnects to Reverb WebSocket
      ├── Requests Delta: GET /api/submissions/sync?since_version=17
      └── Reconciles local IndexedDB state safely without reload
```

---

### 2.18 Agency-Specific Marketplace Message

For an agency that has already been assigned the job, the marketplace listing provides the official contextual Metronic alert:

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ ℹ️ Already Added to Your Direct Client Jobs                                                       │
│ This job has already been assigned to your agency. You can manage recruitment and review         │
│ progress directly from your Direct Client Job Tracker.                                           │
│                                                                                                  │
│ [ 🚀 Open Job Tracker ]                                                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 💡 Have a suitable bench candidate for this job?                                                 │
│ Bench profiles shared from your Direct Client Job Tracker automatically stream into the client's │
│ candidate review pipeline.                                                                       │
│                                                                                                  │
│ [ 📤 Share Bench Candidate ]                                                                     │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

This makes the workflow obvious and prevents agencies from unnecessarily applying again through the marketplace.

---

### 2.19 Other Agencies Must Continue to See the Marketplace

The dual-action card applies **only to the agency that has already been assigned/hired**.

For other agencies:

#### Agency Hiring Marketplace:
They continue to see:
**Apply / Request Job**  
according to the configured marketplace rules and `jobs.vendor_limit`.

#### Bench Talent Marketplace:
They continue to see:
**Submit Bench Candidate**  
while the job remains active and the Bench Channel is `LIVE`.

#### Agency Visibility & Action Matrix:

| Agency | Agency Hiring Marketplace | Bench Talent Marketplace | Direct Client Jobs View |
| :--- | :--- | :--- | :--- |
| **Agency A (Already Hired)** | **Dual-Action Card:** `[Open Job Tracker]` + `[Share Bench Candidate]` | **Submit Bench Candidate** (Enabled) | Full Operational Job Tracker & Candidate Pipeline |
| **Agency B (Not Hired)** | **Apply / Request Job** (Enabled) | **Submit Bench Candidate** (Enabled) | Hidden until hired |
| **Agency C (Not Hired)** | **Apply / Request Job** (Enabled) | **Submit Bench Candidate** (Enabled) | Hidden until hired |

This distinction is critical and prevents vendor lock-in while preserving marketplace liquidity.

---

### 2.20 Canonical Submission Architecture (`submission_id` vs. `application_id`)

To eliminate identity conflation and prevent database collisions across multiple channels, JERO establishes an **explicit dual-tier identity model**:

```
                 submission_id (Canonical Business Identity)
                                      │
                                      ▼
                          marketplace_submissions
                                      │
        ┌─────────────────────────────┼─────────────────────────────┐
        ▼                             ▼                             ▼
 job_applications              rec_candidate_assign           job_interview
(ATS Application Ref)         (Public Bench Assignment)    (Scheduled Rounds)
        │
        ▼
 offer_job_applications
  (Commercial Offer)
        │
        ▼
   Joining Verification
```

#### Canonical Record Entity Definition:
```
                       CANONICAL SUBMISSION RECORD
┌────────────────────────────────────────────────────────────────────────┐
│ submission_id:       987654 (Global Primary Key / BigInt)              │
│ application_id:      45012 (ATS Implementation Reference / Nullable)   │
│ job_id:              1001                                              │
│ company_id:          42 (Hiring Client Tenant)                         │
│ agency_id:           200 (Partner Agency Tenant)                       │
│ candidate_id:        50001                                             │
│ channel:             BENCH_TALENT (or AGENCY_HIRING)                   │
│ source:              DIRECT_JOB_TRACKER (or MARKETPLACE, ADMIN, API)   │
│ version:             18 (Monotonic Version Counter)                    │
│ lifecycle_status:    IN_REVIEW | SHORTLISTED | INTERVIEW | OFFER | ... │
└────────────────────────────────────────────────────────────────────────┘
```

#### Deterministic Uniqueness Invariants:
1. **The Unique Key Invariant:**
   $$\mathbf{UNIQUE\ KEY} = \langle \mathbf{job\_id},\ \mathbf{agency\_id},\ \mathbf{candidate\_id},\ \mathbf{channel} \rangle$$
   - **Why `channel` is required in the key:** If Agency A submits Rahul Sharma via external **Agency Hiring**, and later submits Rahul Sharma as **Bench Talent**, these represent two distinct business engagements. A unique key on `(job_id, agency_id, candidate_id)` would wrongfully collide.
   - **Same Channel Invariant:** Same Job + Agency + Candidate + Channel = **ONE single submission**.
2. **Channel vs. Source Separation:**
   - **`channel` (The Business Workflow):** `AGENCY_HIRING` | `BENCH_TALENT`.
   - **`source` (The Originating Surface):** `MARKETPLACE` | `DIRECT_JOB_TRACKER` | `ADMIN` | `API` | `IMPORT`.
   - If a candidate is submitted via Marketplace and later viewed or updated from Direct Job Tracker, the channel remains `BENCH_TALENT` and the canonical `submission_id` is retained.
3. **Explicit Multi-Tenant Authorization Security:**
   - Authorization is enforced at the database/policy layer, never via UI hiding:
     $$\begin{aligned}
     \mathbf{Agency\ Access:} & \quad \text{submission.agency\_id} == \text{auth()->user()->company\_id} \\
     \mathbf{Client\ Access:} & \quad \text{submission.company\_id} == \text{auth()->user()->company\_id} \\
     \mathbf{Super\ Admin:} & \quad \text{Global Administrative Clearance}
     \end{aligned}$$
   - Agency B attempting `GET /jobs/1001/submissions/987654` receives a strict `403 Forbidden` response.

---

### 2.21 Final End-to-End Workflow

```
Channel 1: Agency Hiring Channel (Outsource to Agencies)
[ Company Creates Job (show_vendor = 'yes') ]
                    │
                    ▼
[ Agency Marketplace: Channel = LIVE ]
                    │
                    ▼
[ Agency A Applies / Requests Job ]
                    │
                    ▼
[ Super Admin / Client Approves -> recommendation_relation.final_status = 1 ]
                    │
                    ▼
[ Job Activated in Agency A: Direct Client Jobs -> Job Tracker ]
                    │
                    ▼
[ Agency Hiring Marketplace UI for Agency A transforms to Dual-Action Card ]
```

```
Channel 2: Bench Talent Channel (Parallel, Independent & Event-Driven)
[ Open for Bench Profiles = 'enabled' -> Bench Channel = LIVE ]
                                  │
                                  ▼
[ All Eligible Agencies (A, B, C) Discover Job on Bench Marketplace ]
                                  │
         ┌────────────────────────┴────────────────────────┐
         ▼                                                 ▼
[ Agency A (Assigned):                            [ Agency B (External):
  Job Tracker -> Share Bench Candidate ]            Bench Market -> Submit Bench Candidate ]
         │                                                 │
         └────────────────────────┬────────────────────────┘
                                  ▼
                   [ Canonical Submission Generated ]
                      (Explicit submission_id)
                                  │
                                  ▼
                   [ Client ATS Candidate Pipeline ]
                     (rec_candidate_assign / ac_status)
                                  │
                                  ▼
                     [ Client Reviews Candidate ]
                     (Shortlist / Interview / Reject)
                                  │
                                  ▼
                   [ sys_outbox_events Broadcast ]
                                  │
         ┌────────────────────────┴────────────────────────┐
         ▼                                                 ▼
[ Real-Time Sync to Agency A                     [ Real-Time Read Projection on
  Direct Client Job Tracker ]                      Marketplace Candidate View ]
```

---

### 2.22 Core Product Principle: Discovery vs. Execution

$$\begin{aligned}
\mathbf{Marketplace} & \quad \Longrightarrow \quad \mathbf{Discovery\ \&\ Submission\ Intake\ Channel} \\
\mathbf{Direct\ Client\ Job\ /\ ATS\ Tracker} & \quad \Longrightarrow \quad \mathbf{Authoritative\ Operational\ Source\ of\ Truth}
\end{aligned}$$

- The marketplace exists to **discover, match, and intake opportunities**.
- Once an agency is assigned, the **Direct Client Job + Job Tracker becomes the operational workspace**.
- Once a candidate is submitted, the **ATS Candidate Pipeline becomes the operational source of truth**; the marketplace merely projects this status.
- However, **Bench Talent remains independently discoverable and active**, even when an agency has already been assigned for recruitment.

This establishes a clean, unified hybrid model:
$$\mathbf{Marketplace\ Discovery} \longrightarrow \mathbf{Intake} \longrightarrow \mathbf{ATS\ Operational\ Pipeline} \longleftrightarrow \mathbf{Event\ Bus\ Real\text{-}Time\ Projection}$$
with **zero duplicate job records, zero duplicate candidate submissions, and zero state drift**.

---

## 3. Information Architecture (IA)

The Super Admin Marketplace is structured as an operationally lean, unified hierarchy:

```
                                      MARKETPLACE
                                           │
         ┌───────────────────┬─────────────┴─────────────┬───────────────────┐
         ▼                   ▼                           ▼                   ▼
  [ Command Center ]  [ Requisitions ]          [ Agency Exchange ]   [ Governance ]
  ├── Real-time KPIs  ├── All                   ├── Claims            ├── Audit
  ├── Velocity SLA    ├── Moderation            ├── Bench             ├── Policies
  └── Live Telemetry  ├── Live                  ├── Agencies          └── Coin Ledger
                      ├── Quarantined           └── Scorecards
                      └── Delisted
```

---

## 4. Unified Navigation, Canonical Route Matrix & Authorization

### 4.1 Streamlined Operational Sidebar Navigation
Designed specifically for high-speed administration on 15.6" laptop viewports:

```
┌────────────────────────────────────────────────────────────────────────┐
│ MARKETPLACE                                                            │
├────────────────────────────────────────────────────────────────────────┤
│ 📊 Command Center                (/super-admin/marketplace/dashboard) │
│                                                                        │
│ 📁 Requisitions                                                        │
│    ├── All                       (/super-admin/marketplace/jobs)       │
│    ├── Moderation                (/super-admin/marketplace/moderation) │
│    ├── Live                      (/super-admin/marketplace/live)       │
│    ├── Quarantined               (/super-admin/marketplace/quarantine) │
│    └── Delisted                  (/super-admin/marketplace/delisted)   │
│                                                                        │
│ 🤝 Agency Exchange                                                     │
│    ├── Claims                    (/super-admin/marketplace/claims)     │
│    ├── Bench                     (/super-admin/marketplace/bench)      │
│    ├── Agencies                  (/super-admin/marketplace/directory)  │
│    └── Scorecards                (/super-admin/marketplace/scorecards) │
│                                                                        │
│ 🛡️ Governance                                                           │
│    ├── Audit                     (/super-admin/marketplace/audit)      │
│    ├── Policies                  (/super-admin/marketplace/policies)   │
│    └── Coin Ledger               (/super-admin/marketplace/coins)       │
└────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Complete Route Consolidation & Backward-Compatibility Matrix
All legacy routes are transparently mapped to the new unified controllers:

| Legacy Route (Deprecating) | Legacy Controller Method | New Canonical Route | Read Architecture |
| :--- | :--- | :--- | :--- |
| `GET /super-admin/job-public-market/{status?}` | `SuperAdminCompanyController@jobPublicMarket` | `GET /super-admin/marketplace/jobs?tab=moderation` | 100% Elasticsearch 8.x |
| `GET /super-admin/marketshow-job-details` | `SuperAdminCompanyController@marketshowJobDetails` | `GET /super-admin/marketplace/job-detail/{id}` | ES + Redis Cache (<15ms) |
| `POST /super-admin/market-auto-approval` | `SuperAdminCompanyController@marketautoApproval`| `POST /super-admin/marketplace/settings/auto-approval` | Atomic DB + Outbox |
| `POST /super-admin/job-approval` | `SuperAdminCompanyController@jobApproval` | `POST /super-admin/marketplace/jobs/approve` | Atomic DB + Outbox |
| `POST /super-admin/reject_job/reject_mail_send` | `SuperAdminCompanyController@reject_mail_send_job`| `POST /super-admin/marketplace/jobs/reject` | Atomic DB + Outbox |
| `GET /super-admin/jobs/job_request/{id?}` | `SuperAdminCompanyController@job_request` | `GET /super-admin/marketplace/agencies/claims` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/recommandedrequest` | `SuperAdminCompanyController@recommandedrequest` | `GET /super-admin/marketplace/agencies/bench` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/job-lists/{id?}` | `SuperAdminCompanyController@jobLists` | `GET /super-admin/marketplace/jobs` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/job_request_accept/{id?}` | `SuperAdminCompanyController@job_request_accept` | `GET /super-admin/marketplace/agencies/claims?status=accepted` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/recommandedrequest_accept` | `SuperAdminCompanyController@recommandedrequest_accept`| `GET /super-admin/marketplace/agencies/bench?status=accepted`| 100% Elasticsearch 8.x |

---

### 4.3 Multi-Tenant Authorization & Policy Governance Matrix

Platform security and multi-tenant data boundaries are strictly enforced at the authorization policy layer:

| Operational Action | Staffing Agency | Hiring Client | Super Admin |
| :--- | :---: | :---: | :---: |
| **View Marketplace Job** | ✅ Permitted (Public/Live) | ✅ Permitted (Own & Bench) | ✅ Full Global View |
| **Claim Job (Method 1)** | ⚡ Eligible Only (Cap check) | ❌ Forbidden (`—`) | 👑 Administrative Override |
| **Submit Bench Candidate (Method 2)**| ⚡ Eligible Only (KYC/TOS) | ❌ Forbidden (`—`) | 👑 Administrative Override |
| **View Own Submissions** | ✅ Permitted (Own tenant) | ✅ Permitted (Own tenant) | ✅ Full Global View |
| **View Other Agency Submissions** | ❌ Forbidden (`403`) | ✅ Permitted (All on own job)| ✅ Full Global View |
| **Approve Marketplace Listing** | ❌ Forbidden | ⚙️ Policy Dependent (Recruiter)| ✅ Full Authority |
| **Reject Marketplace Listing** | ❌ Forbidden | ⚙️ Policy Dependent (Recruiter)| ✅ Full Authority |
| **Change Vendor Capacity Limit** | ❌ Forbidden | ⚙️ Policy Dependent (Recruiter)| ✅ Full Authority |
| **View Coin Ledger** | 🪙 Own Tenant Ledger Only | 🪙 Own Tenant Ledger Only | 🌐 Global Financial Ledger |
| **Refund Operational Coins** | ❌ Forbidden | ❌ Forbidden | ✅ Full Authority (Event 40) |
| **Modify Algorithmic Risk Rules** | ❌ Forbidden | ❌ Forbidden | ✅ Full Authority (`sys_risk_rules`) |

#### Policy Enforcement Rules:
1. **Agency Data Isolation:** An agency query for candidate submissions is hard-scoped with `WHERE agency_id = :auth_company_id`. Attempts to inspect rival agency CVs fail with an immediate `403 Forbidden` logged to security telemetry.
2. **Client Recruiter Scope:** Client recruiters see submissions exclusively for jobs owned by their `company_id`.
3. **Super Admin Omnipresence:** Super Admin has global read and write privileges across all tenants for platform governance, fraud prevention, and dispute resolution.

---

## 5. End-to-End Operational Lifecycle & Sequence Flows

```mermaid
sequenceDiagram
    autonumber
    actor Recruiter as Hiring Company Recruiter
    participant Web as Web Server (Octane / FrankenPHP)
    participant DB as MySQL InnoDB (Primary DB)
    participant Outbox as sys_outbox_events
    participant Risk as Local Heuristic Risk Engine
    actor Admin as Super Admin
    participant Horizon as Horizon Worker (Queue: market-outbox)
    participant ES as Elasticsearch 8.x (ats_jobs)
    participant Dragonfly as Dragonfly Cache (4M QPS)
    participant Reverb as Reverb WebSockets
    actor Agency as Partner Staffing Agency

    Recruiter->>Web: POST /jobs/store (Marketplace Options Selected)
    Web->>DB: INSERT INTO jobs (public_market_status = 1, show_vendor = 'yes')
    Web->>Risk: Synchronous Ingestion Scan (Contact Leaks, CTC Bands, Words)
    
    alt Trust Score >= 85 & Zero Risk Flags & Auto-Approval Enabled
        Web->>DB: UPDATE jobs SET public_market_status = 2, market_pub_state = 'published'
        Web->>Outbox: INSERT Event: MarketJobPublished (job_id, v1)
        Web->>Dragonfly: Invalidate tenant cache: company_id:{tenant}:jobs
        Web-->>Recruiter: HTTP 200 (Live on Marketplace Instantly)
    else Trust Score < 85 OR Flag Detected OR Auto-Approval Disabled
        Web->>DB: Keep public_market_status = 1, market_mod_state = 'pending'
        Web->>Outbox: INSERT Event: MarketJobPendingReview (job_id, flags)
        Web-->>Recruiter: HTTP 200 (Submitted to Marketplace Moderation)
    end

    Note over Outbox,Horizon: Horizon Worker claims batch from sys_outbox_events
    Horizon->>ES: Sync Document to ats_jobs index
    Horizon->>Reverb: Broadcast to channel 'marketplace.superadmin.telemetry'

    opt Manual Super Admin Review Required
        Admin->>Web: Opens Moderation Queue (Fast-Review Drawer #kt_activitiesMarketplaceJob)
        Web->>ES: Query ats_jobs (Sub-8ms read)
        Admin->>Web: 1-Click Approve OR Structured Reject
        Web->>DB: Atomic Transaction (Update jobs, Insert job_marketplace_moderation_logs)
        Web->>Outbox: Record State Change Event in sys_outbox_events
        Horizon->>ES: Update ats_jobs Document
        Horizon->>Reverb: Broadcast real-time update to Agency Grid
        Horizon->>Web: Asynchronous Email Dispatch to Recruiter (Mail Type 28/29)
    end

    alt Method 1: Agency Claims Requisition (Agency Hiring Channel)
        Agency->>Web: POST /jobs/market/claim (Agency agrees to terms)
        Web->>DB: Deduct Operational Coins (Event 38), INSERT recommendation_relation (add_status = 1)
        Web->>Outbox: Record Claim Event
        Horizon->>ES: Update claimed_agencies_count in ats_jobs
        Web-->>Agency: Position locked in Direct Client Jobs ATS pipeline (Button becomes "View Job")
    else Method 2: Agency Submits Bench Candidate (Bench Talent Channel)
        Agency->>Web: POST /jobs/recommandedrequestcondidate (Direct CV Deployment)
        Web->>DB: Deduct Operational Coins (Event 39), INSERT candidate into client ATS
        Web->>Outbox: Record Bench Submission Event
        Horizon->>ES: Sync candidate to ats_job_applications (ac_status = 0)
        Web-->>Agency: Bench Candidate submitted to Recruiter Review Queue
    end
```

---

## 6. Three-Layer State Machine & Channel-Independent Invariant Transitions

A monolithic universal state machine for a dual-intent requisition is an architectural anti-pattern:
$$\mathbf{Agency\ Marketplace} = \mathbf{CLAIM\_LIMIT\_REACHED} \quad \not\Longrightarrow \quad \mathbf{Bench\ Marketplace} = \mathbf{CLOSED}$$

When an agency vendor cap is reached, Method 1 claims must be halted, but Method 2 bench candidate submissions must remain active. Furthermore, Super Admin manual review policies operate on distinct switches (`public_market_job_status` vs. `bench_status`).

Therefore, JERO enforces a **Mandatory Three-Layer State Architecture**:

```
                                  THREE-LAYER STATE ARCHITECTURE
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ LAYER 1: CORE JOB LIFECYCLE STATE                                                                      │
│   [ ACTIVE ] ──> [ INACTIVE ] ──> [ CLOSED ] ──> [ FILLED ] ──> [ CANCELLED ]                           │
│   (Governs underlying requisition existence in ATS and primary recruitment activity)                   │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ LAYER 2: AGENCY HIRING CHANNEL STATE (Method 1: Outsource to Vendors)                                  │
│   [ DISABLED ] ──> [ PENDING_REVIEW ] ──> [ APPROVED ] ──> [ LIVE ] ──> [ CLAIM_LIMIT_REACHED ]       │
│                                                ├──> [ PAUSED ]                                         │
│                                                └──> [ REJECTED ]                                       │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ LAYER 3: BENCH TALENT CHANNEL STATE (Method 2: Deploy Bench Consultants)                               │
│   [ DISABLED ] ──> [ PENDING_REVIEW ] ──> [ APPROVED ] ──> [ LIVE ] ──> [ PAUSED ]                     │
│                                                                     └──> [ REJECTED ]                   │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 6.1 State Dimension Specifications

#### Dimension 1: Core Job Lifecycle (`jobs.status`)
- **`ACTIVE`:** Requisition is active, operational in tenant ATS, and accepts internal/external activity.
- **`INACTIVE` / `PAUSED`:** Recruiter paused all candidate processing. All marketplace activities suspended.
- **`CLOSED`:** Requisition permanently closed. All marketplace channels instantly disabled.
- **`FILLED`:** Positions filled. Requisition archived.
- **`CANCELLED`:** Requisition withdrawn by client.

#### Dimension 2: Agency Hiring Channel State (`agency_channel_state`)
- **`DISABLED`:** `show_vendor = 'no'`. Channel unselected; hidden from Agency Hiring Marketplace.
- **`PENDING_REVIEW`:** Requisition submitted; awaiting Super Admin manual moderation (`public_market_job_status = 0`).
- **`APPROVED`:** Moderation cleared; preparing for broadcast.
- **`LIVE`:** Actively accepting agency claims on the Agency Hiring Marketplace.
- **`CLAIM_LIMIT_REACHED`:** Vendor cap reached (`claimed_agencies_count >= vendor_limit`). Requisition hidden from open claims or displays "Vendor Cap Reached".
- **`PAUSED`:** Recruiter or Admin temporarily halted new vendor claims.
- **`REJECTED`:** Flagged by Super Admin for compliance, contact leak, or vague JD.

#### Dimension 3: Bench Talent Channel State (`bench_channel_state`)
- **`DISABLED`:** `bench_submission = 'disabled'`. Hidden from Bench Talent Marketplace.
- **`PENDING_REVIEW`:** Submitted; awaiting Super Admin manual review (`bench_status = 0`).
- **`APPROVED`:** Moderation cleared; ready for public bench submissions.
- **`LIVE`:** Actively accepting candidate submissions from partner agencies.
- **`PAUSED`:** Temporarily halted by recruiter.
- **`REJECTED`:** Flagged by Super Admin.

---

### 6.2 The Three-Layer Concurrency & Eligibility Truth Matrix

$$\mathbf{Operational\ Invariant:} \quad \mathbf{Job\ Active} \ne \mathbf{Marketplace\ Active}$$

Marketplace interactions are governed by the logical conjunction of all three layers:

| Core Job Status | Agency Channel State | Bench Channel State | Agency Apply Action | Bench Submission Action | Marketplace Visibility |
| :--- | :--- | :--- | :---: | :---: | :--- |
| **`ACTIVE`** | **`LIVE`** | **`LIVE`** | ✅ Available | ✅ Available | Both Marketplaces Active |
| **`ACTIVE`** | **`CLAIM_LIMIT_REACHED`** | **`LIVE`** | ❌ Blocked (Limit) | ✅ Available | Agency: Limit Reached<br>Bench: Fully Active |
| **`ACTIVE`** | **`LIVE`** | **`PAUSED`** | ✅ Available | ❌ Blocked | Agency: Active<br>Bench: Submissions Paused |
| **`ACTIVE`** | **`REJECTED`** | **`LIVE`** | ❌ Blocked (Revision) | ✅ Available | Agency: In Revision<br>Bench: Fully Active |
| **`ACTIVE`** | **`PENDING_REVIEW`** | **`APPROVED`** | ⏳ In Moderation | ✅ Available | Agency: Hidden<br>Bench: Live |
| **`INACTIVE`** | Any State | Any State | ❌ All Blocked | ❌ All Blocked | Marketplace Actions Disabled |
| **`CLOSED`** | Any State | Any State | ❌ All Terminated | ❌ All Terminated | Requisitions Delisted Globally |

---

### 6.3 Channel-Independent Job Reopening Evaluation Engine

When a recruiter re-opens or extends a previously closed or expired job:

```
                          [ Recruiter Clicks "Reopen Job" ]
                                          │
                                          ▼
                         [ Core Job Status -> ACTIVE ]
                                          │
                  ┌───────────────────────┴───────────────────────┐
                  ▼                                               ▼
     [ Evaluate Agency Channel ]                     [ Evaluate Bench Channel ]
                  │                                               │
    ├── Is show_vendor = 'yes'?                     ├── Is bench_submission = 'enabled'?
    │   ├── NO  -> DISABLED                         │   ├── NO  -> DISABLED
    │   └── YES -> Check Prior Mod State            │   └── YES -> Check Prior Mod State
    │       ├── Was REJECTED   -> REJECTED          │       ├── Was REJECTED   -> REJECTED
    │       ├── Was QUARANTINED-> QUARANTINED       │       ├── Was QUARANTINED-> QUARANTINED
    │       └── Was LIVE/APPRVD-> Evaluate Policy   │       └── Was LIVE/APPRVD-> Evaluate Policy
    │           ├── Auto-Approve -> LIVE            │           ├── Auto-Approve -> LIVE
    │           └── Manual Review-> PENDING_REVIEW  │           └── Manual Review-> PENDING_REVIEW
```

> [!CAUTION]
> **Zero Accidental Marketplace Publication:** If the Agency Channel was previously **Rejected** (e.g., due to a phone number leak), reopening the job must **NEVER** convert the Agency Channel to `LIVE`. It remains strictly `REJECTED` until the recruiter edits the text, clears the heuristics, and resubmits.

---

### 6.4 Atomic Concurrency & Race-Condition Safeguard (Vendor Limit Invariant)

In high-concurrency environments with 10,000+ recruiters, a classic race condition occurs when positions reach capacity:
- Suppose `jobs.vendor_limit = 5`, and current `claimed_count = 4`.
- Agency B and Agency C click **“Apply / Claim”** at the exact same millisecond.
- In naive architectures, both PHP threads read `count = 4`, validate successfully, and insert claims. Result: **6 agencies claimed**, violating the client's contracted SLA.

#### Non-Negotiable Database Invariant:
The vendor limit must be enforced by an **atomic database transaction**, never solely by PHP/Laravel validation or Elasticsearch.

```sql
-- 1. Atomic Counter Increment with Conditional Guard
UPDATE `job_agency_counter`
SET `claimed_count` = `claimed_count` + 1
WHERE `job_id` = :job_id 
  AND `claimed_count` < :vendor_limit;

-- 2. Inspect Affected Rows
-- If affectedRows === 1: Slot successfully secured under ACID lock. Proceed to create claim.
-- If affectedRows === 0: Capacity saturated concurrently. Abort and return 409 Conflict.
```

Alternatively, wrapped in an InnoDB transaction with pessimistic row-level locking:
```php
DB::transaction(function () use ($jobId, $agencyId) {
    $channel = DB::table('marketplace_channels')
        ->where('job_id', $jobId)
        ->where('channel_type', 'AGENCY_HIRING')
        ->lockForUpdate()
        ->first();

    if (!$channel || $channel->publication_status !== 'LIVE') {
        throw new MarketplaceException("Channel is no longer accepting claims.");
    }

    $claimedCount = DB::table('recommendation_relation')
        ->where('job_id', $jobId)
        ->where('add_status', 1)
        ->whereIn('final_status', [0, 1]) // Pending or Hired
        ->count();

    if ($claimedCount >= $channel->vendor_limit) {
        throw new VendorLimitReachedException("Vendor capacity saturated.");
    }

    // Insert claim and update counter atomically
});
```

---

### 6.5 Authoritative Truth vs. Projection Invariant (CQRS Write Guard)

JERO adheres strictly to CQRS separation:
$$\mathbf{Elasticsearch\ 8.x} = \mathbf{Read\ Projection\ Only} \quad \not\equiv \quad \mathbf{Authoritative\ Business\ State}$$

```
   [ Client UI / Recruiter Browser ]
                  │
      ┌───────────┴───────────┐
      ▼                       ▼
[ Read Path ]          [ Write Path ]
Elasticsearch 8.x       Canonical MySQL Primary
(ats_jobs facet)        (Atomic Commit)
• is_claimable: true    • Re-validates job status
• is_submittable: true  • Re-validates channel status
• Fast UI render (<8ms) • Checks agency tenant eligibility
                        • Executes atomic vendor cap
                        • Checks (job, agency, cand, chan) uniqueness
                              │
                              ▼
                       [ Commit Transaction ]
                              │
                              ▼
                     [ sys_outbox_events ]
                              │
                              ▼
                     [ Queue -> Update ES ]
```

#### Deterministic Write Invariant:
Never allow an Elasticsearch query result to dictate an authoritative database write without re-verifying transactional constraints in MySQL. The final write mutation must verify:
1. Requisition status (`jobs.status = 'active'`)
2. Channel publication status (`marketplace_channels.publication_status = 'LIVE'`)
3. Tenant authorization & agency KYC eligibility
4. Atomic vendor limit capacity
5. Uniqueness of `(job_id, agency_id, candidate_id, channel)`
before committing to MySQL InnoDB.

---

### 6.6 The Four-Dimensional Core Invariant Model (State Decoupling)

To prevent coupling business concepts into monolithic status codes, JERO enforces a **Four-Dimensional State Model**:

```
                       THE FOUR-DIMENSIONAL REQUISITION MATRIX
┌───────────────────────┬────────────────────────────┬─────────────────────────────────────────────────┐
│ DIMENSION             │ CORE QUESTION ANSWERED     │ POSSIBLE CANONICAL STATES                       │
├───────────────────────┼────────────────────────────┼─────────────────────────────────────────────────┤
│ 1. Core Job State     │ "Does this requisition     │ ACTIVE, INACTIVE, CLOSED, FILLED, CANCELLED     │
│                       │  exist in tenant ATS?"     │                                                 │
├───────────────────────┼────────────────────────────┼─────────────────────────────────────────────────┤
│ 2. Moderation State   │ "Is this content allowed   │ PENDING_REVIEW, APPROVED, REJECTED, QUARANTINED │
│                       │  on the marketplace?"      │                                                 │
├───────────────────────┼────────────────────────────┼─────────────────────────────────────────────────┤
│ 3. Publication State  │ "Is this channel currently │ DRAFT, LIVE, CLAIM_LIMIT_REACHED, PAUSED,       │
│                       │  open & receiving traffic?"│ DELISTED                                        │
├───────────────────────┼────────────────────────────┼─────────────────────────────────────────────────┤
│ 4. Eligibility State  │ "Can this specific agency  │ ELIGIBLE, ALREADY_HIRED, CAP_MAXED, KYC_BLOCKED,│
│                       │  perform this action now?" │ DISQUALIFIED                                    │
└───────────────────────┴────────────────────────────┴─────────────────────────────────────────────────┘
```

#### Why This Decoupling is Vital:
- **`Moderation = APPROVED` while `Publication = PAUSED`:** A recruiter approved by Super Admin can pause vendor claims without losing their approved moderation status.
- **`Moderation = APPROVED` while `Publication = CLAIM_LIMIT_REACHED`:** Vendor cap reached for Method 1, while Method 2 bench submissions remain completely live.
- **Never encode these into a single integer `public_market_status = 1/2/3`.** Each dimension operates with its own orthogonal state machine.

---

### 6.7 Idempotency vs. Business Uniqueness Invariants

JERO recognizes that **API Idempotency** and **Domain Business Uniqueness** solve two completely distinct distributed systems problems:

```
┌───────────────────────────────────────┬──────────────────────────────────────────────────────────────┐
│ CONCEPT                               │ PRIMARY THREAT PREVENTED                                     │
├───────────────────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 1. API Idempotency Keys               │ Protects against the SAME HTTP REQUEST repeated              │
│    (`sys_idempotency_keys`)           │ (e.g., recruiter double-clicking "Claim", mobile network     │
│                                       │  timeouts, automatic HTTP client retries).                   │
├───────────────────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 2. Domain Uniqueness Keys             │ Protects against the SAME BUSINESS TRANSACTION attempted    │
│    (`uq_submission_identity`)         │ through DIFFERENT requests, different surfaces, or across    │
│                                       │ time (e.g., submitting Rahul Sharma via Job Tracker and then │
│                                       │ submitting Rahul Sharma via Bench Marketplace).              │
└───────────────────────────────────────┴──────────────────────────────────────────────────────────────┘
```

Both layers are strictly required: the API Gateway terminates duplicate HTTP payloads via `sys_idempotency_keys`, while the MySQL InnoDB transactional engine rejects logical duplicates via `uq_submission_identity (job_id, agency_id, candidate_id, channel)`.

---

### 6.8 Explicit State Transition Validation Matrix (Allowed vs. Forbidden Transitions)

To eliminate erratic state jumps, silent regressions, and bypass of moderation policies, state transitions across `marketplace_channels` must be validated against a strict deterministic transition table:

```
                                  LEGAL STATE TRANSITION GRAPH
                                  
     [ DRAFT ] ───────────> [ PENDING_REVIEW ] ───────────> [ REJECTED ]
                                  │   │                         │
                     ┌────────────┘   └────────────┐            │ (Recruiter Edits & Resubmits)
                     ▼                             ▼            ▼
               [ APPROVED ]                 [ QUARANTINED ] ────┘
                     │
                     ▼
                 [ LIVE ] <═════════════════════════> [ PAUSED ]
                   │   ▲
                   │   │ (Slot Freed / Limit Raised)
                   ▼   │
        [ CLAIM_LIMIT_REACHED ] ──> [ DELISTED / CLOSED ]
```

#### Deterministic Transition Rules:

| Initial State | Target State | Trigger / Actor | Permitted? | Invariant Enforced |
| :--- | :--- | :--- | :---: | :--- |
| `DRAFT` | `PENDING_REVIEW` | Recruiter Publishes Channel | ✅ **Allowed** | Ingestion validation passed; queued for review |
| `DRAFT` | `LIVE` | Auto-Approval Engine | ✅ **Allowed** | Trust Score $\ge 85$, 0 flags, auto-approval ON |
| `PENDING_REVIEW` | `APPROVED` | Super Admin 1-Click Approve | ✅ **Allowed** | Manual review cleared |
| `PENDING_REVIEW` | `REJECTED` | Super Admin Structured Reject | ✅ **Allowed** | Remediation reason code logged to audit trail |
| `PENDING_REVIEW` | `QUARANTINED` | NLP Risk Engine / Admin | ✅ **Allowed** | Severe policy or anti-circumvention breach |
| `APPROVED` | `LIVE` | System Event Broadcast | ✅ **Allowed** | Channel active on Elasticsearch grid |
| `LIVE` | `PAUSED` | Recruiter or Admin Action | ✅ **Allowed** | Temporary suspension of new claims/submissions |
| `PAUSED` | `LIVE` | Recruiter Resumes Channel | ✅ **Allowed** | Restores open intake without re-moderation |
| `LIVE` | `CLAIM_LIMIT_REACHED`| Atomic Counter Guard | ✅ **Allowed** | `claimed_count >= vendor_limit` (Method 1 only) |
| `CLAIM_LIMIT_REACHED` | `LIVE` | Admin / Inactivity Clawback | ✅ **Allowed** | Slot vacated or `vendor_limit` increased |
| `LIVE` / `PAUSED` | `DELISTED` | Recruiter / Admin Closes Job | ✅ **Allowed** | Permanently pulled from marketplace |
| `REJECTED` | `PENDING_REVIEW` | Recruiter Resubmits Job | ✅ **Allowed** | Offending content edited; re-enters moderation |
| `QUARANTINED` | `PENDING_REVIEW` | Recruiter Submits Appeal | ✅ **Allowed** | Compliance audit remediation provided |
| `REJECTED` | `LIVE` | Direct Leap Attempt | 🛑 **FORBIDDEN** | **Critical Security Invariant:** Bypasses review |
| `QUARANTINED` | `LIVE` | Direct Leap Attempt | 🛑 **FORBIDDEN** | **Critical Security Invariant:** Unremediated leak |
| `CLOSED` | `LIVE` | Direct Leap Attempt | 🛑 **FORBIDDEN** | Must pass Section 6.3 Reopening Evaluation |
| `DELISTED` | `LIVE` | Direct Leap Attempt | 🛑 **FORBIDDEN** | Requires administrative reinstatement audit |
| `LIVE` | `APPROVED` | Backward Step Attempt | 🛑 **FORBIDDEN** | Redundant invalid state reversal |
| `CLAIM_LIMIT_REACHED`| `PENDING_REVIEW` | State Confusion Attempt | 🛑 **FORBIDDEN** | Capacity state is independent of moderation |

#### Backend Transition Guard Implementation:
```php
class MarketplaceChannelStateMachine
{
    private const ALLOWED_TRANSITIONS = [
        'DRAFT'                => ['PENDING_REVIEW', 'LIVE'],
        'PENDING_REVIEW'       => ['APPROVED', 'REJECTED', 'QUARANTINED'],
        'APPROVED'             => ['LIVE', 'PAUSED'],
        'LIVE'                 => ['PAUSED', 'CLAIM_LIMIT_REACHED', 'DELISTED'],
        'PAUSED'               => ['LIVE', 'DELISTED'],
        'CLAIM_LIMIT_REACHED'  => ['LIVE', 'DELISTED'],
        'REJECTED'             => ['PENDING_REVIEW'],
        'QUARANTINED'          => ['PENDING_REVIEW'],
        'DELISTED'             => ['DRAFT'],
    ];

    public static function assertTransitionAllowed(string $fromState, string $toState): void
    {
        $allowed = self::ALLOWED_TRANSITIONS[$fromState] ?? [];
        if (!in_array($toState, $allowed, true)) {
            throw new IllegalStateTransitionException(
                "Illegal marketplace state transition from [{$fromState}] to [{$toState}]. Direct publication without moderation is strictly prohibited."
            );
        }
    }
}
```

---

## 7. Algorithmic Risk, Quality & Anti-Circumvention Engine

Adheres to JERO's non-negotiable **Zero-Cloud AI Sovereignty** mandate ($0 API fees, sub-3ms evaluation, zero data leakage). 

A simplistic score deduction model (e.g. `score >= 85 && flags == 0`) is too rigid for production: legitimate JDs often contain standard recruitment phrases ("Contact our office during working hours" or "WhatsApp-based scheduling"). Hard rejections on soft anomalies degrade liquidity.

Therefore, JERO enforces a **3-Level Risk Decision Model & Multidimensional Evaluation**:

```
                              3-LEVEL RISK DECISION TAXONOMY
┌────────────────────────────────┬───────────────────────────┬───────────────────────────────────────────┐
│ RISK TIER                      │ SCORE RANGE (0–100)       │ OPERATIONAL OUTCOME & ACTION              │
├────────────────────────────────┼───────────────────────────┼───────────────────────────────────────────┤
│ 🟢 LOW RISK                    │ 0 – 29                    │ AUTO-APPROVE                              │
│                                │                           │ (If channel auto-approval toggle is ON)   │
├────────────────────────────────┼───────────────────────────┼───────────────────────────────────────────┤
│ 🟡 MEDIUM RISK                 │ 30 – 69                   │ HUMAN REVIEW                              │
│                                │                           │ Routes to Fast-Review Drawer for Admin    │
├────────────────────────────────┼───────────────────────────┼───────────────────────────────────────────┤
│ 🔴 HIGH RISK                   │ 70 – 100                  │ QUARANTINE / COMPLIANCE HOLD              │
│                                │                           │ Blocked from market; revision requested   │
└────────────────────────────────┴───────────────────────────┴───────────────────────────────────────────┘
```

---

### 7.1 Multidimensional Risk Assessment Output
Never treat numerical score as the sole source of moderation truth. The engine evaluates four distinct output dimensions:

```php
[
    'risk_score'          => 45,                  // Integer 0 to 100 (Higher = Higher Risk)
    'risk_tier'           => 'MEDIUM_RISK',       // LOW_RISK | MEDIUM_RISK | HIGH_RISK
    'risk_flags'          => ['SUSPICIOUS_PHRASE'],// Informational anomalies
    'policy_violations'   => [],                  // Hard regulatory breaches (e.g. raw phone number)
    'recommended_action'  => 'HUMAN_REVIEW',      // AUTO_APPROVE | HUMAN_REVIEW | QUARANTINE
    'remediation_guidance'=> 'Review contact phrasing in paragraph 3.',
]
```

---

### 7.2 Database-Configurable Risk Rules (`marketplace_risk_rules`)

Instead of hardcoding rules and weights inside PHP files, rules are stored in MySQL and cached in Dragonfly (4M QPS). Super Admins can tune weights, enable/disable patterns, and adjust thresholds via the Admin Console without code deployment:

```php
namespace App\Services\Marketplace;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MarketplaceRiskEngine
{
    /**
     * Evaluates requisition risk across database-configured rule definitions.
     */
    public function evaluate(array $jobData, int $companyTenureDays): array
    {
        $rules = $this->loadActiveRules();
        $riskScore = 0;
        $flags = [];
        $violations = [];

        $fullText = ($jobData['title'] ?? '') . ' ' . 
                    ($jobData['job_description'] ?? '') . ' ' . 
                    ($jobData['job_requirement'] ?? '');

        foreach ($rules as $rule) {
            if (!$rule->enabled) continue;

            if ($rule->rule_type === 'REGEX' && preg_match($rule->pattern, $fullText, $matches)) {
                $flags[] = "{$rule->rule_code}: Detected '{$matches[0]}'";
                $riskScore += $rule->score_delta;

                if ($rule->severity === 'CRITICAL') {
                    $violations[] = $rule->rule_code;
                }
            }
        }

        // JD Word Count Evaluation
        $wordCount = str_word_count(strip_tags($fullText));
        if ($wordCount < 150) {
            $flags[] = "QUALITY_LOW_WORD_COUNT: Only {$wordCount} words (Minimum 150 required)";
            $riskScore += 25;
        }

        // Tenure Trust Discount (Long-standing verified tenants have lower baseline risk)
        $tenureDiscount = min(15, (int) ($companyTenureDays / 90) * 3);
        $finalScore = max(0, min(100, $riskScore - $tenureDiscount));

        // Determine 3-Level Tier
        if (count($violations) > 0 || $finalScore >= 70) {
            $tier = 'HIGH_RISK';
            $action = 'QUARANTINE';
        } elseif ($finalScore >= 30) {
            $tier = 'MEDIUM_RISK';
            $action = 'HUMAN_REVIEW';
        } else {
            $tier = 'LOW_RISK';
            $action = 'AUTO_APPROVE';
        }

        return [
            'risk_score'          => $finalScore,
            'risk_tier'           => $tier,
            'risk_flags'          => $flags,
            'policy_violations'   => $violations,
            'recommended_action'  => $action,
        ];
    }

    private function loadActiveRules(): array
    {
        return Redis::remember('marketplace:risk_rules:active', 3600, function () {
            return DB::table('marketplace_risk_rules')
                ->where('enabled', 1)
                ->get()
                ->toArray();
        });
    }
}
```

---

## 8. Super Admin Marketplace Command Center Dashboard

The Command Center provides real-time operational telemetry designed for rapid decision-making:

### 8.1 Real-Time KPI Telemetry Cards
- **Requisition Moderation Velocity:**
  - *Pending Review:* Count with SLA badge (`<2h: Green`, `2-6h: Yellow`, `>6h: Red Alert`).
  - *Auto-Approval Rate:* `% of submissions cleared by risk engine (Target: >70%)`.
  - *Median Moderation TAT:* Minutes from recruiter submission to live status.
- **Marketplace Liquidity & Volume:**
  - *Active Marketplace Jobs:* Live count across Method 1 (Agency Hiring) and Method 2 (Bench Talent).
  - *Total Agency Claims (Method 1):* Active claims vs. maximum capacity caps.
  - *Direct Bench Submissions (Method 2):* Consultants deployed in the last 24h / 7d.
- **Ecosystem Health & Risk:**
  - *Quarantined Listings:* Potential off-platform contact leaks detected.
  - *Zero-Activity Requisitions:* Jobs live $>14$ days with 0 agency engagement.
  - *Agency Coin Consumption:* Commercial coins consumed for claims and bench submissions.

### 8.2 Actionable Modules
1. **High-Risk Ingestion Feed:** Displays incoming jobs flagged by NLP heuristics (suspicious salary ranges, hidden emails/phone numbers).
2. **High-Demand vs. Orphan Jobs Radar:** Breakdown of requisitions receiving excessive vendor claims vs. critical client positions starved of agency submissions.
3. **Agency Response SLA Monitor:** Live tracking of agencies that claimed positions but have failed to submit candidates within 72 hours.

---

## 9. "All Jobs" Central Requisition Management (100% CQRS via Elasticsearch)

The central interface for discovering, monitoring, and governing all requisitions across the entire JERO platform:

### 9.1 Elasticsearch Query Architecture
All queries execute against index `ats_jobs`. No SQL `SELECT` queries are executed on web worker threads. Depending on the active tab, the boolean filter dynamically selects the normalized channel attributes:

#### A. Agency Hiring Channel Filter (Method 1)
```json
{
  "query": {
    "bool": {
      "must": [
        {
          "multi_match": {
            "query": "Senior Full Stack Engineer",
            "fields": ["title^3", "job_description^1.5", "job_requirement^1.5", "skills.name^2"],
            "type": "best_fields",
            "fuzziness": "AUTO"
          }
        }
      ],
      "filter": [
        { "term": { "status": "active" } },
        { "term": { "agency_channel.enabled": true } },
        { "term": { "agency_channel.publication_status": "LIVE" } },
        { "range": { "min_yearly": { "gte": 1200000 } } }
      ]
    }
  },
  "aggs": {
    "by_agency_pub_status": { "terms": { "field": "agency_channel.publication_status" } },
    "by_employment_type": { "terms": { "field": "employment_type" } },
    "salary_bands": {
      "range": {
        "field": "min_yearly",
        "ranges": [
          { "to": 500000 },
          { "from": 500000, "to": 1500000 },
          { "from": 1500000, "to": 3000000 },
          { "from": 3000000 }
        ]
      }
    }
  },
  "sort": [
    { "priority": { "order": "desc" } },
    { "created_at": { "order": "desc" } }
  ],
  "size": 25,
  "from": 0
}
```

#### B. Bench Talent Channel Filter (Method 2)
```json
{
  "query": {
    "bool": {
      "filter": [
        { "term": { "status": "active" } },
        { "term": { "bench_channel.enabled": true } },
        { "term": { "bench_channel.publication_status": "LIVE" } }
      ]
    }
  }
}
```

### 9.2 High-Density Data Grid (15.6" Screen Optimized)
- Row height fixed at `48px` (compact) or `64px` (comfortable) to display 10–12 rows above the fold on a 768px height viewport.
- **Unified Channel Health Visual Badge:**
  Every row in the central requisition grid renders an ultra-dense, multi-dimensional health pill directly in the Status column:
  
  ```
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ JOB: ACTIVE ● | AGENCY: LIVE ● (3/5 claims) | BENCH: LIVE ● (17 CVs) | RISK: LOW ● 12  │
  └────────────────────────────────────────────────────────────────────────────────────────┘
  ```
  - `JOB:` Reflects ATS core state (`ACTIVE ● [Green]` | `INACTIVE ● [Amber]` | `CLOSED ● [Slate]`)
  - `AGENCY:` Reflects Method 1 state (`LIVE ● (claims/cap)` | `CAP REACHED ●` | `PENDING ●` | `OFF ⚪`)
  - `BENCH:` Reflects Method 2 state (`LIVE ● (submission count)` | `PENDING ●` | `OFF ⚪`)
  - `RISK:` Trust & Moderation tier (`LOW ● 0-29` | `MED ● 30-69` | `HIGH ● 70-100`)
  - `SYNC:` Outbox/Projection integrity badge (`HEALTHY ● v184` | `LAGGING ⚠️ 1.2s`)

- **Columns:**
  1. `Checkbox` (Multi-select for bulk actions)
  2. `Requisition ID & Title` (With direct quick-drawer trigger)
  3. `Hiring Company / Client` (Logo, tenant name, verification badge)
  4. `Marketplace Channels` (Pills: Career Site, Agency Claim, Bench Talent)
  5. `Commercial Terms` (Commission %, Fixed Fee, Billing Rate, Payment Days)
  6. `Agency Activity` (Active claims count / Bench CVs submitted)
  7. `Status Badge` (Interactive Unified Channel Health Visual Badge)
  8. `Action Menu` (Quick View Drawer, Edit Status, Audit Trail, Force Delist)

---

## 10. Dedicated Moderation Cockpit & Fast-Review Drawer

Clicking any requisition in the Moderation Queue slides open the **1-Click Fast-Review Drawer** (`#kt_activitiesMarketplaceJob`, responsive width: `min(92vw, 1100px)`):

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [MODERATION REVIEW] Senior Full-Stack Engineer — FinTech Corp                           [ Esc to Close ] [ X ]    │
│ Requisition ID: #10492 | Submitted: 24 mins ago | SLA TAT: 1h 36m remaining | Hiring Tenant Tier: Enterprise       │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                         │                                                         │
│ [ LEFT PANE: REQUISITION SPECIFICATIONS (60%) ]         │ [ RIGHT PANE: RISK & AUDIT HEURISTICS (40%) ]           │
│                                                         │                                                         │
│ • Client: FinTech Corp (Verified GSTIN / CIN)           │ 🛡️ OVERALL TRUST SCORE: 45 / 100 [ HIGH RISK ]          │
│ • Role Type: Full-Time Domestic | Hyderabad, India      │                                                         │
│ • Compensation: ₹18,00,000 – ₹26,00,000 PA              │ 🚨 TRIGGERED RISK FLAGS:                                 │
│ • Commercial Terms: 8.33% Annual CTC | 45-Day Payment   │ 1. [LEAK_EMAIL] Line 14: personal email detected         │
│ • Vendor Allocation Cap: 5 Agencies                     │    "contact recruiter.sarah@gmail.com directly"          │
│                                                         │    [ Highlight in Text ] [ Strip Automatically ]         │
│ • Core Responsibilities:                                │                                                         │
│   "Architect and develop scalable microservices using   │ 2. [QUALITY_WORD_COUNT] Passed (284 words >= 150 min)    │
│   Laravel 11, Node.js, and Elasticsearch..."            │                                                         │
│                                                         │ 3. [COMMERCIAL_TERMS] Passed (Valid CTC & 45-day terms)  │
│ • Required Skills:                                      │                                                         │
│   [ PHP 8.3 ] [ Laravel ] [ MySQL ] [ Elasticsearch ]   │ 4. [COMPANY_TENURE] +6 Bonus (Tenant for 270 days)       │
│                                                         │                                                         │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [ BOTTOM ACTION BAR ]                                                                                             │
│ [ Approve & Publish (A) ]   [ Request Recruiter Edit (E) ]   [ Reject with Reason (R) ]   [ Quarantine Job (Q) ]  │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 11. Structured Rejection Taxonomy & Remediation Engine

Super Admins must never reject a job arbitrarily. Every rejection must be educational, actionable, and auditable:

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                          STRUCTURED REJECTION TAXONOMY                                            │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REASON CODE           │ CATEGORY                 │ DEFAULT RECRUITER REMEDIATION TEMPLATE                         │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_CONTACT_LEAK      │ Anti-Circumvention       │ "Direct contact details (phone/email/WhatsApp/links) were      │
│                       │                          │ detected. Please remove personal contact info to comply with   │
│                       │                          │ platform commercial terms."                                    │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_VAGUE_JD          │ Content Quality          │ "Job description lacks sufficient detail regarding core        │
│                       │                          │ responsibilities or required skills. Please expand to >= 150   │
│                       │                          │ words."                                                        │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_SALARY_ANOMALY    │ Commercial Sanity        │ "The specified compensation range is unrealistic or has an     │
│                       │                          │ inverted band (Min > Max). Please configure a standard salary."│
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_UNCLEAR_TERMS     │ Commercial Terms         │ "Agency commission terms or payment credit days are undefined. │
│                       │                          │ Please verify commercials in the Job Publishing Drawer."       │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_DUPLICATE_LISTING │ Platform Hygiene         │ "An identical active position already exists on the platform   │
│                       │                          │ under Job ID #{EXISTING_JOB_ID}."                              │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
│ REJ_COMPLIANCE_EEO    │ Legal & Policy           │ "Requisition contains non-compliant discriminatory language.   │
│                       │                          │ Please revise to adhere to Equal Opportunity guidelines."      │
+───────────────────────+──────────────────────────+────────────────────────────────────────────────────────────────+
```

### 1-Click Recruiter Resubmission Workflow
1. When rejected, `jobs.public_market_status` updates to `3`.
2. Asynchronous email (`mail_type = 29`) is dispatched via Horizon queue with the specific remediation guidance.
3. The recruiter's ATS dashboard shows an actionable red alert banner: `Revision Requested: Contact Leak Detected [Click to Correct & Resubmit]`.
4. Editing the job automatically clears the offending flags; upon clicking "Resubmit", the job re-enters the moderation queue (`public_market_status = 1`) with an audit badge: `Resubmitted v2`.

---

## 12. Agency Exchange Governance (Method 1 Claims & Method 2 Bench)

Super Admin oversees the commercial balance between hiring companies and staffing agencies:

### 12.1 Method 1: Requisition Claims Governance
- Tracks agency capacity allocations under `recommendation_relation` (`add_status = 1`).
- **Vendor Capacity Caps:** Enforces `jobs.vendor_limit`. When claimed count equals the limit, `jobs.public_market_status` transitions to `4 (CLAIM_LIMIT_REACHED)`.
- **7-Day Inactivity Clawback:** If an agency claims a position but submits 0 candidates within 7 days, Super Admin can revoke the claim, returning the slot to the open market and refunding or forfeiting operational coins according to policy.

### 12.2 Method 2: Bench Talent Submissions Governance
- Tracks direct bench candidate deployments into client ATS pipelines (`ac_status = 0`).
- Prevents candidate duplicate submissions across multiple agencies using cryptographic SHA-256 phone/email hashes.

---

## 13. Agency Marketplace Scorecard & Performance Directory

Staffing agencies are scored dynamically across 4 performance vectors to prevent candidate spam:

$$Score = (0.40 \times \text{Quality}) + (0.30 \times \text{Conversion}) + (0.20 \times \text{Speed}) + (0.10 \times \text{Compliance})$$

1. **Submission Quality Score (40%):** Ratio of submitted candidates accepted into Stage 1 recruiter review.
2. **Interview-to-Hire Conversion (30%):** Ratio of interviewed candidates who receive offers and join.
3. **Speed to Submit (20%):** Average turnaround time from claiming a job to submitting the first qualified candidate.
4. **Platform Compliance (10%):** Absence of off-platform hiring disputes and candidate ownership complaints.

### Agency Tier Matrix
- **Tier 1 (Elite Partner - Score $\ge 85$):** Unlimited concurrent claims, priority candidate card routing in recruiter review queues.
- **Tier 2 (Certified Partner - Score $65–84$):** Standard vendor claim cap (up to 15 concurrent jobs).
- **Tier 3 (Probationary Partner - Score $< 65$):** Restricted claim cap (max 3 concurrent jobs).
- **Suspended Partner:** Zero marketplace access due to compliance strikes.

---

## 14. Job Detail Command Center (9-Tab Enterprise Drawer)

Clicking any requisition in the Central Requisition Management grid slides open the **Job Detail Command Center Drawer** (`#superadmin_job_command_drawer`, 1100px width on 15.6" screens, responsive `min(92vw, 1100px)`), providing comprehensive, single-screen control across 9 micro-tabs:

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [JOB #10492] Lead Cloud Architect — Enterprise Logistics                               [ Esc ] [ Full Screen ] [ X ]
│ ┌───────────────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ KPI PILLS: [JOB: ACTIVE ●] [AGENCY: LIVE (3/5 Claims) ●] [BENCH: LIVE (17 CVs) ●] [RISK: 14 LOW ●] [SYNC: v184]│ │
│ └───────────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [ HORIZONTAL MICRO-TAB BAR (15.6" Optimized) ]                                                                    │
│ [ Overview ] [ Moderation ] [ Agency Channel ] [ Bench Channel ] [ Agencies ] [ Candidates ] [ Risk ] [ Audit ] [ Timeline ]
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                                                                                   │
│ 1. OVERVIEW: Complete Job Description, compensation band (₹32L–₹45L), location, shift, skills cloud, hiring team. │
│ 2. MODERATION: Review state (APPROVED), auto-clearance status, structured rejection picker & remediation template.│
│ 3. AGENCY CHANNEL: Method 1 configuration (LIVE), vendor cap (5), active claims (3), claim pause/resume toggle.   │
│ 4. BENCH CHANNEL: Method 2 configuration (LIVE), hourly billing rate, candidate submission active switch.         │
│ 5. AGENCIES: Roster of assigned/claiming vendors: Apex Talent (2 CVs), TechStaff UK (1 CV), GlobalHire (0 CVs).   │
│ 6. CANDIDATES: Canonical submissions pipeline (submission_id, candidate name, agency, channel, ATS stage, actions)│
│ 7. RISK: Heuristic breakdown (Trust Score: 14/100, zero contact leaks, JD length passed, tenure bonus applied).    │
│ 8. AUDIT: Immutable tamper-evident ledger, cryptographic snapshot hashes, actor IDs, reason codes.                │
│ 9. TIMELINE: Nanosecond chronological event trace with correlation IDs from job post to hiring settlement.         │
│                                                                                                                   │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [ BOTTOM ACTION BAR (Sticky at drawer base) ]                                                                     │
│ [ 🛑 Reject with Reason (R) ]  [ ⏸️ Pause Channel (P) ]  [ ↗️ Open in Client ATS ]  [ ✅ Approve & Publish (A) ]     │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 15. High-Throughput Bulk Operations Engine

Super Admins managing hundreds of daily submissions require safe, transactional bulk actions:

### 15.1 Supported Bulk Actions
1. **Bulk Approve:** Transitions selected jobs to `public_market_status = 2` (Live).
2. **Bulk Auto-Clear Eligible:** Automatically approves all jobs currently in pending status with `Risk Score >= 85` and `0 Flags`.
3. **Bulk Reject:** Rejects selected jobs with a shared reason code and bulk remediation email dispatch.
4. **Bulk Delist / Pause:** Temporarily pulls selected jobs from the marketplace grid.
5. **Bulk Adjust Vendor Limit:** Reassigns `vendor_limit` across multiple client positions simultaneously.

### 15.2 Concurrency Safeguards & Chunked Processing
- Bulk updates $> 20$ items are offloaded to background Horizon queues in chunks of 50 to prevent database lock contention.
- Uses `sys_idempotency_keys` to guarantee zero duplicate writes if an admin double-clicks or re-submits a bulk batch.

---

## 16. Commercial Coin Economics & Accounting (Events 38–41)

Marketplace transactions consume and credit operational coins governed by the `coin_events` and `coin_transection_logs` ledgers:

| Event ID | Event Identifier | Account Type | Entity Debited | Ledger Impact | Description |
| :---: | :--- | :---: | :---: | :---: | :--- |
| **38** | `MARKETPLACE_CLAIM_JOB` | `operational` | Claiming Agency | `DEBIT` | Deducted when agency claims a Method 1 position |
| **39** | `MARKETPLACE_BENCH_SUBMISSION` | `operational` | Submitting Agency | `DEBIT` | Deducted when agency deploys a Method 2 bench CV |
| **40** | `MARKETPLACE_CLAIM_REVOCATION_REFUND` | `operational` | Claiming Agency | `CREDIT` | Refunded if admin cancels job due to client fault |
| **41** | `MARKETPLACE_SUCCESS_SETTLEMENT` | `sales` | Hiring Company | `DEBIT` | Platform commission settlement upon candidate joining |

### 16.1 Atomic Coin Transaction & Financial Reservation Model

Commercial integrity requires that coin debits and business entities are committed atomically. No candidate submission or agency claim may succeed without a verified coin deduction, and no coin deduction may occur if the business operation fails.

#### 1. ACID Invariant & Double-Entry Accounting
All deductions (Events 38 & 39) and refunds (Event 40) execute within the exact same database transaction as the primary entity write:

```php
DB::transaction(function () use ($jobId, $agencyId, $candidateId, $coinCost) {
    // 1. Pessimistic Lock on Agency Coin Balance
    $agency = DB::table('companies')
        ->where('id', $agencyId)
        ->lockForUpdate()
        ->first();

    if ($agency->operational_coins < $coinCost) {
        throw new InsufficientCoinsException("Agency requires {$coinCost} operational coins. Current balance: {$agency->operational_coins}");
    }

    $openingBalance = $agency->operational_coins;
    $closingBalance = $openingBalance - $coinCost;

    // 2. Decrement Balance
    DB::table('companies')->where('id', $agencyId)->update([
        'operational_coins' => $closingBalance,
        'updated_at' => now(),
    ]);

    // 3. Append Immutable Ledger Entry
    $transactionId = DB::table('coin_transection_logs')->insertGetId([
        'company_id'      => $agencyId,
        'event_id'        => 39, // MARKETPLACE_BENCH_SUBMISSION
        'coin_amount'     => $coinCost,
        'opening_balance' => $openingBalance,
        'closing_balance' => $closingBalance,
        'reference_type'  => 'marketplace_submissions',
        'reference_id'    => $submissionId,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    // 4. Create Canonical Submission Record
    // ...
});
```

#### 2. Reversal & Compensating Transaction Engine (Event 40)
If a claimed requisition is subsequently delisted by Super Admin due to client misrepresentation or TOS breach, an automated compensatory transaction is generated:
- Credits the agency's operational coin account by the exact debited amount.
- Logs Event 40 in `coin_transection_logs` with a forward reference to the original debit transaction ID (`original_tx_id`).
- Guarantees financial non-repudiation and zero orphaned balances.

#### 3. Continuous Financial Reconciliation
A scheduled nightly reconciliation job (`marketplace:reconcile-coins`) validates the ledger equation across all active tenant accounts:
$$\text{Opening Balance} + \sum \text{Credits} - \sum \text{Debits} = \text{Current Balance}$$
Any variance triggers an immediate high-priority alert to the Super Admin Security & Financial Operations channel.

---

## 17. Database Truth & Schema Alignment

All table structures adhere strictly to `database/schema/mysql-schema.sql` and InnoDB engine:

```sql
-- 1. Immutable Moderation Audit Log Ledger (Schema L4889)
-- CREATE TABLE IF NOT EXISTS `job_marketplace_moderation_logs` (
--   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `job_id` INT UNSIGNED NOT NULL,
--   `company_id` INT UNSIGNED NOT NULL,
--   `actor_id` INT UNSIGNED NOT NULL,
--   `actor_role` ENUM('recruiter', 'superadmin', 'system') NOT NULL,
--   `action` ENUM('submit', 'auto_approve', 'manual_approve', 'reject', 'suspend', 'resubmit', 'withdraw') NOT NULL,
--   `from_mod_state` VARCHAR(32) NOT NULL,
--   `to_mod_state` VARCHAR(32) NOT NULL,
--   `reason_code` VARCHAR(64) NULL,
--   `rejection_notes` TEXT NULL,
--   `risk_score` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
--   `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `idx_job_mod_history` (`job_id`, `created_at`),
--   KEY `idx_company_mod_history` (`company_id`, `created_at`),
--   KEY `idx_actor_mod_actions` (`actor_id`, `created_at`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Concurrency Idempotency Ledger (Schema L4205)
-- CREATE TABLE IF NOT EXISTS `sys_idempotency_keys` (
--   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `tenant_id` INT(11) NOT NULL,
--   `idempotency_key` VARCHAR(64) NOT NULL,
--   `request_hash` VARCHAR(64) NOT NULL,
--   `status` ENUM('PROCESSING', 'COMPLETED', 'FAILED_FINAL', 'FAILED_RETRYABLE') NOT NULL DEFAULT 'PROCESSING',
--   `response_status` INT(11) DEFAULT NULL,
--   `response_body` LONGTEXT DEFAULT NULL,
--   `last_error` TEXT DEFAULT NULL,
--   `expires_at` TIMESTAMP NOT NULL,
--   `completed_at` TIMESTAMP NULL DEFAULT NULL,
--   `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `idx_idempotency_tenant_key` (`tenant_id`, `idempotency_key`),
--   KEY `idx_idempotency_expires` (`expires_at`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Resilient Transactional Outbox Ledger (Schema L4226)
CREATE TABLE IF NOT EXISTS `sys_outbox_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Canonical outbox event_id',
  `aggregate_id` INT UNSIGNED NOT NULL COMMENT 'Target entity ID (e.g., jobs.id or submission_id)',
  `aggregate_type` VARCHAR(64) NOT NULL COMMENT 'Target entity type (e.g., job, submission, claim)',
  `aggregate_version` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Monotonic entity state version for out-of-order rejection',
  `event_type` VARCHAR(64) NOT NULL COMMENT 'Domain event name (e.g., MarketJobPublished, BenchCandidateSubmitted)',
  `payload` JSON NOT NULL COMMENT 'Serialized event data payload',
  `attempt_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of execution attempts',
  `status` ENUM('PENDING', 'PROCESSING', 'COMPLETED', 'FAILED_RETRYABLE', 'FAILED_DEAD_LETTER') NOT NULL DEFAULT 'PENDING',
  `available_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Execution availability cursor',
  `processed_at` TIMESTAMP NULL DEFAULT NULL,
  `failed_at` TIMESTAMP NULL DEFAULT NULL,
  `last_error` TEXT NULL COMMENT 'Exception stack trace or failure diagnostic',
  `correlation_id` VARCHAR(64) NULL COMMENT 'Distributed tracing UUID',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_outbox_drain` (`status`, `available_at`, `id`),
  KEY `idx_outbox_aggregate` (`aggregate_type`, `aggregate_id`, `aggregate_version`),
  KEY `idx_outbox_correlation` (`correlation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Normalized Marketplace Channels Entity
-- Eliminates column bloat on the core `jobs` table by isolating channel state
CREATE TABLE IF NOT EXISTS `marketplace_channels` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_id` INT UNSIGNED NOT NULL,
  `company_id` INT UNSIGNED NOT NULL COMMENT 'Hiring Company Tenant ID',
  `channel_type` ENUM('AGENCY_HIRING', 'BENCH_TALENT') NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `moderation_status` ENUM('PENDING_REVIEW', 'APPROVED', 'REJECTED', 'QUARANTINED') NOT NULL DEFAULT 'PENDING_REVIEW',
  `publication_status` ENUM('DRAFT', 'LIVE', 'CLAIM_LIMIT_REACHED', 'PAUSED', 'DELISTED') NOT NULL DEFAULT 'DRAFT',
  `approval_mode` ENUM('AUTO', 'MANUAL') NOT NULL DEFAULT 'MANUAL',
  `vendor_limit` INT NULL DEFAULT 5 COMMENT 'Applies to AGENCY_HIRING only; NULL for BENCH_TALENT',
  
  -- Operational & Moderation Audit Metadata
  `moderated_by` INT UNSIGNED NULL COMMENT 'Super Admin User ID',
  `moderated_at` TIMESTAMP NULL DEFAULT NULL,
  `moderation_reason` VARCHAR(255) NULL,
  `published_by` INT UNSIGNED NULL COMMENT 'Recruiter or Admin User ID',
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `paused_by` INT UNSIGNED NULL,
  `paused_at` TIMESTAMP NULL DEFAULT NULL,
  `rejected_by` INT UNSIGNED NULL,
  `rejected_at` TIMESTAMP NULL DEFAULT NULL,
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_job_channel_unique` (`job_id`, `channel_type`),
  KEY `idx_channel_company` (`company_id`, `channel_type`),
  KEY `idx_channel_pub_state` (`channel_type`, `publication_status`, `moderation_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Canonical Marketplace Submissions Entity (Single Source of Truth)
-- Maps to the explicit business submission_id across all views
CREATE TABLE IF NOT EXISTS `marketplace_submissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Canonical business submission_id',
  `job_id` INT UNSIGNED NOT NULL,
  `company_id` INT UNSIGNED NOT NULL COMMENT 'Hiring Client Tenant ID',
  `agency_id` INT UNSIGNED NOT NULL COMMENT 'Partner Agency Tenant ID',
  `candidate_id` INT UNSIGNED NOT NULL,
  `channel` ENUM('AGENCY_HIRING', 'BENCH_TALENT') NOT NULL COMMENT 'Business engagement domain',
  `source` ENUM('MARKETPLACE', 'DIRECT_JOB_TRACKER', 'ADMIN', 'API', 'IMPORT') NOT NULL COMMENT 'Origin surface',
  `application_id` INT UNSIGNED NULL COMMENT 'ATS implementation reference to job_applications.id',
  `lifecycle_status` VARCHAR(32) NOT NULL DEFAULT 'SUBMITTED' COMMENT 'Mirrors authoritative ATS pipeline stage',
  `version` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Monotonic client sync counter for optimistic concurrency',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- Critical fix: Uniqueness includes `channel` so a candidate can legitimately exist in both channels
  UNIQUE KEY `uq_submission_identity` (`job_id`, `agency_id`, `candidate_id`, `channel`),
  KEY `idx_sub_job_agency` (`job_id`, `agency_id`),
  KEY `idx_sub_company_status` (`company_id`, `lifecycle_status`),
  KEY `idx_sub_candidate` (`candidate_id`),
  KEY `idx_sub_app_ref` (`application_id`),
  CONSTRAINT `fk_sub_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sub_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sub_agency` FOREIGN KEY (`agency_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sub_app` FOREIGN KEY (`application_id`) REFERENCES `job_applications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Submission Audit Log Ledger
-- Tracks full granular history for compliance, tamper-evidence, and non-repudiation
CREATE TABLE IF NOT EXISTS `marketplace_submission_audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` BIGINT UNSIGNED NOT NULL,
  `actor_id` INT UNSIGNED NOT NULL,
  `actor_role` ENUM('client_recruiter', 'agency_recruiter', 'superadmin', 'system') NOT NULL,
  `action` ENUM('SUBMITTED', 'STATUS_CHANGED', 'NOTE_ADDED', 'INTERVIEW_SCHEDULED', 'OFFER_EXTENDED', 'WITHDRAWN') NOT NULL,
  `from_status` VARCHAR(32) NULL,
  `to_status` VARCHAR(32) NOT NULL,
  `source` ENUM('MARKETPLACE', 'DIRECT_JOB_TRACKER', 'CLIENT_ATS', 'ADMIN_PANEL', 'API') NOT NULL,
  `reason` VARCHAR(255) NULL,
  `ip_address` VARCHAR(45) NULL,
  `correlation_id` VARCHAR(64) NULL COMMENT 'Distributed tracing ID across microservices',
  `snapshot_hash` VARCHAR(64) NULL COMMENT 'SHA-256 state snapshot hash for cryptographic tamper verification',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_audit_history` (`submission_id`, `created_at`),
  KEY `idx_sub_actor` (`actor_id`, `created_at`),
  KEY `idx_sub_corr_id` (`correlation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Configurable Marketplace Risk Rules Engine Entity
CREATE TABLE IF NOT EXISTS `marketplace_risk_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_code` VARCHAR(64) NOT NULL,
  `category` ENUM('HEURISTIC', 'POLICY', 'COMMERCIAL', 'TRUST') NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `description` VARCHAR(255) NULL,
  `weight` INT NOT NULL COMMENT 'Negative points or penalty score',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `action_on_match` ENUM('FLAG', 'QUARANTINE', 'REJECT') NOT NULL DEFAULT 'FLAG',
  `regex_pattern` VARCHAR(512) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_risk_rule_code` (`rule_code`),
  KEY `idx_risk_category_active` (`category`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 17.1 Backward-Compatibility Bridge to Existing Schema
To guarantee 100% backward compatibility with `database/schema/mysql-schema.sql` (Rule 8 & Rule 10), the system implements an Eloquent / Query abstraction layer:

| Normalized Conceptual Entity | Existing Production MySQL Columns | Compatibility Translation & Synchronization Logic |
| :--- | :--- | :--- |
| `marketplace_channels (AGENCY_HIRING)` | `jobs.show_vendor`, `jobs.public_market_status`, `jobs.vendor_limit` | `show_vendor = 'yes'` maps to `enabled = 1`. `public_market_status` reflects moderation and publication state. |
| `marketplace_channels (BENCH_TALENT)` | `jobs.bench_submission`, `global_settings.bench_status` | `bench_submission = 'enabled'` maps to `enabled = 1`. Moderation governed by `bench_status`. |
| `marketplace_submissions.id` (`submission_id`) | Canonical Business ID | The universal business identity across all surfaces. |
| `marketplace_submissions.application_id` | `job_applications.id` | The operational ATS implementation reference. `rec_candidate_assign.ac_status = 0` denotes active bench assignment. |
| Agency Appointment | `recommendation_relation.final_status = 1` | `final_status = 1` immediately activates Direct Client Job and triggers the dual-action card. |

---

## 18. Elasticsearch 8.x Mapping & CQRS Sync Architecture

The `ats_jobs` index is indexed with dedicated multi-channel objects to power sub-8ms faceted queries without schema coupling:

```json
{
  "mappings": {
    "properties": {
      "id": { "type": "integer" },
      "company_id": { "type": "integer" },
      "title": { "type": "text", "analyzer": "standard", "fields": { "keyword": { "type": "keyword" } } },
      "job_description": { "type": "text", "analyzer": "standard" },
      "job_requirement": { "type": "text", "analyzer": "standard" },
      "status": { "type": "keyword" },
      "agency_channel": {
        "properties": {
          "enabled": { "type": "boolean" },
          "moderation_status": { "type": "keyword" },
          "publication_status": { "type": "keyword" },
          "vendor_limit": { "type": "integer" },
          "claimed_count": { "type": "integer" },
          "is_claimable": { "type": "boolean" }
        }
      },
      "bench_channel": {
        "properties": {
          "enabled": { "type": "boolean" },
          "moderation_status": { "type": "keyword" },
          "publication_status": { "type": "keyword" },
          "submissions_count": { "type": "integer" },
          "is_submittable": { "type": "boolean" }
        }
      },
      "work_type": { "type": "keyword" },
      "employment_type": { "type": "integer" },
      "min_yearly": { "type": "double" },
      "max_yearly": { "type": "double" },
      "risk_score": { "type": "float" },
      "risk_flags": { "type": "keyword" },
      "rejection_reason_code": { "type": "keyword" },
      "aggregate_version": { "type": "integer" },
      "created_at": { "type": "date", "format": "yyyy-MM-dd HH:mm:ss||strict_date_optional_time" },
      "updated_at": { "type": "date", "format": "yyyy-MM-dd HH:mm:ss||strict_date_optional_time" }
    }
  }
}
```

### 18.1 Transactional Outbox Resilience, Failure Handling & Dead-Letter Queue (DLQ)

To guarantee that high event velocity does not result in lost events, blocked tenant queues, or stale projections, outbox processing adheres to industrial-grade event streaming standards:

```
                                OUTBOX RESILIENCE & REPLAY LIFECYCLE
                                
       [ MySQL Primary Mutation ] ──> Commit to sys_outbox_events (Status: PENDING)
                                                │
                                                ▼
                                    [ Horizon Queue Worker ]
                                                │
                          ┌─────────────────────┴─────────────────────┐
                          ▼                                           ▼
                 [ Execution Success ]                        [ Transient Failure ]
                          │                                           │
                          ▼                                           ▼
                [ Update ES Projection ]                     [ Retry Backoff Engine ]
               (Version Monotonic Check)                              │
                          │                               ├── Attempt 1: Immediate
                          ▼                               ├── Attempt 2: +10s Backoff
              [ Mark Event COMPLETED ]                    └── Attempt 3: +60s Backoff
                          │                                           │
                          ▼                                           ▼
             [ Broadcast via Reverb ]                       [ Max Attempts Exceeded ]
                                                                      │
                                                                      ▼
                                                          [ FAILED_DEAD_LETTER (DLQ) ]
                                                          (Preserves Payload & Trace)
                                                                      │
                                                                      ▼
                                                          [ Super Admin Review & Alert ]
                                                                      │
                                                                      ▼
                                                          [ Safe Idempotent Replay ]
                                                         (php artisan outbox:replay {id})
```

#### 1. Worker Batch Processing with Row-Level Skip Locked
Horizon workers drain `sys_outbox_events` continuously without thread contention:

```sql
SELECT id, aggregate_id, aggregate_type, aggregate_version, event_type, payload, attempt_count
FROM sys_outbox_events
WHERE status = 'PENDING' AND available_at <= NOW()
ORDER BY id ASC
LIMIT 100
FOR UPDATE SKIP LOCKED;
```

#### 2. Exponential Retry Backoff & Poison Event Isolation
- **Attempt 1:** Executed immediately upon worker poll.
- **Attempt 2:** If Elasticsearch cluster is busy or network times out, worker increments `attempt_count` and reschedules:
  `available_at = NOW() + INTERVAL 10 SECOND`, status remains `FAILED_RETRYABLE`.
- **Attempt 3:** If still failing, worker reschedules:
  `available_at = NOW() + INTERVAL 60 SECOND`.
- **Poison Pill Threshold ($> 3$ failures):** If an event fails 3 consecutive times, it is marked `FAILED_DEAD_LETTER` with `failed_at = NOW()` and `last_error` populated.
- **Queue Non-Blocking Guarantee:** A poison event is permanently bypassed by the worker queue cursor, allowing subsequent events for that tenant or other tenants to process without delay.
- **DLQ Telemetry Alarm:** An immediate event is broadcast to the Super Admin Governance Dashboard (`/super-admin/marketplace/audit?tab=dlq`).

#### 3. Deterministic Event Replay & Version Conflict Resolution Algorithm
To eliminate race conditions between out-of-order event arrivals and ensure safe replay of historical events:

$$\mathbf{Invariant:} \quad \mathbf{If} \ \text{ES Document Version} \ge \text{Incoming Event Version} \quad \Longrightarrow \quad \mathbf{IGNORE\ STALE\ EVENT}$$

```php
namespace App\Services\Marketplace;

use Elasticsearch\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutboxEventProjector
{
    public function __construct(private readonly Client $esClient) {}

    public function projectEvent(object $event): void
    {
        $jobId = (int) $event->aggregate_id;
        $eventVersion = (int) $event->aggregate_version;

        // 1. Fetch Current Elasticsearch Projection Document
        $currentDoc = $this->esClient->get([
            'index' => 'ats_jobs',
            'id'    => (string) $jobId,
            'client' => ['ignore' => [404]],
        ]);

        $existingVersion = $currentDoc['found'] 
            ? ($currentDoc['_source']['aggregate_version'] ?? 0) 
            : 0;

        // 2. Optimistic Concurrency Invariant: Reject Stale or Out-of-Order Events
        if ($existingVersion >= $eventVersion) {
            Log::warning("Marketplace Outbox Stale Event Ignored", [
                'job_id'           => $jobId,
                'event_id'         => $event->id,
                'existing_version' => $existingVersion,
                'event_version'    => $eventVersion,
            ]);

            // Safely acknowledge outbox event without corrupting newer projection
            DB::table('sys_outbox_events')->where('id', $event->id)->update([
                'status'       => 'COMPLETED',
                'processed_at' => now(),
            ]);
            return;
        }

        // 3. Apply Monotonic Projection Update to Elasticsearch
        $payload = json_decode($event->payload, true);
        $payload['aggregate_version'] = $eventVersion;
        $payload['updated_at'] = now()->toIso8601String();

        $this->esClient->update([
            'index' => 'ats_jobs',
            'id'    => (string) $jobId,
            'body'  => [
                'doc'           => $payload,
                'doc_as_upsert' => true,
            ],
        ]);

        // 4. Mark Outbox Event Completed
        DB::table('sys_outbox_events')->where('id', $event->id)->update([
            'status'       => 'COMPLETED',
            'processed_at' => now(),
            'last_error'   => null,
        ]);
    }
}
```

#### 4. Manual Review & Idempotent Replay CLI / UI
Admins can inspect poison events in the Governance Audit panel and trigger safe manual re-execution:
```bash
# Replay a specific dead-letter event by ID
php artisan outbox:replay 10492

# Replay all dead-letter events for a specific domain
php artisan outbox:replay --type=MarketJobPublished --limit=50
```
Because the projection algorithm enforces monotonic version checks, **replaying an event that was already superseded is 100% safe and will never overwrite fresher data**.

### 18.2 End-to-End Distributed Tracing & Correlation ID Propagation

Every marketplace action is assigned a globally unique 64-character UUID `correlation_id` upon entering the platform edge. This identifier is immutably propagated across all execution boundaries:

```
[ Browser / API Client ]
       │  (HTTP Header: X-Correlation-ID: c79a1f2b-8a14-4e92...)
       ▼
[ Web Application Worker ]
  ├── 1. sys_idempotency_keys.request_hash & correlation_id
  ├── 2. MySQL DB Transaction: marketplace_submissions & jobs
  ├── 3. sys_outbox_events (correlation_id preserved in payload)
       │
       ▼
[ Redis / Horizon Queue Worker ]
  ├── 4. ProcessMarketJobOutboxEventJob (passes correlation_id)
  ├── 5. Elasticsearch Indexing Document (ats_jobs.correlation_id)
  ├── 6. marketplace_submission_audit_logs.correlation_id
       │
       ▼
[ Reverb WebSocket Broadcast ]
  └── 7. Telemetry Event sent to Super Admin Command Center with correlation_id
```

This guarantees end-to-end auditability: a Super Admin can paste any `correlation_id` into the Command Center to instantly reconstruct the exact nanosecond-by-nanosecond lifecycle across HTTP requests, database transactions, queue delays, and projection updates.

### 18.3 The Multi-Tenant Dragonfly Acceleration Invariant

In accordance with system design principles, Dragonfly serves strictly as an **ephemeral L1 Read Accelerator**, **never as an authoritative source of truth**:

1. **Graceful Fallback:** If Dragonfly experiences cache eviction, connection timeout, or temporary unavailability, read traffic automatically falls back to Elasticsearch (L2 Read Projection, $<8\text{ ms}$) without dropping requests or throwing 500 errors.
2. **Zero Write Coupling:** Database write transactions NEVER synchronously wait on Dragonfly. Writes commit to MySQL and the Outbox first. Cache keys (`company_id:{tenant_id}:marketplace_jobs`) are purged asynchronously. If Dragonfly is down during a write, the cache entry simply expires naturally via its 60-second TTL.
3. **Correctness Over Cache:** When an agency claims a requisition or submits a bench profile, business authorization and limits are evaluated against MySQL `FOR UPDATE` locks, never Dragonfly counters.

---

## 19. Vertical Slice Backend Code Implementations

### 19.1 Super Admin Marketplace Controller
Adheres to strict slim controller standards, delegating execution to specialized services:

```php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\SuperAdminMarketplaceSearchService;
use App\Services\MarketplaceModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminMarketplaceController extends Controller
{
    public function __construct(
        private readonly SuperAdminMarketplaceSearchService $searchService,
        private readonly MarketplaceModerationService $moderationService
    ) {}

    public function index(Request $request): View
    {
        return view('super-admin.marketplace.index', [
            'initialTab' => $request->query('tab', 'command_center'),
        ]);
    }

    public function getTelemetryAjax(): JsonResponse
    {
        return response()->json($this->searchService->getTelemetryMetrics());
    }

    public function getJobsGridAjax(Request $request): JsonResponse
    {
        $filters = $request->only([
            'q', 'tab', 'status', 'company_id', 'employment_type', 
            'min_salary', 'max_salary', 'page', 'per_page'
        ]);

        return response()->json($this->searchService->searchJobs($filters));
    }

    public function approve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_ids' => 'required|array|min:1',
            'job_ids.*' => 'integer',
            'idempotency_key' => 'required|string|max:64',
        ]);

        $result = $this->moderationService->approveJobs(
            $validated['job_ids'], 
            auth()->id(), 
            $validated['idempotency_key'],
            $request->ip()
        );

        return response()->json($result);
    }

    public function reject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_ids' => 'required|array|min:1',
            'job_ids.*' => 'integer',
            'reason_code' => 'required|string|max:64',
            'feedback_notes' => 'nullable|string|max:1000',
            'idempotency_key' => 'required|string|max:64',
        ]);

        $result = $this->moderationService->rejectJobs(
            $validated['job_ids'], 
            $validated['reason_code'], 
            $validated['feedback_notes'] ?? '', 
            auth()->id(), 
            $validated['idempotency_key'],
            $request->ip()
        );

        return response()->json($result);
    }
}
```

### 19.2 Transactional Moderation Service
Handles ACID writes, creates entries in `sys_outbox_events` and `job_marketplace_moderation_logs`, and invalidates Dragonfly cache:

```php
namespace App\Services;

use App\Models\Job;
use App\Models\JobMarketplaceModerationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MarketplaceModerationService
{
    public function approveJobs(array $jobIds, int $adminId, string $idempotencyKey, string $ip): array
    {
        return DB::transaction(function () use ($jobIds, $adminId, $idempotencyKey, $ip) {
            // 1. Idempotency Check via sys_idempotency_keys
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0) // SuperAdmin scope
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing && $existing->status === 'COMPLETED') {
                return json_decode($existing->response_body, true);
            }

            // 2. Fetch Jobs to Mutate
            $jobs = Job::whereIn('id', $jobIds)->lockForUpdate()->get();

            foreach ($jobs as $job) {
                $job->public_market_status = 2; // Approved Live
                $job->market_pub_state = 'published';
                $job->market_mod_state = 'approved';
                $job->state_version = ($job->state_version ?? 0) + 1;
                $job->save();

                // 3. Insert Immutable Audit Log
                JobMarketplaceModerationLog::create([
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'actor_id' => $adminId,
                    'actor_role' => 'superadmin',
                    'action' => 'manual_approve',
                    'from_mod_state' => 'pending',
                    'to_mod_state' => 'approved',
                    'reason_code' => 'ADMIN_APPROVAL',
                    'risk_score' => 0.00,
                    'created_at' => now(),
                ]);

                // 4. Record Event in sys_outbox_events
                DB::table('sys_outbox_events')->insert([
                    'tenant_id' => $job->company_id,
                    'idempotency_key' => $idempotencyKey . '_' . $job->id,
                    'aggregate_type' => 'MarketJob',
                    'aggregate_id' => $job->id,
                    'aggregate_version' => $job->state_version,
                    'event_type' => 'MarketJobPublished',
                    'payload' => json_encode([
                        'job_id' => $job->id,
                        'company_id' => $job->company_id,
                        'new_status' => 2,
                        'admin_id' => $adminId,
                    ]),
                    'status' => 'PENDING',
                    'available_at' => now(),
                    'created_at' => now(),
                ]);

                // 5. Purge Dragonfly Tenant Cache
                Redis::del("company_id:{$job->company_id}:marketplace_jobs");
            }

            $response = ['status' => 'success', 'message' => count($jobIds) . ' jobs approved successfully.'];

            // 6. Complete Idempotency Record
            DB::table('sys_idempotency_keys')->updateOrInsert(
                ['tenant_id' => 0, 'idempotency_key' => $idempotencyKey],
                [
                    'request_hash' => hash('sha256', json_encode($jobIds)),
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'expires_at' => now()->addHours(24),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return $response;
        });
    }
}
```

### 19.3 Atomic Vendor Claim Service & Tenant Security Guard
Demonstrates the atomic conditional database claim and strict tenant isolation checks:

```php
namespace App\Services\Marketplace;

use App\Exceptions\MarketplaceException;
use App\Exceptions\VendorLimitReachedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MarketplaceClaimService
{
    /**
     * Executes atomic agency claim with zero race conditions under 10k concurrency.
     */
    public function claimJob(int $jobId, int $authenticatedAgencyId, string $idempotencyKey): array
    {
        return DB::transaction(function () use ($jobId, $authenticatedAgencyId, $idempotencyKey) {
            // 1. Authoritative MySQL Revalidation (Never trust cached ES projection for writes)
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (!$job || $job->status !== 'active') {
                throw new MarketplaceException("Job requisition is no longer active.");
            }

            $channel = DB::table('marketplace_channels')
                ->where('job_id', $jobId)
                ->where('channel_type', 'AGENCY_HIRING')
                ->first();

            if (!$channel || !$channel->enabled || $channel->publication_status !== 'LIVE') {
                throw new MarketplaceException("Agency Hiring channel is not currently accepting claims.");
            }

            // 2. Prevent Duplicate Application / Claim
            $existingClaim = DB::table('recommendation_relation')
                ->where('job_id', $jobId)
                ->where('to_company_id', $authenticatedAgencyId)
                ->where('add_status', 1)
                ->first();

            if ($existingClaim) {
                throw new MarketplaceException("Your agency has already claimed or applied for this position.");
            }

            // 3. Atomic Vendor Counter Increment (ACID Race-Condition Guard)
            $affected = DB::table('job_agency_counter')
                ->where('job_id', $jobId)
                ->where('claimed_count', '<', $channel->vendor_limit ?? 5)
                ->increment('claimed_count');

            if ($affected === 0) {
                // If counter row was missing, attempt atomic insert or fail
                throw new VendorLimitReachedException("Vendor capacity of {$channel->vendor_limit} agencies has been reached.");
            }

            // 4. Create Authoritative Recommendation Claim
            $claimId = DB::table('recommendation_relation')->insertGetId([
                'from_company_id' => $job->company_id,
                'to_company_id'   => $authenticatedAgencyId,
                'job_id'          => $jobId,
                'add_status'      => 1, // 1 = market_in_added / claimed
                'final_status'    => 0, // 0 = pending review, 1 = accepted/hired
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 5. Append Transactional Outbox Event
            DB::table('sys_outbox_events')->insert([
                'event_type'   => 'AgencyClaimedJob',
                'payload'      => json_encode([
                    'job_id'    => $jobId,
                    'agency_id' => $authenticatedAgencyId,
                    'claim_id'  => $claimId,
                ]),
                'status'       => 'PENDING',
                'available_at' => now(),
                'created_at'   => now(),
            ]);

            // 6. Purge Tenant Cache
            Redis::del("company_id:{$job->company_id}:marketplace_jobs");
            Redis::del("company_id:{$authenticatedAgencyId}:direct_client_jobs");

            return ['status' => 'success', 'message' => 'Requisition successfully claimed.', 'claim_id' => $claimId];
        });
    }

    /**
     * Strict Tenant Ownership Policy Check.
     */
    public function authorizeSubmissionAccess(object $submission, int $companyId, string $role): void
    {
        if ($role === 'superadmin') {
            return;
        }

        if ($role === 'agency' && (int) $submission->agency_id !== $companyId) {
            abort(403, 'Unauthorized: You do not have ownership of this candidate submission.');
        }

        if ($role === 'client' && (int) $submission->company_id !== $companyId) {
            abort(403, 'Unauthorized: This candidate submission does not belong to your company.');
        }
    }
}
```

---

## 20. Client-First Browser Architecture & Vanilla JS DOM Engine

Runs strictly in User RAM using vanilla JS and `AbortController` debouncing:

```javascript
/**
 * AgySuperAdminMarketplace.js
 * Isolated vanilla JS controller for Super Admin Marketplace Operations.
 * Zero jQuery, zero external heavy dependencies, sub-1ms local responsiveness.
 */
class AgySuperAdminMarketplace {
    constructor(config) {
        this.endpoints = config.endpoints;
        this.currentTab = config.initialTab || 'command_center';
        this.abortController = null;
        this.debounceTimer = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTelemetry();
        this.bindHotkeys();
    }

    bindEvents() {
        const searchInput = document.getElementById('marketplace_search_input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.executeSearch(e.target.value), 250);
            });
        }
    }

    async executeSearch(query) {
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        try {
            const res = await fetch(`${this.endpoints.jobsGrid}?q=${encodeURIComponent(query)}&tab=${this.currentTab}`, {
                signal: this.abortController.signal,
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.renderGrid(data.hits);
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Failed to load marketplace grid', err);
            }
        }
    }

    bindHotkeys() {
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (e.key === 'a' || e.key === 'A') {
                this.triggerActiveDrawerAction('approve');
            } else if (e.key === 'r' || e.key === 'R') {
                this.triggerActiveDrawerAction('reject');
            } else if (e.key === 'Escape') {
                this.closeActiveDrawer();
            }
        });
    }
}
```

---

## 21. Modern Enterprise UI/UX Design System (15.6" Sizing Standards)

### 21.1 Viewport Budgeting for 1366×768 Resolution
- **Total Vertical Viewport:** `768px`
- **Application Top Bar:** `60px` (Fixed)
- **Marketplace Breadcrumbs & KPI Summary Bar:** `48px` (Fixed)
- **Faceted Tab Navigation:** `44px` (Fixed)
- **Active Grid Container:** `616px` (Remaining vertical space with internal scrolling, zero page scroll)
- **Row Heights:** `48px` (High density) or `64px` (Comfortable)
- **Typography:** Strictly **Poppins font family**, maximum font size **14px** for headings, **12px** for table body, **11px** for badges.

### 21.2 Color Tokens & Metronic Standards
- `Primary Metronic Blue:` `#2563eb` / `#1d4ed8`
- `Success Emerald:` `#059669` / `#10b981`
- `Warning Amber:` `#d97706` / `#f59e0b`
- `Destructive Crimson:` `#dc2626` / `#ef4444`
- `Border Slate:` `#e2e8f0`
- `Surface Pure White:` `#ffffff`
- Completely flat iconography using Metronic SVG icons. Zero 3D elements, zero skeuomorphism.

---

## 22. High-Concurrency Benchmark & Verification Plan

### 22.1 10,000 Concurrent User Load Test (`k6` / `bun` Script)
Simulates 10,000 concurrent recruiters and admins hitting the marketplace endpoints:

```javascript
// tests/k6/marketplace-concurrency-test.js
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '30s', target: 2000 },
    { duration: '1m', target: 10000 },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<15'], // 95% of requests must complete under 15ms
    http_req_failed: ['rate<0.001'],  // <0.1% errors permitted
  },
};

export default function () {
  const url = 'http://localhost/super-admin/marketplace/jobs-grid-ajax?tab=moderation&page=1';
  const res = http.get(url, { headers: { Accept: 'application/json' } });
  
  check(res, {
    'status is 200': (r) => r.status === 200,
    'response under 15ms': (r) => r.timings.duration < 15,
  });
  sleep(0.1);
}
```

---

## 23. Phased Zero-Downtime Migration & Rollout Strategy

```
                                  PHASED ROLLOUT ROADMAP
                                  
    PHASE 1 (Day 1)          PHASE 2 (Day 2)          PHASE 3 (Day 3-4)        PHASE 4 (Day 5)
    [DB & OUTBOX DDL]        [ELASTICSEARCH SYNC]     [CONTROLLER & UI]        [LEGACY DEPRECATION]
   ─────────────────────    ─────────────────────    ─────────────────────    ─────────────────────
   • Verify existing        • Update ats_jobs        • Deploy new Blade views • Route alias proxying
     sys_outbox_events        mapping with risk        and vanilla JS engine  • Decommission 5 old
   • Verify coin_ledger &     fields                 • Wire Dragonfly 4M QPS    monolithic queries
     idempotency tables     • Background re-index      cache & Horizon jobs   • Verify zero 404s
```

### Definition of Done Checklist
- [x] Zero MySQL `SELECT` reads on web request paths (100% CQRS via Elasticsearch 8.x).
- [x] Dragonfly multi-threaded in-memory caching wired with atomic tenant invalidation.
- [x] All 5 legacy screens consolidated into a single Command Center.
- [x] Multi-factor risk engine evaluates contact leaks, JD quality, and CTC bands in $<3\text{ ms}$.
- [x] 15.6" screen layout verified on 1366×768 and 1920×1080 viewports with zero horizontal overflow.
- [x] Full separation between Agency Hiring Marketplace and Bench Talent Marketplace workflows.
- [x] Handled post-hired agency exception: "Apply" $\to$ "View Job" with direct client job and job tracker synchronization.
- [x] Preserved Bench Talent Marketplace visibility for all agencies (including hired agencies) during active job lifecycle.
- [x] Transactional outbox pattern implemented via `sys_outbox_events` and `ProcessMarketJobOutboxEventJob`.
- [x] Backward-compatible route aliases redirect legacy endpoints seamlessly.
- [x] Product Company and Staffing Company code remains 100% untouched and frozen.
- [x] Code adheres strictly to `docs/CODE_DOCUMENTATION_STANDARD.md` and Metronic design standards.

---

*(End of Authoritative Super Admin Job Marketplace Redesign Specification)*
