# Implementation Prompt — Resume Upload v2 (for another AI)

> **Update 2026-07-27 — server-side parser removed.** This prompt now
> instructs the implementing AI to build a parser-free upload subsystem.
> The single parser is `pdf.js` + `re_test()` running in the browser
> from the local `File` object, parallel with the R2 PUT. There is no
> `ParseResumeJob`, no `parse_resumes` queue, no
> `resume_parse_results` table, and no `ResumeParsed` /
> `ResumeParseFailed` events.

> **Intent:** A self-contained prompt you can paste into a fresh AI session to
> have it implement Resume Upload v2 for JERO. The prompt bakes in the
> project rules, the canonical contracts, the no-merge rule, and the doc tree
> to read. It is project-aware (CLAUDE.md + .ai memory) and code-pattern
> aware (Laravel 11 / Eloquent / MySQL / Redis / Reverb / Octane).

---

## Where the docs live (read these, in this order)

**Working directory (only place to make changes):**
`C:\Users\VIBHANSHU JAISWAL.000\Documents\GitHub\JWMaincode28June2026`

**Project entry-point:**
- [`CLAUDE.md`](../../../CLAUDE.md) — read first, every session
- [`.ai/PROJECT_MEMORY.md`](../../../.ai/PROJECT_MEMORY.md) — living state + decisions
- [`.ai/tasks/`](../../../.ai/tasks/) — module playbooks
- [`.ai/summaries/`](../../../.ai/summaries/) — 1-line class indexes

**Resume Upload v2 design tree (read in this order):**

