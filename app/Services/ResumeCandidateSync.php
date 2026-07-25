<?php

namespace App\Services;

use App\Jobs\ElasticCandidateIndex;
use App\Models\ResumeUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Phase 8 — Candidate-side empty-fill from parsed resume data.
 *
 * Triggered by {@see \App\Jobs\ParseResumeJob::handle()} when a parsed
 * session's `candidate_id` is non-null (i.e. the recruiter already
 * submitted the candidate before the parser worker returned). Runs in
 * background after the parse so the candidate detail page sees a
 * fully-populated row on the next refresh without a manual reload.
 *
 * Contract — single source of truth for what parsed values may end up
 * on the candidate row:
 *   1. NO overwrites. A recruiter-typed value on the candidate row
 *      always wins. We check the existing column's value first; only
 *      fill when it is null or empty string.
 *   2. Tenant guard. Every UPDATE is gated on `(id=$candidateId AND
 *      company_id=$row->company_id)`. Mis-routed candidates from a
 *      different tenant are unreachable from this code path because
 *      the session row's `company_id` is the one passed in by the job
 *      constructor.
 *   3. Phone digits-only. The recruiters' UI does NOT use intl-tel,
 *      so we sanitize before writing — leading +91 / +1 / +44 etc.
 *      are stripped, plus any spaces, dashes, parens; only digits
 *      remain. Length is preserved at <20 chars.
 *   4. After the candidate row is updated, we re-dispatch
 *      {@see ElasticCandidateIndex} on the SAME `emails` queue as
 *      the controller does today so the board refresh respects any
 *      contract change in the future.
 *
 * Backward compat: if `parsed_data_json` lacks entries for any column
 * in the column map (the Phase-2 stub today, since it writes only a
 * fingerprint), the method is a no-op. The wiring is in place so the
 * vendor integration slot in cleanly.
 */
class ResumeCandidateSync
{
    /**
     * Authoritative list of parsed_data_json -> job_applications
     * columns. Edits to this map MUST come with a corresponding
     * sweep through ResumeParsed event's `broadcastWith()` so the
     * JS module only auto-fills what the server knows about.
     *
     * Kept narrow deliberately. Career-summary spam from a bad
     * parse would surprise recruiters; skills arrays belong on a
     * different column + table.
     */
    public const COLUMN_MAP = [
        'full_name'           => 'full_name',
        'email'               => 'email',
        'phone'               => 'phone',
        'current_company'     => 'current_company',
        'current_designation' => 'current_designation',
        'careersummary'       => 'careersummary',
    ];

    /**
     * Identify the originating row under tenant scope so two pods
     * racing against the same session can't both win. Returns null
     * if the row's candidate_id is missing (the controller has
     * not yet submitted).
     */
    public function fillEmptyColumnsFromParsed(ResumeUpload $row): int
    {
        if ($row === null || $row->candidate_id === null) {
            return 0;
        }
        if (empty($row->parsed_data_json) || ! is_array($row->parsed_data_json)) {
            // Phase-2 stub has no parsed columns to project. Wiring
            // is in place; vendor integration will populate this.
            return 0;
        }

        $companyId = (int) $row->company_id;
        $candidateId = (int) $row->candidate_id;
        $updates = [];
        $skipped = [];

        foreach (self::COLUMN_MAP as $parsedKey => $column) {
            $parsed = $this->normalize($parsedKey, $row->parsed_data_json[$parsedKey] ?? null);
            if ($parsed === null || $parsed === '') {
                continue;
            }

            // Find the column on the candidate row under tenant scope.
            $existing = DB::table('job_applications')
                ->where('id', $candidateId)
                ->where('company_id', $companyId)
                ->value($column);
            if ($this->isFilled($existing)) {
                $skipped[] = $column;
                continue;
            }

            $updates[$column] = $parsed;
        }

        if (empty($updates)) {
            Log::info('jobs.resume.candidate_sync', [
                'event' => 'no_overwrites',
                'session_uuid' => $row->uuid,
                'candidate_id' => $candidateId,
                'skipped_columns' => $skipped,
                'note' => 'No empty candidate columns to fill — recruiter-typed values were preserved.',
            ]);
            return 0;
        }

        $updates['updated_at'] = Carbon::now();

        // Conditional UPDATE gated by tenant — at most one writer
        // wins if multiple pods dispatch this at the same time.
        // The other sees 0 affected rows and bails.
        $affected = DB::table('job_applications')
            ->where('id', $candidateId)
            ->where('company_id', $companyId)
            ->update($updates);

        Log::info('jobs.resume.candidate_sync', [
            'event' => 'filled',
            'session_uuid' => $row->uuid,
            'candidate_id' => $candidateId,
            'company_id' => $companyId,
            'filled_columns' => array_keys(array_diff_key($updates, ['updated_at' => null])),
            'affected_rows' => $affected,
        ]);

        if ($affected > 0) {
            try {
                ElasticCandidateIndex::dispatch($candidateId)
                    ->onConnection('emails');
            } catch (Throwable $e) {
                // Reindex failure must NOT panic the parser worker.
                // Horizon will retry the whole parse sequence; the
                // empty-column write is durable, the reindex is a
                // best-effort.
                Log::warning('jobs.resume.candidate_sync', [
                    'event' => 'reindex_dispatch_failed',
                    'candidate_id' => $candidateId,
                    'session_uuid' => $row->uuid,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return $affected;
    }

    /**
     * Apply per-column coercion rules. Today only `phone` is
     * sanitized; new rules slot in here.
     */
    private function normalize(string $parsedKey, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $stringy = trim((string) $value);
        if ($stringy === '') {
            return null;
        }
        if ($parsedKey === 'phone') {
            // Strip everything except digits, collapse leading 91/1
            // country codes the JS side already appended if missing.
            $digits = preg_replace('/\D+/', '', $stringy);
            if ($digits === '' || strlen($digits) > 18) {
                return null;
            }
            return $digits;
        }
        return $stringy;
    }

    /**
     * Treat as "filled" if anything except null/empty string.
     * Numeric 0 is filled (recruiter typed "0 years exp").
     */
    private function isFilled(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return true;
    }
}
