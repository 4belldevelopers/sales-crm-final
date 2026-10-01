<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\TransactionalOutboxService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ProcessElasticsearchOutboxCommand
 *
 * Intent: Decoupled outbox worker that claims and syncs transactional outbox records to Elasticsearch.
 * Reclaims expired leases from crashed workers automatically.
 * Fully decoupled from Reverb WebSocket broadcasting to eliminate cross-subsystem latency blocking.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessElasticsearchOutboxCommand extends Command
{
    protected $signature = 'outbox:process-es 
                            {--limit=50 : Number of records to claim per batch} 
                            {--lease=30 : Lease duration in seconds} 
                            {--daemon : Run continuously as a background daemon} 
                            {--sleep=2 : Sleep seconds when no pending records exist}';

    protected $description = 'Process pending Elasticsearch records from the dual-lease transactional outbox';

    public function handle(TransactionalOutboxService $outboxService): int
    {
        $limit = (int) $this->option('limit');
        $lease = (int) $this->option('lease');
        $daemon = (bool) $this->option('daemon');
        $sleep = (int) $this->option('sleep');

        $this->info('Starting Elasticsearch Outbox Worker (PID: '.getmypid().')...');

        do {
            $claimToken = (string) Str::uuid();
            $batch = $outboxService->claimEsBatch($claimToken, $limit, $lease);

            if ($batch->isEmpty()) {
                if ($daemon) {
                    sleep($sleep);

                    continue;
                }
                $this->info('No pending Elasticsearch outbox events to process.');
                break;
            }

            $this->info('Claimed '.$batch->count()." events for Elasticsearch processing (Token: {$claimToken}).");

            foreach ($batch as $record) {
                $payload = json_decode($record->payload, true) ?: [];
                $this->processRecord($outboxService, $record, $payload);
            }

        } while ($daemon);

        return Command::SUCCESS;
    }

    /**
     * Process an individual claimed outbox record for Elasticsearch indexing.
     */
    protected function processRecord(TransactionalOutboxService $outboxService, object $record, array $payload): void
    {
        try {
            switch ($record->event_type) {
                case 'CandidateAddedToJob':
                    $wiseId = (int) ($payload['wise_id'] ?? 0);
                    if ($wiseId > 0) {
                        GeneralMethod::JobApplicationsAddEditUpload($wiseId);
                    }
                    $benchCandidateId = (int) ($payload['candidate_id'] ?? 0);
                    if ($benchCandidateId > 0 && (int) ($payload['source_from_type'] ?? 0) > 0) {
                        try {
                            GeneralMethod::elasticBenchCandidateAddEditIndex($benchCandidateId);
                        } catch (\Throwable $e) {
                            Log::warning("ProcessElasticsearchOutboxCommand: bench sync failed for candidate #{$benchCandidateId}: ".$e->getMessage());
                        }
                    }
                    break;

                default:
                    Log::info('ProcessElasticsearchOutboxCommand: unhandled event type', [
                        'id' => $record->id,
                        'event_type' => $record->event_type,
                    ]);
                    break;
            }

            $outboxService->completeEsEvent($record->id);
            $this->line(" [ES-OK] Event ID {$record->id} ({$record->event_type}) synced to Elasticsearch.");

        } catch (\Throwable $e) {
            Log::error('ProcessElasticsearchOutboxCommand: failed to index event', [
                'id' => $record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $outboxService->failEsEvent($record->id, $e->getMessage());
            $this->error(" [ES-FAIL] Event ID {$record->id} failed: ".$e->getMessage());
        }
    }
}
