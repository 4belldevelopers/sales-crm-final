# Comprehensive MySQL ↔ Elasticsearch Synchronization Implementation Plan

**Project:** JERO B2B Hiring SaaS (`testing.jero.works`)  
**Repository Path:** `C:\xampp\htdocs\git\JWMaincode28June2026`  
**Status:** PROPOSAL & PLAN ONLY (Awaiting Explicit Approval Before Execution)

---

> [!IMPORTANT]
> **DO NOT EXECUTE AUTOMATED CODE MUTATIONS.**  
> This implementation plan is presented for user review and architectural approval. No source code, database schemas, or queue configurations have been modified.

---

## Plan Overview & Phase Architecture

```
┌────────────────────────────────────────────────────────────────────────┐
│                      IMPLEMENTATION ROADMAP MAP                        │
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 1: Transaction & After-Commit Safety (Data Consistency)          │
│ PHASE 2: Missing Elasticsearch Synchronization (Feature Completeness)  │
│ PHASE 3: Retry & Failure Handling (Reliability & Resilience)           │
│ PHASE 4: Idempotency & Deterministic Document IDs (Data Integrity)     │
│ PHASE 5: Concurrency Protection & Timestamp Ordering (Race Conditions) │
│ PHASE 6: Performance, Bulk, & Queue Optimization (Scalability)         │
└────────────────────────────────────────────────────────────────────────┘
```

---

## Phase 1: Transaction & After-Commit Safety

### Objective
Ensure that no Elasticsearch indexing request occurs before the corresponding MySQL database transaction successfully commits.

---

### Item 1.1: Standardize `$afterCommit` on Model Observers & Dispatched Jobs

- **Priority:** `CRITICAL`
- **Category:** `Required for Data Consistency`
- **Problem:** Observers (`JobObserver`, `BenchApplicationObserver`) and queue jobs (`ElasticBenchCandidateIndex`, `SyncClientToElasticJob`) dispatch indexing work synchronously or immediately upon DB write inside active `DB::transaction()` blocks. If the MySQL transaction rolls back, Elasticsearch retains phantom uncommitted data.
- **Current Behavior:**
  ```php
  // In JobObserver.php (Lines 21-40)
  public function updated(Job $job): void {
      GeneralMethod::JobAddEditUpload($job->id); // Executes immediately inline inside transaction!
  }
  ```
- **Proposed Solution:**
  1. Add `public bool $afterCommit = true;` to `JobObserver`, `BenchApplicationObserver`, and all ES jobs.
  2. Refactor `JobObserver` to dispatch `ElasticJobsIndexJob` asynchronously using `afterCommit()`:
     ```php
     public function updated(Job $job): void {
         ElasticJobsIndexJob::dispatch($job->id)->afterCommit();
     }
     ```
- **Files Affected:**
  - `app/Observers/JobObserver.php` (Lines 21-60)
  - `app/Observers/BenchApplicationObserver.php` (Lines 14-33)
  - `app/Jobs/ElasticBenchCandidateIndex.php` (Lines 13-25)
  - `app/Jobs/SyncClientToElasticJob.php` (Lines 21-30)
  - `app/Jobs/SyncUserToElasticJob.php` (Lines 21-30)
- **Risk:** Low. Deferred dispatch guarantees data consistency.
- **Testing Required:**
  - Execute job creation inside a failing `DB::transaction()` block. Verify MySQL rolls back and ES is NOT mutated.
  - Execute job creation inside a successful transaction. Verify ES document is updated after commit.

---

### Item 1.2: Wrap Controller Synchronous ES Calls in `DB::afterCommit()` Callbacks

- **Priority:** `HIGH`
- **Category:** `Required for Data Consistency`
- **Problem:** Inline controller methods execute `GeneralMethod::JobAddEditUpload()` directly inside HTTP request handlers wrapped in `DB::transaction()`.
- **Current Behavior:**
  ```php
  // In ServiceCompanyJobsController.php (Line 145)
  DB::transaction(function() use ($request) {
      $job = Job::create(...);
      GeneralMethod::JobAddEditUpload($job->id); // Unsafe inline call inside transaction
  });
  ```
- **Proposed Solution:**
  Wrap inline ES helper calls inside `DB::afterCommit()` callbacks or offload to background queues:
  ```php
  DB::afterCommit(function() use ($jobId) {
      ElasticJobsIndexJob::dispatch($jobId);
  });
  ```
- **Files Affected:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (Lines 142, 315, 512)
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` (Lines 195, 340)
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` (Lines 95, 215)
- **Risk:** Low.
- **Testing Required:**
  - Test HTTP request lifecycle for job store/update. Verify response speed improved and ES sync deferred post-commit.

---

## Phase 2: Missing Elasticsearch Synchronization

### Objective
Plug all gaps where MySQL database writes occur without corresponding Elasticsearch read model updates.

---

### Item 2.1: Implement ES Sync Triggers for Job Skills and Job Locations

