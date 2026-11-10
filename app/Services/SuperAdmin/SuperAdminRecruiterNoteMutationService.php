<?php

namespace App\Services\SuperAdmin;

use App\Helper\Reply;
use App\Jobs\ProcessRecruiterNoteOutboxEventJob;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class SuperAdminRecruiterNoteMutationService
 *
 * Intent: Encapsulates state-mutating operations for Super Admin Recruiter Note Templates under the
 * Transactional Outbox, Two-Tier Idempotency, Monotonic Aggregate Versioning, and CQRS dual-store architecture.
 *
 * Advanced Enterprise Hardening:
 * - Monotonic Aggregate Versioning (aggregate_version monotonically increments per template).
 * - Deterministic SHA-256 idempotency fingerprinting for oversized keys (no silent prefix collisions).
 * - Request payload tampering detection (422 if idempotency key reused with mismatched payload).
 * - Atomic lease claiming with lease ownership and recovery of abandoned PROCESSING leases (>120s).
 * - Version-aware deletion tombstones in Redis to prevent resurrection from out-of-order events.
 * - Deadlock resilience with bounded retries (3 attempts).
 * - Rich-text HTML sanitization against XSS attack vectors.
 * - Read-after-write cache priming for 0ms immediate data freshness.
 *
 * Tenancy: Scoped strictly to Super Admin (tenant_id = 0, company_id = NULL).
 * Target: 10,000 concurrent users / 0% server strain.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminRecruiterNoteMutationService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Compute a canonical SHA-256 hash for a given payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Format a deterministic idempotency key bounded to 64 characters.
     * Uses SHA-256 fingerprinting for oversized keys to eliminate silent truncation collisions.
     */
    protected function formatIdempotencyKey(string $action, string|int $identifier, ?string $clientKey, array $payload): string
    {
        if ($clientKey && trim($clientKey) !== '') {
            $trimmed = trim($clientKey);
            if (strlen($trimmed) <= 64) {
                return $trimmed;
            }

            // Deterministic collision-free fingerprint for oversized keys bounded to exactly 64 chars
            return 'idemp_'.substr(hash('sha256', $trimmed), 0, 58);
        }

        $hash = substr($this->computeRequestHash($payload), 0, 16);
        $ident = substr((string) $identifier, 0, 20);

        return "rnote:{$action}:{$ident}:{$hash}";
    }

    /**
     * Compute a unique outbox event idempotency key bounded to 64 characters.
     */
    protected function computeOutboxKey(string $baseKey, string|int $suffix): string
    {
        $suffixStr = ":{$suffix}";
        $maxBaseLen = 64 - strlen($suffixStr);

        if ($maxBaseLen > 0) {
            return substr($baseKey, 0, $maxBaseLen).$suffixStr;
        }

        return 'ob_'.hash('sha256', "{$baseKey}:{$suffix}");
    }

    /**
     * Calculate monotonic aggregate version for a template.
     */
    protected function getNextAggregateVersion(int|string $aggregateId): int
    {
        $maxVersion = (int) DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('aggregate_id', $aggregateId)
            ->max('aggregate_version');

        return max($maxVersion + 1, 1);
    }

    /**
     * Sanitize rich-text content to eliminate XSS vectors while preserving legitimate HTML formatting.
     */
    public function sanitizeHtml(string $html): string
    {
        // Strip dangerous tags and their content
        $sanitized = preg_replace('/<(script|style|iframe|object|embed|applet)\b[^>]*>.*?<\/\1>/is', '', $html);
        // Strip inline event attributes (e.g. onload, onerror, onclick)
        $sanitized = preg_replace('/\b(on\w+)\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', (string) $sanitized);
        // Strip javascript: pseudo-protocols
        $sanitized = preg_replace('/href\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', 'href="#"', (string) $sanitized);
        $sanitized = preg_replace('/src\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', 'src=""', (string) $sanitized);

        return trim((string) $sanitized);
    }

    /**
     * Claim idempotency lease in sys_idempotency_keys.
     * Prevents duplicate executions, recovers abandoned leases (>120s), and rejects payload tampering.
     */
    protected function claimIdempotencyLease(string $key, string $hash): ?array
    {
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $key,
            'request_hash' => $hash,
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $key)
                ->first();

            if ($existing) {
                // Reject key reuse with mismatched payload (tampering guard)
                if ($existing->request_hash !== $hash) {
                    abort(422, 'Idempotency key reuse detected with mismatched payload.');
                }

                if ($existing->status === 'COMPLETED' && $existing->response_body) {
                    $cached = json_decode($existing->response_body, true);
                    if (is_array($cached)) {
                        $cached['_replayed'] = true;

                        return $cached;
                    }
                }

                if ($existing->status === 'PROCESSING') {
                    $abandonThreshold = Carbon::now()->subSeconds(120);

                    // If currently processing within lease window, reject concurrent contention
                    if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt($abandonThreshold)) {
                        abort(409, 'A recruiter note mutation is currently processing for this request. Please wait.');
                    }

                    // Lease recovery: abandoned PROCESSING request (>120s) reclaimed atomically with conditional update
                    $reclaimed = DB::table('sys_idempotency_keys')
                        ->where('tenant_id', 0)
                        ->where('idempotency_key', $key)
                        ->where('status', 'PROCESSING')
                        ->where('updated_at', $existing->updated_at)
                        ->update([
                            'request_hash' => $hash,
                            'status' => 'PROCESSING',
                            'updated_at' => Carbon::now(),
                        ]);

                    if (! $reclaimed) {
                        abort(409, 'A recruiter note mutation is currently processing for this request. Please wait.');
                    }
                }
            }
        }

        return null;
    }

    /**
     * Finalize idempotency key as COMPLETED with cached response body.
     */
    protected function finalizeIdempotencyKey(string $key, array $responsePayload): void
    {
        DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $key)
            ->update([
                'status' => 'COMPLETED',
                'response_status' => 200,
                'response_body' => json_encode($responsePayload),
                'completed_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * Store a newly created recruiter note template.
     */
    public function createTemplate(array $payload, ?string $idempotencyKey = null): array
    {
        $sanitizedContent = $this->sanitizeHtml($payload['content'] ?? '');
        $payload['content'] = $sanitizedContent;

        $finalIdempotencyKey = $this->formatIdempotencyKey('create', 'new', $idempotencyKey, $payload);
        $requestHash = $this->computeRequestHash($payload);

        $claim = $this->claimIdempotencyLease($finalIdempotencyKey, $requestHash);
        if ($claim !== null) {
            return $claim;
        }

        $outboxEventId = null;
        $insertedRecord = null;

        $result = DB::transaction(function () use ($payload, $finalIdempotencyKey, &$outboxEventId, &$insertedRecord) {
            $now = Carbon::now();
            $newId = DB::table('recruiter_note_master')->insertGetId([
                'company_id' => null, // Scoped strictly to Global Super Admin
                'title' => $payload['title'],
                'content' => $payload['content'],
                'status' => (int) ($payload['status'] ?? 1),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $insertedRecord = DB::table('recruiter_note_master')
                ->where('id', $newId)
                ->whereNull('company_id')
                ->first();

            $version = 1;

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $this->computeOutboxKey($finalIdempotencyKey, $newId),
                'aggregate_type' => 'RecruiterNoteTemplate',
                'aggregate_id' => $newId,
                'aggregate_version' => $version,
                'event_type' => 'RECRUITER_NOTE_TEMPLATE_CREATED',
                'payload' => json_encode([
                    'id' => $newId,
                    'title' => $payload['title'],
                    'status' => (int) ($payload['status'] ?? 1),
                    'version' => $version,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $res = Reply::successWithData('Recruiter note template added successfully.', [
                'template' => $insertedRecord,
            ]);

            $this->finalizeIdempotencyKey($finalIdempotencyKey, $res);

            return $res;
        }, 3);

        // Read-after-write consistency: Prime item cache and flush listing O(1)
        if ($insertedRecord) {
            SuperAdminRecruiterNoteCacheManager::primeItem($insertedRecord->id, (array) $insertedRecord);
        }
        SuperAdminRecruiterNoteCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchFastPath($outboxEventId);
        }

        return $result;
    }

    /**
     * Update an existing recruiter note template.
     */
    public function updateTemplate(int $id, array $payload, ?string $idempotencyKey = null): array
    {
        $sanitizedContent = $this->sanitizeHtml($payload['content'] ?? '');
        $payload['content'] = $sanitizedContent;

        $finalIdempotencyKey = $this->formatIdempotencyKey('update', $id, $idempotencyKey, $payload);
        $requestHash = $this->computeRequestHash($payload);

        $claim = $this->claimIdempotencyLease($finalIdempotencyKey, $requestHash);
        if ($claim !== null) {
            return $claim;
        }

        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (! $template) {
            return Reply::error('Template not found or has already been removed.');
        }

        $outboxEventId = null;
        $updatedRecord = null;

        $result = DB::transaction(function () use ($id, $payload, $template, $finalIdempotencyKey, &$outboxEventId, &$updatedRecord) {
            $now = Carbon::now();
            DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->update([
                    'title' => $payload['title'],
                    'content' => $payload['content'],
                    'status' => (int) ($payload['status'] ?? $template->status),
                    'updated_at' => $now,
                ]);

            $updatedRecord = DB::table('recruiter_note_master')
                ->where('id', $id)
                ->whereNull('company_id')
                ->first();

            $nextVersion = $this->getNextAggregateVersion($id);

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $this->computeOutboxKey($finalIdempotencyKey, $id),
                'aggregate_type' => 'RecruiterNoteTemplate',
                'aggregate_id' => $id,
                'aggregate_version' => $nextVersion,
                'event_type' => 'RECRUITER_NOTE_TEMPLATE_UPDATED',
                'payload' => json_encode([
                    'id' => $id,
                    'title' => $payload['title'],
                    'status' => (int) ($payload['status'] ?? $template->status),
                    'version' => $nextVersion,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $res = Reply::successWithData('Recruiter note template updated successfully.', [
                'template' => $updatedRecord,
            ]);

            $this->finalizeIdempotencyKey($finalIdempotencyKey, $res);

            return $res;
        }, 3);

        // Read-after-write consistency: Prime item cache and flush listing O(1)
        if ($updatedRecord) {
            SuperAdminRecruiterNoteCacheManager::primeItem($id, (array) $updatedRecord);
        }
        SuperAdminRecruiterNoteCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchFastPath($outboxEventId);
        }

        return $result;
    }

    /**
     * Delete a recruiter note template.
     */
    public function deleteTemplate(int $id, ?string $idempotencyKey = null): array
    {
        $payload = ['id' => $id, 'action' => 'delete'];
        $finalIdempotencyKey = $this->formatIdempotencyKey('delete', $id, $idempotencyKey, $payload);
        $requestHash = $this->computeRequestHash($payload);

        $claim = $this->claimIdempotencyLease($finalIdempotencyKey, $requestHash);
        if ($claim !== null) {
            return $claim;
        }

        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (! $template) {
            return Reply::error('Template not found or already deleted.');
        }

        $outboxEventId = null;

        $result = DB::transaction(function () use ($id, $template, $finalIdempotencyKey, &$outboxEventId) {
            $now = Carbon::now();
            DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->delete();

            $nextVersion = $this->getNextAggregateVersion($id);

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $this->computeOutboxKey($finalIdempotencyKey, $id),
                'aggregate_type' => 'RecruiterNoteTemplate',
                'aggregate_id' => $id,
                'aggregate_version' => $nextVersion,
                'event_type' => 'RECRUITER_NOTE_TEMPLATE_DELETED',
                'payload' => json_encode([
                    'id' => $id,
                    'title' => $template->title,
                    'version' => $nextVersion,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            // Version-aware deletion tombstone in Redis to prevent resurrection from out-of-order events
            SuperAdminRecruiterNoteCacheManager::setTombstone($id, $nextVersion);

            $res = Reply::success('Template deleted successfully.');

            $this->finalizeIdempotencyKey($finalIdempotencyKey, $res);

            return $res;
        }, 3);

        SuperAdminRecruiterNoteCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchFastPath($outboxEventId);
        }

        return $result;
    }

    /**
     * Instant 1-click status toggle (Active <=> Inactive).
     */
    public function toggleStatus(int $id, ?string $idempotencyKey = null): array
    {
        $payload = ['id' => $id, 'action' => 'toggle_status'];
        $finalIdempotencyKey = $this->formatIdempotencyKey('toggle', $id, $idempotencyKey, $payload);
        $requestHash = $this->computeRequestHash($payload);

        $claim = $this->claimIdempotencyLease($finalIdempotencyKey, $requestHash);
        if ($claim !== null) {
            return $claim;
        }

        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (! $template) {
            return Reply::error('Template not found.');
        }

        $newStatus = (int) $template->status === 1 ? 0 : 1;
        $outboxEventId = null;

        $result = DB::transaction(function () use ($id, $newStatus, $finalIdempotencyKey, &$outboxEventId) {
            $now = Carbon::now();
            DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => $now,
                ]);

            $nextVersion = $this->getNextAggregateVersion($id);

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $this->computeOutboxKey($finalIdempotencyKey, $id),
                'aggregate_type' => 'RecruiterNoteTemplate',
                'aggregate_id' => $id,
                'aggregate_version' => $nextVersion,
                'event_type' => 'RECRUITER_NOTE_TEMPLATE_STATUS_TOGGLED',
                'payload' => json_encode([
                    'id' => $id,
                    'status' => $newStatus,
                    'version' => $nextVersion,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $message = $newStatus === 1
                ? 'Template activated successfully.'
                : 'Template deactivated successfully.';

            $res = Reply::successWithData($message, [
                'id' => (int) $id,
                'template_status' => $newStatus,
                'status_label' => $newStatus === 1 ? 'Active' : 'Inactive',
            ]);

            $this->finalizeIdempotencyKey($finalIdempotencyKey, $res);

            return $res;
        }, 3);

        SuperAdminRecruiterNoteCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchFastPath($outboxEventId);
        }

        return $result;
    }

    /**
     * Duplicate existing template as an inactive draft copy.
     */
    public function duplicateTemplate(int $id, ?string $idempotencyKey = null): array
    {
        $payload = ['id' => $id, 'action' => 'duplicate'];
        $finalIdempotencyKey = $this->formatIdempotencyKey('duplicate', $id, $idempotencyKey, $payload);
        $requestHash = $this->computeRequestHash($payload);

        $claim = $this->claimIdempotencyLease($finalIdempotencyKey, $requestHash);
        if ($claim !== null) {
            return $claim;
        }

        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (! $template) {
            return Reply::error('Source template not found.');
        }

        $duplicateTitle = Str::limit(trim($template->title).' (Copy)', 250, '');
        $outboxEventId = null;
        $cloned = null;

        $result = DB::transaction(function () use ($duplicateTitle, $template, $finalIdempotencyKey, &$outboxEventId, &$cloned) {
            $now = Carbon::now();
            $newId = DB::table('recruiter_note_master')->insertGetId([
                'company_id' => null,
                'title' => $duplicateTitle,
                'content' => $template->content,
                'status' => 0, // Cloned templates default to Inactive draft
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $cloned = DB::table('recruiter_note_master')
                ->where('id', $newId)
                ->whereNull('company_id')
                ->first();

            $version = 1;

            $outboxEventId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $this->computeOutboxKey($finalIdempotencyKey, $newId),
                'aggregate_type' => 'RecruiterNoteTemplate',
                'aggregate_id' => $newId,
                'aggregate_version' => $version,
                'event_type' => 'RECRUITER_NOTE_TEMPLATE_DUPLICATED',
                'payload' => json_encode([
                    'id' => $newId,
                    'title' => $duplicateTitle,
                    'status' => 0,
                    'version' => $version,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            $res = Reply::successWithData('Template duplicated successfully as draft.', [
                'template' => $cloned,
            ]);

            $this->finalizeIdempotencyKey($finalIdempotencyKey, $res);

            return $res;
        }, 3);

        if ($cloned) {
            SuperAdminRecruiterNoteCacheManager::primeItem($cloned->id, (array) $cloned);
        }
        SuperAdminRecruiterNoteCacheManager::flushCaches();

        if ($outboxEventId) {
            $this->dispatchFastPath($outboxEventId);
        }

        return $result;
    }

    /**
     * Dispatch fast-path execution of outbox job.
     */
    protected function dispatchFastPath(int $eventId): void
    {
        try {
            ProcessRecruiterNoteOutboxEventJob::dispatchSync($eventId);
        } catch (\Throwable $e) {
            Log::warning("[RECRUITER_NOTE_MUTATION] Fast-path outbox sync failed for event #{$eventId}, queued for worker: {$e->getMessage()}");
            try {
                ProcessRecruiterNoteOutboxEventJob::dispatch($eventId);
            } catch (\Throwable $queueEx) {
                Log::error("[RECRUITER_NOTE_MUTATION] Queue dispatch failed for event #{$eventId}: {$queueEx->getMessage()}");
            }
        }
    }
}
