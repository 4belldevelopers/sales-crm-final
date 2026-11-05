<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\EmailThemeCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessEmailThemeOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Email Themes subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_email_template),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessEmailThemeOutboxEventJob implements ShouldQueue
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
            ->where('aggregate_type', 'EmailTheme')
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
            $themeId = (int) $event->aggregate_id;
            $eventType = (string) $event->event_type;
            $payload = json_decode((string) $event->payload, true) ?: [];

            $indexName = defined('ELASTIC_EMAIL_TEMPLATE')
                ? ELASTIC_EMAIL_TEMPLATE
                : 'local_email_template';

            if ($eventType === 'EMAIL_THEME_DELETED') {
                if ($elasticService->indexExists($indexName)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $themeId,
                            'refresh' => 'wait_for',
                        ]);
                    } catch (\Throwable $deleteEx) {
                        Log::warning("Could not delete Email theme #{$themeId} from ES: ".$deleteEx->getMessage());
                    }
                }
            } elseif ($eventType === 'EMAIL_THEME_STATUS_TOGGLED') {
                $statusVal = (int) ($payload['status'] ?? 0);
                $currDate = now()->toIso8601String();

                if ($elasticService->indexExists($indexName)) {
                    $elasticService->getClient()->update([
                        'index' => $indexName,
                        'id' => (string) $themeId,
                        'retry_on_conflict' => 5,
                        'refresh' => 'wait_for',
                        'body' => [
                            'doc' => [
                                'status' => $statusVal,
                                'curr_date' => $currDate,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            } elseif ($eventType === 'EMAIL_THEME_CLIENT_REQUIRED_TOGGLED') {
                $clientRequiredVal = (int) ($payload['client_required'] ?? 0);
                $currDate = now()->toIso8601String();

                if ($elasticService->indexExists($indexName)) {
                    $elasticService->getClient()->update([
                        'index' => $indexName,
                        'id' => (string) $themeId,
                        'retry_on_conflict' => 5,
                        'refresh' => 'wait_for',
                        'body' => [
                            'doc' => [
                                'client_required' => $clientRequiredVal,
                                'curr_date' => $currDate,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            } elseif ($eventType === 'EMAIL_THEME_BULK_STATUS_UPDATED') {
                $ids = (array) ($payload['ids'] ?? []);
                $statusVal = (int) ($payload['status'] ?? 0);
                $currDate = now()->toIso8601String();

                if (! empty($ids) && $elasticService->indexExists($indexName)) {
                    $params = ['body' => []];
                    foreach ($ids as $id) {
                        $params['body'][] = [
                            'update' => [
                                '_index' => $indexName,
                                '_id' => (string) $id,
                                'retry_on_conflict' => 5,
                            ],
                        ];
                        $params['body'][] = [
                            'doc' => [
                                'status' => $statusVal,
                                'curr_date' => $currDate,
                            ],
                            'doc_as_upsert' => true,
                        ];
                    }
                    $params['refresh'] = 'wait_for';
                    $elasticService->getClient()->bulk($params);
                }
            } else {
                // EMAIL_THEME_CREATED or EMAIL_THEME_UPDATED
                $themeRow = DB::table('th_email_template')
                    ->where('e_temp_id', $themeId)
                    ->where('temptype', '!=', 9)
                    ->first();

                if ($themeRow) {
                    $doc = [
                        'id' => (int) $themeRow->e_temp_id,
                        'e_temp_id' => (int) $themeRow->e_temp_id,
                        'template_name' => (string) $themeRow->template_name,
                        'display_name' => (string) $themeRow->display_name,
                        'template_subject' => (string) ($themeRow->template_subject ?? ''),
                        'template_description' => (string) $themeRow->template_description,
                        'template_message' => (string) ($themeRow->template_message ?? ''),
                        'status' => (int) $themeRow->status,
                        'temptype' => (int) $themeRow->temptype,
                        'tempForInterview' => (int) ($themeRow->tempForInterview ?? 1),
                        'client_required' => (int) ($themeRow->client_required ?? 0),
                        'company_id' => ! is_null($themeRow->company_id) ? (int) $themeRow->company_id : null,
                        'orderByShow' => ! is_null($themeRow->orderByShow) && $themeRow->orderByShow !== '' ? (int) $themeRow->orderByShow : null,
                        'curr_date' => Carbon::parse($themeRow->curr_date)->toIso8601String(),
                    ];

                    if ($elasticService->indexExists($indexName)) {
                        $elasticService->getClient()->update([
                            'index' => $indexName,
                            'id' => (string) $themeRow->e_temp_id,
                            'retry_on_conflict' => 5,
                            'refresh' => 'wait_for',
                            'body' => [
                                'doc' => $doc,
                                'doc_as_upsert' => true,
                                'detect_noop' => false,
                            ],
                        ]);
                    }
                }
            }

            // Flush L2 Dragonfly / Redis caches atomically
            EmailThemeCacheManager::flushCaches();

            // Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } catch (\Throwable $e) {
            Log::error("Failed to process Email Theme outbox event #{$eventId}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $attempts = (int) ($event->attempts ?? 1);
            $newStatus = ($attempts >= $this->tries) ? 'FAILED' : 'PENDING';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'available_at' => now()->addSeconds($this->backoff * $attempts),
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            throw $e;
        }
    }
}
