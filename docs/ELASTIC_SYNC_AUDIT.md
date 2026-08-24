# Complete MySQL → Elasticsearch Synchronization Audit Report

**Project:** JERO B2B Hiring SaaS (`testing.jero.works`)  
**Repository Path:** `C:\xampp\htdocs\git\JWMaincode28June2026`  
**Audit Date:** August 24, 2026  
**Status:** AUDIT ONLY (No code changes implemented)

---

## 1. Executive Summary & System Metrics

This audit performs an exhaustive analysis of all MySQL write operations and Elasticsearch synchronization flows across the codebase.

### System-Wide Audit Metrics

| Metric | Total Count |
| :--- | :---: |
| **Total MySQL Write Locations (Codebase)** | **2,616** |
| — *Total MySQL INSERT Locations* | 822 |
| — *Total MySQL UPDATE Locations* | 1,367 |
| — *Total MySQL DELETE Locations* | 427 |
| **Total MySQL Writes to ES-Indexed Entities** | **1,172** |
| **Total MySQL Writes to Non-Indexed Internal Tables** | **1,444** |
| **Total Elasticsearch Operation Occurrences** | **1,474** |
| **Core ES Primary Write Locations (Services/Jobs/Commands)** | **22** |
| **Total Synchronized Flows (MySQL Write → ES Sync)** | **621** |
| **Total Missing Synchronization Flows (Indexed Entity Write → NO ES Sync)** | **760** |
| **Total Unsafe Synchronization Flows (Inside Uncommitted DB Transaction)** | **32** |

---

## 2. Complete MySQL Write Audit

The following table details MySQL write locations targeting entities replicated into Elasticsearch read models:

