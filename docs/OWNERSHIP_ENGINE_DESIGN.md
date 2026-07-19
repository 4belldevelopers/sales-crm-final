# The Ownership Engine — Technical Design (Revision 3)

> Status: **DESIGN REVIEW ONLY — NOTHING IMPLEMENTED.** This supersedes
> [DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md](DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md) (Revision 1,
> kept for history). Per explicit instruction, this is not "duplicate detection" — it is
> **the** Ownership Engine: the single, authoritative, event-sourced service that decides,
> records, and explains who owns every candidate identity across the entire ATS, forever.
> Every table, rule, and test below exists **only** because the Master Tracker (§1) requires
> it — where it doesn't, this document says so explicitly rather than filling the gap with
> an assumption.
>
> **§0–§17 are Revision 2** (architecture: contract, rules, traceability, event model,
> schema, concurrency, gap register — unchanged in this pass). **§18 onward is Revision 3**:
> a quantified, per-event performance/scale audit of that same architecture, done before
> implementation sign-off, per explicit instruction to treat this as a platform-critical
> engine expected to run for the next decade. Revision 3 found a small number of concrete
> simplifications and optimizations to Revision 2's schema/sequencing (flagged inline where
> they appear, and summarized in §29) — nothing in §1–§4 (the contract and rule set) changed.

---

## 0. What changed from Revision 1, and why

Revision 1 was a competent schema/index/trigger design for a *feature*. It was reframed
because:

- It never reproduced the literal Master Tracker — it worked from a paraphrase, which is
  exactly what principle #1 (below) forbids. **§1 now reproduces it verbatim**, recovered
  from this session's own earlier turn, and §3 traces every one of its 30 rows to a named
  rule, a planned code location, and a planned test.
- It treated ownership as a computation triggered by a couple of hooks. It's now treated as
  a **domain engine with its own history, rebuild path, and explainability API** — the
  event-sourcing model in §7 is the structural answer to five of the thirteen principles at
  once (history, rebuild, determinism, explainability, and — as a side effect — auditability
  for dispute resolution).
- It assumed the event catalog (candidate deleted/withdrawn/merged, job closed, agency
  removed...) without checking whether those things exist in this codebase today. **§6 now
  states, for each one, whether it's confirmed against the live schema or is a hypothetical
  future capability** — conflating the two would mean designing triggers for events that
  can't fire, which is its own kind of unjustified assumption.
- It sized locking around "a bit of contention." **§10 now works the concurrency review
  against the stated 50,000-concurrent-recruiter target explicitly**, including why the
  design's lock key choice makes that number a non-issue for the overwhelming majority of
  traffic.

---

## 1. The Contract: the Master Tracker, verbatim

> Reproduced exactly as pasted earlier in this session. This is the **only** business
> specification. Nothing below this document line invents a rule that isn't traceable to
> the hierarchy statement or a tracker row.

**Final Ownership Hierarchy**
1. **Company Recruiter Reservation (starts from InReview)** — applies only if the Company
   Recruiter is the first to create the candidate *before any agency has established
   ownership*. Once reserved, agencies become duplicates when they move to Sourcing.
2. **Agency Ownership** — if no Company Recruiter reservation exists, the first agency to
   move the candidate to Sourcing becomes the owner.
3. **Company Overall System Duplicate (Cross-Job)** — if an agency has already established
   ownership (Sourcing+) in any eligible job for the same company, and a Company Recruiter
   later adds the candidate (even in InReview), the Company Recruiter becomes Duplicate.
4. **Ownership never flips** from Agency → Company after the agency has already sourced the
   candidate.

**MASTER TRACKER**

