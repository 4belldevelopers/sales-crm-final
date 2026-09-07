# Architecture Decision Records (ADR)

These ADRs are **reconstructed retroactively** from the codebase by an AI agent — they
document decisions *as observed in the code*, not from original design meetings.
Rationale/alternatives are **[INFERRED]** unless confirmed by a human. Use them to
understand *why the code looks the way it does* before changing it.

| ADR | Decision | Status |
|---|---|---|
| [0001](0001-elasticsearch-as-read-store.md) | Elasticsearch as denormalized read store | Active (observed) |
| [0002](0002-redis-horizon-queues.md) | Redis + Horizon for queues/cache/sessions | Active (observed) |
| [0003](0003-octane-frankenphp.md) | Octane + FrankenPHP runtime | Active (observed) |
| [0004](0004-multi-persona-monolith.md) | Single monolith, per-persona namespaces | Active (observed) |
| [0005](0005-schema-in-live-db.md) | Schema managed in live DB, not migrations | Active (observed) ⚠️ |
| [0006](0006-multi-gateway-payments.md) | Multiple payment gateways | Active (observed) |
| [0007](0007-blocking-scenarios-architecture.md) | Blocking flows: session-epoch + Reverb kick + `SyncUserStatusAction` | Active (proposed; code not yet landed) |

To propose a *new* decision, add `NNNN-title.md` and update this table.
