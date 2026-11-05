<?php

namespace App\Jobs;

use App\Services\AgencyCommercialTermsService;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminDefaultTermsCacheManager;
use App\Services\SuperAdmin\SuperAdminDefaultTermsReadService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessDefaultTermOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Default Terms subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_default_terms),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessDefaultTermOutboxEventJob implements ShouldQueue
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
        if (defined('ELASTIC_DEFAULT_TERMS')) {
            return ELASTIC_DEFAULT_TERMS;
        }

        return (string) config('constants.ELASTIC_DEFAULT_TERMS', 'local_default_terms');
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
            ->where('aggregate_type', 'DefaultTerm')
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
            $termId = (int) ($payload['term_id'] ?? $event->aggregate_id);
            $indexName = $this->getIndexName();

            // Ensure index exists
            if (! $elasticService->indexExists($indexName)) {
                $this->createIndexWithSchema($elasticService, $indexName);
            }

            // Read fresh data and build definition
            $readService = app(SuperAdminDefaultTermsReadService::class);
            $settings = DB::table('global_settings')->first() ?: (object) [];
            $agencyDefaults = ($termId === 13) ? app(AgencyCommercialTermsService::class)->getGlobalDefault() : null;
            $definitions = $readService->buildTermsDefinitions($settings, $agencyDefaults);

            if (isset($definitions[$termId])) {
                $def = $definitions[$termId];
                $rawContent = (string) $def['raw_content'];
                $stripped = trim(strip_tags($rawContent));
                $isConfigured = strlen($stripped) > 0;

                if ($termId === 13) {
                    $isConfigured = ! empty($agencyDefaults) && ! empty((array) $agencyDefaults);
                    $stripped = 'Full-Time Model: '.($agencyDefaults->fulltime_fee_model ?? 'percentage').' ('.($agencyDefaults->fulltime_success_fee_percentage ?? 8.33).'% CTC), Payment Days: '.($agencyDefaults->fulltime_payment_terms_days ?? 30).', Replacement: '.($agencyDefaults->fulltime_replacement_period_days ?? 90).' Days; Contract Model: '.($agencyDefaults->contract_billing_model ?? 'monthly').' ('.($agencyDefaults->contract_billing_cycle ?? 'Monthly in Advance').')';
                }

                $charCount = strlen($stripped);
                $wordCount = $charCount > 0 ? str_word_count($stripped) : 0;
                $excerpt = $isConfigured
                    ? mb_strimwidth(preg_replace('/\s+/', ' ', $stripped), 0, 140, '...')
                    : (! empty($def['fallback_to'])
                        ? 'No custom template configured. Automatically inherits '.$def['fallback_to'].' as starter copy.'
                        : 'No content configured yet. Click Edit to establish master policy.');

                $doc = [
                    'id' => (int) $termId,
                    'encrypted_id' => encrypt($termId),
                    'seq_id' => sprintf('#TERM-%03d', $termId),
                    'title' => $def['title'],
                    'audience' => $def['audience'],
                    'audience_key' => $def['audience_key'],
                    'category' => $def['category'],
                    'category_label' => $def['category_label'],
                    'icon' => $def['icon'],
                    'db_field' => $def['db_field'],
                    'storage' => $def['storage'],
                    'raw_content' => $rawContent,
                    'clean_text' => $stripped,
                    'is_configured' => $isConfigured,
                    'char_count' => $charCount,
                    'word_count' => $wordCount,
                    'excerpt' => $excerpt,
                    'fallback_to' => $def['fallback_to'],
                    'tokens' => $def['tokens'],
                    'scope_note' => $def['scope_note'],
                    'public_route' => $def['public_route'],
                    'updated_at' => Carbon::now()->toIso8601String(),
                ];

                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => (string) $termId,
                    'refresh' => 'wait_for',
                    'body' => $doc,
                ]);
            }

            // Atomic cache eviction
            SuperAdminDefaultTermsCacheManager::flushCaches();

            // Mark event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error('ProcessDefaultTermOutboxEventJob failed for event #'.$eventId.': '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) $event->attempts + 1;
            $newStatus = ($attempts >= $this->tries) ? 'FAILED' : 'PENDING';
            $nextAvailable = now()->addSeconds($this->backoff * $attempts);

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
     * Create Elasticsearch index schema if not already initialized.
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
                        'seq_id' => ['type' => 'keyword'],
                        'encrypted_id' => ['type' => 'keyword'],
                        'title' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                            ],
                        ],
                        'audience' => ['type' => 'keyword'],
                        'audience_key' => ['type' => 'keyword'],
                        'category' => ['type' => 'keyword'],
                        'category_label' => ['type' => 'keyword'],
                        'icon' => ['type' => 'keyword'],
                        'db_field' => ['type' => 'keyword'],
                        'storage' => ['type' => 'keyword'],
                        'raw_content' => ['type' => 'text'],
                        'clean_text' => ['type' => 'text'],
                        'is_configured' => ['type' => 'boolean'],
                        'char_count' => ['type' => 'integer'],
                        'word_count' => ['type' => 'integer'],
                        'excerpt' => ['type' => 'text'],
                        'fallback_to' => ['type' => 'keyword'],
                        'tokens' => ['type' => 'keyword'],
                        'scope_note' => ['type' => 'text'],
                        'public_route' => ['type' => 'keyword'],
                        'updated_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);
    }
}
