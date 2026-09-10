# INTERVIEW FEEDBACK — ATTENDANCE STATUS TRANSITIONS & POINT CONSISTENCY IMPLEMENTATION PLAN

> **Document Type:** Phase-Wise Technical Implementation Plan  
> **Status:** PROPOSED — WAITING FOR EXPLICIT USER APPROVAL ("APPROVED — IMPLEMENT")  
> **Target System:** JERO Multi-Tenant B2B Hiring SaaS (`testing.jero.works`)  
> **Scope:** The EXACT 6 Attendance Statuses: **Attended, No Show, Rescheduled, Cancelled by Company, Other Reason, Cancelled by Candidate**.  
> **Strict Rule:** NO CODE HAS BEEN MODIFIED. Implementation will begin ONLY after explicit user approval.

---

## OVERVIEW & GOALS

This implementation plan outlines a 13-phase engineering roadmap to fix all point/coin data integrity issues, missing reversals, race conditions, MySQL/Elasticsearch desynchronization, and multi-tenant inconsistencies across the exact 6 interview attendance statuses.

---

## PHASE 0 — Baseline & Pre-Flight Integrity Setup

### Objective
Establish database snapshot baselines, log audit telemetry, and create reference contracts for testing all 6 attendance statuses before introducing service changes.

### Files / Components
- [NEW] `database/migrations/2026_09_10_000001_add_idempotency_key_to_interview_feedback.php`
- `app/Helper/GeneralMethod.php`

### Exact Proposed Changes
1. Add a unique index migration for `interview_feedback` on `(interview_id, update_by)` to prevent duplicate feedback row generation.
2. Add a composite index on `company_user_points_logs` for `(company_id, user_id, candidate_id, created_at)`.
3. Capture baseline statistical snapshot of `company_user_points_logs` vs `interview_schedules` to identify existing historical discrepancies.

### Dependencies
None.

### Risks
Migration lock timeout on high-volume production tables. Mitigated by using `ALGORITHM=INPLACE, LOCK=NONE`.

### Tests
- `php artisan migrate:status`
- Validate migration rollback script cleanly drops indexes.

### Rollback
Run `php artisan migrate:rollback --step=1`.

### Acceptance Criteria
- Schema supports upserting feedback records per interview per user.
- Historical baseline point counts documented.

---

## PHASE 1 — Status Transition Engine (`InterviewStatusTransitionEngine`)

### Objective
Create a single, authoritative domain service that calculates net point deltas across all 6 UI attendance statuses based on actual status transitions $(A \to B)$ instead of counting historical DB rows.

### Files / Components
- [NEW] `app/Services/Interview/InterviewStatusTransitionEngine.php`
- [NEW] `app/Services/Interview/DTO/InterviewStatusTransitionResult.php`
- `app/Helper/GeneralMethod.php`

### Exact Proposed Changes
1. Build `InterviewStatusTransitionEngine` service with a method `processTransition(int $interviewId, int $newAttendanceStatus, int $newSubStatus, int $userId, int $companyId): InterviewStatusTransitionResult`.
2. Define a static matrix mapping all $36$ status transitions ($6 \times 6$) to their exact point deltas for Recruiter (`interviewScheduled`, `interviewNoShow`) and Team Captain (`teamCaptainInterviewScheduled`, `teamCaptainInterviewNoShow`).
3. Ensure net delta calculation accounts for prior status ($A$) stored in `interview_schedules.available_status` and calculates:
   $$\text{Net Delta} = \text{Effect}(B) - \text{Effect}(A)$$
4. Guarantee that transitioning from `Attended` (1) to `No Show` (2) reverses both the Recruiter (+10) and Team Captain (+2) rewards while applying the No Show deduction (-5).
5. Guarantee that transitioning from `No Show` (2) to `Attended` (1) reverses the No Show deduction (-5) and credits the full Attended reward (+10, +2).
6. Guarantee that transitioning to or from zero-point statuses (`Cancelled by Company` [4], `Other Reason` [6], `Cancelled by Candidate` [7]) accurately reverses prior positive or negative balances.

