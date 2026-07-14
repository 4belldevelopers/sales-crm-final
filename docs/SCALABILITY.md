# Scalability Blueprint — 10K Companies, 100M+ Records/Year

> Target: **10,000 tenant companies**, **~100,000,000 new records/year** (candidates,
> applications, events, logs) growing cumulatively. The system must not degrade as data
> grows. This is the architecture to get there on the existing stack. Numbers are
> planning estimates — **load-test to confirm**.

---

## 0. Scale math (size the problem first)
- 100M rows/yr ≈ **~275k/day ≈ ~3.2 rows/sec average**, with peaks 10–50× (business
  hours, bulk imports) → design for **~50–150 writes/sec sustained, 500+ peak**.
- Reads dominate (ATS = browse/search heavy): expect **10–50× reads vs writes**.
- After 3 years: **300M+ rows** in core tables (`job_applications`, events, logs).
  A single unpartitioned MySQL table at that size = slow. Plan partitioning/archival now.
- ES: hundreds of millions of docs across 60+ indices → **shard & lifecycle planning**
  is mandatory, not optional.

---

## 1. Multi-tenancy strategy (the foundational decision)
Current: **shared DB, shared schema, `company_id` discriminator.** This is the right
default for 10K tenants (simplest ops), **but** must be hardened and tiered:

| Tier | Tenants | Strategy |
|---|---|---|
| Standard | the 9,900 | Shared DB + `company_id` (current), with strict scoping + partitioning |
| Large/Enterprise | top ~100 noisy/big tenants | **Dedicated DB/schema** (or own shard) to isolate load & enable custom SLAs |
| Optional | regulated/regional | Region-pinned DB for data residency (GDPR/DPDP) |

