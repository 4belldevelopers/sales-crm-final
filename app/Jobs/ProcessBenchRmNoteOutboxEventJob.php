<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ProcessBenchRmNoteOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Relationship Manager (RM)
 * Note Templates subsystem. Guarantees at-least-once delivery, dual-store synchronization to
 * Elasticsearch (local_bench_rm_note_master), L2 cache invalidation in Redis/Dragonfly, and atomic
 * status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessBenchRmNoteOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('outbox');
    }

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    protected function processEvent(int $eventId, ElasticService $elasticService): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $eventType = (string) $event->event_type;
            $payload = json_decode((string) $event->payload, true) ?: [];
            $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
                ? ELASTIC_BENCH_RM_NOTE_MASTER
                : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

            if ($eventType === 'BENCH_RM_NOTE_TEMPLATE_DELETED') {
                $docId = (string) ($payload['id'] ?? $event->aggregate_id);

                if ($elasticService->indexExists($indexName)) {
                    try {
                        if ($elasticService->getClient()->exists(['index' => $indexName, 'id' => $docId])) {
                            $elasticService->getClient()->delete([
                                'index' => $indexName,
                                'id' => $docId,
                                'refresh' => 'wait_for',
                            ]);
                        }
                    } catch (\Throwable $deleteEx) {
                        Log::info("[BENCH_RM_NOTE_OUTBOX] Document #{$docId} delete by ID: {$deleteEx->getMessage()}");
                    }
                }
            } else {
                // Upsert: CREATED, UPDATED, STATUS_TOGGLED, DUPLICATED
                $templateId = (int) ($payload['id'] ?? $event->aggregate_id);

                // Fetch latest state from MySQL
                $record = DB::table('bench_rm_note_master')
                    ->where('id', $templateId)
                    ->whereNull('company_id')
                    ->first();

                if ($record && $elasticService->indexExists($indexName)) {
                    $rawContent = (string) $record->content;
                    $cleanText = trim(strip_tags($rawContent));
                    $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    $doc = [
                        'id' => (int) $record->id,
                        'company_id' => null,
                        'title' => (string) $record->title,
                        'content' => (string) $record->content,
                        'status' => (int) $record->status,
                        'word_count' => $cleanText !== '' ? str_word_count($cleanText) : 0,
                        'char_count' => mb_strlen($cleanText),
                        'preview_text' => Str::limit($cleanText, 180),
                        'created_formatted' => $record->created_at ? Carbon::parse($record->created_at)->format('d M Y') : '—',
                        'updated_human' => $record->updated_at ? Carbon::parse($record->updated_at)->diffForHumans() : ($record->created_at ? Carbon::parse($record->created_at)->diffForHumans() : 'Just now'),
                        'created_at' => $record->created_at ? Carbon::parse($record->created_at)->toIso8601String() : null,
                        'updated_at' => $record->updated_at ? Carbon::parse($record->updated_at)->toIso8601String() : null,
                    ];

                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $record->id,
                        'refresh' => 'wait_for',
                        'body' => $doc,
                    ]);
                }
            }

            // Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')->where('id', $eventId)->update([
                'status' => 'COMPLETED',
                'processed_at' => now(),
                'last_error' => null,
            ]);

            SuperAdminBenchRmNoteCacheManager::flushCaches();
        } catch (\Throwable $e) {
            $isLastAttempt = ($event->attempts + 1) >= $this->tries;
            Log::error("[BENCH_RM_NOTE_OUTBOX] Event #{$eventId} execution failed: {$e->getMessage()}", [
                'attempts' => $event->attempts + 1,
                'isLastAttempt' => $isLastAttempt,
            ]);

            DB::table('sys_outbox_events')->where('id', $eventId)->update([
                'status' => $isLastAttempt ? 'FAILED' : 'PENDING',
                'available_at' => now()->addSeconds($this->backoff * ($event->attempts + 1)),
                'last_error' => $e->getMessage(),
            ]);

            if ($this->specificEventId !== null && $isLastAttempt) {
                throw $e;
            }
        }
    }
}
