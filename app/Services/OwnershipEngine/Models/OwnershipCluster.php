<?php

namespace App\Services\OwnershipEngine\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per identity+scope cluster — the lockable, point-queryable current-owner pointer.
 * docs/OWNERSHIP_ENGINE_DESIGN.md §7.2. This is the ONLY table any write path locks
 * (SELECT ... FOR UPDATE), and it locks at most one row per transaction (§8/§23).
 *
 * Private to the Ownership Engine (§5.2) — nothing outside app/Services/OwnershipEngine may
 * reference this model or query `ownership_clusters` directly.
 */
class OwnershipCluster extends Model
{
    protected $table = 'ownership_clusters';

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    protected $fillable = [
        'scoping_company_id', 'scope_mode', 'scope_key', 'normalized_email',
        'normalized_phone', 'owner_row_id', 'owner_type', 'decision_rule',
        'decided_at', 'last_event_id',
    ];
}
