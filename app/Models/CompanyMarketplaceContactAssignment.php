<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CompanyMarketplaceContactAssignment
 *
 * Authoritative Eloquent model governing Marketplace Contact Visibility and Representative
 * assignments across JERO Marketplace (Sales & Client Enquiries, Agency Enquiries, Bench Talent Enquiries).
 *
 * Core Tenets:
 * 1. Single Active Primary: Database-level guarantee via virtual column `primary_enforcer`.
 * 2. Granular Visibility: Per-assignment privacy ('public' vs 'private').
 * 3. Multi-Tenant Scoping: Strictly scoped by `company_id`.
 * 4. Temporal Delegation: Supports scheduled coverage via `effective_from` and `effective_until`.
 *
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property string $channel ('client', 'agency', 'bench')
 * @property string $visibility ('public', 'private')
 * @property string $priority ('primary', 'secondary', 'backup')
 * @property bool $is_active
 * @property \Carbon\Carbon|null $effective_from
 * @property \Carbon\Carbon|null $effective_until
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CompanyMarketplaceContactAssignment extends Model
{
    use HasFactory;

    protected $table = 'company_marketplace_contact_assignments';

    protected $fillable = [
        'company_id',
        'user_id',
        'channel',
        'visibility',
        'priority',
        'is_active',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'user_id' => 'integer',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Relationship to Company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope query to a specific company (Tenancy filter).
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_marketplace_contact_assignments.company_id', $companyId);
    }

    /**
     * Scope query to a specific marketplace channel.
     */
    public function scopeForChannel(Builder $query, string $channel): Builder
    {
        return $query->where('company_marketplace_contact_assignments.channel', strtolower(trim($channel)));
    }

    /**
     * Scope query to active assignments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('company_marketplace_contact_assignments.is_active', true);
    }

    /**
     * Scope query to Primary assignments.
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('company_marketplace_contact_assignments.priority', 'primary');
    }

    /**
     * Scope query to Publicly visible contacts only (external marketplace).
     */
    public function scopePublicOnly(Builder $query): Builder
    {
        return $query->where('company_marketplace_contact_assignments.visibility', 'public');
    }

    /**
     * Scope query to assignments currently effective (honoring temporal delegation).
     */
    public function scopeCurrentlyEffective(Builder $query): Builder
    {
        $now = now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('company_marketplace_contact_assignments.effective_from')
                ->orWhere('company_marketplace_contact_assignments.effective_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('company_marketplace_contact_assignments.effective_until')
                ->orWhere('company_marketplace_contact_assignments.effective_until', '>=', $now);
        });
    }
}
