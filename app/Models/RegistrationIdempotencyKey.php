<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RegistrationIdempotencyKey Model
 *
 * Implements database-enforced HTTP idempotency for activation requests (INV-08 & INV-16).
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $invitation_id
 * @property string $idempotency_key
 * @property string $endpoint
 * @property string $request_hash
 * @property string $status
 * @property int|null $response_code
 * @property array|null $response_body
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $expires_at
 */
class RegistrationIdempotencyKey extends Model
{
    protected $table = 'registration_idempotency_keys';

    protected $guarded = ['id'];

    public $timestamps = false; // Custom timestamp management

    protected $casts = [
        'response_body' => 'array',
        'response_code' => 'integer',
        'tenant_id'     => 'integer',
        'invitation_id' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'expires_at'    => 'datetime',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(TeamInvitation::class, 'invitation_id');
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }
}
