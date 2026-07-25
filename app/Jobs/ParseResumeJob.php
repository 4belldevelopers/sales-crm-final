<?php

namespace App\Jobs;

use App\Events\ResumeParsed;
use App\Logging\ResumeLogContext;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeCandidateSync;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Phase 8 — Resume parsing orchestrator.
 *
 * State machine:
 *   Uploaded ──> QueuedForParsing ──> Parsing ──> Parsed
 *                                          │
 *                                          └─> ParseFailed (only after $tries exhausted)
 *
 * Dispatch seam: {@see \App\Services\ResumeUploadSession::finalize()}
 *   - Transitions Uploaded -> QueuedForParsing in the same DB tx as
 *     the UploadSuccess audit row.
 *   - Then dispatches this job on `redis_parse_resumes` (NOT emails).
 *
 * Side effects on completion:
 *   1. `ResumeParsed` event fires on the tenant-scoped private
 *      channel (`App.Models.Company.{cid}`). JS subscribers see
 *      it within ~100 ms and auto-fill empty fields on forms
 *      and candidate detail pages. Subscribe path in
 *      routes/channels.php.
 *   2. If the session's `candidate_id` is non-null — i.e. the
 *      recruiter already submitted — the candidate's empty
 *      columns are silently filled by {@see ResumeCandidateSync}.
 *      The recruiter-typed values are NEVER overwritten.
 *
 * Retry semantics unchanged from prior phases: $tries=3,
 * $backoff=[30,120,600]. The first parse failure re-throws so
 * Horizon retries. The third failure transitions to ParseFailed
 * + fires ResumeParsed(succeeded:false).
 */
class ParseResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    /** @var array<int,int> seconds per attempt: 30s, 2min, 10min */
    public array $backoff = [30, 120, 600];
    public int $timeout = 60;

    public function __construct(
        public readonly string $sessionUuid,
        public readonly int $companyId,
        public readonly ?int $actorId = null,
    ) {}

    public function handle(): void
    {
        // Phase 6 — bind pod/queue/worker to log context so every
        // line below resolves through ResumeLogContext. Queue name
        // comes from the runtime dispatcher (`parse_resumes`).
        ResumeLogContext::initJob(
            queue: (string) ($this->job?->getQueue() ?? config('resumes.parser_queue', 'parse_resumes')),
            worker: $this->job?->getName() . ':' . (string) ($this->attempts() ?? 0),
        );

        $row = ResumeUpload::tenantScoped($this->companyId)
            ->where('uuid', $this->sessionUuid)
            ->first();
        if ($row === null) {
            return;
        }

        // Phase 8 — accept all three pre-Parsing states so legacy
        // rows still process. Most new rows arrive in
        // QueuedForParsing (set by the dispatch seam); the body
        // is the same regardless of entry state.
        if (! in_array($row->status, [
            ResumeUpload::STATUS_QUEUED_FOR_PARSING,
            ResumeUpload::STATUS_UPLOADED,                  // legacy fallthrough
            ResumeUpload::STATUS_PARSING,                   // resume on a prior partial attempt
        ], true)) {
            return;
        }

        $now = Carbon::now();

        // Conditional UPDATE that gates on (status ∈ {QueuedForParsing, Uploaded}).
        // A second pod's worker observing the same row sees 0 affected
        // rows and returns cleanly — the row is in Parsing on the
        // winning pod. parse_attempt_count bumps once per actual run.
        $picked = DB::table('resume_uploads')
            ->where('id', $row->id)
            ->where('company_id', $this->companyId)
            ->whereIn('status', [
                ResumeUpload::STATUS_QUEUED_FOR_PARSING,
                ResumeUpload::STATUS_UPLOADED,
            ])
            ->update([
                'status' => ResumeUpload::STATUS_PARSING,
                'parse_started_at' => $now,
                'parse_attempt_count' => DB::raw('parse_attempt_count + 1'),
                'updated_at' => $now,
            ]);
        if ($picked === 0) {
            // Another worker won. Exit quietly so we don't double-log.
            return;
        }

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: 'PickedUp',
            actorId: $this->actorId,
            metadata: [
                'attempt' => $row->parse_attempt_count + 1,    // post-increment not refreshed
                'queue' => config('resumes.parser_queue', 'parse_resumes'),
                'path' => 'presigned',
            ],
        );

        $parserVendor = (string) config('resumes.parser_vendor', 'phase2_stub');
        $startedAt = microtime(true);

        try {
            $parsed = $this->runParser($row);
        } catch (Throwable $e) {
            report($e);
            Log::warning('jobs.resume.parse_failed', [
                'session_uuid' => $row->uuid,
                'attempt' => (int) $this->attempts(),
                'tries' => $this->tries,
                'parser' => $parserVendor,
                'exception' => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                // Final failure — escalate the row + broadcast.
                $row->status = ResumeUpload::STATUS_PARSE_FAILED;
                $row->parser_status = 'Failed';
                $row->parser_vendor = $parserVendor;
                $row->save();

                UploadSessionAudit::record(
                    sessionUuid: $row->uuid,
                    companyId: (int) $row->company_id,
                    action: 'ParseFailed',
                    actorId: $this->actorId,
                    metadata: ['attempts' => $this->attempts(), 'parser' => $parserVendor],
                );
                $this->broadcastTerminal($row, succeeded: false);
                throw $e;
            }

            // Otherwise transient — let Horizon retry via $backoff.
            throw $e;
        }

        // Parsing succeeded. Conditional UPDATE that gates on
        // status=Parsing so a stale worker that lost the race above
        // doesn't double-write successful state.
        $finalized = DB::table('resume_uploads')
            ->where('id', $row->id)
            ->where('company_id', $this->companyId)
            ->where('status', ResumeUpload::STATUS_PARSING)
            ->update([
                'status' => ResumeUpload::STATUS_PARSED,
                'parse_completed_at' => $now,
                'parsed_data_json' => json_encode($parsed),
                'parser_vendor' => $parserVendor,
                'parser_status' => 'Done',
                'updated_at' => $now,
            ]);
        if ($finalized === 0) {
            return;
        }

        $row->refresh();
        $row->status = ResumeUpload::STATUS_PARSED;
        $row->parse_completed_at = $now;
        $row->parsed_data_json = $parsed;
        $row->parser_vendor = $parserVendor;
        $row->parser_status = 'Done';

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: 'ParseDone',
            actorId: $this->actorId,
            metadata: [
                'parser' => $parserVendor,
                'bytes' => (int) $row->file_size_bytes,
                'elapsed_ms' => (int) ((microtime(true) - $startedAt) * 1000),
                'queue' => config('resumes.parser_queue', 'parse_resumes'),
            ],
        );

        Log::info('jobs.resume.parse_completed', [
            'session_uuid' => $row->uuid,
            'candidate_id' => $row->candidate_id,
            'parser' => $parserVendor,
            'keys_parsed' => array_keys($parsed),
        ]);

        // Broadcast BEFORE the candidate sync — listeners on the
        // candidate detail page see Parsed state, then observe
        // the column-write + reindex landing.
        $this->broadcastTerminal($row, succeeded: true);

        if ($row->candidate_id !== null) {
            try {
                app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
            } catch (Throwable $e) {
                // Candidate sync failure must NOT panic the parser
                // worker — the broadcast already went out, and the
                // row is durably in `Parsed`. Log + carry on.
                Log::warning('jobs.resume.candidate_sync_failed', [
                    'session_uuid' => $row->uuid,
                    'candidate_id' => $row->candidate_id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }
    }

    public function failed(Throwable $e): void
    {
        // Invoked when all retries are exhausted by Horizon.
        // Make sure the row is in `ParseFailed` and partners
        // received one final ResumeParsed(succeeded:false) — the
        // retry counter may not reach this method if some early
        // attempt succeeded enough to write Audit; idempotent
        // transition ensures correct end state regardless.
        $row = ResumeUpload::tenantScoped($this->companyId)
            ->where('uuid', $this->sessionUuid)
            ->first();
        if ($row === null) {
            return;
        }
        if ($row->status !== ResumeUpload::STATUS_PARSE_FAILED) {
            DB::table('resume_uploads')
                ->where('id', $row->id)
                ->where('company_id', $this->companyId)
                ->update([
                    'status' => ResumeUpload::STATUS_PARSE_FAILED,
                    'parser_status' => 'Failed',
                    'updated_at' => Carbon::now(),
                ]);
            $row->refresh();
        }
        $this->broadcastTerminal($row, succeeded: false);
    }

    /**
     * Extract parser body to a single seam so the vendor integration
     * Phase 8 unlocks can slot in here without touching the state
     * machine. Today's `phase2_stub` hashes the first 4 KB and
     * returns a fingerprint-only blob; nothing of consequence for
     * recruiter UX.
     *
     * Phase 3 vendor slot: replace this method body with the
     * vendor's HTTP call. The return shape stays the same —
     * keys are projected against `ResumeCandidateSync::COLUMN_MAP`
     * on the broadcast side, so any extra keys we add here are
     * dropped before reaching the JS.
     */
    private function runParser(ResumeUpload $row): array
    {
        $disk = Storage::disk(config('filesystems.uploads_disk', 'r2'));
        $bytes = '';
        if ($disk->exists($row->r2_path)) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $stream = $disk->readStream($row->r2_path);
            if (is_resource($stream)) {
                $bytes = (string) fread($stream, 4096);
                fclose($stream);
            }
        }
        $fingerprint = substr(hash('sha256', $bytes), 0, 32);

        return [
            // Phase-2 stub — only fingerprint populates today.
            // Phase 3 vendor slot will add full_name / email / phone
            // / current_company / current_designation / careersummary.
            'fingerprint' => $fingerprint,
            'detected_size_bytes' => (int) $row->file_size_bytes,
            'detected_mime' => (string) $row->mime_type,
            'parsed_at' => Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * Single-channel broadcast; success and failure share the same
     * channel and `broadcastAs()` so the JS subscribes once. The
     * `ResumeParsed` event reads the row's lifecycle state.
     */
    private function broadcastTerminal(ResumeUpload $row, bool $succeeded): void
    {
        try {
            event(new ResumeParsed($row, $succeeded));
        } catch (Throwable $e) {
            // A broadcaster that fails to dispatch (Redis down, Reverb
            // misconfigured) MUST NOT panic the parser. Log loud;
            // do not rethrow.
            Log::warning('jobs.resume.broadcast_failed', [
                'session_uuid' => $row->uuid,
                'succeeded' => $succeeded,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
