# 70 — Jobs & events

> *Intent: name each job, each Reverb event, each queue, with retry policy +*
> *fan-out budget.*
>
> **Update 2026-07-27 — `ParseResumeJob`, `parse_resumes` queue, and*
> *four Reverb parse-events REMOVED.** v2 has no server-side parser;*
> *the parser is browser-side (`pdf.js` + `re_test()`). The remaining*
> *job surface is `ReconcileOrphanUploadsJob` (orphan sweep only),*
> *`RepairStuckSessionsJob` (orphan recovery only — parser-half gone),*
> *and `BulkElasticIndexCandidateJob`. Reverb events collapse to*
> *`ResumeUploaded`, `ResumeCancelled`, `ResumeFailed` (status only).*

## Jobs

### `ParseResumeJob` (queue: `parse_resumes`) — REMOVED 2026-07-27

> v2 has no `ParseResumeJob`. There is no `ResumeParserService`; no
> call site; no `parse_resumes` queue; no `parser_attempts` cap;
> no `parser_started_at` / `parser_completed_at` columns; no
> `ResumeParseStarted` / `ResumeParseProgress` / `ResumeParsed` /
> `ResumeParseFailed` Reverb events; no `extractor_version` column.
> The remaining upload-side event surface is **status only**.
> See [`COMPLETE_FLOW_UPLOAD_AND_EDIT.md`](COMPLETE_FLOW_UPLOAD_AND_EDIT.md)
> §4 "Parser pipeline (ONE layer — browser only)".

### `ReconcileOrphanUploadsJob` (queue: `reconcile_orphans`)

Daily at 03:30 IST. Inherited from v1.

```php
// ... same shape as v1
public int $tries = 1;
public int $timeout = 600;
```

> **2026-07-27 update:** this job no longer "widens scope to bill
> parser-timeout orphans." Parser-timeout orphans no longer exist;
> the job is the orphan-reaper only.

### `RepairStuckSessionsJob` (queue: `repair_stuck`)

Every 15 min. Original scope: re-queues parser jobs for sessions in
`QueuedForParsing`/`Parsing` for > 30 min.

```php
public int $tries = 1;
public int $timeout = 840;
```

> **2026-07-27 update:** the parser-half is removed. The job still
> runs every 15 minutes (`onOneServer()`) but only sweeps
> `Created`/`Uploading` orphans and never re-dispatches any
> `ParseResumeJob` (there is none).

### `BulkElasticIndexCandidateJob` (queue: `bulkelasticindex`)

NEW in v2. Batches ES writes.

```php
public int $tries = 5;
public array $backoff = [10, 30, 60, 120, 300];
public int $timeout = 600;
```

Batch trigger: 1,000 candidates OR 5 sec — whichever first.

## Cron schedule (carry forward v1)

```php
// app/Console/Kernel.php
$schedule->command('uploads:cleanup-orphans --dispatch')
    ->dailyAt('03:30')->timezone('Asia/Kolkata')
    ->withoutOverlapping(300)->onOneServer();

$schedule->command('uploads:reconcile --dispatch')
    ->weekly()->sundays()->at('04:30')->timezone('Asia/Kolkata')
    ->withoutOverlapping(600)->onOneServer();

$schedule->command('uploads:repair --dispatch')
    ->everyFifteenMinutes()->timezone('Asia/Kolkata')
    ->withoutOverlapping(840)->onOneServer();

$schedule->command('index:bulk-candidate')
    ->everyMinute()->withoutOverlapping(60)->onOneServer();
```

## Reverb events

### `ResumeUploaded`

Broadcast on `private-resume.{company_id}` channel.

**Payload** (status only — REMOVED 2026-07-27: the previous
`ResumeParsed` payload that included `fields_summary` is gone):
```json
{
  "uuid": "...",
  "original_filename": "...",
  "file_size_bytes": 142137,
  "upload_completed_at": "2026-07-26T11:55:00Z"
}
```

### `ResumeParseProgress` — REMOVED 2026-07-27

> v2 has no server-side parser. There is no progress to broadcast;
> the browser-side `pdf.js` runs in 50–200 ms locally and emits a
> `parseInline` event in-browser — no Reverb round-trip needed.

### `ResumeParsed` — REMOVED 2026-07-27

> v2 has no server-side `ResumeParserService` and therefore no
> `extractor_version`. Form fields are filled by the browser's
> `pdf.js` + `re_test()` during the R2 PUT, not by a server call.

### `ResumeParseFailed` — REMOVED 2026-07-27

> v2 has no `Retry-After`, no parser error codes.
> Parser failures degrade to "empty form fields" only, which
> INV-2 handles fine.

### `ResumeCancelled`

**Payload**:
```json
{
  "uuid": "...",
  "at_pct": 65
}
```

### `ResumeFailed` (status-only, NOT parser-related)

> Still in the model for cross-pod INV-3 races that force a 409
> rollback to terminal. Carrier is the session status alone; no
> parser payload.

**Payload**:
```json
{
  "uuid": "...",
  "last_error": "RESUME_ALREADY_FINALIZED" | "R2 503" | ...
}
```

## Event payload delivery guarantees

| Event | Best-effort | Future enhancement |
|---|---|---|
| `ResumeUploaded` | at least once | n/a |
| `ResumeCancelled` | at least once | n/a |
| `ResumeFailed` | at least once | n/a |
| ~~`ResumeParseProgress`~~ | REMOVED 2026-07-27 | n/a |
| ~~`ResumeParsed`~~ | REMOVED 2026-07-27 | n/a |
| ~~`ResumeParseFailed`~~ | REMOVED 2026-07-27 | n/a |

We use Reverb, which is in-memory; for at-least-once semantics the
server's last-write-wins is the user-perceived truth (and we never
mutate state via Reverb — only UI sync).
