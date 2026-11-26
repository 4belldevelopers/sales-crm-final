<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CrmOffering — JeroCRM Product & Service Catalog Entity.
 *
 * Intent: Dedicated CRM offering model (Product vs Service) isolated from recruitment `jobs`.
 * Tenancy Note: Enforces strict company_id global scope on authenticated non-superadmin queries.
 */
class CrmOffering extends Model
{
    use SoftDeletes;

    protected $table = 'crm_offerings';

    protected $guarded = ['id'];

    protected $casts = [
        'target_segments' => 'array',
        'eligibility_criteria' => 'array',
        'is_unlimited_capacity' => 'boolean',
        'is_negotiable' => 'boolean',
        'visibility_internal_crm' => 'boolean',
        'visibility_client_portal' => 'boolean',
        'visibility_public_catalog' => 'boolean',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'valid_until' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->is_superadmin) {
                $builder->where('crm_offerings.company_id', auth()->user()->company_id);
            }
        });
    }

    public function locations(): HasMany
    {
        return $this->hasMany(CrmOfferingLocation::class, 'offering_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(CrmOfferingTag::class, 'offering_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CrmOfferingQuestion::class, 'offering_id')->orderBy('sort_order');
    }

    public function team(): HasMany
    {
        return $this->hasMany(CrmOfferingTeam::class, 'offering_id');
    }
}