### Dependencies
Phase 0.

### Risks
Edge cases in legacy data where `interview_schedules.available_status` is `NULL` or 0. Default prior status to 0 (Pending).

### Tests
Unit tests in `tests/Unit/InterviewStatusTransitionEngineTest.php` testing all 36 status transition paths.

### Rollback
Remove service file; fall back to legacy controller logic.

### Acceptance Criteria
- $100\%$ accuracy on all 36 status transitions in isolated unit tests.
- Zero reliance on historical raw row counts in `interview_feedback`.

---

## PHASE 2 — Point/Coin Transaction Consistency & Atomic Ledger

### Objective
Ensure point transactions are recorded with running balance atomicity and proper ledger logging.

### Files / Components
- `app/Helper/GeneralMethod.php` (`teamRankElastic`)
- [NEW] `app/Services/Points/PointLedgerService.php`

### Exact Proposed Changes
1. Refactor `GeneralMethod::teamRankElastic` to delegate point insertion to `PointLedgerService`.
2. Replace hardcoded `'total_points' => 0` with calculated atomic user running balance:
   $$\text{New Balance} = \text{Previous Balance} + \text{Net Delta}$$
3. Ensure net delta updates are written atomically within the primary DB transaction.

### Dependencies
Phase 1.

### Risks
High-frequency point additions creating DB lock contention on user balance summary rows. Mitigated by using delta-logging with atomic `increment()` / `decrement()`.

### Tests
`tests/Feature/PointLedgerConsistencyTest.php`.

### Rollback
Revert `GeneralMethod::teamRankElastic` changes.

### Acceptance Criteria
- `company_user_points_logs` records true net deltas.
- Running balance matches calculated `SUM(delta)` at all times.

---

## PHASE 3 — Idempotency Protection

### Objective
Prevent duplicate point transactions and duplicate feedback records caused by AJAX retries, browser refreshes, or fast double-clicking.

### Files / Components
- `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php`
- `app/Http/Controllers/Admin/InterviewScheduleController.php`
- `app/Http/Controllers/ProductCompany/InterviewScheduleController.php`
- `app/Http/Controllers/StaffingCompany/InterviewScheduleController.php`
- `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`
- [NEW] `app/Http/Middleware/EnsureRequestIdempotency.php`

### Exact Proposed Changes
1. Introduce idempotency key check based on `X-Idempotency-Key` HTTP header or `(interview_id, update_by, request_hash)`.
2. In `updateInterview`, perform an **UPSERT** (`updateOrInsert`) on `interview_feedback` using composite key `['interview_id' => $id, 'update_by' => $userId]`.
3. Discard duplicate requests received within a 3-second window, returning cached HTTP 200 responses.

### Dependencies
Phase 0, Phase 1.

### Risks
Legitimate fast updates by a user being blocked if time window is too aggressive. Set window to 3 seconds with request hash matching.

### Tests
Submit identical payload concurrently via 5 parallel HTTP threads; verify exactly 1 feedback row and 1 point log created.

### Rollback
Disable middleware in route definition.

### Acceptance Criteria
- Repeated POST requests produce 0 duplicate records and 0 extra point logs.

---

## PHASE 4 — MySQL Transaction Safety & Multi-Table Atomicity

### Objective
Wrap all multi-table database operations for interview feedback into atomic MySQL transactions with row locking.

### Files / Components
- `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php`
- `app/Http/Controllers/Admin/InterviewScheduleController.php`
- `app/Http/Controllers/ProductCompany/InterviewScheduleController.php`
- `app/Http/Controllers/StaffingCompany/InterviewScheduleController.php`
- `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`

### Exact Proposed Changes
1. Wrap all writes in `updateInterview` in `DB::transaction(function () use (...) { ... })`.
2. Acquire pessimistic lock on target interview schedule:
   ```php
   $schedule = DB::table('interview_schedules')->where('id', $interviewId)->lockForUpdate()->first();
   ```
