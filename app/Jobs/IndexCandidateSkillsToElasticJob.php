<?php

namespace App\Jobs;

use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * IndexCandidateSkillsToElasticJob — Asynchronous candidate skill experience ES index worker.
 *
 * Intent:
 * Indexes candidate skill rating experience records into ELASTIC_CANDIDATE_SKILLS_EXP out of the
 * synchronous MySQL database transaction thread into Horizon queue.
 *
 * Tenancy:
 * Scoped by candidate_id and job_id.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class IndexCandidateSkillsToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct(
        public array $skillRecords
    ) {
        $this->onQueue('es-index');
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticService $elasticService): void
    {
        if (empty($this->skillRecords)) {
            return;
        }

        try {
            foreach ($this->skillRecords as $record) {
                $expId = $record['exp_id'] ?? null;
                $skillId = $record['skill_id'] ?? null;
                $skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillId)->first();
                $docId = ! empty($expId) ? (string) $expId : "cand_{$record['candidate_id']}_skill_{$skillId}";

                $elasticService->getClient()->index([
                    'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                    'id' => $docId,
                    'body' => [
                        'skill_name' => $skills->skill_name ?? '',
                        'job_skill_id' => $skillId,
                        'skill_value' => $record['skill_value'],
                        'job_id' => (int) $record['job_id'],
                        'candidate_id' => (int) $record['candidate_id'],
                        'rating' => (int) $record['rating'],
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String(),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('IndexCandidateSkillsToElasticJob failed: '.$e->getMessage(), [
                'record_count' => count($this->skillRecords),
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
