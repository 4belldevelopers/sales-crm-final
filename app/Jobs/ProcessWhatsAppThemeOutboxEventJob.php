<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\WhatsAppThemeCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessWhatsAppThemeOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the WhatsApp Themes subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_email_template),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessWhatsAppThemeOutboxEventJob implements ShouldQueue
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
            ->where('aggregate_type', 'WhatsAppTheme')
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

            $indexName = defined('ELASTIC_WHATSAPP_THEMES')
                ? ELASTIC_WHATSAPP_THEMES
                : (defined('ELASTIC_EMAIL_TEMPLATE') ? ELASTIC_EMAIL_TEMPLATE : 'local_email_template');

            if ($eventType === 'WHATSAPP_THEME_DELETED') {
                if ($elasticService->indexExists($indexName)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $themeId,
                            'refresh' => 'wait_for',
                        ]);
                    } catch (\Throwable $deleteEx) {
                        Log::warning("Could not delete WhatsApp theme #{$themeId} from ES: ".$deleteEx->getMessage());
                    }
                }
            } elseif ($eventType === 'WHATSAPP_THEME_STATUS_TOGGLED') {
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
            } else {
                // WHATSAPP_THEME_CREATED or WHATSAPP_THEME_UPDATED
                $themeRow = DB::table('th_email_template')
                    ->where('e_temp_id', $themeId)
                    ->where('temptype', 9)
                    ->first();

                if ($themeRow) {
                    $doc = [
                        'id' => (int) $themeRow->e_temp_id,
                        'e_temp_id' => (int) $themeRow->e_temp_id,
                        'template_name' => (string) $themeRow->template_name,
                        'display_name' => (string) $themeRow->display_name,
                        'template_subject' => (string) ($themeRow->template_subject ?? ''),
                        'template_description' => (string) $themeRow->template_description,
                        'status' => (int) $themeRow->status,
                        'orderByShow' => ! is_null($themeRow->orderByShow) && $themeRow->orderByShow !== '' ? (int) $themeRow->orderByShow : null,
                        'tempForInterview' => (int) ($themeRow->tempForInterview ?? 1),
                        'client_required' => (int) ($themeRow->client_required ?? 0),
                        'temptype' => 9,
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
            WhatsAppThemeCacheManager::flushCaches();

            // Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } catch (\Throwable $e) {
            Log::error("Failed to process WhatsApp Theme outbox event #{$eventId}: ".$e->getMessage(), [
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