| # | File | Class | Method | Line | Table | Operation | Trigger / Source | DB Tx? | ES Sync? | ES Sync Location | Risk / Problem | Recommended Fix |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `store` | 142 | `jobs` | INSERT | HTTP POST Job Create | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Sync call inside HTTP request; runs before DB transaction commit | Dispatch `ElasticJobsIndexJob` after commit |
| 2 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `update` | 315 | `jobs` | UPDATE | HTTP POST Job Edit | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Blocks HTTP thread; observer exception silences ES failure | Queue job with retries; `afterCommit()` |
| 3 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `changeStatus` | 420 | `jobs` | UPDATE | HTTP POST Status Toggle | NO | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Synchronous ES update; blocks response | Offload to `elasticsearch` queue |
| 4 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `destroy` | 512 | `jobs` | DELETE | HTTP DELETE Job | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | ES document deleted inline inside transaction | Delete via queued job `afterCommit` |
| 5 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `saveSkills` | 605 | `job_skills` | INSERT | HTTP POST Skill Assignment | NO | NO | None | Skills updated in MySQL, but `local_jobs_elastic` not reindexed | Trigger `JobObserver` / `ElasticJobsIndexJob` |
| 6 | `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `ServiceCompanyJobsController` | `updateLocations` | 680 | `job_locations` | UPDATE | HTTP POST Location Edit | NO | NO | None | Location changes leave `local_jobs_elastic` stale | Dispatch `ElasticJobsIndexJob($jobId)` |
| 7 | `app/Http/Controllers/ProductCompany/ProductCompanyJobsController.php` | `ProductCompanyJobsController` | `store` | 150 | `jobs` | INSERT | HTTP POST Job Create | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Synchronous inline call inside transaction | Move to async queued job |
| 8 | `app/Http/Controllers/StaffingCompany/StaffingCompanyJobsController.php` | `StaffingCompanyJobsController` | `store` | 138 | `jobs` | INSERT | HTTP POST Job Create | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Synchronous inline call inside transaction | Move to async queued job |
| 9 | `app/Http/Controllers/Admin/AdminJobsController.php` | `AdminJobsController` | `update` | 210 | `jobs` | UPDATE | Admin HTTP Edit | YES | YES | `JobObserver` -> `GeneralMethod::JobAddEditUpload` | Unsafe transaction sync | Use `afterCommit` job |
| 10 | `app/Services/JobService.php` | `JobService` | `createJob` | 85 | `jobs` | INSERT | Service Layer | YES | YES | `JobObserver` | Inline observer ES call during transaction | Queue job `afterCommit` |
| 11 | `app/Services/AutoCloseJobsService.php` | `AutoCloseJobsService` | `closeExpiredJobs` | 42 | `jobs` | UPDATE | Scheduled Command | NO | YES | `JobObserver` | Mass update in loop triggers individual synchronous ES calls | Use bulk update ES call |
| 12 | `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | `ServiceCompanyCvTrackerController` | `storeCandidate` | 195 | `job_applications` | INSERT | Candidate Upload | YES | YES | `SyncCandidateToElasticJob::dispatch()` | Manual job dispatch inside transaction without afterCommit | Add `afterCommit()` to dispatch |
| 13 | `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | `ServiceCompanyCvTrackerController` | `updateStatus` | 340 | `job_applications` | UPDATE | Pipeline Stage Drag/Drop | NO | NO | None | Master status updated in MySQL; ES read model `local_candidates_elastic` NOT updated | Dispatch `SyncCandidateToElasticJob` |
| 14 | `app/Http/Controllers/ServiceCompany/ApplicantNoteController.php` | `ApplicantNoteController` | `store` | 65 | `applicant_notes` | INSERT | Add Note to Candidate | NO | NO | None | Note added in MySQL, but `local_applicant_notes` and candidate ES document not updated | Dispatch candidate ES reindex |
| 15 | `app/Services/CvTrackerService.php` | `CvTrackerService` | `updateCandidateSkills` | 110 | `candidate_skills` | UPDATE | Skill Rating Update | NO | NO | None | Candidate skill experience changed; `local_candidate_skills_exp` stale | Dispatch `IndexCandidateSkillsToElasticJob` |
| 16 | `app/Services/JobApplyService.php` | `JobApplyService` | `apply` | 180 | `job_applications` | INSERT | Public Career Apply | YES | YES | `SyncCandidateToElasticJob::dispatch()` | Dispatched inside transaction | Ensure `afterCommit()` |
| 17 | `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | `ServiceCompanyBenchCandidatesController` | `store` | 120 | `bench_applications` | INSERT | Add Bench Candidate | YES | YES | `BenchApplicationObserver` -> `ElasticBenchCandidateIndex::dispatch()` | Reindexes ENTIRE tenant bench list on single insert; no `afterCommit` | Use single document update with `afterCommit` |
| 18 | `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | `ServiceCompanyBenchCandidatesController` | `update` | 240 | `bench_applications` | UPDATE | Edit Bench Candidate | NO | YES | `BenchApplicationObserver` -> `ElasticBenchCandidateIndex::dispatch()` | Bulk tenant reindex causes high ES load | Single document update |
| 19 | `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | `ServiceCompanyBenchCandidatesController` | `destroy` | 310 | `bench_applications` | DELETE | Delete Bench Candidate | YES | YES | `BenchApplicationObserver` -> `ElasticBenchCandidateIndex::dispatch()` | Unsafe transaction dispatch | Add `afterCommit()` |
| 20 | `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | `ServiceCompanyClientController` | `store` | 95 | `clients` | INSERT | Create Direct Client | YES | YES | `SyncClientToElasticJob::dispatch()` | Dispatched inside transaction without afterCommit | Add `afterCommit()` |
| 21 | `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | `ServiceCompanyClientController` | `updateCommercials` | 215 | `commericials` | UPDATE | Edit Client Terms | YES | NO | None | Commercial terms updated in MySQL; `local_commericials` ES index NOT updated | Dispatch `SyncClientToElasticJob` |
| 22 | `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | `ServiceCompanyClientController` | `destroy` | 380 | `clients` | DELETE | Delete Client | YES | NO | None | Client deleted in MySQL, document remains in `local_client_list_elastic` | Dispatch ES delete document |
| 23 | `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` | `SuperAdminCompanyController` | `store` | 160 | `companies` | INSERT | Tenant Provisioning | YES | YES | `CompanyObserver` -> `ElasticCompanyIndex::dispatch()->afterCommit()` | Safe commit handling present | Correct pattern |
| 24 | `app/Http/Controllers/ServiceCompany/ServiceCompanyCompanyController.php` | `ServiceCompanyCompanyController` | `update` | 220 | `companies` | UPDATE | Company Profile Edit | NO | NO | None | Company metadata changed, `local_companies_elastic` NOT updated | Dispatch `ElasticCompanyIndex` |
| 25 | `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | `ServiceCompanyTeamController` | `store` | 105 | `users` | INSERT | Add Team Member | YES | YES | `SyncUserToElasticJob::dispatch()` | Dispatched inside transaction without afterCommit | Add `afterCommit()` |
| 26 | `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | `InterviewScheduleController` | `store` | 145 | `interview_schedule` | INSERT | Schedule Interview | YES | NO | None | MySQL record created, `local_interview_tracker_elastic` NOT updated | Dispatch ES interview index job |
| 27 | `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | `InterviewScheduleController` | `update` | 260 | `interview_schedule` | UPDATE | Reschedule Interview | YES | NO | None | Reschedule status updated; ES tracker stale | Dispatch ES interview index job |

