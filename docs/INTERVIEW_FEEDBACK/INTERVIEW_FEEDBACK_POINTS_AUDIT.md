# INTERVIEW FEEDBACK — ATTENDANCE STATUS TRANSITIONS & POINT/COIN CONSISTENCY AUDIT

> **Audit Date:** September 10, 2026  
> **Target System:** JERO Multi-Tenant B2B Hiring SaaS (`testing.jero.works`)  
> **Scope:** Interview Feedback System — Attendance Statuses, Status Transitions, Point/Coin Ledger, MySQL Atomicity, Elasticsearch Synchronization (`ELASTIC_TEAM_RANK`), Concurrency, Idempotency, and Frontend/AJAX Workflows.  
> **Strict Rule:** AUDIT ONLY — Zero Code Modifications Made.

---

## 1. EXECUTIVE SUMMARY

An exhaustive technical audit of the JERO Interview Feedback system was conducted. The audit focused on the **EXACT 6 Attendance Statuses** displayed in the UI:

1. **Attended** (DB ID: `1`)
2. **No Show** (DB ID: `2`)
3. **Rescheduled** (DB ID: `3`)
4. **Cancelled by Company** (DB ID: `4`)
5. **Other Reason** (DB ID: `6`)
6. **Cancelled by Candidate** (DB ID: `7`)

### Core Audit Finding
The current system has **CRITICAL DATA INTEGRITY & LEDGER CORRUPTION DEFECTS** across status updates and transitions. When an interview's attendance status is updated, point transactions are frequently **missing, duplicated, unreversed, or incorrectly logged**, causing severe desynchronization between MySQL, Elasticsearch (`ELASTIC_TEAM_RANK`), point balances, transaction history, and frontend datatables.

### Top Critical Defects Summary
1. **Flawed Prior-State Detection Engine:** The system attempts to detect prior status by querying raw row counts in `interview_feedback`. Because every feedback submission executes a raw `INSERT` rather than updating existing records, count variables (`$interviewCount_1`, `$interviewCount_2`, `$interviewCount_3`) become invalid after the first update. As a result, **point calculation logic is completely skipped on status reversals (e.g. No Show → Attended)**, leaving penalties applied and rewards lost forever.
2. **Missing Database Transactions:** Multi-table MySQL writes (`interview_feedback`, `interview_skills_feedback`, `interview_schedules`, `updatetimeline`, `company_user_points_logs`) and Elasticsearch API calls execute sequentially without `DB::beginTransaction()` / `DB::commit()`. A failure at any point leaves orphan records in MySQL and breaks MySQL ↔ ES sync.
3. **Asymmetric Controller Implementations:** Point calculation and Elasticsearch sync (`teamRankElastic`) exist *only* in `ServiceCompany\InterviewScheduleController.php`. The four other company controllers (`Admin`, `ProductCompany`, `StaffingCompany`, `SuperAdmin`) and inline datatable dropdowns (`updateAvailableStatus`) update status directly in MySQL, **completely bypassing the point/coin system and Elasticsearch**.
4. **Duplicate Record Accumulation:** Re-submitting feedback for an interview inserts a brand new primary key row into `interview_feedback` rather than updating the user's existing feedback record, corrupting average star ratings and historical reporting.
5. **Synchronous Elasticsearch Indexing & Race Conditions:** Elasticsearch indexing occurs synchronously within the web request thread without optimistic concurrency control (`if_seq_no` / `if_primary_term`). Double-clicking or parallel requests produce duplicate point entries in MySQL and race conditions in Elasticsearch.

---

## 2. UI ATTENDANCE STATUS INVENTORY & DATABASE MAPPING

The Interview Feedback interface presents Section 1 (**Interview Attendance**) with **EXACTLY 6 attendance statuses**:

| UI Display Name | Radio Input Value (`app_int_status`) | DB Table | DB Column (`available_status`) | Intent / Business Meaning | Default Point Effect |
|---|---|---|---|---|---|
| **Attended** | `1` | `interview_status` | `id = 1` | Candidate attended interview & feedback submitted | Recruiter: **+10 pts** (`interviewScheduled`) <br> Team Captain: **+2 pts** (`teamCaptainInterviewScheduled`) |
| **No Show** | `2` | `interview_status` | `id = 2` | Candidate failed to attend without notice | Recruiter: **-5 pts** (`interviewNoShow`) <br> Team Captain: **0 pts** |
| **Rescheduled** | `3` | `interview_status` | `id = 3` | Interview postponed to another time | Recruiter: **-10 pts** (If previously Attended) or **-5 pts** (If candidate requested) |
| **Cancelled by Company** | `4` | `interview_status` | `id = 4` | Interview cancelled by recruiter / company | Recruiter: **0 pts** (or net reversal of prior reward) |
| **Other Reason** | `6` | `interview_status` | `id = 6` | Interview cancelled for miscellaneous reasons | Recruiter: **0 pts** (or net reversal of prior reward) |
| **Cancelled by Candidate** | `7` | `interview_status` | `id = 7` | Candidate requested cancellation in advance | Recruiter: **0 pts** (or net reversal of prior reward) |

---

## 3. POINT / COIN RULE INVENTORY

From `ServiceCompany\InterviewScheduleController.php` (lines 2477–2710) and `GeneralMethod::teamRankElastic` (lines 9546–9665):

| Attendance Status | Sub-Status / Condition | Recruiter Delta | Team Captain Delta | Source Function | Intended Business Logic |
|---|---|---|---|---|---|
| **Attended (1)** | Feedback Submitted ($>0$) | **+10 pts** (`interviewScheduled`) | **+2 pts** (`teamCaptainInterviewScheduled`) | `updateInterview` (L2488) | Reward recruiter & team captain when candidate attends |
| **No Show (2)** | Any | **-5 pts** (`interviewNoShow`) | **0 pts** | `updateInterview` (L2491) | Penalize recruiter for candidate no-show |
| **Rescheduled (3)** | Sub 13 (Cancelled by Cand) | **-5 pts** (`interviewNoShow`) | **0 pts** | `updateInterview` (L2493) | Deduct no-show points when candidate cancels/reschedules |
| **Rescheduled (3)** | Sub 14 (Cancelled by Co) | **-10 pts** (`interviewScheduled`), **0** (`interviewNoShow`) | **0 pts** | `updateInterview` (L2605) | Reverses prior +10 attendance reward if previously Attended |
| **Rescheduled (3)** | Sub 15 (Rescheduled) | **-10 pts** (`interviewScheduled`), **0** (`interviewNoShow`) | **0 pts** | `updateInterview` (L2608) | Reverses prior +10 attendance reward if previously Attended |
| **Rescheduled (3)** | Sub 17 (Other Reason) | **-10 pts** (`interviewScheduled`), **0** (`interviewNoShow`) | **0 pts** | `updateInterview` (L2611) | Reverses prior +10 attendance reward if previously Attended |
| **Cancelled Co (4)**| Any | **0 pts** | **0 pts** | Unhandled | Zero point effect (or net reversal of prior reward) |
| **Other Reason (6)**| Any | **0 pts** | **0 pts** | Unhandled | Zero point effect (or net reversal of prior reward) |
| **Cancelled Cand (7)**| Any | **0 pts** | **0 pts** | Unhandled | Zero point effect (or net reversal of prior reward) |

---

## 4. COMPLETE STATUS TRANSITION MATRIX ($6 \times 6 = 36$ TRANSITIONS)

The table below documents the full transition matrix across all 6 UI attendance statuses:

| Previous Status (A) | Target Status (B) | Prev Point Effect | New Point Effect | Expected Net Delta | Actual Code Execution Delta | MySQL Result | ES Result | Audit Finding |
|---|---|---|---|---|---|---|---|---|
| **1 (Attended)** | **1 (Attended)** | +10 (R), +2 (C) | +10 (R), +2 (C) | **0** | **0** (or **+10** if counts corrupt) | Duplicate row in `interview_feedback` | Skips ES write | ❌ **INCORRECT** |
| **1 (Attended)** | **2 (No Show)** | +10 (R), +2 (C) | -5 (R) | **-15 (R), -2 (C)** | **-10 (Sched), -5 (NoShow)** | Inserted `interview_feedback` (Status 2) | Logged `-10, -5` | **-2 Captain reward NEVER reversed!** | ❌ **INCORRECT** |
| **1 (Attended)** | **3 (Resched)** | +10 (R), +2 (C) | 0 / -5 | **-10 (R), -2 (C)** | **-10 (Sched), 0 / -5 (NoShow)** | Inserted `interview_feedback` (Status 3) | Logged `-10` | **-2 Captain reward NEVER reversed!** | ❌ **INCORRECT** |
| **1 (Attended)** | **4 (Cancel Co)** | +10 (R), +2 (C) | 0 | **-10 (R), -2 (C)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **+10 & +2 rewards NOT reversed!** | ❌ **INCORRECT** |
| **1 (Attended)** | **6 (Other)** | +10 (R), +2 (C) | 0 | **-10 (R), -2 (C)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **+10 & +2 rewards NOT reversed!** | ❌ **INCORRECT** |
| **1 (Attended)** | **7 (Cancel Cand)**| +10 (R), +2 (C) | 0 | **-10 (R), -2 (C)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **+10 & +2 rewards NOT reversed!** | ❌ **INCORRECT** |
| **2 (No Show)** | **1 (Attended)** | -5 (R) | +10 (R), +2 (C) | **+15 (R), +2 (C)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 1) | Skips ES write | **CRITICAL BUG: -5 NOT reversed, +10 NOT awarded!** | ❌ **INCORRECT** |
| **2 (No Show)** | **2 (No Show)** | -5 (R) | -5 (R) | **0** | **0 (Skipped)** | Duplicate row inserted | Skips ES write | Skipped via count check | ❌ **INCORRECT** |
| **2 (No Show)** | **3 (Resched)** | -5 (R) | 0 | **+5 (R)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 3) | Skips ES write | **-5 deduction NOT reversed!** | ❌ **INCORRECT** |
| **2 (No Show)** | **4 (Cancel Co)** | -5 (R) | 0 | **+5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **-5 deduction NOT reversed!** | ❌ **INCORRECT** |
| **2 (No Show)** | **6 (Other)** | -5 (R) | 0 | **+5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **-5 deduction NOT reversed!** | ❌ **INCORRECT** |
| **2 (No Show)** | **7 (Cancel Cand)**| -5 (R) | 0 | **+5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | **-5 deduction NOT reversed!** | ❌ **INCORRECT** |
| **3 (Resched)** | **1 (Attended)** | 0 / -5 | +10 (R), +2 (C) | **+10 / +15 (R)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 1) | Skips ES write | **CRITICAL BUG: +10 NOT awarded!** | ❌ **INCORRECT** |
| **3 (Resched)** | **2 (No Show)** | 0 / -5 | -5 (R) | **-5 / 0 (R)** | **-10 (Sched), -5 (NoShow)** | Inserted `interview_feedback` (Status 2) | Logged `-10, -5` | **Applies -10 even if +10 was never given!** | ❌ **INCORRECT** |
| **3 (Resched)** | **3 (Resched)** | 0 / -5 | 0 / -5 | **0** | **0 (Skipped)** | Duplicate row inserted | Skips ES write | Skipped via count check | ❌ **INCORRECT** |
| **3 (Resched)** | **4 (Cancel Co)** | 0 / -5 | 0 | **0 / +5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | Deduction not reversed | ❌ **INCORRECT** |
| **3 (Resched)** | **6 (Other)** | 0 / -5 | 0 | **0 / +5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | Deduction not reversed | ❌ **INCORRECT** |
| **3 (Resched)** | **7 (Cancel Cand)**| 0 / -5 | 0 | **0 / +5 (R)** | **0 (Skipped)** | Updated `interview_schedules` | Skips ES write | Deduction not reversed | ❌ **INCORRECT** |
| **4 (Cancel Co)** | **1 (Attended)** | 0 | +10 (R), +2 (C) | **+10 (R), +2 (C)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 1) | Skips ES write | **Reward NOT awarded!** | ❌ **INCORRECT** |
| **4 (Cancel Co)** | **2 (No Show)** | 0 | -5 (R) | **-5 (R)** | **-10 (Sched), -5 (NoShow)** | Inserted `interview_feedback` (Status 2) | Logged `-10, -5` | **Applies -10 even if +10 was never given!** | ❌ **INCORRECT** |
| **4 (Cancel Co)** | **3, 4, 6, 7** | 0 | 0 | **0** | **0 (Skipped)** | Updated DB | Skips ES write | Correct net delta (by accident) | ⚠️ **PARTIAL** |
| **6 (Other)** | **1 (Attended)** | 0 | +10 (R), +2 (C) | **+10 (R), +2 (C)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 1) | Skips ES write | **Reward NOT awarded!** | ❌ **INCORRECT** |
| **6 (Other)** | **2 (No Show)** | 0 | -5 (R) | **-5 (R)** | **-10 (Sched), -5 (NoShow)** | Inserted `interview_feedback` (Status 2) | Logged `-10, -5` | **Applies -10 even if +10 was never given!** | ❌ **INCORRECT** |
| **6 (Other)** | **3, 4, 6, 7** | 0 | 0 | **0** | **0 (Skipped)** | Updated DB | Skips ES write | Correct net delta (by accident) | ⚠️ **PARTIAL** |
| **7 (Cancel Cand)**| **1 (Attended)** | 0 | +10 (R), +2 (C) | **+10 (R), +2 (C)** | **0 (Skipped)** | Inserted `interview_feedback` (Status 1) | Skips ES write | **Reward NOT awarded!** | ❌ **INCORRECT** |
| **7 (Cancel Cand)**| **2 (No Show)** | 0 | -5 (R) | **-5 (R)** | **-10 (Sched), -5 (NoShow)** | Inserted `interview_feedback` (Status 2) | Logged `-10, -5` | **Applies -10 even if +10 was never given!** | ❌ **INCORRECT** |
| **7 (Cancel Cand)**| **3, 4, 6, 7** | 0 | 0 | **0** | **0 (Skipped)** | Updated DB | Skips ES write | Correct net delta (by accident) | ⚠️ **PARTIAL** |