3. Ensure that if any insert/update fails, MySQL rolls back all writes (`interview_feedback`, `interview_skills_feedback`, `interview_schedules`, `updatetimeline`, `company_user_points_logs`).

### Dependencies
Phase 1, Phase 2.

### Risks
Deadlocks if lock acquisition order varies across endpoints. Standardize lock order: `interview_schedules` -> `job_applications` -> `company_user_points_logs`.

### Tests
Simulate DB exception on `updatetimeline` update; verify `interview_feedback` and `company_user_points_logs` are completely rolled back.

### Rollback
Remove `DB::transaction()` block wrapper.

### Acceptance Criteria
- Zero partial writes across all failure modes.

---

## PHASE 5 — Asynchronous Elasticsearch Consistency & Resilience

### Objective
Decouple Elasticsearch indexing from the synchronous HTTP request path to eliminate web request failures and guarantee eventual consistency.

### Files / Components
- `app/Helper/GeneralMethod.php` (`teamRankElastic`)
- [NEW] `app/Jobs/SyncTeamRankToElasticJob.php`

### Exact Proposed Changes
1. Remove synchronous `$elasticService->getClient()->index(...)` call from `GeneralMethod::teamRankElastic`.
2. Dispatch `SyncTeamRankToElasticJob` inside `DB::afterCommit()` callback:
   ```php
   DB::afterCommit(function () use ($payload) {
       SyncTeamRankToElasticJob::dispatch($payload)->onQueue('elastic-sync');
   });
   ```
3. In `SyncTeamRankToElasticJob`, implement optimistic concurrency locking using `if_seq_no` and `if_primary_term` with exponential backoff retries (3 attempts).

### Dependencies
Phase 4.

### Risks
Delay in Elasticsearch team rank updates. Queue processing latency is <200ms under standard Horizon load.

### Tests
Mock ES server failure; verify MySQL transaction commits cleanly, job retries 3 times, and dead-letter log receives failure if ES remains down.

### Rollback
Revert `teamRankElastic` to inline execution.

### Acceptance Criteria
- Web request succeeds even if Elasticsearch is temporarily offline.
- ES index becomes consistent within seconds of queue processing.

---

## PHASE 6 — Concurrency Protection & Multi-Tenant Lock Scope

### Objective
Protect against concurrent updates on the same interview from multiple browser tabs or recruiters.

### Files / Components
- `app/Services/Interview/InterviewStatusTransitionEngine.php`

### Exact Proposed Changes
1. Implement Redis atomic locks (`Cache::lock("interview:update:{$interviewId}", 5)`) at the start of status transition processing.
2. If lock cannot be acquired within 2 seconds, return user-friendly error response: `"Another update is currently processing for this interview. Please try again."`

### Dependencies
Phase 4.

### Risks
Stale locks if server crashes before releasing lock. Set lock TTL to 5 seconds.

### Tests
Execute two simultaneous requests for interview ID 100 via parallel curl processes; verify one succeeds and the other receives a locked response.

### Rollback
Disable Redis lock wrapping in engine.

### Acceptance Criteria
- Zero race conditions or lost updates under concurrent request load.

---

## PHASE 7 — Frontend / AJAX Reliability & Inline Status Unification

### Objective
Unify inline datatable dropdowns (`updateAvailableStatus`, `updateFeedbackStatus`) to route through the centralized `InterviewStatusTransitionEngine`.

### Files / Components
- `resources/views/service_company/interview-schedule/showfeedback.blade.php`
- `app/Http/Controllers/Admin/InterviewScheduleController.php` (`updateAvailableStatus`, `updateFeedbackStatus`)
- `app/Http/Controllers/ProductCompany/InterviewScheduleController.php` (`updateAvailableStatus`, `updateFeedbackStatus`)
- `app/Http/Controllers/StaffingCompany/InterviewScheduleController.php` (`updateAvailableStatus`, `updateFeedbackStatus`)

