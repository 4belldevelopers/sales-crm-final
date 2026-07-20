<?php

namespace App\Services\OwnershipEngine\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Immutable, append-only audit/history/rebuild log. docs/OWNERSHIP_ENGINE_DESIGN.md §7.1.
 * The Ownership Engine only ever INSERTs here — never UPDATE, never DELETE. Private to the
 * Ownership Engine (§5.2) — nothing outside app/Services/OwnershipEngine may reference this
 * model or query `ownership_events` directly.
 */
class OwnershipEvent extends Model
{
    protected $table = 'ownership_events';

    protected $casts = [
        'event_time' => 'datetime',
    ];

    protected $fillable = [
        'event_uuid', 'cluster_id', 'scoping_company_id', 'scope_mode', 'scope_key',
        'normalized_email', 'normalized_phone', 'subject_row_id', 'event_type',
        'decision_rule', 'scenario_ref', 'previous_owner_row_id', 'new_owner_row_id',
        'trigger_source', 'actor_user_id', 'reason', 'event_time',
    ];
}