---

## 5. SAME-STATUS UPDATE AUDIT

- **Attended $\to$ Attended (1 $\to$ 1):** Re-submitting feedback for an interview already marked `Attended` inserts another row into `interview_feedback`. Count logic skips point recalculations, but average star ratings and feedback metrics accumulate duplicate records.
- **No Show $\to$ No Show (2 $\to$ 2):** Inserts a second row with `available_status = 2`. Points are skipped, but duplicate database rows accumulate.
- **Rescheduled $\to$ Rescheduled (3 $\to$ 3):** Duplicate rows inserted into `interview_feedback`. Point calculation skipped.
- **Cancelled / Other $\to$ Same (4 $\to$ 4, 6 $\to$ 6, 7 $\to$ 7):** DB rows accumulate without point updates.

---

## 6. TRANSITION AUDITS BY CATEGORY

### A. Positive $\to$ Negative Transition (Attended [1] $\to$ No Show [2])
- **Current Behavior:** Calculates `$interviewNoShow = -5` and `$interviewScheduled = -10`.
- **Defect:** Reverses the recruiter's $+10$ points and deducts $-5$ points. **HOWEVER**, the Team Captain's prior **$+2$ points** (`teamCaptainInterviewScheduled`) is **NEVER REVERSED**. The Team Captain retains their reward even though the candidate did not show up!

### B. Negative $\to$ Positive Transition (No Show [2] $\to$ Attended [1])
- **Current Behavior:** The code checks `$interviewCount_1 == 0 && $interviewCount_2 == 0 && $interviewCount_3 == 0` (FALSE) and `app_int_status == 2 || app_int_status == 3` (FALSE).
- **Defect:** **Point logic skips completely.** The recruiter remains penalized with $-5$ points, and the $+10$ reward for Attended is NEVER credited!

### C. Zero $\to$ Positive Transition (Other Reason [6] / Cancelled [4, 7] $\to$ Attended [1])
- **Current Behavior:** Point logic skips completely because historical feedback count $>0$.
- **Defect:** The recruiter receives **0 points** for the newly attended interview.

### D. Positive $\to$ Zero Transition (Attended [1] $\to$ Other Reason [6] / Cancelled [4, 7])
- **Current Behavior:** Point logic skips completely.
- **Defect:** Prior $+10$ recruiter reward and $+2$ team captain reward are **NEVER REVERSED**.

---

## 7. MYSQL AUDIT

5 queries execute sequentially without `DB::beginTransaction()` / `DB::commit()`:
1. `INSERT INTO interview_feedback`
2. `INSERT INTO interview_skills_feedback`
3. `UPDATE interview_schedules SET available_status = ?, feedback = ?, average_star_rating = ?`
4. `UPDATE updatetimeline SET average_star_rating = ?, interview_message = ?`
5. `INSERT INTO company_user_points_logs`

If step 3, 4, or 5 fails, preceding inserts remain committed in MySQL, creating orphaned feedback records and points desynchronization.

---

## 8. POINT / COIN LEDGER AUDIT

