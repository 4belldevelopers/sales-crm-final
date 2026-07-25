# `docs/future_resume_upload_2.0/` — Resume Upload v2 design

This tree is the v2 design for the resume upload & parsing subsystem. Goal:
**handle 10,000 concurrent users uploading resumes without failure**, while
keeping the manual-edit alway-wins UX rule from the v1 product brief.

Every doc here is **a spec, not a contract claim** — re-derive any item against
the listed source paths before relying on it (CLAUDE.md rule 1 + rule 12).
The state of the working tree as the design was written is captured in
[`10_current_state.md`](10_current_state.md).

## Read order

| # | File | Purpose |
|---|---|---|
| 0 | [`README.md`](README.md) | this index |
| 1 | [`00_overview.md`](00_overview.md) | the one-paragraph pitch |
| 2 | [`10_current_state.md`](10_current_state.md) | today's pipeline + gaps for 10K |
| 3 | [`20_goals_non_goals.md`](20_goals_non_goals.md) | measurable success + out of scope |
| 4 | [`30_architecture.md`](30_architecture.md) | the 6-tier architecture |
| 5 | [`30_v1_brief.md`](30_v1_brief.md) | verbatim copy of the user-supplied v1 ASCII tree + rating |
| 6 | [`30_workflow_diagram.md`](30_workflow_diagram.md) | the v1 tree adapted into v2 contracts |
| 7 | [`40_data_model.md`](40_data_model.md) | ResumeUpload / ParseResult / Audit / enum / counters |
| 8 | [`45_failure_modes.md`](45_failure_modes.md) | every way 10K break + our response |
| 9 | [`50_api_contracts.md`](50_api_contracts.md) | routes, JSON, error matrix |
| 10 | [`55_tenancy_and_security.md`](55_tenancy_and_security.md) | tenant scope, file validation, rate limits |
| 11 | [`60_storage_and_queue.md`](60_storage_and_queue.md) | R2 → S3, idempotency, Retry-After, queue sizing |
| 12 | [`65_browser_sizing.md`](65_browser_sizing.md) | what the BROWSER does for 10K |
| 13 | [`70_jobs_and_events.md`](70_jobs_and_events.md) | ParseResumeJob, Reverb payload, channels |
| 14 | [`75_capacity_plan.md`](75_capacity_plan.md) | actual pod / queue / redis / R2 numbers |
| 15 | [`80_observability.md`](80_observability.md) | metrics, health endpoint, log context |
| 16 | [`85_scalability_redteam.md`](85_scalability_redteam.md) | what happens when each component fails |
| 17 | [`90_rollout_plan.md`](90_rollout_plan.md) | 14-step small-shipped-live-tested rollout |
| 18 | [`95_god_mode_spec.md`](95_god_mode_spec.md) | tier ladder — v2.5 / v3 — minimal PHP |
| 19 | [`96_invariants.md`](96_invariants.md) | 5 kept-true invariants + tests |
| 20 | [`97_faq_enterprise.md`](97_faq_enterprise.md) | the 3 FAQ reviewer questions, expanded |
| 21 | [`98_failure_matrix_never.md`](98_failure_matrix_never.md) | wall-poster recovery + never matrix |
| 22 | [`99_decision_log.md`](99_decision_log.md) | ADRs (ADR-017 Worker presign, ADR-018 BroadcastChannel) |

## Constants v2 commits to

These are **design targets**, not measured behavior. Re-confirm against
test fixtures before relying on them.

| Constant | Value | Source / rationale |
|---|---|---|
| Concurrent target | **10,000 users** | SCALABILITY.md goal |
| Files per second at peak | ~1,200 | 10K / ~8 s median upload |
| Cap per resume | **6 MB** (PDF/DOC/DOCX) | matches today's `max:6048` |
| Direct-to-R2 PUT | yes — PHP only orchestrates | 80% bandwidth savings |
| Parser pods | ≥4 with bounded concurrency | AI service on a single failure domain in v2 |
| Parser timeout | 30 s | covers 99% of p95 |
| Parser retry | **once** with `Retry-After` | memory map §"OpenAI" |
| Session TTL | 30 min after creation | R2 lifecycle |
| Live progress WS | Reverb, same-tenant private channel | Pusher/100-conn/limit ok at 1:4 fan-out |
| Idempotency key | `sha256(file_bytes + company_id + safe_filename)` | file hash dedupes S3 objects |
| Rate limit — create session | 30/min/user, **120/min/company** | fairness across tenants |
| Rate limit — submit candidate | 20/min/user | user-level abuse guard |

## 4-step spine

If you only have time for 4 documents:

1. [`00_overview.md`](00_overview.md) — pitch
2. [`30_architecture.md`](30_architecture.md) — design
3. [`45_failure_modes.md`](45_failure_modes.md) — why v2
4. [`90_rollout_plan.md`](90_rollout_plan.md) — how we ship
