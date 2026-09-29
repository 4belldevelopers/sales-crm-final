<?php

namespace App\Console\Commands;

use App\Services\TransactionalOutboxService;
use Illuminate\Console\Command;

/**
 * Class PruneTransactionalOutboxCommand
 *
 * Intent: Safely prunes old completed records from transactional_outboxes table
 * to maintain bounded table size and optimal B-Tree index performance.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class PruneTransactionalOutboxCommand extends Command
{
    protected $signature = 'apptracker:prune-outbox {--days=7 : Retention period in days for completed outbox records}';

    protected $description = 'Prune completed dual-lease outbox records older than retention threshold';

    public function handle(TransactionalOutboxService $outboxService): int
    {
        $days = (int) $this->option('days');
        $this->info("Pruning completed outbox records older than {$days} days...");

        $deleted = $outboxService->pruneCompletedEvents($days);

        $this->info("Successfully pruned {$deleted} completed outbox record(s).");

        return Command::SUCCESS;
    }
}