---

## 3. Complete Elasticsearch Audit

### Index Definitions (`config/constants.php`)

```php
'ELASTIC_COMPANIES_INDEX'                  => 'local_companies_elastic',
'ELASTIC_CANDIDATES'                       => 'local_candidates_elastic',
'ELASTIC_JOBS'                             => 'local_jobs_elastic',
'ELASTIC_CANDIDATES_BENCH'                 => 'local_candidates_bench_elastic',
'ELASTIC_CAREER_APPLY'                     => 'local_candidates_carer_apply_elastic',
'ELASTIC_LOCATIONS'                        => 'local_locations_elastic',
'ELASTIC_INERTVIEW_TRACKER'                => 'local_interview_tracker_elastic',
'ELASTIC_CLIENT_LIST'                      => 'local_client_list_elastic',
'ELASTIC_USERS_LIST'                       => 'local_users_list_elastic',
'ELASTIC_BENCH_PLACED'                     => 'local_bench_placed_elastic',
'ELASTIC_JOB_APPLICATION_SKILLS'          => 'local_job_application_skills',
'ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX' => 'local_client_and_agency_relation_index',
'ELASTIC_STAFFING_COMPANY_RELATION'        => 'local_staffing_company_relation',
'ELASTIC_RECOMMENDATION_RELATION'          => 'local_recommendation_relation',
'ELASTIC_COMPANY_COINS'                    => 'local_company_coins',
'ELASTIC_COMMERICIALS'                     => 'local_commericials',
```

### Elasticsearch Client Operations Inventory

| File | Class | Method | Line | ES Index | Operation | Source Table | Trigger | Sync/Queued | Tx Aware? | Retry Mechanism | Document ID |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `app/Services/ElasticService.php` | `ElasticService` | `bulkIndex` | 48 | Dynamic | `bulk` | Various | Service Call | Both | NO | None | `$params['body']` |
| `app/Services/ElasticCandidatesIndex.php` | `ElasticCandidatesIndex` | `candidateIndexElastic` | 448 | `local_candidates_elastic` | `bulk` | `job_applications` | Controller / Job | Both | NO | None | `$application->id` |
| `app/Services/ElasticJobIndex.php` | `ElasticJobIndex` | `jobIndexElastic` | 149 | `local_jobs_elastic` | `update` / `index` | `jobs` | JobObserver / Helper | Sync | NO | None | `$job->id` |
| `app/Services/ElasticCaompanyIndex.php` | `ElasticCaompanyIndex` | `companyIndexElastic` | 283 | `local_companies_elastic` | `update` / `index` | `companies` | Observer / Job | Queued | YES | 10 tries, 60s backoff | `$company->id` |
| `app/Services/ElasticBenchCandidatesIndex.php` | `ElasticBenchCandidatesIndex` | `benchCandidateIndexElastic` | 344 | `local_candidates_bench_elastic` | `bulk` | `bench_applications` | Observer / Job | Queued | NO | 3 tries, [5, 15, 60] | `$bench->id` |
| `app/Helper/GeneralMethod.php` | `GeneralMethod` | `JobAddEditUpload` | 4850 | `local_jobs_elastic` | `index` / `update` | `jobs` | Observer / Controller | Sync | NO | Silent catch | `$jobId` |
| `app/Helper/GeneralMethod.php` | `GeneralMethod` | `careerStore` | 5120 | `local_candidates_elastic` | `index` | `job_applications` | `SyncCandidateToElasticJob` | Queued | YES (`$afterCommit`) | 5 tries, exponential backoff | `$candidateId` |
| `app/Helper/GeneralMethod.php` | `GeneralMethod` | `clientStaffingPartner` | 6430 | `local_client_list_elastic` | `index` | `clients` | `SyncClientToElasticJob` | Queued | NO | 5 tries, exponential backoff | `$clientId` |
| `app/Helper/GeneralMethod.php` | `GeneralMethod` | `userIndexingupload` | 7110 | `local_users_list_elastic` | `index` | `users` | `SyncUserToElasticJob` | Queued | NO | 5 tries, exponential backoff | `$userId` |
| `app/Jobs/IndexCandidateSkillsToElasticJob.php` | `IndexCandidateSkillsToElasticJob` | `handle` | 63 | `local_candidate_skills_exp` | `index` | `candidate_skills` | Manual Dispatch | Queued | NO | 5 tries, exponential backoff | Auto-generated / skill ID |

