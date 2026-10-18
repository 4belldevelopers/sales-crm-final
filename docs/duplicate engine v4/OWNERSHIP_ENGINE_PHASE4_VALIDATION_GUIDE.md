# Ownership Engine — Phase 4 (Backfill) Runtime Validation Guide

> **Purpose**: manual, staging-executable validation of Phase 4 (historical backfill) per
> docs/OWNERSHIP_ENGINE_DESIGN.md, "Phase 4: Historical Backfill" section. No further
> implementation begins until this has been run and reviewed. As with the Phase 3 guide, this
> is written as manual steps + copy-pasteable SQL/shell, not PHPUnit — the staging environment
> has no automated testing framework installed.
>
> **What this is validating**: that `php artisan ownership-engine:backfill` correctly and
> safely synthesizes historical ownership data for pre-Phase-3 candidate rows, without ever
> touching legacy behavior, and is safe to re-run. It is **not** re-validating Phase 3's live
> triggers (already covered by the Phase 3 guide) or Master Tracker correctness under
> concurrency (Phase 6).

---

## 0. Prerequisites

- Same access as the Phase 3 guide: shell/SSH on staging, raw SQL access, log access
  (`storage/logs/laravel.log`).
- **Important scoping note**: the backfill command runs as a plain, synchronous Artisan
  command — it is **not** queued/dispatched through Horizon (unlike Phase 3's
  `AttemptOwnershipCompanyReservation` job). It runs inline, in whatever shell session
  invokes it, for its entire duration. For a long run, use `nohup`/`screen`/`tmux` so it
  survives an SSH disconnect — this is an operational choice to make at run time, not
  something the command handles for you.
- Know (or query) roughly how many `add_job_wise_condidates` rows currently have **no**
  `ownership_events` row — this is what `--dry-run` will report, and it tells you what to
  expect before running for real:
  ```sql
  SELECT COUNT(*) FROM add_job_wise_condidates ajwc
  WHERE NOT EXISTS (SELECT 1 FROM ownership_events oe WHERE oe.subject_row_id = ajwc.id);
  ```
- Pick one **test company id** for the company-scoped run (§2) — ideally one with a mix of
  historical company-recruiter and agency-submitted candidates, so the chronological
  reconstruction logic (company reservation vs. agency-first-to-Sourcing) actually gets
  exercised, not just trivial single-contender cases.

---

## 1. Test Case: Dry-Run Execution

1. **Action**:
   ```
   php artisan ownership-engine:backfill --dry-run
   ```
2. **Expected outcome**:
   - Output reads `Dry run: <N> row(s) currently have no ownership_events and would be
     examined.` followed by `No writes performed.`
   - `<N>` should match (or be very close to — a live candidate could be created between
     your query and the command running) the count from §0's SQL query.
   - **Verify no writes actually happened**:
     ```sql
     SELECT COUNT(*) FROM ownership_events WHERE trigger_source = 'manual_rebuild';
     SELECT COUNT(*) FROM ownership_clusters;
     ```
     Compare against a snapshot taken before the dry run — both counts must be **identical**
     before and after. Any change means `--dry-run` did not correctly no-op — a real bug,
     stop here and report it rather than continuing to §2.

---

## 2. Test Case: Company-Scoped Backfill

Run against a single company first — smaller blast radius, easier to inspect fully by hand
before trusting a full run.

1. **Precondition**: note the test company id (§0), and snapshot its current state:
   ```sql
   SELECT COUNT(*) FROM add_job_wise_condidates WHERE company_id = <test company id>
     AND ownership_state IS NULL;
   ```
2. **Action**:
   ```
   php artisan ownership-engine:backfill --company=<test company id> --dry-run
   php artisan ownership-engine:backfill --company=<test company id>
   ```
   (Run `--dry-run` first as a sanity check on the scoped count, then the real run.)
