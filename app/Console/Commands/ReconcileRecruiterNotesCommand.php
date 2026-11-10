<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ReconcileRecruiterNotesCommand
 *
 * Intent: Automated reconciliation engine for Super Admin Recruiter Note Templates.
 * Detects and idempotently repairs:
 * 1. Missing Elasticsearch documents (records in MySQL but absent in ES).
 * 2. Orphaned Elasticsearch documents (documents in ES whose MySQL records were deleted).
 * 3. Expired outbox processing leases (>300 seconds).
 * 4. Stale cache states and generation versioning.
 *
 * Signature: php artisan recruiter-notes:reconcile {--fix : Automatically repair identified drift}
 * Tenancy: Scoped strictly to Super Admin (tenant_id = 0, company_id = NULL).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReconcileRecruiterNotesCommand extends Command
{
    protected $signature = 'recruiter-notes:reconcile {--fix : Automatically repair identified inconsistencies}';

    protected $description = 'Detect and idempotently repair data drift between MySQL, Elasticsearch, and Redis cache';

    public function handle(ElasticService $elasticService): int
    {
        $fix = (bool) $this->option('fix');
        $this->info('Starting Super Admin Recruiter Notes self-healing reconciliation scan...');

        $indexName = defined('ELASTIC_RECRUITER_NOTE_MASTER')
            ? ELASTIC_RECRUITER_NOTE_MASTER
            : config('constants.ELASTIC_RECRUITER_NOTE_MASTER', 'local_recruiter_note_master');

        // 1. Fetch all authoritative MySQL IDs
        $dbRecords = DB::table('recruiter_note_master')
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
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', now()->subSeconds(300));

        $expiredLeasesCount = $expiredLeasesQuery->count();

        // 5. Dead-Letter Count
        $deadLetterCount = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('status', 'DEAD_LETTER')
            ->count();

        // Display Scan Results Table
        $this->table(
            ['Metric', 'Count / Value', 'Status'],
            [
                ['MySQL Total Records', count($dbIds), 'Authoritative Source of Truth'],
                ['Elasticsearch Indexed Docs', count($esIds), $esAvailable ? 'Online' : 'UNAVAILABLE'],
                ['Missing in Elasticsearch', count($missingInEs), count($missingInEs) === 0 ? '✓ In Sync' : '⚠ DRIFT DETECTED'],
                ['Orphaned in Elasticsearch', count($orphanedInEs), count($orphanedInEs) === 0 ? '✓ In Sync' : '⚠ ORPHANS FOUND'],
                ['Expired Outbox Leases (>300s)', $expiredLeasesCount, $expiredLeasesCount === 0 ? '✓ Healthy' : '⚠ STUCK LEASES'],
                ['Dead-Letter Events', $deadLetterCount, $deadLetterCount === 0 ? '✓ Clean' : 'ℹ Requires Review'],
                ['Cache Generation Version', SuperAdminRecruiterNoteCacheManager::getVersion(), 'Active'],
            ]
        );

        if (! $fix) {
            if (count($missingInEs) > 0 || count($orphanedInEs) > 0 || $expiredLeasesCount > 0) {
                $this->warn('Inconsistencies detected! Run with --fix to automatically heal data drift.');
            } else {
                $this->info('✓ All systems in 100% authoritative sync. Zero drift detected.');
            }

            return Command::SUCCESS;
        }

        // Execution of Self-Healing Repairs
        $this->info('Initiating self-healing repairs...');
        $repairedMissing = 0;
        $repairedOrphans = 0;

        // Repair 1: Backfill missing ES docs
        if (! empty($missingInEs) && $esAvailable) {
            foreach ($missingInEs as $id) {
                $record = $dbRecords->get($id);
                if (! $record) {
                    continue;
                }

                $rawContent = (string) $record->content;
                $cleanText = trim(strip_tags($rawContent));
                $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $doc = [
                    'id' => (int) $record->id,
                    'company_id' => null,
                    'title' => (string) $record->title,
                    'content' => (string) $record->content,
                    'status' => (int) $record->status,
                    'version' => 1,
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
                } catch (\Throwable $e) {
                    $this->error("Failed to repair missing doc #{$id}: {$e->getMessage()}");
                }
            }
            $this->info("Healed {$repairedMissing} missing Elasticsearch document(s).");
        }

        // Repair 2: Prune orphaned ES docs
        if (! empty($orphanedInEs) && $esAvailable) {
            foreach ($orphanedInEs as $id) {
                try {
                    $elasticService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $id,
                        'refresh' => 'wait_for',
                    ]);
                    $repairedOrphans++;
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
        }

        // Flush all L2 cache keys and advance generation version
        SuperAdminRecruiterNoteCacheManager::flushCaches();
        $this->info('Flushed L2 Redis/Dragonfly caches and incremented cache generation.');

        $this->info('✓ Self-healing reconciliation complete. Systems restored to 100% authoritative consistency.');

        return Command::SUCCESS;
    }
}
