<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSmtpSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminSmtpSettingReadService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ProcessSmtpSettingOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin SMTP Settings subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_smtp_settings),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSmtpSettingOutboxEventJob implements ShouldQueue
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

    /**
     * Resolve target Elasticsearch index name.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_SMTP_SETTINGS')) {
            return ELASTIC_SMTP_SETTINGS;
        }

        return (string) config('constants.ELASTIC_SMTP_SETTINGS', 'local_smtp_settings');
    }

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'SmtpSetting')
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
            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $gatewayIds = (array) ($payload['gateway_ids'] ?? [$event->aggregate_id]);
            $indexName = $this->getIndexName();

            // Ensure index exists
            if (! $elasticService->indexExists($indexName)) {
                $this->createIndexWithSchema($elasticService, $indexName);
            }

            $readService = app(SuperAdminSmtpSettingReadService::class);
            $records = DB::table('smtp_settings')
                ->whereIn('id', $gatewayIds)
                ->get();

            $bulkBody = [];
            foreach ($records as $record) {
                $doc = $readService->buildGatewayDocument($record);

                $bulkBody[] = [
                    'index' => [
                        '_index' => $indexName,
                        '_id' => (string) $record->id,
                    ],
                ];
                $bulkBody[] = $doc;
            }

            if (! empty($bulkBody)) {
                $elasticService->getClient()->bulk([
                    'body' => $bulkBody,
                    'refresh' => 'wait_for',
                ]);
            }

            // Invalidate L2 caches
            SuperAdminSmtpSettingCacheManager::flushCaches();

            // Mark completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessSmtpSettingOutboxEventJob: Event #{$eventId} processed successfully for gateways: ".implode(',', $gatewayIds));
        } catch (Throwable $e) {
            Log::error("ProcessSmtpSettingOutboxEventJob: Event #{$eventId} failed: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) $event->attempts + 1;
            $newStatus = $attempts >= $this->tries ? 'FAILED' : 'PENDING';
            $nextAvailable = Carbon::now()->addSeconds($this->backoff * $attempts);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => $nextAvailable,
                ]);

            if ($newStatus === 'FAILED') {
                throw $e;
            }
        }
    }

    /**
     * Create Elasticsearch index with optimal mappings.
     */
    protected function createIndexWithSchema(ElasticService $elasticService, string $indexName): void
    {
        $elasticService->getClient()->indices()->create([
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'index.requests.cache.enable' => true,
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'mail_driver' => ['type' => 'keyword'],
                        'mail_host' => [
                            'type' => 'keyword',
                            'fields' => ['text' => ['type' => 'text']],
                        ],
                        'mail_port' => ['type' => 'integer'],
                        'mail_username' => [
                            'type' => 'keyword',
                            'fields' => ['text' => ['type' => 'text']],
                        ],
                        'mail_password' => ['type' => 'keyword'],
                        'mail_from_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                        ],
                        'mail_from_email' => [
                            'type' => 'keyword',
                            'fields' => ['text' => ['type' => 'text']],
                        ],
                        'mail_encryption' => ['type' => 'keyword'],
                        'notification_mail_host' => ['type' => 'keyword'],
                        'notification_mail_port' => ['type' => 'integer'],
                        'notification_mail_username' => ['type' => 'keyword'],
                        'notification_mail_password' => ['type' => 'keyword'],
                        'notification_mail_encryption' => ['type' => 'keyword'],
                        'mass_mail_host' => ['type' => 'keyword'],
                        'mass_mail_port' => ['type' => 'keyword'],
                        'mass_mail_username' => ['type' => 'keyword'],
                        'mass_mail_password' => ['type' => 'keyword'],
                        'mass_mail_encryption' => ['type' => 'keyword'],
                        'email_id' => [
                            'type' => 'keyword',
                            'fields' => ['text' => ['type' => 'text']],
                        ],
                        'contact_no' => ['type' => 'keyword'],
                        'website' => ['type' => 'keyword'],
                        'status' => ['type' => 'integer'],
                        'is_active' => ['type' => 'boolean'],
                        'dailyCount' => ['type' => 'integer'],
                        'dailyDate' => ['type' => 'keyword'],
                        'verified' => ['type' => 'boolean'],
                        'mailtype' => ['type' => 'text'],
                        'gateway_title' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                        ],
                        'gateway_badge' => ['type' => 'keyword'],
                        'service_file' => ['type' => 'keyword'],
                        'purpose_desc' => ['type' => 'text'],
                        'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                        'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                    ],
                ],
            ],
        ]);
    }
}
