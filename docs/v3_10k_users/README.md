# `docs/v3_10k_users/` — Scale to 10K Concurrent Users

> **Purpose:** Complete enterprise-grade documentation for scaling the JERO platform to
> 10,000 concurrent users, 1,000,000 daily candidate uploads, and 1,000 daily job updates
> — without failure.
>
> Based on: real codebase analysis, exact RAM math for the 3-server setup,
> and the V2 resume upload architecture already planned.
>
> **Every number in this folder is derived from first principles. No estimates.**

---

## ⚠️ Read First

> The bottleneck is **not hardware**. It is the `HomeController` (16,633 LOC) and
> `ServiceCompanyJobApplicationController` boot-time loading in Octane workers.
> **Every recommendation in this folder is noise until god controllers are extracted.**
> See [`20_god_controller_problem.md`](20_god_controller_problem.md).

---

## Read Order

| # | File | Purpose |
|---|---|---|
| **0** | [`README.md`](README.md) | This index |
| **1** | [`00_capacity_math.md`](00_capacity_math.md) | Exact numbers — what 10K / 1M / 1K actually means per second |
| **2** | [`10_hardware_verdict.md`](10_hardware_verdict.md) | 3-server RAM analysis: can it handle the load? |
| **3** | [`20_god_controller_problem.md`](20_god_controller_problem.md) | #1 bottleneck — why Octane crashes before hardware does |
| **4** | [`30_architecture_layers.md`](30_architecture_layers.md) | 8-layer GOD architecture: CDN → Octane → MySQL → Redis → ES → Horizon → Reverb → Contract |
| **5** | [`40_server_configuration.md`](40_server_configuration.md) | Exact config values for every server |
| **6** | [`50_action_plan.md`](50_action_plan.md) | Prioritized implementation roadmap with effort estimates |
| **7** | [`60_slos_and_monitoring.md`](60_slos_and_monitoring.md) | SLOs, health endpoints, failure budgets, alert thresholds |

---

## The 3-Server Setup Summary

```
Server A: 32 GB RAM — Laravel Octane/FrankenPHP + Horizon + Reverb
Server B: 32 GB RAM — MySQL 8 primary
Server C: 32 GB RAM — Elasticsearch single node

Missing (must add):
Server D:  8 GB RAM — Redis (separate, non-negotiable)
```

---

## One-Line Verdict Per Target

| Target | Verdict | Condition |
|---|---|---|
| 1,000,000 uploads/day | ✅ Handles it today | Average PHP concurrency is only ~66 requests at once |
| 10,000 concurrent users | ❌ Crashes today / ✅ Works after fixes | Must extract god controllers + move Redis off app server |
| 1,000 job updates/day | ✅ Trivial | 0.01 writes/second — not measurable |

---

## Cross-references to V2 Docs

These V3 scale docs build directly on the V2 design:

| V3 Topic | V2 Source |
|---|---|
| Browser→R2 direct PUT (zero PHP bytes) | [`../future_resume_upload_2.0/30_architecture.md`](../future_resume_upload_2.0/30_architecture.md) |
| Capacity pod math | [`../future_resume_upload_2.0/75_capacity_plan.md`](../future_resume_upload_2.0/75_capacity_plan.md) |
| God controller extraction | [`../future_resume_upload_2.0/99_decision_log.md`](../future_resume_upload_2.0/99_decision_log.md) ADR-014 |
| BroadcastChannel tab sync | [`../future_resume_upload_2.0/95_god_mode_spec.md`](../future_resume_upload_2.0/95_god_mode_spec.md) |
| Failure modes + recovery | [`../future_resume_upload_2.0/45_failure_modes.md`](../future_resume_upload_2.0/45_failure_modes.md) |
| Tier ladder V2→V3 | [`../future_resume_upload_2.0/95_god_mode_spec.md`](../future_resume_upload_2.0/95_god_mode_spec.md) |
