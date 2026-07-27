# 70 — Jobs & events

> *Intent: name each job, each Reverb event, each queue, with retry policy +*
> *fan-out budget.*

## Jobs

### `ParseResumeJob` (queue: `parse_resumes`)

Orchestrates the parser invocation. Reads `resume_uploads` row,
calls `ResumeParserService::parse($session)`, writes to
`resume_parse_results`, updates session status.

```php
class ParseResumeJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60; // dedups duplicate dispatches within 60 s

    public int $tries = 1;
    public int $timeout = 30;
    public array $backoff = [];

    public function __construct(public int $resumeUploadId) {}

    public function handle(ResumeParserService $parser): void {
        ResumeLogContext::initJob('parse_resumes', 'ParseResumeJob');
        $session = ResumeUpload::findOrFail($this->resumeUploadId);

        if ($session->status !== ResumeUploadStatus::QUEUED_FOR_PARSING) {
            // Already parsed (re-dispatch race). Idempotent short-circuit.
            return;
        }

        if ($session->parser_attempts >= 1) {
            $session->update(['status' => ResumeUploadStatus::FAILED, 'last_error' => 'ALREADY_ATTEMPTED']);
            return;
        }

        // Bump attempts BEFORE parse so POD crash mid-parse counts as a try.
        $session->update([
            'status' => ResumeUploadStatus::PARSING,
            'parser_started_at' => now(),
            'parser_attempts' => $session->parser_attempts + 1,
        ]);
        broadcast(new ResumeParseStarted($session))->toOthers();

        try {
            $results = $parser->parse($session->r2_temp_key, $session->mime_type);
            $session->resumeParseResult()->create([
                'company_id'         => $session->company_id,
                'payload'            => $results->payload(),
                'extractor_version'  => 'v2.0.0',
                'extracted_at'       => now(),
            ]);
            $session->update([
                'status' => ResumeUploadStatus::PARSED,
                'parser_completed_at' => now(),
            ]);
            broadcast(new ResumeParsed($session))->toOthers();
            ResumeLogContext::event('parse_completed', ['uuid' => $session->uuid, 'took_ms' => $tookMs]);
        } catch (RetryAfterException $e) {
            // Honor Retry-After, single short retry
            sleep(min($e->retryAfterSeconds, 10));
            $service->parse(...); // single retry inline; never re-dispatched
        } catch (Throwable $e) {
            $session->update([
                'status' => ResumeUploadStatus::FAILED,
                'last_error' => Str::limit($e->getMessage(), 200, ''),
                'parser_completed_at' => now(),
            ]);
            broadcast(new ResumeParseFailed($session, $e))->toOthers();
            ResumeLogContext::event('parse_failed', ['uuid' => $session->uuid, 'code' => $e->getCode()]);
        }
    }

    public function uniqueId(): string {
        return (string) $this->resumeUploadId;
    }
}
```

### `ReconcileOrphanUploadsJob` (queue: `reconcile_orphans`)

Daily at 03:30 IST. (Inherited from v1; v2 widens scope to also
bill parser-timeout orphans.)

```php
// ... same shape as v1
public int $tries = 1;
public int $timeout = 600;
```

### `RepairStuckSessionsJob` (queue: `repair_stuck`)

Every 15 min. Re-queues parser jobs for sessions in
`QueuedForParsing`/`Parsing` for > 30 min.

```php
public int $tries = 1;
public int $timeout = 840;
```

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

**Payload**:
```json
{
  "uuid": "...",
  "original_filename": "...",
  "file_size_bytes": 142137,
  "upload_completed_at": "2026-07-26T11:55:00Z"
}
```

### `ResumeParseProgress`

For long-running parses; broadcast every 25% threshold.

**Payload**:
```json
{
  "uuid": "...",
  "pct": 25 | 50 | 75 | 100,
  "stage": "extract_text" | "extract_fields" | "build_payload"
}
```

### `ResumeParsed`

**Payload**:
```json
{
  "uuid": "...",
  "fields_summary": { "n_fields": 8, "took_ms": 7400, "extractor_version": "v2.0.0" }
}
```

### `ResumeParseFailed`

**Payload**:
```json
{
  "uuid": "...",
  "code": "PAR_TIMEOUT" | "PAR_429" | "PAR_AUTH" | "PAR_BODY" | "PAR_OTHER",
  "retry_after_s": 5,        // only for PAR_429
  "message_safe": "Server is busy, please try again later"
}
```

### `ResumeCancelled`

**Payload**:
```json
{
  "uuid": "...",
  "at_pct": 65
}
```

## Event payload delivery guarantees

| Event | Best-effort | Future enhancement |
|---|---|---|
| `ResumeUploaded` | at least once | n/a |
| `ResumeParseProgress` | at most once (throttled) | confirmation backlog |
| `ResumeParsed` | at least once | n/a |
| `ResumeParseFailed` | at least once | n/a |

We use Reverb, which is in-memory; for at-least-once semantics the
server's last-write-wins is the user-perceived truth (and we never
mutate state via Reverb — only UI sync).