**Hard requirements** (today's gaps — see [SECURITY.md](SECURITY.md), [BUG_REPORT.md](BUG_REPORT.md)):
- `company_id` on **every** table, indexed, and **enforced** on every query
  (fix the commented-out scope + raw-query leaks first — isolation bugs are unacceptable
  at marketplace scale).
- A **tenant routing layer** so you can later move a big tenant to its own shard without
  app rewrites (resolve connection by `company_id`).

---

## 2. Database scaling (MySQL)
1. **Read/write split now**: writes → primary, reads → **multiple read replicas**
   (Laravel `config/database.php` read/write). Most ATS traffic is reads.
2. **Connection pooling / proxy** (ProxySQL or RDS Proxy) — Octane + many workers will
   exhaust MySQL `max_connections` otherwise. Critical with remote DB (`76.13.243.68`).
3. **Partition hot tables** by time and/or tenant:
   - `job_applications`, application events, audit logs → **RANGE partition by month/year**
     (or `HASH(company_id)` for tenant spread). Keeps hot partitions small, enables fast
     drop/archive of old data.
4. **Index discipline**: composite indexes on `(company_id, status, …)`,
   covering indexes for top queries; remove non-sargable predicates ([BUG-05](BUG_REPORT.md)).
5. **Archive cold data**: applications older than N months → archive tables / S3 (Parquet)
   / a data warehouse; the live OLTP set stays small and fast.
6. **Horizontal sharding (future)**: shard by `company_id` ranges/hash when a single
   primary can't keep up. The tenant routing layer (§1) makes this incremental.
7. **No cross-shard joins**: keep tenant data co-located; use ES / a warehouse for
   cross-tenant analytics (and the marketplace — see [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md)).

---

## 3. Search/read store scaling (Elasticsearch)
ES is already your primary read store — at this scale it needs real ops:
1. **Index Lifecycle Management (ILM)**: hot → warm → cold → delete; roll over indices
   by size/age (e.g. `ats_candidates-000001`) behind **aliases** (never hardcode physical
   names — also enables zero-downtime reindex/rename, see [NAMING_CONVENTIONS.md](NAMING_CONVENTIONS.md)).
2. **Shard sizing**: target 20–50 GB/shard; right-size primaries + replicas; avoid the
   "too many tiny indices" trap (you have 60+ — consider consolidating low-volume ones
   into shared indices with a `type`/`company_id` field).
3. **Multi-node cluster** with dedicated master/data/coordinating nodes; replicas for HA.
4. **Bulk + async indexing** (`_bulk`), backpressure, and the **reconciliation job**
   ([BUG-07](BUG_REPORT.md)) so ES can be trusted for 100% of reads.
5. **Filter context + request cache** for repeatable tenant filters.

---

## 4. Caching & "near-zero server load" (see ENTERPRISE_PLAYBOOK)
The cheapest way to scale reads is to not serve them from origin:
- **CDN/edge** for public surface (job boards, careers) + SWR.
- **Redis** for settings/master-data/permission maps + ES result caching (tagged,
  event-invalidated).
- **Pre-computed aggregates** for dashboards (1 ES GET vs many COUNTs).
- **Push (Reverb) over polling** — at 10K tenants, polling dashboards would melt the
  origin; broadcasts cost almost nothing.

> Target: **>90% of requests never reach MySQL.** This is what makes 10K tenants
> affordable on modest origin hardware.

---

## 5. Queue & background scaling (Redis/Horizon)
- **Split queues** (`indexing`, `uploads`, `mail`, `exports`, `default`) — the single
  `default` queue will not survive bulk imports + mass mail + reindex contention.
- **Autoscale workers by queue depth** (KEDA/k8s or Horizon supervisors per host).
- **Redis HA**: Redis is queue+cache+session — use Redis Cluster/Sentinel + separate
  instances per concern so one workload can't evict another (sessions vs cache vs queue).
- **Rate-limit external calls** (mail, WhatsApp, AI, payment) with retries/circuit
  breakers so spikes don't cascade.
- **Idempotent jobs** (safe re-run) — essential at volume.

---

## 6. Application/runtime scaling
- **Stateless web tier** (Octane) behind a load balancer → scale horizontally; no local
  session/file state (Redis sessions ✔, S3 files ✔). Fix Octane state-bleed risks (ADR 0003).
- **Separate concerns into deployables**: web, Horizon workers, scheduler, Reverb — scale
  each independently (workers usually scale most).
- **Move `env()`→`config()` then `config:cache`** ([BUG-02](BUG_REPORT.md)) — required for
  Octane perf and to avoid the null-config outage.
- **Containerize + orchestrate** (k8s/ECS) for autoscaling, rolling deploys, self-heal.

---

## 7. Data lifecycle, compliance & cost
- **Retention policy** per data class; auto-archive/delete (GDPR/DPDP erasure — SEC-12).
- **Tiered storage**: hot (MySQL/ES) → warm (cheaper ES tier) → cold (S3/Glacier) →
  warehouse (BigQuery/Snowflake/ClickHouse) for analytics & the marketplace's
  cross-tenant intelligence.
- **Audit log** of PII access (who viewed/exported candidates) — also a marketplace trust
  feature.

---

## 8. Observability & reliability at scale
- **APM + tracing** (OpenTelemetry/Sentry/New Relic): p95 per route, slow queries, queue
  wait, ES `took`. You can't scale blind.
- **SLOs + error budgets** ([PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md)); alert on
  failed index jobs (= drift), replica lag, Redis evictions, queue backlog.
- **Load test** to 5–10× projected peak before each growth milestone (k6/Gatling).
- **DR**: backups (DB PITR, ES snapshots), tested restore, multi-AZ, RTO/RPO targets.

---

## 9. Phased capacity roadmap
| Stage | Tenants / data | Key moves |
|---|---|---|
| **Now → 500** | < 5M rows | Fix BUG-02/03; split queues; Redis cache; index audit; read replica |
| **500 → 2K** | tens of M | Partition `job_applications`; ES ILM + aliases; CDN edge; APM/SLOs; Reverb-everywhere |
| **2K → 5K** | ~100M/yr | Connection proxy; archive cold data; dedicated DB for top tenants; ES multi-node; autoscale workers |
| **5K → 10K+** | 300M+ cumulative | Tenant sharding by `company_id`; warehouse for analytics/marketplace; multi-region/residency; full DR drills |

---

## 10. The 10 commandments for "won't fail as it grows"
1. `company_id` indexed + enforced on **every** query (no leaks, no full scans).
2. Reads from ES/cache/edge; MySQL writes only.
3. Partition + archive hot tables before they get huge.
4. ES behind **aliases** with ILM — never hardcode index names.
5. Split, autoscale, and make queues/jobs idempotent.
6. Stateless web tier; scale workers independently.
7. `config()` not `env()`; cache everything cacheable.
8. Circuit-break every third party; degrade gracefully.
9. Measure everything; SLOs + load tests gate each growth stage.
10. Plan tenant sharding & data residency **before** you need them — leave the seams in now.
