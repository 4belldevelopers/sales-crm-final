<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TenantStateVersion Model
 *
 * Implements the universal domain state version pulse for JERO's Client-First CQRS (INV-11 & INV-12).
 *
 * @property int $company_id
 * @property string $domain
 * @property int $version
 * @property int|null $last_event_id
 * @property Carbon $updated_at
 */
class TenantStateVersion extends Model
{
    protected $table = 'tenant_state_versions';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = ['company_id', 'domain'];

    protected $guarded = [];

    protected $casts = [
        'company_id'    => 'integer',
        'version'       => 'integer',
        'last_event_id' => 'integer',
        'updated_at'    => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function scopeForDomain(Builder $query, int $companyId, string $domain): Builder
    {
        return $query->where('company_id', $companyId)->where('domain', $domain);
    }
}