### Exact Proposed Changes
1. Refactor `updateAvailableStatus` and `updateFeedbackStatus` in all controllers to invoke `InterviewStatusTransitionEngine`.
2. Update `showfeedback.blade.php` submit button handler to generate and include a client-side idempotency UUID in the AJAX header.
3. Disable all status select dropdowns in datatables while an inline AJAX request is pending.

### Dependencies
Phase 1, Phase 3, Phase 7.

### Risks
Datatable refresh interrupting user selection. Mitigated by updating datatable row state in-place from AJAX response JSON.

### Tests
Change status via datatable dropdown in `Admin` view; verify point logs and Elasticsearch update correctly.

### Rollback
Restore legacy controller methods.

### Acceptance Criteria
- Inline status changes trigger identical point and ES updates as full feedback form submits.

---

## PHASE 8 — Queue & Event Reliability

### Objective
Ensure background mail dispatch and point event listeners are robust and fail-safe.

### Files / Components
- `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php`
- `app/Jobs/QueueNotificationMailService.php`

### Exact Proposed Changes
1. Move `QueueNotificationMailService::dispatch(...)` inside `DB::afterCommit()` to ensure notification emails are sent only after DB transaction commits successfully.
2. Add exception boundary around mail queue dispatch so mail server issues do not crash the HTTP response.

### Dependencies
Phase 4.

### Risks
None.

### Tests
Trigger feedback submission with invalid mail configuration; verify DB transaction commits and user receives success response while mail failure is logged.

### Rollback
Revert to inline dispatch.

### Acceptance Criteria
- Emails never dispatch for rolled-back transactions.

---

## PHASE 9 — Historical Data Validation & Data Reconciliation Tool

### Objective
Build a safe, read-only reconciliation CLI command to detect historical point discrepancies across existing production data without mutating data.

### Files / Components
- [NEW] `app/Console/Commands/AuditInterviewPointsCommand.php`

### Exact Proposed Changes
1. Create Artisan command `php artisan audit:interview-points {--company_id=} {--fix=false}`.
2. Command scans `interview_schedules`, calculates expected historical points for each candidate interview across the 6 attendance statuses, compares with `company_user_points_logs`, and outputs a detailed discrepancy report table.
3. Default mode is strictly READ-ONLY. `--fix` option is disabled until explicit user approval.

### Dependencies
Phase 1.

### Risks
High memory usage on large DB tables. Use chunking (`DB::table(...)->chunkById(500)`).

### Tests
Run command in staging environment and verify discrepancy report accuracy.

### Rollback
Delete command file.

### Acceptance Criteria
- Command accurately identifies all historical point discrepancies across tenant companies.

---

## PHASE 10 — Automated Test Suite

### Objective
Create a comprehensive test suite validating all 36 status transitions across the 6 UI attendance statuses, concurrency scenarios, and failure modes.

### Files / Components
- [NEW] `tests/Feature/InterviewFeedbackStatusTransitionTest.php`
- [NEW] `tests/Unit/InterviewStatusTransitionEngineTest.php`

### Exact Proposed Changes
1. Implement complete test matrix covering:
   - All 36 transitions ($6 \times 6$) for Attended, No Show, Rescheduled, Cancelled by Company, Other Reason, Cancelled by Candidate.
   - Repeated same-status saves.
   - Rapid double-clicks.
   - Network retries.
   - DB transaction rollback on failure.
   - ES fallback on queue failure.
2. Run test suite via `./vendor/bin/phpunit`.

### Dependencies
Phases 1-8.

### Risks
None.

### Tests
Run `php artisan test --filter=InterviewFeedbackStatusTransitionTest`.

### Rollback
N/A.

### Acceptance Criteria
- $100\%$ pass rate across all automated test cases.

---

## PHASE 11 — 10K Concurrent User Scalability & Performance Benchmarking

### Objective
Benchmark the refactored Interview Feedback system under simulated 10K concurrent recruiter load to verify performance budgets.

