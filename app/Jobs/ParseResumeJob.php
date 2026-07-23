<?php

namespace App\Jobs;

use App\Models\ResumeUpload;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Phase 2 stub for resume parsing.
 *
 * For now: read the file from R2, hash the first 4 KB as a cheap fingerprint,
 * store shape '{fingerprint, parsed}' so the form can show "parsed" indicator
 * and we can recover pre-fill data on form re-open.
 *
 * Phase 3 will swap this implementation for a real vendor (e.g. Afternoon,
 * Sovren, internal PDF.js extract). Wrap the parse step in try/catch — a
 * vendor failure must NOT toast the upload service, just escalate the row
 * to ParseFailed which the JS client can surface as a manual-review banner.
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
        $row = ResumeUpload::query()
            ->where('uuid', $this->sessionUuid)
            ->where('company_id', $this->companyId)
            ->first();
        if ($row === null) {
            return;
        }
        if (! in_array($row->status, [ResumeUpload::STATUS_UPLOADED, ResumeUpload::STATUS_PARSING], true)) {
            // Already past Parsing — another worker won. Quit.
            return;
        }

        $now = Carbon::now();

        $row->status = ResumeUpload::STATUS_PARSING;
        $row->parse_started_at = $now;
        $row->parse_attempt_count = $row->parse_attempt_count + 1;
        $row->save();

        try {
            // Cheap stub: read first 4 KB and hash it (a "fingerprint" of the
            // file content) so the form has SOMETHING to display on re-open.
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

            $parsed = [
                'stub' => true,
                'fingerprint' => $fingerprint,
                'detected_size_bytes' => $row->file_size_bytes,
                'detected_mime' => $row->mime_type,
                'note' => 'Phase 2 stub — replace with vendor parser in Phase 3',
                'parsed_at' => $now->toIso8601String(),
            ];

            $row->status = ResumeUpload::STATUS_PARSED;
            $row->parse_completed_at = $now;
            $row->parsed_data_json = $parsed;
            $row->parser_vendor = 'phase2_stub';
            $row->parser_status = 'Done';
            $row->save();
        } catch (\Throwable $e) {
            report($e);
            if ($this->attempts() >= $this->tries) {
                $row->status = ResumeUpload::STATUS_PARSE_FAILED;
                $row->parser_status = 'Failed';
                $row->save();
                throw $e;
            }
            // Otherwise transient — let Horizon retry via $backoff.
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        $row = ResumeUpload::query()
            ->where('uuid', $this->sessionUuid)
            ->where('company_id', $this->companyId)
            ->first();
        if ($row !== null) {
            $row->status = ResumeUpload::STATUS_PARSE_FAILED;
            $row->parser_status = 'Failed';
            $row->save();
        }
    }
}
