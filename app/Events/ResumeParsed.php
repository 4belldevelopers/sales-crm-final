<?php

namespace App\Events;

use App\Models\ResumeUpload;
use App\Services\ResumeCandidateSync;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * Phase 8 — Resume parsed broadcast.
 *
 * Fired exactly once per parse outcome by
 * {@see \App\Jobs\ParseResumeJob::handle()} on a tenant-scoped
 * private channel. JS subscribers on the create/edit recruiter
 * forms auto-fill empty fields from the `fillable` payload; the
 * candidate detail page (if open) receives the same event and
 * the populate-if-empty helper writes to already-rendered fields
 * with the same no-overwrite contract as the server-side sync.
 *
 * Channel: `App.Models.Company.{cid}`. Auth predicate lives in
 * routes/channels.php — same-tenant only.
 *
 * Broadcast naming: `resume.parsed` (the leading `.` is how
 * Echo's `.listen('.resume.parsed', ...)` matches the
 * `broadcastAs()` return value).
 *
 * Payload is intentionally minimal:
 *   - session_uuid + company_id + candidate_id (for routing & UI)
 *   - status (Parsed or ParseFailed)
 *   - fillable (only columns the {@see ResumeCandidateSync}
 *     COLUMN_MAP authorizes — never the raw fingerprint blob)
 *   - parser_vendor / parsed_at (for the JS to render vendor tag)
 *   - request_id / trace_id (Phase 6 — same preamble so log
 *     correlation works whether you start at the broadcast or
 *     the structured log)
 *
 * Backward compat: success and failure share the channel so the
 * JS never has to subscribe twice. The `succeeded` flag in the
 * payload is the gating signal — recruiter UI shows "Resume
 * parsed" or "Resume parsing failed, please re-upload".
 */
class ResumeParsed implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(
        public readonly ResumeUpload $row,
        public readonly bool $succeeded,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.Company.' . $this->row->company_id)];
    }

    public function broadcastAs(): string
    {
        return 'resume.parsed';
    }

    public function broadcastWith(): array
    {
        $parsed = is_array($this->row->parsed_data_json)
            ? $this->row->parsed_data_json
            : (array) $this->row->parsed_data_json;

        // Project only the columns the sync authorizes. Recruiters
        // never see fingerprint bytes, vendor internal flags,
        // last-known file size, or anything else outside the
        // column map. This is a contract: edits to the column map
        // require a parallel edit here.
        $fillable = [];
        foreach (ResumeCandidateSync::COLUMN_MAP as $parsedKey => $column) {
            if (! empty($parsed[$parsedKey])) {
                $fillable[$column] = (string) $parsed[$parsedKey];
            }
        }

        return [
            'session_uuid' => $this->row->uuid,
            'company_id' => (int) $this->row->company_id,
            'candidate_id' => $this->row->candidate_id !== null
                ? (int) $this->row->candidate_id
                : null,
            'status' => $this->succeeded ? 'Parsed' : 'ParseFailed',
            'fillable' => $fillable,
            'parser_vendor' => $this->row->parser_vendor,
            'parsed_at' => optional($this->row->parse_completed_at)->toIso8601String()
                ?? Carbon::now()->toIso8601String(),
            'request_id' => request()?->header('X-Request-Id'),
            'trace_id' => request()?->header('X-Trace-Id'),
        ];
    }
}