- **Priority:** `HIGH`
- **Category:** `Required for Data Consistency`
- **Problem:** Updating `job_skills` or `job_locations` using `DB::table()` queries bypasses Eloquent observers. Elasticsearch `local_jobs_elastic` search filters become stale.
- **Current Behavior:**
  ```php
  // In ServiceCompanyJobsController.php (Line 605)
  DB::table('job_skills')->insert($skillsData); // No ES sync triggered!
  ```
- **Proposed Solution:**
  Dispatch `ElasticJobsIndexJob::dispatch($jobId)->afterCommit()` after updating job skills or locations.
- **Files Affected:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` (Lines 605, 680)
  - `app/Http/Controllers/ProductCompany/ProductCompanyJobsController.php` (Lines 590, 660)
  - `app/Services/JobService.php` (Lines 110, 145)
- **Risk:** Low.
- **Testing Required:**
  - Add skill to job in UI. Perform ES search by skill. Verify job appears in search results immediately.

---

### Item 2.2: Implement ES Sync Triggers for Candidate Status and Notes

- **Priority:** `CRITICAL`
- **Category:** `Required for Data Consistency`
- **Problem:** Moving a candidate across pipeline stages in `ServiceCompanyCvTrackerController::updateStatus` updates MySQL `job_applications.master_status_id` but does NOT update `local_candidates_elastic`.
- **Current Behavior:**
  ```php
  // In ServiceCompanyCvTrackerController.php (Line 340)
  JobApplication::where('id', $id)->update(['master_status_id' => $statusId]);
  // No ES sync call! Candidate remains in old status in ES!
  ```
- **Proposed Solution:**
  Add candidate ES sync dispatch in `JobApplicationObserver` or directly in controller:
  ```php
  SyncCandidateToElasticJob::dispatch($jobApplication->id)->afterCommit();
  ```
- **Files Affected:**
  - `app/Observers/JobApplicationObserver.php` (Lines 31-56)
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` (Line 340)
  - `app/Http/Controllers/ServiceCompany/ApplicantNoteController.php` (Line 65)
- **Risk:** Low.
- **Testing Required:**
  - Drag candidate to new stage on ATS board. Verify status update reflects in ES search queries.

---

### Item 2.3: Implement ES Sync Triggers for Client Commercial Terms and Deletions

- **Priority:** `HIGH`
- **Category:** `Required for Data Consistency`
- **Problem:** Updating `commericials` or deleting `clients` in `ServiceCompanyClientController` leaves `local_client_list_elastic` and `local_commericials` out of sync.
- **Current Behavior:**
  ```php
  // In ServiceCompanyClientController.php (Line 380)
  $client->delete(); // MySQL row removed; ES document remains!
  ```
- **Proposed Solution:**
  Implement `ClientObserver` with delete and update triggers:
  ```php
  public function deleted(Client $client): void {
      DeleteClientFromElasticJob::dispatch($client->id)->afterCommit();
  }
  ```
