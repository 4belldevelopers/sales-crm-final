# 40 — Data model

> *Intent: define every column/status the v2 implementation creates. All names*
> *follow the baked-in-typo policy (CLAUDE.md rule 7): if a typo is on a real*
> *table today, we keep it; this document introduces new names only.*

> **Provenance note:** The Phase-1 migrations from `database/migrations/`
> introduced the `resume_uploads` table at v1 Phase 1. v2 ADDS columns but does
> NOT drop old ones. Always read [database/schema/mysql-schema.sql] for actual
> column names before writing migrations.

## Tables

### `resume_uploads` (existing + additive migration)

Add columns; do not rename. CLAUDE.md rule 7.

| Column | Type | Index | Notes |
|---|---|---|---|
| `id` | bigIncrements | PK | |
| `uuid` | char(36) | UQ | |
| `company_id` | unsignedInteger | idx | tenant scope (CLAUDE.md rule 3) |
| `user_id` | unsignedInteger | idx | creator; nullable for public careers |
| `status` | varchar(32) | idx | enum below |
| `original_filename` | varchar(255) | | |
| `stored_filename` | varchar(255) | | |
| `file_hash` | char(64) | (compound idem) | SHA256 of file bytes |
| `idempotency_key` | char(64) | idx (company_id) | per-request random UUID from `Idempotency-Key` HTTP header (NOT a content hash; see ADR-002, INV-4) |
| `r2_temp_key` | varchar(512) | nullable | |
| `r2_temp_url` | varchar(512) | nullable | presigned URL |
| `r2_url` | varchar(512) | nullable | baked-final S3 URL |
| `mime_type` | varchar(127) | | |
| `file_size_bytes` | unsignedBigInteger | | cap 6 MB |
| `candidate_id` | unsignedInteger | nullable | |
| `bench_application_id` | unsignedInteger | nullable | |
| `linked_candidate_id` | unsignedInteger | nullable | NEW in v2: distinct from candidate_id; conservative dedupe |
| `expires_at` | timestamp | nullable | session TTL = Created + 30 min |
| `upload_completed_at` | timestamp | nullable | |
| `parser_started_at` | timestamp | nullable | |
| `parser_completed_at` | timestamp | nullable | |
| `parser_attempts` | unsignedTinyInt | default 0 | capped at 1 |
| `last_error` | text | nullable | sanitized, code only |
| `cleanup_reason` | varchar(64) | nullable | preserved from v1 migration |
| `created_at` | timestamp | | |
| `updated_at` | timestamp | | |

**Indexes:**

```sql
UNIQUE KEY `uq_uuid_company` (`uuid`, `company_id`)
KEY `idx_company_idem` (`company_id`, `idempotency_key`)            -- replay-lookup (NO UNIQUE — INV-1, ADR-002, ADR-009)
KEY `idx_status_expires` (`status`, `expires_at`)
KEY `idx_company_created` (`company_id`, `created_at`)
```

> The `idx_company_idem` index is a **replay-lookup aid** so the
> INSERT-then-SELECT pattern in `createOrPresign()` resolves a
> transport retry to its existing session in O(1). It is **not** a
> uniqueness gate. See INV-4, ADR-002, ADR-009.

### `resume_parse_results` (new, additive)

Idempotent with the session row — one row per session. Linked from
`resume_uploads.id` via `resume_upload_id`.

| Column | Type | Notes |
|---|---|---|
| `id` | bigIncrements | PK |
| `resume_upload_id` | unsignedBigInteger | FK |
| `company_id` | unsignedInteger | tenant shadow |
| `payload` | json | {name,email,phone,skills,experience,company,education,keywords} |
| `extractor_version` | varchar(32) | e.g. `v2.0.0` |
| `extracted_at` | timestamp | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `upload_session_audits` (new, additive)

| Column | Type | Notes |
|---|---|---|
| `id` | bigIncrements | PK |
| `resume_upload_id` | unsignedBigInteger | FK |
| `company_id` | unsignedInteger | tenant scope |
| `user_id` | unsignedInteger | actor |
| `action` | varchar(64) | enum below |
| `request_id` | varchar(64) | ULID |
| `pod_id` | varchar(64) | hostname |
| `error_code` | varchar(64) | nullable |
| `message_safe` | text | nullable, sanitized |
| `created_at` | timestamp | |

### `resume_metrics_redis` (non-table, prefixed keys)

Redis-only counter store (PHP unit tests stub). Keys live under
`resume:metrics:{kind}` with a 90-day TTL.

```
resume:metrics:created_total            (int)
resume:metrics:uploaded_total           (int)
resume:metrics:parsed_total             (int)
resume:metrics:reconciled_total         (int)
resume:metrics:parse_failed_total       (int)
resume:metrics:parser_429_total         (int)
resume:metrics:presign_total            (int)
resume:metrics:sessions_expired_total   (int)
```

Counts are **monotonic** via Redis `INCRBY`.

## Status enum

```php
// New: app/Enums/ResumeUploadStatus.php
final class ResumeUploadStatus {
    const CREATED            = 'Created';           // presign issued
    const UPLOADING          = 'Uploading';         // browser is at byte 0..100% (best-effort)
    const UPLOADED           = 'Uploaded';          // R2 object present, finalize POSTed
    const QUEUED_FOR_PARSING = 'QueuedForParsing';  // parse job dispatched
    const PARSING            = 'Parsing';           // worker picked up
    const PARSED             = 'Parsed';            // payload stored
    const FAILED             = 'Failed';            // parser >1 fail, terminal
    const CANCELLED          = 'Cancelled';         // recruiter clicked
    const RECONCILED         = 'Reconciled';        // orphan sweep
    const EXPIRED            = 'Expired';           // session too old
}
```

## Audit actions

```
ActionCreated, ActionPresigned, ActionUploadStart, ActionUploadProgress,
ActionCancelRequested, ActionCancelCompleted, ActionLinked,
ActionParseStarted, ActionParseProgress, ActionParseCompleted,
ActionParseFailed, ActionReconcileScored, ActionReconcileCompleted,
ActionExpireSweep
```

## Transition diagram

```
                ┌──[sweep]───► EXPIRED
                │              │
CREATED ──► UPLOADING ──► UPLOADED ──► QUEUED_FOR_PARSING ──► PARSING ──► PARSED
  │           │                │                                              │
  │           │                ▼                          ◄──────────────► FAILED
  │           ▼              QUEUED                                                       
  ├──► CANCELLED ◄── (any state after CREATED, but only before PARSING) ─── (best-effort) ───► CANCELLED  
  │                                                                              │
  ▼                                                                              ▼
 EXPIRED                                                                     RECONCILED
```

## Migration plan

Additive only. v2 is a series of forward migrations. Rollback is
also additive — DROP the new columns / DROP the new tables.

| Date | Migration | Action |
|---|---|---|
| v2.0.0a | `add_resume_v2_indexes` | UQ (uuid, company_id), IDX (company_id, idempotency_key) — NO UNIQUE on idempotency_key (ADR-002, ADR-009) |
| v2.0.0a | `add_resume_v2_session_columns` | parsesStarted/completed/parser_attempts/expires_at/last_error |
| v2.0.0b | `create_resume_parse_results` | model+index |
| v2.0.0b | `create_upload_session_audits` | audit table |

## Naming strictness

- Resume-related **code** uses `Resume*` prefix.
- Tables: `resume_uploads` (existing), `resume_parse_results` (new),
  `upload_session_audits` (new; not `resume_session_*` because we may
  generalize the audit table to all file uploads in v3).
- No `condidate`/`commericial` typos introduced — only `Resume` keywords.
