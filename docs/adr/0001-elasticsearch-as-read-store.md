# ADR 0001 — Elasticsearch as a denormalized read store

**Status:** Active (observed in code) · **Reconstructed retroactively, [INFERRED] rationale**

## Decision
Use Elasticsearch 7.17 not only for full-text search but as the primary **read/query
store** for list, search, dashboard, and tracker screens. MySQL remains the source of
truth; data is denormalized into 60+ ES indices (`ats_*`) via services, jobs,
observers, and `Index*ToElasticsearch` artisan commands.

## Reason (inferred)
- ATS workloads are read-heavy with complex multi-field filtering, fuzzy skill
  matching, and ranking that is expensive in MySQL across wide, joined tables.
- ES gives fast faceted search and relevance scoring (`skill_match`, boosting via
  `boostkeywords`) at scale across tenants.

## Alternatives (inferred)
- **MySQL full-text / heavy SQL** — simpler, single store, but poor at fuzzy ranking
  and slow on wide filtered lists.
- **Scout with a managed engine (Algolia/Meilisearch)** — less ops, but cost and less
  control over custom indices/ranking.
- **Materialized views / read replicas** — keeps one datastore but weaker search.

## Tradeoffs
- ➕ Fast, flexible search & list rendering; offloads MySQL.
- ➖ **Dual-write complexity** (MySQL + ES) with no transactional guarantee → drift.
- ➖ Operational overhead (ES cluster, 60+ indices, reindex jobs).
- ➖ Business logic (ranking/matching) embedded in large indexing services — hard to
  test and change.

## Consequences for contributors
When changing an indexed entity: update the matching `Elastic*Index` service, ensure
the Observer/Job re-indexes, and consider a backfill via the relevant
`Index*ToElasticsearch` command. Don't assume reads hit MySQL.
