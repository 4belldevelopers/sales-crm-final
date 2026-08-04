# 00 — Overview

> *Intent: state the pitch in one paragraph so a reviewer can decide in 30
> seconds whether the rest of the document tree is worth their time.*

---

## ⚠️ Read This First — Critical Facts for AI Agents and New Engineers

> **1 — V2 does NOT use OpenAI or any external AI/LLM for parsing.**
>
> The sole resume parser in V2 is **browser-side**: `pdf.js` (PDF files)
> + `mammoth.js` (DOCX files), both feeding into `re_test()` to populate
> form fields. This runs from the browser's local `File` object in
> 50–200 ms — zero network, zero server cost, zero API dependency.
>
> `app/Services/ResumeParserService.php` in the codebase **still contains
> an OpenAI method** (`parseWithOpenAI()`). This is **dead code** — a v1
> design artifact. **V2 does not call it. Do not add calls to it.**
> See [`CHANGELOG.md §2026-07-27`](CHANGELOG.md) for the full pivot record.

> **2 — The server-side parser was removed mid-design (2026-07-27).**
>
> Every doc in this folder contains `REMOVED 2026-07-27` annotations.
> Before reading individual docs, read [`CHANGELOG.md`](CHANGELOG.md)
> for a single consolidated record of everything that was removed and why.

> **3 — Where to start implementation.**
>
> [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md)
> is the **single actionable build plan** — 20 steps, 7 phases, each with
> feature flags, code samples, and staging tests. Start there, not here.
> See the [Step-to-Rollout mapping](#step-to-rollout-mapping) below for
> how the 20 implementation steps map to the 14 rollout versions in
> [`90_rollout_plan.md`](90_rollout_plan.md).

---

## The Pitch

**V2 makes the resume upload pipeline survivable at 10,000 concurrent users.**

It does this by moving the heavy bytes out of PHP (direct browser-to-R2
presigned PUT), queueing every stateful transition behind a row-level DB
state machine (the same correctness primitive from Phase 5 of v1), and
putting observability on every transition so 10K × 1 failure produces
one structured log line per pod, not an avalanche.

The user-facing UX is unchanged from the v1 product brief:
*manual values always win*, no double-upload, parallel upload + parse, full
offline / refresh / cancel recovery. The 10K-survivability comes from
infrastructure choices, not UX changes.

---

## Step-to-Rollout Mapping

> This table maps the 20 implementation steps in
> [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md)
> to the 14 release versions in [`90_rollout_plan.md`](90_rollout_plan.md).
> The implementation plan is the **how-to-build** guide;
> the rollout plan is the **how-to-ship** guide. They are the same work,
> two different lenses.

| Rollout Version | Implementation Steps | Phase | What Ships |
|---|---|---|---|
| `v2.0.0a` | Steps 1–3 | Phase A — Foundation | `config/resumes.php`, DB migrations, enum + models, health endpoint shell |
| `v2.0.0b` | Steps 4–5 | Phase B (part 1) | `ResumeUploadSession` service, `POST /resume-sessions` presign endpoint |
| `v2.0.0c` | Step 6 | Phase B (part 2) | Browser JS presign + direct R2 PUT (`resume-presign.js`, `resume-upload-progress.js`) |
| `v2.0.0d` | ~~parser wiring~~ → Step 6 | Phase B (part 2) | Browser-side `pdf.js` + `re_test()` wiring — **no server parser dispatch** |
| `v2.0.0e` | Step 7 | Phase C (part 1) | Submit with `session_uuid` — 2-RTT race-safe flow |
| `v2.0.0f` | Step 16 (partial) | Phase E (partial) | Extract `careerStore` from `HomeController` |
| `v2.0.0g` | Step 7 (partial) | Phase C (part 1) | Extract `store` from `ServiceCompanyJobApplicationController` |
| `v2.0.0h` | Step 14 | Phase D — Hardening | `BroadcastChannel` cross-tab sync (`resume-broadcast-sync.js`) |
| `v2.0.0i` | Steps 8–9 | Phase C (part 2) | Cancel endpoint + Refresh recovery + GET session endpoint |
| `v2.0.0j` | Steps 10–11 | Phase C (part 3) | Multipart upload (≥5 MB) + Network resilience (offline/online handling) |
| `v2.0.0k` | Phase C extensions | Phase C | Bulk upload primitives (composes presign + finalize + queue) |
| `v2.0.0l` | Steps 12–13, 15, 17 | Phase D + E (part) | Cron jobs, rate limiting, metrics + health endpoint, load test |
| `v2.0.0m` | Step 16 | Phase E | Full `CareerApplyController` vertical slice |
| `v2.0.0n` | Step 18 | Phase E — Ship | Production GA; legacy `POST /uploadresume` rate-limited, deprecated |
| *(post-GA)* | Step 19 | Phase F — Edit page | Edit page vertical slice (replace / remove resume) |
| *(post-GA)* | Step 20 | Phase G | Duplicate Ownership Engine integration (detect + report, never merge) |

> **Note on v2.0.0d:** The original rollout plan was written before the 2026-07-27 pivot.
> What was "parser wiring" in v2.0.0d is now simply the browser-side `pdf.js` + `re_test()`
> wiring already in the Blade views. No server dispatch, no queue, no new Reverb events.
> See [`CHANGELOG.md`](CHANGELOG.md) for the full record.

---

## What We Are NOT Redesigning

| Out of scope (v3 candidates) | Reason |
|---|---|
| Bulk upload | Same upload primitive, separate Blade; build v2 first |
| Classical ranking/rerank | Out of resume upload scope |
| AI search / candidates search | Different pipeline, downstream of submit |
| Multi-region active/active | V2 ships single-region with read replicas for ES |
| Marketplace talent ingestion | Different intake surface (staffing bench) |
| OpenAI / LLM parsing | Removed 2026-07-27 — browser-side pdf.js is sufficient |

---

## Definition of Done for This Design

1. Every commit lands with the live-test pattern from
   [`90_rollout_plan.md`](90_rollout_plan.md): small step, push, browser
   smoke test, log tail 15 min, then `bash scripts/ai-guards.sh` + tests.
2. Each step has a corresponding new test file in `tests/Feature/`
   under a `ResumeV2*` prefix — clear marker vs v1's `Resume*` tests.
3. The state machine in [`40_data_model.md`](40_data_model.md) has a
   transition test covering every legal arrow.
4. Health endpoint from [`80_observability.md`](80_observability.md) is
   registered behind `internal_token` middleware (does NOT trust client IPs).
5. Capacity numbers in [`75_capacity_plan.md`](75_capacity_plan.md) are
   signed off by infrastructure before prod rollout (CLAUDE.md rule 8).