- **Files Affected:**
  - `app/Observers/ClientObserver.php` (Lines 10-18)
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` (Lines 215, 380)
- **Risk:** Low.
- **Testing Required:**
  - Delete client in UI. Query `local_client_list_elastic` via ES API. Verify document deleted.

---

## Phase 3: Retry & Failure Handling

### Objective
Ensure that temporary Elasticsearch cluster outages or network timeouts do not cause permanent data loss.

---

### Item 3.1: Standardize Job Retry Policies Across All ES Jobs

- **Priority:** `HIGH`
- **Category:** `Recommended for Reliability`
- **Problem:** Some ES jobs lack retry backoffs (`ElasticCandidateIndex`, `ElasticJobsIndex`), while others have inconsistent retry counts.
- **Current Behavior:**
  `ElasticCandidateIndex.php` has no `$tries` property. If ES is down for 5 seconds, job fails permanently without retry.
- **Proposed Solution:**
  Enforce uniform retry contract across all ES queue jobs:
  ```php
  public int $tries = 5;
  public array $backoff = [10, 30, 60, 120, 300]; // Exponential backoff up to 5 mins
  public int $timeout = 60;
  ```
- **Files Affected:**
  - `app/Jobs/ElasticCandidateIndex.php` (Lines 13-25)
  - `app/Jobs/ElasticJobsIndex.php` (Lines 13-25)
  - `app/Jobs/ElasticBenchCandidateIndex.php` (Lines 19-22)
  - `app/Jobs/SyncClientToElasticJob.php` (Lines 25-30)
- **Risk:** Low.
- **Testing Required:**
  - Simulate ES node failure (stop ES service). Dispatch job. Verify worker retries 5 times with backoff delays before sending to `failed_jobs`.

---

## Phase 4: Idempotency & Document ID Strategy

### Objective
Guarantee that executing an ES sync job multiple times results in the exact same index state without duplicate documents.

---

### Item 4.1: Deterministic Document ID Construction

- **Priority:** `MEDIUM`
- **Category:** `Required for Data Integrity`
- **Problem:** `IndexCandidateSkillsToElasticJob` uses non-deterministic body indexing, creating multiple document IDs for the same skill entry on repeated executions.
- **Current Behavior:**
  ```php
  // In IndexCandidateSkillsToElasticJob.php (Line 63)
  $elasticService->getClient()->index([
      'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
      'body' => $data // No 'id' specified! ES auto-generates random document hash!
  ]);
  ```
- **Proposed Solution:**
  Specify deterministic document ID combining candidate ID and skill ID:
  ```php
  $elasticService->getClient()->index([
      'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
      'id'    => "cand_{$this->candidateId}_skill_{$this->skillId}",
      'body'  => $data
  ]);
  ```
- **Files Affected:**
  - `app/Jobs/IndexCandidateSkillsToElasticJob.php` (Lines 51-70)
- **Risk:** Low.
- **Testing Required:**
  - Dispatch `IndexCandidateSkillsToElasticJob` 3 times for same candidate/skill. Check ES count; verify exactly 1 document exists.

---

## Phase 5: Concurrency Protection

### Objective
Prevent race conditions where out-of-order queue processing overwrites newer MySQL state with older data.

---

### Item 5.1: Include `updated_at` Timestamp Constraints in ES Write Payloads

- **Priority:** `MEDIUM`
- **Category:** `Recommended for Reliability`
- **Problem:** Concurrent user actions can trigger two jobs out of order. A slower initial edit job processing after a fast subsequent edit job overwrites fresh data with old state.
- **Proposed Solution:**
  Include MySQL `updated_at` ISO string in document payload and verify document timestamp prior to indexing, or utilize ES optimistic concurrency control (`if_seq_no` / `if_primary_term`).
- **Files Affected:**
  - `app/Services/ElasticCandidatesIndex.php` (Line 404)
  - `app/Services/ElasticJobIndex.php` (Line 76)
  - `app/Services/ElasticCaompanyIndex.php` (Line 79)
- **Risk:** Medium.
- **Testing Required:**
  - Dispatch job for version T1 after job for version T2. Verify T2 state persists in ES.

---

## Phase 6: Performance, Bulk, & Queue Optimization

### Objective
Eliminate synchronous web-thread bottlenecks and reduce ES bulk query overhead.

---

### Item 6.1: Replace Full-Tenant Bulk Re-Indexing with Single-Document Updates

- **Priority:** `HIGH`
- **Category:** `Performance Optimization`
- **Problem:** Updating 1 bench candidate triggers `ElasticBenchCandidateIndex::dispatch($comid)`, which queries and bulk-indexes EVERY bench candidate belonging to that company.
- **Current Behavior:**
  ```php
  // In ElasticBenchCandidateIndex.php (Line 45)
  $elasticCandidateIndex->benchCandidateIndexElastic($this->comid); // Full tenant wipe & re-index!
  ```
- **Proposed Solution:**
  Refactor job to accept single `$benchApplicationId` and execute targeted document index/update:
  ```php
  public function handle(): void {
      $bench = BenchApplication::find($this->benchId);
      $elasticService->indexDocument('local_candidates_bench_elastic', $bench->id, $bench->toArray());
  }
  ```
- **Files Affected:**
  - `app/Jobs/ElasticBenchCandidateIndex.php` (Lines 13-48)
  - `app/Services/ElasticBenchCandidatesIndex.php` (Lines 20-350)
  - `app/Observers/BenchApplicationObserver.php` (Lines 14-33)
- **Risk:** Medium.
- **Testing Required:**
  - Benchmark bench candidate update operation. Verify database query count reduced from N to 1.

---

### Item 6.2: Dedicated Queue Routing for Elasticsearch Jobs

- **Priority:** `MEDIUM`
- **Category:** `Performance Optimization`
- **Problem:** ES jobs share default queue with email and notification jobs, causing indexing delays during mass mailing runs.
- **Proposed Solution:**
  Route all ES indexing jobs to dedicated `elasticsearch` queue connection:
  ```php
  public function __construct() {
      $this->onQueue('elasticsearch');
  }
  ```
- **Files Affected:**
  - `app/Jobs/ElasticCandidateIndexJob.php`
  - `app/Jobs/ElasticJobsIndexJob.php`
  - `app/Jobs/SyncCandidateToElasticJob.php`
  - `app/Jobs/SyncClientToElasticJob.php`
  - `app/Jobs/SyncUserToElasticJob.php`
- **Risk:** Low. Requires queue worker process configuration (`php artisan horizon` or supervisor config).
- **Testing Required:**
  - Run heavy mail queue workload. Dispatch ES job. Verify ES job processed immediately on `elasticsearch` queue without delay.

---

## Summary of Verification Strategy

1. **Unit & Integration Testing:**
   - Execute Laravel test suite (`php artisan test`) with mocked Elasticsearch client to verify events fire properly.
2. **Transaction Integrity Testing:**
   - Run integration tests that intentionally throw exceptions inside `DB::transaction()` blocks. Verify zero calls reach ES client.
3. **Queue Execution Verification:**
   - Monitor Horizon queue worker logs during bulk entity updates to verify retry behavior, backoff timing, and zero failed jobs.
4. **Read/Write Consistency Verification:**
   - Run `ReconcileElasticIndices` console command post-migration to verify 100% data alignment between MySQL tables and Elasticsearch indices.
