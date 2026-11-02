<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminBenchOutboxPruneCommand
 *
 * Intent: Retention lifecycle management and compaction CLI for Super Admin Bench Outbox
 * (sys_outbox_events) and distributed idempotency keys (sys_idempotency_keys).
 * Safely removes completed, obsolete records older than the configured retention horizon
 * in small transactional batches with throttle delays to prevent row/table lock contention.
 *
 * Usage:
 *   php artisan superadmin:bench:outbox-prune              (Default: prune > 14 days)
 *   php artisan superadmin:bench:outbox-prune --days=7    (Prune older than 7 days)
 *   php artisan superadmin:bench:outbox-prune --dry-run   (Simulate count without deletion)
 *
 * Tenancy: Scoped to aggregate_type = 'BenchCandidate' and tenant_id = 0.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchOutboxPruneCommand extends Command
{
    protected $signature = 'superadmin:bench:outbox-prune 
                            {--days=14 : Retention horizon in days for completed outbox events}
                            {--batch-size=500 : Number of records to delete per iteration}
                            {--dry-run : Simulate pruning without executing deletes}
                            {--json : Output results as JSON}';

    protected $description = 'Prune old completed bench candidate outbox events and expired idempotency keys';

    public function handle(): int
    {
        $days = max((int) $this->option('days'), 1);
        $batchSize = max((int) $this->option('batch-size'), 50);
        $dryRun = (bool) $this->option('dry-run');
        $isJson = (bool) $this->option('json');

        $cutoff = Carbon::now()->subDays($days);

        // 1. Calculate Outbox Prune Targets
        $outboxQuery = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchCandidate')
            ->where('status', 'COMPLETED')
            ->where('created_at', '<', $cutoff);

        $outboxPrunableCount = $outboxQuery->count();

        // 2. Calculate Idempotency Keys Prune Targets
        $idempotencyQuery = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where(function ($q) {
                $q->where('idempotency_key', 'like', 'sa_app_%')
                    ->orWhere('idempotency_key', 'like', 'autoapp_%')
                    ->orWhere('idempotency_key', 'like', 'approve_%')
                    ->orWhere('idempotency_key', 'like', 'reject_%');
            })
            ->where(function ($q) use ($cutoff) {
                $q->where(function ($sub) use ($cutoff) {
                    $sub->where('status', 'COMPLETED')
                        ->where('created_at', '<', $cutoff);
                })->orWhere(function ($sub) {
                    $sub->where('expires_at', '<', Carbon::now())
                        ->where('status', '!=', 'PROCESSING');
                });
            });

        $idempotencyPrunableCount = $idempotencyQuery->count();

        if ($dryRun) {
            $report = [
                'mode' => 'DRY_RUN',
                'retention_days' => $days,
                'cutoff_date' => $cutoff->toIso8601String(),
                'bench_outbox_prunable' => $outboxPrunableCount,
                'idempotency_keys_prunable' => $idempotencyPrunableCount,
            ];

            if ($isJson) {
                $this->output->writeln(json_encode($report, JSON_PRETTY_PRINT));
            } else {
                $this->info('========================================================================');
                $this->info('   SUPER ADMIN BENCH OUTBOX PRUNE (DRY RUN SIMULATION)                  ');
                $this->info('========================================================================');
                $this->line("Retention Horizon     : > {$days} days (older than {$cutoff->toDateTimeString()})");
                $this->line("Bench Outbox Events   : {$outboxPrunableCount} records eligible for purge");
                $this->line("Idempotency Keys      : {$idempotencyPrunableCount} records eligible for purge");
                $this->info('Zero records were deleted.');
            }

            return 0;
        }

        // 3. Batch Deletion of Outbox Events
        $deletedOutbox = 0;
        while (true) {
            $ids = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchCandidate')
                ->where('status', 'COMPLETED')
                ->where('created_at', '<', $cutoff)
                ->limit($batchSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $count = DB::table('sys_outbox_events')->whereIn('id', $ids)->delete();
            $deletedOutbox += $count;

            if ($count < $batchSize) {
                break;
            }

            usleep(20000); // 20ms pause between batches
        }

        // 4. Batch Deletion of Expired Idempotency Keys
        $deletedIdempotency = 0;
        while (true) {
            $keyIds = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where(function ($q) {
                    $q->where('idempotency_key', 'like', 'sa_app_%')
                        ->orWhere('idempotency_key', 'like', 'autoapp_%')
                        ->orWhere('idempotency_key', 'like', 'approve_%')
                        ->orWhere('idempotency_key', 'like', 'reject_%');
                })
                ->where(function ($q) use ($cutoff) {
                    $q->where(function ($sub) use ($cutoff) {
                        $sub->where('status', 'COMPLETED')
                            ->where('created_at', '<', $cutoff);
                    })->orWhere(function ($sub) {
                        $sub->where('expires_at', '<', Carbon::now())
                            ->where('status', '!=', 'PROCESSING');
                    });
                })
                ->limit($batchSize)
                ->pluck('id');

            if ($keyIds->isEmpty()) {
                break;
            }

            $count = DB::table('sys_idempotency_keys')->whereIn('id', $keyIds)->delete();
            $deletedIdempotency += $count;

            if ($count < $batchSize) {
                break;
            }

            usleep(20000); // 20ms pause between batches
        }

        if ($isJson) {
            $this->output->writeln(json_encode([
                'status' => 'SUCCESS',
                'retention_days' => $days,
                'outbox_deleted' => $deletedOutbox,
                'idempotency_deleted' => $deletedIdempotency,
            ], JSON_PRETTY_PRINT));

            return 0;
        }

        $this->info('========================================================================');
        $this->info('               SUPER ADMIN BENCH OUTBOX COMPACTION COMPLETE             ');
        $this->info('========================================================================');
        $this->line("Retention Horizon    : > {$days} days");
        $this->line("Deleted Outbox Events: {$deletedOutbox}");
        $this->line("Deleted Idempotency  : {$deletedIdempotency}");
        $this->info('========================================================================');

        return 0;
    }
}