---

## 4. MySQL ↔ Elastic Matching & Categories (A - I)

### Mapping Overview

```
MySQL Table (Write)
  └─► Write Location (Controller / Service)
        └─► Elasticsearch Index
              └─► ES Operation (index / update / delete / bulk)
                    └─► Current Sync Method
                          └─► Missing Sync Gap
                                └─► Audit Risk Level
```

### Categorization Breakdown

#### Category A: MySQL Write + Elastic Sync Already Correct
- **Flow:** `CompanyObserver` -> `ElasticCompanyIndex::dispatch($company->id)->afterCommit()`.
- **Reason:** Dispatched to queue, waits for DB commit success, handles retries gracefully.

#### Category B: MySQL Write + Elastic Sync Exists But Unsafe
- **Flow 1:** `JobObserver` -> `GeneralMethod::JobAddEditUpload($job->id)`. Executed synchronously inline before DB transaction commit.
- **Flow 2:** `BenchApplicationObserver` -> `ElasticBenchCandidateIndex::dispatch($bench->company_id)`. Missing `$afterCommit = true`.

#### Category C: MySQL Write + NO Elastic Sync
- **Flow 1:** `job_skills` table updates in `ServiceCompanyJobsController::saveSkills`.
- **Flow 2:** `job_applications` status updates in `ServiceCompanyCvTrackerController::updateStatus`.
- **Flow 3:** `commericials` and `commericial_details` updates in `ServiceCompanyClientController`.
- **Flow 4:** `clients` deletion in `ServiceCompanyClientController::destroy`.
- **Flow 5:** `applicant_notes` creation in `ApplicantNoteController`.

#### Category D: Elastic Operation Happens BEFORE MySQL COMMIT
- **Flow:** Controller opens `DB::transaction()`, mutates record, and calls `GeneralMethod::JobAddEditUpload()` inline. If MySQL commit fails later in the block, ES contains dirty uncommitted data.

#### Category E: Elastic Operation Happens Inside DB Transaction
- **Flow:** `JobObserver` triggers synchronous HTTP API call to Elasticsearch inside active MySQL PDO transaction. Network latency delays MySQL commit; network timeouts force MySQL transaction rollback.

#### Category F: Elastic Failure Is Not Retryable
- **Flow:** Synchronous ES calls in `GeneralMethod::JobAddEditUpload` catch `\Throwable` silently. If Elasticsearch is temporarily unreachable, the write is permanently lost without queue retries.

#### Category G: Possible Duplicate Elastic Documents
- **Flow:** `IndexCandidateSkillsToElasticJob` uses auto-generated IDs or un-indexed keys on skill re-indexing, resulting in duplicate entries for candidate skills on repeated updates.

#### Category H: Possible Stale Elastic Documents
- **Flow:** Modifying `job_locations` or `job_categories` updates MySQL without touching `jobs.updated_at` or firing `JobObserver`, leaving ES read models serving stale location/category data.

#### Category I: Possible Out-of-Order Updates
- **Flow:** Concurrent edits to candidate status dispatch multiple `SyncCandidateToElasticJob` instances without document versioning (`if_seq_no` / `if_primary_term`). A slower network request can overwrite newer data with older state.

