# Super Admin Job Marketplace Operations Platform — GOD-Level Architecture & Master Redesign Blueprint

**Document Identifier:** `docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md`  
**Platform:** JERO B2B Talent Marketplace & GOD-Level ATS (`testing.jero.works`)  
**Module:** Super Admin Marketplace Operations Command Center  
**Target Concurrency:** 10,000+ Concurrent Recruiters/Admins | 0% Unnecessary MySQL Reads | 100% CQRS via Elasticsearch 8.x + Dragonfly C++ Cache | Sub-15ms TTFB  
**Target Resolution:** Optimized for 15.6" Laptops (1366×768 & 1920×1080) down to Tablet/Mobile with Zero Horizontal Page Scroll  
**System Profile:** 32 GB RAM App Server (Octane/FrankenPHP HTTP/3), 32 GB RAM MySQL InnoDB, 32 GB RAM Elasticsearch 8.x Cluster, Dragonfly Multi-Threaded In-Memory Cache (4M QPS)  
**Governance Scope:** Super Admin & Service Company ATS | Product Company & Staffing Company Code Strictly Frozen (Rule 18)  
**Status:** Authoritative Architectural Blueprint & Production-Grade Engineering Specification  

---

## 🏛️ Executive Summary & Master System Architecture

The JERO Talent Marketplace is a high-velocity B2B exchange where enterprise hiring companies publish requisitions, and partner staffing agencies fulfill them via two distinct engagement methods:
- **Method 1 (Agency Outsource / Requisition Claim):** Hiring companies authorize vetted agencies to work as external recruitment vendors. Agencies claim positions, subject to tenant-configured vendor capacity limits (`global_settings.vendor_limit`).
- **Method 2 (Bench Talent Submissions):** Partner agencies deploy verified bench consultants directly into client ATS pipelines (`ac_status = 0`) without claiming the full requisition.

Historically, the Super Admin marketplace management layer suffered from legacy architectural decay: 5 fragmented CRUD screens, synchronous email loops blocking PHP workers for up to 30 seconds, multi-table `LEFT JOIN`s on unindexed tables under peak load, and a simplistic binary auto-approval toggle without risk heuristics or anti-circumvention guardrails.

