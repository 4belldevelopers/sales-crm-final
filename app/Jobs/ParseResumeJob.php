<?php

/**
 * Downloads a resume from R2, extracts text, parses structured data via OpenAI,
 * and updates candidate_resume_uploads. If a candidate_id is known (edit flow),
 * also updates the job_applications record with parsed fields.
 * Runs on the 'default' queue so it never blocks an HTTP response.
 */

namespace App\Jobs;

use App\Models\CandidateResumeUpload;
use App\Services\ResumeParserService;
use App\Services\ResumeTextExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParseResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;
    public int $timeout = 180;

    public function __construct(private readonly int $resumeUploadId) {}

    public function handle(ResumeTextExtractorService $extractor, ResumeParserService $parser): void
    {
        $record = CandidateResumeUpload::find($this->resumeUploadId);

        if (!$record) {
            Log::error('ParseResumeJob: record not found', ['id' => $this->resumeUploadId]);
            return;
        }

        try {
            $record->update(['status' => 'Processing']);

            // 1. Extract text from R2 file
            $text = $extractor->extractFromR2($record->object_key);

            // 2. Parse structured data
            $parsed = $parser->parse($text);

            // 3. Persist results
            $record->update([
                'status'      => 'Completed',
                'resume_text' => mb_substr($text, 0, 65535),
                'parsed_data' => $parsed,
                'error_log'   => null,
            ]);

            // 4. If candidate already exists (edit flow), sync parsed fields
            if ($record->candidate_id) {
                $this->syncToCandidate($record->candidate_id, $parsed);
            }

        } catch (\Throwable $e) {
            Log::error('ParseResumeJob failed', [
                'resume_upload_id' => $this->resumeUploadId,
                'error'            => $e->getMessage(),
                'trace'            => $e->getTraceAsString(),
            ]);

            $record->update([
                'status'    => 'Failed',
                'error_log' => $e->getMessage(),
            ]);

            throw $e; // trigger retry
        }
    }

    /**
     * Sync non-empty parsed fields to job_applications without overwriting
     * fields the recruiter already filled in.
     */
    private function syncToCandidate(int $candidateId, array $parsed): void
    {
        $existing = DB::table('job_applications')->where('id', $candidateId)->first();
        if (!$existing) {
            return;
        }

        $updates = [];

        if (empty($existing->full_name) && !empty($parsed['full_name'])) {
            $updates['full_name'] = $parsed['full_name'];
        }
        if (empty($existing->email) && !empty($parsed['email'])) {
            $updates['email'] = $parsed['email'];
        }
        if (empty($existing->phone) && !empty($parsed['phone'])) {
            $updates['phone'] = $parsed['phone'];
        }
        if (empty($existing->career_summary) && !empty($parsed['career_summary'])) {
            $updates['career_summary'] = $parsed['career_summary'];
        }

        if (!empty($updates)) {
            DB::table('job_applications')->where('id', $candidateId)->update($updates);
        }
    }
}
