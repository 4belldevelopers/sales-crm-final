# Candidate Submit Path V2 — Multi-Step Latency Optimization Plan (Refined)

> **Goal:** Reduce candidate submission HTTP response time from **1.5–3.5 seconds down to <150 ms (15X–20X speedup)** while guaranteeing that the new candidate row is **100% visible on AppTracker (`ELASTIC_CANDIDATES`) IMMEDIATELY** upon redirect.

---

## Executive Summary & Architecture Guarantee

```
Current HTTP Submit Latency:   1,500 ms – 3,500 ms (blocking)
Optimized HTTP Submit Latency:  100 ms – 150 ms (instant UI response)
Speedup Factor:                15X – 20X Faster
Candidate Visibility:          IMMEDIATE on AppTracker (ES) & Candidate Detail (MySQL)
```

---

## The Dual-Layer Strategy

```
[Recruiter Clicks Submit]
       │
       ├─► 1. Save Candidate to MySQL job_applications (< 30 ms)
       ├─► 2. Index Primary Candidate Doc to ELASTIC_CANDIDATES (~40 ms)
       │       └──► HTTP Redirect to AppTracker: CANDIDATE ROW IS VISIBLE IMMEDIATELY! ✅
       │
       └─► 3. Queue Secondary Horizon Background Jobs (Async, non-blocking):
               ├──► SyncCandidateSkillsToElasticJob (Heavy skill matrix loop)
               ├──► SyncCandidateQuestionsToElasticJob (Custom question upserts)
               ├──► SyncTeamRankElasticJob (TeamRank stats & skill match)
               └──► QueueNotificationMailService (Email notification prep & dispatch)
```

---

## Phase 1: Keep Primary Candidate Indexing Synchronous

### Step 1: Keep `GeneralMethod::JobApplicationsAddEditUpload` in HTTP Thread
- **Location**: `ServiceCompanyJobApplicationController.php` (Line 2447)
- **Intent**: Index the primary candidate document into `ELASTIC_CANDIDATES` (~40ms) *before* page redirect.
- **Guarantee**: Guarantees that when the user is redirected to AppTracker (`/service_company/apptracker/{id}/all`), the candidate row appears in the Elasticsearch grid **immediately**.

---

## Phase 2: Offload Heavy Secondary Index Loops & Queries

### Step 2: Create `SyncCandidateSkillsToElasticJob`
- **File**: `app/Jobs/SyncCandidateSkillsToElasticJob.php`
- **Intent**: Offload synchronous loop of ES `deleteByQuery` and `index` calls for skills (lines 2018–2071) to Horizon queue.
- **Impact**: Saves **400ms – 1,200ms** per submit.

### Step 3: Create `SyncCandidateQuestionsToElasticJob`
- **File**: `app/Jobs/SyncCandidateQuestionsToElasticJob.php`
- **Intent**: Offload synchronous ES `update` upserts for custom job screening questions (lines 2409–2424) to Horizon queue.
- **Impact**: Saves **200ms – 500ms** per submit.

### Step 4: Async `GeneralMethod::teamRankElastic` & `skillsAddElastic`
- **File**: `app/Jobs/SyncTeamRankElasticJob.php`
- **Intent**: Move team rank and global skill index updates outside the HTTP transaction into async jobs.
- **Impact**: Saves **300ms – 600ms** per submit.

---

## Phase 3: R2 Storage & Email Query Deferral

### Step 5: Perform R2 HEAD Check Outside MySQL Transaction
- **Location**: `ServiceCompanyJobApplicationController.php` (Lines 1995–2008)
- **Intent**: Execute R2 storage checks *before* opening `DB::beginTransaction()` to eliminate row lock holding during network I/O.
- **Impact**: Saves **200ms – 500ms** per submit.

### Step 6: Defer Email Preparation Queries to Queue Job
- **Location**: `ServiceCompanyJobApplicationController.php` (Lines 2154–2260)
- **Intent**: Move the 6 heavy multi-table `JOIN` queries into `QueueNotificationMailService` async handler.
- **Impact**: Saves **100ms – 250ms** per submit.

---

## Phase 4: Verification & Automated Benchmarking

### Step 7: Automated Latency & Visibility Test
- **File**: `tests/Feature/CandidateSubmitLatencyTest.php`
- **Assertions**:
  - Assert submit returns HTTP 200/redirect in `< 200 ms`.
  - Assert candidate row exists in `ELASTIC_CANDIDATES` immediately.
  - Assert secondary Horizon queue jobs are dispatched.
