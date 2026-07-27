# 20 — Goals / Non-goals

> *Intent: keep v2 from sprawling into v3 features. Every non-goal listed here*
> *is something we considered and said no to; if it shows up in a v2 PR, it gets*
> *asked back to product.*

## Goal hierarchy (what success looks like)

### G-1 — Crash-free at 10,000 concurrent users

10K is a marketing number from SCALABILITY.md. Concretely: 10K clients
sending presigned PUT requests to R2 at the same moment must produce
zero server-side 5xx attributable to our code path.

**Measureable**:
Error budget: ≤0.01% 5xx attributable to resume endpoints. Verified
with `php artisan test --filter=ResumeV2*` and a `10k_concurrent.php`
load harness under `tests/Load/`.

### G-2 — Zero data loss on network failure

If a tab loses connection during upload, refresh, parser, or submit,
the resume either makes it to the S3 final bucket OR the user sees an
honest "we didn't get it" state — never both-failed and never double-billed.

**Measureable**: chaos harness ([future-resume-upload-subsystem-map.md §resume][memory-map] §Open follow-ups rolled forward) with 1,000 iterations of forced disconnect varies in 7 positions; ≥99% recover without upload duplication, ≥0% lose bytes.

### G-3 — Manual values always win — the v1 product rule, hardened

If recruiter types `Alice Smith` after parse pre-fills `Alice Smith`,
the saved record has their text byte-for-byte. We test this in a
single test: parse fills → user types → submit → fetch candidate →
assert full_name bytes match the typed string (not the parsed one).

**Measureable**: `test_manual_values_always_win_over_parser`.

### G-4 — Exactly one parser attempt per resume per minute

OpenAI / parser-side 429 = `Retry-After` honored. A second failure goes
to `Failed` and surfaces in UI; a third attempt must come from the recruiter
re-clicking, not from automation.

**Measureable**: `test_parser_not_retried_under_threshold`, `test_parser_three_failures_go_to_failed_status`.

### G-5 — State machine transitions are race-safe across N pods

Two pods reading `Created` and both writing `Uploaded` — only one wins,
the other is a no-op. Already proved for `Reconcile`/`Repair` in v1; v2
extends the same correctness primitive to all transitions.

**Measureable**: `test_two_pod_race_on_upload_finished_transition`.

### G-6 — Realtime progress in every open tab on the same tenant

Open two tabs → upload in tab A → tab B sees `parser_progress=42%` from
Reverb.

**Measureable**: `test_reverb_progress_visible_across_same_tenant_tabs`,
`test_reverb_progress_NOT_visible_across_different_tenants`.

### G-7 — Capacity numbers are predictable

100% honest visibility into how many pods, how much Redis, how much R2
budget, what the per-doctrine backpressure thresholds are. When
someone asks "could we run 50K?" the answer reads off the dashboard.

**Measureable**: Health endpoint [/api/internal/health/resume] is registered and produces JSON consumed by the Grafana board.

### G-8 — Bulk upload gets a path that is NOT a hack

Even though bulk is a non-goal for v2, the v2 primitives (presigned
PUT, idempotency, queue) are picked so a v3 bulk path can compose
them without rewrites.

**Measureable**: `tests/Feature/ResumeV2PresignTest` includes a "tenant
issues 100 parallel presigns" case that saturates the rate limit
cleanly at the boundary.

## Non-goals (v2 will explicitly NOT do these)

### NG-1 — Replace `ChatGptService` with a polyglot fallback parser

Future ticket. v2 wraps it cleanly behind `ResumeParserService` (this
exists in v1 era; reused) but does not implement LLM failover.

### NG-2 — Marketplace cross-tenant talent ingestion

Bench candidates come through a different pipeline
(`StaffingCompany*` surface). v2 does not touch them.

### NG-3 — Auto-extracted location inference (city/state from text)

The parser returns 8 fields today; lat/lng is something we deliberately
do not put in v2 — it's a downstream enrichment layer.

### NG-4 — WebSocket for the public career form

Public career form uses HTTP polling at 1 Hz. Reverb is internal-tenant
only in v2. Reduces Reverb pod scale-out budget drastically.

### NG-5 — Multi-region active/active

Single-region primary + warm standby. DR is out of scope.

### NG-6 — Re-designing the recruiter add-candidate *Blade*

The v2 work changes data-model and controller-side. The Blade is
written against v2 contracts; restyling happens in a follow-up. The
recruiter UX IS in scope (manual-always-wins, parallel upload + parse,
offline / cancel / refresh), but the visual layout passes through as-is.

### NG-7 — A 100% greenfield rewrite

Vertical slices into the god controller are necessary — but the
`HomeController` isochronous surface (careerApply, careerStore, candidateCareerApply)
persists as a thin delegating wrapper, not a deletion. Delete it after
v2 is verified across one full sprint cycle.

### NG-8 — Quota/billing changes

Scratch behavior on `coins` is out of scope (CLAUDE.md rule 8 sign-off).

[memory-map]: ../.claude/projects/C--Users-VIBHANSHU-JAISWAL-000-Documents-GitHub-JWMaincode28June2026/memory/future-resume-upload-subsystem-map.md
