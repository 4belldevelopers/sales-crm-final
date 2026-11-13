<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSuperAdminProfileOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProcessSuperAdminProfileOutboxCommand
 *
 * Intent: Command-line processor and worker for the Super Admin Profile Transactional Outbox.
 * Scans `sys_outbox_events` for pending or retryable SuperAdminProfile events and synchronizes them to Elasticsearch.
 *
 * Signature: php artisan superadmin-profile:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSuperAdminProfileOutboxCommand extends Command
{
    protected $signature = 'superadmin-profile:process-outbox {--limit=50} {--retry-failed} {--retry-dead-letter} {--reconcile}';

    protected $description = 'Process pending Super Admin Profile transactional outbox events for dual-store synchronization and automated reconciliation';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');
        $retryDeadLetter = (bool) $this->option('retry-dead-letter');
        $reconcile = (bool) $this->option('reconcile');

        // Automated Reconciliation
        if ($reconcile) {
            $this->info('Starting automated reconciliation between MySQL users and Elasticsearch...');
            $readService = app(\App\Services\SuperAdmin\SuperAdminProfileReadService::class);
            $indexName = $readService->getIndexName();
            $superAdmins = DB::table('users')->where('is_superadmin', 1)->get();

            $driftRepaired = 0;
            foreach ($superAdmins as $admin) {
                $doc = $readService->buildProfileDocument($admin);
                $needsSync = false;

                if (! $elasticService->indexExists($indexName)) {
                    $needsSync = true;
                } else {
                    $exists = $elasticService->getClient()->exists(['index' => $indexName, 'id' => (string) $admin->id]);
                    if (! $exists) {
                        $needsSync = true;
                    } else {
                        $esDoc = $elasticService->getClient()->get(['index' => $indexName, 'id' => (string) $admin->id]);
                        $esVersion = (int) ($esDoc['_source']['aggregate_version'] ?? 0);
                        if ($esVersion < $doc['aggregate_version']) {
                            $needsSync = true;
                        }
                    }
                }

                if ($needsSync) {
                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $admin->id,
                        'body' => $doc,
                        'refresh' => 'wait_for',
                    ]);
                    \App\Services\SuperAdmin\SuperAdminProfileCacheManager::flushCaches((int) $admin->id);
                    $driftRepaired++;
                    $this->line("Reconciled Super Admin #{$admin->id} ({$admin->email})");
                }
            }

            $this->info("Reconciliation complete. {$driftRepaired} drifted record(s) repaired.");
        }

        if ($retryFailed || $retryDeadLetter) {
            $query = DB::table('sys_outbox_events')->where('aggregate_type', 'SuperAdminProfile');
            if ($retryFailed && $retryDeadLetter) {
                $query->whereIn('status', ['FAILED', 'DEAD_LETTER']);
            } elseif ($retryDeadLetter) {
                $query->where('status', 'DEAD_LETTER');
            } else {
                $query->where('status', 'FAILED');
            }

            $resetCount = $query->update([
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
            ]);
            $this->info("Reset {$resetCount} outbox events to PENDING.");
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'SuperAdminProfile')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if (empty($events)) {
            $this->info('No pending SuperAdminProfile outbox events found.');

            return self::SUCCESS;
        }

        $this->info('Processing ' . count($events) . ' pending SuperAdminProfile outbox events...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $job = new ProcessSuperAdminProfileOutboxEventJob((int) $eventId);
                $job->handle($elasticService);
                $processed++;
                $this->line("Processed outbox event #{$eventId}");
            } catch (Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: " . $e->getMessage());
            }
        }

        $this->info("Successfully processed {$processed} outbox events.");

        return self::SUCCESS;
    }
}
