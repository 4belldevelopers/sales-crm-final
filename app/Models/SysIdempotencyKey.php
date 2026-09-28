<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysIdempotencyKey
 *
 * Intent: Transactionally coordinated idempotency state machine entity per docs/drawer_candidate.md.
 * Manages atomic claims, request hash verification, and OCC execution results.
 */
class SysIdempotencyKey extends Model
{
    protected $table = 'sys_idempotency_keys';

    protected $guarded = ['id'];

    protected $casts = [
        'tenant_id' => 'integer',
        'response_status' => 'integer',
        'processing_until' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now());
    }
}