| ID | Mode | Agency A | Agency B | Company Recruiter | First Uploaded | First Sourced | Final Owner | Duplicate Shown To | Why |
|---|---|---|---|---|---|---|---|---|---|
| S1 | Job | InReview | – | – | Agency A | None | None | Nobody | Candidate is private. |
| S2 | Job | InReview | InReview | – | Agency A | None | None | Nobody | Both agencies are private. |
| S3 | Job | Sourcing | InReview | – | Agency B | Agency A | Agency A | Nobody | First agency to Sourcing wins. |
| S4 | Job | InReview | Sourcing | – | Agency A | Agency B | Agency B | Nobody | First agency to Sourcing wins. |
| S5 | Job | Sourcing | Sourcing | – | Agency B | Agency A | Agency A | Agency B | Agency B becomes duplicate. |
| S6 | Job | Evaluation | Interview | – | Agency A | Agency A | Agency A | Agency B | Ownership continues after Sourcing. |
| S7 | Job | Joined | Offer | – | Agency A | Agency A | Agency A | Agency B | Ownership never changes. |
| S8 | Job | InReview | – | InReview | Company Recruiter | None | Company Recruiter | Nobody | Company reservation starts immediately. |
| S9 | Job | Sourcing | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Agency becomes duplicate when sourced. |
| S10 | Job | Evaluation | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Reservation still active. |
| S11 | Job | Offer | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Reservation still active. |
| S12 | Job | Joined | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Reservation still active. |
| S13 | Job | Sourcing | Sourcing | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A & B | Company reservation overrides agencies. |
| S14 | Job | InReview | InReview | InReview | Company Recruiter | None | Company Recruiter | Nobody | All private, company already reserved. |
| S15 | Job | Sourcing | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Agency already owned. Company becomes duplicate. |
| S16 | Job | Evaluation | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Ownership cannot flip. |
| S17 | Job | Offer | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Ownership cannot flip. |
| S18 | Job | Joined | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Ownership cannot flip. |
| S19 | Company Overall | Sourcing (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Company already reserved another job. |
| S20 | Company Overall | Sourcing (Job A) | Sourcing (Job C) | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A & B | Company reservation overrides all agencies. |
| S21 | Company Overall | Evaluation (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Reservation remains valid across jobs. |
| S22 | Company Overall | Joined (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Reservation remains valid across jobs. |
| S23 | Company Overall | Sourcing (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Agency sourced first across company. Company becomes duplicate. |
| S24 | Company Overall | Evaluation (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Ownership stays with agency. |
| S25 | Company Overall | Joined (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Ownership stays with agency. |
| S26 | Company Overall | Sourcing (Job A) | Sourcing (Job C) | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Agency B & Company Recruiter | Agency A established ownership first. |
| S27 | Company Overall | InReview (Job A) | InReview (Job C) | InReview (Job B) | Company Recruiter | None | Company Recruiter | Nobody | Company reservation exists, agencies remain private. |
| S28 | Company Overall | InReview (Job A) | Sourcing (Job C) | InReview (Job B) | Company Recruiter | Agency B | Company Recruiter | Agency B | Company reservation overrides agency. |
| S29 | Company Overall | Sourcing (Job A) | InReview (Job C) | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reservation overrides agency. |
| S30 | Company Overall | Sourcing (Job A) | Sourcing (Job C) | Sourcing (Job B) | Company Recruiter | Company Recruiter | Company Recruiter | Agency A & B | Company is primary owner everywhere. |

**Ownership Decision Tree** (as given): on InReview→Sourcing, branch on Duplicate Mode
(Job vs. Company Overall) → check for an earlier Company Recruiter record in scope → if
found, Company owns and the sourcing agency is Duplicate → if not found, search for any
Sourcing+ agency record in scope → if found, that agency is owner and the new arrival is
Duplicate → if not found, the new arrival becomes owner.

---

## 2. The Engine Rule Set — the contract reduced to eight orthogonal rules

Rather than encoding "30 special cases," the engine implements eight rules. Every rule's
wording is a direct paraphrase of a hierarchy sentence or a "Why" cell above — none are
invented. §3 proves this reduction is complete (every scenario decomposes into a small
combination of these, and nothing in the tracker requires a ninth rule).

| Rule | Statement | Source |
|---|---|---|
| **ER1** | An agency row that has never reached Sourcing contributes nothing to ownership and is never itself flagged. | "Candidate is private" (S1/S2); "still-InReview... stays fully private" |
| **ER2** | A Company Recruiter row reserves ownership at the instant of creation *if* no agency has established ownership before that instant. | Hierarchy rule 1, verbatim |
| **ER3** | Absent a valid Company Reservation, the earliest agency row to reach Sourcing becomes owner. | Hierarchy rule 2, verbatim |
| **ER4** | Once an owner is set for an identity+scope cluster, it never changes, regardless of what any other row does afterward. | Hierarchy rule 4, verbatim; "ownership cannot flip" (S16–18, S24–25) |
| **ER5** | A Company Recruiter row is compared against the current owner immediately upon creation, regardless of its own stage — never privacy-shielded. | S15–18, S23–25: company rows shown as Duplicate while still described only as "Added ... InReview" |
| **ER6** | An agency row is only compared/flagged once **it itself** reaches Sourcing — never before, no matter who else already owns the identity. | S9–13: Agency A/B only listed under "Duplicate Shown To" once their own stage is Sourcing+; ER1 covers the InReview case explicitly |
| **ER7** | Scope determines the comparison universe: Job mode compares within one job; Company Overall mode compares across every eligible job the scoping company owns, as **one** cluster, not per-job islands. | Hierarchy rule 3; S19–30 vs. S1–18 |
| **ER8** | Among N competing contenders, exactly one is ever eligible to be owner (the earliest per ER2/ER3); every other contender that meets ER5/ER6's own visibility condition is Duplicate against that one owner — this generalizes the two-contender rules to arbitrary N without a new rule. | S13/S20/S26/S30 (three-way and more) |

---

## 3. Traceability Matrix — Scenario → Rule → Code → Test

"Code" cites the planned function (§9) that will implement it — nothing here exists yet.
"Test" is the planned automated test ID (§14 gives the full suite).

| Scenario | Rules fired | Code (planned) | Test ID |
|---|---|---|---|
| S1 | ER1 | `OwnershipEngine::evaluateCluster()` — no owner branch | T-S1 |
| S2 | ER1 | same | T-S2 |
| S3 | ER1 (B), ER3 (A) | `resolveAgencyOwner()` | T-S3 |
| S4 | ER1 (A), ER3 (B) | same (mirror) | T-S4 |
| S5 | ER3, ER6 (B) | `resolveAgencyOwner()` + `markDuplicate()` | T-S5 |
| S6 | ER4 | `evaluateCluster()` — owner-already-set short-circuit | T-S6 |
| S7 | ER4 | same | T-S7 |
| S8 | ER2 | `attemptCompanyReservation()` | T-S8 |
| S9 | ER2, ER4, ER6 | `attemptCompanyReservation()` then later `claimForAgencySourcing()` finds owner locked | T-S9 |
| S10–12 | ER2, ER4 | same | T-S10, T-S11, T-S12 |
| S13 | ER2, ER4, ER6 (×2), ER8 | same, generalized over N=2 agencies | T-S13 |
| S14 | ER2, ER1 | `attemptCompanyReservation()`, no agency ever sources | T-S14 |
| S15 | ER3, ER4, ER5 | `claimForAgencySourcing()` wins first; later `attemptCompanyReservation()` finds owner locked, marks itself Duplicate immediately | T-S15 |
| S16–18 | ER4, ER5 | same, company row progresses further, stays Duplicate | T-S16, T-S17, T-S18 |
| S19 | ER7, ER2, ER4 | `resolveScope()` (mode=2) + `attemptCompanyReservation()` | T-S19 |
| S20 | ER7, ER2, ER4, ER8 | same, N=2 agencies | T-S20 |
| S21–22 | ER7, ER2, ER4 | same | T-S21, T-S22 |
| S23 | ER7, ER3, ER4, ER5 | `resolveScope()` (mode=2) + `claimForAgencySourcing()` wins, company reservation attempt later finds owner locked | T-S23 |
| S24–25 | ER7, ER3, ER4, ER5 | same | T-S24, T-S25 |
| S26 | ER7, ER3, ER4, ER5, ER6, ER8 | same, 3-way (Agency A owner, Agency B + Company Recruiter both Duplicate via different rules) | T-S26 |
| S27 | ER7, ER2, ER1 | `resolveScope()` (mode=2), no agency ever sources | T-S27 |
| S28–29 | ER7, ER2, ER4, ER6 (one agency only) | same | T-S28, T-S29 |
| S30 | ER7, ER2, ER4, ER6, ER8 | same, company also happens to have progressed — irrelevant, already owner | T-S30 |

This table is what makes "nothing exists unless justified by the Master Tracker"
mechanically checkable at implementation time: every method in `OwnershipEngine`
traces back to this matrix, and every matrix row traces back to §1's literal text — a
change to the tracker means a diffable change to this table, which means a diffable
change to the test suite.

---

## 4. The Engine's own vocabulary — nine questions, nine answers, one place each

| Question | Answered by | Where it lives |
|---|---|---|
| Who owns this candidate? | `ownership_clusters.owner_row_id` | §7.2 |
| Why does this owner exist? | `ownership_events.decision_rule` (ER1–ER8) on the event that set `owner_row_id` | §7.1 |
| When was ownership established? | `ownership_clusters.decided_at` (never updated after first write) | §7.2 |
| Which rule created ownership? | Same `decision_rule` value, human-readable via `OwnershipEngine::explain()` | §11 |
| Who became duplicate? | `add_job_wise_condidates.ownership_state = DUPLICATE` rows referencing this cluster | §7.3 |
| Why did they become duplicate? | The specific `ownership_events` row for that member, carrying `decision_rule` + a generated `reason` sentence | §11 |
| Job scope or Company Overall scope? | `ownership_clusters.scope_mode`, snapshotted at first decision, immune to later setting changes | §6 (event: mode changed) |
| Can ownership ever change? | No — enforced structurally, not by convention (§7.2's write path refuses to overwrite a non-null `owner_row_id`) | §7, ER4 |
| If not, why not? | Hierarchy rule 4, ER4 — `OwnershipEngine::explain()` returns this exact rule text for any "why can't this flip" question | §11 |

Every page, API, report, dashboard, notification, and future feature answers these nine
questions by calling one of exactly three read methods (§9, §15) — never by re-deriving
them from raw rows.

---

## 5. One Source of Truth — inventory and enforcement

### 5.1 Every current and anticipated call site

| Consumer | Today | After this engine ships |
|---|---|---|
| `client_stage_submit()` 8→1 hook | Calls `resolveDuplicateOwnership()` directly | Calls `OwnershipEngine::claimForAgencySourcing()` |
| `rec_candidate_assign()` (4 branches), `copy_current_selected_applicant()`, Upload (3 sites) | Call `checkDuplicateCandidate()` only (no ownership arbitration at all today) | Call `OwnershipEngine::attemptCompanyReservation()` when `thirdparty_company_id = 0` |
| `all_apptracker.blade.php`, `all_candidates.blade.php`, `clientappdata_ajax.blade.php` | Read raw `duplicate`/`wise_duplicate` cache columns only (ownership calls removed this session) | Read `ownership_state` cache column only — still zero engine calls at render time |
| Any future report/dashboard | Doesn't exist yet | Must call `OwnershipEngine::getCachedState()` / a read-model query object (§15) — never a raw join against `ownership_clusters`/`ownership_events` |
| Any future notification (e.g., "you've been outbid") | Doesn't exist yet | Subscribes to `OwnershipEvent` domain events (§7.1) fired by the engine — never recomputes |
| Any future API/webhook consumer | Doesn't exist yet | Same read methods as UI — one contract for internal and external consumers |
| Any future AI matching / vendor scoring feature | Doesn't exist yet | Reads `ownership_clusters`/`ownership_events` **only** through the engine's query object — per principle 13, this is the entire point of centralizing it now |

### 5.2 Structural enforcement, not just convention

A rule that says "call the engine" is not self-enforcing — Revision 1's whole problem was
three call sites quietly reinventing the same computation. Two concrete guards:

1. **A grep-based CI guard** (matching the existing `scripts/ai-guards.sh` pattern already
   used in this repo for `dd()`/`env()`): fail the build if any file outside
   `app/Services/OwnershipEngine/` references `ownership_clusters`, `ownership_events`, or
   `add_job_wise_condidates.ownership_state` via a raw `DB::table()`/`whereHas` — the only
   legitimate access is through the engine's own class.
2. **No Eloquent model exposes the ledger tables generally** — `OwnershipCluster` and
   `OwnershipEvent` Eloquent models exist only inside the service namespace, are not
   `use`d anywhere else, and the CI guard in (1) catches any raw-query bypass attempt too.

---

## 6. Event Catalog — reviewed against the actual schema, not assumed

Every event below was checked against `database/schema/mysql-schema.sql` before being
listed as "confirmed." Where a event doesn't correspond to anything that exists today, it's
marked **HYPOTHETICAL** rather than silently included as if it were real — designing a
trigger for a feature that doesn't exist would itself be an unjustified assumption.

| Event | Status | Fires |
|---|---|---|
| Company Recruiter creates candidate (`thirdparty_company_id = 0`) | **CONFIRMED** — every `add_job_wise_condidates` insert already carries this column | `attemptCompanyReservation()` |
| Agency creates candidate (`thirdparty_company_id > 0`) | **CONFIRMED** | No ownership event (ER1) — only logged as a row's existence, no reservation |
| InReview(8) → Sourcing(1) on a client-owned job | **CONFIRMED** — this is `client_stage_submit()`'s existing hook point | `claimForAgencySourcing()` |
| Candidate Rejected (client, id 7 / agency, id 9) | **CONFIRMED** stage exists | No ownership event — §14/§7 flag this as **Gap M-Reject** (see §12): the tracker never shows a Rejected row, so whether a Rejected owner keeps owning is a genuine open question, not something this design resolves unilaterally |
| Candidate un-rejected (9→8) | **CONFIRMED** (existing transition rule) | No ownership event — reservation, if any, already happened at creation; un-rejecting doesn't re-create it |
| `job_applications.deleted_at` set (candidate soft-deleted) | **CONFIRMED** — column exists on `job_applications` today | **Gap M-Delete** (§12) — needs a decision, not assumed |
| `job_applications.deleted_at` cleared (candidate restored) | **CONFIRMED** — same column supports restore | **Gap M-Restore** (§12) |
| Candidate merged (two profiles identified as one person) | **NOT CONFIRMED** — no merge tooling found anywhere in `app/` (checked; only incidental unrelated string matches). **HYPOTHETICAL.** | Designed for extensibility only (§13) — not wired to anything today |
| `job_applications.email`/`phone` edited | **CONFIRMED** columns are plain, editable fields (no evidence of an audit/immutability constraint found) | **Gap M-Identity-Edit** (§12) |
| `jobs.status` changed (`active` ↔ `hold`/`inactive`) | **CONFIRMED** — `jobs.status enum('active','hold','inactive')` exists | **Gap M-JobStatus** (§12) — the hierarchy's own text says "any **eligible** job" for Company Overall scope, but never defines what makes a job eligible; this column is the obvious candidate, needs product confirmation |
| `companies.app_duplicate` changed (0/1/2) | **CONFIRMED** — existing column | Prospective-only (§7.2's `scope_mode` snapshot) — never rewrites history |
| `companies.enable_cross_agency_duplicate` toggled | **CONFIRMED** — existing column | Gates the toast/UI consequence only, not whether the engine records events (unchanged from Revision 1's finding) |
| `companies.status` changed (`active` ↔ `inactive`) | **CONFIRMED** — `companies.status enum('active','inactive')` exists | **Gap M-CompanyDeactivated** (§12) |
| Agency relationship removed from a client | **NOT CONFIRMED** — no vendor/third-party relationship table found under any searched name; agencies relate to a job purely via `thirdparty_company_id` on each row, with no separate "vendor list" to remove someone from. **HYPOTHETICAL** until such a table is found or built. | Not wired to anything today |
| Candidate "withdrawn" | **NOT A DISTINCT STAGE** — confirmed `withdraw` only appears as a free-text, per-company-configured **sub-status label** (`candidate_status.name` containing "withdraw", matched by keyword for its icon in `GeneralMethod::csdSubStatusMeta()`), always filed under whichever parent `master_status` (almost certainly Rejected) the company configured it under. There is no separate "Withdrawn" master stage. | Folded into the Rejected event above — not a separate trigger |
| Bulk import / API import (many candidates in one request) | **CONFIRMED as a general pattern** (Upload paths exist; a bulk variant is plausible given ATS scale) | §10.6 — same trigger as single creation, but must handle in-batch collisions (§12 Gap M-BulkBatch) |
| Queue job retry / at-least-once redelivery | **CONFIRMED as a Laravel/Horizon platform property**, not a business event, but affects every trigger above | §10.7 — idempotency via `event_uuid` |

---

## 7. Data model — event-sourced, three tiers, each justified by a distinct access pattern

### 7.1 `ownership_events` — the immutable log (source of truth)

```php
Schema::create('ownership_events', function (Blueprint $table) {
    $table->id();
    $table->uuid('event_uuid')->unique();          // idempotency key — see §10.7
    $table->unsignedBigInteger('cluster_id')->nullable(); // set once the cluster exists
    $table->unsignedBigInteger('scoping_company_id');
    $table->tinyInteger('scope_mode');               // 1=Job, 2=Company Overall — snapshot
    $table->unsignedBigInteger('scope_key');
    $table->string('normalized_email', 190)->nullable();
    $table->string('normalized_phone', 32)->nullable();
    $table->unsignedBigInteger('subject_row_id');    // the add_job_wise_condidates.id this event concerns
    $table->string('event_type', 40);                // RESERVATION_ATTEMPT, AGENCY_SOURCED, OWNER_ESTABLISHED, DUPLICATE_MARKED, REBUILD_REPLAY, ...
    $table->string('decision_rule', 10)->nullable();  // ER1..ER8
    $table->string('scenario_ref', 10)->nullable();   // informational only: "S9" — never authoritative, just for debugging against §1
    $table->unsignedBigInteger('previous_owner_row_id')->nullable();
    $table->unsignedBigInteger('new_owner_row_id')->nullable();
    $table->string('trigger_source', 40);             // candidate_creation, stage_transition, job_status_change, settings_change, manual_rebuild
    $table->unsignedBigInteger('actor_user_id')->nullable(); // null for system-generated events
    $table->text('reason');                           // human-generated sentence, see §11
    $table->timestamp('event_time', 6);               // microsecond precision — see §9.4/§10
    $table->timestamps();
});
```

**Why it exists**: this is the only object in the whole design that directly answers
"Ownership History" (§ principle 5) and "Recovery & Rebuild" (§ principle 6) — without an
immutable log, "rebuild" would mean re-deriving from raw `add_job_wise_condidates`/
`timeline` rows every time, which loses the *reason* (decision_rule, actor, trigger_source)
even if it can still reproduce the *outcome*. **Cannot be simplified away**: merging this
with the cluster table (7.2) would mean either mutating audit history (violates
immutability) or storing full history *inside* a row that also needs to be fast-locked on
every write (bloats the lock-holding row, worse locking behavior — see §7.4).

**Storage impact**: one row per meaningful transition, not per page view — bounded by
business events, not traffic. At `add_job_wise_condidates`'s current ~64K rows this table
would have a similar or smaller order of magnitude of rows (many creations produce zero
event — agency rows below Sourcing generate none per ER1). At 100M+ candidate-rows/year
scale, this becomes the single fastest-growing table in this design, comparable to
`timeline`'s own growth — **partition by `event_time` (monthly) or archive after N months**
is a realistic future need, flagged here rather than assumed unnecessary.

**Query impact**: never queried for point lookups (that's §7.2's job) — only scanned for
(a) rebuild replay of one cluster (bounded, indexed — see below) and (b) explainability/audit
UI for one row (bounded to one `subject_row_id`).

**Locking impact**: none — pure `INSERT`, never `UPDATE`/`DELETE`. This is the entire reason
it can sit safely alongside a highly-contended lock target (7.2) without adding to that
contention.

**Indexes**: `(cluster_id, event_time, id)` for replay order; `(subject_row_id)` for
per-row explainability lookups; unique `(event_uuid)` for idempotent retry handling.

### 7.2 `ownership_clusters` — the lockable current-state pointer (rename of Revision 1's "ledger")

```php
Schema::create('ownership_clusters', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('scoping_company_id');
    $table->tinyInteger('scope_mode');
    $table->unsignedBigInteger('scope_key');
    $table->string('normalized_email', 190)->nullable();
    $table->string('normalized_phone', 32)->nullable();
    $table->unsignedBigInteger('owner_row_id')->nullable();
    $table->enum('owner_type', ['company', 'agency'])->nullable();
    $table->string('decision_rule', 10)->nullable();   // ER2 or ER3 — whichever established this owner
    $table->timestamp('decided_at', 6)->nullable();     // written once, never updated again
    $table->unsignedBigInteger('last_event_id');         // FK into ownership_events — traceability
    $table->timestamps();

    $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_email'], 'oc_email_unique');
    $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_phone'], 'oc_phone_unique');
});
```

**Why it exists, and why it can't just be "the latest event"**: the write path (§9) needs
to answer "who owns this identity right now, and can I lock that answer while I decide
whether a new contender changes anything" in O(1) — reconstructing that from
`ownership_events` (a "last event per cluster" query) doesn't support `SELECT ... FOR UPDATE`
on a single narrow row cheaply at scale, and re-deriving it on every write recomputes
exactly what this whole design exists to stop recomputing.

**Storage impact**: one row per *identity+scope*, not per candidate-row or per event — this
is the smallest table in the design by row count (bounded by unique people-in-scope, not
by total pipeline activity).

**Query impact**: the only query pattern is a point lookup on the unique keys above —
always an index hit, never a scan.

**Locking impact**: this is the **only** table where locking matters (§9/§10) — by design,
exactly one row is ever locked per write transaction, and it's the narrowest possible row
(a handful of scalar columns), minimizing lock hold time.

**Future scalability**: since a cluster row exists once per identity+scope and never grows
unboundedly (unlike `ownership_events`), this table stays small relative to the platform's
overall growth — the growth curve here tracks unique-people-per-company, not
applications-per-year.

### 7.3 Cache columns on `add_job_wise_condidates` — the zero-computation read path

```php
Schema::table('add_job_wise_condidates', function (Blueprint $table) {
    $table->tinyInteger('ownership_state')->nullable();      // null=undetermined/private, 1=owner, 2=duplicate
    $table->unsignedBigInteger('ownership_cluster_id')->nullable();
    $table->timestamp('ownership_evaluated_at', 6)->nullable();
});
```

**Why this doesn't collapse into just "join `ownership_clusters` at render time"**: the
whole point of the render-time-removal work done earlier this session was to guarantee
*zero* recomputation and *zero* joins during Blade rendering — this cache column preserves
that guarantee permanently, at the cost of one denormalized value per row that the engine
(not the UI) keeps in sync, transactionally, at the moment it changes (§9).

**Storage impact**: three small columns on an existing, already-large table — negligible
marginal cost relative to the table's existing width.

### 7.4 Considered and rejected: merging tiers, or dropping one

- **Merge `ownership_events` into `ownership_clusters`** (one wide table, history as JSON
  column) — rejected: mixes an append-only audit log with a hot, lockable row; every write
  would grow the exact row being locked, worsening lock hold time as history accumulates
  indefinitely on the same physical row.
- **Drop `ownership_clusters`, derive current state from `ownership_events` on demand** —
  rejected (§7.2): loses O(1) point lookup and a clean lock target; would require a
  "latest-per-group" query on every write, which is the N+1-at-scale problem this whole
  effort exists to eliminate.
- **Drop the per-row cache columns, always join `ownership_clusters` at render time** —
  rejected: directly contradicts the standing decision (this session, prior turns) that no
  page may recompute or even re-join for ownership at render time; the whole design point of
  §7.3 is that a row already knows its own answer.
- **Functional index instead of the generated `normalized_email`/`normalized_phone` columns**
  (`CREATE INDEX ... ((LOWER(TRIM(email))))`, MySQL 8.0.13+, no visible column added) —
  **seriously considered**, and it is simpler in one sense (no new column exposed in the
  schema). Rejected anyway because the engine needs to *read* the normalized value directly
  (to compare against `ownership_clusters.normalized_email`) as often as it needs to search
  by it — a functional index alone only accelerates the search, not the comparison, so the
  PHP-side normalization logic would still exist in parallel and could still drift out of
  sync with the index expression. A stored generated column gives one normalization,
  authoritative in the database, usable for both search and comparison. This is the one
  place Revision 1's design is kept exactly as proposed, having been re-examined against
  "prefer the simpler design" and found not simpler on balance.

---

## 8. Concurrency Review

Every scenario below assumes the write path described in §9 (single `SELECT ... FOR UPDATE`
on the `ownership_clusters` row for the identity+scope in question, inside one transaction).

| Race | Mechanism that makes the winner deterministic |
|---|---|
| Two agencies reach Sourcing simultaneously (same identity, same job) | Both transactions attempt to lock the same `ownership_clusters` row; MySQL serializes them — whichever commits first sees `owner_row_id IS NULL` and wins (ER3); the second sees it already set and only marks itself Duplicate (ER4/ER6). The "first" is whichever transaction's lock-acquire wins the InnoDB queue — not defined by wall-clock request-arrival order, but *is* always exactly one winner, never zero or two. |
| Company Recruiter creates while an agency is mid-way through sourcing the same identity | Same single-row lock — whichever transaction commits first establishes the fact (ER2 or ER3); the other evaluates against the now-locked owner. |
| Two Company Recruiters (same company) create the same candidate simultaneously | Same lock; both are `owner_type=company` candidates — deterministic winner is whichever commits first (§12 Gap M-CompanyRace flags that the tracker doesn't name this scenario explicitly, but the general lock mechanism resolves it the same way regardless). |
| Bulk import creates N candidates, some sharing an identity within the same batch | Each row's reservation attempt is its own transaction against the same lock target — processed one at a time even if submitted "simultaneously" as one HTTP request, because the lock serializes them; order within a single-process bulk loop is simply insertion order, which is itself an arbitrary-but-deterministic tiebreak (§9.4) once microsecond timestamps also tie. |
| API imports racing a UI-driven create | No distinction — same code path (`OwnershipEngine::attemptCompanyReservation()`), same lock target, same guarantee. |
| Queue job retry (Horizon at-least-once redelivery) | `event_uuid` uniqueness (§7.1) makes replay a no-op: the retried job attempts to insert an event with a UUID it already used; the unique constraint rejects the duplicate insert, and the job's handler treats that specific exception as "already applied, nothing to do" rather than an error. |
| Duplicate HTTP request (double-click, network retry from the client) | Same `event_uuid` mechanism, keyed off an idempotency key generated client-side or derived server-side from `(subject_row_id, event_type)` for events that are naturally at-most-once per row (e.g., a given row can only ever have one `AGENCY_SOURCED` event). |
| Deadlock | Structurally avoided, not just handled — every transaction in this design locks **at most one** `ownership_clusters` row, so there is no second lock to order against and no cycle possible. If a future change ever needs two cluster locks in one transaction (e.g., a merge operation, §12 Gap M-Merge), that would need an explicit lock-ordering rule (e.g., always lock the lower `id` first) — flagged now so it isn't forgotten if merge is ever built. |
| Rollback (e.g., the surrounding `client_stage_submit()` transaction fails after the ownership claim but before the stage write commits) | The ownership event and the stage-transition write happen in **the same database transaction** (§9) — if the stage write fails, the whole transaction rolls back, including the ownership event and cluster update, so the two can never disagree. |
| Two different identities' claims running truly in parallel (the overwhelming majority of the 50,000-concurrent-recruiter case) | No contention at all — different lock targets (different `ownership_clusters` rows), fully parallel, InnoDB row-level locking guarantees no cross-identity interference. This is the property §10 leans on for scale. |

---

## 9. Service architecture — API surface

```
app/Services/OwnershipEngine/
    OwnershipEngine.php        — the only public entry point (write + read)
    OwnershipEvent.php         — Eloquent model, ownership_events (private to this namespace)
    OwnershipCluster.php       — Eloquent model, ownership_clusters (private to this namespace)
    Rules/
        ER1_PrivateUntilSourced.php ... ER8_MultiContenderGeneralization.php
            — one small class per rule, unit-testable in isolation (§14)
    Support/
        IdentityNormalizer.php  — single source of truth for email/phone normalization,
                                   mirrors the generated-column expression exactly (§7.4)
        ScopeResolver.php        — Job vs. Company Overall scope resolution (§7, ER7)
```

```php
final class OwnershipEngine
{
    // Write paths — one per confirmed trigger event (§6)
    public function attemptCompanyReservation(int $rowId): OwnershipResult;
    public function claimForAgencySourcing(int $rowId): OwnershipResult;

    // Read paths — the only way anything else may ever learn ownership state
    public function getCachedState(int $rowId): ?OwnershipResult;
    public function explain(int $rowId): OwnershipExplanation;      // see §11
    public function history(string $identityKey, int $scopeKey): Collection; // ownership_events replay

    // Recovery — see §10
    public function rebuildCandidate(int $rowId): OwnershipResult;
    public function rebuildJob(int $jobId): int;                    // returns affected row count
    public function rebuildCompany(int $companyId): int;
    public function rebuildSystem(): int;                           // queued, never inline
}
```

Every write path funnels through one private transaction helper
(`lockAndEvaluate(string $identityKey, int $scopeKey, callable $decision)`) — this is what
makes §8's "at most one row locked per transaction" guarantee a structural property of the
code rather than a discipline every call site has to remember.

---

## 10. Recovery & Rebuild

**Principle**: `ownership_events` is the source of truth; `ownership_clusters` and the
per-row cache are *projections* of it and can always be regenerated by replaying events in
`(event_time, id)` order through the same rule classes (§9) used for live writes — not a
separate "backfill" code path that could drift from production logic.

- **`rebuildCandidate($rowId)`**: find every event referencing this row's identity+scope,
  replay from scratch (truncate the one cluster row, re-run every event's decision function
  in order), rewrite the row's cache. Cheapest, most common recovery operation — used for
  "this one candidate's ownership looks wrong, why?"
- **`rebuildJob($jobId)`**: same, for every distinct identity+scope cluster touched by rows
  in this job. Bounded by the job's own pipeline size.
- **`rebuildCompany($companyId)`**: same, for every cluster scoped to this company (both
  Job-mode and Company-Overall-mode clusters). This is the expensive one — always queued
  (§9's `rebuildSystem()` and this method both go through Horizon, never run inline), per
  the same reasoning as Revision 1's "Company Overall cold path" finding.
- **`rebuildSystem()`**: every cluster, batched by company, queued as many small jobs rather
  than one giant transaction — a full-system rebuild must never hold a single long-running
  lock or transaction; it's a fan-out of many independent `rebuildCompany()` jobs.
- **First-ever backfill** (before any `ownership_events` exist at all): synthesized events
  are generated directly from the raw historical data (`add_job_wise_condidates.created_at`
  + `timeline`'s "first reached Sourcing" per row, exactly as Revision 1's algorithm read
  them), fed through the **same** rule classes as live events — this guarantees the
  backfill and the live path can never disagree, because they are literally the same code,
  just fed a different event source once.

**Determinism proof sketch**: each rule class is a pure function of
`(existing cluster state, new event's data)` — no wall-clock reads, no "current time" other
than the event's own carried `event_time`, no dependency on which HTTP request happens to
run it. Replaying the same ordered event sequence through the same pure functions therefore
always yields the same terminal cluster state — this is exactly the guarantee event-sourced
systems are chosen for, not a property specific to this domain that has to be separately
argued.

---

## 11. Explainability

`OwnershipEngine::explain($rowId)` returns a structured answer built directly from the
cluster + the specific event that decided this row's state — for example, tracing S15:

```
Row #4821 (Company Recruiter, added 2026-07-19 14:02:11)
  State: DUPLICATE
  Because: Agency A (row #4790) reached Sourcing at 2026-07-19 09:15:44, which is
  earlier than this row's creation time. Per ER3 ("absent a valid Company Reservation,
  the earliest agency to reach Sourcing becomes owner") and ER4 ("ownership never
  flips"), Agency A remains the permanent owner of this identity in Job #310.
  Per ER5, this row is compared against that owner immediately despite being at
  InReview, since Company Recruiter rows are never privacy-shielded.
  Matches Master Tracker scenario: S15.
  Owning row: #4790 (Agency A), decided_at: 2026-07-19 09:15:44, decision_rule: ER3.
```

This is generated from stored, structured fields (`decision_rule`, `event_time`,
`owner_row_id`, the two rows' own data) — not free text written at decision time, so it
stays accurate even if the *wording* of an explanation template is improved later.

---

## 12. Gap Register — scenarios the Master Tracker does not cover

Reviewed the tracker again, specifically hunting for gaps rather than assuming completeness,
per explicit instruction. Each is real, concrete, and needs a product decision before
implementation — none are resolved unilaterally here.

| Gap | Description | Why it's not already answered |
|---|---|---|
| **M-Reject** | Does an owner retain ownership after moving to Rejected (7/9)? | No tracker row ever shows a Rejected stage; "ownership never flips... no matter how far the loser progresses" implies the *winner's* fate is equally frozen, but that's an inference, not a stated rule |
| **M-Delete** | `job_applications.deleted_at` is a real, existing column — what happens to a cluster when its owning row's parent profile is soft-deleted? | Not modeled at all; the tracker has no concept of deletion |
| **M-Restore** | Symmetric to M-Delete — does restoring reinstate the original `decided_at`, or is a restored row treated as brand-new (losing its place in line)? | Same reason |
| **M-IdentityEdit** | `job_applications.email`/`phone` are plain editable columns — an edit after ownership is decided splits or merges clusters | Not modeled; `checkDuplicateCandidate()` doesn't handle this today either, so there's no existing precedent to follow |
| **M-JobStatus** | The hierarchy says "any **eligible** job" for Company Overall scope — `jobs.status` (`active`/`hold`/`inactive`) is the obvious candidate for "eligible," but this is never stated explicitly | Confirmed the column exists (§6); confirmed the tracker never defines "eligible" beyond that one word |
| **M-CompanyDeactivated** | `companies.status` toggling to `inactive` — do that company's clusters freeze, archive, or keep evaluating normally? | Not modeled |
| **M-CompanyRace** | Two Company Recruiters at the same company independently create the same candidate on the same job | The tracker's "Company Recruiter" column only ever shows one; ER2/ER5 generalize correctly (§8) but no S-row exercises it explicitly |
| **M-CrossSignal** | A row matching only by email, another only by phone, a third matching both — do they form one cluster or two? | Neither the tracker nor the current `resolveDuplicateOwnership()` code has an explicit, deliberate answer — the current code's OR-based query merges them transitively as an implementation *accident*, not a decided rule |
| **M-Tie** | Two deciding timestamps land at the same instant | No tracker row models simultaneity; carried over from Revision 1 (§9.4 there) |
| **M-BulkBatch** | Two rows within the *same* bulk-import request are the same person | Not modeled; interacts with, but is distinct from, the concurrency mechanism in §8 |
| **M-Merge** | An admin merges two candidate profiles later found to be the same person | No merge tooling exists in the codebase today (§6) — purely a future-proofing question (§13), not a current gap in the tracker's coverage of existing behavior |
| **M-AgencyRemoved** | A client removes an agency's access entirely | No vendor-relationship table found (§6) — likely not currently expressible as an event at all |

None of these block finishing the *design* — they block **implementation sign-off** for
the specific rule each touches. Recommend resolving them as a short, explicit Q&A pass
before coding starts, rather than each being guessed at independently during
implementation.

---

## 13. Scale: 50,000 concurrent recruiters

The design's central scalability property (§7.2, §8) is that **the lock key is the
identity+scope**, not a global resource — 50,000 recruiters uploading 50,000 genuinely
different candidates at the same instant produces 50,000 *different* `ownership_clusters`
rows being touched, which is 50,000 independent, non-blocking, row-level locks. MySQL
InnoDB row locking makes this embarrassingly parallel by construction; there is no
central mutex, no global sequence, no single hot row anywhere in this design.

Contention only exists for the genuinely rare case of two+ recruiters acting on the
*same* candidate identity within the same lock-hold window (milliseconds), and even then
the critical section is a handful of indexed reads/writes on a narrow row — sub-millisecond
in practice, not a queueing bottleneck.

Concrete measures for the stated scale:
- Company Reservation attempts are queued (§ Revision 1's §6, carried forward) — the
  creation-time hot path (Upload, Similar Candidates, etc.) never waits on the ownership
  lock at all; only the queue worker does.
- The InReview→Sourcing claim stays synchronous (user-facing toast), but its lock is
  scoped to one narrow row with a bounded critical section — even at high concurrency,
  this adds low, bounded latency to a transition that already does a coin-deduction write
  in the same request.
- `ownership_events` is insert-only (§7.1) — no lock contention there regardless of volume;
  it can absorb bursty write traffic the same way `timeline` already does today.
- Read paths (the overwhelming majority of traffic — every page render) touch only cache
  columns (§7.3) — zero locks, zero joins, zero engine invocation at all.
- Horizon queue worker count for the reservation-attempt queue should scale with recruiter
  concurrency, not be fixed — flagged as an operational/infra sizing decision, not a schema
  one.

---

## 14. Test Strategy

- **Unit** — one test per rule class (ER1–ER8) in isolation, plus one test per tracker row
  (T-S1…T-S30, §3) against `OwnershipEngine` directly, no HTTP layer.
- **Integration** — `rec_candidate_assign()` (4 branches) and
  `copy_current_selected_applicant()` actually invoke `attemptCompanyReservation()` only for
  company-owned rows; `client_stage_submit()`'s hook invokes `claimForAgencySourcing()`
  exactly once, after coin deduction, in the same transaction.
- **Concurrency** — every row of §8's race table gets a test that spins up two overlapping
  transactions (via an explicit lock-hold-and-release harness or two real processes) and
  asserts exactly one deterministic winner, no deadlock, no double-cluster-row creation.
- **Load** — simulate the 50,000-concurrent-recruiter target with mostly-distinct identities
  plus a small deliberate collision rate; assert p99 latency on `attemptCompanyReservation`/
  `claimForAgencySourcing` stays bounded and that lock wait time scales with collision rate,
  not total traffic.
- **Recovery/Rebuild** — for each of `rebuildCandidate`/`rebuildJob`/`rebuildCompany`/
  `rebuildSystem`: corrupt a cache column by hand, rebuild, assert convergence back to the
  same state the event log implies; run the same rebuild twice in a row and assert byte-
  identical output (determinism).
- **Failure** — kill the process mid-transaction (simulated), assert the partial write never
  commits (cluster + event + cache stay consistent or all roll back together); replay a
  duplicated queue job and assert no duplicate event/side effect (§8's `event_uuid` test).
- Every Gap Register item (§12) that gets a product decision before implementation also gets
  its own scenario ID (M-Reject, M-Delete, ...) and its own test, following the exact same
  traceability discipline as the S1–S30 matrix — no gap gets closed by code without a named,
  reviewable rule the way §2/§3 requires for the tracker itself.

---

## 15. Future-proofing — the consumption contract

Any future capability (AI matching signals, vendor scoring, candidate merge tooling,
external APIs, reports, dashboards, notifications, analytics, audit logs) is required to
consume ownership state through exactly the three read methods in §9
(`getCachedState`, `explain`, `history`) or the future `ScopeResolver`/query-object
equivalents built on top of them — never a raw query against `ownership_clusters` or
`ownership_events` (enforced per §5.2's CI guard). This is what makes "future features can
consume the same engine without introducing additional duplicate logic" an enforced
property of the codebase rather than a hope.

---

## 16. Open product decisions (not engineering defaults) — needed before implementation

1. §7.4 / M-CrossSignal — union-find merge across email/phone, or two independent pools?
2. M-Reject — does a Rejected owner retain ownership?
3. M-Delete/M-Restore — soft-delete/restore semantics for ownership.
4. M-IdentityEdit — re-evaluation strategy when email/phone changes post-decision.
5. M-JobStatus — does `jobs.status` define "eligible job" for Company Overall scope?
6. M-CompanyDeactivated — freeze, archive, or continue evaluating a deactivated company's clusters?
7. Whether `app_duplicate` mode changes should ever trigger a backfill sweep, or stay
   prospective-only (Revision 1's default recommendation, carried forward, still just a
   recommendation).

---

## 17. Critical files (once approved — nothing below exists yet)

- Migrations: `ownership_events`, `ownership_clusters`, cache columns on
  `add_job_wise_condidates`, generated `email_normalized`/`phone_normalized` on
  `job_applications`, the `timeline` composite index (Revision 1 §3, unchanged).
- `app/Services/OwnershipEngine/` — full namespace per §9.
- `app/Helper/GeneralMethod.php` — `resolveDuplicateOwnership()` retired once a
  differential test proves the new engine reproduces it on every §3 row.
- Call-site edits: `rec_candidate_assign()` (4 branches), `copy_current_selected_applicant()`,
  Upload's 3 sites, `client_stage_submit()`'s hook (§5.1).
- `docs/CLIENT_JOB_STAGE_OWNERSHIP.md` §8.4 — updated once implementation lands, not before.
- CI guard script (§5.2), alongside the existing `scripts/ai-guards.sh`.

---
---

# REVISION 3 — Performance & Scale Readiness Review

Everything above is unchanged. This section audits it quantitatively, event by event and
query by query, before implementation sign-off — per explicit instruction that this engine
is expected to run in production for the next decade and must minimize database and server
load at 50,000 concurrent recruiters / 100M+ candidates / millions of ownership events.

## 18. Per-Event Cost Analysis

Two conventions used throughout this section:
- **"Reused" reads** are queries the *calling* method (e.g. `client_stage_submit()`) already
  runs today for its own purposes — the engine doesn't re-fetch data its caller already
  loaded into memory. This matters most for the one synchronous, user-facing event.
- All costs below assume the schema exactly as specified in §7, **except** the two
  corrections adopted in §26 (dropping two indexes, changing the UUID column type) — those
  are folded in here already so this section reflects the final, reviewed design, not the
  pre-review one.

### 18.1 `attemptCompanyReservation(rowId)` — Company Recruiter creates a candidate

Triggered from `rec_candidate_assign()` (4 branches), `copy_current_selected_applicant()`,
Upload's 3 sites — always queued (§13), never on the hot creation-request path.

| Step | Query | Reused? |
|---|---|---|
| 1 | Read subject row + join `job_applications` for identity (PK lookups both sides) | No — first thing the queued job does |
| 2 | Read scoping company's `app_duplicate` mode (PK lookup) | No |
| 3 | `SELECT ... FOR UPDATE` on `ownership_clusters` by unique (company, mode, key, email\|phone) | No |
| 3a | *(cold path only)* `INSERT` new cluster row, then re-`SELECT ... FOR UPDATE` it | N/A — only fires once per never-before-seen identity+scope |
| 4 | `INSERT` into `ownership_events` | No |
| 5 | `UPDATE ownership_clusters` (only if this row becomes owner) | No |
| 6 | `UPDATE add_job_wise_condidates` cache columns, this row only | No |

- **DB reads**: 3 (warm cluster) / 4 (cold cluster, one extra `SELECT` after the insert).
- **DB writes**: 2 (event insert + this row's own cache update) if the cluster already has
  an owner and this row loses (ER5); 3 if this row becomes the new owner (adds the cluster
  update); +1 insert on the cold path.
- **Tables touched**: `job_applications` (read), `companies` (read), `ownership_clusters`
  (read, occasionally write), `ownership_events` (write), `add_job_wise_condidates` (write).
- **Rows locked**: exactly 1 (`ownership_clusters` row) for the duration of the critical
  section; the `add_job_wise_condidates` row is locked only for the instant of its own
  single-row `UPDATE`.
- **Lock duration**: from the `FOR UPDATE` acquire to commit — 2–3 single-row writes, no
  scans in between → low single-digit milliseconds, independent of table size.
- **Total queries**: **5 typical / 8 worst case (cold identity)** — see §20 budget.
- **Big-O**: O(1) in every step — no query's cost depends on total system size.

### 18.2 `claimForAgencySourcing(rowId)` — InReview(8)→Sourcing(1) handoff

Runs **inside** `client_stage_submit()`'s existing transaction, after coin deduction, as the
last step before commit (§27 — sequencing change adopted this revision). Synchronous,
user-facing (drives the toast).

| Step | Query | Reused? |
|---|---|---|
| 1 | Subject row + identity | **Yes** — `client_stage_submit()` already has this row loaded |
| 2 | Scoping company's mode + `enable_cross_agency_duplicate` | **Yes** — the existing hook already fetches `$ownerCompany` for its own gate check |
| 3 | `SELECT ... FOR UPDATE` on `ownership_clusters` | No |
| 3a | *(cold path only)* insert + re-select | N/A |
| 4 | `INSERT` into `ownership_events` | No |
| 5 | `UPDATE ownership_clusters` (only if this row becomes owner) | No |
| 6 | `UPDATE add_job_wise_condidates` cache, this row only | No |

- **Net new queries beyond what `client_stage_submit()` already runs today**: **3 typical /
  6 worst case** — steps 1–2 are free.
- **Rows locked**: 1 (the cluster row), held for the *remainder* of the caller's transaction
  (bounded by however long the rest of `client_stage_submit()` takes after this point — which
  is why this event is sequenced last, §27, to minimize that remainder to ~0).
- **Big-O**: O(1).
- One additional real cost this revision surfaces explicitly: **if the new owner-vs-loser
  decision retroactively changes a *different* row's cache** (the S15/S23 case — an existing
  company row must flip to Duplicate because an agency just won the race), that other row's
  cache write is **not** done inline here — it's dispatched as a small queued job (§27) so
  the synchronous critical section never grows with cluster size.

### 18.3 Row rendering (AppTracker / CV Tracker / ClientApp list pages)

- **Additional queries: zero.** `ownership_state` is a plain column on
  `add_job_wise_condidates`, already part of whatever single list query the page runs today
  — it rides along in the existing `SELECT`, the same way `duplicate`/`wise_duplicate`
  already do. No join, no engine call, no per-row lookup. This is the literal O(1)-with-zero-
  marginal-cost property principle #2 asked for, not just "cheap."

### 18.4 `explain(rowId)` — on-demand, not part of rendering

Explicitly **not** part of any page load — a deliberate, user-triggered action (e.g.
clicking "why duplicate?").

| Step | Query |
|---|---|
| 1 | Subject row's cache columns + `ownership_cluster_id` (PK lookup) |
| 2 | Cluster row by `cluster_id` (PK lookup) |
| 3 | The one decisive event via `ownership_clusters.last_event_id` (PK lookup — not a scan) |
| 4 | *(only if the subject row isn't the owner)* the subject row's own `DUPLICATE_MARKED` event, indexed by `subject_row_id` |

- **Total: 3–4 queries, every one a point lookup by primary or unique key.** No event
  replay, no scan of `ownership_events` by `cluster_id` range — that access pattern is
  reserved exclusively for rebuild (§19, §22).

### 18.5 Rebuild operations

| Operation | Reads | Writes | Big-O |
|---|---|---|---|
| `rebuildCandidate($rowId)` | 1 (locate cluster) + 1 (range-scan this cluster's events, indexed) | 1 (cluster) + 1 batched multi-row `UPDATE` for every member row's cache | **O(k)**, k = events in *this one* cluster (single digits to low tens in practice — never proportional to system size) |
| `rebuildJob($jobId)` | 1 (enumerate M distinct clusters touched by this job) + M × `rebuildCandidate`'s cost | M × 2 | **O(M·k)**, M = distinct people in this job (bounded by job size) |
| `rebuildCompany($companyId)` | Chunked enumeration of clusters scoped to this company (cursor-based, §21) | Chunked | **O(clusters for this company)** — always queued, never inline (§19) |
| `rebuildSystem()` | Fanned out as many `rebuildCompany` queue jobs | — | **O(total clusters)**, but parallelized across workers, never one transaction |

---

## 19. The O(1) Read Path — hard contract

**Rendering never invokes the engine.** Every UI page reads exactly the three cache columns
on `add_job_wise_condidates` (§7.3), already present in whatever query the page runs today.
Formally:

- Rendering = zero joins to `ownership_clusters`/`ownership_events`, zero calls into
  `OwnershipEngine`, zero business-logic evaluation, ever.
- `explain()`/`history()` are **not rendering** — they are separate, deliberately-invoked,
  on-demand reads (§18.4), bounded to a handful of point lookups, never triggered by a page
  load.
- The CI guard (§5.2) is what makes this a structural guarantee rather than a convention:
  any Blade/controller code outside `app/Services/OwnershipEngine/` that touches the ledger
  tables directly fails the build.

## 20. Query Budget (maximum queries, per event/page — the explicit contract asked for)

| Path | Max queries | Notes |
|---|---|---|
| `attemptCompanyReservation` | 8 (cold) / 5 (warm) | Queued, not on the hot creation path |
| `claimForAgencySourcing` | 6 (cold) / 3 (warm) **net new** | Reuses 2 reads the caller already makes |
| List-page row render | **+0** | Rides along in the existing query |
| `explain()` | 4 | On-demand only, never during rendering |
| `history()` | 1 (range scan of one cluster's events) | On-demand only |
| `rebuildCandidate` | ~4 | O(k), k bounded and small |
| `rebuildJob` | ~2 + M·4 | M = distinct people in the job |
| `rebuildCompany`/`rebuildSystem` | Chunked, no fixed ceiling — bounded per chunk, not per operation | Always queued |

No path above has a hidden N+1 — every "per-row" cost in the rebuild rows is an explicit,
acknowledged O(k)/O(M), not an accidental loop-around-a-query.

## 21. Memory Budget

- **Every write-path event** (`attemptCompanyReservation`, `claimForAgencySourcing`) holds
  at most one subject row, one cluster row, and one event row in memory — constant, regardless
  of cluster size or system size.
- **`rebuildCandidate`** loads one cluster's full event list into memory to replay — bounded
  by k (cluster size), safe to load eagerly at the sizes this domain produces (a handful of
  contenders per person, not thousands).
- **`rebuildJob`/`rebuildCompany`/`rebuildSystem`** must **never** `->get()` the full list of
  affected clusters upfront. Use Laravel's `cursor()` or `chunkById()` to stream clusters one
  at a time (or in small batches), replaying and writing each before fetching the next — this
  is the concrete "stream, don't collect" requirement: at 100M+ candidates, a company-level
  rebuild could otherwise mean holding tens of thousands of clusters' worth of events in
  memory at once, which is exactly what chunked iteration avoids.
- No step anywhere in this design loads an entire candidate's cluster history into memory
  *just to render a page* — that would violate §19 outright, and nothing in the design does it.

## 22. Index Review — justified, not defensive

For each index: why it exists, the query that uses it, estimated selectivity, storage, write
overhead. Two indexes proposed in Revision 1/2 are **removed** here as unjustified for the
engine's actual access pattern (found during this review, not carried over uncritically).

| Index | Why | Query | Selectivity | Storage | Write overhead |
|---|---|---|---|---|---|
| `ownership_clusters` unique (company, mode, key, `normalized_email`) | The lock target + point lookup for every write event | §18.1/§18.2 step 3 | High — approaches unique per real identity+scope by construction | Small (2 ints + tinyint + varchar(190)) | 1 per cluster insert/update — cheap, this table has the fewest writes of the three |
| `ownership_clusters` unique (company, mode, key, `normalized_phone`) | Mirror, for phone-only matches | Same | Same | Same | Same |
| `ownership_events` composite (`cluster_id`, `event_time`, `id`) | Ordered replay range-scan for rebuild | §18.5 | Low per-value (many events share a `cluster_id`), but this is a *range-scan* index — that's the point, not a weakness | Moderate — this table has the highest row count long-term | 1 per event insert; **inserts are append-at-the-tail** since `event_time` is monotonically increasing, so no mid-tree page splits |
| `ownership_events` (`subject_row_id`) | Per-row `explain()` lookup | §18.4 | High — 1–2 events per row typically | Small | 1 per event insert (2nd index on this hot-insert table — justified because it's keyed on a genuinely different leading column, not redundant with the composite above) |
| `ownership_events` unique (`event_uuid`) | Idempotency (§23) | Every write path's dedup check | Perfectly unique by definition | See below | 1 per event insert (3rd index) |
| ~~`ownership_clusters` (`owner_row_id`)~~ | Proposed in Revision 1/2 for a hypothetical future "what does this row own" admin tool | **No query in this design uses it yet** | — | — | **Removed — not defensive.** Add only when a concrete admin/report query needs it. |
| ~~`job_applications` index on `email_normalized`/`phone_normalized`~~ | Revision 1/2 assumed the engine searches `job_applications` by email/phone | **It doesn't** — the engine always starts from a known `add_job_wise_condidates` row (PK lookup) and reads *that row's own* normalized values via the join in §18.1 step 1; it never searches `job_applications` broadly by identity. The cluster table's own index (above) is what does all real identity-matching work. | — | — | **Removed from the engine's requirements.** The generated columns themselves (§7.4) stay, for the normalization-consistency reason already given — only the *secondary index* on them is dropped. (`checkDuplicateCandidate()`, a separate, untouched function, may still benefit from its own indexing strategy — out of scope here.) |

**One concrete fix adopted from this review**: store `event_uuid` as `binary(16)` (or a
ULID/UUIDv7 rather than random UUIDv4) instead of a 36-character string. Two reasons: (a)
half the storage/index size, and (b) **time-ordered** UUIDs (ULID/UUIDv7) insert at the tail
of their index like the `event_time` composite above, avoiding the random-insert page-split
pattern that a purely random UUIDv4 index would cause on a high-insert-volume table — the
one place in this schema where the choice of ID format has a measurable locking/fragmentation
effect.

## 23. Locking Review

- **Smallest possible critical section, already true by construction**: every write path
  locks exactly one `ownership_clusters` row, for the shortest span that still guarantees
  correctness (§8/§18). Nothing in this review found a way to shrink it further *except*:
- **Sequencing** (adopted, §18.2/§27): `claimForAgencySourcing()` should run as the **last**
  step of `client_stage_submit()`'s transaction, immediately before commit — not because the
  lock itself is large, but because it's held for whatever *remains* of the caller's
  transaction after it fires. Placing it last minimizes that remainder to ~0.
- **Deferred side-effect writes** (adopted, §18.2): when an event changes a *different* row's
  cached state (not the triggering row's own), that write is dispatched to a queued job
  instead of happening inline — this is what keeps the critical section's write count at a
  constant 2–3 regardless of how many other rows a cluster eventually accumulates.
- **Can queries execute before locking?** Yes, and the design already does this: identity
  normalization, scope resolution, and the subject-row read (§18.1 steps 1–2) all happen
  *before* the `FOR UPDATE` acquire — only the actual arbitration decision and its writes
  happen while the lock is held.
- **Can writes be batched?** The rebuild paths already batch (§18.5, §21 — multi-row `UPDATE`
  rather than one query per member row). The live write paths have nothing left to batch —
  each is already down to the minimum 2–3 single-row writes the correctness guarantee (§8)
  requires.
- **Do indexes reduce contention?** Yes — the unique index on `ownership_clusters` is what
  makes the lock target a single, narrow, precisely-addressed row instead of a range or table
  lock; without it, "find the cluster for this identity" would require a scan, and locking
  a scanned-to row set is a very different (much worse) contention profile.

## 24. Large-Scale Validation (50K concurrent recruiters / 100M+ candidates / millions of events)

Ties §18's per-event costs directly to the stated targets:

- **Every live write/read path is O(1)** (§18.1–§18.4) — none of them re-derive cost from
  total candidates, total events, or total companies. This is what "degrade gracefully"
  means concretely here: the *only* operations whose cost scales with data size are the
  rebuild operations (§18.5), and those are explicitly chunked, cursor-based (§21), and
  always queued (§19 of Revision 2/§13) — never on a path a recruiter's request waits on.
- **Lock contention is per-identity, not global** (Revision 2 §8/§13, restated): 50,000
  recruiters uploading 50,000 distinct people touch 50,000 different `ownership_clusters`
  rows — fully parallel under InnoDB row locking. Genuine contention is confined to the rare
  case of two+ people racing to claim the *same* identity within the same millisecond, and
  even that critical section is now down to 2–3 single-row writes (§18.2, §23).
  - **Company-Overall mode's live path needs no job-enumeration at all** (a clarification
    this revision makes explicit, correcting an imprecision carried from Revision 1): once a
    cluster's `scope_key` is simply the scoping company's own id (not a per-job value), a
    live event never needs to join against `jobs` or enumerate that company's job list to find
    or lock the right cluster row — it's a direct point lookup, identical in cost to Job mode.
    The `jobs.company_id` join Revision 1 proposed only ever mattered for **bulk historical
    enumeration** (rebuild's "find every row this company owns" pass), which was already
    confined to the queued rebuild path — so this isn't a new optimization, it's a correction
    that the live path never had this cost to begin with.
- **`ownership_events`' append-only, time-ordered insert pattern** (§22) means its write
  throughput scales linearly with event volume without the index fragmentation a randomly-
  keyed high-volume table would suffer — the one place row-count growth (millions of events)
  could have degraded write latency over time, and the design specifically avoids it.
- **Enterprise customers with very large Company-Overall scopes** (thousands of jobs, a
  candidate identity appearing in hundreds of them): the *live* cost of adding one more
  contender to such a cluster is unchanged (§18.1/§18.2 — still O(1), the cluster row doesn't
  get bigger, only `ownership_events` gains one more row). Only a **rebuild** of that specific
  company would show the accumulated size, and that's explicitly the chunked, queued,
  cursor-based path (§18.5/§21) — not something a recruiter's request ever touches.

## 25. Failure Scenarios — convergence guarantee per scenario

| Failure | Why the design still converges to one correct state |
|---|---|
| **Deadlock** | Structurally impossible — every transaction locks at most one `ownership_clusters` row (§8/§23); there is no second lock to order against. |
| **Server restart mid-transaction** | Relies on InnoDB's standard crash-recovery guarantee: an uncommitted transaction is rolled back in full on restart — the event insert, cluster update, and cache write either all landed (committed before the crash) or none did. Nothing engine-specific is needed beyond this standard ACID property, but it's worth stating explicitly since it's exactly what makes "partial writes" (below) a non-issue. |
| **Queue retry (Horizon at-least-once redelivery)** | The `event_uuid` unique constraint (§22) makes replay a no-op — a retried job's insert collides on the unique key, and the handler treats that specific exception as "already applied." |
| **Duplicate requests (double-click, network retry)** | For this engine's specific event types, a *natural* idempotency key is stronger than a client-supplied one: a given `add_job_wise_condidates` row can only meaningfully fire one `AGENCY_SOURCED` event ever, so the natural key `(subject_row_id, event_type)` — not a client-generated UUID the server has to trust — is what should back the uniqueness check where applicable. |
| **Database failover** (primary → replica promotion) | Only affects transactions in flight at the exact moment of failover — those fail and roll back like any other mid-transaction failure above; the calling HTTP request surfaces a normal error and the user retries, hitting the duplicate-request path above. This is a shared infra concern (DB connection/retry configuration), not something the engine's schema needs to handle specially. |
| **Partial writes** (event committed, cluster/cache update lost, or vice versa) | Impossible by construction — the event insert, cluster update, and *the triggering row's own* cache update all happen in one transaction (§8/§18); they commit together or not at all. (Deferred side-effect cache writes to *other* rows, §18.2/§23, are a separate queued job — if *that* job fails, it's retried independently via the same `event_uuid`-style idempotency, and until it succeeds the affected other row simply shows its previous cached state, which is stale but never *wrong* in a way that contradicts the ledger — a rebuild of that one row would immediately correct it.) |

## 26. Explainability — extended worked examples (timestamps + comparisons + losers, explicitly)

Restating and extending §11's example with the exact elements requested: which scenario
matched, which rule executed, which timestamps were compared, and why the loser lost.

**Single contender pair (S15):**
```
Row #4821 (Company Recruiter, Job #310) — STATE: DUPLICATE
Master Tracker scenario matched: S15
Rule executed: ER3 (Agency Ownership) established the owner; ER5 (Company rows are
  never privacy-shielded) is why THIS row is evaluated despite being at InReview.
Timestamps compared:
  Agency A (row #4790) first_sourced_at = 2026-07-19 09:15:44.201203
  This row's created_at              = 2026-07-19 14:02:11.008812
  09:15:44 < 14:02:11 → Agency A had already established ownership before this row
  even existed, so ER2's precondition ("before any agency has established ownership")
  fails for this row — it cannot reserve.
Owner: row #4790 (Agency A), decided_at 2026-07-19 09:15:44.201203, decision_rule ER3.
```

**Three-way, Company-Overall (S26):**
```
Cluster: scoping_company_id=88, scope_mode=2 (Company Overall)
Members: Agency A (Job #310, row #4790) | Agency B (Job #333, row #4801) |
         Company Recruiter (Job #351, row #4906)
Owner: Agency A (row #4790) — decision_rule ER3, decided_at 2026-07-19 09:15:44
Why Agency A won: earliest first_sourced_at among all contenders in this
  company-wide cluster (09:15:44), earlier than Agency B's first_sourced_at
  (2026-07-19 11:40:02) and earlier than the Company Recruiter's created_at
  (2026-07-19 16:20:00).
Agency B — STATE: DUPLICATE. Rule: ER6 (agency rows are only compared once they
  themselves reach Sourcing) — Agency B's own first_sourced_at (11:40:02) is after
  the owner's (09:15:44), so it loses under ER4 (never flips).
Company Recruiter — STATE: DUPLICATE. Rule: ER5 (never privacy-shielded, compared
  immediately at creation) — created after the owner already existed, per ER2's
  precondition failing (an agency had already established ownership by 09:15:44,
  before this row's created_at of 16:20:00).
Master Tracker scenario matched: S26.
```

Both are generated purely from stored fields (§7.1/§7.2's columns) — no free text authored
at decision time, so the *wording* can improve later without the underlying facts changing.

## 27. Sequencing change adopted this revision

`claimForAgencySourcing()`'s call inside `client_stage_submit()` should be moved to
**immediately before the transaction commits** — after coin deduction, after the stage
write, after `UpdateTimeline()` — rather than merely "after coin deduction" as Revision 2
specified. This is a pure sequencing change (no new query, no schema change): its only
purpose is minimizing how long the `ownership_clusters` row stays locked, per §23.

## 28. Corrections adopted this revision (index/schema deltas from Revision 2)

1. **Dropped**: `ownership_clusters(owner_row_id)` index — no query in this design uses it
   (§22). Add later only when a concrete consumer needs it.
2. **Dropped**: secondary indexes on `job_applications.email_normalized`/`phone_normalized`
   — the engine never searches `job_applications` by identity; it only ever reads a known
   row's own normalized value (§22). The generated columns themselves are unchanged/kept.
3. **Changed**: `ownership_events.event_uuid` from a generic UUID string to `binary(16)`
   populated with a time-ordered identifier (ULID/UUIDv7), for storage and insert-locality
   reasons (§22).
4. **Changed**: any event that updates a row *other than* the one that triggered it (the
   S15/S23 retroactive-flip case) writes that other row's cache asynchronously via a queued
   job, not inline in the triggering transaction (§18.2/§23).
5. **Clarified, not changed**: Company-Overall mode's live write/read path never needs a
   `jobs` join or job-enumeration — that cost only ever existed on the rebuild path (§24).
   Revision 1/2's phrasing implied this mattered more broadly than it does; corrected here.

Nothing in §1–§4 (the Master Tracker, the rule set, or the traceability matrix) changed —
this revision is entirely about the cost of executing that same, unchanged specification.

## 29. Final Readiness Assessment

Weighed against the five stated priorities:

- **Correctness** — unchanged from Revision 2; every rule still traces to the literal
  tracker (§1–§4), untouched by this pass.
- **Determinism** — unchanged; the event-sourced replay guarantee (Revision 2 §10) doesn't
  depend on any of this revision's query-count/index changes.
- **Simplicity** — improved: two indexes removed as unjustified (§22/§28), one architectural
  ambiguity clarified away (§24/§28.5) rather than left as an implicit assumption.
- **Minimum server load** — every live path is now an explicitly quantified O(1) operation
  (§18) with a stated maximum query count (§20) and a critical section reduced to its
  provable minimum (§23); the one remaining size-dependent cost (rebuild) is fully confined
  to a queued, chunked, memory-bounded path (§18.5/§21) that never touches a live request.
- **Maximum scalability** — validated directly against the stated 50K-recruiter/100M-
  candidate targets (§24), with the specific mechanism (per-identity lock keys, O(1) live
  queries, append-only time-ordered event storage) named rather than asserted.

No avoidable bottleneck was found and left in place; no optimization was added without a
concrete query/lock/storage reason tied to a specific access pattern in this design. This
architecture is ready for implementation sign-off, pending only the product decisions listed
in §16 (unchanged by this revision) and this revision's own two dropped-index/UUID-format
corrections (§28), which should be reflected in the actual migration when it's written.
