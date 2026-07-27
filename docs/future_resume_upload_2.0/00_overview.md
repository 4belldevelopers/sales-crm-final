# 00 — Overview

> *Intent: state the pitch in one paragraph so a reviewer can decide in 30*
> *seconds whether the rest of the document tree is worth their time.*

**v2 makes the resume upload pipeline survivable at 10,000 concurrent users.**

It does this by moving the heavy bytes out of PHP (direct browser-to-R2
presigned PUT), queueing every stateful transition behind a row-level DB
state machine (the same correctness primitive from Phase 5 of v1), capping
parser fan-out behind a Redis semaphore, and putting observability on
every transition so 10K × 1 failure produces one structured log line per
pod, not an avalanche.

The user-facing UX is unchanged from the v1 product brief:
*manual values always win*, no double-upload, parallel upload + parse, full
offline / refresh / cancel recovery. The 10K-survivability comes from
infrastructure choices, not UX changes.

## What we are NOT redesigning

| Out of scope (v3 candidates) | Reason |
|---|---|
| Bulk upload | Same upload primitive, separate Blade; build v2 first |
| Classical ranking/rerank | Out of resume upload scope |
| AI search / candidates search | Different pipeline, downstream of submit |
| Multi-region active/active | v2 ships single-region with read replicas for ES |
| Marketplace talent ingestion | Different intake surface (staffing bench) |

## Definition of done for this design

1. Every commit lands with the live-test pattern from
   [`90_rollout_plan.md`](90_rollout_plan.md): small step, push, browser
   smoke test, log tail 15 min, then `bash scripts/ai-guards.sh` + tests.
2. Each step has a corresponding new test file in `tests/Feature/`
   under a `ResumeV2*` prefix — clear marker vs v1's `Resume*` tests.
3. The state machine in [`40_data_model.md`](40_data_model.md) has a
   transition test covering every legal arrow (8 transitions × 2 paths
   each = 16 cases minimum).
4. Health endpoint from [`80_observability.md`](80_observability.md) is
   registered behind `internal_token` middleware (does NOT trust client
   IPs).
5. Capacity numbers in [`75_capacity_plan.md`](75_capacity_plan.md) are
   signed off by infrastructure before prod rollout (CLAUDE.md rule 8).
