<?php

namespace App\Console\Commands;

use App\Services\MarketplaceReconciliationService;
use Illuminate\Console\Command;

/**
 * ReconcileMarketplaceCommand
 *
 * Runs scheduled self-healing reconciliation between MySQL authoritative state
 * and Elasticsearch projections, recovering stalled outbox locks.
 */
class ReconcileMarketplaceCommand extends Command
{
    protected $signature = 'marketplace:reconcile {--limit=100 : Maximum number of recent jobs to scan}';

    protected $description = 'Reconciles Agency Job Marketplace state drift between MySQL and Elasticsearch';

    public function handle(MarketplaceReconciliationService $service): int
    {
        $this->info('[Marketplace Reconciler] Starting self-healing sweep...');

        // 1. Recover stalled outbox events
        $recovered = $service->recoverStalledOutboxEvents(5);
        $this->line("  - Recovered stalled outbox events: {$recovered}");

        // 2. Heal projection drift
        $limit = (int) $this->option('limit');
        $result = $service->healProjectionDrift($limit);
        $this->line("  - Scanned recent jobs: {$result['scanned']}");
        $this->line("  - Drift repaired in Elasticsearch: {$result['repaired']}");
        $this->line("  - Errors encountered: {$result['errors']}");

        $this->info('[Marketplace Reconciler] Sweep completed successfully.');

        return Command::SUCCESS;
    }
}
