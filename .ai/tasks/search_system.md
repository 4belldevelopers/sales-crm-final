# Task Memory — Search & Indexing System

## Purpose
Elasticsearch is the **denormalized read store** for lists, search, dashboards, and
trackers. 60+ `ats_*` indices (see `.env` `ELASTIC_*`). MySQL is source of truth; ES
is kept in sync asynchronously.

## Workflows
- **Index (write):** model change → Observer → `Elastic*Index` job → `Elastic*Index`
  service builds denormalized doc → `ElasticService` (HTTP) → ES upsert.
- **Backfill:** artisan `Index*ToElasticsearch` rebuilds an index from MySQL.
- **Query (read):** controller builds ES query (bool/filter, from/size) →
  `ElasticService` → hits → DataTables JSON / Blade.
See [DATA_FLOW.md](../../docs/DATA_FLOW.md) §5 and [adr/0001](../../docs/adr/0001-elasticsearch-as-read-store.md).

## Business rules (verify before changing)
- **Any change to an indexed field requires updating the matching `Elastic*Index`
  service AND ensuring the Observer/Job re-indexes.** Otherwise reads go stale.
- Ranking/matching (`skill_match`, boosting via `boostkeywords`) is business logic —
  do not alter without explicit confirmation.
- Index names come from `.env` (`ELASTIC_*`) — never hardcode index names.
- Dual-write has no transaction; failed index jobs cause drift — handle/retry.

## Common files
- Services: `ElasticService.php` (HTTP wrapper), `ElasticCandidatesIndex.php`,
  `ElasticBenchCandidatesIndex.php`, `ElasticJobIndex.php`, `ElasticJobListIndex.php`,
  `ElasticCaompanyIndex.php` (sic)
- Jobs: `ElasticCandidateIndex`, `ElasticBenchCandidateIndex`, `ElasticJobsIndex`,
  `ElasticCompanyIndex`
- Commands: `app/Console/Commands/Index*ToElasticsearch.php`
- Models (ES mirrors): `ElasticCandidate`, `ElasticJobs`, `ElasticCampany`, etc.
- Config: `config/elasticsearch.php`, `.env` `ELASTIC_*`

## Gotchas
- Spellings `Caompany`, `Commericial` are intentional (map to real code/indices).
- ES version is 7.17 — use 7.x query/mapping syntax, not 8.x.
- Re-indexing is heavy and shares the single `default` queue — beware contention.
- After bulk schema/field changes, run the relevant `Index*ToElasticsearch` backfill.
