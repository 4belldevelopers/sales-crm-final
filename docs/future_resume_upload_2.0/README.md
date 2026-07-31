# `docs/future_resume_upload_2.0/` — Resume Upload V2 Design

This tree is the v2 design for the resume upload & parsing subsystem. Goal:
**handle 10,000 concurrent users uploading resumes without failure**, while
keeping the manual-edit always-wins UX rule from the v1 product brief.

Every doc here is **a spec, not a contract claim** — re-derive any item against
the listed source paths before relying on it (CLAUDE.md rule 1 + rule 12).
The state of the working tree as the design was written is captured in
[`10_current_state.md`](10_current_state.md).

---

## ⚠️ Critical Facts — Read Before Any Other File

> **V2 does NOT use OpenAI or any external AI/LLM.**
>
> The sole parser is browser-side: `pdf.js` + `mammoth.js` + `re_test()`.
> `app/Services/ResumeParserService.php` in the codebase **still contains
> an OpenAI method — this is dead v1 code.** Do not call it from V2.

> **The server-side parser was removed mid-design on 2026-07-27.**
> All 27 docs carry inline `REMOVED 2026-07-27` annotations.
> Read [`CHANGELOG.md`](CHANGELOG.md) **first** for the single consolidated
> record of everything removed, added, and why.

> **Where to start implementation:**
> [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md)
> is the `[START HERE]` doc — 20 steps, 7 phases, each with feature flags,
> ready-to-paste code, and staging tests. It maps to the 14 rollout
> versions in [`90_rollout_plan.md`](90_rollout_plan.md) — see the
> mapping table in [`00_overview.md`](00_overview.md).

---

## Read Order

| # | File | Purpose |
|---|---|---|
| ★ | [`CHANGELOG.md`](CHANGELOG.md) | **Read first —** all design pivots consolidated (esp. 2026-07-27 server parser removal) |
| ★ | [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md) | **[START HERE for implementation]** 20 steps, 7 phases, full code + tests |
| 0 | [`README.md`](README.md) | this index |
| 1 | [`00_overview.md`](00_overview.md) | pitch + OpenAI warning + Step-to-Rollout mapping table |
| 2 | [`10_current_state.md`](10_current_state.md) | today's pipeline + gaps for 10K |
| 3 | [`20_goals_non_goals.md`](20_goals_non_goals.md) | measurable success + out of scope |
| 4 | [`30_architecture.md`](30_architecture.md) | the 6-tier architecture |
| 5 | [`30_v1_brief.md`](30_v1_brief.md) | verbatim copy of the user-supplied v1 ASCII tree + rating |
| 6 | [`30_workflow_diagram.md`](30_workflow_diagram.md) | the v1 tree adapted into v2 contracts |
| 7 | [`40_data_model.md`](40_data_model.md) | ResumeUpload / Audit / enum / counters (`resume_parse_results` table REMOVED 2026-07-27) |
| 8 | [`45_failure_modes.md`](45_failure_modes.md) | every way 10K breaks + our response (parser FAIL-7/8/9 REMOVED 2026-07-27) |
| 9 | [`50_api_contracts.md`](50_api_contracts.md) | routes, JSON, error matrix |
| 10 | [`55_tenancy_and_security.md`](55_tenancy_and_security.md) | tenant scope, file validation, rate limits |
| 11 | [`60_storage_and_queue.md`](60_storage_and_queue.md) | R2 → S3, idempotency, queue sizing |
| 12 | [`65_browser_sizing.md`](65_browser_sizing.md) | what the BROWSER does for 10K |
| 13 | [`70_jobs_and_events.md`](70_jobs_and_events.md) | Reverb events (status only — `ParseResumeJob` REMOVED 2026-07-27) |
| 14 | [`75_capacity_plan.md`](75_capacity_plan.md) | actual pod / queue / Redis / R2 numbers |
| 15 | [`80_observability.md`](80_observability.md) | metrics, health endpoint, log context |
| 16 | [`85_scalability_redteam.md`](85_scalability_redteam.md) | what happens when each component fails |
| 17 | [`90_rollout_plan.md`](90_rollout_plan.md) | 14 named release versions (`v2.0.0a` → `v2.0.0n`) |
| 18 | [`95_god_mode_spec.md`](95_god_mode_spec.md) | tier ladder — v2 / v2.5 / v3 — minimal PHP |
| 19 | [`96_invariants.md`](96_invariants.md) | 4 kept-true invariants + tests (INV-5 REMOVED 2026-07-27) |
| 20 | [`97_faq_enterprise.md`](97_faq_enterprise.md) | the 3 FAQ reviewer questions, expanded |
| 21 | [`98_failure_matrix_never.md`](98_failure_matrix_never.md) | wall-poster recovery + never matrix |
| 22 | [`99_decision_log.md`](99_decision_log.md) | 18 ADRs (ADR-005 + ADR-015 tombstoned post-2026-07-27) |

## Constants v2 commits to

These are **design targets**, not measured behavior. Re-confirm against
test fixtures before relying on them.

| Constant | Value | Source / rationale |
|---|---|---|
| Concurrent target | **10,000 users** | SCALABILITY.md goal |
| Files per second at peak | ~1,200 | 10K / ~8 s median upload |
| Cap per resume | **6 MB** (PDF/DOC/DOCX) | matches today's `max:6048` |
| Direct-to-R2 PUT | yes — PHP only orchestrates | 80% bandwidth savings |
| ~~Parser pods~~ | REMOVED 2026-07-27 — no server-side parser; no parser pods |
| ~~Parser timeout~~ | REMOVED 2026-07-27 — browser-side pdf.js runs in 50–200 ms locally; no server timeout |
| ~~Parser retry~~ | REMOVED 2026-07-27 — no `Retry-After` to honor |
| Session TTL | 30 min after creation | R2 lifecycle |
| Live progress WS | Reverb, same-tenant private channel | Pusher/100-conn/limit ok at 1:4 fan-out |
| Idempotency key | per-request random UUID via `crypto.randomUUID()` | HTTP `Idempotency-Key` header; replay-lookup only, NOT content dedupe (ADR-002, INV-4, Step 20) |
| Rate limit — create session | 30/min/user, **120/min/company** | fairness across tenants |
| Rate limit — submit candidate | 20/min/user | user-level abuse guard |

## 4-Step Spine

If you only have time for 4 documents:

1. [`CHANGELOG.md`](CHANGELOG.md) — what changed and why (read first)
2. [`00_overview.md`](00_overview.md) — pitch + step-to-rollout mapping
3. [`45_failure_modes.md`](45_failure_modes.md) — why V2 exists
4. [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md) — how to build it
