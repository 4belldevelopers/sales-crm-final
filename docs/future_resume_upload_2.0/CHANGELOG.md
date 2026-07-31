# CHANGELOG — Resume Upload V2 Design

> **Purpose:** Every time a significant design decision is reversed,
> added, or deferred, it is recorded here FIRST. This file is the
> canonical source for "what changed and why" — AI agents, engineers,
> and reviewers should read this before reading any other doc in
> this folder.
>
> Format: newest entry at the top. Each entry links every impacted document.

---

## 2026-07-27 — Server-Side Parser Removed (Major Pivot)

### Decision
The server-side resume parser was removed entirely from V2.
The **sole parser** is now browser-side: `pdf.js` (for PDF files) +
`mammoth.js` (for DOCX files), both feeding into `re_test()` to
extract fields into the form. This runs from the browser's local
`File` object in 50–200 ms — zero network, zero server load,
zero API cost.

### Why
- The browser-side parser was already implemented and working in v1.
- Adding a server parser (OpenAI or any other) introduced a rate-limit
  surface, a queue dependency, a new DB table, 3 new Reverb events,
  per-pod semaphore logic, and ~300 lines of infrastructure for no
  improvement over what `re_test()` already does for recruiter workflows.
- The "manual values always win" rule (INV-2) already means the parser
  is advisory, not authoritative. Making it server-side adds cost and
  complexity with no UX change.

### What Was Removed

| What | Where it was documented |
|---|---|
| `ParseResumeJob` — queue job for server-side parsing | `70_jobs_and_events.md`, `00_STEPS_IMPLEMENTATION_PLAN.md` |
| `parse_resumes` Horizon queue | `60_storage_and_queue.md`, `75_capacity_plan.md` |
| `resume_parse_results` DB table | `40_data_model.md` |
| `QUEUED_FOR_PARSING`, `PARSING`, `PARSED` status enum values | `40_data_model.md` status enum |
| `ResumeParseProgress` Reverb event | `30_architecture.md`, `70_jobs_and_events.md` |
| `ResumeParsed` Reverb event | `30_architecture.md`, `70_jobs_and_events.md` |
| `ResumeParseFailed` Reverb event | `30_architecture.md`, `70_jobs_and_events.md` |
| Redis parser semaphore (per-tenant concurrency cap) | `30_architecture.md` |
| `parser_started_at`, `parser_completed_at`, `parser_attempts`, `last_parser_pod_id` DB columns | `40_data_model.md` |
| `resume:metrics:parsed_total`, `resume:metrics:parse_failed_total`, `resume:metrics:parser_429_total` Redis keys | `40_data_model.md` |
| `ActionParseStarted`, `ActionParseProgress`, `ActionParseCompleted`, `ActionParseFailed` audit actions | `40_data_model.md` |
| `FAIL-7` (parser network failure), `FAIL-8` (parser 429), `FAIL-9` (parser timeout) | `45_failure_modes.md` |
| `INV-5` (parser result belongs to one session) | `96_invariants.md` |
| `ADR-005` (Retry-After on parser 429) | `99_decision_log.md` |
| `ADR-015` (per-tenant parser pod concurrency) | `99_decision_log.md` |
| `test_parser_*` test files / test names | `96_invariants.md` |
| G-4 goal (parser attempt budget per minute) | `20_goals_non_goals.md` |
| NG-1 goal (no polyglot server parser) | `20_goals_non_goals.md` |
| `parse_resumes` pod budget (~7 pods) | `75_capacity_plan.md` — total drops from ~39 to ~32 pods |
| `parser_backlog_count` health endpoint field | `50_api_contracts.md` §EP-6 |
| Step 4c (server parser dispatch from finalize) in rollout | `90_rollout_plan.md` §v2.0.0d |

### What Replaced It

| Replacement | Where documented |
|---|---|
| Browser-side `pdf.js` + `mammoth.js` + `re_test()` (already in Blade views) | `30_architecture.md` §Tier-1, `65_browser_sizing.md` |
| `ResumeUploaded` Reverb event carries status only (no parser payload) | `70_jobs_and_events.md` |
| INV-2 "manual values always win" unchanged | `96_invariants.md` |
| Status flow simplified: `Pending → Uploading → Uploaded → Completed` | `40_data_model.md` |
| `resume-populate-if-empty.js` runs inline during R2 PUT | `30_architecture.md` |

### Impacted Documents (all carry inline `REMOVED 2026-07-27` annotations)

`00_overview.md`, `10_current_state.md`, `20_goals_non_goals.md`,
`30_architecture.md`, `40_data_model.md`, `45_failure_modes.md`,
`50_api_contracts.md`, `60_storage_and_queue.md`, `70_jobs_and_events.md`,
`75_capacity_plan.md`, `85_scalability_redteam.md`, `90_rollout_plan.md`,
`95_god_mode_spec.md`, `96_invariants.md`, `98_failure_matrix_never.md`,
`99_decision_log.md`, `00_STEPS_IMPLEMENTATION_PLAN.md`

### What Was NOT Changed

- `ResumeParserService.php` in the codebase **still contains OpenAI code**.
  This is **dead code** — it is not called by any V2 path. See note below.
- The existing `ParseResumeJob.php` in the codebase is a v1 artifact,
  not part of V2. V2 does not dispatch it.

> **⚠️ For AI agents and engineers reading the codebase:**
> `app/Services/ResumeParserService.php` contains an OpenAI `parseWithOpenAI()`
> method and an API key constructor. **This code is NOT used by V2.**
> V2 has no server-side parser. Do not add calls to this service in any
> V2 controller, job, or queue. The browser-side `pdf.js` + `re_test()`
> is the only parser. If you are implementing V2, treat `ResumeParserService`
> as a dormant v1 artifact.

---

## 2026-07-26 — V2 Design Initiated

### Decision
Resume Upload V2 design started. All docs in this folder were created
to address the gaps identified in the v1 product brief (`30_v1_brief.md`).

### Key initial decisions recorded as ADRs

| ADR | Decision |
|---|---|
| ADR-001 | Browser direct-to-R2 presigned PUT; PHP sees zero bytes |
| ADR-002 | Idempotency key = random UUID per request, NOT content hash |
| ADR-003 | Reverb auth from session, NOT URL or localStorage |
| ADR-004 | State machine via conditional UPDATE, not transitions table |
| ADR-006 | Legacy `POST /uploadresume` kept as fallback for 6 months |
| ADR-007 | No `chmod 0777` on `bootstrap/cache` |
| ADR-008 | Health endpoint ships at v2.0.0a unconditionally |
| ADR-009 | INDEX (not UNIQUE) on idempotency_key, INSERT-then-SELECT |
| ADR-010 | Hard 6 MB file cap |
| ADR-011 | Public career-apply keeps HTTP polling, NOT Reverb |
| ADR-012 | Multipart R2 PUT for ≥5 MB, single PUT for <5 MB |
| ADR-013 | Explicit `test_manual_values_always_win_over_parser` test |
| ADR-014 | Vertical slice extraction from god controllers |
| ADR-016 | `documents()` de-dup on compound `(name, company_id, candidate_id)` |
| ADR-017 | Presign stays on Octane in V2; Cloudflare Worker deferred to V3 |
| ADR-018 | `BroadcastChannel` for same-browser tab sync (V2.5) |

---

## How to add a new entry

```
## YYYY-MM-DD — Short title of the decision/change

### Decision
One paragraph.

### Why
Bullet reasoning.

### What Was Removed / What Was Added
Table of impacted items + which doc they live in.

### Impacted Documents
Comma-separated list.
```