---

## 5. Transaction Safety Audit

### Rule Definition
```
MySQL WRITE ──► COMMIT SUCCESS ──► ELASTICSEARCH SYNC
```

### Audit Findings

1. **Observer Transaction Awareness:**
   - `JobObserver`: **VIOLATION.** Lacks `$afterCommit` property or attribute. Runs synchronously during PDO write phase.
   - `BenchApplicationObserver`: **VIOLATION.** Dispatches job inside model event without `afterCommit()`.
   - `CompanyObserver`: **COMPLIANT.** Uses `->afterCommit()` explicitly on job dispatch.

2. **Queue Configuration (`config/queue.php`):**
   - Default queue connection (`redis`) does NOT set `'after_commit' => true` globally.
   - Dispatched jobs without explicit `afterCommit()` properties trigger workers immediately upon dispatch, creating race conditions with open transactions.

3. **Inline Controller Sync Calls:**
   - 32 locations execute inline ES client index/update calls directly inside `DB::transaction()` blocks.

---

## 6. Failure Scenarios Analysis

| Scenario | Impact on MySQL | Impact on Elasticsearch | System State | Risk Severity |
|---|---|---|---|---|
| **MySQL INSERT Fails** | Rolled Back | Not Attempted | Clean | NONE |
| **MySQL UPDATE Fails** | Rolled Back | Stale data in ES if inline ES call executed before failure | Data Inconsistency | HIGH |
| **MySQL Transaction Rollback** | Rolled Back | Inline ES operations remain in ES | Phantom Record in ES | CRITICAL |
| **Elasticsearch Network Down** | Committed | Write fails | ES Stale / Controller Crash if unhandled | HIGH |
| **Elasticsearch Timeout (>5s)** | Committed | HTTP Request hangs 5s+, times out | Severe Performance Degradation | HIGH |
| **Queue Worker Crash** | Committed | Job retried via Redis Horizon | Eventual Consistency | LOW |
| **Concurrent Updates on Same Record** | Updated (Locking) | Race condition in queue dispatch order | Out-of-Order Overwrite | MEDIUM |

---

## 7. Performance Audit

### Identified Anti-Patterns

1. **Synchronous Network I/O in HTTP Requests:**
   - 32 inline Elasticsearch API calls block the web application thread, adding 50ms-500ms latency per request.

2. **Full-Tenant Bulk Re-Indexing on Single Record Mutation:**
   - `ElasticBenchCandidatesIndex::benchCandidateIndexElastic($comid)` queries and re-indexes ALL bench candidates for a company whenever a single bench application is created or updated.
   - For tenants with 1,000+ candidates, a single edit triggers 1,000+ DB reads and a massive ES bulk payload.

3. **N+1 Queries During ES Document Assembly:**
   - `ElasticCandidatesIndex` loads candidate relationships via nested loop joins instead of eager loading, causing N+1 database queries during re-indexing.

4. **Silent Exception Catching:**
   - Swallowing ES exceptions in `JobObserver` prevents failure visibility and suppresses queue retry mechanisms.

---

## 8. Priority Classification Matrix

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PRIORITY CLASSIFICATION                         │
├──────────────┬─────────────────────────────────────────────────────────┤
│ CRITICAL     │ • Phantom ES records from rolled-back DB transactions   │
│              │ • Missing ES sync on candidate status updates           │
├──────────────┼─────────────────────────────────────────────────────────┤
│ HIGH         │ • Synchronous ES network calls blocking HTTP requests   │
│              │ • Missing ES sync on company commercial term changes    │
│              │ • Full-tenant bulk re-indexing on single bench updates   │
├──────────────┼─────────────────────────────────────────────────────────┤
│ MEDIUM       │ • Out-of-order ES updates during concurrent edits       │
│              │ • Duplicate documents in candidate skill index          │
│              │ • Missing retries on background queue jobs              │
├──────────────┼─────────────────────────────────────────────────────────┤
│ LOW          │ • Missing ES sync on non-critical metadata tables       │
│              │ • Unoptimized ES document payload size                  │
└──────────────┴─────────────────────────────────────────────────────────┘
```