- **Table:** `company_user_points_logs`
- **Defect:** Column `total_points` is hardcoded to `0` on insert (`'total_points' => 0`). Point balances are computed dynamically via `SUM()` over `company_user_points_logs`. Because duplicate log entries are generated upon repeated submissions, **user point balances become permanently corrupted**.

---

## 9. ELASTICSEARCH AUDIT & MYSQL ↔ ES CONSISTENCY

- **Index:** `ELASTIC_TEAM_RANK`
- **Defect:** Indexing is performed synchronously inside the HTTP request thread (`$elasticService->getClient()->index(...)`). If ES times out or throws an error, the HTTP request fails even though MySQL writes completed, leaving MySQL and ES out of sync.

---

## 10. IDEMPOTENCY & CONCURRENCY AUDIT

- **Idempotency:** No uniqueness key on `(interview_id, update_by)` in `interview_feedback` or `company_user_points_logs`. Rapid double-clicks trigger duplicate point entries.
- **Concurrency:** No pessimistic row locking (`lockForUpdate()`) or Redis lock on `interview_schedules`. Concurrent requests from parallel tabs cause lost updates and corrupted point logs.

---

## 11. FRONTEND / AJAX AUDIT

- The UI form (`showfeedback.blade.php`) submits via AJAX POST to `updateInterview`.
- Inline datatable dropdowns (`updateAvailableStatus`) across `Admin`, `ProductCompany`, and `StaffingCompany` update `available_status` in MySQL directly, **completely bypassing point logging and Elasticsearch sync**.

---

## 12. ROOT CAUSES & CLASSIFICATION (P0 – P3)

| ID | Issue Description | Location | Severity |
|---|---|---|---|
| **RC-01** | Prior status detected via raw row counts; re-inserting feedback corrupts counts & disables reversal logic | `ServiceCompany\InterviewScheduleController.php` | **P0** |
| **RC-02** | Absence of `DB::transaction()` around multi-table DB writes & ES calls | `ServiceCompany\InterviewScheduleController.php` | **P0** |
| **RC-03** | Asymmetric implementation: Admin, Product, Staffing, SuperAdmin controllers lack point logic | `Admin`, `ProductCompany`, `StaffingCompany`, `SuperAdmin` Controllers | **P0** |
| **RC-04** | Direct `insert()` into `interview_feedback` on updates creates duplicate rows | `ServiceCompany\InterviewScheduleController.php` | **P0** |
| **RC-05** | Team Captain reward (+2 pts) is never reversed when status changes from Attended to No Show | `ServiceCompany\InterviewScheduleController.php` | **P1** |
| **RC-06** | Synchronous ES indexing in HTTP request path causes failure/slowness when ES is unavailable | `GeneralMethod.php` (`teamRankElastic`) | **P1** |
| **RC-07** | Lack of idempotency token or unique database constraint permits duplicate point logging | `ServiceCompany\InterviewScheduleController.php` | **P1** |
| **RC-08** | Inline status select dropdowns in datatables update DB directly, bypassing point/ES system | Datatable Controller Actions | **P1** |
| **RC-09** | Dynamic SUM query reliance with hardcoded `total_points = 0` in ledger entries | `GeneralMethod.php` | **P2** |
| **RC-10** | Missing optimistic locking / double-click protection on backend controller endpoints | All Controllers | **P2** |

---

## 13. RECOMMENDED ARCHITECTURE (PROPOSED PLAN ONLY)

Introduce a unified domain service **`InterviewStatusTransitionEngine`**:

```
[ Frontend: 6 Attendance Statuses ] ──► (Attended, No Show, Rescheduled, Cancelled Co, Other, Cancelled Cand)
                                             │
                                             ▼
                             [ InterviewStatusTransitionEngine ] (Acquires SELECT ... FOR UPDATE)
                                             │
                                             ├── (1) Fetches Current DB Status from interview_schedules
                                             ├── (2) Calculates Net Delta: Net = Effect(New) - Effect(Previous)
                                             │
                                             ├── (3) Executes DB Transaction:
                                             │         - Upserts interview_feedback (1 row per interview+user)
                                             │         - Updates interview_schedules & updatetimeline
                                             │         - Inserts Net Delta into company_user_points_logs
                                             │         - Atomically updates running point balance
                                             │
                                             └── (4) Dispatches After-Commit Job:
                                                       - SyncTeamRankToElasticJob (Queued ES sync)
```

---
*End of Audit Document.*