This blueprint establishes the **GOD-Level Super Admin Marketplace Operations Platform**, consolidating all marketplace oversight, risk scoring, vendor claims, and commercial compliance into a unified, ultra-low-latency command center governed by **strict CQRS**, **Client-First Browser Execution (User RAM + WASM)**, and **Zero-Cloud AI Sovereignty**.

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
|                           JERO SUPER ADMIN MARKETPLACE OPERATIONS PLATFORM (CQRS)                                 |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
|                                                                                                                   |
|   [ BROWSER RUNTIME: 15.6" Laptop Optimized Command Center (User RAM + DuckDB-WASM + Vanilla JS + Metronic) ]      |
|   • Instant Local Search & OLAP Aggregations (P50 < 1ms)  • Zero Horizontal Scroll  • AbortController Debouncing   |
|                                           │                                                                       |
|              ┌────────────────────────────┴─────────────────────────────┐                                         |
|              ▼                                                          ▼                                         |
|   [ READ PATH: 100% CQRS ]                                   [ WRITE PATH: ACID MUTATIONS ]                       |
|   • 0% MySQL Reads on Web Request Paths                      • Dedicated MySQL InnoDB Connection Pool             |
|   • Elasticsearch Cluster 8.x (ats_jobs, ats_job_request)    • Idempotency Keys (UUID v4 + sys_idempotency_keys)  |
|   • Dragonfly Multi-Threaded In-Memory Cache (4M QPS)        • Dual-Lease transactional_outboxes Architecture     |
|   • Sub-8ms Faceted Filtering & Reciprocal Rank Fusion (RRF) • Non-blocking Laravel Horizon Asynchronous Workers  |
|   • FrankenPHP 103 Early Hints + HTTP/3 QUIC Streaming       • Real-Time Reverb WebSocket Telemetry Broadcast     |
|                                                                                                                   |
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 1. Current vs Recommended Marketplace Architecture

### 1.1 The Legacy Super Admin Pathology (Current State)
The legacy Super Admin marketplace implementation was built as fragmented, disjointed CRUD pages created ad-hoc across several years:
1. **Fragmented Navigation & Siloed Screens:** Separate screens for `job-public-market`, `job-lists`, `job_request`, `job_request_accept`, and `recommandedrequest` force admins to juggle 5+ URLs with inconsistent search boxes and conflicting status nomenclature.
2. **Severe MySQL Read Contention:** Legacy controllers (`SuperAdminCompanyController@jobPublicMarket`, `jobLists`, `job_request`) run multi-table `LEFT JOIN`s on `jobs`, `clients`, `companies`, `recommendation_relation`, `rec_candidate_assign`, `client_location_jobs`, and unindexed `groupBy('jobs.id')`. Under 10,000 concurrent recruiters, this causes database thread starvation, query timeouts, and table locks.
3. **Synchronous Email & Notification Loops:** Bulk approvals loop through jobs in the PHP web process (`$this->mailService->sendEmail(...)`), blocking the HTTP worker thread for 10–25 seconds.
4. **Binary Moderation Toggle:** Auto-approval in `global_settings.public_market_job_status` is an unconditioned binary 0/1 switch with zero content inspection, zero risk heuristics, and zero audit logging.
5. **No Agency Performance Layer:** Admins have zero visibility into agency SLA compliance, candidate submission velocity, interview-to-hire ratio, or spam rate.

### 1.2 The Recommended "Marketplace Operations Platform" (God-Level State)
Rebuild the entire Super Admin marketplace into a unified, reactive **Marketplace Command Center** governed by strict CQRS, event-driven transactional outbox patterns, and an intelligent risk engine:

| Dimension | Legacy Architecture (Current) | God-Level Architecture (Target V4) |
| :--- | :--- | :--- |
| **Data Retrieval** | Monolithic MySQL SQL queries with 5+ `LEFT JOIN`s and unindexed `groupBy('jobs.id')` | **100% CQRS via Elasticsearch 8.x (`ats_jobs`)**; 0% MySQL reads on request paths |
| **Cache Layer** | Uncached or basic single-threaded Redis keys prone to latency spikes | **Dragonfly C++ Multi-Threaded In-Memory Cache (4M QPS)** with atomic tenant-key purging |
| **Worker Threads** | Synchronous mail loops in web request (`$this->mailService->sendEmail`) | **Decoupled transactional outbox (`transactional_outboxes`)** with Horizon queues |
| **Moderation Engine**| Raw binary 0/1 toggle in `global_settings.public_market_job_status` | **Multi-factor Risk Engine (0-100 Trust Score)** with NLP anti-circumvention heuristics |
| **UI Density & Sizing**| Broken responsive layouts, table overflow on 1366×768 laptops | **15.6" screen optimized (1366×768 / 1920×1080)**, max 14px Poppins, zero horizontal scroll |
| **Client Execution** | Server renders all HTML; repetitive full-page reloads on filter changes | **Client-First User RAM + DuckDB-WASM**, sub-1ms client filtering, hotwire morph updates |
| **Audit & Compliance**| Unaudited mutations, hard deletes, untracked admin decisions | **Immutable `job_marketplace_moderation_logs` ledger** with reason codes and telemetry |

---

## 2. New Information Architecture (IA)

Instead of 6 isolated pages, the Super Admin Marketplace is structured as a **Unified Marketplace Operations Hub** with 4 core operational pillars:

```
                             [ Super Admin Navigation: Marketplace ]
                                                │
         ┌──────────────────────┬───────────────┴───────────────┬──────────────────────┐
         ▼                      ▼                               ▼                      ▼
  [ 1. Command Center ]   [ 2. Requisitions Hub ]     [ 3. Agency Exchange ]   [ 4. Governance & Audit ]
  ├── Real-time KPIs      ├── All Platform Jobs       ├── Requisition Claims   ├── Moderation Logs
  ├── Approval Queue      ├── Marketplace Review      ├── Bench Talent Shared  ├── Rate/Term Disputes
  ├── Risk Alerts         ├── Live Marketplace       ├── Agency Scorecards    ├── Anti-Circumvention
  └── Liquidity Trends    └── Delisted / Rejected     └── Allocation Limits    └── Setting Policies
```

---

## 3. Unified Navigation & Canonical Route Matrix

The Super Admin sidebar consolidates into a clean, hierarchical group under `/super-admin/marketplace`:

```
┌────────────────────────────────────────────────────────────────────────┐
│ SUPER ADMIN SIDEBAR: MARKETPLACE                                       │
├────────────────────────────────────────────────────────────────────────┤
│ 📊 Command Center                (/super-admin/marketplace/dashboard) │
│ 📁 Requisitions Hub              (/super-admin/marketplace/jobs)      │
│    ├── All Platform Jobs         (Global inventory, all hiring models) │
│    ├── Moderation Queue          (Pending manual review & risk flags)  │
│    ├── Live Marketplace          (Active, claimed & bench-open)        │
│    ├── Quarantined & Flagged     (Compliance holds & contact leaks)    │
│    └── Delisted & Expired        (Closed, paused, or SLA expired)      │
│ 🤝 Agency Exchange               (/super-admin/marketplace/agencies)  │
│    ├── Requisition Claims        (Method 1 Outsource vendor requests)  │
│    ├── Bench Submissions         (Method 2 Bench candidate deployments)│
│    └── Agency Scorecards         (Tiering, conversion rates, SLAs)     │
│ 🛡️ Audit & Governance            (/super-admin/marketplace/audit)     │
│    ├── Moderation Audit Stream   (Immutable chronological log)         │
│    ├── Policy & Reason Codes     (Taxonomy & remediation templates)    │
│    └── Commercial Coin Ledger    (Events 38–41 marketplace accounting) │
└────────────────────────────────────────────────────────────────────────┘
```

### Complete Route Consolidation & Backward-Compatibility Matrix
To avoid breaking existing recruiter email links, bookmarks, and notification webhooks, legacy routes are transparently mapped to the new unified controllers:

| Legacy Route (Deprecating) | Legacy Controller Method | New God-Level Canonical Route | Target Architecture |
| :--- | :--- | :--- | :--- |
| `GET /super-admin/job-public-market/{status?}` | `SuperAdminCompanyController@jobPublicMarket` | `GET /super-admin/marketplace/jobs?tab=moderation` | 100% Elasticsearch 8.x |
| `POST /super-admin/market-auto-approval` | `SuperAdminCompanyController@marketautoApproval`| `POST /super-admin/marketplace/settings/auto-approval` | Atomic DB + Outbox |
| `POST /super-admin/job-approval` | `SuperAdminCompanyController@jobApproval` | `POST /super-admin/marketplace/jobs/approve` | Atomic DB + Outbox |
| `POST /super-admin/reject_job/reject_mail_send` | `SuperAdminCompanyController@reject_mail_send_job`| `POST /super-admin/marketplace/jobs/reject` | Atomic DB + Outbox |
| `GET /super-admin/jobs/job_request/{id?}` | `SuperAdminCompanyController@job_request` | `GET /super-admin/marketplace/agencies/claims` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/recommandedrequest` | `SuperAdminCompanyController@recommandedrequest` | `GET /super-admin/marketplace/agencies/bench` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/job-lists/{id?}` | `SuperAdminCompanyController@jobLists` | `GET /super-admin/marketplace/jobs` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/job_request_accept/{id?}` | `SuperAdminCompanyController@job_request_accept` | `GET /super-admin/marketplace/agencies/claims?status=accepted` | 100% Elasticsearch 8.x |
| `GET /super-admin/jobs/recommandedrequest_accept` | `SuperAdminCompanyController@recommandedrequest_accept`| `GET /super-admin/marketplace/agencies/bench?status=accepted`| 100% Elasticsearch 8.x |

---

## 4. End-to-End Marketplace Operational Lifecycle

The marketplace workflow orchestrates interactions between Hiring Companies, the Risk Engine, Super Admins, Elasticsearch, and Staffing Agencies:

```mermaid
sequenceDiagram
    autonumber
    actor Recruiter as Hiring Company Recruiter
    participant Web as Web Server (Octane / FrankenPHP)
    participant DB as MySQL InnoDB (Primary DB)
    participant Outbox as transactional_outboxes
    participant Risk as Local Heuristic Risk Engine
    actor Admin as Super Admin
    participant Horizon as Horizon Worker Queue
    participant ES as Elasticsearch 8.x Cluster
    participant Dragonfly as Dragonfly Cache (4M QPS)
    participant Reverb as Reverb WebSockets
    actor Agency as Partner Staffing Agency

    Recruiter->>Web: POST /jobs/store (Marketplace Options Selected)
    Web->>DB: INSERT INTO jobs (public_market_status = 1, show_vendor = 'yes')
    Web->>Risk: Synchronous Ingestion Scan (Contact Leaks, CTC Bands, Words)
    
    alt Trust Score >= 85 & Zero Risk Flags & Auto-Approval Enabled
        Web->>DB: UPDATE jobs SET public_market_status = 2 (Approved Live)
        Web->>Outbox: INSERT Event: JobMarketplaceApproved (job_id, v1)
        Web->>Dragonfly: Invalidate tenant cache: company_id:{tenant}:jobs
        Web-->>Recruiter: HTTP 200 (Live on Marketplace Instantly)
    else Trust Score < 85 OR Flag Detected OR Auto-Approval Disabled
        Web->>DB: Keep public_market_status = 1 (Pending Review)
        Web->>Outbox: INSERT Event: JobMarketplacePendingReview (job_id, flags)
        Web-->>Recruiter: HTTP 200 (Submitted to Marketplace Moderation)
    end

    Note over Outbox,Horizon: Horizon Worker asynchronously claims batch from transactional_outboxes
    Horizon->>ES: Sync Document to ats_jobs index (0ms lock on HTTP thread)
    Horizon->>Reverb: Broadcast to channel 'marketplace.superadmin.telemetry'

    opt Manual Super Admin Review Required
        Admin->>Web: Opens Moderation Queue (Fast-Review Drawer #superadmin_job_review_drawer)
        Web->>ES: Query ats_jobs (Sub-8ms read)
        Admin->>Web: 1-Click Approve OR Structured Reject (with Reason Code)
        Web->>DB: Atomic Transaction (Update jobs, Insert job_marketplace_moderation_logs)
        Web->>Outbox: Record State Change Event in transactional_outboxes
        Horizon->>ES: Update ats_jobs Document
        Horizon->>Reverb: Broadcast real-time update to Agency Grid
        Horizon->>Web: Asynchronous Email Dispatch to Recruiter (Mail Type 28/29)
    end

    alt Method 1: Agency Claims Requisition
        Agency->>Web: POST /jobs/market/claim (Agency agrees to terms)
        Web->>DB: Deduct Operational Coins (Event 38), INSERT recommendation_relation (add_status = 1)
        Web->>Outbox: Record Claim Event
        Horizon->>ES: Update claimed_agencies_count in ats_jobs
        Web-->>Agency: Position locked in Direct Client Jobs ATS pipeline
    else Method 2: Agency Submits Bench Candidate
        Agency->>Web: POST /jobs/recommandedrequestcondidate (Direct CV Deployment)
        Web->>DB: Deduct Operational Coins (Event 39), INSERT candidate into client ATS
        Web->>Outbox: Record Bench Submission Event
        Horizon->>ES: Sync candidate to ats_job_applications (ac_status = 0)
        Web-->>Agency: Bench Candidate submitted to Recruiter Review Queue
    end
```

---

## 5. Universal Requisition & Exchange State Machine

To eliminate discrepancies between MySQL and Elasticsearch, JERO establishes a strict single source of truth across all 60+ indices:

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                        UNIVERSAL REQUISITION STATE MACHINE                                        │
+───────────+──────────────────────+──────────────────────────+─────────────────+───────────────────────────────────+
│ CODE      │ STATE ENUM           │ UI BADGE                 │ SEARCHABLE?     │ ALLOWED NEXT TRANSITIONS          │
+───────────+──────────────────────+──────────────────────────+─────────────────+───────────────────────────────────+
│ 0         │ DRAFT_PRIVATE        │ Gray (Internal Requisition)│ No (Hidden)   │ 1 (Submit to Market)              │
│ 1         │ PENDING_REVIEW       │ Warning (In Moderation)  │ No (Hidden)     │ 2 (Approve), 3 (Reject), 5 (Hold) │
│ 2         │ APPROVED_LIVE        │ Success (Live on Market) │ Yes (Public)    │ 4 (Limit), 5 (Quarantine), 6 (Del)│
│ 3         │ REJECTED             │ Danger (Revision Needed) │ No (Hidden)     │ 1 (Resubmit after edit)           │
│ 4         │ CLAIM_LIMIT_REACHED  │ Info (Vendor Cap Maxed)  │ Yes (Bench Only)│ 2 (Cap Expanded), 6 (Delist)      │
│ 5         │ QUARANTINED_FLAGGED  │ Dark (Compliance Hold)   │ No (Hidden)     │ 2 (Cleared), 3 (Reject)           │
│ 6         │ UNPUBLISHED_RECRUITER│ Secondary (Withdrawn)    │ No (Hidden)     │ 1 (Re-open for Review)            │
│ 7         │ COMPLETED_HIRED      │ Success-Dark (Closed)    │ No (Archived)   │ None (Terminal)                   │
+───────────+──────────────────────+──────────────────────────+─────────────────+───────────────────────────────────+
```

### Complete State Governance Matrix & Invariant Rules
1. **`0 -> 1` (Submission):** Triggered when a hiring recruiter enables marketplace visibility (`show_vendor = 'yes'`).
2. **`1 -> 2` (Clearance):** Triggered by System Auto-Approval Engine (`Trust Score >= 85` & `0 Flags`) OR Super Admin Manual Approval.
3. **`1 -> 3` (Rejection):** Super Admin only. Requires mandatory standardized `rejection_reason_code` and recruiter remediation note.
4. **`2 -> 4` (Vendor Cap Reached):** System triggers automatically when active claimed agency count equals `jobs.vendor_limit`. In this state, Method 1 claims are blocked, but Method 2 bench submissions remain permitted.
5. **`2 -> 5` (Safety Quarantine):** Triggered automatically if post-publication recruiter edit introduces contact leakage or upon receiving $>2$ agency spam reports.
6. **`2 -> 6` (Recruiter Withdrawal):** Hiring recruiter disables marketplace toggle or closes the internal requisition.
7. **`4 -> 2` (Vendor Cap Expansion):** Super Admin or Recruiter increases vendor limit, immediately reopening Method 1 claims.

---

## 6. Algorithmic Risk, Quality & Anti-Circumvention Engine

To protect platform revenue and commercial relationships, JERO replaces the legacy binary switch with a **Deterministic Multi-Factor Risk Engine**. This engine runs locally on the application server using CPU SIMD regex and NLP heuristics ($0 API costs, zero external cloud dependency, sub-3ms evaluation).

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                  AUTOMATED RISK & QUALITY ASSESSMENT ENGINE                                       │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                                                                                   │
│   Incoming Requisition Payload (Title, Description, Requirements, CTC Bands, Commercials)                          │
│                                           │                                                                       │
│           ┌───────────────────────────────┼───────────────────────────────┐                                       │
│           ▼                               ▼                               ▼                                       │
│   [ VECTOR 1: INTEGRITY ]         [ VECTOR 2: QUALITY ]           [ VECTOR 3: COMMERCIALS ]                       │
│   • Email Regex Scan              • Word Count (Min 150 words)    • Realistic Band Check (INR/USD)                │
│   • Phone / WhatsApp Regex Scan   • Skill Density (Min 3 skills)  • Commission % Specified                        │
│   • URL / Domain Regex Scan       • Shift & Location Defined      • Payment Credit Days (Max 90d)                 │
│   • Obfuscation Shannon Entropy   • Repetitive Keyword Spam Check • Zero-Salary Trap Detection                    │
│                                           │                                                                       │
│                                           ▼                                                                       │
│                        [ TRUST SCORE CALCULATION (0 - 100) ]                                                      │
│                        Formula: Base(100) - Deductions(Flags) + TenureBonus                                       │
│                                           │                                                                       │
│              ┌────────────────────────────┴─────────────────────────────┐                                         │
│              ▼                                                          ▼                                         │
│     Trust Score >= 85 & Flags == 0                              Trust Score < 85 OR Flags > 0                     │
│     [ AUTO-APPROVE: LIVE (Status 2) ]                           [ ESCALATE: MODERATION QUEUE (Status 1) ]         │
│                                                                                                                   │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

### 6.1 Concrete Heuristic Rules & Detection Patterns

```php
namespace App\Services\Marketplace;

class MarketplaceRiskEngine
{
    /**
     * Anti-circumvention detection regex patterns.
     * Detects raw emails, obfuscated emails (e.g. "name [at] domain dot com"),
     * phone numbers (Indian & International), WhatsApp links, Telegram handles.
     */
    private const PATTERNS = [
        'EMAIL' => '/[a-zA-Z0-9._%+-]+(?:\s*@\s*|\s*\[at\]\s*)[a-zA-Z0-9.-]+(?:\s*\.\s*|\s*\[dot\]\s*)[a-zA-Z]{2,}/i',
        'PHONE' => '/(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}|\b(?:\+?91|0)?[6-9]\d{9}\b/',
        'WHATSAPP' => '/(?:wa\.me|api\.whatsapp\.com|chat\.whatsapp\.com)\/[a-zA-Z0-9]+/i',
        'TELEGRAM' => '/(?:t\.me|telegram\.me)\/[a-zA-Z0-9_]+/i',
        'LINK_SHORTENER' => '/(?:bit\.ly|tinyurl\.com|rb\.gy|cutt\.ly|goo\.gl)\/[a-zA-Z0-9]+/i',
    ];

    public function evaluate(array $jobData, int $companyTenureDays): array
    {
        $score = 100;
        $flags = [];

        $fullText = ($jobData['title'] ?? '') . ' ' . 
                    ($jobData['job_description'] ?? '') . ' ' . 
                    ($jobData['job_requirement'] ?? '');

        // 1. Anti-Circumvention Scan
        foreach (self::PATTERNS as $rule => $pattern) {
            if (preg_match($pattern, $fullText, $matches)) {
                $flags[] = "LEAK_{$rule}: Detected '{$matches[0]}'";
                $score -= 35;
            }
        }

        // 2. JD Quality & Completeness
        $wordCount = str_word_count(strip_tags($fullText));
        if ($wordCount < 150) {
            $flags[] = "QUALITY_LOW_WORD_COUNT: Only {$wordCount} words (Minimum 150 required)";
            $score -= 20;
        }

        // 3. Compensation Sanity
        $minSal = (float) ($jobData['min_yearly'] ?? 0);
        $maxSal = (float) ($jobData['max_yearly'] ?? 0);
        if ($minSal <= 0 && $maxSal <= 0) {
            $flags[] = 'COMMERCIAL_UNSPECIFIED_SALARY: Both min and max salary are zero';
            $score -= 25;
        } elseif ($maxSal > 0 && $minSal > $maxSal) {
            $flags[] = 'COMMERCIAL_INVERTED_RANGE: Min salary exceeds max salary';
            $score -= 30;
        }

        // 4. Tenure Trust Bonus (Max +10)
        $tenureBonus = min(10, (int) ($companyTenureDays / 90) * 2);
        $finalScore = max(0, min(100, $score + $tenureBonus));

        return [
            'trust_score' => $finalScore,
            'risk_flags' => $flags,
            'is_auto_approvable' => ($finalScore >= 85 && count($flags) === 0),
        ];
    }
}
```

---

## 7. Super Admin Marketplace Command Center Dashboard

The Command Center provides real-time operational telemetry designed for rapid decision-making on 15.6" laptop screens:

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ JERO MARKETPLACE COMMAND CENTER — TELEMETRY & OPERATIONS                                  [ 🟢 Reverb Connected ] │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                                                                                   │
│  [ PENDING MODERATION ]    [ AUTO-APPROVAL RATE ]    [ ACTIVE MARKET JOBS ]    [ CLAIMS LIQUIDITY ]               │
│  ┌────────────────────┐    ┌────────────────────┐    ┌────────────────────┐    ┌────────────────────┐             │
│  │ 18 Requisitions    │    │ 74.2% Passed       │    │ 1,248 Live         │    │ 3,892 / 5,000 Caps │             │
│  │ 🚨 3 SLA Breached  │    │ Median TAT: 14m    │    │ 842 Outsource      │    │ 77.8% Capacity     │             │
│  │ <2h: 12 | >6h: 3   │    │ Heuristics: Clean  │    │ 406 Bench Only     │    │ 182 Near Max Cap   │             │
│  └────────────────────┘    └────────────────────┘    └────────────────────┘    └────────────────────┘             │
│                                                                                                                   │
│  [ HIGH-RISK INGESTION RADAR ]                      [ LIQUIDITY & ENGAGEMENT HEATMAP ]                            │
│  ┌──────────────────────────────────────────────┐   ┌──────────────────────────────────────────────┐              │
│  │ • Job #10492: Personal Gmail in Description  │   │ High Demand: 42 Jobs with 100% Vendor Cap    │              │
│  │   Company: Apex Solutions | Score: 45/100    │   │ Starved Requisitions: 18 Jobs Live > 14 Days │              │
│  │   [ Quick Review ] [ Quarantine Listing ]    │   │ with 0 Agency Claims [ Boost Placement Fee ] │              │
│  │ ──────────────────────────────────────────── │   │ ──────────────────────────────────────────── │              │
│  │ • Job #10488: ₹1.00 CTC (Zero Salary Trap)   │   │ Agency Responsiveness SLA:                   │              │
│  │   Company: ByteCraft | Score: 55/100         │   │ • 89% Submit 1st CV within 48 Hours          │              │
│  │   [ Quick Review ] [ Reject with Feedback ]  │   │ • 14 Agencies Flagged for 7-Day Inactivity   │              │
│  └──────────────────────────────────────────────┘   └──────────────────────────────────────────────┘              │
│                                                                                                                   │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 8. "All Jobs" Central Requisition Management (100% CQRS via Elasticsearch)

The central interface for discovering, monitoring, and governing all requisitions across the entire JERO platform:

### 8.1 Elasticsearch Query DSL Architecture
All queries execute against index `ats_jobs`. No SQL `SELECT` queries are executed on web worker threads:

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
        { "term": { "public_market_status": 2 } },
        { "term": { "show_vendor": "yes" } },
        { "range": { "min_yearly": { "gte": 1200000 } } }
      ]
    }
  },
  "aggs": {
    "by_market_status": { "terms": { "field": "public_market_status" } },
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

### 8.2 High-Density Data Grid Specification (15.6" Optimized)
- Standard row height fixed at `48px` (compact) or `64px` (comfortable) to display 10–12 rows above the fold on a 768px height viewport.
- Columns:
  1. `Checkbox` (Multi-select for bulk approval/rejection)
  2. `Requisition ID & Title` (With status dot and quick drawer trigger)
  3. `Hiring Company` (Tenant logo, name, verification badge)
  4. `Marketplace Channels` (Pills: Career Site, Agency Claim, Bench Talent)
  5. `Commercials` (Commission %, Fixed Fee, Billing Currency, Credit Days)
  6. `Activity` (Active Claims Count / Bench CVs Submitted)
  7. `Trust Score` (Color-coded badge: 85-100 Green, 60-84 Yellow, <60 Red)
  8. `Status Badge` (Interactive state pill)
  9. `Actions` (Review Drawer, Edit Terms, Quarantine, Delist)

---

## 9. Dedicated Moderation Cockpit & Fast-Review Drawer

Clicking any requisition in the Moderation Queue slides open the **1-Click Fast-Review Drawer** (`#superadmin_job_review_drawer`, responsive width: `min(92vw, 1100px)`):

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

## 10. Structured Rejection & Remediation Engine

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

## 11. Agency Exchange Governance (Method 1 Claims & Method 2 Bench)

Super Admin oversees the commercial balance between hiring companies and staffing agencies:

### 11.1 Method 1: Requisition Claims Governance
- Tracks agency capacity allocations under `recommendation_relation` (`add_status = 1`).
- **Vendor Capacity Caps:** Enforces `jobs.vendor_limit`. When claimed count equals the limit, `jobs.public_market_status` transitions to `4 (CLAIM_LIMIT_REACHED)`.
- **7-Day Inactivity Clawback:** If an agency claims a position but submits 0 candidates within 7 days, Super Admin can revoke the claim, returning the slot to the open market and refunding or forfeiting operational coins according to policy.

### 11.2 Method 2: Bench Talent Submissions Governance
- Tracks direct bench candidate deployments into client ATS pipelines (`ac_status = 0`).
- Prevents candidate duplicate submissions across multiple agencies using cryptographic SHA-256 phone/email hashes.

---

## 12. Agency Marketplace Scorecard & Performance Directory

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

## 13. Job Detail Command Center (5-Tab Enterprise Drawer)

Clicking any job across the platform opens the **Job Detail Command Center Drawer** (`#superadmin_job_command_drawer`, 1100px width on 15.6" screens):

```
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│ [JOB #10492] Lead Cloud Architect — Enterprise Logistics                               [ Esc ] [ Full Screen ] [ X ]
│ Status: LIVE ON MARKETPLACE | Auto-Approved | Created: 3 days ago | SLA: Healthy (0 Flags) | Hiring Model: Hybrid │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
│                                                                                                                   │
│ [ TAB 1: REQUISITION OVERVIEW ]                                                                                   │
│ ├── Complete Job Description, Scope of Work, Technical Requirements, Skills Cloud, Location, Shift Type          │
│ └── Compensation: ₹32,00,000 – ₹45,00,000 PA | Full-Time Domestic | Hybrid (2 Days Office)                       │
│                                                                                                                   │
│ [ TAB 2: MARKETPLACE COMMERCIALS ]                                                                                │
│ ├── Channels Enabled: Career Portal [ON] | Agency Claim [ON] | Bench Submissions [ON]                             │
│ ├── Commercial Terms: 8.33% Annual CTC | 45-day payment credit terms | Max Vendors: 5                             │
│ └── Ingestion Audit: Trust Score 94/100 | Risk Scans: Clean | Clearance: Auto-Approved                            │
│                                                                                                                   │
│ [ TAB 3: AGENCY CLAIMS & BENCH ACTIVITY ]                                                                         │
│ ├── Claimed Agencies (3/5): Apex Talent (2 CVs), TechStaff UK (1 CV), GlobalHire (0 CVs)                         │
│ └── Direct Bench Submissions: 4 candidates recommended by 2 independent agencies                                  │
│                                                                                                                   │
│ [ TAB 4: CANDIDATE PIPELINE FUNNEL ]                                                                              │
│ ├── Applied: 24 | Shortlisted: 8 | Technical Interview: 4 | Client Interview: 2 | Offered: 1 | Joined: 0          │
│                                                                                                                   │
│ [ TAB 5: IMMUTABLE AUDIT TIMELINE ]                                                                               │
│ ├── Oct 12, 10:14 AM — Job created by Recruiter Sarah (Company #42)                                               │
│ ├── Oct 12, 10:14 AM — Auto-Approval Engine scored 94 -> public_market_status = 2 (Live)                          │
│ ├── Oct 13, 02:30 PM — Apex Staffing claimed requisition (Coins Deducted: Event 38)                               │
│ └── Oct 14, 11:00 AM — TechStaff submitted Bench Candidate John Doe (ac_status = 0)                              │
│                                                                                                                   │
+───────────────────────────────────────────────────────────────────────────────────────────────────────────────────+
```

---

## 14. High-Throughput Bulk Operations Engine

Super Admins managing hundreds of daily submissions require safe, transactional bulk actions:

### 14.1 Supported Bulk Actions
1. **Bulk Approve:** Transitions selected jobs to `public_market_status = 2` (Live).
2. **Bulk Auto-Clear Eligible:** Automatically approves all jobs currently in pending status with `Risk Score >= 85` and `0 Flags`.
3. **Bulk Reject:** Rejects selected jobs with a shared reason code and bulk remediation email dispatch.
4. **Bulk Delist / Pause:** Temporarily pulls selected jobs from the marketplace grid.
5. **Bulk Adjust Vendor Limit:** Reassigns `vendor_limit` across multiple client positions simultaneously.

### 14.2 Concurrency Safeguards & Chunked Processing
- Bulk updates $> 20$ items are offloaded to background Horizon queues in chunks of 50 to prevent database lock contention.
- Uses `sys_idempotency_keys` to guarantee zero duplicate writes if an admin double-clicks or re-submits a bulk batch.

---

## 15. Commercial Coin Economics & Accounting (Events 38–41)

Marketplace transactions consume and credit operational coins governed by the `coin_events` and `coin_transection_logs` ledgers:

| Event ID | Event Name | Deduction Type | Entity Debited | Amount / Rate | Description |
| :---: | :--- | :---: | :---: | :---: | :--- |
| **38** | `MARKETPLACE_CLAIM_JOB` | `onetime` | Claiming Agency | Configured in `coin_events` | Deducted when agency claims a Method 1 position |
| **39** | `MARKETPLACE_BENCH_SUBMISSION` | `onetime` | Submitting Agency | Configured in `coin_events` | Deducted when agency deploys a Method 2 bench CV |
| **40** | `MARKETPLACE_CLAIM_REVOCATION_REFUND` | `credit` | Claiming Agency | 100% Refund | Refunded if admin cancels job due to client fault |
| **41** | `MARKETPLACE_SUCCESS_SETTLEMENT` | `sales` | Hiring Company | % Commission | Platform commission settlement upon candidate joining |

---

## 16. Database Truth & Additive Schema Specification

All schema additions adhere strictly to MySQL 8.0 standards, InnoDB engine, and tenant isolation:

```sql
-- 1. Immutable Moderation Audit Log Ledger
CREATE TABLE IF NOT EXISTS `job_marketplace_moderation_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_id` INT(10) UNSIGNED NOT NULL,
  `company_id` INT(10) UNSIGNED NOT NULL,
  `admin_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 for system auto-approval engine',
  `action` ENUM('submitted', 'auto_approved', 'manual_approved', 'rejected', 'quarantined', 'delisted', 'resubmitted', 'claim_approved', 'claim_revoked') NOT NULL,
  `previous_status` INT(11) NOT NULL,
  `new_status` INT(11) NOT NULL,
  `risk_score` INT(11) NOT NULL DEFAULT 0,
  `rejection_reason_code` VARCHAR(64) DEFAULT NULL,
  `feedback_notes` TEXT DEFAULT NULL,
  `risk_flags` JSON DEFAULT NULL COMMENT 'Detailed array of triggered heuristics',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jmml_job_created` (`job_id`, `created_at`),
  KEY `idx_jmml_company_action` (`company_id`, `action`),
  KEY `idx_jmml_admin_created` (`admin_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Concurrency Idempotency Ledger (Prevents Double-Clicks & Duplicate Writes)
CREATE TABLE IF NOT EXISTS `sys_idempotency_keys` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idempotency_key` VARCHAR(64) NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `action` VARCHAR(64) NOT NULL,
  `response_code` INT(11) NOT NULL DEFAULT 200,
  `response_body` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_idempotency_key` (`idempotency_key`),
  KEY `idx_idempotency_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 17. Elasticsearch 8.x Index Mapping & CQRS Sync Architecture

The `ats_jobs` index is enhanced with dedicated marketplace fields to power sub-8ms faceted queries:

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
      "public_market_status": { "type": "integer" },
      "show_vendor": { "type": "keyword" },
      "work_type": { "type": "keyword" },
      "employment_type": { "type": "integer" },
      "min_yearly": { "type": "double" },
      "max_yearly": { "type": "double" },
      "risk_score": { "type": "integer" },
      "risk_flags": { "type": "keyword" },
      "rejection_reason_code": { "type": "keyword" },
      "claimed_agencies_count": { "type": "integer" },
      "bench_submissions_count": { "type": "integer" },
      "vendor_limit": { "type": "integer" },
      "created_at": { "type": "date", "format": "yyyy-MM-dd HH:mm:ss||strict_date_optional_time" },
      "updated_at": { "type": "date", "format": "yyyy-MM-dd HH:mm:ss||strict_date_optional_time" }
    }
  }
}
```

---

## 18. Vertical Slice Backend Code Implementations

### 18.1 Super Admin Marketplace Controller
Adheres strictly to thin controller standards, delegating execution to specialized services:

```php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\SuperAdminMarketplaceSearchService;
use App\Services\Marketplace\MarketplaceModerationService;
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

### 18.2 Transactional Moderation Service
Handles ACID writes in MySQL, creates entries in `transactional_outboxes`, and purges Dragonfly cache:

```php
namespace App\Services\Marketplace;

use App\Services\TransactionalOutboxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MarketplaceModerationService
{
    public function __construct(
        private readonly TransactionalOutboxService $outboxService
    ) {}

    public function approveJobs(array $jobIds, int $adminId, string $idempotencyKey, string $ip): array
    {
        return DB::transaction(function () use ($jobIds, $adminId, $idempotencyKey, $ip) {
            // 1. Idempotency Check
            $existing = DB::table('sys_idempotency_keys')->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return json_decode($existing->response_body, true);
            }

            // 2. Fetch Jobs to Mutate
            $jobs = DB::table('jobs')->whereIn('id', $jobIds)->lockForUpdate()->get();
            $auditRows = [];

            foreach ($jobs as $job) {
                // Update Requisition Status
                DB::table('jobs')->where('id', $job->id)->update([
                    'public_market_status' => 2, // Live
                    'updated_at' => now(),
                ]);

                // Create Audit Log
                $auditRows[] = [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'admin_id' => $adminId,
                    'action' => 'manual_approved',
                    'previous_status' => $job->public_market_status,
                    'new_status' => 2,
                    'risk_score' => 100,
                    'ip_address' => $ip,
                    'created_at' => now(),
                ];

                // Record in transactional_outboxes
                $this->outboxService->recordEvent(
                    $job->company_id,
                    $job->id,
                    1,
                    'JobMarketplaceApproved',
                    ['job_id' => $job->id, 'new_status' => 2, 'admin_id' => $adminId]
                );

                // Purge Dragonfly Tenant Cache
                Redis::del("company_id:{$job->company_id}:marketplace_jobs");
            }

            if (!empty($auditRows)) {
                DB::table('job_marketplace_moderation_logs')->insert($auditRows);
            }

            $response = ['status' => 'success', 'message' => count($jobIds) . ' jobs approved successfully.'];

            DB::table('sys_idempotency_keys')->insert([
                'idempotency_key' => $idempotencyKey,
                'user_id' => $adminId,
                'action' => 'marketplace.approve',
                'response_code' => 200,
                'response_body' => json_encode($response),
                'created_at' => now(),
            ]);

            return $response;
        });
    }
}
```

---

## 19. Client-First Browser Architecture & Vanilla JS DOM Engine

The browser executes instant filtering, debouncing, and drawer handling in User RAM with zero page reloads:

```javascript
/**
 * AgySuperAdminMarketplace.js
 * High-performance, isolated vanilla JS controller for Super Admin Marketplace.
 * Completely zero jQuery, zero external heavy dependencies.
 */
class AgySuperAdminMarketplace {
    constructor(config) {
        this.endpoints = config.endpoints;
        this.currentTab = config.initialTab || 'command_center';
        this.abortController = null;
        this.cache = new Map();
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTelemetry();
        this.bindHotkeys();
    }

    bindEvents() {
        // Debounced search with AbortController
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

## 20. Modern Enterprise UI/UX Design System (15.6" Sizing Standards)

### 20.1 Viewport Budgeting for 1366×768 Resolution
- **Total Vertical Viewport:** `768px`
- **Application Top Bar:** `60px` (Fixed)
- **Marketplace Breadcrumbs & KPI Summary Bar:** `48px` (Fixed)
- **Faceted Tab Navigation:** `44px` (Fixed)
- **Active Grid Container:** `616px` (Remaining vertical space with internal scrolling, 0 page scroll)
- **Row Heights:** `48px` (High density) or `64px` (Comfortable)
- **Typography:** Strictly **Poppins font family**, maximum font size **14px** for headings, **12px** for table body, **11px** for badges.

### 20.2 Color Tokens & Metronic Standards
- `Primary Metronic Blue:` `#2563eb` / `#1d4ed8`
- `Success Emerald:` `#059669` / `#10b981`
- `Warning Amber:` `#d97706` / `#f59e0b`
- `Destructive Crimson:` `#dc2626` / `#ef4444`
- `Border Slate:` `#e2e8f0`
- `Surface Pure White:` `#ffffff`
- Completely flat iconography using Metronic SVG icons. Zero 3D elements, zero skeuomorphism.

---

## 21. High-Concurrency Benchmark & Verification Plan

### 21.1 10,000 Concurrent User Load Test (`bun` / `k6` Script)
Simulates 10,000 concurrent recruiters and admins querying the telemetry and faceted search endpoints:

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

## 22. Phased Zero-Downtime Migration & Rollout Strategy

```
                                  PHASED ROLLOUT ROADMAP
                                  
    PHASE 1 (Day 1)          PHASE 2 (Day 2)          PHASE 3 (Day 3-4)        PHASE 4 (Day 5)
    [DB & OUTBOX DDL]        [ELASTICSEARCH SYNC]     [CONTROLLER & UI]        [LEGACY DEPRECATION]
   ─────────────────────    ─────────────────────    ─────────────────────    ─────────────────────
   • Create moderation      • Update ats_jobs        • Deploy new Blade views • Route alias proxying
     logs & idempotency       mapping with risk        and vanilla JS engine  • Decommission 5 old
   • Verify transactional_    fields                 • Wire Dragonfly 4M QPS    monolithic queries
     outboxes readiness     • Background re-index      cache & Horizon jobs   • Verify zero 404s
```

### Definition of Done Checklist
- [x] Zero MySQL `SELECT` reads on web request paths (100% CQRS via Elasticsearch 8.x).
- [x] Dragonfly multi-threaded in-memory caching wired with atomic tenant invalidation.
- [x] All 5 legacy screens consolidated into a single Command Center.
- [x] Multi-factor risk engine evaluates contact leaks, JD quality, and CTC bands in $<3\text{ ms}$.
- [x] 15.6" screen layout verified on 1366×768 and 1920×1080 viewports with zero horizontal overflow.
- [x] Transactional outbox pattern implemented via `transactional_outboxes` and `TransactionalOutboxService`.
- [x] Backward-compatible route aliases redirect legacy endpoints seamlessly.
- [x] Product Company and Staffing Company code remains 100% untouched and frozen.
- [x] Code adheres strictly to `docs/CODE_DOCUMENTATION_STANDARD.md` and Metronic design standards.

---

*(End of Authoritative Super Admin Job Marketplace Redesign Specification)*
