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

If a tab loses connection during upload, refresh, or submit,
the resume either makes it to the S3 final bucket OR the user sees an
honest "we didn't get it" state — never both-failed and never double-billed.

**Measureable**: chaos harness ([future-resume-upload-subsystem-map.md §resume][memory-map] §Open follow-ups rolled forward) with 1,000 iterations of forced disconnect varies in 7 positions; ≥99% recover without upload duplication, ≥0% lose bytes.

### G-3 — Manual values always win — the v1 product rule, hardened

If recruiter types `Alice Smith` after parse pre-fills `Alice Smith`,
the saved record has their text byte-for-byte. We test this in a
single test: parse fills → user types → submit → fetch candidate →
assert full_name bytes match the typed string (not the parsed one).

**Measureable**: `test_manual_values_always_win_over_parser`.

### G-4 — REMOVED 2026-07-27 — server-side parser removed

> v2 has no server-side parser. There is no per-minute attempt budget,
> no `Retry-After` to honor, no `Failed` status from a parser outage.
> The browser-side `pdf.js` runs in 50–200 ms locally; failures
> degrade to "empty form fields" (INV-2) and the recruiter types
> manually. This goal was the source of `test_parser_*` — those
> tests are REMOVED.

### G-5 — State machine transitions are race-safe across N pods

Two pods reading `Created` and both writing `Uploaded` — only one wins,
the other is a no-op. Already proved for `Reconcile`/`Repair` in v1; v2
extends the same correctness primitive to all transitions.

**Measureable**: `test_two_pod_race_on_upload_finished_transition`.

### G-6 — Realtime status in every open tab on the same tenant

Open two tabs → upload in tab A → tab B sees `ResumeUploaded` from
Reverb (status only — REMOVED 2026-07-27: parser progress is
browser-side, never crosses Reverb).

**Measureable**: `test_reverb_status_visible_across_same_tenant_tabs`,
`test_reverb_status_NOT_visible_across_different_tenants`.

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

### NG-1 — REMOVED 2026-07-27 — no server parser to replace

> v2 has no server-side parser. The parser is browser-side
> (`pdf.js` + `re_test()`) and runs from the local `File` object.
> There is no polyglot fallback to design. A future v3 may
> re-introduce a server-side parser; this section would be the
> place to document the policy.

### NG-2 — Marketplace cross-tenant talent ingestion

Bench candidates come through a different pipeline
(`StaffingCompany*` surface). v2 does not touch them.

### NG-3 — Auto-extracted location inference (city/state from text)

The browser-side `pdf.js` exposes the same fields; lat/lng is
something we deliberately do not put in v2 — it's a downstream
enrichment layer. (REMOVED 2026-07-27: "parser returns" was
rephrased; the parser is now a local pdf.js worker.)

### NG-4 — WebSocket for the public career form

Public career form uses HTTP polling at 1 Hz. Reverb is internal-tenant
only in v2. Reduces Reverb pod scale-out budget drastically.

### NG-5 — Multi-region active/active

Single-region primary + warm standby. DR is out of scope.

### NG-6 — Re-designing the recruiter add-candidate *Blade*

The v2 work changes data-model and controller-side. The Blade is
written against v2 contracts; restyling happens in a follow-up. The
recruiter UX IS in scope (manual-always-wins, parallel upload + browser
parse, offline / cancel / refresh), but the visual layout passes
through as-is.

### NG-7 — A 100% greenfield rewrite

Vertical slices into the god controller are necessary — but the
`HomeController` isochronous surface (careerApply, careerStore, candidateCareerApply)
persists as a thin delegating wrapper, not a deletion. Delete it after
v2 is verified across one full sprint cycle.

### NG-8 — Quota/billing changes

Scratch behavior on `coins` is out of scope (CLAUDE.md rule 8 sign-off).

[memory-map]: ../.claude/projects/C--Users-VIBHANSHU-JAISWAL-000-Documents-GitHub-JWMaincode28June2026/memory/future-resume-upload-subsystem-map.md
