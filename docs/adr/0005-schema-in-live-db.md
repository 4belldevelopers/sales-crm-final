# ADR 0005 — Database schema managed in the live DB, not migrations ⚠️

**Status:** Active (observed) · **[INFERRED] rationale** · **High-risk**

## Decision (as observed)
The MySQL schema for `dbmaster` is maintained directly in the database. The repo
contains only 3 stock Laravel migrations (`users`, `cache`, `jobs`) despite 86 Eloquent
models referencing dozens of tables.

## Reason (inferred — likely accidental, not deliberate)
- Rapid development; schema evolved via GUI/manual SQL on a shared remote DB.
- Possibly inherited from a CodeIgniter/legacy import where migrations weren't backfilled.

## Alternatives
- **Migrations as source of truth** (standard Laravel) — reproducible, versioned,
  FK-enforced, CI-testable.
- **Schema dump checked in** (`schema/mysql-schema.sql` via `schema:dump`) — at least
  a versioned snapshot.

## Tradeoffs
- ➕ (Short-term) faster ad-hoc schema changes.
- ➖ No reproducible/local schema; new envs can't be built from the repo.
- ➖ No FK constraints, no versioned indexes, no review of schema changes.
- ➖ Agents/devs can't trust column existence; high chance of runtime errors.

## Consequences for contributors
- **Never assume a column exists** — confirm against the live/dev DB
  (`php artisan db:table <t>`, `SHOW CREATE TABLE`).
- **Do not auto-generate "missing" migrations** — they could diverge from prod.
- **Recommended remediation** (only if asked): `php artisan schema:dump` to capture a
  snapshot, then introduce migrations forward-only. See [DB_SCHEMA.md](../DB_SCHEMA.md).
