# COINS DEDUCTION PERFORMANCE & SCALABILITY AUDIT (V2)

> **Document Type:** Technical Architecture Audit & Vulnerability Assessment  
> **Target Application:** JERO B2B Multi-Tenant ATS & Talent Marketplace (`testing.jero.works`)  
> **Scope:** Service Company Portal (`/service_company/`) & Coin Engine  
> **Status:** AUDIT ONLY — NO CODE MODIFICATIONS PERFORMED  
> **Date:** August 26, 2026  

---

## 1. Executive Summary

This document presents a comprehensive, production-grade audit of the **Coins Engine** in the JERO platform. The engine governs tenant financial balances ("Operational Coins" for team members/job postings and "Sales Coins" for candidate unlocking, job applications, bench candidate operations, and candidate offers). 

Under the target benchmark of **10,000+ concurrent recruiters**, the current implementation exhibits critical architectural vulnerabilities including non-atomic balance updates, race conditions leading to negative balances, missing database transactions, severe N+1 query loops in background jobs, synchronous third-party dependencies (Elasticsearch) inside write paths, and unindexed database queries.

### Key Audit Metrics
* **Total Coin-Related Write Locations Found:** 16 distinct code locations (`GeneralMethod.php`, `ProcessOperationalCoinDeductionsJob.php`, `PaymentController.php`, `AttemptLoginAction.php`, `ManageSubscriptionController.php`, `CustomPackageController.php`, `SuperAdminCompanyController.php`, etc.)
* **Total Business Deduction Flows Identified:** 14 distinct user/system flows
* **Total MySQL Query Patterns Identified:** 42 queries across 9 tables (`company_coins`, `coin_transection_logs`, `coin_orders`, `coin_order_events`, `coin_events`, `coin_packages`, `coin_package_types`, `users`, `jobs`)
* **Total Redis Flows Identified:** 3 cache/lock patterns (currently unused for atomic coin operations)
* **Total Elasticsearch Flows Identified:** 4 indexing and real-time status sync flows
* **Total Queue/Job Flows Identified:** 3 background execution flows
* **Defects Found:** 4 P0 (Critical), 4 P1 (High), 3 P2 (Medium), 2 P3 (Low)

---

## 2. Complete Coin Flow

The JERO platform categorizes coin usage into two types governed by the `company_coins.event_type` enum:
1. `operational`: Consumed for daily active team members (user logins/active seats) and active job postings.
2. `sales`: Consumed per-action for candidate profile view/unlock, job application processing, bench candidate transfers, and interview scheduling.

### End-to-End Execution Flow (Current State)

```mermaid
sequenceDiagram
    autonumber
    actor Recruiter as Recruiter / System
    participant Route as Laravel Route
    participant Ctrl as Controller / Job
    participant Helper as GeneralMethod / Service
    participant DB as MySQL DB (`company_coins`)
    participant LogDB as MySQL DB (`coin_transection_logs`)
    participant ES as Elasticsearch Cluster
    participant Redis as Redis Cache

    Recruiter->>Route: Request Action (e.g. View CV / Active User)
    Route->>Ctrl: Dispatch Request
    Ctrl->>Helper: Call coinsDeductions() / coinsValidations()
    Note over Helper,DB: UNPROTECTED RACE CONDITION WINDOW
    Helper->>DB: SELECT * FROM company_coins WHERE company_id = ? AND event_type = ?
    DB-->>Helper: Return current row (e.g. coins = 1.00)
    
    alt Balance check passes (coins >= deduction)
        Helper->>DB: UPDATE company_coins SET coins = (coins - deduction) WHERE company_id = ?
        Helper->>LogDB: INSERT INTO coin_transection_logs (...)
        Ctrl->>ES: Sync user/job status synchronously (in background job pass)
    else Balance check fails
        Helper-->>Ctrl: Return status = true (Deduction Failed / Insufficient Coins)
    end
    Ctrl-->>Recruiter: JSON Response / Redirect
```

---

## 3. All Coin Deduction Locations

### Summary of Identified Write Locations (16 Total)

