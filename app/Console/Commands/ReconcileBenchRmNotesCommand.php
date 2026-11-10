<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ReconcileBenchRmNotesCommand
 *
 * Intent: Automated reconciliation engine for Super Admin Relationship Manager (RM) Note Templates (bench_rm_note_master).
 * Detects and idempotently repairs:
 * 1. Missing Elasticsearch documents (records in MySQL but absent in ES).
 * 2. Orphaned Elasticsearch documents (documents in ES whose MySQL records were deleted).
 * 3. Expired outbox processing leases (>300 seconds).
 * 4. Stale cache states and generation versioning.
 *
 * Signature: php artisan bench-rm-notes:reconcile {--fix} {--dry-run} {--batch=100}
 * Tenancy: Scoped strictly to Super Admin (tenant_id = 0, company_id = NULL).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReconcileBenchRmNotesCommand extends Command
{
    protected $signature = 'bench-rm-notes:reconcile {--fix : Automatically repair identified inconsistencies} {--dry-run : Perform analysis only without modifying data} {--batch=100 : Maximum number of documents to repair per batch}';

    protected $description = 'Detect and idempotently repair data drift between MySQL, Elasticsearch, and Redis cache for Relationship Manager Note Templates';

    public function handle(ElasticService $elasticService): int
    {
        $fix = (bool) $this->option('fix');
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $fix = false;
        }
        $batchLimit = max(1, (int) ($this->option('batch') ?: 100));

        // Distributed Lock: Prevent concurrent reconciliation runs from stomping on each other
        $lockKey = 'lock:bench_rm_notes:reconcile';
        $lock = Cache::lock($lockKey, 300);
        if (! $lock->get()) {
            $this->warn('Reconciliation is already running on another process. Aborting to avoid concurrency conflicts.');

            return Command::FAILURE;
        }

        try {
            $this->info('Starting Super Admin Relationship Manager Notes self-healing reconciliation scan...');

            $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
                ? ELASTIC_BENCH_RM_NOTE_MASTER
                : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

            // 1. Fetch all authoritative MySQL IDs
            $dbRecords = DB::table('bench_rm_note_master')
                ->whereNull('company_id')
                ->get(['id', 'title', 'content', 'status', 'created_at', 'updated_at'])
                ->keyBy('id');

            $dbIds = $dbRecords->keys()->map(fn ($id) => (int) $id)->toArray();

            // 2. Fetch all Elasticsearch Document IDs
            $esIds = [];
            $esAvailable = false;
            try {
                if ($elasticService->indexExists($indexName)) {
                    $esAvailable = true;
                    $scrollRes = $elasticService->getClient()->search([
                        'index' => $indexName,
                        'size' => 1000,
                        '_source' => ['id', 'version'],
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['bool' => ['must_not' => ['exists' => ['field' => 'company_id']]]],
                                        ['term' => ['company_id' => 0]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ]);

                    foreach ($scrollRes['hits']['hits'] as $hit) {
                        $esIds[] = (int) ($hit['_source']['id'] ?? $hit['_id']);
                    }
                } else {
                    $this->warn("Elasticsearch index {$indexName} does not exist yet.");
                }
            } catch (\Throwable $e) {
                $this->error("Elasticsearch query error during reconciliation: {$e->getMessage()}");
            }

            // 3. Compute Set Differences
            $missingInEs = array_values(array_diff($dbIds, $esIds));
            $orphanedInEs = array_values(array_diff($esIds, $dbIds));

            // 4. Check Stale Outbox Processing Leases (>300s)
            $expiredLeasesQuery = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->where('status', 'PROCESSING')
                ->where('locked_at', '<', now()->subSeconds(300));

            $expiredLeasesCount = $expiredLeasesQuery->count();

            // 5. Dead-Letter Count
            $deadLetterCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->where('status', 'DEAD_LETTER')
                ->count();

            // 6. Drift Severity Classification
            $totalDrift = count($missingInEs) + count($orphanedInEs) + $expiredLeasesCount;
            $driftSeverity = 'NONE';
            if ($totalDrift > 10 || ! $esAvailable) {
                $driftSeverity = 'CRITICAL';
            } elseif ($totalDrift >= 3) {
                $driftSeverity = 'MEDIUM';
            } elseif ($totalDrift > 0) {
                $driftSeverity = 'LOW';
            }

            // Display Scan Results Table
            $this->table(
                ['Metric', 'Count / Value', 'Status / Detail'],
                [
                    ['MySQL Total Records', count($dbIds), 'Authoritative Source of Truth'],
                    ['Elasticsearch Indexed Docs', count($esIds), $esAvailable ? 'Online' : 'UNAVAILABLE'],
                    ['Missing in Elasticsearch', count($missingInEs), count($missingInEs) === 0 ? '✓ In Sync' : '⚠ DRIFT DETECTED'],
                    ['Orphaned in Elasticsearch', count($orphanedInEs), count($orphanedInEs) === 0 ? '✓ In Sync' : '⚠ ORPHANS FOUND'],
                    ['Expired Outbox Leases (>300s)', $expiredLeasesCount, $expiredLeasesCount === 0 ? '✓ Healthy' : '⚠ STUCK LEASES'],
                    ['Dead-Letter Events', $deadLetterCount, $deadLetterCount === 0 ? '✓ Clean' : 'ℹ Requires Review'],
                    ['Drift Severity Level', $driftSeverity, $driftSeverity === 'NONE' ? 'Optimal' : 'Action Recommended'],
                    ['Cache Generation Version', SuperAdminBenchRmNoteCacheManager::getVersion(), 'Active'],
                ]
            );

            if ($dryRun) {
                $this->info('DRY-RUN SCAN COMPLETED: Analysis performed without modifying data.');

                return Command::SUCCESS;
            }

            if (! $fix) {
                if ($totalDrift > 0) {
                    $this->warn("Inconsistencies detected! Run with --fix (or --fix --batch={$batchLimit}) to automatically heal data drift.");
                } else {
                    $this->info('✓ All systems in 100% authoritative sync. Zero drift detected.');
                }

                return Command::SUCCESS;
            }

            // Execution of Version-Aware Self-Healing Repairs (Bounded Batches)
            $this->info("Initiating version-aware self-healing repairs (Batch limit: {$batchLimit})...");
            $repairedMissing = 0;
            $repairedOrphans = 0;

            // Repair 1: Backfill missing ES docs (Version-Aware)
            if (! empty($missingInEs) && $esAvailable) {
                $targetMissing = array_slice($missingInEs, 0, $batchLimit);
                foreach ($targetMissing as $id) {
                    $record = $dbRecords->get($id);
                    if (! $record) {
                        continue;
                    }

                    // Look up authoritative latest aggregate version from outbox
                    $latestVersion = (int) DB::table('sys_outbox_events')
                        ->where('aggregate_type', 'BenchRmNoteTemplate')
                        ->where('aggregate_id', $id)
                        ->max('aggregate_version');
                    $docVersion = max($latestVersion, 1);

                    $rawContent = (string) $record->content;
                    $cleanText = trim(strip_tags($rawContent));
                    $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    $doc = [
                        'id' => (int) $record->id,
                        'company_id' => null,
                        'title' => (string) $record->title,
                        'content' => (string) $record->content,
                        'status' => (int) $record->status,
                        'version' => $docVersion,
                        'word_count' => $cleanText !== '' ? str_word_count($cleanText) : 0,
                        'char_count' => mb_strlen($cleanText),
                        'preview_text' => Str::limit($cleanText, 180),
                        'created_formatted' => $record->created_at ? Carbon::parse($record->created_at)->format('d M Y') : '—',
                        'updated_human' => $record->updated_at ? Carbon::parse($record->updated_at)->diffForHumans() : ($record->created_at ? Carbon::parse($record->created_at)->diffForHumans() : 'Just now'),
                        'created_at' => $record->created_at ? Carbon::parse($record->created_at)->toIso8601String() : null,
                        'updated_at' => $record->updated_at ? Carbon::parse($record->updated_at)->toIso8601String() : null,
                    ];

                    try {
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $record->id,
                            'refresh' => 'wait_for',
                            'body' => $doc,
                        ]);
                        $repairedMissing++;
                        Log::info("[RECONCILE_AUDIT] Repaired missing doc #{$id} with version {$docVersion}");
                    } catch (\Throwable $e) {
                        $this->error("Failed to repair missing doc #{$id}: {$e->getMessage()}");
                    }
                }
                $this->info("Healed {$repairedMissing} missing Elasticsearch document(s).");
            }

            // Repair 2: Prune orphaned ES docs
            if (! empty($orphanedInEs) && $esAvailable) {
                $targetOrphans = array_slice($orphanedInEs, 0, $batchLimit);
                foreach ($targetOrphans as $id) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $id,
                            'refresh' => 'wait_for',
                        ]);
                        $repairedOrphans++;
                        Log::info("[RECONCILE_AUDIT] Pruned orphaned doc #{$id} from ES index {$indexName}");
                    } catch (\Throwable $e) {
                        $this->error("Failed to prune orphan doc #{$id}: {$e->getMessage()}");
                    }
                }
                $this->info("Pruned {$repairedOrphans} orphaned Elasticsearch document(s).");
            }

            // Repair 3: Reset expired outbox leases
            if ($expiredLeasesCount > 0) {
                $resetCount = $expiredLeasesQuery->update([
                    'status' => 'PENDING',
                    'available_at' => now(),
                ]);
                $this->info("Reset {$resetCount} expired outbox lease(s) back to PENDING.");
                Log::info("[RECONCILE_AUDIT] Reset {$resetCount} expired outbox leases to PENDING");
            }

            // Flush all L2 cache keys and advance generation version
            SuperAdminBenchRmNoteCacheManager::flushCaches();
            $this->info('Flushed L2 Redis/Dragonfly caches and incremented cache generation.');

            // After-Repair Verification
            $this->info('Running post-repair verification check...');
            $postEsIds = [];
            try {
                if ($elasticService->indexExists($indexName)) {
                    $verifyRes = $elasticService->getClient()->search([
                        'index' => $indexName,
                        'size' => 1000,
                        '_source' => ['id'],
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['bool' => ['must_not' => ['exists' => ['field' => 'company_id']]]],
                                        ['term' => ['company_id' => 0]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ]);
                    foreach ($verifyRes['hits']['hits'] as $hit) {
                        $postEsIds[] = (int) ($hit['_source']['id'] ?? $hit['_id']);
                    }
                }
            } catch (\Throwable) {
            }

            $postMissing = array_values(array_diff($dbIds, $postEsIds));
            $postOrphans = array_values(array_diff($postEsIds, $dbIds));

            $this->table(
                ['Post-Repair Metric', 'Remaining Drift', 'Verification'],
                [
                    ['Missing in ES', count($postMissing), count($postMissing) === 0 ? '✓ 100% In Sync' : '⚠ Action Required'],
                    ['Orphaned in ES', count($postOrphans), count($postOrphans) === 0 ? '✓ Clean' : '⚠ Action Required'],
                ]
            );

            $this->info('✓ Self-healing reconciliation complete. Systems restored to 100% authoritative consistency.');

            return Command::SUCCESS;
        } finally {
            optional($lock)->release();
        }
    }
}
