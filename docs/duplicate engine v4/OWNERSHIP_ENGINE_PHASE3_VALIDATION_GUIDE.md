# Ownership Engine — Phase 3 Runtime Validation Guide

> **Purpose**: manual, staging-executable validation of Phase 3 (trigger integration, shadow
> mode) per docs/OWNERSHIP_ENGINE_DESIGN.md. Phase 4 does not begin until every test case
> below has been run and its outcome recorded. The staging environment has no automated
> testing framework installed, so this guide is written as manual steps + copy-pasteable SQL
> and shell commands, not PHPUnit.
>
> **What this guide is validating**: that the new Ownership Engine (Phase 2/3) runs
> alongside the legacy duplicate-detection logic without changing anything the legacy system
> or any user can observe — while itself producing correct, complete ownership data. It is
> **not** validating the correctness of every Master Tracker scenario end-to-end under
> concurrency (that's Phase 6) — this guide is scoped to "does the wiring work, safely."

---

## 0. Prerequisites

- SSH/shell access to staging, or an equivalent way to run `php artisan tinker` /
  `php artisan queue:work` / raw SQL against the staging database.
- Access to the Horizon dashboard (`/horizon`, confirmed installed — `laravel/horizon ^5.44`
  in `composer.json`) — Phase 3's queued job runs on the `redis` connection, `default` queue
  (confirmed in `config/horizon.php`; the job sets no `$queue` override).
- Read access to `storage/logs/laravel.log` (or wherever the `single`/`stack` channel writes
  in this environment).
- Two test user accounts: one **Company Recruiter** (a service company's own user, no
  third-party involvement) and one **Agency Recruiter** at a *different* company that has
  been given access to submit candidates into one of the Company Recruiter's client-owned
  jobs. If staging doesn't already have this pairing set up, it needs to before Section 3B.
- Three test companies with `companies.app_duplicate` set to `1` (Job mode), `2` (Company
  Overall mode), and `0` (disabled) respectively — see 0.1.

### 0.1 One-time setup SQL (adjust IDs to real staging data — do not run against production)

```sql
-- Confirm current app_duplicate values before changing anything:
SELECT id, company_name, app_duplicate, enable_cross_agency_duplicate FROM companies
WHERE id IN (/* your 3 test company IDs */);

-- Set the three test companies' modes (only if they don't already match what you need):
UPDATE companies SET app_duplicate = 1 WHERE id = /* job-mode test company id */;
UPDATE companies SET app_duplicate = 2 WHERE id = /* company-overall test company id */;
UPDATE companies SET app_duplicate = 0 WHERE id = /* disabled test company id */;
```

**Record the exact company/job/user IDs you use for each test case in the results sheet —
every SQL check below needs them.**

---

## 1. Test Cases: Company Reservation Triggers

Each of these calls `App\Jobs\AttemptOwnershipCompanyReservation::dispatch()`, which is
queued (not inline) — see §2 for how to confirm the job actually ran. All 7 sites are
guarded to fire only when the new row's `thirdparty_company_id = 0` (a Company Recruiter's
own submission, never an agency's).

| # | Trigger | Controller::method | Route (name) | UI path |
|---|---|---|---|---|
| 1A | Direct Upload | `ServiceCompanyJobApplicationController::store()` | `job-applications.store` (POST, resource route) | Job Applications → Upload Candidate |
| 1B | Google Sheet import | `ServiceCompanyJobApplicationController::googleSheetCandidateData()` | **Not found under any registered route in `routes/service.php` or `routes/web.php`** — flagged, not confirmed reachable. See 1B note below; treat as informational, not a required pass/fail case, until confirmed. | Unknown |
| 1C | Similar Candidates → Add to Job | `ServiceCompanyAppTrackerController::rec_candidate_assign()` branch 1 | `apptracker.rec_candidate_assign` (GET, `source_from_type=0`) | AppTracker → Similar Candidates → Add to Job |
| 1D | Internal Bench → Add to Job | same, branch 2 | same route, `source_from_type=1` | AppTracker → In-house Bench Talents → Add to Job |
| 1E | Agency Bench → Add to Job | same, branch 3 | same route, `source_from_type=2` | AppTracker → Agency Bench Talents → Add to Job |
| 1F | Career Page → Add to Job | same, branch 4 | same route, `source_from_type=3` | AppTracker → Career Page candidates → Add to Job |
| 1G | Copy to Other Job | `ServiceCompanyCvTrackerController::copy_current_selected_applicant()` | `cvtracker.copy_current_selected_applicant` (POST) | CV Tracker → select candidate → Copy to Other Job |

**1B note**: this method exists in the controller and was wired identically to the other six
(see docs/OWNERSHIP_ENGINE_DESIGN.md Phase 3 section), but no route registration could be
found pointing to it during this review. Before treating it as a real, testable trigger:
run `php artisan route:list | grep -i googlesheet` on staging and confirm whether a route
exists (possibly registered dynamically, or in a file not checked). If no route exists, this
is very likely dead/unreachable code and should be flagged back rather than tested — don't
spend time trying to exercise a UI path that may not exist.

### Test procedure (repeat for 1A, 1C–1G)

1. **Precondition**: log in as the Company Recruiter test user, on a job owned by the
   `app_duplicate = 1` test company (§0.1). Note the job's `id`.
2. **Action**: perform the UI action in the table above, creating one new candidate with a
   fresh, never-before-used email (e.g. `phase3-test-1a-<timestamp>@example.test`).
3. **Get the new row's id**:
   ```sql
   SELECT id, job_id, company_id, thirdparty_company_id, master_status_id, created_at
   FROM add_job_wise_condidates
   WHERE job_application_id = (SELECT id FROM job_applications WHERE email = 'phase3-test-1a-<timestamp>@example.test')
   ORDER BY id DESC LIMIT 1;
   ```
   Confirm `thirdparty_company_id = 0` — if not, you've triggered the wrong path; every
   Company Reservation test case must produce a company row.
4. **Wait for the queue to process** (a few seconds if a Horizon worker is running; run
   `php artisan horizon:status` to confirm a worker is active, or watch the Horizon
   dashboard's "default" queue for the job to move from Pending → Completed).
5. **Verify** — see §5 (Database Verification) for the exact expected `ownership_clusters`/
   `ownership_events`/cache-column state. In short, for the *first-ever* candidate with this
   identity in this scope, you should see: `ownership_state = 1` (owner) on the new row,
   one new `ownership_clusters` row, one new `ownership_events` row with `event_type =
   'OWNER_ESTABLISHED'` and `decision_rule = 'ER2'`.
6. **Verify legacy behavior is unaffected**: the candidate should appear normally in
   AppTracker/CV Tracker exactly as it would have before this deployment — same duplicate
   badge behavior (driven by `add_job_wise_condidates.duplicate`, untouched), same success
   message, no new UI element, no added latency the user would notice.

### Expected outcome (1A, 1C–1G)

| Check | Expected |
|---|---|
| Candidate created successfully, normal response/redirect | ✅ Unchanged from pre-Phase-3 behavior |
| `add_job_wise_condidates.thirdparty_company_id` on new row | `0` |
| Job dispatched to `default` queue | Visible in Horizon, moves to Completed within a few seconds |
| `ownership_state` on new row (first-ever identity in scope) | `1` (owner) |
| `ownership_clusters` row created | 1 new row, `owner_row_id` = new row's id, `decision_rule = 'ER2'` |
| `ownership_events` row created | 1 new row, `event_type = 'OWNER_ESTABLISHED'`, `decision_rule = 'ER2'`, `trigger_source = 'candidate_creation'` |
| Legacy duplicate badge / message | Identical to current production behavior |
| Log entries | None (success path logs nothing — only failures log, by design) |

---

## 2. Test Case: Agency Creation Does NOT Reserve Ownership

1. **Precondition**: log in as the Agency Recruiter test user, submitting into the client's
   job (the same job used in §1, or a fresh one — either is fine, since this test is about
   the row's `thirdparty_company_id`, not scope).
2. **Action**: have the agency submit a new candidate via whichever of the 7 paths in §1 the
   agency actually has access to (agencies typically only reach the client's job via Similar
   Candidates/Agency Bench/Upload-on-behalf-of-client, depending on staging's permission
   setup — use whichever is available).
3. **Verify**:
   ```sql
   SELECT id, thirdparty_company_id, ownership_state, ownership_cluster_id, ownership_evaluated_at
   FROM add_job_wise_condidates
   WHERE job_application_id = (SELECT id FROM job_applications WHERE email = 'phase3-test-2-<timestamp>@example.test')
   ORDER BY id DESC LIMIT 1;
   ```

### Expected outcome

| Check | Expected |
|---|---|
| `thirdparty_company_id` | `> 0` (the agency's company id) |
| `ownership_state` | `NULL` — no reservation attempt fires for agency rows at creation (ER1) |
| `ownership_cluster_id` | `NULL` |
| `ownership_evaluated_at` | `NULL` |
| No `ownership_clusters`/`ownership_events` rows created for this identity yet | Confirmed by querying `ownership_events WHERE subject_row_id = <this row's id>` → 0 rows |

If any of these show a non-null value, `attemptCompanyReservation()` was called on an agency
row somewhere — this would be a real bug (the engine itself should have thrown a
`LogicException`, caught and logged by the job, so also check the log for
`OwnershipEngine::attemptCompanyReservation failed` mentioning this row's id).

---

## 3. Test Case: Agency Sourcing Trigger (InReview → Sourcing)

This is the one **synchronous** call (`claimForAgencySourcing()`, not queued — per
docs/OWNERSHIP_ENGINE_DESIGN.md §13, this is the user-facing, latency-sensitive one, called
inline inside `client_stage_submit()`, gated on `thirdparty_company_id != 0`).

1. **Precondition**: the row from §2 (an agency-submitted candidate, currently at InReview,
   stage id 8), on a job owned by a company with `enable_cross_agency_duplicate` — check
   this too, since the *toast* half of the existing hook only fires if that's on, though the
   **new engine call fires regardless of that setting** (it's a separate, independent call —
   see docs §Phase 3, the block was added as a deliberately separate condition from the
   existing `resolveDuplicateOwnership()` hook, gated only on `master_status == 1 &&
   ...->master_status_id === 8 && thirdparty_company_id !== 0`).
2. **Action**: move the candidate from InReview to Sourcing via the normal ClientApp/CV
   Tracker "Change Candidate Stage" drawer (as the agency, or however this transition is
   normally triggered in the current UI).
3. **Verify immediately** (no queue wait needed — this call is synchronous):
   ```sql
   SELECT id, ownership_state, ownership_cluster_id, ownership_evaluated_at, master_status_id
   FROM add_job_wise_condidates WHERE id = <row id from §2>;

   SELECT * FROM ownership_clusters WHERE id = (
     SELECT ownership_cluster_id FROM add_job_wise_condidates WHERE id = <row id from §2>
   );

   SELECT * FROM ownership_events WHERE subject_row_id = <row id from §2> ORDER BY event_time;
   ```

### Expected outcome

| Check | Expected |
|---|---|
| Stage transition itself | Succeeds exactly as before — response unchanged, coin deduction (if applicable) unchanged |
| `master_status_id` after | `1` (Sourcing) |
| `ownership_state` (assuming no company row or earlier agency already owns this identity) | `1` (owner) — this row is now the owner via ER3 |
| `ownership_clusters.decision_rule` | `ER3` |
| `ownership_events` new row | `event_type = 'OWNER_ESTABLISHED'`, `decision_rule = 'ER3'`, `trigger_source = 'stage_transition'` |
| Existing `resolveDuplicateOwnership()`/`crossAgencyDuplicate` toast behavior | Byte-for-byte unchanged — confirm the response still only contains the pre-existing keys, plus `crossAgencyDuplicate` only if the legacy logic already added it |
| Response latency | See §7 |

**Also test the losing case**: repeat with a second contender (another agency or a Company
Recruiter row on the same identity+scope, created *after* this one already owns) to confirm
it gets marked `ownership_state = 2` (duplicate) against this row as owner — same DB checks,
expect `decision_rule` = `ER5` (if the loser is a company row) or `ER6` (if it's another
agency reaching Sourcing later).

---

## 4. Test Case: `app_duplicate = 0` Skips Evaluation Entirely

1. **Precondition**: use the test company with `app_duplicate = 0` (§0.1).
2. **Action**: create a candidate as a Company Recruiter on this company's job (any of the
   §1 trigger paths).
3. **Verify**:
   ```sql
   SELECT id, ownership_state, ownership_cluster_id, ownership_evaluated_at
   FROM add_job_wise_condidates
   WHERE job_application_id = (SELECT id FROM job_applications WHERE email = 'phase3-test-4-<timestamp>@example.test');
   ```

### Expected outcome

| Check | Expected |
|---|---|
| `ownership_state` | `NULL` |
| `ownership_cluster_id` | `NULL` |
| No `ownership_clusters` row for this identity+scope | Confirm via `SELECT * FROM ownership_clusters WHERE scoping_company_id = <disabled company id>` → 0 rows |
| No `ownership_events` row for this subject row | `SELECT * FROM ownership_events WHERE subject_row_id = <row id>` → 0 rows |
| Job still dispatched to the queue (it dispatches unconditionally; the *engine* itself is what self-gates once it runs) | Visible in Horizon as Completed — this is expected and correct, not a bug: dispatch happens regardless, `OwnershipEngine::evaluate()` checks `ScopeResolver::isEnabled()` first and returns immediately, writing nothing |
| Legacy behavior | Unaffected — `checkDuplicateCandidate()` already skips when `app_duplicate == 0`, same as always |

---

## 5. Database Verification Reference

Run these after any of the above to inspect the raw state directly.

```sql
-- All clusters for a given scope (Job mode — replace with a real job_id):
SELECT * FROM ownership_clusters WHERE scope_mode = 1 AND scope_key = <job_id>;

-- All clusters for a given scope (Company Overall mode — replace with a real company_id):
SELECT * FROM ownership_clusters WHERE scope_mode = 2 AND scope_key = <company_id>;

-- Full event history for one cluster, in decision order:
SELECT event_type, decision_rule, scenario_ref, subject_row_id, previous_owner_row_id,
       new_owner_row_id, trigger_source, reason, event_time
FROM ownership_events
WHERE cluster_id = <cluster id>
ORDER BY event_time, id;

-- Cross-check: does the cache column on every member row match the cluster's verdict?
SELECT ajwc.id, ajwc.thirdparty_company_id, ajwc.ownership_state, ajwc.ownership_cluster_id,
       oc.owner_row_id, oc.decision_rule
FROM add_job_wise_condidates ajwc
JOIN ownership_clusters oc ON oc.id = ajwc.ownership_cluster_id
WHERE ajwc.ownership_cluster_id = <cluster id>;
-- Expect: exactly one row where ajwc.id = oc.owner_row_id and ownership_state = 1;
-- every other row in the result should have ownership_state = 2.

-- Sanity check: no cluster should ever have owner_row_id pointing to a row whose own
-- ownership_state isn't 1 (would indicate a cache/cluster desync bug):
SELECT oc.id AS cluster_id, oc.owner_row_id, ajwc.ownership_state
FROM ownership_clusters oc
JOIN add_job_wise_condidates ajwc ON ajwc.id = oc.owner_row_id
WHERE ajwc.ownership_state != 1;
-- Expect: 0 rows. Any result here is a real bug — flag it, update the design doc if it
-- reveals a logic gap (Implementation Rule #8), then fix.
```

---

## 6. Queue Verification

1. **Horizon dashboard** (`/horizon`): under the `default` queue / supervisor, confirm jobs
   named `App\Jobs\AttemptOwnershipCompanyReservation` appear and complete after each §1 test
   case. Check the **Failed Jobs** tab specifically — Phase 3's job sets `$tries = 1`
   deliberately (no automatic retries in shadow mode), so a failed job should appear there
   exactly once per genuine failure, never retry-storm.
2. **`failed_jobs` table** (standard Laravel table):
   ```sql
   SELECT id, queue, payload, exception, failed_at FROM failed_jobs
   WHERE payload LIKE '%AttemptOwnershipCompanyReservation%'
   ORDER BY failed_at DESC LIMIT 20;
   ```
   Expected: empty, under normal test conditions. If not empty, read the `exception` column
   — this is your first signal of what's actually breaking.
3. **Log verification** — three distinct log messages to grep for, each corresponding to a
   different failure point:
   ```
   grep "AttemptOwnershipCompanyReservation dispatch failed" storage/logs/laravel.log
   grep "OwnershipEngine::attemptCompanyReservation failed" storage/logs/laravel.log
   grep "OwnershipEngine::claimForAgencySourcing failed" storage/logs/laravel.log
   ```
   - The first is the **dispatch-time** guard (Gap B fix) — would only appear if the Redis
     queue connection itself was unreachable at the moment of dispatch.
   - The second is the **job's internal** guard — appears if the engine itself throws once
     the job actually runs (e.g., a data integrity issue, a `LogicException` from a
     mis-guarded call).
   - The third is `claimForAgencySourcing()`'s own inline guard in `client_stage_submit()`.

   Expected under normal test conditions: **zero matches for all three**, across every test
   case in §1–4. If you find any, that's the priority item to investigate before signing off
   Phase 3 — include the full log line (with row id) in your results.

---

## 7. Performance Verification

There's no automated benchmark harness in this environment, so this is manual/observational.

1. **Candidate creation latency** (§1 trigger paths): since Company Reservation calls are
   **queued, not inline**, the request itself should show effectively no added latency vs.
   pre-Phase-3 behavior — the only new cost on the request thread is the `dispatch()` call
   itself (a single Redis `RPUSH`, sub-millisecond). Use browser devtools' Network tab (or
   server-side request timing/logs if available) to compare response time for a §1 action
   before/after — if there's a noticeable difference, something is wrong (most likely: the
   dispatch isn't actually queued — confirm `QUEUE_CONNECTION` is `redis` in staging's `.env`,
   not accidentally `sync`, which would make every queued job run inline on the request
   thread).
2. **Sourcing transition latency** (§3): this one **is** synchronous by design. Compare
   before/after response time for the stage-change action. Expect a small, bounded increase
   (a handful of indexed queries plus one row lock) — not zero, but should be low
   single-digit milliseconds under normal (non-contended) conditions. A large increase here
   would be worth investigating (check for missing indexes — confirm the migrations from
   Phase 1 actually applied: `SHOW INDEX FROM ownership_clusters; SHOW INDEX FROM
   ownership_events;`).
3. **Queue processing time**: in Horizon, check the job's recorded runtime for
   `AttemptOwnershipCompanyReservation` — should be well under a second per job under normal
   load (it's the same handful of indexed queries as the synchronous case, just off the
   request thread).
4. **Record actual numbers** (even rough ones) in your results — "felt the same" is a valid
   but weaker signal than an actual before/after millisecond comparison if you're able to get
   one.

---

## 8. Rollback / Resilience Verification

There is currently **no explicit on/off switch** for the Ownership Engine — its safety net
*is* the isolation built into Phase 3 (every call site try/catches around both the dispatch
and the job's internal execution). This section validates that safety net directly, rather
than a kill switch that doesn't exist yet. If, after this validation, you want an explicit
config-based disable flag as an extra layer, that's a small, separate addition to propose —
not assumed or built here without your sign-off.

**Test**: simulate a failure and confirm the legacy system is completely unaffected.

1. Temporarily break the queue connection (e.g., stop the Redis service used for queues on a
   staging-safe instance, or point `QUEUE_CONNECTION` at an unreachable host in a scoped
   test config — **do not do this against a shared staging Redis other features depend on
   without coordinating first**, since Horizon/other queued jobs share the same connection).
2. Repeat one of the §1 Company Reservation test cases.
3. **Expected**: the candidate is still created successfully, the response is unchanged, and
   the log shows `AttemptOwnershipCompanyReservation dispatch failed` (the Gap B guard
   catching the Redis error at dispatch time) — no 500 error, no broken page, no visible
   difference to the user.
4. Restore the queue connection.
5. Separately, if you can safely do so: temporarily rename/break something the engine reads
   (not recommended against shared staging tables — if this is too risky to simulate safely,
   skip this specific step and rely on the code-level guarantee instead: `evaluate()`'s own
   internals are wrapped by the job's try/catch, already exercised structurally by
   `AttemptOwnershipCompanyReservationJobTest`, which ran and passed in this session — see
   docs/OWNERSHIP_ENGINE_DESIGN.md Phase 3 section).
6. **Expected**: same as step 3 — legacy behavior fully intact regardless of what the engine
   does internally.

### Expected outcome

| Check | Expected |
|---|---|
| Candidate creation succeeds during simulated queue outage | ✅ |
| Response/UI unchanged during simulated outage | ✅ |
| Failure logged (dispatch-site catch) | ✅ exactly one log line per failed attempt |
| Legacy duplicate detection / stage transitions | Fully unaffected throughout |

---

## 9. Shadow-Mode Comparison

For at least one Company-Overall-mode scenario (mirrors Master Tracker S9/S15 or S19/S23 —
see docs/OWNERSHIP_ENGINE_DESIGN.md §1/§3 if you want the exact scenario semantics), create
the same "who wins" situation and compare both systems' answers side by side:

1. Create a Company Recruiter row first (§1, on the `app_duplicate = 2` test company).
2. Have an agency source a matching-identity candidate afterward (§3).
3. Legacy system's answer: check whatever the legacy toast/response showed the agency
   (`crossAgencyDuplicate` key in the `client_stage_submit()` response, gated by
   `companies.enable_cross_agency_duplicate` — this is a **separate, older opt-in setting**,
   independent of the new engine).
4. New engine's answer: `SELECT ownership_state, ownership_cluster_id FROM
   add_job_wise_condidates WHERE id = <agency row id>` → expect `2` (duplicate), owner =
   the Company Recruiter row.
5. **These two systems are allowed to disagree in this phase** — the legacy system's
   `crossAgencyDuplicate` flag depends on `enable_cross_agency_duplicate` being on for that
   company and uses the older `resolveDuplicateOwnership()` logic; the new engine always
   evaluates (when `app_duplicate` isn't 0) regardless of that toggle. **What must not
   happen**: the legacy system's own answer changing because the new engine ran. Confirm the
   legacy toast/flag behaves identically to a staging run from before this deployment (or,
   if no earlier reference is available, that its logic — controlled entirely by
   `resolveDuplicateOwnership()`, untouched this whole effort — still traces correctly by
   itself).

---

## 10. Results Summary Template

Copy this into your validation record and fill in each row after running the above.

| Section | Test case | Pass/Fail | Notes / log lines / SQL output |
|---|---|---|---|
| 1A | Direct Upload reservation | | |
| 1B | Google Sheet import | N/A pending route confirmation | |
| 1C | Similar Candidates reservation | | |
| 1D | Internal Bench reservation | | |
| 1E | Agency Bench reservation | | |
| 1F | Career Page reservation | | |
| 1G | Copy to Other Job reservation | | |
| 2 | Agency creation does not reserve | | |
| 3 | Agency Sourcing trigger (winner) | | |
| 3 | Agency Sourcing trigger (loser) | | |
| 4 | `app_duplicate = 0` skip | | |
| 5 | DB cross-check queries (no desync found) | | |
| 6 | Queue: Horizon completed jobs | | |
| 6 | Queue: `failed_jobs` empty | | |
| 6 | Logs: zero unexpected warnings | | |
| 7 | Creation-request latency (no regression) | | |
| 7 | Sourcing-transition latency (bounded) | | |
| 8 | Resilience under simulated queue outage | | |
| 9 | Shadow-mode comparison (legacy vs. new) | | |

**Sign-off**: Phase 4 (Backfill) begins only once every row above is Pass (or an explicitly
accepted N/A), and any issue found along the way has been fed back — design doc updated
first if it revealed a real gap, then the implementation, per Implementation Rule #8 — before
being marked Pass.
