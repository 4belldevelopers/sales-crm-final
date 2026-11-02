<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMarketplaceOutboxPruneCommand
 *
 * Intent: Retention lifecycle management and database compaction CLI for the Super Admin
 * Marketplace transactional outbox (sys_outbox_events) and distributed idempotency keys (sys_idempotency_keys).
 * Safely removes completed, obsolete records older than the configured retention horizon
 * in small transactional batches to avoid database row/table lock contention.
 *
 * Usage:
 *   php artisan superadmin:marketplace:outbox-prune              (Default: prune > 14 days)
 *   php artisan superadmin:marketplace:outbox-prune --days=7     (Prune older than 7 days)
 *   php artisan superadmin:marketplace:outbox-prune --dry-run    (Simulate count without deletion)
 *
 * Tenancy: Scoped to aggregate_type = 'MarketJob' and expired idempotency keys.
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMarketplaceOutboxPruneCommand extends Command
{
    protected $signature = 'superadmin:marketplace:outbox-prune 
                            {--days=14 : Retention horizon in days for completed outbox events}
                            {--batch-size=500 : Number of records to delete per iteration to avoid lock contention}
                            {--dry-run : Simulate pruning without executing deletes}
                            {--json : Output results as JSON}';

    protected $description = 'Prune old completed marketplace outbox events and expired idempotency keys';

    public function handle(): int
    {
        $days = max((int) $this->option('days'), 1);
        $batchSize = max((int) $this->option('batch-size'), 50);
        $dryRun = (bool) $this->option('dry-run');
        $isJson = (bool) $this->option('json');

        $cutoff = Carbon::now()->subDays($days);

        // 1. Calculate Outbox Prune Targets
        $outboxQuery = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MarketJob')
            ->where('status', 'COMPLETED')
            ->where('created_at', '<', $cutoff);

        $outboxCandidateCount = $outboxQuery->count();

        // 2. Calculate Idempotency Keys Prune Targets (completed older than cutoff OR past expires_at)
        $idempotencyQuery = DB::table('sys_idempotency_keys')
            ->where(function ($q) use ($cutoff) {
                $q->where('status', 'COMPLETED')
                    ->where('created_at', '<', $cutoff);
            })
            ->orWhere(function ($q) {
                $q->where('expires_at', '<', Carbon::now())
                    ->whereIn('status', ['COMPLETED', 'FAILED_FINAL']);
            });

        $idempotencyCandidateCount = $idempotencyQuery->count();

        if ($dryRun) {
            $report = [
                'mode' => 'DRY_RUN',
                'retention_days' => $days,
                'cutoff_timestamp' => $cutoff->toIso8601String(),
                'eligible_outbox_events' => $outboxCandidateCount,
                'eligible_idempotency_keys' => $idempotencyCandidateCount,
            ];

            if ($isJson) {
                $this->line(json_encode($report, JSON_PRETTY_PRINT));
            } else {
                $this->info('[DRY RUN] Marketplace Pruning Simulation:');
                $this->line("  - Cutoff Date: {$cutoff->toDateTimeString()} ({$days} days retention)");
                $this->line("  - Completed Outbox Events eligible for pruning: {$outboxCandidateCount}");
                $this->line("  - Expired/Completed Idempotency Keys eligible for pruning: {$idempotencyCandidateCount}");
                $this->line('  Run without --dry-run to perform deletion.');
            }

            return 0;
        }

        // Execute Batched Pruning: Outbox Events
        $totalOutboxDeleted = 0;
        while (true) {
            $ids = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'MarketJob')
                ->where('status', 'COMPLETED')
                ->where('created_at', '<', $cutoff)
                ->limit($batchSize)
                ->pluck('id')
                ->toArray();

            if (empty($ids)) {
                break;
            }

            $deleted = DB::table('sys_outbox_events')->whereIn('id', $ids)->delete();
            $totalOutboxDeleted += $deleted;

            if ($deleted < $batchSize) {
                break;
            }
        }

        // Execute Batched Pruning: Idempotency Keys
        $totalIdempotencyDeleted = 0;
        while (true) {
            $ids = DB::table('sys_idempotency_keys')
                ->where(function ($q) use ($cutoff) {
                    $q->where('status', 'COMPLETED')
                        ->where('created_at', '<', $cutoff);
                })
                ->orWhere(function ($q) {
                    $q->where('expires_at', '<', Carbon::now())
                        ->whereIn('status', ['COMPLETED', 'FAILED_FINAL']);
                })
                ->limit($batchSize)
                ->pluck('id')
                ->toArray();

            if (empty($ids)) {
                break;
            }

            $deleted = DB::table('sys_idempotency_keys')->whereIn('id', $ids)->delete();
            $totalIdempotencyDeleted += $deleted;

            if ($deleted < $batchSize) {
                break;
            }
        }

        $summary = [
            'status' => 'SUCCESS',
            'retention_days' => $days,
            'outbox_events_pruned' => $totalOutboxDeleted,
            'idempotency_keys_pruned' => $totalIdempotencyDeleted,
            'timestamp' => Carbon::now()->toIso8601String(),
        ];

        if ($isJson) {
            $this->line(json_encode($summary, JSON_PRETTY_PRINT));
        } else {
            $this->info('Marketplace Pruning Complete:');
            $this->line("  - Pruned Outbox Events: {$totalOutboxDeleted}");
            $this->line("  - Pruned Idempotency Keys: {$totalIdempotencyDeleted}");
        }

        return 0;
    }
}
