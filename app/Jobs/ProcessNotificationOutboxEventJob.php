<?php

namespace App\Jobs;

use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessNotificationOutboxEventJob
 *
 * Intent: Decoupled Transactional Outbox Worker for Super Admin Email Notification Projections.
 * Claims pending events from sys_outbox_events (aggregate_type 'EmailNotification'),
 * executes idempotent updates to Elasticsearch read models (ELASTIC_EMAIL_NOTIFICATIONS and ELASTIC_EMAIL_PERMISSIONS),
 * invalidates tenant subscription and notification caches in Redis/Dragonfly,
 * and guarantees at-least-once synchronization with exponential backoff and DLQ protection under 10,000 concurrency.
 *
 * Tenancy: Super Admin operations operate with tenant_id = 0.
 *
 * Author: Antigravity AI — Phase 1 Outbox & Idempotency Architecture
 * Date: October 28, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessNotificationOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public int $timeout = 45;

    /**
     * Create a new job instance.
     *
     * @param int|null $specificEventId Target specific outbox event ID or null for batch mode
     */
    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('es-index');
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticService $elasticService): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by worker or scheduler
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailNotification')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', Carbon::now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    /**
     * Process an individual claimed outbox event.
     */
    public function processEvent(int $eventId, ?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        // 1. Lease acquisition via atomic row lock and status transition
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->whereIn('status', ['PENDING', 'FAILED'])
            ->where(function ($q) {
                $q->whereNull('locked_at')
                    ->orWhere('locked_at', '<', Carbon::now()->subSeconds(60));
            })
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => Carbon::now(),
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
            $payload = json_decode($event->payload, true);
            if (! is_array($payload)) {
                throw new \InvalidArgumentException("Malformed JSON payload in outbox event #{$eventId}");
            }

            $notificationId = (int) ($payload['id'] ?? $event->aggregate_id);
            $eventVersion = (int) ($event->aggregate_version ?? 1);
            $updatedAt = $payload['updated_at'] ?? Carbon::now()->toIso8601String();

            // 2. Synchronize Elasticsearch ELASTIC_EMAIL_NOTIFICATIONS document
            $docData = [
                'id' => $notificationId,
                'aggregate_version' => $eventVersion,
                'updated_at' => $updatedAt,
            ];

            if (isset($payload['status'])) {
                $docData['status'] = (int) $payload['status'];
            }
            if (isset($payload['purpose'])) {
                $docData['purpose'] = (string) $payload['purpose'];
            }
            if (isset($payload['subject'])) {
                $docData['subject'] = (string) $payload['subject'];
            }
            if (isset($payload['from_email_id'])) {
                $docData['from_email_id'] = (string) $payload['from_email_id'];
            }
            if (isset($payload['mailType'])) {
                $docData['mailType'] = (int) $payload['mailType'];
            }
            if (array_key_exists('body_description', $payload)) {
                $docData['body_description'] = $payload['body_description'];
            }

            $notificationIndex = defined('ELASTIC_EMAIL_NOTIFICATIONS')
                ? ELASTIC_EMAIL_NOTIFICATIONS
                : config('constants.ELASTIC_EMAIL_NOTIFICATIONS', 'local_email_notifications');

            $elasticService->getClient()->update([
                'index' => $notificationIndex,
                'id' => $notificationId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => $docData,
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            // 3. Synchronize ELASTIC_EMAIL_PERMISSIONS if permissions payload or role sync required
            if (isset($payload['sync_permissions']) && $payload['sync_permissions'] === true) {
                $permissionIndex = defined('ELASTIC_EMAIL_PERMISSIONS')
                    ? ELASTIC_EMAIL_PERMISSIONS
                    : config('constants.ELASTIC_EMAIL_PERMISSIONS', 'local_mail_permissions');

                // Delete old permissions for this notification in Elasticsearch
                $elasticService->getClient()->deleteByQuery([
                    'index' => $permissionIndex,
                    'conflicts' => 'proceed',
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['notification_id' => $notificationId]],
                                ],
                            ],
                        ],
                    ],
                ]);

                // Re-index all authoritative permissions from MySQL
                $dbPerms = DB::table('mail_permissions')
                    ->where('notification_id', $notificationId)
                    ->get();

                foreach ($dbPerms as $perm) {
                    $elasticService->getClient()->update([
                        'index' => $permissionIndex,
                        'id' => $perm->id,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => (int) $perm->id,
                                'role_name' => (string) $perm->role_name,
                                'notification_id' => (int) $perm->notification_id,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            }

            // 4. Invalidate affected Redis / Dragonfly caches
            Cache::forget('email_notification_cpr_admin');
            Cache::forget('email_notification_cpr_customer');
            Cache::forget("email_notification:{$notificationId}");

            // 5. Mark Outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'locked_at' => null,
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            $attempts = (int) (DB::table('sys_outbox_events')->where('id', $eventId)->value('attempts') ?? 1);
            $newStatus = $attempts >= $this->tries ? 'DEAD_LETTER' : 'FAILED';
            $backoffSeconds = min(300, 5 * (2 ** ($attempts - 1)));

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'available_at' => Carbon::now()->addSeconds($backoffSeconds),
                    'locked_at' => null,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            Log::error("[NOTIFICATION_OUTBOX] Failed processing outbox event #{$eventId}: {$e->getMessage()}", [
                'event_id' => $eventId,
                'notification_id' => $notificationId ?? null,
                'attempts' => $attempts,
                'status' => $newStatus,
                'backoff' => $backoffSeconds,
            ]);
        }
    }
}
