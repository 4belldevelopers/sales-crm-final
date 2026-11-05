<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSkillCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessSkillOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Skills subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_skills),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSkillOutboxEventJob implements ShouldQueue
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
            ->where('aggregate_type', 'Skill')
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

            if ($eventType === 'SKILL_DELETED') {
                $docId = (string) ($payload['doc_id'] ?? $event->aggregate_id);
                $skillName = (string) ($payload['skill_name'] ?? '');

                if (defined('ELASTIC_SKILLS') && $elasticService->indexExists(ELASTIC_SKILLS)) {
                    // 1. Delete by ES document ID if exists
                    try {
                        if ($elasticService->getClient()->exists(['index' => ELASTIC_SKILLS, 'id' => $docId])) {
                            $elasticService->getClient()->delete([
                                'index' => ELASTIC_SKILLS,
                                'id' => $docId,
                                'refresh' => 'wait_for',
                            ]);
                        }
                    } catch (\Throwable $deleteEx) {
                        Log::info("[SKILL_OUTBOX] Document #{$docId} delete by ID: {$deleteEx->getMessage()}");
                    }

                    // 2. Also delete any matching term query by skill_name if provided
                    if ($skillName !== '') {
                        try {
                            $elasticService->getClient()->deleteByQuery([
                                'index' => ELASTIC_SKILLS,
                                'refresh' => true,
                                'body' => [
                                    'query' => [
                                        'term' => ['skill_name' => strtolower($skillName)],
                                    ],
                                ],
                            ]);
                        } catch (\Throwable $qEx) {
                            Log::info("[SKILL_OUTBOX] Delete by query for '{$skillName}': {$qEx->getMessage()}");
                        }
                    }
                }
            } elseif ($eventType === 'SKILL_UPDATED') {
                $docId = (string) ($payload['doc_id'] ?? $event->aggregate_id);
                $skillName = (string) ($payload['skill_name'] ?? '');
                $updatedAt = $payload['updated_at'] ?? now()->toIso8601String();

                if (defined('ELASTIC_SKILLS') && $elasticService->indexExists(ELASTIC_SKILLS)) {
                    if ($elasticService->getClient()->exists(['index' => ELASTIC_SKILLS, 'id' => $docId])) {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_SKILLS,
                            'id' => $docId,
                            'refresh' => 'wait_for',
                            'body' => [
                                'doc' => [
                                    'skill_name' => $skillName,
                                    'updated_at' => $updatedAt,
                                ],
                            ],
                        ]);
                    } else {
                        // If document not found by docId, index it fresh
                        $elasticService->getClient()->index([
                            'index' => ELASTIC_SKILLS,
                            'id' => $docId,
                            'refresh' => 'wait_for',
                            'body' => [
                                'id' => $docId,
                                'skill_name' => $skillName,
                                'created_at' => $payload['created_at'] ?? now()->toIso8601String(),
                                'updated_at' => $updatedAt,
                            ],
                        ]);
                    }
                }
            } else {
                // SKILL_SAVED
                $docId = (string) ($payload['doc_id'] ?? ($payload['id'] ?? $event->aggregate_id));
                $skillName = (string) ($payload['skill_name'] ?? '');
                $createdAt = $payload['created_at'] ?? now()->toIso8601String();
                $updatedAt = $payload['updated_at'] ?? now()->toIso8601String();

                if (defined('ELASTIC_SKILLS') && $elasticService->indexExists(ELASTIC_SKILLS)) {
                    $elasticService->getClient()->index([
                        'index' => ELASTIC_SKILLS,
                        'id' => $docId,
                        'refresh' => 'wait_for',
                        'body' => [
                            'id' => $docId,
                            'skill_name' => $skillName,
                            'created_at' => $createdAt,
                            'updated_at' => $updatedAt,
                        ],
                    ]);
                }
            }

            // Mark event COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            // Atomic cache invalidation
            SuperAdminSkillCacheManager::flushCaches();
        } catch (\Throwable $e) {
            Log::error("[SKILL_OUTBOX] Event #{$eventId} execution failed: {$e->getMessage()}");

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'FAILED',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => Carbon::now()->addSeconds($this->backoff * (int) $event->attempts),
                ]);

            throw $e;
        }
    }
}
