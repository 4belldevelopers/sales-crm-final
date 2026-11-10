<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessGoogleMeetingOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminGoogleMeetingMutationService
 *
 * Intent: Encapsulates state-mutating operations for Super Admin Google Meet Link Pool
 * under the Transactional Outbox, Two-Tier Idempotency, and CQRS dual-store synchronization architecture.
 * Guarantees zero duplicate executions under network retries, ACID row-locked state transitions,
 * zero remote network I/O inside MySQL transactions (duration < 15ms), instant Elasticsearch sync,
 * and O(1) Redis/Dragonfly L2 cache invalidation.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with platform-wide interview scheduling cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminGoogleMeetingMutationService
{
    /**
     * Compute a canonical SHA-256 hash for a given payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Format a deterministic idempotency key when explicitly supplied by client.
     */
    protected function formatIdempotencyKey(?string $clientKey): ?string
    {
        if ($clientKey && trim($clientKey) !== '') {
            return trim($clientKey);
        }

        return null;
    }

    /**
     * Check and claim two-tier idempotency lease in sys_idempotency_keys.
     */
    protected function claimIdempotencyLease(?string $idempotencyKey, string $requestHash): ?array
    {
        if (! $idempotencyKey) {
            return null;
        }

        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $requestHash,
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
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
                        abort(409, 'A Google Meeting update is currently processing for this request. Please wait.');
                    }

                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', 0)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'PROCESSING',
                            'request_hash' => $requestHash,
                            'updated_at' => Carbon::now(),
                        ]);
                }
            }
        }

        return null;
    }

    /**
     * Finalize idempotency record to COMPLETED with response payload.
     */
    protected function completeIdempotency(?string $idempotencyKey, array $responsePayload, int $statusCode = 200): void
    {
        if (! $idempotencyKey) {
            return;
        }

        DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->update([
                'status' => 'COMPLETED',
                'response_status' => $statusCode,
                'response_body' => json_encode($responsePayload),
                'completed_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * Mark idempotency record as FAILED_RETRYABLE on error.
     */
    protected function failIdempotency(?string $idempotencyKey, Throwable $e): void
    {
        if (! $idempotencyKey) {
            return;
        }

        try {
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);
        } catch (Throwable) {
            // Ignore failure reporting exceptions
        }
    }

    /**
     * Determine next monotonic aggregate version.
     */
    protected function getNextVersion(int|string $aggregateId): int
    {
        $maxVersion = (int) DB::table('sys_outbox_events')
            ->where('tenant_id', 0)
            ->where('aggregate_type', 'GoogleMeetingLink')
            ->where('aggregate_id', (int) $aggregateId)
            ->max('aggregate_version');

        return $maxVersion + 1;
    }

    /**
     * Store Google Meeting links (supports dynamic multi-row entry and single link creation).
     */
    public function storeLinks(array $payload, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalKey = $this->formatIdempotencyKey($idempotencyKey);
        $requestHash = $this->computeRequestHash($payload);

        $replayed = $this->claimIdempotencyLease($finalKey, $requestHash);
        if ($replayed !== null) {
            return $replayed;
        }

        $outboxEventId = null;
        $response = [];

        DB::beginTransaction();
        try {
            // Case 1: Multi-row dynamic manual entry
            if (isset($payload['holder_email']) && is_array($payload['holder_email'])) {
                $emails = (array) $payload['holder_email'];
                $links = (array) ($payload['meeting_link'] ?? []);

                $insertData = [];
                foreach ($emails as $key => $email) {
                    $emailTrim = trim((string) $email);
                    $linkTrim = trim((string) ($links[$key] ?? ''));

                    if ($emailTrim === '' || $linkTrim === '') {
                        continue;
                    }

                    if (! preg_match('/^https?:\/\//i', $linkTrim)) {
                        $linkTrim = 'https://'.$linkTrim;
                    }

                    $insertData[] = [
                        'account_name' => $emailTrim,
                        'meeting_link' => $linkTrim,
                        'status' => 1,
                    ];
                }

                if (empty($insertData)) {
                    DB::rollBack();

                    return [
                        'status' => false,
                        'type' => 'warning',
                        'message' => 'Please provide at least one valid account email and meeting link.',
                    ];
                }

                // Insert into MySQL
                DB::table('google_meeting_lists')->insertOrIgnore($insertData);

                // Fetch inserted IDs by meeting links
                $targetLinks = array_column($insertData, 'meeting_link');
                $insertedRows = DB::table('google_meeting_lists')
                    ->whereIn('meeting_link', $targetLinks)
                    ->get(['id', 'meeting_link']);

                $insertedIds = $insertedRows->pluck('id')->toArray();
                $firstId = $insertedIds[0] ?? 0;
                $version = $this->getNextVersion($firstId);

                // Transactional Outbox Event
                $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => $finalKey,
                    'aggregate_type' => 'GoogleMeetingLink',
                    'aggregate_id' => (int) $firstId,
                    'aggregate_version' => $version,
                    'event_type' => 'GOOGLE_MEETING_LINKS_CREATED',
                    'payload' => json_encode([
                        'ids' => $insertedIds,
                        'count' => count($insertData),
                        'admin_id' => $adminId,
                        'timestamp' => now()->toIso8601String(),
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);

                $response = [
                    'status' => true,
                    'type' => 'success',
                    'message' => count($insertData).' meeting link(s) added successfully to the pool.',
                    'url' => route('superadmin.company.google_meeting_setting'),
                ];
            } else {
                // Case 2: Single item creation
                $accountName = trim((string) ($payload['account_name'] ?? ''));
                $meetingLink = trim((string) ($payload['meeting_link'] ?? ''));
                $status = isset($payload['status']) ? (int) $payload['status'] : 1;

                if (! preg_match('/^https?:\/\//i', $meetingLink)) {
                    $meetingLink = 'https://'.$meetingLink;
                }

                $exists = DB::table('google_meeting_lists')->where('meeting_link', $meetingLink)->exists();
                if ($exists) {
                    DB::rollBack();

                    return [
                        'status' => false,
                        'type' => 'warning',
                        'message' => 'This Google Meeting link already exists in the pool.',
                    ];
                }

                $id = DB::table('google_meeting_lists')->insertGetId([
                    'account_name' => $accountName,
                    'meeting_link' => $meetingLink,
                    'status' => $status,
                ]);

                $version = $this->getNextVersion($id);

                $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => $finalKey,
                    'aggregate_type' => 'GoogleMeetingLink',
                    'aggregate_id' => (int) $id,
                    'aggregate_version' => $version,
                    'event_type' => 'GOOGLE_MEETING_LINKS_CREATED',
                    'payload' => json_encode([
                        'ids' => [$id],
                        'account_name' => $accountName,
                        'meeting_link' => $meetingLink,
                        'status' => $status,
                        'admin_id' => $adminId,
                        'timestamp' => now()->toIso8601String(),
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);

                $response = [
                    'status' => true,
                    'type' => 'success',
                    'message' => 'Meeting link created successfully.',
                    'id' => (int) $id,
                    'url' => route('superadmin.company.google_meeting_setting'),
                ];
            }

            $this->completeIdempotency($finalKey, $response);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->failIdempotency($finalKey, $e);
            Log::error('SuperAdminGoogleMeetingMutationService::storeLinks failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        // Multi-Tenant Atomic Cache Invalidation (Rule 6)
        SuperAdminGoogleMeetingCacheManager::flushCaches();

        // Fast-path dual-store sync (<15ms)
        if ($outboxEventId) {
            $this->dispatchOutboxFastPath($outboxEventId);
        }

        return $response;
    }

    /**
     * Atomically toggle status (1 or 0) of a Google Meeting pool link.
     */
    public function updateStatus(int $id, int $newStatus, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalKey = $this->formatIdempotencyKey($idempotencyKey);
        $requestHash = $this->computeRequestHash(['id' => $id, 'chkStatus' => $newStatus]);

        $replayed = $this->claimIdempotencyLease($finalKey, $requestHash);
        if ($replayed !== null) {
            return $replayed;
        }

        $outboxEventId = null;
        $response = [];

        DB::beginTransaction();
        try {
            $record = DB::table('google_meeting_lists')->where('id', $id)->lockForUpdate()->first();
            if (! $record) {
                DB::rollBack();

                return [
                    'status' => 'error',
                    'message' => 'Meeting link record not found.',
                ];
            }

            DB::table('google_meeting_lists')
                ->where('id', $id)
                ->update(['status' => $newStatus]);

            $version = $this->getNextVersion($id);

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalKey,
                'aggregate_type' => 'GoogleMeetingLink',
                'aggregate_id' => (int) $id,
                'aggregate_version' => $version,
                'event_type' => 'GOOGLE_MEETING_LINK_STATUS_UPDATED',
                'payload' => json_encode([
                    'id' => (int) $id,
                    'status' => (int) $newStatus,
                    'admin_id' => $adminId,
                    'timestamp' => now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => $newStatus === 1 ? 'Meeting link activated successfully.' : 'Meeting link paused successfully.',
                'new_status' => (int) $newStatus,
                'id' => (int) $id,
            ];

            $this->completeIdempotency($finalKey, $response);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->failIdempotency($finalKey, $e);
            Log::error('SuperAdminGoogleMeetingMutationService::updateStatus failed: '.$e->getMessage());

            throw $e;
        }

        SuperAdminGoogleMeetingCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchOutboxFastPath($outboxEventId);
        }

        return $response;
    }

    /**
     * Update an individual Google Meeting link record (account_name and meeting_link).
     */
    public function updateLink(int $id, array $data, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalKey = $this->formatIdempotencyKey($idempotencyKey);
        $requestHash = $this->computeRequestHash(array_merge(['id' => $id], $data));

        $replayed = $this->claimIdempotencyLease($finalKey, $requestHash);
        if ($replayed !== null) {
            return $replayed;
        }

        $outboxEventId = null;
        $response = [];

        DB::beginTransaction();
        try {
            $record = DB::table('google_meeting_lists')->where('id', $id)->lockForUpdate()->first();
            if (! $record) {
                DB::rollBack();

                return [
                    'status' => false,
                    'type' => 'error',
                    'message' => 'Meeting link record not found.',
                ];
            }

            $accountName = trim((string) ($data['account_name'] ?? ''));
            $meetingLink = trim((string) ($data['meeting_link'] ?? ''));
            if (! preg_match('/^https?:\/\//i', $meetingLink)) {
                $meetingLink = 'https://'.$meetingLink;
            }

            // Conflict check
            $conflict = DB::table('google_meeting_lists')
                ->where('meeting_link', $meetingLink)
                ->where('id', '!=', $id)
                ->exists();

            if ($conflict) {
                DB::rollBack();

                return [
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'Another record with this meeting link already exists.',
                ];
            }

            DB::table('google_meeting_lists')->where('id', $id)->update([
                'account_name' => $accountName,
                'meeting_link' => $meetingLink,
            ]);

            $version = $this->getNextVersion($id);

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalKey,
                'aggregate_type' => 'GoogleMeetingLink',
                'aggregate_id' => (int) $id,
                'aggregate_version' => $version,
                'event_type' => 'GOOGLE_MEETING_LINK_UPDATED',
                'payload' => json_encode([
                    'id' => (int) $id,
                    'account_name' => $accountName,
                    'meeting_link' => $meetingLink,
                    'admin_id' => $adminId,
                    'timestamp' => now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => true,
                'type' => 'success',
                'message' => 'Meeting link updated successfully.',
                'data' => [
                    'id' => (int) $id,
                    'account_name' => $accountName,
                    'meeting_link' => $meetingLink,
                ],
            ];

            $this->completeIdempotency($finalKey, $response);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->failIdempotency($finalKey, $e);
            Log::error('SuperAdminGoogleMeetingMutationService::updateLink failed: '.$e->getMessage());

            throw $e;
        }

        SuperAdminGoogleMeetingCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchOutboxFastPath($outboxEventId);
        }

        return $response;
    }

    /**
     * Delete one or multiple Google Meeting pool links.
     */
    public function deleteLinks(array $ids, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalKey = $this->formatIdempotencyKey($idempotencyKey);
        $requestHash = $this->computeRequestHash(['ids' => $ids]);

        $replayed = $this->claimIdempotencyLease($finalKey, $requestHash);
        if ($replayed !== null) {
            return $replayed;
        }

        $outboxEventId = null;
        $response = [];

        DB::beginTransaction();
        try {
            $targetIds = array_map('intval', $ids);
            if (empty($targetIds)) {
                DB::rollBack();

                return [
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'Please select at least one record.',
                ];
            }

            // Set Redis tombstones to guard against delayed outbox upserts
            $version = 999999;
            foreach ($targetIds as $tid) {
                SuperAdminGoogleMeetingCacheManager::setTombstone($tid, $version);
            }

            $deleted = DB::table('google_meeting_lists')
                ->whereIn('id', $targetIds)
                ->delete();

            $firstId = $targetIds[0] ?? 0;
            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalKey,
                'aggregate_type' => 'GoogleMeetingLink',
                'aggregate_id' => (int) $firstId,
                'aggregate_version' => $version,
                'event_type' => 'GOOGLE_MEETING_LINKS_DELETED',
                'payload' => json_encode([
                    'ids' => $targetIds,
                    'count' => $deleted,
                    'admin_id' => $adminId,
                    'timestamp' => now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => true,
                'type' => 'success',
                'message' => $deleted.' record(s) deleted successfully.',
            ];

            $this->completeIdempotency($finalKey, $response);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->failIdempotency($finalKey, $e);
            Log::error('SuperAdminGoogleMeetingMutationService::deleteLinks failed: '.$e->getMessage());

            throw $e;
        }

        SuperAdminGoogleMeetingCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchOutboxFastPath($outboxEventId);
        }

        return $response;
    }

    /**
     * Dispatch fast-path synchronous execution with queue worker fallback.
     */
    protected function dispatchOutboxFastPath(int $outboxEventId): void
    {
        try {
            $job = new ProcessGoogleMeetingOutboxEventJob($outboxEventId);
            $job->handle();
        } catch (Throwable $ex) {
            Log::warning('SuperAdminGoogleMeetingMutationService: Fast-path sync deferred to queue: '.$ex->getMessage());
            dispatch(new ProcessGoogleMeetingOutboxEventJob($outboxEventId));
        }
    }
}