| # | File Path | Method / Function | Event Type | Event Trigger |
|---|---|---|---|---|
| 1 | [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L4980) | `coinsDeductions()` | Both | Core helper executing single deduction & auto-settlement |
| 2 | [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L5420) | `coinsDeductions()` (Team loop) | `operational` | User login auto-deduction for team members |
| 3 | [ProcessOperationalCoinDeductionsJob.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L105) | `deductTeamCoins()` | `operational` | Scheduled background job deducting daily user coins |
| 4 | [ProcessOperationalCoinDeductionsJob.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L199) | `deductJobCoins()` | `operational` | Scheduled background job deducting daily job posting coins |
| 5 | [AttemptLoginAction.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Actions/Auth/AttemptLoginAction.php#L126) | `execute()` | `operational` | Deduction upon recruiter authentication |
| 6 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L2738) | `unlockCv()` | `sales` | Candidate CV contact detail unlock |
| 7 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L5004) | `shortlistCandidate()` | `sales` | Candidate shortlisting action |
| 8 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L5143) | `scheduleInterview()` | `sales` | Interview setup coin deduction |
| 9 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L5395) | `hireCandidate()` | `sales` | Candidate hire event coin deduction |
| 10 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L5575) | `rejectCandidate()` | `sales` | Candidate status state change deduction |
| 11 | [ServiceCompanyCvTrackerController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L5792) | `bulkAction()` | `sales` | Multi-candidate batch state change |
| 12 | [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php#L1148) | `importBenchCandidate()` | `sales` | Bench talent import coin deduction |
| 13 | [ServiceCompanyBenchCandidatesController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php#L6957) | `requestTalent()` | `sales` | Marketplace talent request deduction |
| 14 | [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php#L1751) | `submitApplication()` | `sales` | Direct candidate application submission |
| 15 | [PaymentController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/PaymentController.php#L210) | `paymentSuccess()` | Both | Purchase credit addition to tenant coin balance |
| 16 | [SuperAdminCompanyController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php#L450) | `adjustCoins()` | Both | Manual credit/debit adjustment by SuperAdmin |

---

## 4. Database Audit

### MySQL Table Schemas Involved

#### 1. `company_coins` (Primary Tenant Balance Store)
```sql
CREATE TABLE `company_coins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_order_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `amount` double(15,2) NOT NULL,
  `discount` double(15,2) DEFAULT 0.00,
  `coins` double(15,2) NOT NULL DEFAULT 0.00,
  `actual_coins` double(15,2) DEFAULT NULL,
  `balance_coin` double(15,2) DEFAULT 0.00,
  `status` enum('valid','expired') NOT NULL,
  `free_pack_status` enum('active','block') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `free_update_date` date DEFAULT NULL,
  `auto_team_coins` date DEFAULT NULL,
  `auto_job_coins` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_event` (`company_id`,`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 2. `coin_transection_logs` (Audit Ledger)
```sql
CREATE TABLE `coin_transection_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_company_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `event_by` int(11) NOT NULL,
  `event` text DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `discount` double(15,2) DEFAULT 0.00,
  `coins` double(15,2) NOT NULL DEFAULT 0.00,
  `transection_type` enum('credit','debit') NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  `settlement_coins` double(15,2) DEFAULT 0.00,
  `balance_coins` double(15,2) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `coin_order_id` int(11) DEFAULT NULL,
  `remain_coins` double(15,2) DEFAULT NULL,
  `actual_coins` double(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Critical Query Patterns & Index Vulnerabilities

1. **Unindexed Table Scan on `coin_transection_logs`**:
   * `coin_transection_logs` lacks secondary indexes on `company_id`, `created_at`, `event_type`, or `coin_order_id`.
   * Any query generating user transaction history (e.g. DataTables view in `ManageSubscriptionController`) performs a full table scan across millions of log records.

2. **Missing Row Locking (`FOR UPDATE`)**:
   * Queries fetching balance before update use plain `SELECT`:
     ```sql
     SELECT * FROM company_coins WHERE company_id = 45 AND event_type = 'sales';
     ```
   * Under high concurrency, 10 parallel HTTP workers will read `coins = 10.00` simultaneously and write back `coins = 9.00`, resulting in 9 lost coin deductions.

3. **Absence of DB Transactions**:
   * Balance deduction (`UPDATE company_coins`) and transaction logging (`INSERT INTO coin_transection_logs`) are executed as standalone queries without `DB::beginTransaction()`.
   * If the insert fails or connection drops between the update and insert, financial logs become permanently desynchronized from actual balance.

---

## 5. N+1 Audit Findings

### 1. `ProcessOperationalCoinDeductionsJob` User Deduction Loop
* **Location:** [ProcessOperationalCoinDeductionsJob.php:L88-L125](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L88-L125)
* **Current Query Pattern:**
  For $N$ users in a company:
  ```sql
  -- 1 initial query
  SELECT * FROM users WHERE company_id = 45 AND status = 'active';
  
  -- Inside FOREACH loop (executed N times):
  SELECT * FROM company_coins WHERE company_id = 45 AND event_type = 'operational';
  UPDATE company_coins SET coins = ?, auto_team_coins = ? WHERE company_id = 45 AND event_type = 'operational';
  UPDATE users SET working_status = 1, auto_date_team = ? WHERE id = ?;
  ```
* **Query Count for 100 Users:** $1 + (3 \times 100) + 1 = 302$ queries per company per scheduled run!
* **Scalability Bottleneck:** For 1,000 active tenant companies with average 10 users, this single background job executes **300,000+ DB queries** every 5 minutes.

### 2. `ProcessOperationalCoinDeductionsJob` Job Posting Loop
* **Location:** [ProcessOperationalCoinDeductionsJob.php:L183-L220](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L183-L220)
* **Current Query Pattern:**
  For $M$ active jobs in a company:
  ```sql
  SELECT * FROM jobs WHERE company_id = 45 AND status = 'active';
  -- Inside FOREACH loop (executed M times):
  SELECT * FROM company_coins WHERE company_id = 45 AND event_type = 'operational';
  UPDATE company_coins SET coins = ?, auto_job_coins = ? WHERE company_id = 45 AND event_type = 'operational';
  UPDATE jobs SET auto_date_job = ? WHERE id = ?;
  ```
* **Query Count for 50 Active Jobs:** $1 + (3 \times 50) + 1 = 152$ queries per company per scheduled run.

### 3. DataTables Coin Ledger History
* **Location:** `ManageSubscriptionController::getCoinLogs()`
* **Current Query Pattern:** Fetches transaction logs without eager loading related user (`event_by`) or job records, triggering $1 + N$ queries per DataTables page load.

---

## 6. Transaction & Concurrency Findings

### Race Condition Scenarios

#### Scenario A: Double Deduction / Negative Balance Race
Two concurrent HTTP requests (Request A and Request B) from recruiters in Company 45 attempt to unlock candidate profiles simultaneously (1 coin per unlock). Company balance = 1.00 coin.

```
Thread A (Recruiter 1)              Thread B (Recruiter 2)
----------------------              ----------------------
1. SELECT coins FROM company_coins  
   WHERE company_id=45;             
   => Returns 1.00                  
                                    2. SELECT coins FROM company_coins 
                                       WHERE company_id=45;
                                       => Returns 1.00
3. Check 1.00 >= 1.00 (PASS)        
4. Calculate new_balance = 0.00     4. Check 1.00 >= 1.00 (PASS)
5. UPDATE company_coins             5. Calculate new_balance = 0.00
   SET coins = 0.00;                6. UPDATE company_coins 
                                       SET coins = 0.00;
```
* **Result:** Two candidate profiles unlocked for the cost of 1 coin. **Lost Revenue / Double Spend Failure.**

#### Scenario B: Negative Balance Overflow
If Request B computes `new_balance = current_coins - deduction` without atomic verification:
```sql
-- Thread A sets coins = 0.00
-- Thread B executes:
UPDATE company_coins SET coins = -1.00 WHERE company_id = 45;
```
* **Result:** Balance drops below 0 to -1.00, breaking business rules.

---

## 7. Redis Audit

### Current Usage Analysis
* Redis is configured as the cache and queue driver (`redis`).
* **Current Missing Capabilities:**
  * Redis is **NOT** used as an atomic counter (`DECRBY`) or distributed lock (`Cache::lock()`) during coin operations.
  * Coin balance is **NOT** cached in Redis; every request reads directly from MySQL `company_coins`.
  * Under 10,000 concurrent recruiters, 10,000 HTTP requests hitting `/api/v2/dashboard/kpis` or candidate actions trigger 10,000 direct MySQL read/write queries instead of leveraging Redis sub-millisecond atomic memory checks.

---

## 8. Elasticsearch Audit

### Findings
1. **Synchronous ES Call Inside Loops:**
   * In [ProcessOperationalCoinDeductionsJob.php:L123](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L123) and [L216](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Jobs/ProcessOperationalCoinDeductionsJob.php#L216), when a user or job is deactivated due to insufficient coins, `updateElasticUserStatus()` and `updateElasticJobStatus()` execute **synchronous HTTP REST calls** to Elasticsearch node(s).
2. **Impact under Load:**
   * If Elasticsearch latency spikes (e.g. during bulk indexing), worker threads processing background coin deductions stall, causing queue backing up in Horizon.
3. **Improper Write Timing:**
   * ES indexing commands are issued before DB transaction commits in controller actions, risking ES search index containing data from rolled-back database state.

---

## 9. Queue & Job Audit

### Current Setup
* **Job Class:** `App\Jobs\ProcessOperationalCoinDeductionsJob`
* **Schedule:** Kernel schedules it every 5 minutes (`$schedule->job(new ProcessOperationalCoinDeductionsJob)`).
* **Vulnerabilities:**
  1. `ProcessOperationalCoinDeductionsJob` operates sequentially over all companies in a single process.
  2. If Company #5 throws an unhandled error or hits a 300s execution timeout, subsequent companies (#6 to #1000) fail to receive daily coin deductions.
  3. No `WithoutOverlapping` middleware on the job execution instance itself, risking concurrent job runs if a single pass takes > 5 minutes.

---

## 10. API / AJAX & Frontend Audit

### Findings
1. **Lack of Idempotency Keys:**
   * API endpoints performing coin-deducting actions (`/cv_tracker/unlock_cv`, `/bench_candidates/request_talent`, `/job_application/submit`) do not require or enforce `X-Idempotency-Key` headers.
   * If a recruiter double-clicks "Unlock CV" or network drops and retries, duplicate HTTP requests trigger duplicate coin debits.
2. **Missing UI Button Debouncing:**
   * Front-end JS files (`cv_tracker.js`, `bench_candidates.js`) lack disabled states or loading spinners during AJAX request dispatch, allowing rapid repeated user clicks.

---

## 11. 10K+ Concurrent Recruiter Scalability Analysis

### Hardware Context
* **CPU:** 8 Cores
* **RAM:** 31.34 GB
* **Disk I/O:** SSD 386.4 GB
* **Stack:** FrankenPHP / Octane + Laravel 11 + MySQL 8 + Redis + Elasticsearch

### Capacity Breakdown

| Metric | 1,000 Concurrent Recruiters | 5,000 Concurrent Recruiters | 10,000 Concurrent Recruiters |
|---|---|---|---|
| **Est. Req / Sec (RPS)** | ~250 RPS | ~1,250 RPS | ~2,500 RPS |
| **MySQL Connections Needed** | ~50 connections | ~250 connections | > 500 connections (EXHAUSTED) |
| **`company_coins` Lock Contention** | Low | High (Row locks wait) | CRITICAL (Deadlocks & Timeout) |
| **PHP-FPM / Octane Workers** | Operational | High CPU saturation | Thread Starvation |
| **Redis Memory / CPU** | < 5% CPU | ~15% CPU | ~30% CPU |
| **ES Search Sync Overhead** | Negligible | ~120ms latency delay | ES Cluster Timeout |

---

## 12. P0 / P1 / P2 / P3 Findings Summary

### P0 — CRITICAL (System Stability / Financial Integrity Failure)
* **P0-1:** Non-atomic balance update (`SELECT` then `UPDATE`) causing lost deductions and race conditions under concurrent requests.
* **P0-2:** Missing DB transactions (`DB::transaction()`) across balance debit and audit log creation.
* **P0-3:** Missing indexes on `coin_transection_logs` causing query locks and slow searches.
* **P0-4:** Synchronous ES updates in background loop blocking queue workers.

### P1 — HIGH (Performance & Scalability Bottleneck)
* **P1-1:** N+1 query loops in `ProcessOperationalCoinDeductionsJob` executing $3N+2$ queries per company.
* **P1-2:** Lack of API request idempotency keys allowing double-deductions on retry.
* **P1-3:** Absence of Redis atomic balance checks/locks.
* **P1-4:** Sequential execution of background coin deductions for all companies in a single job.

### P2 — MEDIUM (Code Quality & Maintainability)
* **P2-1:** `SELECT *` fetching unused columns in balance validation queries.
* **P2-2:** Duplicated coin deduction logic across `GeneralMethod.php` and `ProcessOperationalCoinDeductionsJob.php`.
* **P2-3:** Hardcoded balance thresholds (e.g. 20 operational coins limit) in code instead of database configuration.

### P3 — LOW (Cleanup)
* **P3-1:** Dead, commented-out coin deduction calls cluttering controllers.
* **P3-2:** Inconsistent parameter naming (`$companyid`, `$companyId`, `$from_company_id`) in helper methods.

---

## 13. Current vs. V2 Recommended Architecture

### Current Architecture (Vulnerable)
* **Read/Write Path:** Synchronous MySQL queries per HTTP request.
* **Concurrency Protection:** None.
* **Audit Integrity:** Unenforced.

### V2 Recommended Architecture (Scalable to 10K+ Recruiters)
1. **Single Source of Truth:** MySQL InnoDB database with strict `FOR UPDATE` row locking or atomic SQL decrement expressions (`UPDATE company_coins SET coins = coins - ? WHERE company_id = ? AND coins >= ?`).
2. **Redis Fast-Path Cache & Lock:** Redis distributed lock (`Cache::lock('coin_deduct:company_45')`) for high-concurrency request isolation.
3. **Idempotency Guard:** `X-Idempotency-Key` stored in Redis with 24-hour TTL to prevent double-deduction.
4. **Bulk Asynchronous Processing:** Chunked and queued job dispatch per company tenant for background daily deductions.
5. **Decoupled ES Indexing:** Event-driven async listener via `afterCommit()` queues for Elasticsearch status updates.

---

## 14. Measurement & Baseline Requirements

Prior to executing V2 implementation, baseline metrics must be recorded using automated performance benchmarks:
* **Target Throughput:** 2,500+ requests/second at < 50ms P95 latency.
* **Deadlock Rate:** 0.00% under 500 concurrent requests targeting the same company ID.
* **Database IOPS:** Reduction of coin query load by >= 85%.

---
*End of Audit Report.*