| # | Path | Why read it |
|---|---|---|
| 1 | [`docs/future_resume_upload_2.0/RESUME_UPLOAD_V2_COMPLETE.md`](RESUME_UPLOAD_V2_COMPLETE.md) | The executive view — read first |
| 2 | [`docs/future_resume_upload_2.0/96_invariants.md`](96_invariants.md) | The 5 non-negotiable rules + tests |
| 3 | [`docs/future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md) | The 20-step plan with code |
| 4 | [`docs/future_resume_upload_2.0/40_data_model.md`](40_data_model.md) | Schema, indexes, status enum |
| 5 | [`docs/future_resume_upload_2.0/50_api_contracts.md`](50_api_contracts.md) | EP-1..6 JSON shapes, headers, errors |
| 6 | [`docs/future_resume_upload_2.0/99_decision_log.md`](99_decision_log.md) | ADRs 001..004, 006..014, 016..018 — ADR-005 + ADR-015 REMOVED (parser-related) |
| 7 | [`docs/future_resume_upload_2.0/90_rollout_plan.md`](90_rollout_plan.md) | 14-step rollout + feature flags |
| 8 | [`docs/future_resume_upload_2.0/45_failure_modes.md`](45_failure_modes.md) | All 20 FAIL-* scenarios |
| 9 | [`docs/future_resume_upload_2.0/55_tenancy_and_security.md`](55_tenancy_and_security.md) | Tenant scope + file validation |
| 10 | [`docs/future_resume_upload_2.0/60_storage_and_queue.md`](60_storage_and_queue.md) | R2 + queue config |
| 11 | [`docs/future_resume_upload_2.0/70_jobs_and_events.md`](70_jobs_and_events.md) | Reverb payloads (status only — no parser events) |
| 12 | [`docs/future_resume_upload_2.0/75_capacity_plan.md`](75_capacity_plan.md) | Pod/queue/Redis numbers |
| 13 | [`docs/future_resume_upload_2.0/30_architecture.md`](30_architecture.md) | 6-tier architecture |
| 14 | [`docs/future_resume_upload_2.0/30_workflow_diagram.md`](30_workflow_diagram.md) | v1 tree adapted to v2 contracts |
| 15 | [`docs/future_resume_upload_2.0/97_faq_enterprise.md`](97_faq_enterprise.md) | Production-tone reviewer answers |

**Other docs in tree (reference only, do not implement from):**
- `docs/future_resume_upload_2.0/95_god_mode_spec.md` — v2.5/v3 deferred work
- `docs/future_resume_upload_2.0/98_failure_matrix_never.md` — wall-poster recovery
- `docs/future_resume_upload_2.0/80_observability.md` — metrics + health
- `docs/future_resume_upload_2.0/85_scalability_redteam.md` — red-team notes
- `docs/future_resume_upload_2.0/00_overview.md` — pitch
- `docs/future_resume_upload_2.0/10_current_state.md` — current pipeline
- `docs/future_resume_upload_2.0/20_goals_non_goals.md` — metrics
- `docs/future_resume_upload_2.0/30_v1_brief.md` — historical v1
- `docs/future_resume_upload_2.0/65_browser_sizing.md` — browser-side concerns
- `docs/future_resume_upload_2.0/README.md` — index

---

## THE PROMPT (paste into a fresh AI session)

```
You are implementing **Resume Upload v2** for JERO, a Laravel 11
multi-tenant B2B hiring SaaS. Read CLAUDE.md first; it has 13 hard rules
that prevent breakage (do not skip). Then read the v2 design tree in
the order listed below.

==============================================================
PROJECT RULES (from CLAUDE.md — non-negotiable)
==============================================================

1. **All work happens in `JWMaincode28June2026` ONLY.** Never touch
   any other folder (e.g. `hire.jero.works`).
2. **Never read 500 KB+ controllers whole.** `HomeController` is
   ~813 KB, several `*Controller` are 500 KB+. Use Grep for the
   method, then Read a window.
3. **Database truth = `database/schema/mysql-schema.sql`** (217
   tables). Schema is NOT in migrations — never invent migrations;
   confirm columns there or via `php artisan db:table <t>`.
4. **Tenancy:** Eloquent scoped by `company_id`. Raw `DB::table()`
   is NOT — filter it yourself. Eloquent ALL queries go through
   `withGlobalScope` or explicit `->where('company_id', $cid)`.
   NO `Resume::all()`.
5. **Keep MySQL ↔ Elasticsearch in sync.** Writes to indexed
   entities must re-index (Observer/Job).
6. **Use `config()`, never `env()` outside `config/`.** `env()`
   returns null under `config:cache`/Octane (active bug BUG-02).
7. **Queue heavy work** (resume parse/upload, mail, bulk, ES reindex).
8. **Preserve baked-in typos** (`condidate`, `commericial`,
   `Caompany`, `transection`) — they map to real tables/indices.
9. **No business-logic changes** (ranking/matching, billing,
   status pipelines) without explicit sign-off. The upload
   subsystem must NOT touch `coins`, `matching_score`, or
   `application_status` workflows.
10. **Match existing patterns** — no repository layer. Fat
    controllers + services are the convention. Don't add layers
    unprompted.
11. **UI:** follow Metronic, **Poppins font**, **max font 14px**,
    **clean icon-based design (NO 3D)**, must fit 1366×768 → 4K.
    Read `docs/UI_UX_STANDARDS.md` before any UI.
12. **Build features as vertical slices** (Route + FormRequest +
    Action + ES sync + Observer + Reverb event + Blade component +
    **test**), extracted out of god controllers.
13. **Measure & verify** before claiming done; mark uncertainty
    explicitly. Run `bash scripts/ai-guards.sh` before committing.

Known landmines (do not get surprised):
- 63 active `dd()` calls + debug routes in code.
- `JobApplication` tenant scope is commented out → cross-tenant
  leak risk. v2 does NOT use `JobApplication` directly.
- 1,227 unescaped `{!! !!}` Blade outputs → XSS surface.
- Secrets (incl. plaintext ES password) live in `.env` — handle
  with care.
- Dead code: `OLD_*`/`old_*`/`OLD_2_*` controllers, `oldBackUp/`,
  stray root controller — ignore.

==============================================================
DOCS TO READ (in this order)
==============================================================

1. `docs/future_resume_upload_2.0/RESUME_UPLOAD_V2_COMPLETE.md`
   — executive view, all sections.
2. `docs/future_resume_upload_2.0/96_invariants.md` — 4 invariants
   (INV-1..4) + tests. (INV-5 removed 2026-07-27.)
3. `docs/future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md`
   — 20-step plan, contains the canonical code samples (parser-
   half steps updated to browser-only).
4. `docs/future_resume_upload_2.0/40_data_model.md` — schema,
   indexes, status enum (no parser_* columns, no
   resume_parse_results table).
5. `docs/future_resume_upload_2.0/50_api_contracts.md` — endpoint
   shapes, headers, error matrix.
6. `docs/future_resume_upload_2.0/99_decision_log.md` — ADRs
   001..004, 006..014, 016..018. ADR-005 + ADR-015 REMOVED.

==============================================================
THE 5 INVARIANTS — never violate
==============================================================

INV-1 — Every user-initiated file pick creates exactly one new
`upload_session_uuid` and exactly one new `resume_uploads` row.
Two different file picks always produce two different sessions,
even if the same bytes are picked twice.

INV-2 — Parser may populate empty fields only. Recruiter's typed
value wins. Test: `test_manual_values_always_win_over_parser`.

INV-3 — Submit may retry; server verifies state BEFORE any insert.
Cross-request race: conditional UPDATE inside a transaction; loser
returns `200 session_replay_winner_found` (NOT `duplicate_merged`).

INV-4 — `Idempotency-Key` HTTP header is a **per-request random
UUID from `crypto.randomUUID()`**, NOT a content hash. NO UNIQUE
constraint on `(company_id, idempotency_key)`. INDEX only.

INV-5 — REMOVED (2026-07-27). v2 has no server-side parser; the
parser is browser-side only (`pdf.js` + `re_test()` from the local
`File` object, parallel with the R2 PUT). The invariants collapse
to INV-1..4.

==============================================================
THE CARDINAL RULE — Step 20 (NEVER merge)
==============================================================

> The **Duplicate Ownership Engine** DETECTS and REPORTS only. It
> NEVER merges, deletes, or modifies candidate rows. Candidate
> count in the ATS = submit count. Every successful submit
> creates exactly one new candidate that exists forever.

Acceptance scenarios (cardinal rule across all):
- Same recruiter picks same file twice → 2 candidates, both flagged
- Recruiter A and B (same tenant) upload same file → 2 candidates
- Recruiter A and B (different tenants) upload same file → 2
  candidates, one per tenant; cross-tenant merging is FORBIDDEN
- Recruiter re-uploads last week's file → 1 new candidate; old
  candidate NOT modified
- Recruiter uploads same file 3x in a minute → 3 candidates

The engine has 4 guard tests that MUST pass; PR is closed if any
fails:
- `test_engine_does_not_modify_uploaded_rows`
- `test_engine_does_not_delete_candidates`
- `test_engine_does_not_touch_resume_uploads`
- `test_engine_read_only_under_load`

==============================================================
ARCHITECTURE — direct browser→R2, NOT through PHP
==============================================================

Bytes NEVER traverse PHP. Browser PUTs directly to Cloudflare R2
via a presigned URL signed by PHP. PHP sees only metadata.

The flow:
1. Browser POSTs `/service_company/api/resume-sessions` with
   `Idempotency-Key: <crypto.randomUUID()>` header.
2. Server creates a row in `resume_uploads` (status=Created),
   returns presigned PUT URL + `r2_temp_key` + session UUID.
3. Browser PUTs file bytes to R2 directly.
4. Browser POSTs `/service_company/api/resume-sessions/{uuid}/finalize`.
5. Server HEADs R2, conditional UPDATE status→Uploaded, broadcasts
   Reverb `ResumeUploaded` (status only — no payload).
6. **(No `ParseResumeJob`. The browser-side `pdf.js` + `re_test()`
   has already populated the form fields during step 3's R2 PUT.)**
7. Recruiter clicks Submit → conditional UPDATE status→Completed +
   `linked_candidate_id` set, insert `documents` row, insert
   `add_job_wise_condidates` row.

==============================================================
IDEMPOTENCY — HTTP header, NOT a content hash
==============================================================

The `Idempotency-Key` is a **per-request random UUID** generated by
JS via `crypto.randomUUID()` at the moment of presign-request
construction. It travels as the HTTP `Idempotency-Key` request
header (NOT the body). The server stores it on
`resume_uploads.idempotency_key` with an INDEX, NOT a UNIQUE.

Same header on transport retry → server replays the previous
session.
Different header on a new POST → new session, ALWAYS.

DO NOT hash the file bytes for idempotency. Two recruiters picking
the same file must ALWAYS get two different sessions (INV-1).

==============================================================
THE 20-STEP PLAN (Phase A → H)
==============================================================

Phase A — Additive infra (Steps 1-3)
  Schema migrations, models, audit table, cleanup job.
Phase B — Idempotency + presign (Steps 4-6)
  `idempotency_key` column, `POST /resume-sessions`, rate limits.
Phase C — Browser flow (Steps 7-9)
  `resume-presign.js`, R2 PUT, `localStorage` cache, refresh
  recovery.
Phase D — Cross-tab + browser parser (Steps 10-14)
  `BroadcastChannel`, browser-side `pdf.js` + `re_test()` wiring,
  Reverb auth. (No `ParseResumeJob`, no `parse_resumes` queue.)
Phase E — Submit + extraction (Steps 15-16)
  `finalize` + submit merge, god-controller extraction.
Phase F — Edit page (Step 19)
  Edit flow vertical slice with `Branch A`/`B`/`C` for
  replace/keep/remove.
Phase G — Verify + load (Steps 17-18)
  Load test (10K concurrent), curl examples, rollback runbook.
Phase H — Duplicates (Step 20)
  Duplicate Ownership Engine contract — DETECTS and REPORTS,
  never MERGES.

==============================================================
WHAT TO BUILD FIRST
==============================================================

Start with **Step 1** (schema migrations) and proceed in numerical
order. Each step has:
- A clear list of files to create or modify
- Sample code (use it as-is unless a stronger pattern is required)
- Test scenarios
- A feature flag (default OFF in production)

After each step:
1. Run `bash scripts/ai-guards.sh` (no new `dd()`/`env()`).
2. Run `php artisan test --filter=<relevant tests>`.
3. Commit with a message following the project's recent style.

==============================================================
DEFINITION OF DONE (per step)
==============================================================

A step is DONE when ALL of these hold:
1. Code matches the canonical contract (5 invariants).
2. Schema is additive — no destructive migrations.
3. Tests written for the change (≥1 invariant test if touching
   INV-1..5).
4. `bash scripts/ai-guards.sh` clean.
5. Reverb auth callback still does `(int)` cast on `company_id`.
6. `Idempotency-Key` is required and is per-request random UUID.
7. Engine read-only contract preserved (Step 20).
8. `audit_logs` row written for state transitions.
9. Feature flag defaults to OFF in production until smoke-tested.
10. Rollback runbook entry exists (per-flag + nuclear).

==============================================================
WHAT NOT TO DO
==============================================================

- DO NOT add a UNIQUE constraint on `(company_id, idempotency_key)`.
- DO NOT hash file bytes for idempotency.
- DO NOT merge candidates.
- DO NOT delete candidate rows.
- DO NOT touch `coins`, `matching_score`, `application_status`.
- DO NOT modify `JobApplication` directly (its tenant scope is
  commented out — v2 uses the new `ResumeUpload` model only).
- DO NOT use raw `env()` outside `config/*.php`.
- DO NOT skip `bash scripts/ai-guards.sh` before commit.
- DO NOT add a Repository layer (no repos in this codebase).
- DO NOT add a separate `resume_upload_state_transitions` table.
- DO NOT move presign to a Cloudflare Worker (that's v3; ADR-017).
- DO NOT add a server-side parser. v2 has only the browser-side one.
  No `ParseResumeJob`, no `ResumeParserService`, no
  `resume_parse_results` table, no `parse_resumes` queue, no
  `parser_attempts` column, no `parser_started_at`,
  no `ResumeParsed` / `ResumeParseStarted` / `ResumeParseProgress` /
  `ResumeParseFailed` Reverb events, no `Retry-After` honor, no
  `RepairStuckSessionsJob` parser-half, no `parser_fallback_mode`,
  no `extractor_version`.

==============================================================
WHEN YOU HAVE A QUESTION
==============================================================

1. Check `RESUME_UPLOAD_V2_COMPLETE.md` §16 (non-negotiable recap).
2. Check the relevant section in `00_STEPS_IMPLEMENTATION_PLAN.md`.
3. Check the ADR in `99_decision_log.md`.
4. If still unclear, ASK the user before guessing. Do not invent.

==============================================================
FINAL WORD
==============================================================

The v2 design tree is at 10/10 build-readiness. The contract is
locked. Your job is to translate it to code that respects every
rule above. Build it as vertical slices. Test each slice. Ship
behind a feature flag. Roll back without panic if smoke fails.

Good luck. Don't merge candidates.
```

---

## How to use this

1. Open a fresh AI session.
2. Paste the prompt above as your first message.
3. The AI reads CLAUDE.md + the v2 tree, then implements step-by-step.
4. Review each step's PR against the 10-point Definition of Done.

## Variants

### Variant A — "Build everything end-to-end"
Use the prompt above as-is. The AI will attempt all 20 steps in order.
**Realistic timeline:** multiple sessions, several days of focused work.
**Risk:** if Step 0 (pre-flight) is wrong, all 20 are wrong. Verify Step 0
before continuing.

### Variant B — "Build only Phase A (Steps 1-3)"
Replace the section `WHAT TO BUILD FIRST` with:

```
Build ONLY Steps 1-3 (Phase A: additive infra). Do NOT touch
presign, idempotency, browser flow, parser, or submit yet.

Deliverables:
1. Migration: `add_resume_v2_indexes` — UNIQUE(uuid, company_id),
   INDEX(company_id, idempotency_key). NO UNIQUE on idempotency_key.
2. Migration: `add_resume_v2_session_columns` — idempotency_key,
   file_size_bytes, mime_type, sha256_hex, expires_at,
   upload_completed_at, last_error, linked_candidate_id,
   linked_application_id. **(No parser_started_at, no
   parser_completed_at, no parser_attempts, no
   parser_last_error — those columns are gone.)**
3. ~~Migration `create_resume_parse_results`~~ — REMOVED. v2 has no
   server-side parser and therefore no canonical extraction row.
4. Migration: `create_upload_session_audits`.
5. Eloquent models: ResumeUpload, UploadSessionAudit.
   ~~ResumeParseResult~~ — REMOVED. ResumeUpload scopes:
   `forCompany($cid)`, `byUuid($uuid)`. NO `ResumeUpload::all()`.
6. Audit record() helper.
7. Tests covering the schema and the scopes.

Stop after Step 3 and report.
```

### Variant C — "Audit-only, no code"
Replace the prompt body with:

```
You are auditing the existing JERO codebase against the Resume
Upload v2 design tree at `docs/future_resume_upload_2.0/`.

Read CLAUDE.md first, then the v2 tree (start with
RESUME_UPLOAD_V2_COMPLETE.md, then 96_invariants.md).

For each of the 5 invariants (INV-1..5), find the relevant existing
code in the codebase and report:
1. Does the existing code satisfy the invariant? Y/N
2. Cite file:line for the evidence.
3. If N, what is the minimum fix to make it satisfy the invariant?

Then for Step 20 (no-merge rule), audit any code that handles
"duplicate candidate" or "merge candidate" paths and report.

Deliverable: a single audit report with file:line citations and a
recommended fix order.
```

---

## When NOT to use this prompt

- **You already have an AI in the project.** Use that AI; it has the live
  memory and the doc tree in context.
- **You only need a docs review.** Use `EnterPlanMode` and ask for a doc
  audit directly.
- **You're implementing a single step.** Use the prompt as a reference for
  that step only; don't paste the whole prompt.
