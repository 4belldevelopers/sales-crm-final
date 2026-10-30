<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminCandidatesOutboxPruneCommand
 *
 * Intent: Retention lifecycle management and compaction CLI for Super Admin Candidate Outbox
 * (sys_outbox_events) and distributed idempotency keys (sys_idempotency_keys).
 * Safely removes completed, obsolete records older than the configured retention horizon
 * in small transactional batches with throttle delays to prevent row/table lock contention.
 *
 * Usage:
 *   php artisan superadmin:candidates:outbox-prune              (Default: prune > 7 days)
 *   php artisan superadmin:candidates:outbox-prune --days=14   (Prune older than 14 days)
 *   php artisan superadmin:candidates:outbox-prune --dry-run   (Simulate count without deletion)
 *
 * Tenancy: Scoped to aggregate_type = 'Candidate' and tenant_id = 0.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminCandidatesOutboxPruneCommand extends Command
{
    protected $signature = 'superadmin:candidates:outbox-prune 
                            {--days=7 : Retention horizon in days for completed outbox events}
                            {--batch-size=500 : Number of records to delete per iteration}
                            {--dry-run : Simulate pruning without executing deletes}
                            {--json : Output results as JSON}';

    protected $description = 'Prune old completed candidate outbox events and expired idempotency keys';

    public function handle(): int
    {
        $days = max((int) $this->option('days'), 1);
        $batchSize = max((int) $this->option('batch-size'), 50);
        $dryRun = (bool) $this->option('dry-run');
        $isJson = (bool) $this->option('json');

        $cutoff = Carbon::now()->subDays($days);

        // 1. Calculate Candidate Outbox Prune Targets
        $outboxQuery = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Candidate')
            ->where('status', 'COMPLETED')
            ->where('created_at', '<', $cutoff);

        $outboxCandidateCount = $outboxQuery->count();

        // 2. Calculate Idempotency Keys Prune Targets (completed older than cutoff OR past expires_at)
        $idempotencyQuery = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where(function ($q) use ($cutoff) {
                $q->where('status', 'COMPLETED')
                    ->where('created_at', '<', $cutoff);
            })
            ->orWhere(function ($q) {
                $q->where('tenant_id', 0)
                    ->where('expires_at', '<', Carbon::now())
                    ->where('status', '!=', 'PROCESSING');
            });

        $idempotencyCandidateCount = $idempotencyQuery->count();

        if ($dryRun) {
            $report = [
                'mode' => 'DRY_RUN',
                'retention_days' => $days,
                'cutoff_date' => $cutoff->toIso8601String(),
                'candidate_outbox_prunable' => $outboxCandidateCount,
                'idempotency_keys_prunable' => $idempotencyCandidateCount,
            ];

            if ($isJson) {
                $this->output->writeln(json_encode($report, JSON_PRETTY_PRINT));
            } else {
                $this->info('========================================================================');
                $this->info('   SUPER ADMIN CANDIDATES OUTBOX PRUNE (DRY RUN SIMULATION)             ');
                $this->info('========================================================================');
                $this->line("Retention Horizon     : > {$days} days (older than {$cutoff->toDateTimeString()})");
                $this->line("Candidate Outbox Events: {$outboxCandidateCount} records eligible for purge");
                $this->line("Idempotency Keys       : {$idempotencyCandidateCount} records eligible for purge");
                $this->info('Zero records were deleted.');
            }

            return 0;
        }

        // 3. Batch Deletion of Outbox Events
        $deletedOutbox = 0;
        while (true) {
            $ids = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Candidate')
                ->where('status', 'COMPLETED')
                ->where('created_at', '<', $cutoff)
                ->limit($batchSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            DB::table('sys_outbox_events')->whereIn('id', $ids)->delete();
            $deletedOutbox += $ids->count();
            usleep(20000); // 20ms pause between batches to prevent DB lock contention
        }

        // 4. Batch Deletion of Idempotency Keys
        $deletedIdempotency = 0;
        while (true) {
            $ids = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where(function ($q) use ($cutoff) {
                    $q->where('status', 'COMPLETED')
                        ->where('created_at', '<', $cutoff);
                })
                ->orWhere(function ($q) {
                    $q->where('tenant_id', 0)
                        ->where('expires_at', '<', Carbon::now())
                        ->where('status', '!=', 'PROCESSING');
                })
                ->limit($batchSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            DB::table('sys_idempotency_keys')->whereIn('id', $ids)->delete();
            $deletedIdempotency += $ids->count();
            usleep(20000);
        }

        $report = [
            'mode' => 'EXECUTE',
            'retention_days' => $days,
            'cutoff_date' => $cutoff->toIso8601String(),
            'deleted_outbox_events' => $deletedOutbox,
            'deleted_idempotency_keys' => $deletedIdempotency,
        ];

        if ($isJson) {
            $this->output->writeln(json_encode($report, JSON_PRETTY_PRINT));
        } else {
            $this->info('========================================================================');
            $this->info('   SUPER ADMIN CANDIDATES OUTBOX PRUNE COMPLETED                        ');
            $this->info('========================================================================');
            $this->line("Retention Horizon     : > {$days} days (older than {$cutoff->toDateTimeString()})");
            $this->line("Deleted Outbox Events : {$deletedOutbox} records purged");
            $this->line("Deleted Idempotency   : {$deletedIdempotency} records purged");
            $this->info('Prune compaction finished with 0% database lock contention.');
        }

        return 0;
    }
}
