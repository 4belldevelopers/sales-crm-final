<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Enums\InvitationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TeamInvitation Model
 *
 * Encapsulates the lifecycle, cryptographic verification, and tenancy boundaries
 * of team member invitations (INV-01 through INV-04).
 *
 * @property int $id
 * @property string $uuid
 * @property int $company_id
 * @property string $email
 * @property string $token_hash
 * @property int $role_id
 * @property string $context_version
 * @property InvitationStatus $status
 * @property DeliveryStatus $delivery_status
 * @property int|null $active_token_slot
 * @property int $invited_by
 * @property Carbon|null $link_accessed_at
 * @property Carbon|null $used_at
 * @property Carbon|null $revoked_at
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TeamInvitation extends Model
{
    protected $table = 'team_invitations';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'status'           => InvitationStatus::class,
        'delivery_status'  => DeliveryStatus::class,
        'link_accessed_at' => 'datetime',
        'used_at'          => 'datetime',
        'revoked_at'       => 'datetime',
        'expires_at'       => 'datetime',
        'company_id'       => 'integer',
        'role_id'          => 'integer',
        'invited_by'       => 'integer',
        'active_token_slot'=> 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TeamInvitationAuditLog::class, 'invitation_id');
    }

    /**
     * Checks if the invitation is currently acceptable and can be opened/activated.
     * Enforces INV-04 decoupled status authority.
     */
    public function isAcceptable(): bool
    {
        if (! $this->status->isAcceptable()) {
            return false;
        }

        if ($this->revoked_at !== null) {
            return false;
        }

        if ($this->used_at !== null) {
            return false;
        }

        if ($this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Scope query to invitations holding an active uniqueness slot.
     */
    public function scopeActiveSlot(Builder $query): Builder
    {
        return $query->whereNotNull('active_token_slot');
    }

    /**
     * Strict multi-tenant scope (Rule 9).
     */
    public function scopeForTenant(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}
