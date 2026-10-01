<?php

namespace App\Console\Commands;

use App\Events\AppTrackerCandidateAssignedEvent;
use App\Services\TransactionalOutboxService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ProcessReverbOutboxCommand
 *
 * Intent: Decoupled outbox worker that claims and broadcasts transactional outbox records to Reverb WebSockets.
 * Reclaims expired leases from crashed workers automatically.
 * Fully decoupled from Elasticsearch indexing to guarantee <50ms real-time UI synchronization.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessReverbOutboxCommand extends Command
{
    protected $signature = 'outbox:process-reverb 
                            {--limit=50 : Number of records to claim per batch} 
                            {--lease=15 : Lease duration in seconds} 
                            {--daemon : Run continuously as a background daemon} 
                            {--sleep=1 : Sleep seconds when no pending records exist}';

    protected $description = 'Process pending Reverb WebSocket records from the dual-lease transactional outbox';

    public function handle(TransactionalOutboxService $outboxService): int
    {
        $limit = (int) $this->option('limit');
        $lease = (int) $this->option('lease');
        $daemon = (bool) $this->option('daemon');
        $sleep = (int) $this->option('sleep');

        $this->info('Starting Reverb Outbox Worker (PID: '.getmypid().')...');

        do {
            $claimToken = (string) Str::uuid();
            $batch = $outboxService->claimReverbBatch($claimToken, $limit, $lease);

            if ($batch->isEmpty()) {
                if ($daemon) {
                    sleep($sleep);

                    continue;
                }
                $this->info('No pending Reverb outbox events to process.');
                break;
            }

            $this->info('Claimed '.$batch->count()." events for Reverb broadcast (Token: {$claimToken}).");

            foreach ($batch as $record) {
                $payload = json_decode($record->payload, true) ?: [];
                $this->processRecord($outboxService, $record, $payload);
            }

        } while ($daemon);

        return Command::SUCCESS;
    }

    /**
     * Process an individual claimed outbox record for Reverb broadcasting.
     */
    protected function processRecord(TransactionalOutboxService $outboxService, object $record, array $payload): void
    {
        try {
            switch ($record->event_type) {
                case 'CandidateAddedToJob':
                    $tenantId = (int) ($record->tenant_id ?? ($payload['company_id'] ?? 0));
                    $jobId = (int) ($record->job_id ?? ($payload['job_id'] ?? 0));
                    $version = (int) ($record->version ?? ($payload['pipeline_version'] ?? 1));
                    $candidateId = (int) ($payload['candidate_id'] ?? 0);
                    $wiseId = (int) ($payload['wise_id'] ?? 0);
                    $masterStatusId = (int) ($payload['master_status_id'] ?? 8);

                    broadcast(new AppTrackerCandidateAssignedEvent(
                        tenantId: $tenantId,
                        jobId: $jobId,
                        version: $version,
                        candidateId: $candidateId,
                        wiseId: $wiseId,
                        masterStatusId: $masterStatusId,
                        metadata: [
                            'rec_candidate_assign_id' => $payload['rec_candidate_assign_id'] ?? null,
                            'source_from_type' => $payload['source_from_type'] ?? null,
                            'candidate_name' => $payload['candidate_name'] ?? null,
                            'added_count' => (int) ($payload['added_count'] ?? 1),
                        ]
                    ));
                    break;

                default:
                    Log::info('ProcessReverbOutboxCommand: unhandled event type', [
                        'id' => $record->id,
                        'event_type' => $record->event_type,
                    ]);
                    break;
            }

            $outboxService->completeReverbEvent($record->id);
            $this->line(" [REVERB-OK] Event ID {$record->id} ({$record->event_type}) broadcast via Reverb.");

        } catch (\Throwable $e) {
            Log::error('ProcessReverbOutboxCommand: failed to broadcast event', [
                'id' => $record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $outboxService->failReverbEvent($record->id, $e->getMessage());
            $this->error(" [REVERB-FAIL] Event ID {$record->id} failed: ".$e->getMessage());
        }
    }
}