3. **Expected outcome**:
   - Progress bar completes; summary table shows `Rows examined`, `Rows/clusters given a new
     event this run`, `Rows already covered (skipped)`, `Rows failed`.
   - `Rows failed` should be `0` on a clean first run. If not, see §7 (the log message to grep
     for is included there).
   - Every row that was `ownership_state IS NULL` before, for a real (non-empty) identity, is
     now either `1` (owner) or `2` (duplicate) — **except** rows belonging to an
     `app_duplicate = 0` company (should stay `NULL` — see §5's disabled-company check) or an
     agency row that never historically reached Sourcing (should also stay `NULL` — this is
     correct, not a bug: see docs §"Phase 4", `backfillCandidate()`'s description).
4. **Spot-check at least 2-3 individual clusters by hand** using the queries in §5 below —
   don't just trust the summary counts; confirm the *specific* owner/duplicate assignment
   makes sense given the real historical `created_at`/`timeline` data for those rows.

---

## 3. Test Case: Full Backfill on Staging

Only after §2 looks correct.

1. **Action** (use `nohup` or a persistent session — see §0):
   ```
   nohup php artisan ownership-engine:backfill --chunk=500 > backfill_run.log 2>&1 &
   ```
2. **Monitor**: `tail -f backfill_run.log` for progress; note the start/end wall-clock time.
3. **Expected outcome**: same shape as §2's summary table, now covering the whole dataset.
   Record the final numbers (examined / processed / skipped / failed) in your results.
4. **Verify total row coverage**:
   ```sql
   SELECT COUNT(*) FROM add_job_wise_condidates
   WHERE ownership_state IS NULL
     AND job_application_id IN (SELECT id FROM job_applications WHERE email IS NOT NULL OR phone IS NOT NULL);
   ```
   Rows still `NULL` after a full run should only be: (a) rows on `app_duplicate = 0`
   companies, or (b) agency rows that genuinely never reached Sourcing historically. If you
   find a `NULL` row that doesn't fit either explanation, that's a real gap — investigate
   before signing off (check the log for a failure on that specific row's id, or on another
   row in the same cluster that may have blocked it — see the Phase 4 design doc's disclosed
   "persistently-failing row blocks later rows in its own cluster" limitation).

---

## 4. Test Case: Idempotent Re-Run

1. **Action**: immediately re-run the exact same full command from §3:
   ```
   php artisan ownership-engine:backfill --chunk=500
   ```
2. **Expected outcome**:
   - `Rows examined` should be small or zero (only genuinely new candidates created since
     §3, if any) — **not** a repeat of the full dataset.
   - `Rows/clusters given a new event this run` should be **0** (everything from §3 is
     already covered).
   - `Rows already covered (skipped)` accounts for whatever `Rows examined` found.
   - **Verify no duplicate events were created**:
     ```sql
     SELECT subject_row_id, COUNT(*) FROM ownership_events
     GROUP BY subject_row_id HAVING COUNT(*) > 1;
     ```
     Expected: **0 rows**. Any result here is a real idempotency bug — a given row must never
     have more than one event in its lifetime (see design doc's Finding 4 on why this is
     supposed to be structurally impossible).

---

## 5. Test Case: Database Consistency

Run these after §3 (or §2 for a scoped check) — same cross-check queries the Phase 3 guide
used, now validated against backfilled data specifically.

```sql
-- Every cluster's owner_row_id must point to a row whose own cache says it's the owner:
SELECT oc.id AS cluster_id, oc.owner_row_id, ajwc.ownership_state
FROM ownership_clusters oc
JOIN add_job_wise_condidates ajwc ON ajwc.id = oc.owner_row_id
WHERE ajwc.ownership_state != 1;
-- Expected: 0 rows.

-- Every non-owner member of a cluster must be marked duplicate, never left NULL or "owner":
SELECT ajwc.id, ajwc.ownership_state, oc.owner_row_id
FROM add_job_wise_condidates ajwc
JOIN ownership_clusters oc ON oc.id = ajwc.ownership_cluster_id
WHERE ajwc.id != oc.owner_row_id AND ajwc.ownership_state != 2;
-- Expected: 0 rows.

-- Every backfilled event should carry decision_rule ER2, ER3, ER5, or ER6 only (ER1/ER4/ER7/
-- ER8 are never the *recorded* rule on an event — they're guards/derivations, not outcomes):
SELECT DISTINCT decision_rule FROM ownership_events WHERE trigger_source = 'manual_rebuild';
-- Expected: only ER2, ER3, ER5, ER6 appear.

-- Confirm backfilled events used real historical timestamps, not "now" (event_time should
-- be spread across your historical data's actual date range, not clustered at the moment
-- you ran the command):
SELECT MIN(event_time), MAX(event_time), COUNT(*) FROM ownership_events
WHERE trigger_source = 'manual_rebuild';
-- Expected: MIN/MAX span your real historical candidate-creation date range. If every
-- event_time is suspiciously close to when you ran the command, evaluateAt()'s $asOf
-- parameter isn't actually being honored — a real bug, not a data artifact.

-- Cross-check one specific historical cluster end-to-end (pick one from your spot-checks):
SELECT event_type, decision_rule, subject_row_id, trigger_source, reason, event_time
FROM ownership_events WHERE cluster_id = <cluster id> ORDER BY event_time, id;
```

---

## 6. Queue and Performance Observations

Since the backfill command is **not** queued (§0), "queue" verification here is really about
whether it *should* be, at real data volumes — an operational judgment call to record, not a
pass/fail test.

1. **Record wall-clock duration** for the full run (§3) and back-calculate rows/second.
2. **Record memory usage** if possible (`/usr/bin/time -v php artisan ...` or equivalent) —
   should stay flat/bounded across the whole run, since the command chunks (§21 of the design
   doc's streaming requirement) rather than loading the full table.
3. **Check for lock contention** during the run — if other Ownership Engine activity (Phase
   3's live triggers) is also happening on staging concurrently, watch for any slow queries
   or lock waits on `ownership_clusters` in MySQL's process list (`SHOW PROCESSLIST` /
   `SHOW ENGINE INNODB STATUS` if you suspect contention). Expected: minimal, since each
   cluster lock is narrow and short-held (§8/§23 of the design), but worth observing directly
   rather than assuming.
4. **Judgment call to record for the next milestone**: at staging's current data volume
   (tens of thousands of rows), is a synchronous foreground command acceptable, or should a
   future refinement wrap this in a queued job for production-scale (100M+) runs? Record your
   observation — this is exactly the kind of "additional refinement" input the next
   milestone's decision depends on, not something to decide unilaterally here.

---

## 7. Test Case: Failure Logging (non-blocking)

1. **Check for failures** from any of the runs above:
   ```
   grep "OwnershipEngine::backfillCandidate failed" storage/logs/laravel.log
   ```
2. **Expected**: zero matches on a clean dataset. If you do find failures, for each one:
   - Note the `row_id` and `error` from the log line.
   - Query that row directly (`SELECT * FROM add_job_wise_condidates WHERE id = <row_id>`)
     to understand what's unusual about it (missing `job_application_id`? malformed email?).
   - Confirm the command's overall run still completed (didn't abort) and that other rows in
     the same run were still processed — a failure on one row must not have blocked unrelated
     clusters (it may block *later rows in its own cluster* — the disclosed limitation in the
     design doc; confirm whether that's what you're seeing here or something broader, which
     would be a new bug).
   - Re-run the command — per the design's idempotency guarantee, the failed row (and
     anything blocked behind it in the same cluster) should be retried automatically.

---

## 8. Test Case: Rollback Procedure

Exercise the actual rollback SQL from the design doc — don't just read it, run it against a
disposable/scoped subset first.

1. **Precondition**: pick a small, disposable scope for this test — ideally re-run §2's
   company-scoped backfill against a throwaway test company specifically for this purpose, so
   rolling it back doesn't touch data you want to keep.
2. **Action**:
   ```sql
   -- Identify affected clusters first (review before deleting):
   SELECT DISTINCT cluster_id FROM ownership_events
   WHERE trigger_source = 'manual_rebuild'
     AND scoping_company_id = <throwaway test company id>;

   -- Reset the cache columns:
   UPDATE add_job_wise_condidates
   SET ownership_state = NULL, ownership_cluster_id = NULL, ownership_evaluated_at = NULL
   WHERE ownership_cluster_id IN (
     SELECT DISTINCT cluster_id FROM ownership_events
     WHERE trigger_source = 'manual_rebuild' AND scoping_company_id = <throwaway test company id>
   );

   -- Remove the synthesized events and the clusters they created:
   DELETE FROM ownership_events
   WHERE trigger_source = 'manual_rebuild' AND scoping_company_id = <throwaway test company id>;
   DELETE FROM ownership_clusters
   WHERE scoping_company_id = <throwaway test company id>
     AND id NOT IN (SELECT DISTINCT cluster_id FROM ownership_events);
   ```
3. **Expected outcome**:
   - All three cache columns back to `NULL` for that company's rows.
   - No orphaned `ownership_clusters` rows left for that company (the last `DELETE`'s
     `NOT IN` subquery should catch every cluster that only ever had `manual_rebuild` events).
   - **No other company's data touched** — spot-check a row from a *different* company that
     was also backfilled and confirm it's untouched.
4. **Re-run backfill for that company** afterward and confirm it reconstructs identically to
   the first time (proves the rollback was clean and backfill is genuinely re-runnable from
   scratch, not just from a "gap" state).

---

## 9. Test Case: Legacy System Remains Unaffected Throughout

Same spirit as the Phase 3 guide's shadow-mode checks, now specifically around the backfill
command's execution window.

1. **Before, during, and after** the full backfill run (§3), exercise a few ordinary
   candidate-creation and stage-transition actions through the normal UI (any of the Phase 3
   guide's §1/§3 trigger paths).
2. **Expected outcome**:
   - Legacy duplicate badges/toasts/responses behave identically to before this deployment,
     with no observable difference while backfill is running in the background.
   - No new errors in the log unrelated to Ownership Engine's own `manual_rebuild`/
     `AttemptOwnershipCompanyReservation` messages.
   - Any live candidate created *during* the backfill run should be picked up correctly by
     Phase 3's normal live path (queued job / synchronous sourcing call) — confirm at least
     one such row ends up with the right `ownership_state`, and that a second, later
     `ownership-engine:backfill` run doesn't try to reprocess it (should show up as "already
     covered," not reprocessed — ties back to §4's idempotency check).

---

## 10. Results Summary Template

| Section | Test case | Pass/Fail | Notes / numbers |
|---|---|---|---|
| 1 | Dry-run reports correct count, writes nothing | | |
| 2 | Company-scoped backfill: counts correct | | |
| 2 | Company-scoped backfill: spot-checked clusters correct | | |
| 3 | Full backfill: completes, summary recorded | | |
| 3 | Full backfill: no unexplained `NULL` rows remain | | |
| 4 | Idempotent re-run: zero new events created | | |
| 4 | Idempotent re-run: no duplicate events found (§5 query) | | |
| 5 | DB consistency: owner/duplicate cross-checks clean | | |
| 5 | DB consistency: event_time reflects real historical dates | | |
| 6 | Performance: duration, rows/sec, memory | | |
| 6 | Performance: lock contention observed? | | |
| 6 | Judgment: should this become a queued job at scale? | | |
| 7 | Failure logging: zero unexpected failures (or explained) | | |
| 8 | Rollback: cache/events/clusters cleanly removed | | |
| 8 | Rollback: other companies' data unaffected | | |
| 8 | Rollback: re-run reconstructs identically | | |
| 9 | Legacy behavior unaffected during backfill run | | |
| 9 | Live-during-backfill row handled correctly, no double-processing | | |

**Sign-off**: results from this table feed directly into the "activation planning vs.
additional refinements" decision — record actual numbers where the table asks for them, not
just Pass/Fail, since several rows (performance, rollback) are explicitly inputs to that
judgment call, not binary gates.
