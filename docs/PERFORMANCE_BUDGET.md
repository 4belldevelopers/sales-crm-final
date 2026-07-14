# Performance Budget

> **[PROPOSED TARGETS]** — these are sensible starting budgets for an ATS SaaS, not
> measured SLOs. No APM/benchmark data was available during analysis. Treat as goals
> to instrument against, then tune. Pair with [BAD_PATTERNS.md](BAD_PATTERNS.md).

## Proposed targets

| Concern | Target (p95) | Notes |
|---|---|---|
| Interactive page (server render) | < 500 ms | Blade page TTFB under Octane |
| DataTables AJAX list endpoint | < 400 ms | server-side paginated lists |
| Elasticsearch query (list/search) | < 150 ms ES round-trip | excludes app render |
| API/JSON action (create/update) | < 600 ms | excludes queued work |
| MySQL query (single) | < 50 ms | < 10 ms for indexed point lookups |
| Resume upload (user-perceived) | < 1 s to ack | actual S3 upload is queued |
| Queue job latency (interactive, e.g. ES index) | < 30 s wait | mail/index jobs |
| Bulk import / mass mail | minutes (background) | must not block requests |
| Interview reminder dispatch | within 1 min of schedule tick | scheduled command |
| Concurrency | sustain N concurrent Octane workers without state leak | size to host |

## Measurable definitions
- **API latency** = time from request received to response sent (exclude queued work).
- **Search latency** = ES HTTP round-trip measured in `ElasticService`.
- **Queue SLA** = enqueue → start processing (Horizon "wait" metric).
- **DB response** = query execution time (enable slow-query log > 50 ms).

## How to instrument (recommended)
- Horizon dashboard for queue wait/throughput/failures per supervisor.
- Laravel Telescope or query log in non-prod for N+1 + slow queries.
- ES `took` field already returned per query — log it in `ElasticService`.
- Octane metrics / Caddy access logs for page latency.

---

## Current bottlenecks (identified, ranked)

1. **🟥 Single `default` Horizon queue** — no isolation; heavy jobs (resume upload,
   mass mail, ES bulk re-index) block interactive indexing/mail.
   *Action:* split queues (`indexing`, `uploads`, `mail`, `default`) + per-queue
   supervisors in `config/horizon.php`.
2. **🟥 `job_applications` table** — wide, central, read on every tracker/dashboard;
   N+1 + possible missing indexes; re-indexed to ES on every change.
   *Action:* confirm indexes on `company_id, job_id, master_status_id, status`;
   eager-load; project only needed columns.
3. **🟧 MySQL↔Elasticsearch dual write** — write amplification + drift; failed index
   jobs desync. *Action:* retries + dead-letter visibility; reconcile command.
4. **🟧 God controllers** — large methods doing many queries per request inflate
   page latency and make hotspots hard to isolate.
5. **🟧 Non-sargable queries** — `DB::raw('DATE(col))` in WHERE (e.g.
   `Company::isFeatured`) defeats indexes.
6. **🟨 Repeated `smtp_settings`/config reads** per mail send — cache in Redis.
7. **🟨 Remote MySQL + remote ES** (`76.13.243.68`) — network RTT adds to every
   query; co-locate or pool connections.

## Octane-specific budget notes
- Long-lived workers: avoid per-request growth in static caches (memory leak →
  worker restarts → latency spikes).
- Reset/avoid stateful singletons holding request data.
