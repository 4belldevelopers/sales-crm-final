# Centralized Duplicate Ownership Engine — Technical Design Review (Revision 1, SUPERSEDED)

> **SUPERSEDED 2026-07-19 (same day).** After reviewing this revision, the user
> reframed the whole effort from "duplicate detection" to a core, authoritative
> **Ownership Engine** with much stronger requirements (full Master-Tracker
> traceability, event history, rebuild/recovery, determinism, explainability, a full
> concurrency/scale review for 50,000 concurrent recruiters). That revision lives in
> **[OWNERSHIP_ENGINE_DESIGN.md](OWNERSHIP_ENGINE_DESIGN.md)** and is the current design
> to review/approve. This file is kept for history only — its core algorithm (§1) was
> carried forward and re-verified against the full, literal Master Tracker (recovered
> from this session's own earlier turn) in the new document; everything below this
> banner is otherwise unchanged from when it was written.

---

> Status: **DESIGN REVIEW ONLY — NOT IMPLEMENTED.** Nothing in this document has been
> built. Per explicit instruction (2026-07-19), this is the design to approve *before*
> any schema/migration/code work starts. The current, still-live state of the system is
> the read-only `GeneralMethod::resolveDuplicateOwnership()` function, called from
> exactly one place (`client_stage_submit()`'s InReview→Sourcing hook) as a stateless,
> unpersisted, advisory-only computation — see
> [CLIENT_JOB_STAGE_OWNERSHIP.md §8.4](CLIENT_JOB_STAGE_OWNERSHIP.md) for how it got
> there. This document proposes what replaces it.

---

## 0. Inputs this design is built from

- The finalized **Ownership Hierarchy** (Company Recruiter Reservation → Agency
  Ownership-by-first-Sourcing → undetermined), stated verbatim across two prior turns
  in this session and codified in the current `resolveDuplicateOwnership()` body
  (`app/Helper/GeneralMethod.php:652-780`), which this design treats as the reference
  implementation of the *decision logic* (correct, per hand-trace validation done
  earlier this session) — what's missing is everything **around** it: persistence,
  triggers, concurrency safety, and indexing.
- The **Master Tracker** (S1–S30), pasted verbatim by the user in an earlier turn now
  outside this context window. I did **not** re-fetch its literal per-row text for this
  pass (see §9 for why, and what I did instead: reconstructed and validated the
  *scenario families* the tracker exercises, from the hierarchy rules themselves and
  from the specific tie-break pairs called out explicitly in this session — S9/S15 and
  their Company-Overall mirrors S19/S23). **Before implementation sign-off, re-paste
  the literal table and I'll diff it line-by-line against §9's families** — that's a
  cheap, high-value check I'd rather flag honestly than fake.
- The existing stage/visibility model (`docs/CLIENT_JOB_STAGE_OWNERSHIP.md` §2–§4):
  master_status 8=InReview (private), 1=Sourcing (handoff/first client-visible stage),
  2–7/9=post-handoff. Only `client_stage_submit()`'s 8→1 transition is a "handoff"
  moment; the plain flow (`stage_submit()`) has no equivalent event.

---

## 1. Recap: the decision algorithm (unchanged, just relocated conceptually)

For a given **identity** (normalized email and/or phone) within a given **scope**
(single job, or every job the scoping company owns — see §2), across all matching
`add_job_wise_condidates` rows:

```
firstCompanyRow      = earliest-created row where thirdparty_company_id = 0
firstSourcedAgencyRow = earliest first_sourced_at among rows where
                        thirdparty_company_id != 0 AND first_sourced_at IS NOT NULL

owner =
  firstCompanyRow,        if it exists AND (no agency ever sourced
                           OR firstCompanyRow.created_at < firstSourcedAgencyRow.first_sourced_at)
  firstSourcedAgencyRow,  else if it exists
  undetermined,           otherwise (nothing has reached Sourcing yet — S1/S2 family)

is_duplicate(row) =
  false,                              if row == owner
  owner != null,                      if row.is_company_row  (never privacy-protected)
  owner != null AND row.first_sourced_at != null,  if row is an agency row (silent until it sources)
```

This is a **strict total order over timestamps**, which is the property that makes it
internally consistent: for any fixed set of `(created_at, first_sourced_at)` values,
exactly one row satisfies "owner" — there is no code path that can produce two owners
or zero owners-when-eligible-rows-exist. §9 stress-tests this claim, including the one
real gap it has today (timestamp ties — see §9.4).

**Ownership never flips** is enforced structurally, not by a rule that has to be
remembered: once a ledger row's `owner_row_id` is written (§2), the engine's write path
never overwrites it — only appends new duplicate members to the cluster.

---

## 2. Database schema changes

### 2.1 Why a ledger table, not just two new columns

The obvious minimal design — add `ownership_state` + `owner_row_id` columns straight
onto `add_job_wise_condidates` — was my first instinct and is **wrong** for one
concrete reason: "ownership never flips" is a claim about a *cluster of rows sharing an
identity*, not a property of any single row. If it lives only as denormalized columns
on each row, there is no single place that answers "who owns this identity right now"
without re-scanning every row that ever matched it — which is exactly the expensive,
repeated computation this whole redesign exists to eliminate. A dedicated ledger gives
one authoritative, lockable, auditable record per (scope, identity) cluster; the
per-row columns become a **cache** of that ledger's verdict, safe to denormalize
because the ledger is the source of truth they're denormalized from.

### 2.2 New table: `duplicate_ownership_ledger`

```php
Schema::create('duplicate_ownership_ledger', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('scoping_company_id');   // the company whose app_duplicate setting scoped this
    $table->tinyInteger('scope_mode');                   // 1=Job, 2=Company Overall — snapshotted at decision time
    $table->unsignedBigInteger('scope_key');              // job_id when mode=1; scoping_company_id again when mode=2
    $table->string('normalized_email', 190)->nullable();
    $table->string('normalized_phone', 32)->nullable();
    $table->unsignedBigInteger('owner_row_id')->nullable(); // add_job_wise_condidates.id — null = undetermined
    $table->enum('owner_type', ['company', 'agency'])->nullable();
    $table->enum('decision_rule', ['company_reservation', 'agency_first_sourced'])->nullable();
    $table->timestamp('decided_at')->nullable();          // when owner_row_id was first set — never updated after
    $table->timestamp('last_evaluated_at')->nullable();   // last time this cluster was touched (new member added)
    $table->timestamps();

    $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_email'],
        'dol_email_identity_unique');
    $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_phone'],
        'dol_phone_identity_unique');
});
```

**Open design wrinkle, flagged rather than silently resolved:** email and phone are two
*independent* match signals that can each pull in rows the other wouldn't. The current
`resolveDuplicateOwnership()` query ORs them together in one pass, which means three
rows — A matches by email only, B matches by phone only, C matches both — get merged
into a single transitive cluster through C, even though A and B alone share nothing.
Two nullable-unique constraints (above) can't express "one cluster per transitive
group" — they only dedupe exact email or exact phone independently, which would let the
**same** underlying cluster get represented as two different ledger rows (one keyed by
email, one by phone) if a row matches only one of them. Three ways to resolve this,
in order of my recommendation:

1. **Union-find at write time**: when a new row matches an existing email-ledger-entry
   AND a different phone-ledger-entry, merge them into one canonical entry (repoint the
   other's `owner_row_id`/rows to the survivor). More correct, more code.
2. **Split identity into two independent ledgers, never merged** (own separately by
   email-match and phone-match) — simpler, but means a candidate who used one email
   with Agency A and a different email but the *same phone* with Agency B would get two
   independent ownership decisions instead of one — probably **not** what "same
   person" duplicate detection is supposed to mean.
3. **Require at least one of email/phone to be present, and treat "identity" as
   whichever single field the row actually has** — i.e., don't try to match cross-field
   at all, only email-to-email and phone-to-phone, and treat overlap resolution as out
   of scope for v1.

I'd default to **option 1** since it matches the current code's actual behavior (which
already transitively merges via OR), but it is real, non-trivial additional logic
(effectively a small union-find/graph-merge on write) that the master tracker's 30
scenarios — as I've reconstructed them — don't appear to explicitly exercise (all the
tie-break pairs I can recall are single-signal, same-job races). **This needs a
product decision, not an engineering default**, since it changes what "duplicate" means
at the margins.

### 2.3 New/changed columns on `add_job_wise_condidates` (cache only, ledger is truth)

```php
Schema::table('add_job_wise_condidates', function (Blueprint $table) {
    $table->tinyInteger('ownership_state')->nullable()->default(null)->after('duplicate');
    // 0 = undetermined, 1 = owner, 2 = duplicate (silent — company row or not-yet-sourced... N/A), 3 = duplicate (visible)
    $table->unsignedBigInteger('ownership_ledger_id')->nullable()->after('ownership_state');
    $table->timestamp('ownership_evaluated_at')->nullable()->after('ownership_ledger_id');
});
```

Row partials (`all_apptracker.blade.php`, `all_candidates.blade.php`,
`clientappdata_ajax.blade.php`) read `ownership_state`/`ownership_ledger_id` directly —
**zero joins, zero recomputation, zero `resolveDuplicateOwnership()` calls at render
time**, which is the exact property the user asked for last turn and this design
preserves permanently rather than by convention.

### 2.4 Generated columns for indexed identity matching (on `job_applications`)

Today's matching can't use an index at all: `REGEXP_REPLACE(ja.phone, '[^0-9]', '') = ?`
wraps the column in a function, which forces a full scan on `phone` for every
evaluation. Fix with MySQL 8 generated columns (already confirmed MySQL 8.0.45 is in
use, which supports `REGEXP_REPLACE` and generated columns):

```php
Schema::table('job_applications', function (Blueprint $table) {
    $table->string('email_normalized', 190)
        ->storedAs('LOWER(TRIM(email))')
        ->nullable()
        ->after('email');
    $table->string('phone_normalized', 32)
        ->storedAs("REGEXP_REPLACE(phone, '[^0-9]', '')")
        ->nullable()
        ->after('phone');
});
```

Both are then indexable normally (§3). This also removes the current PHP-side
normalization/SQL-side normalization duplication (`GeneralMethod` does
`strtolower(trim())`/`preg_replace('/\D+/', '', ...)` in PHP for the query *parameter*,
while the query itself re-normalizes the *column* — keeping both in sync by hand is
already a latent bug risk; generated columns make the column-side normalization
authoritative and single-sourced).

---

## 3. Required indexes

| Table | Index | Why |
|---|---|---|
| `job_applications` | `(email_normalized)` | Identity lookup by email, indexed instead of function-wrapped scan |
| `job_applications` | `(phone_normalized)` | Identity lookup by phone, same reason |
| `duplicate_ownership_ledger` | `(scoping_company_id, scope_mode, scope_key, normalized_email)` UNIQUE | Cluster lookup + the write-path's lock target (§5) |
| `duplicate_ownership_ledger` | `(scoping_company_id, scope_mode, scope_key, normalized_phone)` UNIQUE | Same, phone side |
| `duplicate_ownership_ledger` | `(owner_row_id)` | Reverse lookup: "what does this row own", used by admin/debug tooling |
| `add_job_wise_condidates` | `(job_id, thirdparty_company_id, created_at)` | Company-Overall mode's per-job company-row lookup; also speeds the existing `firstCompanyRow` sort |
| `add_job_wise_condidates` | `(company_id)` | Company-Overall mode's job-ownership join target (see §8 — replaces the current `whereIn(job_id,...)` pattern) |
| `timeline` | `(application_id, job_id, master_process_name, create_at)` COMPOSITE | Makes the batched `MIN(create_at)` "first reached Sourcing" query a range scan, not a full scan — **critical at 100M+ row scale**, this table is append-only and grows forever |

The `timeline` composite index is the single highest-priority addition in this whole
design — everything else touches tables scoped to one company or one job; `timeline`
is the one table this feature reads that has no natural per-tenant bound on total size.

---

## 4. Trigger points / events

| Event | Where | What runs |
|---|---|---|
| Company-added candidate created (any of: Upload, Similar Candidates, Internal Bench, Agency Bench, Career Page, Copy-to-other-job) **where `thirdparty_company_id = 0`** | `rec_candidate_assign()` (4 branches), `copy_current_selected_applicant()`, Upload's 3 existing call sites | Attempt **Company Reservation**: create-or-touch the ledger row for this identity+scope; if no owner yet, this row becomes owner immediately (even though it's created at InReview-equivalent — company rows were never privacy-gated) |
| Agency-added candidate created (`thirdparty_company_id > 0`) | same call sites | **No reservation attempt** — agency rows only ever compete via first-Sourcing (below); creation alone does nothing to the ledger, consistent with "uploading first counts for nothing" |
| InReview(8)→Sourcing(1) transition on a client-owned job | `client_stage_submit()`, existing hook location | **Agency Ownership attempt**: this is the only moment an agency row can newly claim ownership (`treatCurrentRowAsJustSourcedNow`-equivalent — the transitioning row's own `timeline` entry doesn't exist yet at this point in the method, same caveat the current code already documents) |
| Company-owned candidate's own stage changes (any stage, any transition) | `client_stage_submit()`, `stage_submit()` | **No new reservation event** — reservation happened once, at creation; later stage moves don't re-trigger it. (A company row's *duplicate* cache flag can still change if a *different* row later claims ownership out from under it via the tie-break rule — see next row.) |
| A losing company row exists, then an agency later out-sources it (S15-family: agency sourced *before* the company row was created) | `client_stage_submit()`'s hook | The agency's claim attempt discovers the company row was created *after* its own first-sourced timestamp — agency wins, and the **already-existing company row** must be retroactively marked `ownership_state = duplicate` in the same transaction. This is the one case where a row's cache flag changes **after** it was originally written — not because ownership flipped (it never had an owner yet — see §7 state diagram, `undetermined` is not a "flip" target) |
| `companies.app_duplicate` changed (0↔1↔2) | Account Settings save | **No retroactive rewrite** — `scope_mode` is snapshotted onto each ledger row at decision time, so changing the setting never rewrites history. Only affects the scope used for **future** evaluations. Flagged product question: should turning "Overall System" on for the first time trigger a backfill sweep across existing candidates, or only apply prospectively? I'd default to **prospective-only** (matches how `checkDuplicateCandidate()`'s own mode setting already behaves — no historical backfill exists for that today either) unless told otherwise. |
| `companies.enable_cross_agency_duplicate` toggled off | Account Settings save | Ledger keeps accumulating in the background regardless (cheap, and needed so re-enabling later isn't a cold start) — the toggle only gates whether the **toast**/UI-visible consequence fires, not whether the engine runs. This matches the current code's actual gating point (the setting is checked at the *display/notify* step, not the compute step) and is worth preserving since it means turning the feature on mid-flight doesn't start from zero. |
| Candidate's email/phone edited on an existing `job_applications` row | Wherever that edit currently happens (not yet located — needs a grep pass before implementation) | Must re-run identity resolution for **both** the old and new normalized identity — this is a genuine gap with no current equivalent in `checkDuplicateCandidate()` either (that function also never handles a post-hoc email edit). Flagging as a scenario to explicitly test, not assumed handled by the trigger list above. |
| Plain flow (`stage_submit()`, fully agency-owned job, no client) | N/A | **Deliberately not wired to any of this** — confirmed there's no "become the client's responsibility" moment on a job the same company already owns end-to-end, so there's nothing for the engine to arbitrate. Preserved exactly as documented in §8.4. |

---

## 5. Transaction and locking strategy

The write path is the only place correctness is at risk (reads are cache hits against
already-decided columns — no locking needed there at all). Every write path funnels
through one method that does:

```
BEGIN TRANSACTION
  SELECT ... FROM duplicate_ownership_ledger
    WHERE scoping_company_id = ? AND scope_mode = ? AND scope_key = ?
      AND (normalized_email = ? OR normalized_phone = ?)
    FOR UPDATE
  -- if no row: INSERT a new ledger row (owner_row_id = NULL) inside this same
  --   transaction, then re-SELECT ... FOR UPDATE it (guarantees we're locking the
  --   row we just created, closing the classic insert-then-lock race)
  -- if owner_row_id IS NULL: run the arbitration algorithm (§1) across every row
  --   currently in scope + the triggering row; set owner_row_id/decided_at if a
  --   winner emerges
  -- if owner_row_id IS NOT NULL already: owner is untouchable — only decide whether
  --   the triggering row (and, per the S15-retroactive-fix case in §4, any existing
  --   row whose duplicate-status this new fact changes) gets appended to the
  --   duplicate set
  UPDATE add_job_wise_condidates SET ownership_state = ..., ownership_ledger_id = ...
    WHERE id IN (affected row ids)   -- cache write, same transaction
COMMIT
```

**Why `SELECT ... FOR UPDATE` and not optimistic (version-column) locking:** the
critical section here is short (a handful of indexed reads + a bounded write), and the
contention is inherently *per-identity* — two recruiters racing to submit the same
candidate to the same job's Sourcing stage is a real but narrow hot spot, not a
system-wide bottleneck. Pessimistic locking scoped to one ledger row is simpler to
reason about than retrying on optimistic-lock failures for a decision that has to be
correct exactly once. Isolation level: InnoDB's default **REPEATABLE READ** is fine —
the `FOR UPDATE` row lock is what actually prevents the race, not the isolation level
itself.

**Deadlock avoidance:** a transaction ever locks **at most one** ledger row (the
scope+identity being evaluated) plus a bounded `UPDATE ... WHERE id IN (...)` on
`add_job_wise_condidates` rows that all belong to that same cluster — there's no
scenario in this design where two ledger rows are locked in the same transaction, so
there's no lock-ordering problem to solve.

**Unique-constraint race (two concurrent first-time inserts for the same new
identity):** the `INSERT` in the "no row found" branch can itself collide under
concurrency (two requests both see "no row," both try to insert). Handle with a
catch-and-retry on the unique-constraint-violation exception: on collision, don't
error — just re-run the `SELECT ... FOR UPDATE` (the other transaction's insert already
committed) and continue from there. This is a standard "upsert via
insert-catch-retry" pattern, not a new one specific to this table.

---

## 6. Concurrency handling (request-path vs. background)

Two different call patterns need different answers:

- **Company Reservation on candidate creation** (Upload, Similar Candidates, etc.) —
  no user-facing "you just won/lost a race" message exists today at creation time, and
  none is being proposed. This can safely be **queued** (`ResolveOwnershipReservation`
  job on the existing Horizon queues) rather than run inline — keeps the hot,
  high-volume "add candidate" paths from ever waiting on a ledger lock, at the cost of
  a brief window (queue latency) where the row's `ownership_state` cache is still
  `NULL`/stale. Acceptable since nothing currently reads that cache synchronously right
  after creation.
- **Agency Ownership attempt at InReview→Sourcing** — this **is** user-facing (the
  losing agency's toast, gated by `enable_cross_agency_duplicate`). This one stays
  **synchronous, inline, inside the existing transaction** that already wraps the coin
  deduction and stage write in `client_stage_submit()` — the lock scope is one ledger
  row, held for a handful of indexed reads, so the added latency is small and bounded,
  and an eventually-consistent toast (arriving after the page already re-rendered)
  would be a confusing UX regression. Flagged explicitly: **do not queue this one**,
  even though queuing is the general instinct for anything DB-lock-adjacent — the
  correctness/UX requirement here overrides the usual "queue heavy work" guidance from
  CLAUDE.md rule 6, because this isn't heavy (bounded, single-row-locked) and the
  synchronicity is load-bearing for the feature's own purpose.
- **Company-Overall mode's cold-start evaluation** (§8) — this is the one path that
  really can be heavy on a large company's first-ever evaluation of a given identity —
  queue this specific case, with the synchronous path only handling the fast, already
  indexed steady-state lookup.

---

## 7. State transition diagram

Two separate state machines — conflating them was the main conceptual trap in the old
"any match = flag everyone" version this replaces.

**(a) Cluster-level state** (one per scope+identity, lives in the ledger row):

```
                 ┌─────────────┐
                 │  NO_OWNER    │  (no row has ever reserved or sourced)
                 └──────┬───────┘
                        │ first company row created,
                        │   OR first agency row reaches Sourcing
                        ▼
                 ┌─────────────┐
                 │ OWNER_LOCKED │  (owner_row_id set — terminal, never re-entered)
                 │  (row X)     │
                 └─────────────┘
```

Only one outbound edge exists from `NO_OWNER`, and there is no edge back — matching the
"ownership never flips" requirement structurally (the transaction in §5 refuses to
touch `owner_row_id` once non-null).

**(b) Row-level display state** (derived per-row, cached in `ownership_state`):

```
   company row created ──────────────────────────► OWNER  (if cluster had NO_OWNER)
                        └───────────────────────► DUPLICATE_VISIBLE  (if cluster already OWNER_LOCKED)

   agency row created ──► PRIVATE (InReview-equivalent, no cache value written yet)
        │
        │ row reaches Sourcing
        ▼
   cluster still NO_OWNER? ──► OWNER  (this row becomes the cluster's owner)
   cluster already OWNER_LOCKED,
     and this row IS the owner? ──► OWNER
   cluster already OWNER_LOCKED,
     this row is NOT the owner? ──► DUPLICATE_VISIBLE
```

Note there is **no `DUPLICATE_SILENT` cache value** — an agency row sitting at
InReview simply has `ownership_state = NULL` (undetermined/not-yet-evaluated), which
*is* the "silent" state; it only gets a real cache value the moment it sources and the
transition above runs. This avoids ever having to remember to hide a "silent duplicate"
value at render time — if it's silent, nothing was ever written.

---

## 8. API / service architecture

**Deliberate exception to CLAUDE.md rule 9** ("no repository layer; fat controllers +
services; don't add layers unprompted"): this warrants a dedicated service class rather
than more static methods bolted onto `GeneralMethod`, because it now owns a
transaction/locking contract that every call site must go through identically — a
single class makes that contract enforceable (private constructor for the transaction
helper, one public entry point) in a way that free-floating static helpers on a
1000+-method god-helper don't. Flagging this explicitly for sign-off since it's a
structural deviation, not assuming it's pre-approved by the general "don't add layers"
rule.

Proposed shape:

```
app/Services/DuplicateOwnership/
  DuplicateOwnershipEngine.php   — the only public entry point
  OwnershipResult.php            — plain DTO: ownerRowId, ownerType, duplicateRowIds[], clusterState
  IdentityKey.php                — value object: normalizeEmail(), normalizePhone(), matches another IdentityKey
```

```php
class DuplicateOwnershipEngine
{
    // Company Reservation attempt — called from every "add to job" path.
    // Queued by the caller (see §6); this method itself is synchronous once invoked.
    public function reserveForCompanyRow(int $rowId): OwnershipResult;

    // Agency Ownership attempt — called from client_stage_submit()'s 8->1 hook only.
    // Synchronous, inline, inside the caller's existing transaction.
    public function claimForAgencySourcing(int $rowId): OwnershipResult;

    // Cache-only read for Blade/UI — no computation, no lock, just the two columns.
    public function getCachedState(int $rowId): ?OwnershipResult;
}
```

Call-site mapping (mechanical, once the engine exists):

| Call site | Method |
|---|---|
| `rec_candidate_assign()` (4 branches), `copy_current_selected_applicant()`, Upload's 3 sites — only when `thirdparty_company_id == 0` | `reserveForCompanyRow()` (queued) |
| `client_stage_submit()`, existing 8→1 hook | `claimForAgencySourcing()` (sync, inline) |
| `all_apptracker.blade.php`, `all_candidates.blade.php`, `clientappdata_ajax.blade.php` row partials | `getCachedState()` — or, per the standing decision in §8.4 of the ownership doc, simply read `$row->ownership_state` directly with no method call at all, since it's already the row's own column via the existing ES/MySQL select |

**Company-Overall mode scope resolution — do not pre-pull job IDs into an `IN` list.**
The current code's `$eligibleJobIds = DB::table('jobs')->where('company_id', ...)->pluck('id')`
followed by `whereIn('ajwc.job_id', $eligibleJobIds)` is fine at today's data volume but
is a known cliff at the stated 10,000-company/100M-row target — a company with
thousands of jobs turns into a multi-thousand-element `IN` clause on every evaluation.
Replace with a direct join:

```php
$query->join('jobs as scope_jobs', function ($j) use ($ownerCompanyId) {
    $j->on('scope_jobs.id', '=', 'ajwc.job_id')->where('scope_jobs.company_id', '=', $ownerCompanyId);
});
```

---

## 9. Unit / integration test plan (organized by scenario family)

I could not re-fetch the master tracker's literal S1–S30 text in this pass (it was
pasted in an earlier turn now outside my context window, and re-deriving 30 rows of
someone else's spec from memory risks silently drifting from what was actually
specified). What I *can* do honestly is organize the test plan around the scenario
**families** the hierarchy rules themselves imply, cross-referenced against the
specific pairs this session already named explicitly (S9/S15, S19/S23). **Ask:
re-paste the tracker table before implementation sign-off so I can map each family
below to its literal S-number and confirm nothing was missed or miscategorized** — this
is a 5-minute check that removes the one real uncertainty in this whole design.

| Family | Representative case | Expected result |
|---|---|---|
| **F1 — No eligible rows** | Only InReview-stage rows exist (agency, never sourced); or nothing at all matches the identity | Cluster stays `NO_OWNER`; every row's cache is `NULL` (silent) — maps to the tracker's S1/S2 |
| **F2 — Single company row, no competition** | One company-added row, no agency rows | Company row becomes owner trivially (reservation with nothing to contend against) |
| **F3 — Agency-vs-agency race** | Two+ agency rows, all eventually reach Sourcing at different times | Earliest `first_sourced_at` wins regardless of which was *uploaded* first — maps to S3/S4 |
| **F4 — Company-created-first, agency sources later** | Company row `created_at` < agency's `first_sourced_at` | Company wins (reservation); agency row shown as duplicate the moment it sources — maps to S9 |
| **F5 — Agency-sources-first, company created later** | Agency `first_sourced_at` < company row's `created_at` | **Mirror of F4** — agency wins; the company row, though created after, is still shown as duplicate immediately (never privacy-protected) — maps to S15. **This pair (F4/F5) is the one place a pure "whoever exists first, period" rule would give the wrong answer for one of the two orderings — the algorithm must compare the right two timestamps (company `created_at` vs agency `first_sourced_at`), not row-creation-order alone.** |
| **F6 — Company-Overall mirrors of F4/F5** | Same as F4/F5 but scope = every job the company owns, matches spread across two different jobs | Same outcome, scope widened — maps to S19/S23 |
| **F7 — Agency reserved silently, not yet visible** | Owner already decided (by another agency or the company); a losing agency row still sits at InReview | Losing row's cache stays `NULL`/undetermined-display even though the cluster already has an owner — only becomes `DUPLICATE_VISIBLE` once *this* row sources — maps to S13/S14 |
| **F8 — Third+ contender after owner locked** | Owner already decided; a brand-new third row (company or agnecy) enters the cluster later | New row is never compared against "whoever's earliest" again — it's compared only against the already-locked owner; always loses, never re-opens arbitration |
| **F9 — Same company, two of its own recruiters both add the candidate to the same job** | Two company rows (`thirdparty_company_id = 0`), no agency involved | Earliest becomes owner; the later one is a duplicate against **its own company's** earlier submission — this is implied by "company rows are never privacy-protected... regardless of stage" but **I don't recall a tracker row that explicitly names an all-company scenario** — flag for confirmation, not assumed |
| **F10 — Rejected/withdrawn owner** | The owning row later moves to Rejected | Ownership stays with it regardless ("never flips... no matter how far the loser progresses" — implies the *winner's* later fate is equally frozen) — flag for explicit product confirmation, since it means a client could be told "already owned by Agency X" for a candidate Agency X has since rejected |
| **F11 — Identity edited post-hoc** | A `job_applications.email`/`phone` value changes after ownership was already decided | Neither the current code nor `checkDuplicateCandidate()` has ever handled this — needs its own resolution strategy (re-evaluate old + new identity clusters), not something to assume "just works" |
| **F12 — Cross-signal identity overlap** | Row A matches only by email, Row B matches only by phone, Row C matches both | Exposes the union-find question from §2.2 — needs a product decision before a test can even be written, since "expected result" depends on which of the 3 options in §2.2 is chosen |
| **F13 — Timestamp tie** | Two rows' deciding timestamps land in the same second (or literally simultaneous under concurrent writes) | Exposes the ordering gap in §9.4 below — needs microsecond precision + an explicit deterministic tiebreak, not left to incidental row order |

Each family becomes a small set of Pest/PHPUnit cases against
`DuplicateOwnershipEngine` directly (no HTTP layer needed for the core logic — only the
two trigger-point integration tests below need to go through the controllers):

- **Integration test A**: `rec_candidate_assign()` (each of the 4 branches) actually
  invokes `reserveForCompanyRow()` when `thirdparty_company_id == 0`, and does **not**
  invoke anything when it's an agency-sourced add.
- **Integration test B**: `client_stage_submit()`'s 8→1 transition invokes
  `claimForAgencySourcing()` exactly once, after the coin-deduction block (never
  before, never blocking it on failure), and the response's `crossAgencyDuplicate` key
  reflects the *row's own* post-arbitration state, not "any match existed."
- **Concurrency test**: two near-simultaneous `client_stage_submit()` calls for two
  different agency rows sharing one identity — assert exactly one becomes owner and no
  deadlock/duplicate-ledger-row occurs (this is the one test class that actually needs
  to spin up overlapping DB transactions, e.g. via two processes or an explicit lock
  hold-and-release harness, not just sequential PHPUnit calls).

### 9.4 The one concrete bug this design review surfaces in the *current* algorithm

`resolveDuplicateOwnership()`'s comparisons (`strtotime($a->created_at) <
strtotime($b->first_sourced_at)`) have no defined tiebreak when two timestamps are
equal at whatever precision the column stores. MySQL `DATETIME`/`TIMESTAMP` columns
default to second-level precision unless declared `DATETIME(6)`; under real concurrent
writes (§5/§6), two events landing in the same second is not a theoretical edge case at
scale. Laravel's `Collection::sortBy` is stable (PHP 8's underlying sort is stable), so
a tie currently resolves to "whichever the SQL query happened to return first" — which
has no `ORDER BY` tiebreak today, meaning it's **effectively arbitrary and
non-deterministic** run to run. Fix (required, not optional, before implementation):

1. Widen `add_job_wise_condidates.created_at` and `timeline.create_at` comparisons to
   microsecond precision (`DATETIME(6)`) where the underlying columns support it
   (confirm current column definitions — if they're already `TIMESTAMP` default
   precision, this is itself a migration).
2. Add an explicit secondary sort key (row `id`, ascending) to both the
   `firstCompanyRow` and `firstSourcedAgencyRow` resolution queries, so a genuine tie
   has a deterministic, reproducible winner instead of an accidental one.

---

## 10. Performance considerations for Company-Overall mode

Company-Overall mode is the one scope that has no natural per-request bound — it's
"every job this company has ever owned." At the stated scale target (10,000 companies,
100M+ records/year):

- **Don't pre-pull job IDs into an `IN` list** (§8) — join against `jobs.company_id`
  directly. An `IN` list with thousands of elements changes MySQL's query plan for the
  worse and bloats the query string itself.
- **The ledger amortizes the cost.** The expensive step — scanning every row that's
  ever matched this identity across the company's whole job history — only happens
  once per identity, the first time it's ever evaluated for that company+mode. Every
  subsequent evaluation is an indexed point lookup on the ledger (§3) plus, at most, a
  handful of rows to append to an already-known cluster. Company-Overall mode is
  expensive on the **cold path**, not on steady state — size the "queue this specific
  case" decision in §6 around cold-path latency, not steady-state throughput.
- **The `timeline` composite index (§3) matters more here than anywhere else** — this
  mode's identity lookup can span a company's entire multi-year job history, so a
  missing index here is the difference between an indexed range scan and a full table
  scan of a table with no natural per-tenant size cap.
- **Recommend a per-company circuit breaker / sampling cap** for pathological cases
  (a company with, hypothetically, 50,000 jobs and a candidate identity that genuinely
  appears in hundreds of them) — cap the cluster evaluation at some bounded N most
  recent matches rather than an unbounded scan, with a documented, deliberate
  trade-off (extremely rare, extremely high-volume identities get an approximate rather
  than exhaustive arbitration). Flagging as a recommendation, not assuming the user
  wants this complexity — could also just be accepted as a rare, acceptable slow path
  if the product doesn't expect this scale of single-identity fan-out in practice.
- **Async by default for this mode specifically** (§6) — Job-mode's scope is naturally
  bounded (one job's pipeline), so it can stay synchronous; Company-Overall mode should
  default to queued evaluation with the row's cache showing "pending" until the queued
  job completes, rather than making a request wait on a potentially large scan.

---

## 11. Consistency validation summary

What I verified holds, and what I could not fully verify without the literal tracker:

✅ **Verified — single-valued outcome.** The owner-selection rule is a strict
comparison over two well-defined values (`firstCompanyRow.created_at` vs
`firstSourcedAgencyRow.first_sourced_at`), each itself a deterministic `MIN`/earliest
selection — no code path produces two different "owners" for the same fixed input, and
`undetermined` only occurs when neither value exists yet (F1).

✅ **Verified — "never flips" is structurally enforced**, not just documented, once
ownership lives in a ledger row that the write path refuses to overwrite (§5, §7a) —
this closes what was previously just a stated rule with no code path actually
preventing violation.

✅ **Verified — the tie-break pairs (F4/F5 ≡ S9/S15, F6 ≡ S19/S23) are true mirror
images** produced by the *same* comparison run in opposite timestamp orderings, not two
separate rules — a good sign of internal consistency, since a spec that needed two
different rules to cover both orderings would itself be suspect.

⚠️ **Not verified — timestamp ties (F13/§9.4).** This is a genuine, concrete gap in
the current algorithm, independent of anything about persistence — worth fixing
regardless of whether this redesign proceeds.

⚠️ **Not verified — cross-signal identity overlap (F12/§2.2).** Needs a product
decision (union-find merge vs. independent email/phone pools) before it can be called
consistent either way — right now the *existing* code has an implicit answer (merges
transitively via OR) that may not be the intended one.

❓ **Unconfirmed against the literal tracker — F9 (same-company internal duplicate)
and F10 (owner later rejected).** Both follow logically from the stated rules ("company
rows never privacy-protected regardless of stage," "ownership never flips no matter how
far the loser progresses") but I don't have firm recall of a tracker row naming either
scenario explicitly. Re-pasting the table resolves this in minutes; I'd rather flag the
uncertainty than assert coverage I can't currently point to.

---

## 12. What this design deliberately does NOT decide yet

- The exact resolution to §2.2 (cross-signal identity merge strategy) and §4's
  "settings-change backfill" question — both need a product answer, not an engineering
  default, since they change observable behavior at the margins.
- Whether F9/F10 are actually correct per the tracker (§11) — pending re-confirmation.
- Any UI change beyond what already exists — this document is schema/engine/trigger
  design only; the AppTracker badge, CV Tracker/ClientApp Ownership column, and
  Account Settings copy all stay exactly as they are today (render-time reads of
  whatever the engine caches, per the standing decision in
  `docs/CLIENT_JOB_STAGE_OWNERSHIP.md` §8.4) until a separate UI pass is explicitly
  requested.

---

## 13. Critical files (once approved)

- New migration(s): `duplicate_ownership_ledger` table; `ownership_state` /
  `ownership_ledger_id` / `ownership_evaluated_at` on `add_job_wise_condidates`;
  `email_normalized` / `phone_normalized` generated columns on `job_applications`; the
  `timeline` composite index.
- New: `app/Services/DuplicateOwnership/DuplicateOwnershipEngine.php` (+
  `OwnershipResult`/`IdentityKey` value objects).
- `app/Helper/GeneralMethod.php` — `resolveDuplicateOwnership()` retired in favor of
  the new engine once it's proven equivalent on every family in §9 (keep the old method
  temporarily as a reference/oracle for a differential test, then remove).
- Trigger-point edits: `rec_candidate_assign()` (4 branches),
  `copy_current_selected_applicant()`, Upload's 3 sites, `client_stage_submit()`'s
  existing hook — each becomes a call into the new engine instead of the old function.
- `docs/CLIENT_JOB_STAGE_OWNERSHIP.md` §8.4 — updated once implementation actually
  lands, not before.
