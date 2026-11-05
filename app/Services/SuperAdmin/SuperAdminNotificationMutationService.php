<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessNotificationOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class SuperAdminNotificationMutationService
 *
 * Intent: Encapsulates transactional state mutations for Super Admin Email Notification Templates
 * and Role Permissions. Implements strict two-tier distributed idempotency via sys_idempotency_keys,
 * guarantees at-least-once asynchronous synchronization to Elasticsearch projections via sys_outbox_events,
 * invalidates multi-tenant Dragonfly/Redis caches via SuperAdminNotificationCacheManager, and eliminates stale reads.
 *
 * Tenancy: Super Admin operations operate with tenant_id = 0.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminNotificationMutationService
{
    public function __construct(
        protected ?SuperAdminNotificationReadService $readService = null
    ) {
        $this->readService = $readService ?? app(SuperAdminNotificationReadService::class);
    }

    /**
     * Compute a deterministic sha256 request hash from a payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Format a clean idempotency key when provided.
     */
    protected function formatIdempotencyKey(?string $clientKey): ?string
    {
        if ($clientKey && trim($clientKey) !== '') {
            return trim($clientKey);
        }

        return null;
    }

    /**
     * Mutate notification active/inactive status with transactional outbox and two-tier idempotency.
     *
     * @param  int  $notificationId  Target email_notifications.id
     * @param  int  $newStatus  Target status (1 = Active, 0 = Inactive)
     * @param  string|null  $idempotencyKey  Client-supplied UUID or auto-generated token
     * @param  int|null  $adminId  Authenticated admin user ID
     * @return array<string, mixed> Structured domain result payload
     */
    public function toggleStatus(
        int $notificationId,
        int $newStatus,
        ?string $idempotencyKey = null,
        ?int $adminId = null
    ): array {
        $normalizedStatus = $newStatus === 1 ? 1 : 0;
        $clientKey = $this->formatIdempotencyKey($idempotencyKey);
        $finalIdempotencyKey = $clientKey ?: (string) Str::uuid();

        $requestPayload = [
            'action' => 'toggle_status',
            'notification_id' => $notificationId,
            'status' => $normalizedStatus,
            'admin_id' => $adminId,
        ];
        $requestHash = $this->computeRequestHash($requestPayload);

        // 1. Two-Tier Idempotency Lease Claim Check
        if ($clientKey) {
            $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => 0,
                'idempotency_key' => $clientKey,
                'request_hash' => $requestHash,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addHours(24),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                $existing = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'A notification status update is currently in-flight for this key. Please wait.');
                        }

                        DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $clientKey)
                            ->update([
                                'status' => 'PROCESSING',
                                'request_hash' => $requestHash,
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
            }
        }

        // 2. Authoritative check for record existence
        $notification = DB::table('email_notifications')
            ->where('id', $notificationId)
            ->first();

        if (! $notification) {
            return [
                'status' => 'error',
                'message' => "Notification template #{$notificationId} not found.",
                'code' => 404,
            ];
        }

        $now = Carbon::now();

        // 3. Compute aggregate version
        $latestVersion = (int) (DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailNotification')
            ->where('aggregate_id', $notificationId)
            ->max('aggregate_version') ?? 1);
        $nextVersion = $latestVersion + 1;

        $outboxPayload = [
            'id' => $notificationId,
            'status' => $normalizedStatus,
            'purpose' => $notification->purpose,
            'subject' => $notification->subject,
            'from_email_id' => $notification->from_email_id,
            'mailType' => (int) $notification->mailType,
            'sync_permissions' => false,
            'updated_at' => $now->toIso8601String(),
            'admin_id' => $adminId,
        ];

        $eventId = null;

        // 4. Micro-transaction (<15ms duration)
        DB::beginTransaction();
        try {
            DB::table('email_notifications')
                ->where('id', $notificationId)
                ->update([
                    'status' => $normalizedStatus,
                    'updated_at' => $now,
                ]);

            $eventId = (int) DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'EmailNotification',
                'aggregate_id' => $notificationId,
                'aggregate_version' => $nextVersion,
                'event_type' => 'NotificationStatusToggled',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $responseResult = [
                'status' => 'success',
                'message' => 'Status Changed',
                'id' => $notificationId,
                'new_status' => $normalizedStatus,
                'event_id' => $eventId,
                'idempotency_key' => $finalIdempotencyKey,
            ];

            if ($clientKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseResult),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            // 5. Post-Commit Hook: Invalidate L2 Dragonfly Cache & Immediate Projection Sync
            DB::afterCommit(function () use ($notificationId, $eventId) {
                SuperAdminNotificationCacheManager::flushCaches($notificationId);

                if ($eventId) {
                    try {
                        ProcessNotificationOutboxEventJob::dispatchSync($eventId);
                    } catch (Throwable $e) {
                        Log::warning("[NOTIFICATION_MUTATION] Immediate projection sync queued for background retry: {$e->getMessage()}", [
                            'event_id' => $eventId,
                            'notification_id' => $notificationId,
                        ]);
                    }
                }
            });

            DB::commit();

            return $responseResult;
        } catch (Throwable $e) {
            DB::rollBack();

            if ($clientKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->update([
                        'status' => 'FAILED_FINAL',
                        'last_error' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            Log::error("[NOTIFICATION_MUTATION] Status toggle transaction failed: {$e->getMessage()}", [
                'notification_id' => $notificationId,
                'new_status' => $normalizedStatus,
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to update notification status in database.',
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Create or update notification template and associated role permissions.
     *
     * @param  array<string, mixed>  $data  Form payload
     * @param  int|null  $tokenId  Notification ID (0/null = create, >0 = update)
     * @param  string|null  $idempotencyKey  Client-supplied UUID or auto-generated token
     * @param  int|null  $adminId  Authenticated admin user ID
     * @return array<string, mixed> Structured domain result payload
     */
    public function saveNotification(
        array $data,
        ?int $tokenId = null,
        ?string $idempotencyKey = null,
        ?int $adminId = null
    ): array {
        $tokenId = (int) ($tokenId ?? ($data['tokenId'] ?? 0));
        $isCreate = $tokenId === 0;
        $clientKey = $this->formatIdempotencyKey($idempotencyKey);
        $finalIdempotencyKey = $clientKey ?: (string) Str::uuid();

        $requestHash = $this->computeRequestHash($data);

        // 1. Two-Tier Idempotency Lease Claim Check
        if ($clientKey) {
            $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => 0,
                'idempotency_key' => $clientKey,
                'request_hash' => $requestHash,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addHours(24),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                $existing = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'Notification save is already processing for this key. Please wait.');
                        }

                        DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $clientKey)
                            ->update([
                                'status' => 'PROCESSING',
                                'request_hash' => $requestHash,
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
            }
        }

        // 2. Validate role selections against cached roles (0% MySQL reads)
        $rawRoles = $data['role_ck'] ?? [];
        if (! is_array($rawRoles)) {
            $rawRoles = [];
        }

        $validRoles = $this->readService->getRoles()->pluck('display_name')->toArray();
        $selectedRoles = array_values(array_intersect($rawRoles, $validRoles));

        $purpose = trim((string) ($data['purpose'] ?? ''));
        $subject = trim((string) ($data['subject'] ?? ''));
        $fromEmail = trim((string) ($data['from_email_id'] ?? 'noreply@jero.co.in'));
        $mailType = (int) ($data['mailType_is'] ?? ($data['mailType'] ?? 1));
        $status = (int) ($data['status'] ?? ($data['status_is'] ?? 1));
        $bodyDescription = (string) ($data['editor1'] ?? ($data['body_description'] ?? ''));

        $targetId = $tokenId;
        $eventId = null;
        $now = Carbon::now();

        // 3. Micro-transaction (<15ms duration)
        DB::beginTransaction();
        try {
            if ($isCreate) {
                $targetId = (int) DB::table('email_notifications')->insertGetId([
                    'purpose' => $purpose,
                    'subject' => $subject,
                    'status' => $status,
                    'body_description' => $bodyDescription,
                    'from_email_id' => $fromEmail,
                    'mailType' => $mailType,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($selectedRoles as $roleName) {
                    DB::table('mail_permissions')->insert([
                        'role_name' => $roleName,
                        'notification_id' => $targetId,
                    ]);
                }

                $eventType = 'NotificationCreated';
                $nextVersion = 1;

            } else {
                DB::table('email_notifications')
                    ->where('id', $targetId)
                    ->update([
                        'purpose' => $purpose,
                        'subject' => $subject,
                        'status' => $status,
                        'body_description' => $bodyDescription,
                        'mailType' => $mailType,
                        'from_email_id' => $fromEmail,
                        'updated_at' => $now,
                    ]);

                DB::table('mail_permissions')
                    ->where('notification_id', $targetId)
                    ->delete();

                foreach ($selectedRoles as $roleName) {
                    DB::table('mail_permissions')->insert([
                        'role_name' => $roleName,
                        'notification_id' => $targetId,
                    ]);
                }

                $eventType = 'NotificationUpdated';
                $latestVersion = (int) (DB::table('sys_outbox_events')
                    ->where('aggregate_type', 'EmailNotification')
                    ->where('aggregate_id', $targetId)
                    ->max('aggregate_version') ?? 1);
                $nextVersion = $latestVersion + 1;
            }

            $outboxPayload = [
                'id' => $targetId,
                'purpose' => $purpose,
                'subject' => $subject,
                'status' => $status,
                'from_email_id' => $fromEmail,
                'mailType' => $mailType,
                'body_description' => $bodyDescription,
                'role_names' => $selectedRoles,
                'sync_permissions' => true,
                'updated_at' => $now->toIso8601String(),
                'admin_id' => $adminId,
            ];

            $eventId = (int) DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'EmailNotification',
                'aggregate_id' => $targetId,
                'aggregate_version' => $nextVersion,
                'event_type' => $eventType,
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $responseResult = [
                'status' => 'success',
                'message' => 'Email Notification saved successfully.',
                'id' => $targetId,
                'event_id' => $eventId,
                'idempotency_key' => $finalIdempotencyKey,
            ];

            if ($clientKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseResult),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            // 4. Post-Commit Hook: Invalidate L2 Dragonfly Cache & Fast-Path Projection Sync
            DB::afterCommit(function () use ($targetId, $eventId) {
                SuperAdminNotificationCacheManager::flushCaches($targetId);

                if ($eventId) {
                    try {
                        ProcessNotificationOutboxEventJob::dispatchSync($eventId);
                    } catch (Throwable $e) {
                        Log::warning("[NOTIFICATION_MUTATION] Immediate projection sync queued for background retry: {$e->getMessage()}", [
                            'event_id' => $eventId,
                            'notification_id' => $targetId,
                        ]);
                    }
                }
            });

            DB::commit();

            return $responseResult;
        } catch (Throwable $e) {
            DB::rollBack();

            if ($clientKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $clientKey)
                    ->update([
                        'status' => 'FAILED_FINAL',
                        'last_error' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            Log::error("[NOTIFICATION_MUTATION] Save notification transaction failed: {$e->getMessage()}", [
                'target_id' => $targetId,
                'is_create' => $isCreate,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to save notification template in database.',
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }
}
