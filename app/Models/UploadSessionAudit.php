<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Append-only audit log for resume_uploads state transitions.
 *
 * Schema-level backstop for "never DELETE, never UPDATE":
 *   - Boot hooks reject update() and delete() at runtime.
 *   - There is no migration-level constraint that hardens this; even with
 *     DB-level enforcement, the Laravel layer's boot hooks are the simpler
 *     guarantee.
 *
 * Retention:
 *   - Schema does NOT include an index on (created_at) for full-table scans;
 *     use (company_id, action, created_at) for tenant-scoped rollups and
 *     (session_uuid, created_at) for per-session lookup.
 *   - Retention window is an operational concern (separate from this model):
 *     recommend 7 years for SOC 2 baseline but make it configurable per-tenant
 *     for GDPR right-to-erasure exemptions.
 *
 * The companion {@see self::record()} factory is the only intended write path.
 */
class UploadSessionAudit extends Model
{
    protected $table = 'upload_session_audit';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata_json' => 'array',
        'latency_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    /** Append-only enforcement. Lifted only by direct DB writes (the GC job doesn't touch this table). */
    protected static function boot()
    {
        parent::boot();
        static::updating(function (UploadSessionAudit $row): bool {
            throw new RuntimeException('upload_session_audit is append-only; updates are not permitted');
        });
        static::deleting(function (UploadSessionAudit $row): bool {
            throw new RuntimeException('upload_session_audit is append-only; deletes are not permitted');
        });
    }

    /**
     * Factory for the only intended write path. Returns the persisted row so
     * callers can reference id() in the same request if they need to.
     *
     * @param array $metadata Free-form per-action metadata.
     */
    public static function record(
        string $sessionUuid,
        int $companyId,
        string $action,
        ?int $actorId = null,
        ?string $actorIp = null,
        ?string $userAgent = null,
        ?string $requestId = null,
        array $metadata = [],
        ?int $latencyMs = null,
    ): self {
        return self::create([
            'session_uuid' => $sessionUuid,
            'company_id' => $companyId,
            'action' => $action,
            'actor_id' => $actorId,
            'actor_ip' => $actorIp,
            'user_agent' => $userAgent !== null ? substr($userAgent, 0, 255) : null,
            'request_id' => $requestId,
            'metadata_json' => $metadata,
            'latency_ms' => $latencyMs,
            'created_at' => now(),
        ]);
    }
}