### Files / Components
- [NEW] `tests/Performance/InterviewFeedbackLoadTest.php`

### Exact Proposed Changes
1. Conduct load testing analyzing N+1 queries, DB lock times, Redis lock throughput, and memory consumption.
2. Verify:
   - Max response time $< 250\text{ms}$ at 99th percentile.
   - Zero DB deadlocks under 500 concurrent HTTP threads.
   - Zero duplicate point logs generated under race conditions.

### Dependencies
Phases 1-10.

### Risks
High CPU load on staging test server during benchmarking.

### Tests
Execute k6 / Apache JMeter performance script against test environment.

### Rollback
N/A.

### Acceptance Criteria
- Meets all performance metrics under 10K simulated user load.

---

## PHASE 12 — Production Rollout, Telemetry & Monitoring

### Objective
Safely deploy the refactored system to production with real-time monitoring and zero downtime.

### Files / Components
- Config & Horizon settings

### Exact Proposed Changes
1. Execute database migrations (`php artisan migrate`).
2. Deploy code changes to application servers.
3. Monitor Horizon queue `elastic-sync` for job throughput and error rates.
4. Monitor Sentry / Bugsnag for any unhandled exceptions in `InterviewStatusTransitionEngine`.

### Dependencies
Phases 0-11.

### Risks
Deployment edge cases. Covered by instant feature-flag fallback to legacy mode if required.

### Tests
Verify production telemetry dashboard.

### Rollback
Revert git release and disable feature flag.

### Acceptance Criteria
- Zero errors in production log; $100\%$ point ledger consistency.

---

## COMPLETE TEST MATRIX FOR THE 6 UI ATTENDANCE STATUSES

| Test Case ID | Initial Status | Target Status | Expected Recruiter Delta | Expected Captain Delta | Expected MySQL Action | Expected ES Action | Acceptance Criteria |
|---|---|---|---|---|---|---|---|
| **TC-01** | None (0) | Attended (1) | +10 (`interviewScheduled`) | +2 (`teamCaptain...`) | Upsert feedback, Insert log | Job queued | Points credited correctly |
| **TC-02** | None (0) | No Show (2) | -5 (`interviewNoShow`) | 0 | Upsert feedback, Insert log | Job queued | Deduction applied correctly |
| **TC-03** | Attended (1) | No Show (2) | -15 (-10 Sched, -5 NoShow) | -2 | Upsert feedback, Insert log | Job queued | Rewards reversed, deduction applied |
| **TC-04** | No Show (2) | Attended (1) | +15 (+5 Reversal, +10 Sched) | +2 | Upsert feedback, Insert log | Job queued | Deduction reversed, rewards credited |
| **TC-05** | Attended (1) | Rescheduled (3)| -10 | -2 | Upsert feedback, Insert log | Job queued | Reward reversed |
| **TC-06** | Rescheduled (3)| Attended (1) | +10 | +2 | Upsert feedback, Insert log | Job queued | Reward credited |
| **TC-07** | Attended (1) | Cancelled Co (4)| -10 | -2 | Upsert feedback, Insert log | Job queued | Reward reversed |
| **TC-08** | Attended (1) | Other Reason (6)| -10 | -2 | Upsert feedback, Insert log | Job queued | Reward reversed |
| **TC-09** | Attended (1) | Cancelled Cand (7)| -10 | -2 | Upsert feedback, Insert log | Job queued | Reward reversed |
| **TC-10** | Attended (1) | Attended (1) | 0 Delta | 0 | Feedback updated, 0 log | No ES write | Zero duplicate points |
| **TC-11** | Double Click | Attended (1) | +10 (Single Execution) | +2 (Single Execution) | 1 feedback, 1 log | Single job | Idempotency blocks 2nd click |
| **TC-12** | Race Condition| Attended (1) | Deterministic Last Win | Deterministic Last Win | Single winning transaction | Consistent ES | Redis lock prevents race condition |

---
*End of Implementation Plan.*
